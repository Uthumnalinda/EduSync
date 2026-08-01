# EDUsync - School Management Dashboard

A modern, responsive **School Management Dashboard** built using **PHP (OOP)**, **MySQL**, **Vanilla HTML5**, **CSS3**, and **JavaScript** for execution on **XAMPP** (Apache + MySQL).

---

## 🛠️ Tech Stack
- **Frontend**: HTML5, Vanilla CSS3 (Custom Design System with Flexbox/Grid), Vanilla JavaScript (ES6+)
- **Charts & Visualizations**: [Chart.js](https://www.chartjs.org/) (via CDN)
- **Backend**: PHP 8.x (Object-Oriented Programming with PDO)
- **Database**: MySQL / MariaDB (`school_db`)
- **Server**: XAMPP (Apache + MySQL)

---

## 📁 Project Structure

```
School Management Dashboard/
├── config/
│   └── Database.php         # PDO Database Singleton Class
├── classes/
│   └── Dashboard.php        # Dashboard OOP Service Class
├── includes/
│   ├── header.php           # Shared Document Head & CSS
│   ├── sidebar.php          # Shared Navigation Sidebar
│   ├── navbar.php           # Shared Header Bar
│   └── footer.php           # Shared Footer Layout
├── assets/
│   ├── css/
│   │   └── style.css        # Main Design System CSS
│   └── js/
│       └── dashboard.js     # Sidebar toggle & Chart renderers
├── index.php                # Main Dashboard View
├── students.php             # Students Module Starter
├── teachers.php             # Teachers Module Starter
├── subject.php              # Subjects Module Directory
├── enrollments.php          # Enrollments Module Starter
├── reports.php              # Reports & Analytics Starter
├── settings.php             # System Settings Starter
└── database.sql             # MySQL Schema & Seed Data Script
```

---

## 🚀 Setup & Installation (XAMPP)

1. **Start Apache & MySQL**:
   Open XAMPP Control Panel and start **Apache** and **MySQL**.

2. **Import Database (`database.sql`)**:
   - Go to `http://localhost/phpmyadmin` in your web browser.
   - Click on the **Import** tab.
   - Choose the `database.sql` file from this project folder.
   - Click **Go** to create the `school_db` database and populate seed data.

3. **Deploy to `htdocs`**:
   - Move or copy this project folder into `C:/xampp/htdocs/school_dashboard/`.

4. **Launch Application**:
   - Visit `http://localhost/school_dashboard/index.php` in your web browser.