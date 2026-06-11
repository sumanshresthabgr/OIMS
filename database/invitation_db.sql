CREATE DATABASE IF NOT EXISTS `invitation_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `invitation_db`;

-- Table 1: admins
CREATE TABLE IF NOT EXISTS `admins` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(50) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL
) ENGINE=InnoDB;

-- Table 2: invitations
CREATE TABLE IF NOT EXISTS `invitations` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `admin_id` INT NOT NULL,
  `title` VARCHAR(150) NOT NULL,
  `date_time` DATETIME NOT NULL,
  `venue` TEXT NOT NULL,
  `description` TEXT DEFAULT NULL,
  `theme` VARCHAR(30) NOT NULL,
  `share_token` VARCHAR(8) NOT NULL UNIQUE,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`admin_id`) REFERENCES `admins`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Table 3: rsvps
CREATE TABLE IF NOT EXISTS `rsvps` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `invitation_id` INT NOT NULL,
  `guest_name` VARCHAR(100) NOT NULL,
  `status` ENUM('Attending', 'Declined') NOT NULL,
  `submitted_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`invitation_id`) REFERENCES `invitations`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Insert default admin account (Username: admin | Password: password123)
-- Password hashed using PHP's password_hash() equivalent
INSERT INTO `admins` (`id`, `username`, `password`) 
VALUES (1, 'admin', '$2y$10$w6e7pZ94G0b61K4lO8EeeunZ6D1q5e0f7n8p9o0m1l2k3j4i5h6g7')
ON DUPLICATE KEY UPDATE `id`=`id`;