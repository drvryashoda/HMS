CREATE DATABASE IF NOT EXISTS hms_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE hms_db;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(120) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin','doctor','nurse','receptionist','pharmacist','lab_tech') NOT NULL DEFAULT 'admin',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS patients (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    gender ENUM('male','female','other') NOT NULL,
    date_of_birth DATE NOT NULL,
    phone VARCHAR(25) NOT NULL,
    email VARCHAR(120),
    address TEXT,
    blood_group VARCHAR(8),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS doctors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    specialization VARCHAR(120) NOT NULL,
    phone VARCHAR(25) NOT NULL,
    email VARCHAR(120),
    room_no VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS appointments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    patient_id INT NOT NULL,
    doctor_id INT NOT NULL,
    appointment_date DATETIME NOT NULL,
    reason TEXT,
    status ENUM('scheduled','in_progress','completed','cancelled') DEFAULT 'scheduled',
    FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE CASCADE,
    FOREIGN KEY (doctor_id) REFERENCES doctors(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS admissions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    patient_id INT NOT NULL,
    ward VARCHAR(80) NOT NULL,
    bed_no VARCHAR(20) NOT NULL,
    admit_date DATE NOT NULL,
    discharge_date DATE NULL,
    diagnosis TEXT,
    status ENUM('admitted','discharged') DEFAULT 'admitted',
    FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS lab_tests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    patient_id INT NOT NULL,
    test_name VARCHAR(120) NOT NULL,
    ordered_date DATE NOT NULL,
    result TEXT,
    status ENUM('pending','completed') DEFAULT 'pending',
    FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS pharmacy (
    id INT AUTO_INCREMENT PRIMARY KEY,
    patient_id INT NOT NULL,
    medicine VARCHAR(120) NOT NULL,
    quantity INT NOT NULL,
    unit_price DECIMAL(10,2) NOT NULL,
    issue_date DATE NOT NULL,
    FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS billing (
    id INT AUTO_INCREMENT PRIMARY KEY,
    patient_id INT NOT NULL,
    bill_date DATE NOT NULL,
    consultation_fee DECIMAL(10,2) NOT NULL DEFAULT 0,
    lab_fee DECIMAL(10,2) NOT NULL DEFAULT 0,
    medicine_fee DECIMAL(10,2) NOT NULL DEFAULT 0,
    room_fee DECIMAL(10,2) NOT NULL DEFAULT 0,
    paid_status ENUM('pending','paid') DEFAULT 'pending',
    FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE CASCADE
);

INSERT INTO users (name, email, password, role)
VALUES ('System Administrator', 'admin@hospital.com', '$2y$10$PIx7rNgPuQ9bXoK4hJxQ4.gSLYvRwkfIPyw4jVhI9bX8jFjBwy6u2', 'admin')
ON DUPLICATE KEY UPDATE email=email;

INSERT INTO doctors (name, specialization, phone, email, room_no)
VALUES ('Dr. Laura Stevens', 'Cardiology', '555-1001', 'laura@hospital.com', 'C-12')
ON DUPLICATE KEY UPDATE name=name;

INSERT INTO patients (name, gender, date_of_birth, phone, email, address, blood_group)
VALUES ('Michael Stone', 'male', '1990-08-15', '555-3001', 'michael@example.com', '123 Main Street', 'A+')
ON DUPLICATE KEY UPDATE name=name;
