-- Create FleetFlow Database
CREATE DATABASE IF NOT EXISTS fleetflow CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE fleetflow;

-- Users table
CREATE TABLE IF NOT EXISTS users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    email_verified_at TIMESTAMP NULL,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(255) NOT NULL DEFAULT 'user',
    remember_token VARCHAR(100) NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Vehicles table
CREATE TABLE IF NOT EXISTS vehicles (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    model VARCHAR(255) NOT NULL,
    license_plate VARCHAR(255) UNIQUE NOT NULL,
    max_capacity DECIMAL(10,2) NOT NULL,
    odometer DECIMAL(10,2) NOT NULL,
    status ENUM('available', 'on_trip', 'in_shop', 'out_of_service') NOT NULL DEFAULT 'available',
    out_of_service BOOLEAN NOT NULL DEFAULT FALSE,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Drivers table
CREATE TABLE IF NOT EXISTS drivers (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    license_number VARCHAR(255) NOT NULL,
    license_expiry DATE NOT NULL,
    phone VARCHAR(20) NOT NULL,
    status ENUM('available', 'on_duty', 'off_duty') NOT NULL DEFAULT 'available',
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Trips table
CREATE TABLE IF NOT EXISTS trips (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    vehicle_id BIGINT UNSIGNED NOT NULL,
    driver_id BIGINT UNSIGNED NOT NULL,
    origin VARCHAR(255) NOT NULL,
    destination VARCHAR(255) NOT NULL,
    cargo_weight DECIMAL(10,2) NOT NULL,
    distance DECIMAL(10,2) NOT NULL,
    estimated_duration INT NOT NULL,
    status ENUM('draft', 'dispatched', 'on_trip', 'completed', 'cancelled') NOT NULL DEFAULT 'draft',
    started_at TIMESTAMP NULL,
    completed_at TIMESTAMP NULL,
    arrived_late BOOLEAN NOT NULL DEFAULT FALSE,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (vehicle_id) REFERENCES vehicles(id),
    FOREIGN KEY (driver_id) REFERENCES drivers(id)
);

-- Maintenance logs table
CREATE TABLE IF NOT EXISTS maintenance_logs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    vehicle_id BIGINT UNSIGNED NOT NULL,
    type VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    cost DECIMAL(10,2) NOT NULL,
    odometer_at_service DECIMAL(10,2) NOT NULL,
    performed_at TIMESTAMP NOT NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (vehicle_id) REFERENCES vehicles(id)
);

-- Fuel logs table
CREATE TABLE IF NOT EXISTS fuel_logs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    vehicle_id BIGINT UNSIGNED NOT NULL,
    liters DECIMAL(10,2) NOT NULL,
    cost_per_liter DECIMAL(10,2) NOT NULL,
    cost DECIMAL(10,2) NOT NULL,
    odometer DECIMAL(10,2) NOT NULL,
    fuel_date TIMESTAMP NOT NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (vehicle_id) REFERENCES vehicles(id)
);

-- Driver scores table
CREATE TABLE IF NOT EXISTS driver_scores (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    driver_id BIGINT UNSIGNED NOT NULL,
    score DECIMAL(5,2) NOT NULL,
    reason TEXT NOT NULL,
    score_date TIMESTAMP NOT NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (driver_id) REFERENCES drivers(id)
);

-- Trip status history table
CREATE TABLE IF NOT EXISTS trip_status_history (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    trip_id BIGINT UNSIGNED NOT NULL,
    old_status VARCHAR(255) NULL,
    new_status VARCHAR(255) NOT NULL,
    changed_at TIMESTAMP NOT NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (trip_id) REFERENCES trips(id)
);

