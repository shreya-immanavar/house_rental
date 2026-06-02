CREATE DATABASE house_rental;
USE house_rental;

-- USERS
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    email VARCHAR(100),
    password VARCHAR(100),
    role ENUM('admin','user') DEFAULT 'user'
);

-- PROPERTIES
CREATE TABLE properties (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(100),
    category VARCHAR(50) DEFAULT 'House',
    location VARCHAR(100),
    price INT,
    description TEXT,
    tags VARCHAR(255) DEFAULT ''
);

-- MULTIPLE IMAGES
CREATE TABLE property_images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    property_id INT,
    image VARCHAR(255),
    FOREIGN KEY (property_id) REFERENCES properties(id) ON DELETE CASCADE
);

-- BOOKINGS
CREATE TABLE bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    property_id INT,
    tenant_phone VARCHAR(20),
    move_in_date DATE,
    end_date DATE,
    occupants INT DEFAULT 1,
    notes TEXT,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    booking_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- WISHLIST
CREATE TABLE wishlist (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    property_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY user_property (user_id, property_id)
);

-- MESSAGES
CREATE TABLE messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sender_id INT NOT NULL,
    receiver_id INT NOT NULL,
    property_id INT NULL,
    message TEXT NOT NULL,
    is_read TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ADMIN LOGIN
INSERT INTO users (name,email,password,role)
VALUES ('Admin','admin@gmail.com','1234','admin');