<?php
/**
 * EDUsync School Management System - Students Module
 * Page Template for Team Member handling Student Management
 */

$pageTitle = "Student Management";

include_once __DIR__ . '/includes/header.php';
include_once __DIR__ . '/includes/sidebar.php';
?>

<div class="main-wrapper">
    <?php include_once __DIR__ . '/includes/navbar.php'; ?>

    <main class="content-body">
        <div class="page-header">
            <div>
                <h1 class="page-title">Students Directory</h1>
                <p class="page-subtitle">Manage student admissions, profiles, and academic records.</p>
            </div>
            <div>
                <button type="button" class="btn btn-primary">+ Add New Student</button>
            </div>
        </div>

        <div class="card">
            <p style="color: var(--text-muted); font-size: 14px;">
                Student management table and CRUD interface. Team member can add student list query and modals here using <code>database.sql</code> (table <code>students</code>).
            </p>
        </div>
    </main>

<?php include_once __DIR__ . '/includes/footer.php'; ?>
