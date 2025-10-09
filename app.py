from __future__ import annotations

import os
from datetime import datetime, date
from functools import wraps

from flask import (
    Flask,
    flash,
    redirect,
    render_template,
    request,
    session,
    url_for,
)
from flask_sqlalchemy import SQLAlchemy
from werkzeug.security import check_password_hash, generate_password_hash

BASE_DIR = os.path.abspath(os.path.dirname(__file__))
DB_PATH = os.path.join(BASE_DIR, "clinic.db")

app = Flask(__name__)
app.config["SQLALCHEMY_DATABASE_URI"] = f"sqlite:///{DB_PATH}"
app.config["SQLALCHEMY_TRACK_MODIFICATIONS"] = False
app.config["SECRET_KEY"] = "dev-secret-key"

db = SQLAlchemy(app)

ROLE_DASHBOARD_ENDPOINTS = {
    "admin": "admin_dashboard",
    "subadmin": "admin_dashboard",
    "doctor": "doctor_dashboard",
    "receptionist": "receptionist_dashboard",
    "pharmacy": "pharmacy_dashboard",
    "lab": "lab_dashboard",
}


def dashboard_url_for(user: User) -> str:
    endpoint = ROLE_DASHBOARD_ENDPOINTS.get(user.role)
    if endpoint:
        return url_for(endpoint)
    return url_for("login")


class User(db.Model):
    id = db.Column(db.Integer, primary_key=True)
    name = db.Column(db.String(120), nullable=False)
    email = db.Column(db.String(120), unique=True, nullable=False)
    role = db.Column(db.String(32), nullable=False)
    password_hash = db.Column(db.String(128), nullable=False)
    panel_permissions = db.Column(db.String(256), default="")
    is_active = db.Column(db.Boolean, default=True)

    def set_password(self, password: str) -> None:
        self.password_hash = generate_password_hash(password)

    def check_password(self, password: str) -> bool:
        return check_password_hash(self.password_hash, password)

    def allowed_panels(self):
        permissions = (self.panel_permissions or "").strip()
        if not permissions:
            return set()
        return {perm.strip() for perm in permissions.split(",") if perm.strip()}


class Patient(db.Model):
    id = db.Column(db.Integer, primary_key=True)
    name = db.Column(db.String(120), nullable=False)
    age = db.Column(db.Integer)
    sex = db.Column(db.String(10))
    address = db.Column(db.String(255))
    phone = db.Column(db.String(20))

    consultations = db.relationship("Consultation", back_populates="patient")


class Consultation(db.Model):
    __tablename__ = "consultations"

    id = db.Column(db.Integer, primary_key=True)
    patient_id = db.Column(db.Integer, db.ForeignKey("patient.id"), nullable=False)
    doctor_id = db.Column(db.Integer, db.ForeignKey("user.id"), nullable=False)
    receptionist_id = db.Column(db.Integer, db.ForeignKey("user.id"))
    main_complaints = db.Column(db.Text)
    status = db.Column(db.String(32), default="Queued")
    weight = db.Column(db.String(20))
    bp = db.Column(db.String(20))
    created_at = db.Column(db.DateTime, default=datetime.utcnow)
    updated_at = db.Column(db.DateTime, default=datetime.utcnow, onupdate=datetime.utcnow)

    patient = db.relationship("Patient", back_populates="consultations")
    doctor = db.relationship("User", foreign_keys=[doctor_id])
    receptionist = db.relationship("User", foreign_keys=[receptionist_id])
    prescription = db.relationship("Prescription", back_populates="consultation", uselist=False, cascade="all, delete-orphan")
    bill = db.relationship("Bill", back_populates="consultation", uselist=False, cascade="all, delete-orphan")


class Prescription(db.Model):
    id = db.Column(db.Integer, primary_key=True)
    consultation_id = db.Column(db.Integer, db.ForeignKey("consultations.id"), nullable=False)
    diagnosis = db.Column(db.Text)
    notes = db.Column(db.Text)
    follow_up = db.Column(db.String(120))

    consultation = db.relationship("Consultation", back_populates="prescription")
    medicines = db.relationship("PrescriptionMedicine", back_populates="prescription", cascade="all, delete-orphan")
    investigations = db.relationship("Investigation", back_populates="prescription", cascade="all, delete-orphan")


class PrescriptionMedicine(db.Model):
    id = db.Column(db.Integer, primary_key=True)
    prescription_id = db.Column(db.Integer, db.ForeignKey("prescription.id"), nullable=False)
    name = db.Column(db.String(120), nullable=False)
    dosage = db.Column(db.String(120))
    frequency = db.Column(db.String(120))
    duration = db.Column(db.String(120))

    prescription = db.relationship("Prescription", back_populates="medicines")


