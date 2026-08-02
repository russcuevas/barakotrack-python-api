-- ========================================================
-- Barako Track: Smart Lost and Found Management System SQL
-- Target Database Engine: MySQL / MariaDB (phpMyAdmin Compatible)
-- ========================================================

CREATE DATABASE IF NOT EXISTS `barako_track` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `barako_track`;

-- 1. Users Table
DROP TABLE IF EXISTS `claims`;
DROP TABLE IF EXISTS `notifications`;
DROP TABLE IF EXISTS `chatbot_logs`;
DROP TABLE IF EXISTS `lost_items`;
DROP TABLE IF EXISTS `found_items`;
DROP TABLE IF EXISTS `categories`;
DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(255) NOT NULL,
  `email` VARCHAR(255) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `role` ENUM('student', 'admin', 'security') NOT NULL DEFAULT 'student',
  `student_id_number` VARCHAR(50) NULL,
  `phone` VARCHAR(30) NULL,
  `status` ENUM('active', 'suspended') NOT NULL DEFAULT 'active',
  `remember_token` VARCHAR(100) NULL,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Categories Table
CREATE TABLE `categories` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `slug` VARCHAR(100) NOT NULL UNIQUE,
  `icon` VARCHAR(50) NOT NULL DEFAULT 'bi-box-seam',
  `description` TEXT NULL,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Lost Items Table
CREATE TABLE `lost_items` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `category_id` BIGINT UNSIGNED NOT NULL,
  `title` VARCHAR(255) NOT NULL,
  `description` TEXT NOT NULL,
  `date_lost` DATE NOT NULL,
  `location` VARCHAR(255) NOT NULL,
  `image_path` VARCHAR(255) NULL,
  `feature_vector` LONGTEXT NULL,
  `status` ENUM('open', 'claim_pending', 'resolved', 'cancelled') NOT NULL DEFAULT 'open',
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Found Items Table
CREATE TABLE `found_items` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `category_id` BIGINT UNSIGNED NOT NULL,
  `title` VARCHAR(255) NOT NULL,
  `description` TEXT NOT NULL,
  `date_found` DATE NOT NULL,
  `location` VARCHAR(255) NOT NULL,
  `storage_location` VARCHAR(255) NOT NULL DEFAULT 'SAO Headquarters',
  `image_path` VARCHAR(255) NULL,
  `feature_vector` LONGTEXT NULL,
  `status` ENUM('available', 'claim_pending', 'claimed', 'disposed') NOT NULL DEFAULT 'available',
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. Claims Table
CREATE TABLE `claims` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `lost_item_id` BIGINT UNSIGNED NULL,
  `found_item_id` BIGINT UNSIGNED NOT NULL,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `proof_description` TEXT NOT NULL,
  `proof_image` VARCHAR(255) NULL,
  `status` ENUM('pending', 'under_review', 'approved', 'rejected', 'completed') NOT NULL DEFAULT 'pending',
  `admin_notes` TEXT NULL,
  `verified_by` BIGINT UNSIGNED NULL,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`found_item_id`) REFERENCES `found_items` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. Notifications Table
CREATE TABLE `notifications` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `title` VARCHAR(255) NOT NULL,
  `message` TEXT NOT NULL,
  `type` VARCHAR(50) NOT NULL DEFAULT 'info',
  `is_read` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 7. Chatbot Logs Table
CREATE TABLE `chatbot_logs` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `user_id` BIGINT UNSIGNED NULL,
  `user_query` TEXT NOT NULL,
  `bot_response` TEXT NOT NULL,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================================
-- SEED DATA
-- Default Password for all seed users: password123 (Bcrypt hashed)
-- ========================================================

-- Seed Admin User
INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `student_id_number`, `phone`) VALUES
(1, 'System Administrator', 'admin@ub.edu.ph', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.RAW2df4Wq', 'admin', 'ADM-2026-001', '09171234567'),
(2, 'Decsten Matibag', 'student@ub.edu.ph', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.RAW2df4Wq', 'student', 'UB-2024-8812', '09189876543');

-- Seed Categories
INSERT INTO `categories` (`id`, `name`, `slug`, `icon`, `description`) VALUES
(1, 'Electronics & Gadgets', 'electronics', 'bi-laptop', 'Laptops, Smartphones, Earbuds, Chargers, Powerbanks'),
(2, 'IDs & Cards', 'ids-cards', 'bi-card-heading', 'Student IDs, Driver License, ATM Cards, RFID Badges'),
(3, 'Bags & Wallets', 'bags-wallets', 'bi-backpack', 'Backpacks, Purses, Wallets, Pouches'),
(4, 'Books & Documents', 'books-documents', 'bi-journal-bookmark', 'Textbooks, Notebooks, Envelopes, Diplomas'),
(5, 'Keys & Accessories', 'keys-accessories', 'bi-key', 'Keys, Eyeglasses, Watches, Jewelry, Umbrellas'),
(6, 'Clothing & Uniforms', 'clothing', 'bi-tsheart', 'Jackets, PE Uniforms, Caps, Lab Coats');

-- Seed Sample Lost & Found Items
INSERT INTO `lost_items` (`id`, `user_id`, `category_id`, `title`, `description`, `date_lost`, `location`, `status`) VALUES
(1, 2, 1, 'Black Sony Noise Canceling Headphones', 'Black over-ear Wireless Headphones left on the 3rd floor library table near the window.', '2026-08-01', 'Main Library 3rd Floor', 'open'),
(2, 2, 2, 'Student ID Card - Decsten Matibag', 'University Student ID with maroon lanyard.', '2026-08-01', 'Student Center Cafeteria', 'open');

INSERT INTO `found_items` (`id`, `user_id`, `category_id`, `title`, `description`, `date_found`, `location`, `storage_location`, `status`) VALUES
(1, 1, 1, 'Black Sony Headphones', 'Black wireless over-ear headphones found on library bench.', '2026-08-01', 'Main Library 3rd Floor', 'SAO Office Cabinet B1', 'available'),
(2, 1, 3, 'Brown Leather Wallet', 'Brown leather wallet containing cash and receipts (no ID inside).', '2026-08-02', 'Gymnasium Bleachers', 'Campus Security Safe Box', 'available');
