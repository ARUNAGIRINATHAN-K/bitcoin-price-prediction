CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('teacher','admin','gov') DEFAULT 'teacher',
    school_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (school_id) REFERENCES schools(school_id)
);
CREATE TABLE schools (
    school_id INT AUTO_INCREMENT PRIMARY KEY,
    school_name VARCHAR(150) NOT NULL,
    district VARCHAR(100),
    state VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
CREATE TABLE students (
    student_id INT AUTO_INCREMENT PRIMARY KEY,
    school_id INT NOT NULL,
    admission_no VARCHAR(50) UNIQUE,
    name VARCHAR(100) NOT NULL,
    gender ENUM('M','F','O') NOT NULL,
    dob DATE,
    class VARCHAR(20),
    section VARCHAR(5),
    photo_url VARCHAR(255),         -- stored image file
    face_embedding TEXT,            -- JSON vector from face-api.js
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (school_id) REFERENCES schools(school_id)
);
CREATE TABLE attendance_sessions (
    session_id INT AUTO_INCREMENT PRIMARY KEY,
    school_id INT NOT NULL,
    teacher_id INT NOT NULL,
    class VARCHAR(20),
    section VARCHAR(5),
    session_date DATE NOT NULL,
    start_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    sync_status ENUM('pending','synced') DEFAULT 'pending',
    FOREIGN KEY (school_id) REFERENCES schools(school_id),
    FOREIGN KEY (teacher_id) REFERENCES users(user_id)
);
CREATE TABLE attendance_logs (
    log_id INT AUTO_INCREMENT PRIMARY KEY,
    session_id INT NOT NULL,
    student_id INT NOT NULL,
    status ENUM('present','absent') NOT NULL,
    captured_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    sync_status ENUM('pending','synced') DEFAULT 'pending',
    UNIQUE(session_id, student_id),  -- avoids duplicates
    FOREIGN KEY (session_id) REFERENCES attendance_sessions(session_id),
    FOREIGN KEY (student_id) REFERENCES students(student_id)
);
CREATE TABLE sync_queue (
    sync_id INT AUTO_INCREMENT PRIMARY KEY,
    device_id VARCHAR(100),
    payload JSON,                 -- raw attendance data
    sync_status ENUM('pending','completed') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