class Investigation(db.Model):
    id = db.Column(db.Integer, primary_key=True)
    prescription_id = db.Column(db.Integer, db.ForeignKey("prescription.id"), nullable=False)
    name = db.Column(db.String(120), nullable=False)
    status = db.Column(db.String(32), default="Pending")

    prescription = db.relationship("Prescription", back_populates="investigations")


class PharmacyMedicine(db.Model):
    id = db.Column(db.Integer, primary_key=True)
    name = db.Column(db.String(120), unique=True, nullable=False)
    description = db.Column(db.String(255))
    quantity = db.Column(db.Integer, default=0)


class Bill(db.Model):
    id = db.Column(db.Integer, primary_key=True)
    consultation_id = db.Column(db.Integer, db.ForeignKey("consultations.id"), nullable=False)
    doctor_fee = db.Column(db.Float, default=0.0)
    investigation_fee = db.Column(db.Float, default=0.0)
    medicine_fee = db.Column(db.Float, default=0.0)
    miscellaneous_fee = db.Column(db.Float, default=0.0)

    consultation = db.relationship("Consultation", back_populates="bill")

    @property
    def total(self) -> float:
        return sum(
            [
                self.doctor_fee or 0.0,
                self.investigation_fee or 0.0,
                self.medicine_fee or 0.0,
                self.miscellaneous_fee or 0.0,
            ]
        )


def login_required(role=None):
    def decorator(view_func):
        @wraps(view_func)
        def wrapped(*args, **kwargs):
            user_id = session.get("user_id")
            if not user_id:
                flash("Please log in to continue", "warning")
                return redirect(url_for("login"))
            user = User.query.get(user_id)
            if not user or not user.is_active:
                session.clear()
                flash("Your account is inactive.", "danger")
                return redirect(url_for("login"))
            if role:
                allowed_roles = role if isinstance(role, (list, tuple, set)) else [role]
                if user.role not in allowed_roles:
                    flash("You do not have permission to access that page.", "danger")
                    return redirect(dashboard_url_for(user))
            request.current_user = user
            return view_func(*args, **kwargs)

        return wrapped

    return decorator


@app.context_processor
def inject_globals():
    user = None
    user_id = session.get("user_id")
    if user_id:
        user = User.query.get(user_id)
    return {"current_user": user, "today": date.today()}


@app.route("/")
def index():
    user_id = session.get("user_id")
    if user_id:
        user = User.query.get(user_id)
        if user:
            return redirect(dashboard_url_for(user))
    return redirect(url_for("login"))


@app.route("/login", methods=["GET", "POST"])
def login():
    if request.method == "POST":
        email = request.form.get("email")
        password = request.form.get("password")
        user = User.query.filter_by(email=email).first()
        if user and user.check_password(password):
            session["user_id"] = user.id
            flash(f"Welcome back, {user.name}!", "success")
            return redirect(dashboard_url_for(user))
        flash("Invalid credentials", "danger")
    return render_template("login.html")


@app.route("/logout")
def logout():
    session.clear()
    flash("Logged out successfully", "info")
    return redirect(url_for("login"))


@app.route("/doctor")
@login_required(role="doctor")
def doctor_dashboard():
    user = request.current_user
    status_filter = request.args.get("status")
    query = Consultation.query.filter_by(doctor_id=user.id).order_by(Consultation.created_at.desc())
    if status_filter and status_filter != "all":
        query = query.filter_by(status=status_filter)
    consultations = query.all()
    statuses = ["Queued", "In Progress", "Review", "Review with Report", "Admission", "Completed"]
    return render_template(
        "doctor_dashboard.html",
        consultations=consultations,
        statuses=statuses,
        status_filter=status_filter or "all",
    )


