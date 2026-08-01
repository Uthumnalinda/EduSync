-- School Management Database Schema for EDUsync (Sri Lanka School Edition)
-- Compatible with XAMPP MySQL / MariaDB

SET FOREIGN_KEY_CHECKS = 0;

CREATE DATABASE IF NOT EXISTS `school_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `school_db`;

-- --------------------------------------------------------
-- Table structure for `students`
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
  `grade` VARCHAR(20) NOT NULL,
  `status` ENUM('Active', 'Inactive') DEFAULT 'Active',
  `photo` VARCHAR(255) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Seed data for `students` (Sri Lankan School Format)
INSERT INTO `students` (`student_code`, `adm_no`, `first_name`, `last_name`, `dob`, `gender`, `email`, `phone`, `address`, `guardian_name`, `guardian_phone`, `grade`, `status`) VALUES
('S001', 'ADM2024001', 'Kasun', 'Fernando', '2009-03-14', 'Male', 'kasun.fernando@edusync.lk', '0771122334', 'No. 10, Kandy Rd, Peradeniya', 'Nimal Fernando', '0719988776', 'Grade 10', 'Active'),
('S002', 'ADM2024002', 'Dilhani', 'Perera', '2008-07-22', 'Female', 'dilhani.perera@edusync.lk', '0712233445', '25/1, Main St, Galle', 'Saman Perera', '0778877665', 'Grade 11', 'Active'),
('S003', 'ADM2024003', 'Sahan', 'Silva', '2009-11-05', 'Male', 'sahan.silva@edusync.lk', '0753344556', 'No. 8, Temple Rd, Kelaniya', 'Kanthi Silva', '0757766554', 'Grade 10', 'Active'),
('S004', 'ADM2024004', 'Piumi', 'Bandara', '2007-01-18', 'Female', 'piumi.bandara@edusync.lk', '0784455667', '14, Lake Rd, Kurunegala', 'Dhammika Bandara', '0786655443', 'Grade 12', 'Inactive'),
('S005', 'ADM2024005', 'Ruwan', 'Jayawardena', '2010-05-30', 'Male', 'ruwan.jayawardena@edusync.lk', '0705566778', 'No. 50, Station Rd, Badulla', 'Mahinda Jayawardena', '0705544332', 'Grade 9', 'Active'),
('S006', 'ADM2024006', 'Tharushi', 'Wickramasinghe', '2008-09-12', 'Female', 'tharushi.w@edusync.lk', '0776677889', '88, Highlevel Rd, Maharagama', 'Sunil Wickramasinghe', '0714455667', 'Grade 11', 'Active');

-- --------------------------------------------------------
-- Table structure for `teachers`
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

-- Seed data for `teachers` (Sri Lankan School Format)
INSERT INTO `teachers` (`teacher_code`, `first_name`, `last_name`, `nic`, `subject`, `qualification`, `email`, `phone`, `address`, `date_joined`, `salary`, `status`) VALUES
('T001', 'Kamal', 'Perera', '198514209876', 'Mathematics', 'B.Sc. (Hons) Mathematics, Dip.Ed', 'kamal.perera@edusync.lk', '0771234567', 'No. 45, Kandy Road, Kurunegala', '2018-09-01', 75000.00, 'Active'),
('T002', 'Nimali', 'Fernando', '199068403210', 'English Language', 'B.A. (Hons) English, M.Ed', 'nimali.fernando@edusync.lk', '0712345678', '12/A, Station Road, Badulla', '2019-01-15', 68000.00, 'Active'),
('T003', 'Sunil', 'Silva', '198223004567', 'Biology', 'B.Sc. Bio Science', 'sunil.silva@edusync.lk', '0753456789', 'No. 88, Galle Road, Colombo 03', '2020-08-20', 65000.00, 'Active'),
('T004', 'Wathsala', 'Bandara', '198859102345', 'Physics', 'B.Sc. Physical Science', 'wathsala.bandara@edusync.lk', '0784567890', '25, Temple Road, Passara', '2017-03-10', 72000.00, 'Active'),
('T005', 'Dinesh', 'Jayasinghe', '198912405678', 'History', 'B.A. History', 'dinesh.jayasinghe@edusync.lk', '0705678901', 'No. 14, Main Street, Bandarawela', '2021-09-01', 62000.00, 'Inactive');

-- --------------------------------------------------------
-- Table structure for `courses`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `courses`;
CREATE TABLE `courses` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `course_code` VARCHAR(20) NOT NULL UNIQUE,
  `course_name` VARCHAR(100) NOT NULL,
  `description` TEXT DEFAULT NULL,
  `teacher_id` INT DEFAULT NULL,
  `grade` VARCHAR(20) NOT NULL,
  `credits` INT NOT NULL DEFAULT 3,
  `duration` VARCHAR(30) NOT NULL DEFAULT '40 weeks',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`teacher_id`) REFERENCES `teachers`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Seed data for `courses`
INSERT INTO `courses` (`course_code`, `course_name`, `description`, `teacher_id`, `grade`, `credits`, `duration`) VALUES
('MATH101', 'Mathematics Grade 10', 'Algebra, Geometry, and Trigonometry', 1, 'Grade 10', 4, '40 weeks'),
('ENG201', 'English Language', 'Grammar, Literature, and Essay Writing', 2, 'Grade 11', 3, '40 weeks'),
('BIO301', 'Biology', 'Cell Biology, Genetics, and Human Physiology', 3, 'Grade 10', 4, '40 weeks'),
('PHY401', 'Physics', 'Mechanics, Electricity, and Thermodynamics', 4, 'Grade 12', 4, '40 weeks'),
('HIS101', 'History', 'Sri Lankan and World History Studies', 5, 'Grade 9', 3, '40 weeks');

-- --------------------------------------------------------
-- Table structure for `enrollments`
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

INSERT INTO `enrollments` (`student_id`, `course_id`, `enrollment_date`, `status`) VALUES
(1, 1, '2024-01-10', 'Enrolled'),
(1, 3, '2024-01-10', 'Enrolled'),
(2, 2, '2024-01-12', 'Enrolled'),
(3, 1, '2024-01-15', 'Enrolled'),
(4, 4, '2024-01-08', 'Completed');

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

-- Seed Data for `notifications`
INSERT INTO `notifications` (`title`, `message`, `type`, `is_read`, `ref_student_id`) VALUES
('New Student Registered', 'Kasun Fernando was enrolled in Grade 10', 'student', 0, 1),
('New Student Registered', 'Dilhani Perera was enrolled in Grade 11', 'student', 0, 2),
('New Student Registered', 'Sahan Silva was enrolled in Grade 10', 'student', 0, 3);

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

INSERT INTO `activities` (`user_name`, `action`, `time_ago`, `icon_type`) VALUES
('Mr. Kamal Perera', 'Graded MATH101 Mid-term Assignments', '10 mins ago', 'grade'),
('Admin Staff', 'Enrolled 3 new students in Grade 10', '45 mins ago', 'student'),
('Mr. Sunil Silva', 'Updated Biology Lab timetable', '2 hours ago', 'course'),
('System Administrator', 'Completed weekly database backup', '5 hours ago', 'system');

-- --------------------------------------------------------
-- Table structure for `workshops`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `workshops`;
CREATE TABLE `workshops` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(150) NOT NULL,
  `instructor` VARCHAR(100) NOT NULL,
  `scheduled_date` DATE NOT NULL,
  `capacity` INT NOT NULL DEFAULT 30,
  `status` ENUM('Scheduled', 'Ongoing', 'Completed', 'Cancelled') DEFAULT 'Scheduled',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `workshops` (`title`, `instructor`, `scheduled_date`, `capacity`, `status`) VALUES
('STEM Leadership & Science Workshop', 'Mr. Kamal Perera', '2026-08-10', 40, 'Scheduled'),
('Digital Literacy for Educators', 'Mrs. Nimali Fernando', '2026-08-15', 25, 'Scheduled');

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

-- Seed Admin User Credentials
INSERT INTO `users` (`full_name`, `email`, `password`, `role`, `status`) VALUES
('System Administrator', 'admin@edusync.edu', '$2y$10$8K1p/a0dL1LXMIg.hJz2rO6S1vK8wH0V4D7b4vH9iO.lX8pU2j9mC', 'Administrator', 'Active'),
('University Admin', 'index@std.uwu.ac.lk', '$2y$10$8K1p/a0dL1LXMIg.hJz2rO6S1vK8wH0V4D7b4vH9iO.lX8pU2j9mC', 'Administrator', 'Active');

SET FOREIGN_KEY_CHECKS = 1;
