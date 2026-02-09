CREATE DATABASE IF NOT EXISTS spark;
USE spark;

-- Users Table
CREATE TABLE IF NOT EXISTS users (
    userId INT AUTO_INCREMENT PRIMARY KEY,
    fullName VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL, -- Storing plain text as requested
    role ENUM('student', 'admin', 'departmentCoordinator', 'studentAffairs') NOT NULL DEFAULT 'student',
    departmentId INT DEFAULT NULL,
    createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Departments Table
CREATE TABLE IF NOT EXISTS departments (
    departmentId INT AUTO_INCREMENT PRIMARY KEY,
    departmentName VARCHAR(100) NOT NULL UNIQUE,
    coordinatorId INT DEFAULT NULL,
    FOREIGN KEY (coordinatorId) REFERENCES users(userId) ON DELETE SET NULL
);

-- Projects Table
CREATE TABLE IF NOT EXISTS projects (
    projectId INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    description TEXT,
    studentId INT NOT NULL,
    departmentId INT DEFAULT NULL,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    submissionDate TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (studentId) REFERENCES users(userId) ON DELETE CASCADE,
    FOREIGN KEY (departmentId) REFERENCES departments(departmentId) ON DELETE SET NULL
);

-- Notifications Table
CREATE TABLE IF NOT EXISTS notifications (
    notificationId INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(100) NOT NULL,
    message TEXT NOT NULL,
    targetRole ENUM('all', 'student', 'admin', 'departmentCoordinator', 'studentAffairs') DEFAULT 'all',
    createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Schedule Table
CREATE TABLE IF NOT EXISTS schedule (
    scheduleId INT AUTO_INCREMENT PRIMARY KEY,
    eventTitle VARCHAR(150) NOT NULL,
    eventDate DATETIME NOT NULL,
    description TEXT
);

-- Insert Default Departments
INSERT INTO departments (departmentName) VALUES 
('Computer Science'),
('Information Technology'),
('Electronics & Communication'),
('Mechanical Engineering'),
('Civil Engineering');

-- Insert Default Admin User
INSERT INTO users (fullName, email, password, role) VALUES 
('System Admin', 'admin@spark.com', 'admin123', 'admin');

-- Insert Sample Coordinator
INSERT INTO users (fullName, email, password, role, departmentId) VALUES 
('Coord One', 'coord@spark.com', 'coord123', 'departmentCoordinator', 1);

-- Insert Sample Student Affairs
INSERT INTO users (fullName, email, password, role) VALUES 
('Student Affairs Head', 'affairs@spark.com', 'affairs123', 'studentAffairs');

-- Insert Sample Student
INSERT INTO users (fullName, email, password, role, departmentId) VALUES 
('John Doe', 'student@spark.com', 'student123', 'student', 1);
