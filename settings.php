<?php
/**
 * EDUsync School Management System - Settings Module
 */

$pageTitle = "System Settings";

include_once __DIR__ . '/includes/header.php';
include_once __DIR__ . '/includes/sidebar.php';
?>

<div class="main-wrapper">
    <?php include_once __DIR__ . '/includes/navbar.php'; ?>

    <main class="content-body">
        <div class="page-header">
            <div>
                <h1 class="page-title">System Settings</h1>
                <p class="page-subtitle">Configure school profile, academic year, and system parameters.</p>
            </div>
        </div>

        <div class="card">
            <p style="color: var(--text-muted); font-size: 14px;">
                Settings page. Team member can build setting forms here.
            </p>
        </div>
    </main>

<?php include_once __DIR__ . '/includes/footer.php'; ?>
