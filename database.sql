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

-- Seed data for `students` (G.C.E. A/L Students: Grade 12 & Grade 13)
INSERT INTO `students` (`student_code`, `adm_no`, `first_name`, `last_name`, `dob`, `gender`, `email`, `phone`, `address`, `guardian_name`, `guardian_phone`, `grade`, `status`) VALUES
('AL2024-001', 'ADM-AL-001', 'Kasun', 'Fernando', '2007-03-14', 'Male', 'kasun.fernando@edusync.lk', '0771122334', 'No. 10, Kandy Rd, Peradeniya', 'Nimal Fernando', '0719988776', 'Grade 12 - Physical Science', 'Active'),
('AL2024-002', 'ADM-AL-002', 'Dilhani', 'Perera', '2006-07-22', 'Female', 'dilhani.perera@edusync.lk', '0712233445', '25/1, Main St, Galle', 'Saman Perera', '0778877665', 'Grade 13 - Bio Science', 'Active'),
('AL2024-003', 'ADM-AL-003', 'Sahan', 'Silva', '2007-11-05', 'Male', 'sahan.silva@edusync.lk', '0753344556', 'No. 8, Temple Rd, Kelaniya', 'Kanthi Silva', '0757766554', 'Grade 12 - Commerce', 'Active'),
('AL2024-004', 'ADM-AL-004', 'Piumi', 'Bandara', '2006-01-18', 'Female', 'piumi.bandara@edusync.lk', '0784455667', '14, Lake Rd, Kurunegala', 'Dhammika Bandara', '0786655443', 'Grade 13 - Physical Science', 'Active'),
('AL2024-005', 'ADM-AL-005', 'Ruwan', 'Jayawardena', '2007-05-30', 'Male', 'ruwan.jayawardena@edusync.lk', '0705566778', 'No. 50, Station Rd, Badulla', 'Mahinda Jayawardena', '0705544332', 'Grade 12 - Technology', 'Active'),
('AL2024-006', 'ADM-AL-006', 'Tharushi', 'Wickramasinghe', '2006-09-12', 'Female', 'tharushi.w@edusync.lk', '0776677889', '88, Highlevel Rd, Maharagama', 'Sunil Wickramasinghe', '0714455667', 'Grade 13 - Arts', 'Active'),
('AL2024-007', 'ADM-AL-007', 'Nuwan', 'Kulatunga', '2007-08-19', 'Male', 'nuwan.k@edusync.lk', '0779988776', 'No. 12, Hospital Rd, Kandy', 'Sarath Kulatunga', '0712233445', 'Grade 12 - Physical Science', 'Active');

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

