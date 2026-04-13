CREATE DATABASE IF NOT EXISTS `fp_db`;
USE `fp_db`;

CREATE TABLE IF NOT EXISTS `users` (
    `user_id` INT AUTO_INCREMENT PRIMARY KEY,
    `firstname` VARCHAR(255) NOT NULL,
    `lastname` VARCHAR(255) NOT NULL,
    `address` TEXT NOT NULL,
    `phonenumber` VARCHAR(20) NOT NULL,
    `email` VARCHAR(255) NOT NULL,
    `password` VARCHAR(255) NOT NULL
);

CREATE TABLE IF NOT EXISTS `vendor` (
    `vendor_id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL,
    `email` VARCHAR(255) NOT NULL,
    `contact_info` VARCHAR(50) NOT NULL,
    `address` TEXT NOT NULL,
    `password` VARCHAR(255) NOT NULL
);

CREATE TABLE IF NOT EXISTS `stall` (
    `stall_id` INT AUTO_INCREMENT PRIMARY KEY,
    `vendor_id` INT NOT NULL,
    `stall_number` VARCHAR(50),
    `location_description` TEXT,
    `name` VARCHAR(255) NOT NULL,
    `owner` VARCHAR(100),
    `description` TEXT,
    `price_range` VARCHAR(50),
    `contact` VARCHAR(50),
    `hours` VARCHAR(100),
    `image` VARCHAR(255),
    `availability` VARCHAR(50),
    `special_offers` TEXT,
    `accessibility_info` TEXT
);

CREATE TABLE IF NOT EXISTS `menu_items` (
    `menu_items_id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL,
    `description` TEXT,
    `price` DECIMAL(10,2) NOT NULL,
    `category` VARCHAR(100),
    `size` VARCHAR(50),
    `image` VARCHAR(255),
    `vendor_id` INT,
    `stall_id` INT,
    `quantity` INT DEFAULT 1
);

CREATE TABLE IF NOT EXISTS `cart` (
    `cart_id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `menu_items_id` INT NOT NULL,
    `quantity` INT DEFAULT 1
);

CREATE TABLE IF NOT EXISTS `orders` (
    `order_id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `vendor_id` INT NOT NULL,
    `time_stamp` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `status` VARCHAR(50) DEFAULT 'Pending'
);

CREATE TABLE IF NOT EXISTS `order_items` (
    `order_items_id` INT AUTO_INCREMENT PRIMARY KEY,
    `order_id` INT NOT NULL,
    `menu_items_id` INT NOT NULL,
    `quantity` INT DEFAULT 1,
    `price` DECIMAL(10,2)
);

CREATE TABLE IF NOT EXISTS `review` (
    `review_id` INT AUTO_INCREMENT PRIMARY KEY,
    `vendor_id` INT NOT NULL,
    `user_id` INT NOT NULL,
    `rating` INT DEFAULT 5,
    `comment` TEXT,
    `time_stamp` DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS `booking` (
    `booking_id` INT AUTO_INCREMENT PRIMARY KEY,
    `vendor_id` INT NOT NULL,
    `user_id` INT,
    `booking_status` VARCHAR(50) DEFAULT 'pending'
);

CREATE TABLE IF NOT EXISTS `stall_rent` (
    `rent_id` INT AUTO_INCREMENT PRIMARY KEY,
    `vendor_id` INT NOT NULL,
    `payment_status` VARCHAR(50) DEFAULT 'pending'
);

DROP TABLE IF EXISTS `order`;
DROP VIEW IF EXISTS `order`;
CREATE VIEW `order` AS SELECT * FROM `orders`;
