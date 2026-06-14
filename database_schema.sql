
CREATE DATABASE IF NOT EXISTS lending_db;
USE lending_db;


CREATE TABLE IF NOT EXISTS users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    role VARCHAR(20) NOT NULL, 
    contact_no VARCHAR(20),
    face_photo VARCHAR(255),
    id_photo VARCHAR(255),
    video_proof VARCHAR(255),
    account_status VARCHAR(20) DEFAULT 'pending', 
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


CREATE TABLE IF NOT EXISTS loans (
    id INT PRIMARY KEY AUTO_INCREMENT,
    lendee_id INT NOT NULL,
    loan_type VARCHAR(50), 
    item_details VARCHAR(255),
    total_amount DECIMAL(10,2) NOT NULL,
    remaining_balance DECIMAL(10,2) NOT NULL,
    due_date DATE,
    penalty DECIMAL(10,2) DEFAULT 0,
    status VARCHAR(20) DEFAULT 'pending', 
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (lendee_id) REFERENCES users(id)
);


CREATE TABLE IF NOT EXISTS payments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    loan_id INT NOT NULL,
    payment_amount DECIMAL(10,2) NOT NULL,
    payment_date DATE NOT NULL,
    payment_method VARCHAR(50),
    status VARCHAR(20) DEFAULT 'completed', 
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (loan_id) REFERENCES loans(id)
);


CREATE TABLE IF NOT EXISTS notifications (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    message TEXT NOT NULL,
    type VARCHAR(50),
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);


CREATE INDEX idx_lendee_id ON loans(lendee_id);
CREATE INDEX idx_loan_id ON payments(loan_id);
CREATE INDEX idx_user_id ON notifications(user_id);
CREATE INDEX idx_status ON loans(status);
