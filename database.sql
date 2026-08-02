-- G.C.E. Advanced Level (A/L) School Management Database Schema for EDUsync
-- Specialized Sri Lankan A/L Edition (Grade 12 & Grade 13)
-- Compatible with XAMPP MySQL / MariaDB

SET FOREIGN_KEY_CHECKS = 0;

CREATE DATABASE IF NOT EXISTS `school_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `school_db`;

-- --------------------------------------------------------
-- Table structure for `students` (G.C.E. A/L Edition)
-- --------------------------------------------------------
DROP TABLE IF EXISTS `students`;
CREATE TABLE `students` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `student_code` VARCHAR(20) NOT NULL UNIQUE,
  `adm_no` VARCHAR(30) NOT NULL UNIQUE,
  `first_name` VARCHAR(50) NOT NULL,
  `last_name` VARCHAR(50) NOT NULL,
  `dob` DATE NOT NULL,
  `gender` ENUM('Male', 'Female', 'Other') NOT NULL,
  `email` VARCHAR(100) NOT NULL UNIQUE,
  `phone` VARCHAR(20) NOT NULL,
  `address` TEXT NOT NULL,
  `guardian_name` VARCHAR(100) NOT NULL,
  `guardian_phone` VARCHAR(20) NOT NULL,
  `grade` VARCHAR(50) NOT NULL,
  `status` ENUM('Active', 'Inactive', 'Completed A/L') DEFAULT 'Active',
  `photo` VARCHAR(255) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Table structure for `teachers` (G.C.E. A/L Academic Staff)
-- --------------------------------------------------------
DROP TABLE IF EXISTS `teachers`;
CREATE TABLE `teachers` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `teacher_code` VARCHAR(20) NOT NULL UNIQUE,
  `first_name` VARCHAR(50) NOT NULL,
  `last_name` VARCHAR(50) NOT NULL,
  `nic` VARCHAR(30) NOT NULL UNIQUE,
  `subject` VARCHAR(50) NOT NULL,
  `qualification` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) NOT NULL UNIQUE,
  `phone` VARCHAR(20) NOT NULL,
  `address` TEXT NOT NULL,
  `date_joined` DATE NOT NULL,
  `salary` DECIMAL(10,2) NOT NULL,
  `status` ENUM('Active', 'Inactive') DEFAULT 'Active',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Table structure for `courses` (G.C.E. A/L Subjects Catalog)
-- --------------------------------------------------------
DROP TABLE IF EXISTS `courses`;
CREATE TABLE `courses` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `course_code` VARCHAR(20) NOT NULL UNIQUE,
  `course_name` VARCHAR(100) NOT NULL,
  `description` TEXT DEFAULT NULL,
  `teacher_id` INT DEFAULT NULL,
  `grade` VARCHAR(50) NOT NULL,
  `credits` INT NOT NULL DEFAULT 4,
  `duration` VARCHAR(30) NOT NULL DEFAULT '2 Years',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`teacher_id`) REFERENCES `teachers`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Pre-loaded G.C.E. A/L Master Subject Catalog (Physical Science, Bio Science, Commerce, Arts, Technology, Common)
INSERT INTO `courses` (`course_code`, `course_name`, `description`, `teacher_id`, `grade`, `credits`, `duration`) VALUES
-- Physical Science Stream (Maths)
('AL-COMB12', 'Combined Mathematics (Grade 12)', 'Pure Mathematics & Applied Mathematics for Physical Science Stream', NULL, 'Grade 12', 4, '2 Years'),
('AL-COMB13', 'Combined Mathematics (Grade 13)', 'Advanced Calculus, Mechanics, & Statics for A/L Physical Science', NULL, 'Grade 13', 4, '2 Years'),
('AL-PHYS12', 'Physics (Grade 12)', 'Mechanics, Properties of Matter, Oscillation & Thermal Physics', NULL, 'Grade 12', 4, '2 Years'),
('AL-PHYS13', 'Physics (Grade 13)', 'Electronics, Fields, Radiation, Magnetic & Modern Physics', NULL, 'Grade 13', 4, '2 Years'),

-- Biological Science Stream
('AL-CHEM12', 'Chemistry (Grade 12)', 'Atomic Structure, Chemical Bonding & General Chemistry', NULL, 'Grade 12', 4, '2 Years'),
('AL-CHEM13', 'Chemistry (Grade 13)', 'Organic Chemistry, Physical Kinetics, & Inorganic Chemistry', NULL, 'Grade 13', 4, '2 Years'),
('AL-BIOL12', 'Biology (Grade 12)', 'Cell Biology, Molecular Genetics, & Plant Diversity', NULL, 'Grade 12', 4, '2 Years'),
('AL-BIOL13', 'Biology (Grade 13)', 'Animal Physiology, Human Health, Ecology & Biotechnology', NULL, 'Grade 13', 4, '2 Years'),
('AL-AGRI12', 'Agricultural Science (Grade 12/13)', 'Soil Science, Crop Production, Farm Machinery & Agri-Business', NULL, 'Grade 12', 4, '2 Years'),

-- Commerce Stream
('AL-ACCT12', 'Accounting (Grade 12)', 'Financial Accounting Principles & Partnership Accounting', NULL, 'Grade 12', 4, '2 Years'),
('AL-ACCT13', 'Accounting (Grade 13)', 'Company Financial Statements, Auditing & Cost Accounting', NULL, 'Grade 13', 4, '2 Years'),
('AL-BS12', 'Business Studies (Grade 12)', 'Business Environment, Management Functions & Entrepreneurship', NULL, 'Grade 12', 4, '2 Years'),
('AL-BS13', 'Business Studies (Grade 13)', 'Marketing Management, Human Resources & Operations', NULL, 'Grade 13', 4, '2 Years'),
('AL-ECON12', 'Economics (Grade 12)', 'Microeconomics, Price Theory & Production Economics', NULL, 'Grade 12', 4, '2 Years'),
('AL-ECON13', 'Economics (Grade 13)', 'Macroeconomics, Public Finance & Sri Lankan Economic Growth', NULL, 'Grade 13', 4, '2 Years'),

-- Technology Stream
('AL-ETEC12', 'Engineering Technology (Grade 12/13)', 'Civil, Mechanical & Electrical Engineering Fundamentals', NULL, 'Grade 12', 4, '2 Years'),
('AL-BTEC12', 'Biosystems Technology (Grade 12/13)', 'Food Processing, Post-Harvest Tech & Bio-Resource Management', NULL, 'Grade 12', 4, '2 Years'),
('AL-STEC12', 'Science for Technology (Grade 12/13)', 'Applied Mathematics, Applied Chemistry & Applied Physics', NULL, 'Grade 12', 4, '2 Years'),

-- Arts Stream
('AL-SINH12', 'Sinhala Language & Literature (Grade 12/13)', 'Classical Sinhala Literature, Grammar, Poetry & Modern Drama', NULL, 'Grade 12', 4, '2 Years'),
('AL-POLS12', 'Political Science (Grade 12/13)', 'Political Concepts, Constitutional Law & Sri Lankan Governance', NULL, 'Grade 12', 4, '2 Years'),
('AL-LOGC12', 'Logic & Scientific Method (Grade 12/13)', 'Deductive Logic, Symbolic Logic & Scientific Methodology', NULL, 'Grade 12', 4, '2 Years'),
('AL-GEOG12', 'Geography (Grade 12/13)', 'Physical Geography, Human Geography & Cartography/GIS', NULL, 'Grade 12', 4, '2 Years'),

-- Common Mandatory Subjects
('AL-ICT12', 'Information & Communication Tech (ICT)', 'Programming (Python), Database Systems, Web Tech & Networking', NULL, 'Grade 12', 4, '2 Years'),
('AL-GENG12', 'General English (A/L)', 'Comprehension, Grammar, Report Writing & Academic Speaking', NULL, 'Grade 12', 2, '2 Years'),
('AL-CGT12', 'Common General Test (A/L)', 'Analytical Ability, General Knowledge & Current Affairs', NULL, 'Grade 12', 2, '2 Years');

-- --------------------------------------------------------
-- Table structure for `enrollments` (G.C.E. A/L Class Allocations)
-- --------------------------------------------------------
DROP TABLE IF EXISTS `enrollments`;
CREATE TABLE `enrollments` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `student_id` INT NOT NULL,
  `course_id` INT NOT NULL,
  `enrollment_date` DATE NOT NULL,
  `status` ENUM('Enrolled', 'Completed', 'Dropped') DEFAULT 'Enrolled',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`student_id`) REFERENCES `students`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`course_id`) REFERENCES `courses`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Table structure for `marks` (G.C.E. A/L Term Evaluation Marks)
-- --------------------------------------------------------
DROP TABLE IF EXISTS `marks`;
CREATE TABLE `marks` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `student_id` INT NOT NULL,
  `course_id` INT NOT NULL,
  `term` ENUM('Term 1', 'Term 2', 'Term 3') NOT NULL DEFAULT 'Term 1',
  `marks_obtained` DECIMAL(5,2) NOT NULL,
  `grade` VARCHAR(5) NOT NULL,
  `remarks` VARCHAR(255) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`student_id`) REFERENCES `students`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`course_id`) REFERENCES `courses`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Table structure for `notifications`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `notifications`;
CREATE TABLE `notifications` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(100) NOT NULL,
  `message` TEXT NOT NULL,
  `type` VARCHAR(30) NOT NULL DEFAULT 'student',
  `is_read` TINYINT(1) DEFAULT 0,
  `ref_student_id` INT DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Table structure for `activities`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `activities`;
CREATE TABLE `activities` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_name` VARCHAR(100) NOT NULL,
  `action` VARCHAR(255) NOT NULL,
  `time_ago` VARCHAR(50) NOT NULL,
  `icon_type` VARCHAR(30) DEFAULT 'user',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Table structure for `users` (System Administrators / Staff)
