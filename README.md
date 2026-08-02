# EduSync - School Management System

A web-based School Management System built for Sri Lankan G.C.E. Advanced Level (A/L) schools. It helps manage Grade 12 and Grade 13 student records, master teacher allocations, subject catalogs, term test evaluation marks, and printable progress report cards.

Built with pure **PHP (OOP)**, **MySQL (PDO)**, **Vanilla HTML5/CSS3**, and **JavaScript** for local deployment on XAMPP.

---

## Key Features

- **Dashboard**: Live metrics for active students, master teachers, subject catalog, average term marks, and interactive Chart.js analytics.
- **Student Management**: Full CRUD for Grade 12 & 13 students across A/L Streams (Physical Science, Bio Science, Commerce, Arts, Technology).
- **Teacher Directory**: Manage teacher qualifications, assigned subjects, contact details, and status.
- **Subject Catalog**: Manage A/L subjects, streams, credit hours, and assigned teachers.
- **Class Allocations**: Assign students to class sections and subject streams with real-time dropdown filtering.
- **Term Test Marks**: Record 1st, 2nd, and 3rd term test marks with automatic Sri Lankan grade calculation (A, B, C, S, F).
- **Academic Progress Reports**: Generate and print individual student report cards cleanly formatted for A4 printing without unwanted page headers/footers.
- **Global Search & Notifications**: System-wide navbar search across students, teachers, and subjects with real-time notification alerts.

---

## Tech Stack

- **Backend**: PHP 8.x (OOP with PDO)
- **Database**: MySQL / MariaDB
- **Frontend**: HTML5, Vanilla CSS3 (Custom responsive layout system), Vanilla JavaScript (ES6)
- **Charts**: Chart.js
- **Environment**: XAMPP (Apache + MySQL)

---

## Setup & Local Installation Guide

### Prerequisites
- Install [XAMPP](https://www.apachefriends.org/) (PHP 8.0+ and MySQL).

### Step 1: Clone or Copy the Repository
Place the project folder inside your XAMPP `htdocs` directory:
```text
C:\xampp\htdocs\school_dashboard
```

### Step 2: Start XAMPP Services
Open the XAMPP Control Panel and start **Apache** and **MySQL**.

### Step 3: Create & Import the Database
1. Open your browser and go to `http://localhost/phpmyadmin`
2. Create a new database named **`school_db`** (utf8mb4_general_ci).
3. Select the `school_db` database, click **Import**, choose the file `database.sql` from the root folder, and click **Import**.

*Alternatively via MySQL Command Line:*
```bash
mysql -u root -e "CREATE DATABASE IF NOT EXISTS school_db;"
mysql -u root school_db < database.sql
```

### Step 4: Database Configuration Check
Verify the database connection settings in `config/Database.php`:
```php
class Database {
    private $host = "localhost";
    private $db_name = "school_db";
    private $username = "root";
    private $password = "";
}
```

### Step 5: Launch the Application
Open your browser and navigate to:
```text
http://localhost/school_dashboard/login.php
```

#### Default Admin Credentials:
- **Email**: `admin@edusync.edu`
- **Password**: `admin123`

---

## Project Structure

```text
school_dashboard/
├── config/
│   └── Database.php         # PDO Database connection class
├── classes/
│   ├── Student.php          # Student management logic
│   ├── Teacher.php          # Teacher directory logic
│   ├── Subject.php          # Subject catalog logic
│   ├── Enrollment.php       # Class allocation logic
│   ├── Mark.php             # Term test evaluation logic
│   ├── Report.php           # Progress report card logic
│   └── Dashboard.php        # Dashboard analytics & charts logic
├── includes/
│   ├── header.php           # Shared document header
│   ├── sidebar.php          # Sidebar navigation
│   ├── navbar.php           # Header search & profile menu
│   └── footer.php           # Shared document footer
├── assets/
│   ├── css/
│   │   └── style.css        # Main stylesheet
│   └── js/
│       └── dashboard.js     # Client controller & charts
├── index.php                # Dashboard overview page
├── students.php             # Student management page
├── teachers.php             # Teacher management page
├── subject.php              # Subject management page
├── enrollments.php          # Class allocation page
├── marks.php                # Term marks management page
├── reports.php              # Academic report cards & analytics page
├── search.php               # Universal global search page
├── settings.php             # System preferences & rollover page
├── login.php                # Login authentication page
├── logout.php               # Session logout page
└── database.sql             # MySQL schema and initial seed data
```