-- Notifications table
CREATE TABLE IF NOT EXISTS notifications (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    title VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    type VARCHAR(255) NOT NULL,
    read_at TIMESTAMP NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Insert sample users
INSERT INTO users (name, email, password, role) VALUES
('John Manager', 'manager@fleetflow.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'manager'),
('Sarah Dispatcher', 'dispatcher@fleetflow.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'dispatcher'),
('Mike Safety', 'safety@fleetflow.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'safety'),
('Lisa Finance', 'finance@fleetflow.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'finance');

-- Insert sample vehicles
INSERT INTO vehicles (model, license_plate, max_capacity, odometer, status) VALUES
('Ford F-150', 'ABC-123', 1000.00, 45000.00, 'available'),
('Chevrolet Silverado', 'XYZ-789', 1200.00, 52000.00, 'available'),
('Ram 1500', 'DEF-456', 1100.00, 38000.00, 'in_shop'),
('GMC Sierra', 'GHI-012', 1300.00, 61000.00, 'available'),
('Toyota Tundra', 'JKL-345', 1050.00, 29000.00, 'on_trip');

-- Insert sample drivers
INSERT INTO drivers (name, license_number, license_expiry, phone, status) VALUES
('John Smith', 'DL123456', '2024-12-31', '+1-555-0101', 'available'),
('Sarah Johnson', 'DL789012', '2025-06-30', '+1-555-0102', 'on_duty'),
('Mike Wilson', 'DL345678', '2024-09-15', '+1-555-0103', 'available'),
('Lisa Brown', 'DL901234', '2025-03-20', '+1-555-0104', 'off_duty');

-- Insert sample trips
INSERT INTO trips (vehicle_id, driver_id, origin, destination, cargo_weight, distance, estimated_duration, status, started_at, completed_at) VALUES
(1, 1, 'New York', 'Boston', 800.00, 350.00, 270, 'completed', '2024-02-20 08:00:00', '2024-02-20 14:30:00'),
(2, 2, 'Boston', 'Philadelphia', 600.00, 300.00, 240, 'completed', '2024-02-20 09:00:00', '2024-02-20 13:00:00'),
(5, 1, 'Philadelphia', 'Washington DC', 500.00, 200.00, 180, 'on_trip', '2024-02-21 08:00:00', NULL);

-- Insert sample maintenance logs
INSERT INTO maintenance_logs (vehicle_id, type, description, cost, odometer_at_service, performed_at) VALUES
(1, 'Oil Change', 'Regular oil change and filter replacement', 75.00, 44000.00, '2024-02-15 10:00:00'),
(2, 'Tire Rotation', 'Tire rotation and balancing', 120.00, 50000.00, '2024-02-18 14:00:00'),
(3, 'Brake Service', 'Brake pad replacement and rotor resurfacing', 350.00, 37000.00, '2024-02-19 09:00:00');

-- Insert sample fuel logs
INSERT INTO fuel_logs (vehicle_id, liters, cost_per_liter, cost, odometer, fuel_date) VALUES
(1, 50.00, 1.45, 72.50, 45050.00, '2024-02-20 08:00:00'),
(2, 60.00, 1.42, 85.20, 52060.00, '2024-02-20 09:30:00'),
(5, 45.00, 1.48, 66.60, 29045.00, '2024-02-21 07:30:00');

-- Insert sample driver scores
INSERT INTO driver_scores (driver_id, score, reason, score_date) VALUES
(1, 2.5, 'Good performance, no incidents', '2024-02-20 15:00:00'),
(2, 4.0, 'One late arrival this week', '2024-02-20 16:00:00'),
(3, 1.0, 'Excellent driving record', '2024-02-20 17:00:00');

-- Insert sample notifications
INSERT INTO notifications (user_id, title, message, type) VALUES
(1, 'Maintenance Alert', 'Vehicle ABC-123 requires maintenance soon', 'maintenance'),
(2, 'Trip Completed', 'Trip to Boston has been completed', 'trip'),
(3, 'License Expiry', 'Driver Lisa Brown license expiring soon', 'license'),
(4, 'Cost Alert', 'Monthly fuel costs increased by 15%', 'cost');
