-- School Management Database Schema for EDUsync
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

-- Seed data for `students`
INSERT INTO `students` (`student_code`, `adm_no`, `first_name`, `last_name`, `dob`, `gender`, `email`, `phone`, `address`, `guardian_name`, `guardian_phone`, `grade`, `status`) VALUES
('S001', 'ADM2024001', 'Amara', 'Osei', '2009-03-14', 'Female', 'amara.osei@edusync.edu', '0712345678', '14 Maple Ave, Accra', 'Kwame Osei', '0201234567', 'Grade 10', 'Active'),
('S002', 'ADM2024002', 'James', 'Mensah', '2008-07-22', 'Male', 'james.mensah@edusync.edu', '0723456789', '5 Palm St, Kumasi', 'Ama Mensah', '0202345678', 'Grade 11', 'Active'),
('S003', 'ADM2024003', 'Fatima', 'Al-Hassan', '2009-11-05', 'Female', 'fatima.alhassan@edusync.edu', '0734567890', '22 River Rd, Tamale', 'Ibrahim Al-Hassan', '0203456789', 'Grade 10', 'Active'),
('S004', 'ADM2024004', 'Kofi', 'Boateng', '2007-01-18', 'Male', 'kofi.boateng@edusync.edu', '0745678901', '3 Hill Close, Cape Coast', 'Esi Boateng', '0204567890', 'Grade 12', 'Inactive'),
('S005', 'ADM2024005', 'Abena', 'Kyei', '2010-05-30', 'Female', 'abena.kyei@edusync.edu', '0756789012', '8 Green Lane, Takoradi', 'Yaw Kyei', '0205678901', 'Grade 9', 'Active'),
('S006', 'ADM2024006', 'Daniel', 'Tetteh', '2008-09-12', 'Male', 'daniel.tetteh@edusync.edu', '0767890123', '17 Beach Rd, Tema', 'Akua Tetteh', '0206789012', 'Grade 11', 'Active'),
('S007', 'ADM2024007', 'Nana', 'Agyeman', '2009-02-28', 'Female', 'nana.agyeman@edusync.edu', '0778901234', '9 Unity Rd, Ho', 'Kojo Agyeman', '0207890123', 'Grade 10', 'Active'),
('S008', 'ADM2024008', 'Kwesi', 'Darko', '2007-12-03', 'Male', 'kwesi.darko@edusync.edu', '0789012345', '6 School Ave, Wa', 'Adwoa Darko', '0208901234', 'Grade 12', 'Active');

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

-- Seed data for `teachers`
INSERT INTO `teachers` (`teacher_code`, `first_name`, `last_name`, `nic`, `subject`, `qualification`, `email`, `phone`, `address`, `date_joined`, `salary`, `status`) VALUES
('T001', 'Ama', 'Asante', 'GHA-1234567', 'Mathematics', 'PhD Mathematics', 'ama.asante@edusync.edu', '0201234567', '12 Faculty Row, Accra', '2018-09-01', 4500.00, 'Active'),
('T002', 'Kwabena', 'Frimpong', 'GHA-2345678', 'English Language', 'M.Ed English', 'kwabena.frimpong@edusync.edu', '0202345678', '7 Staff Close, Kumasi', '2019-01-15', 3800.00, 'Active'),
('T003', 'Akosua', 'Nyarko', 'GHA-3456789', 'Biology', 'BSc Biology', 'akosua.nyarko@edusync.edu', '0203456789', '3 Oak Crescent, Accra', '2020-08-20', 3500.00, 'Active'),
('T004', 'Yaw', 'Owusu', 'GHA-4567890', 'Physics', 'MSc Physics', 'yaw.owusu@edusync.edu', '0204567890', '15 Science Ave, Tema', '2017-03-10', 4200.00, 'Active'),
('T005', 'Efua', 'Aidoo', 'GHA-5678901', 'History', 'MA History', 'efua.aidoo@edusync.edu', '0205678901', '2 Heritage Lane, Cape Coast', '2021-09-01', 3600.00, 'Inactive');

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
('MATH301', 'Advanced Mathematics', 'Calculus, algebra, and statistics for senior students', 1, 'Grade 12', 4, '40 weeks'),
('ENG201', 'English Literature', 'Comprehensive study of literary works and composition', 2, 'Grade 11', 3, '40 weeks'),
('BIO101', 'General Biology', 'Fundamentals of life sciences and ecosystems', 3, 'Grade 10', 3, '40 weeks'),
('PHY201', 'Applied Physics', 'Mechanics, thermodynamics, and electromagnetism', 4, 'Grade 11', 4, '40 weeks'),
('HIS101', 'World History', 'African and world history from ancient to modern', 5, 'Grade 9', 2, '40 weeks'),
('MATH101', 'Foundation Mathematics', 'Core mathematical concepts for junior students', 1, 'Grade 9', 3, '40 weeks');