-- Seed data for `teachers` (G.C.E. A/L Master Teachers)
INSERT INTO `teachers` (`teacher_code`, `first_name`, `last_name`, `nic`, `subject`, `qualification`, `email`, `phone`, `address`, `date_joined`, `salary`, `status`) VALUES
('ALT-001', 'Kamal', 'Perera', '198514209876', 'Combined Mathematics', 'B.Sc. (Hons) Mathematics, M.Sc.', 'kamal.perera@edusync.lk', '0771234567', 'No. 45, Kandy Road, Kurunegala', '2018-09-01', 85000.00, 'Active'),
('ALT-002', 'Nimali', 'Fernando', '199068403210', 'Chemistry', 'B.Sc. (Hons) Chemistry, Ph.D.', 'nimali.fernando@edusync.lk', '0712345678', '12/A, Station Road, Badulla', '2019-01-15', 82000.00, 'Active'),
('ALT-003', 'Sunil', 'Silva', '198223004567', 'Biology', 'B.Sc. Bio Science, M.Ed', 'sunil.silva@edusync.lk', '0753456789', 'No. 88, Galle Road, Colombo 03', '2020-08-20', 78000.00, 'Active'),
('ALT-004', 'Wathsala', 'Bandara', '198859102345', 'Physics', 'B.Sc. Physical Science, M.Sc.', 'wathsala.bandara@edusync.lk', '0784567890', '25, Temple Road, Passara', '2017-03-10', 84000.00, 'Active'),
('ALT-005', 'Dinesh', 'Jayasinghe', '198912405678', 'Accounting', 'B.Com (Hons), ACA', 'dinesh.jayasinghe@edusync.lk', '0705678901', 'No. 14, Main Street, Bandarawela', '2021-09-01', 76000.00, 'Active'),
('ALT-006', 'Kithsiri', 'Ratnayake', '198425109823', 'Economics', 'B.A. (Hons) Economics, M.A.', 'kithsiri.ratnayake@edusync.lk', '0713456789', 'No. 32, Peradeniya Rd, Kandy', '2016-05-12', 80000.00, 'Active'),
('ALT-007', 'Sandya', 'Wickramasinghe', '198765403219', 'Business Studies', 'B.B.A. (Hons), MBA', 'sandya.w@edusync.lk', '0774567890', 'No. 18, Lake Round, Kurunegala', '2018-11-01', 77000.00, 'Active'),
('ALT-008', 'Nuwan', 'Senanayake', '198619208734', 'Engineering Technology', 'B.Sc. Eng (Hons), AMIE(SL)', 'nuwan.senanayake@edusync.lk', '0765678901', 'No. 90, Negombo Rd, Colombo', '2019-06-15', 86000.00, 'Active'),
('ALT-009', 'Malini', 'Cooray', '199158309124', 'Biosystems Technology', 'B.Sc. Agriculture (Hons), M.Sc.', 'malini.cooray@edusync.lk', '0726789012', 'No. 5, University Rd, Peradeniya', '2020-02-10', 75000.00, 'Active'),
('ALT-010', 'Somapala', 'Hewage', '198011209345', 'Sinhala Language & Literature', 'B.A. (Hons) Sinhala, M.Phil', 'somapala.hewage@edusync.lk', '0717890123', 'No. 67, Temple St, Kelaniya', '2015-08-01', 81000.00, 'Active'),
('ALT-011', 'Janaka', 'Abeywardena', '198934509182', 'Information & Communication Tech (ICT)', 'B.Sc. Computer Science (Hons)', 'janaka.a@edusync.lk', '0778901234', 'No. 120, Highlevel Rd, Nugegoda', '2019-10-20', 83000.00, 'Active'),
('ALT-012', 'Champa', 'Ranasinghe', '198878901234', 'General English', 'B.A. (Hons) English, Dip.TESL', 'champa.r@edusync.lk', '0789012345', 'No. 44, Havelock Rd, Colombo 05', '2017-07-01', 79000.00, 'Active');

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

-- Seed data for All G.C.E. A/L Subjects (Physical Science, Bio Science, Commerce, Arts, Technology, Common)
INSERT INTO `courses` (`course_code`, `course_name`, `description`, `teacher_id`, `grade`, `credits`, `duration`) VALUES
-- Physical Science Stream (Maths)
('AL-COMB12', 'Combined Mathematics (Grade 12)', 'Pure Mathematics & Applied Mathematics for Physical Science Stream', 1, 'Grade 12', 4, '2 Years'),
('AL-COMB13', 'Combined Mathematics (Grade 13)', 'Advanced Calculus, Mechanics, & Statics for A/L Physical Science', 1, 'Grade 13', 4, '2 Years'),
('AL-PHYS12', 'Physics (Grade 12)', 'Mechanics, Properties of Matter, Oscillation & Thermal Physics', 4, 'Grade 12', 4, '2 Years'),
('AL-PHYS13', 'Physics (Grade 13)', 'Electronics, Fields, Radiation, Magnetic & Modern Physics', 4, 'Grade 13', 4, '2 Years'),

-- Biological Science Stream
('AL-CHEM12', 'Chemistry (Grade 12)', 'Atomic Structure, Chemical Bonding & General Chemistry', 2, 'Grade 12', 4, '2 Years'),
('AL-CHEM13', 'Chemistry (Grade 13)', 'Organic Chemistry, Physical Kinetics, & Inorganic Chemistry', 2, 'Grade 13', 4, '2 Years'),
('AL-BIOL12', 'Biology (Grade 12)', 'Cell Biology, Molecular Genetics, & Plant Diversity', 3, 'Grade 12', 4, '2 Years'),
('AL-BIOL13', 'Biology (Grade 13)', 'Animal Physiology, Human Health, Ecology & Biotechnology', 3, 'Grade 13', 4, '2 Years'),
('AL-AGRI12', 'Agricultural Science (Grade 12/13)', 'Soil Science, Crop Production, Farm Machinery & Agri-Business', 9, 'Grade 12', 4, '2 Years'),