-- --------------------------------------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `full_name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `role` ENUM('Administrator', 'Teacher', 'Student') DEFAULT 'Administrator',
  `status` ENUM('Active', 'Inactive') DEFAULT 'Active',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Default System Admin Accounts (Password: admin123)
INSERT INTO `users` (`full_name`, `email`, `password`, `role`, `status`) VALUES
('System Administrator', 'admin@edusync.edu', '$2y$10$8K1p/a0dL1LXMIg.hJz2rO6S1vK8wH0V4D7b4vH9iO.lX8pU2j9mC', 'Administrator', 'Active'),
('University Admin', 'index@std.uwu.ac.lk', '$2y$10$8K1p/a0dL1LXMIg.hJz2rO6S1vK8wH0V4D7b4vH9iO.lX8pU2j9mC', 'Administrator', 'Active');

-- --------------------------------------------------------
-- Table structure for `system_settings` (Academic Year & Global Settings)
-- --------------------------------------------------------
DROP TABLE IF EXISTS `system_settings`;
CREATE TABLE `system_settings` (
  `setting_key` VARCHAR(50) PRIMARY KEY,
  `setting_value` VARCHAR(255) NOT NULL,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `system_settings` (`setting_key`, `setting_value`) VALUES
('academic_year', '2024/2025');

-- --------------------------------------------------------
-- Table structure for `user_preferences` (Theme & UI Preferences)
-- --------------------------------------------------------
DROP TABLE IF EXISTS `user_preferences`;
CREATE TABLE `user_preferences` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `email` VARCHAR(100) NOT NULL UNIQUE,
  `theme` VARCHAR(20) DEFAULT 'light',
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

SET FOREIGN_KEY_CHECKS = 1;
