<?php
/**
 * EDUsync School Management System - Courses Module
 */

$pageTitle = "Course Catalog";

include_once __DIR__ . '/includes/header.php';
include_once __DIR__ . '/includes/sidebar.php';
?>

<div class="main-wrapper">
    <?php include_once __DIR__ . '/includes/navbar.php'; ?>

    <main class="content-body">
        <div class="page-header">
            <div>
                <h1 class="page-title">Course Catalog</h1>
                <p class="page-subtitle">Manage curriculum, subject allocations, and grade levels.</p>
            </div>
            <div>
                <button type="button" class="btn btn-primary">+ Create Course</button>
            </div>
        </div>

        <div class="card">
            <p style="color: var(--text-muted); font-size: 14px;">
                Courses management catalog. Team member can query table <code>courses</code> in <code>database.sql</code>.
            </p>
        </div>
    </main>

<?php include_once __DIR__ . '/includes/footer.php'; ?>