@app.route("/doctor/consultations/<int:consultation_id>", methods=["GET", "POST"])
@login_required(role="doctor")
def doctor_consultation_detail(consultation_id):
    consultation = Consultation.query.get_or_404(consultation_id)
    if consultation.doctor_id != request.current_user.id:
        flash("You cannot access this consultation.", "danger")
        return redirect(url_for("doctor_dashboard"))

    if request.method == "POST":
        patient = consultation.patient
        patient.name = request.form.get("name", patient.name)
        patient.age = request.form.get("age") or None
        patient.sex = request.form.get("sex") or None
        patient.address = request.form.get("address") or None
        patient.phone = request.form.get("phone") or None

        consultation.main_complaints = request.form.get("main_complaints")
        consultation.weight = request.form.get("weight")
        consultation.bp = request.form.get("bp")
        consultation.status = request.form.get("status", consultation.status)

        diagnosis = request.form.get("diagnosis")
        notes = request.form.get("notes")
        follow_up = request.form.get("follow_up")

        if consultation.prescription is None:
            consultation.prescription = Prescription()
        prescription = consultation.prescription
        prescription.diagnosis = diagnosis
        prescription.notes = notes
        prescription.follow_up = follow_up

        prescription.medicines.clear()
        for name, dosage, frequency, duration in zip(
            request.form.getlist("medicine_name"),
            request.form.getlist("medicine_dosage"),
            request.form.getlist("medicine_frequency"),
            request.form.getlist("medicine_duration"),
        ):
            if name.strip():
                prescription.medicines.append(
                    PrescriptionMedicine(
                        name=name.strip(),
                        dosage=dosage.strip(),
                        frequency=frequency.strip(),
                        duration=duration.strip(),
                    )
                )

        prescription.investigations.clear()
        for name in request.form.getlist("investigation_name"):
            if name.strip():
                prescription.investigations.append(Investigation(name=name.strip()))

        db.session.commit()
        flash("Consultation updated successfully", "success")
        return redirect(url_for("doctor_consultation_detail", consultation_id=consultation_id))

    statuses = ["Queued", "In Progress", "Review", "Review with Report", "Admission", "Completed"]
    inventory_names = {medicine.name for medicine in PharmacyMedicine.query.all()}
    return render_template(
        "doctor_consultation.html",
        consultation=consultation,
        statuses=statuses,
        inventory_names=inventory_names,
    )


@app.route("/doctor/consultations/<int:consultation_id>/print")
@login_required(role="doctor")
def doctor_print_prescription(consultation_id):
    consultation = Consultation.query.get_or_404(consultation_id)
    if consultation.doctor_id != request.current_user.id:
        flash("You cannot access this consultation.", "danger")
        return redirect(url_for("doctor_dashboard"))
    return render_template("prescription_print.html", consultation=consultation)


@app.route("/receptionist", methods=["GET", "POST"])
@login_required(role="receptionist")
def receptionist_dashboard():
    user = request.current_user
    doctors = User.query.filter_by(role="doctor", is_active=True).all()
    if request.method == "POST":
        name = request.form.get("name")
        age = request.form.get("age")
        sex = request.form.get("sex")
        weight = request.form.get("weight")
        bp = request.form.get("bp")
        address = request.form.get("address")
        phone = request.form.get("phone")
        complaints = request.form.get("main_complaints")
        doctor_id = request.form.get("doctor_id")

        if not (name and doctor_id):
            flash("Name and doctor are required", "danger")
        else:
            patient = Patient.query.filter_by(phone=phone).first() if phone else None
            if patient:
                patient.name = name
                patient.age = age or patient.age
                patient.sex = sex or patient.sex
                patient.address = address or patient.address
            else:
                patient = Patient(name=name, age=age or None, sex=sex or None, address=address, phone=phone)
                db.session.add(patient)
                db.session.flush()

            consultation = Consultation(
                patient_id=patient.id,
                doctor_id=int(doctor_id),
                receptionist_id=user.id,
                main_complaints=complaints,
                weight=weight,
                bp=bp,
                status="Queued",
            )
            db.session.add(consultation)
            db.session.commit()
            flash("Consultation appointment created", "success")
            return redirect(url_for("receptionist_dashboard"))

    consultations = (
        Consultation.query.order_by(Consultation.created_at.desc())
        .filter(Consultation.receptionist_id == user.id)
        .all()
    )
    return render_template("receptionist_dashboard.html", doctors=doctors, consultations=consultations)


@app.route("/receptionist/consultations/<int:consultation_id>")
@login_required(role="receptionist")
def receptionist_consultation_view(consultation_id):
    consultation = Consultation.query.get_or_404(consultation_id)
    if consultation.receptionist_id != request.current_user.id:
        flash("You are not assigned to this consultation", "danger")
        return redirect(url_for("receptionist_dashboard"))
    return render_template("receptionist_prescription.html", consultation=consultation)


