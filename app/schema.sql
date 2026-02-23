CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    email VARCHAR(190) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at DATETIME NOT NULL
);

CREATE TABLE IF NOT EXISTS settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(120) NOT NULL UNIQUE,
    setting_value TEXT NULL
);

CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    slug VARCHAR(160) NOT NULL UNIQUE,
    created_at DATETIME NOT NULL
);

CREATE TABLE IF NOT EXISTS tags (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    slug VARCHAR(160) NOT NULL UNIQUE,
    created_at DATETIME NOT NULL
);

CREATE TABLE IF NOT EXISTS articles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    excerpt TEXT,
    body LONGTEXT NOT NULL,
    seo_title VARCHAR(255) NULL,
    seo_description TEXT NULL,
    status ENUM('draft', 'published') NOT NULL DEFAULT 'draft',
    featured TINYINT(1) NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL
);

CREATE TABLE IF NOT EXISTS article_category (
    article_id INT NOT NULL,
    category_id INT NOT NULL,
    PRIMARY KEY (article_id, category_id),
    FOREIGN KEY (article_id) REFERENCES articles(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS article_tag (
    article_id INT NOT NULL,
    tag_id INT NOT NULL,
    PRIMARY KEY (article_id, tag_id),
    FOREIGN KEY (article_id) REFERENCES articles(id) ON DELETE CASCADE,
    FOREIGN KEY (tag_id) REFERENCES tags(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS appointment_slots (
    id INT AUTO_INCREMENT PRIMARY KEY,
    slot_date DATE NOT NULL,
    slot_time TIME NOT NULL,
    is_booked TINYINT(1) NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL
);

CREATE TABLE IF NOT EXISTS appointments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    patient_name VARCHAR(160) NOT NULL,
    patient_email VARCHAR(190) NOT NULL,
    patient_phone VARCHAR(50) NULL,
    notes TEXT NULL,
    slot_id INT NOT NULL,
    created_at DATETIME NOT NULL,
    FOREIGN KEY (slot_id) REFERENCES appointment_slots(id)
);
