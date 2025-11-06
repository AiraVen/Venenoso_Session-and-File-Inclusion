CREATE DATABASE IF NOT EXISTS eshop
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;
USE eshop;

CREATE TABLE users (
    id              INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    email           VARCHAR(191),
    password_hash   VARCHAR(255),
    first_name      VARCHAR(60),
    last_name       VARCHAR(60),
    contact         VARCHAR(20),
    address         TEXT,
    role            ENUM('customer','admin') NOT NULL DEFAULT 'customer',
    created_at      TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    last_login_at   TIMESTAMP NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
netsh
CREATE TABLE products (
    product_id INT AUTO_INCREMENT PRIMARY KEY,
    product_name VARCHAR(150) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    image_path VARCHAR(255),
    date_added TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO products (product_name, description, price, image_path) VALUES
('Elegant Chair','Comfortable upholstered accent chair — perfect for living room and office. Solid frame, soft cushion, available in multiple colors.', 1599, 'Product_IMG/princess-chair-12.jpg'),
('Smart Lamp', 'Sleek metal design with adjustable head — brightens any corner. Energy-efficient LED compatible.', 599, 'Product_IMG/Lamp.jpg'),
('Modern Desk', 'Sturdy wooden desktop with clean lines — ideal for home office and study.', 1699, 'Product_IMG/product desk.jpg'),
('iPhone 17', 'Next‑gen performance and advanced camera system with all‑day battery life.', 59999, 'Product_IMG/iPhone_17_Black_PDP_Image_Positi.jpg'),
('iPhone 17 Pro', 'Pro-grade cameras, titanium design, and powerful chipset for creators.', 69999, 'Product_IMG/Apple-iPhone-17-Pro-Deep-Blue.jpg'),
('iPhone 17 Pro Max', 'Largest iPhone 17 display with best battery life and pro camera system.', 79999, 'Product_IMG/iPhone_17_Pro_Max.jpg'),
('iPhone 17 Air', 'Ultra‑light design with vibrant display and fast performance.', 75999, 'Product_IMG/iphone-17-air-could-be-available.jpg'),
('iPhone 16', 'Fast chip, bright display, and reliable all‑day battery for everyday use.', 59999, 'Product_IMG/iPhone_16_Ultramarine_PDP_Image.jpg'),
('iPhone 16 Pro', 'Pro cameras and premium build with exceptional performance.', 69999, 'Product_IMG/iPhone_16_Pro_Desert_Titanium_PD.jpg'),
('iPhone 16 Pro Max', 'Biggest display in the 16 series with top‑tier battery and cameras.', 79999, 'Product_IMG/iPhone_16_Pro_Max_Black_Titanium.jpg'),
('iPhone 15', 'Powerful features and excellent value with great camera quality.', 49999, 'Product_IMG/15.jpg'),
('iPhone 15 Pro', 'Lightweight design, fast performance, and pro‑level camera capabilities.', 59999, 'Product_IMG/15 PR.jpg'),
('iPhone 15 Pro Max', 'Large immersive display and the most advanced cameras in the 15 line.', 69999, 'Product_IMG/iPhone_15_Pro_Max_Blue_Titanium.jpg'),
('RK61 Mechanical Keyboard', 'Compact 60% mechanical keyboard with wireless connectivity and RGB lighting.', 5999, 'Product_IMG/RK61_-1.jpg'),
('Logitech G304 Lightspeed Wireless Gaming Mouse [White]', 'Lightweight wireless mouse with HERO sensor and ultra‑low latency.', 3999, 'Product_IMG/LOGITECH-G304-LIGHTSPEED-WIRELES.jpg');