@app.route("/receptionist/consultations/<int:consultation_id>/bill", methods=["GET", "POST"])
@login_required(role="receptionist")
def receptionist_bill(consultation_id):
    consultation = Consultation.query.get_or_404(consultation_id)
    if consultation.receptionist_id != request.current_user.id:
        flash("You are not assigned to this consultation", "danger")
        return redirect(url_for("receptionist_dashboard"))

    bill = consultation.bill or Bill(consultation=consultation)
    if request.method == "POST":
        bill.doctor_fee = float(request.form.get("doctor_fee") or 0)
        bill.investigation_fee = float(request.form.get("investigation_fee") or 0)
        bill.medicine_fee = float(request.form.get("medicine_fee") or 0)
        bill.miscellaneous_fee = float(request.form.get("miscellaneous_fee") or 0)
        db.session.add(bill)
        db.session.commit()
        flash("Bill saved successfully", "success")
        return redirect(url_for("receptionist_bill", consultation_id=consultation_id))

    return render_template("receptionist_bill.html", consultation=consultation, bill=bill)


@app.route("/lab", methods=["GET", "POST"])
@login_required(role="lab")
def lab_dashboard():
    investigations = (
        Investigation.query.join(Prescription)
        .join(Consultation)
        .filter(Consultation.status != "Completed")
        .order_by(Investigation.status.desc(), Consultation.created_at.desc())
        .all()
    )
    if request.method == "POST":
        investigation_id = request.form.get("investigation_id")
        new_status = request.form.get("status")
        investigation = Investigation.query.get(investigation_id)
        if investigation:
            investigation.status = new_status or investigation.status
            db.session.commit()
            flash("Investigation updated", "success")
        return redirect(url_for("lab_dashboard"))
    return render_template("lab_dashboard.html", investigations=investigations)


@app.route("/pharmacy", methods=["GET", "POST"])
@login_required(role="pharmacy")
def pharmacy_dashboard():
    prescriptions = (
        Prescription.query.join(Consultation)
        .order_by(Consultation.created_at.desc())
        .all()
    )
    medicines = PharmacyMedicine.query.all()
    inventory = {med.name: med for med in medicines}
    return render_template(
        "pharmacy_dashboard.html",
        prescriptions=prescriptions,
        inventory=inventory,
        inventory_list=medicines,
    )


@app.route("/pharmacy/inventory", methods=["POST"])
@login_required(role="pharmacy")
def pharmacy_inventory_update():
    name = request.form.get("name")
    description = request.form.get("description")
    quantity = int(request.form.get("quantity") or 0)
    if not name:
        flash("Medicine name is required", "danger")
        return redirect(url_for("pharmacy_dashboard"))

    medicine = PharmacyMedicine.query.filter_by(name=name.strip()).first()
    if medicine:
        medicine.description = description
        medicine.quantity = quantity
    else:
        medicine = PharmacyMedicine(name=name.strip(), description=description, quantity=quantity)
        db.session.add(medicine)
    db.session.commit()
    flash("Inventory updated", "success")
    return redirect(url_for("pharmacy_dashboard"))


@app.route("/admin", methods=["GET", "POST"])
@login_required(role=["admin", "subadmin"])
def admin_dashboard():
    user = request.current_user
    allowed = user.allowed_panels() if user.role == "subadmin" else {"dashboard", "user_management", "inventory_management"}

    stats = {}
    if "dashboard" in allowed:
        today = date.today()
        start_of_month = today.replace(day=1)
        consultations_today = (
            db.session.query(User.name, db.func.count(Consultation.id))
            .join(Consultation, Consultation.doctor_id == User.id)
            .filter(
                User.role == "doctor",
                db.func.date(Consultation.created_at) == today,
            )
            .group_by(User.name)
            .all()
        )
        consultations_month = (
            db.session.query(User.name, db.func.count(Consultation.id))
            .join(Consultation, Consultation.doctor_id == User.id)
            .filter(
                User.role == "doctor",
                Consultation.created_at >= start_of_month,
            )
            .group_by(User.name)
            .all()
        )
        stats = {
            "today": consultations_today,
            "month": consultations_month,
            "total": Consultation.query.count(),
        }

    roles = ["doctor", "receptionist", "lab", "pharmacy", "admin", "subadmin"]
    users = []
    if "user_management" in allowed:
        users = User.query.order_by(User.role, User.name).all()

    inventory = []
    if "inventory_management" in allowed:
        inventory = PharmacyMedicine.query.order_by(PharmacyMedicine.name).all()

    return render_template(
        "admin_dashboard.html",
        stats=stats,
        roles=roles,
        users=users,
        inventory=inventory,
        allowed=allowed,
    )


