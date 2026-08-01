# EDUsync - G.C.E. Advanced Level (A/L) School Management System

A specialized, responsive **G.C.E. Advanced Level (A/L) School Management System** built using **PHP (OOP)**, **MySQL**, **Vanilla HTML5**, **CSS3**, and **JavaScript** for execution on **XAMPP** (Apache + MySQL).

Designed specifically for Sri Lankan Secondary Schools managing Grade 12 & Grade 13 students across A/L Streams (*Physical Science*, *Biological Science*, *Commerce*, *Arts*, *Technology*).

---

## 🛠️ Tech Stack
- **Frontend**: HTML5, Vanilla CSS3 (Custom Design System with Flexbox/Grid), Vanilla JavaScript (ES6+)
- **Charts & Visualizations**: [Chart.js](https://www.chartjs.org/)
- **Backend**: PHP 8.x (Object-Oriented Programming with PDO)
- **Database**: MySQL / MariaDB (`school_db`)
- **Server**: XAMPP (Apache + MySQL)

---

## 📁 Project Structure

```text
School Management System/
├── config/
│   └── Database.php         # PDO Database Singleton Connection
├── classes/
│   ├── Student.php          # Student OOP Service Class
│   ├── Teacher.php          # Teacher OOP Service Class
│   ├── Subject.php          # Subject OOP Service Class
│   ├── Enrollment.php       # Class & Stream Allocation Service Class
│   ├── Mark.php             # A/L Term Evaluation Marks Service Class
│   └── Dashboard.php        # Dashboard Metrics & Chart Data Service Class
├── includes/
│   ├── header.php           # Shared Document Head & Fonts
│   ├── sidebar.php          # Shared Sidebar Navigation
│   ├── navbar.php           # Shared Header Bar & Search
│   └── footer.php           # Shared Footer Layout
├── assets/
│   ├── css/
│   │   └── style.css        # Main Design System CSS
│   ├── js/
│   │   └── dashboard.js     # Client Controller & Chart Renderers
│   └── img/                 # System Logos & Assets
├── index.php                # Main Dashboard View
├── students.php             # A/L Students Directory
├── teachers.php             # A/L Master Teachers Directory
├── subject.php              # A/L Subjects Directory
├── enrollments.php          # A/L Class & Stream Allocations
├── marks.php                # G.C.E. A/L Term Test Marks & Report Cards
├── reports.php              # Reports & Analytics Module
├── settings.php             # Admin Settings & Theme Preferences
├── login.php                # System Login Authentication
├── logout.php               # User Logout Handler
└── database.sql             # G.C.E. A/L MySQL Schema & Sri Lankan Seed Data
```