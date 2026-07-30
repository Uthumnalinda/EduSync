<?php
/**
 * EDUsync School Management System - Teachers Module
 * Page Template for Team Member handling Teacher Management
 */

$pageTitle = "Teachers Directory";

include_once __DIR__ . '/includes/header.php';
include_once __DIR__ . '/includes/sidebar.php';
?>

<div class="main-wrapper">
    <?php include_once __DIR__ . '/includes/navbar.php'; ?>

    <main class="content-body">
        <div class="page-header">
            <div>
                <h1 class="page-title">Faculty & Teachers</h1>
                <p class="page-subtitle">Manage teaching staff, assignments, and qualifications.</p>
            </div>
            <div>
                <button type="button" class="btn btn-primary">+ Add New Teacher</button>
            </div>
        </div>

        <div class="card">
            <p style="color: var(--text-muted); font-size: 14px;">
                Teachers management table and CRUD interface. Team member can add teacher query and forms here using <code>database.sql</code> (table <code>teachers</code>).
            </p>
        </div>
    </main>

<?php include_once __DIR__ . '/includes/footer.php'; ?>
