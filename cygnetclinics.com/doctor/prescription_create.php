<?php
$allowedRoles = ['doctor'];
$portalTitle = 'Doctor Portal';
$portalNav = [
    ['href' => '/cygnetclinics.com/doctor/dashboard.php', 'label' => 'Dashboard'],
    ['href' => '/cygnetclinics.com/doctor/appointments.php', 'label' => 'Appointments'],
    ['href' => '/cygnetclinics.com/doctor/prescription_create.php', 'label' => 'New prescription'],
    ['href' => '/cygnetclinics.com/doctor/prescriptions.php', 'label' => 'My prescriptions'],
];
require_once __DIR__ . '/../includes/portal_header.php';

$patients = fetch_all('patients');

if (is_post()) {
    $patientId = (int)($_POST['patient_id'] ?? 0);
    $newPatientName = sanitize($_POST['new_patient_name'] ?? '');
    $newPatientPhone = sanitize($_POST['new_patient_phone'] ?? '');
    $diagnosis = sanitize($_POST['diagnosis'] ?? '');
    $complaints = sanitize($_POST['complaints'] ?? '');
    $medications = sanitize($_POST['medications'] ?? '');
    $labTests = sanitize($_POST['lab_tests'] ?? '');
    $followUp = sanitize($_POST['follow_up_date'] ?? '');

    if (!$patientId && $newPatientName) {
        $patientId = insert('patients', [
            'name' => $newPatientName,
            'contact_number' => $newPatientPhone,
            'created_at' => date('Y-m-d H:i:s')
        ]);
    }

    if ($patientId) {
        $prescriptionId = insert('prescriptions', [
            'doctor_id' => $user['id'],
            'patient_id' => $patientId,
            'diagnosis' => $diagnosis,
            'complaints' => $complaints,
            'medications' => $medications,
            'lab_tests' => $labTests,
            'follow_up_date' => $followUp ?: null,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        insert('pharmacy_orders', [
            'prescription_id' => $prescriptionId,
            'status' => 'pending',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);
        flash('success', 'Prescription generated successfully.');
        redirect('/cygnetclinics.com/doctor/prescriptions.php');
    } else {
        flash('error', 'Please select or create a patient.');
        redirect('/cygnetclinics.com/doctor/prescription_create.php');
    }
}

$success = flash('success');
$error = flash('error');
?>
<h1>Create prescription</h1>
<?php if ($success): ?>
    <div class="alert alert-success" data-timeout="5000"><?php echo $success; ?></div>
<?php endif; ?>
<?php if ($error): ?>
    <div class="alert alert-error" data-timeout="5000"><?php echo $error; ?></div>
<?php endif; ?>
<div class="card">
    <form method="post">
        <h2>Patient details</h2>
        <label for="patient_id">Existing patient</label>
        <select name="patient_id" id="patient_id">
            <option value="0">Select patient</option>
            <?php foreach ($patients as $patient): ?>
                <option value="<?php echo $patient['id']; ?>"><?php echo $patient['name']; ?></option>
            <?php endforeach; ?>
        </select>

        <p style="margin: 1rem 0; font-weight: 600;">Or add a new patient</p>
        <label for="new_patient_name">Patient name</label>
        <input type="text" name="new_patient_name" id="new_patient_name" placeholder="New patient name">
        <label for="new_patient_phone">Phone</label>
        <input type="text" name="new_patient_phone" id="new_patient_phone" placeholder="Phone number">

        <h2>Clinical information</h2>
        <label for="complaints">Chief complaints</label>
        <textarea name="complaints" id="complaints" rows="3"></textarea>

        <label for="diagnosis">Diagnosis</label>
        <textarea name="diagnosis" id="diagnosis" rows="3"></textarea>

        <label for="medications">Medications & dosage</label>
        <textarea name="medications" id="medications" rows="5" placeholder="e.g. Amoxicillin 500mg - take twice daily for 5 days"></textarea>

        <label for="lab_tests">Recommended lab tests</label>
        <textarea name="lab_tests" id="lab_tests" rows="3"></textarea>

        <label for="follow_up_date">Follow up date</label>
        <input type="date" name="follow_up_date" id="follow_up_date">

        <div class="form-actions">
            <button class="btn btn-primary" type="submit">Generate prescription</button>
            <a class="link-button" href="/cygnetclinics.com/doctor/dashboard.php">Cancel</a>
        </div>
    </form>
</div>
<?php include __DIR__ . '/../includes/portal_footer.php'; ?>
