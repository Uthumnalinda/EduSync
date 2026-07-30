<?php
/**
 * EDUsync School Management System - Reports Module
 */

$pageTitle = "Reports & Analytics";

include_once __DIR__ . '/includes/header.php';
include_once __DIR__ . '/includes/sidebar.php';
?>

<div class="main-wrapper">
    <?php include_once __DIR__ . '/includes/navbar.php'; ?>

    <main class="content-body">
        <div class="page-header">
            <div>
                <h1 class="page-title">Reports & Analytics</h1>
                <p class="page-subtitle">View detailed academic reports and performance metrics.</p>
            </div>
        </div>

        <div class="card">
            <p style="color: var(--text-muted); font-size: 14px;">
                Reports page. Team member can render summary reports and metrics here.
            </p>
        </div>
    </main>

<?php include_once __DIR__ . '/includes/footer.php'; ?>