-- Commerce Stream
('AL-ACCT12', 'Accounting (Grade 12)', 'Financial Accounting Principles & Partnership Accounting', 5, 'Grade 12', 4, '2 Years'),
('AL-ACCT13', 'Accounting (Grade 13)', 'Company Financial Statements, Auditing & Cost Accounting', 5, 'Grade 13', 4, '2 Years'),
('AL-BS12', 'Business Studies (Grade 12)', 'Business Environment, Management Functions & Entrepreneurship', 7, 'Grade 12', 4, '2 Years'),
('AL-BS13', 'Business Studies (Grade 13)', 'Marketing Management, Human Resources & Operations', 7, 'Grade 13', 4, '2 Years'),
('AL-ECON12', 'Economics (Grade 12)', 'Microeconomics, Price Theory & Production Economics', 6, 'Grade 12', 4, '2 Years'),
('AL-ECON13', 'Economics (Grade 13)', 'Macroeconomics, Public Finance & Sri Lankan Economic Growth', 6, 'Grade 13', 4, '2 Years'),

-- Technology Stream
('AL-ETEC12', 'Engineering Technology (Grade 12/13)', 'Civil, Mechanical & Electrical Engineering Fundamentals', 8, 'Grade 12', 4, '2 Years'),
('AL-BTEC12', 'Biosystems Technology (Grade 12/13)', 'Food Processing, Post-Harvest Tech & Bio-Resource Management', 9, 'Grade 12', 4, '2 Years'),
('AL-STEC12', 'Science for Technology (Grade 12/13)', 'Applied Mathematics, Applied Chemistry & Applied Physics', 8, 'Grade 12', 4, '2 Years'),

-- Arts Stream
('AL-SINH12', 'Sinhala Language & Literature (Grade 12/13)', 'Classical Sinhala Literature, Grammar, Poetry & Modern Drama', 10, 'Grade 12', 4, '2 Years'),
('AL-POLS12', 'Political Science (Grade 12/13)', 'Political Concepts, Constitutional Law & Sri Lankan Governance', 6, 'Grade 12', 4, '2 Years'),
('AL-LOGC12', 'Logic & Scientific Method (Grade 12/13)', 'Deductive Logic, Symbolic Logic & Scientific Methodology', 1, 'Grade 12', 4, '2 Years'),
('AL-GEOG12', 'Geography (Grade 12/13)', 'Physical Geography, Human Geography & Cartography/GIS', 4, 'Grade 12', 4, '2 Years'),

-- Common Mandatory Subjects
('AL-ICT12', 'Information & Communication Tech (ICT)', 'Programming (Python), Database Systems, Web Tech & Networking', 11, 'Grade 12', 4, '2 Years'),
('AL-GENG12', 'General English (A/L)', 'Comprehension, Grammar, Report Writing & Academic Speaking', 12, 'Grade 12', 2, '2 Years'),
('AL-CGT12', 'Common General Test (A/L)', 'Analytical Ability, General Knowledge & Current Affairs', 6, 'Grade 12', 2, '2 Years');

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

INSERT INTO `enrollments` (`student_id`, `course_id`, `enrollment_date`, `status`) VALUES
(1, 1, '2024-01-10', 'Enrolled'),
(1, 2, '2024-01-10', 'Enrolled'),
(2, 4, '2024-01-12', 'Enrolled'),
(3, 5, '2024-01-15', 'Enrolled'),
(4, 2, '2024-01-08', 'Enrolled');

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

INSERT INTO `marks` (`student_id`, `course_id`, `term`, `marks_obtained`, `grade`, `remarks`) VALUES
(1, 1, 'Term 1', 85.00, 'A', 'Distinction in Combined Mathematics'),
(1, 2, 'Term 1', 78.00, 'A', 'Excellent performance in Physics'),
(2, 4, 'Term 1', 88.00, 'A', 'Top scorer in Biology'),
(3, 5, 'Term 1', 68.00, 'B', 'Good credit pass in Accounting'),
(4, 2, 'Term 1', 92.00, 'A', 'Outstanding performance in Physics');

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

INSERT INTO `notifications` (`title`, `message`, `type`, `is_read`, `ref_student_id`) VALUES
('New A/L Student Enrolled', 'Kasun Fernando was enrolled in Grade 12 Physical Science', 'student', 0, 1),
('New A/L Student Enrolled', 'Dilhani Perera was enrolled in Grade 13 Bio Science', 'student', 0, 2),
('New A/L Student Enrolled', 'Sahan Silva was enrolled in Grade 12 Commerce', 'student', 0, 3);

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
('Mr. Kamal Perera', 'Graded Combined Maths Term 1 Exam Papers', '10 mins ago', 'grade'),
('A/L Admin Office', 'Enrolled 5 new Grade 12 Physical Science students', '45 mins ago', 'student'),
('Mrs. Nimali Fernando', 'Updated A/L Chemistry Laboratory Timetable', '2 hours ago', 'course'),
('System Administrator', 'Completed weekly A/L database backup', '5 hours ago', 'system');

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

SET FOREIGN_KEY_CHECKS = 1;
