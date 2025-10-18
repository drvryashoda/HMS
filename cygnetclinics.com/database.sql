CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    email VARCHAR(160) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin','doctor','receptionist','lab','pharmacy') NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO users (name, email, password, role) VALUES
('System Administrator', 'admin@cygnetclinics.com', '$2y$12$1dJaH1AZGryxH29iDLAMO.ejcOg6T7nYJiR8yV80/6/KSWB2BLZ/q', 'admin')
ON DUPLICATE KEY UPDATE email = VALUES(email);

CREATE TABLE IF NOT EXISTS banners (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(160) NOT NULL,
    subtitle TEXT NOT NULL,
    image_url VARCHAR(255) NOT NULL,
    cta_label VARCHAR(80) NULL,
    cta_link VARCHAR(255) NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS about_sections (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(160) NOT NULL,
    content TEXT NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(160) NOT NULL,
    description TEXT NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS blog_posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    content LONGTEXT NOT NULL,
    published_at DATE NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS contact_details (
    id INT AUTO_INCREMENT PRIMARY KEY,
    label VARCHAR(120) NOT NULL,
    value VARCHAR(255) NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS contact_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(160) NOT NULL,
    email VARCHAR(160) NOT NULL,
    phone VARCHAR(40) NULL,
    message TEXT NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS patients (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(160) NOT NULL,
    date_of_birth DATE NULL,
    gender VARCHAR(20) NULL,
    contact_number VARCHAR(40) NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS appointments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    patient_id INT NULL,
    doctor_id INT NULL,
    scheduled_at DATETIME NOT NULL,
    status ENUM('scheduled','in-progress','completed','cancelled') NOT NULL DEFAULT 'scheduled',
    notes TEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE SET NULL,
    FOREIGN KEY (doctor_id) REFERENCES users(id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS prescriptions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    doctor_id INT NOT NULL,
    patient_id INT NULL,
    diagnosis TEXT NULL,
    complaints TEXT NULL,
    medications TEXT NULL,
    lab_tests TEXT NULL,
    follow_up_date DATE NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (doctor_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS lab_tests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    patient_id INT NULL,
    test_name VARCHAR(200) NOT NULL,
    status ENUM('pending','in-progress','completed') NOT NULL DEFAULT 'pending',
    results TEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    completed_at DATETIME NULL,
    FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS pharmacy_orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    prescription_id INT NOT NULL,
    status ENUM('pending','dispensed','on-hold') NOT NULL DEFAULT 'pending',
    notes TEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (prescription_id) REFERENCES prescriptions(id) ON DELETE CASCADE
);

-- Seed homepage defaults
INSERT INTO banners (title, subtitle, image_url, cta_label, cta_link)
SELECT * FROM (
    SELECT 'Expert care for every family', 'Consult highly experienced specialists across primary care, diagnostics and pharmacy services.', 'https://images.unsplash.com/photo-1580281657521-939e3f3f1720', 'Book appointment', '#contact'
) AS tmp
WHERE NOT EXISTS (SELECT 1 FROM banners);

INSERT INTO services (name, description)
SELECT * FROM (
    SELECT 'Family Medicine', 'Preventive care, chronic disease management and same-day visits.'
) AS tmp
WHERE NOT EXISTS (SELECT 1 FROM services);

INSERT INTO about_sections (title, content)
SELECT * FROM (
    SELECT 'Trusted neighbourhood clinic', 'Serving the community with evidence-based medicine, modern diagnostics and compassionate care.'
) AS tmp
WHERE NOT EXISTS (SELECT 1 FROM about_sections);

INSERT INTO contact_details (label, value)
SELECT * FROM (
    SELECT 'Phone', '+91 98765 43210'
) AS tmp
WHERE NOT EXISTS (SELECT 1 FROM contact_details);
