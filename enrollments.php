<?php
/**
 * EDUsync School Management System - Enrollments Module
 */

$pageTitle = "Student Enrollments";

include_once __DIR__ . '/includes/header.php';
include_once __DIR__ . '/includes/sidebar.php';
?>

<div class="main-wrapper">
    <?php include_once __DIR__ . '/includes/navbar.php'; ?>

    <main class="content-body">
        <div class="page-header">
            <div>
                <h1 class="page-title">Course Enrollments</h1>
                <p class="page-subtitle">Track student course enrollments and academic status.</p>
            </div>
            <div>
                <button type="button" class="btn btn-primary">+ New Enrollment</button>
            </div>
        </div>

        <div class="card">
            <p style="color: var(--text-muted); font-size: 14px;">
                Enrollments table and filtering. Team member can query table <code>enrollments</code> in <code>database.sql</code>.
            </p>
        </div>
    </main>

<?php include_once __DIR__ . '/includes/footer.php'; ?>