-- --------------------------------------------------------
-- Table structure for `enrollments`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `enrollments`;
CREATE TABLE `enrollments` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `student_id` INT NOT NULL,
  `course_id` INT NOT NULL,
  `enrollment_date` DATE NOT NULL,
  `status` ENUM('Active', 'Dropped', 'Completed') DEFAULT 'Active',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`student_id`) REFERENCES `students`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`course_id`) REFERENCES `courses`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Seed data for `enrollments`
INSERT INTO `enrollments` (`student_id`, `course_id`, `enrollment_date`, `status`) VALUES
(1, 1, '2024-09-02', 'Active'),
(2, 2, '2024-09-02', 'Active'),
(3, 3, '2024-09-03', 'Active'),
(4, 4, '2024-09-03', 'Dropped'),
(5, 5, '2024-09-04', 'Active'),
(6, 4, '2024-09-04', 'Active'),
(7, 3, '2024-09-05', 'Active'),
(8, 1, '2024-09-05', 'Active');

-- --------------------------------------------------------
-- Table structure for `events`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `events`;
CREATE TABLE `events` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(100) NOT NULL,
  `event_date` DATE NOT NULL,
  `event_time` VARCHAR(30) NOT NULL,
  `location` VARCHAR(100) NOT NULL,
  `category` VARCHAR(50) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `events` (`title`, `event_date`, `event_time`, `location`, `category`) VALUES
('Parent-Teacher Association Meeting', '2026-08-05', '10:00 AM', 'Main Auditorium', 'Meeting'),
('Annual Science Fair 2026', '2026-08-12', '09:00 AM', 'Science Complex', 'Exhibition'),
('Mid-Term Examinations Begin', '2026-08-18', '08:30 AM', 'All Classrooms', 'Academics'),
('Inter-School Sports Gala', '2026-08-25', '08:00 AM', 'Sports Complex', 'Sports');

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
('Dr. Ama Asante', 'Graded MATH301 Mid-term Assignments', '10 mins ago', 'grade'),
('Kwame Admin', 'Enrolled 3 new students in Grade 10', '45 mins ago', 'student'),
('Mr. Yaw Owusu', 'Updated Physics Lab timetable', '2 hours ago', 'course'),
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
('STEM Leadership & Robotics Workshop', 'Dr. Ama Asante', '2026-08-10', 40, 'Scheduled'),
('Digital Literacy for Educators', 'Mr. Kwabena Frimpong', '2026-08-15', 25, 'Scheduled');

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
-- Plain text demo password: admin123 (stored hashed & supported plain for demo)
INSERT INTO `users` (`full_name`, `email`, `password`, `role`, `status`) VALUES
('System Administrator', 'admin@edusync.edu', '$2y$10$8K1p/a0dL1LXMIg.hJz2rO6S1vK8wH0V4D7b4vH9iO.lX8pU2j9mC', 'Administrator', 'Active'),
('University Admin', 'index@std.uwu.ac.lk', '$2y$10$8K1p/a0dL1LXMIg.hJz2rO6S1vK8wH0V4D7b4vH9iO.lX8pU2j9mC', 'Administrator', 'Active');

SET FOREIGN_KEY_CHECKS = 1;

