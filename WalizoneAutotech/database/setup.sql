-- Create database if it doesn't exist
CREATE DATABASE IF NOT EXISTS walizone_autotech;

-- Use the database
USE walizone_autotech;

-- Contact messages table
CREATE TABLE IF NOT EXISTS contact_messages (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    message TEXT NOT NULL,
    created_at DATETIME NOT NULL,
    status ENUM('new', 'read', 'replied') DEFAULT 'new'
);

-- Services table
CREATE TABLE IF NOT EXISTS services (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    icon VARCHAR(50),
    created_at DATETIME NOT NULL
);

-- Users table for authentication
CREATE TABLE IF NOT EXISTS users (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100),
    role ENUM('admin', 'staff') DEFAULT 'staff',
    created_at DATETIME NOT NULL,
    last_login DATETIME
);

-- Appointments table
CREATE TABLE IF NOT EXISTS appointments (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    service VARCHAR(100) NOT NULL,
    appointment_date DATE NOT NULL,
    appointment_time TIME NOT NULL,
    message TEXT,
    status ENUM('pending', 'confirmed', 'completed', 'cancelled') DEFAULT 'pending',
    created_at DATETIME NOT NULL
);

-- Insert sample services
INSERT INTO services (name, description, icon, created_at) VALUES
('Routine Maintenance', 'Oil changes, tire rotations, brake checks, and more.', 'wrench', NOW()),
('Computer Diagnostics', 'Advanced computerized vehicle diagnostics and repairs.', 'laptop', NOW()),
('Engine & Transmission Repair', 'Full service engine repairs, transmission fixes, and replacements.', 'cogs', NOW()),
('AC & Heating Services', 'AC recharge, heating system repairs, and diagnostics.', 'snowflake', NOW()),
('Suspension & Steering', 'Repairing shocks, struts, and ensuring a smooth ride.', 'car', NOW()),
('Panel Beating & Painting', 'High-performance panel beating and custom spray painting.', 'paint-brush', NOW()),
('Electrical Systems', 'Battery tests, alternator repairs, and full electrical diagnostics.', 'bolt', NOW()),
('Custom Modifications', 'Performance upgrades, body kits, and custom car designs.', 'tools', NOW());

-- Insert sample admin user (password: admin123)
INSERT INTO users (username, email, password, full_name, role, created_at) VALUES
('admin', 'mwakamule@gmail.com', '$2y$10$8KzO3LOgMxQQWJHXJM0YAuB5hPUMp.ZjQOQj5ggqPeD/4PEpRQOHi', 'System Administrator', 'admin', NOW());