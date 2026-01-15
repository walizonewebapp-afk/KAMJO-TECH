-- Fix vehicles table
ALTER TABLE vehicles ADD COLUMN IF NOT EXISTS mileage INT NULL AFTER color;
ALTER TABLE vehicles ADD COLUMN IF NOT EXISTS engine_type VARCHAR(100) NULL AFTER mileage;
ALTER TABLE vehicles ADD COLUMN IF NOT EXISTS transmission VARCHAR(50) NULL AFTER engine_type;
ALTER TABLE vehicles ADD COLUMN IF NOT EXISTS status VARCHAR(20) DEFAULT 'active' AFTER transmission;

-- Create service_history table if it doesn't exist
CREATE TABLE IF NOT EXISTS service_history (
    id INT AUTO_INCREMENT PRIMARY KEY,
    vehicle_id INT NOT NULL,
    service_date DATE NOT NULL,
    service_type VARCHAR(100) NOT NULL,m
    description TEXT,
    mileage INT,
    cost DECIMAL(10,2),
    technician VARCHAR(100),
    status VARCHAR(20) DEFAULT 'scheduled',
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Create vehicle_documents table if it doesn't exist
CREATE TABLE IF NOT EXISTS vehicle_documents (
    id INT AUTO_INCREMENT PRIMARY KEY,
    vehicle_id INT NOT NULL,
    document_type VARCHAR(100) NOT NULL,
    file_name VARCHAR(255) NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create vehicle_reminders table if it doesn't exist
CREATE TABLE IF NOT EXISTS vehicle_reminders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    vehicle_id INT NOT NULL,
    reminder_type VARCHAR(100) NOT NULL,
    due_date DATE NOT NULL,
    description TEXT,
    status VARCHAR(20) DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Add foreign keys (these will fail if the tables don't exist, but that's okay)
ALTER TABLE service_history ADD CONSTRAINT IF NOT EXISTS fk_vehicle_id FOREIGN KEY (vehicle_id) REFERENCES vehicles(id) ON DELETE CASCADE;
ALTER TABLE vehicle_documents ADD CONSTRAINT IF NOT EXISTS fk_doc_vehicle_id FOREIGN KEY (vehicle_id) REFERENCES vehicles(id) ON DELETE CASCADE;
ALTER TABLE vehicle_reminders ADD CONSTRAINT IF NOT EXISTS fk_reminder_vehicle_id FOREIGN KEY (vehicle_id) REFERENCES vehicles(id) ON DELETE CASCADE;