@app.route("/admin/create_user", methods=["POST"])
@login_required(role=["admin", "subadmin"])
def admin_create_user():
    user = request.current_user
    allowed = user.allowed_panels() if user.role == "subadmin" else {"user_management"}
    if "user_management" not in allowed:
        flash("You do not have permission to manage users.", "danger")
        return redirect(url_for("admin_dashboard"))

    name = request.form.get("name")
    email = request.form.get("email")
    role = request.form.get("role")
    password = request.form.get("password")
    permissions = request.form.getlist("permissions")

    if not all([name, email, role, password]):
        flash("All fields are required", "danger")
        return redirect(url_for("admin_dashboard"))

    if User.query.filter_by(email=email).first():
        flash("Email already exists", "danger")
        return redirect(url_for("admin_dashboard"))

    new_user = User(name=name, email=email, role=role)
    new_user.set_password(password)
    if role == "subadmin":
        new_user.panel_permissions = ",".join(permissions)
    db.session.add(new_user)
    db.session.commit()
    flash("User account created", "success")
    return redirect(url_for("admin_dashboard"))


@app.route("/admin/inventory", methods=["POST"])
@login_required(role=["admin", "subadmin"])
def admin_inventory_update():
    user = request.current_user
    allowed = user.allowed_panels() if user.role == "subadmin" else {"inventory_management"}
    if "inventory_management" not in allowed:
        flash("You do not have permission to manage inventory.", "danger")
        return redirect(url_for("admin_dashboard"))

    bulk = request.form.get("bulk_list")
    if bulk:
        lines = [line.strip() for line in bulk.splitlines() if line.strip()]
        for line in lines:
            parts = [part.strip() for part in line.split(",")]
            name = parts[0]
            quantity = int(parts[1]) if len(parts) > 1 and parts[1].isdigit() else 0
            description = parts[2] if len(parts) > 2 else ""
            medicine = PharmacyMedicine.query.filter_by(name=name).first()
            if medicine:
                medicine.quantity = quantity
                medicine.description = description
            else:
                db.session.add(PharmacyMedicine(name=name, quantity=quantity, description=description))
        db.session.commit()
        flash("Bulk inventory updated", "success")
        return redirect(url_for("admin_dashboard"))

    name = request.form.get("name")
    description = request.form.get("description")
    quantity = int(request.form.get("quantity") or 0)
    if not name:
        flash("Medicine name is required", "danger")
        return redirect(url_for("admin_dashboard"))
    medicine = PharmacyMedicine.query.filter_by(name=name).first()
    if medicine:
        medicine.description = description
        medicine.quantity = quantity
    else:
        db.session.add(PharmacyMedicine(name=name, description=description, quantity=quantity))
    db.session.commit()
    flash("Inventory updated", "success")
    return redirect(url_for("admin_dashboard"))


def initialize_database():
    if not os.path.exists(DB_PATH):
        db.create_all()
        seed_data()
    else:
        db.create_all()


def seed_data():
    admin = User(name="Clinic Admin", email="admin@clinic.com", role="admin")
    admin.set_password("admin123")
    doctor = User(name="Dr. Anita Rao", email="doctor@clinic.com", role="doctor")
    doctor.set_password("doctor123")
    receptionist = User(name="Ravi Reception", email="reception@clinic.com", role="receptionist")
    receptionist.set_password("reception123")
    pharmacist = User(name="Pooja Pharmacy", email="pharmacy@clinic.com", role="pharmacy")
    pharmacist.set_password("pharmacy123")
    lab_user = User(name="Sanjay Lab", email="lab@clinic.com", role="lab")
    lab_user.set_password("lab123")

    db.session.add_all([admin, doctor, receptionist, pharmacist, lab_user])
    db.session.commit()

    patient = Patient(name="Rahul Kumar", age=32, sex="Male", address="Hyderabad", phone="9876543210")
    db.session.add(patient)
    db.session.flush()

    consultation = Consultation(
        patient_id=patient.id,
        doctor_id=doctor.id,
        receptionist_id=receptionist.id,
        main_complaints="Fever, body ache",
        weight="70",
        bp="120/80",
        status="Queued",
    )
    db.session.add(consultation)
    db.session.commit()

    db.session.add_all(
        [
            PharmacyMedicine(name="Paracetamol 500mg", quantity=50, description="Tablets"),
            PharmacyMedicine(name="Azithromycin 250mg", quantity=30, description="Tablets"),
            PharmacyMedicine(name="Vitamin C", quantity=40, description="Supplements"),
        ]
    )
    db.session.commit()


with app.app_context():
    initialize_database()


if __name__ == "__main__":
    app.run(debug=True)
