<?php
session_start();

// Redirect to login page if user is not logged in
if (!isset($_SESSION['user_email'])) {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . '/classes/Dashboard.php';

// Instantiate OOP Dashboard Service
$dashboardService = new Dashboard();
$metrics = $dashboardService->getMetrics();
$enrollmentTrends = $dashboardService->getEnrollmentTrends();
$courseDist = $dashboardService->getCourseDistribution();
$gradeBreakdown = $dashboardService->getGradeBreakdown();
$recentStudents = $dashboardService->getRecentStudents();

$pageTitle = "Dashboard";

// Include Shared Header and Navigation
include_once __DIR__ . '/includes/header.php';
include_once __DIR__ . '/includes/sidebar.php';
?>

<!-- Main Content Area Wrapper -->
<div class="main-wrapper">
    <!-- Shared Top Navbar -->
    <?php include_once __DIR__ . '/includes/navbar.php'; ?>

    <!-- Dashboard Content Body -->
    <main class="content-body">
        <!-- 1. Top Row: 4 KPI Cards Grid (100% Live Database Numbers Only) -->
        <div class="stats-grid">
            <!-- Total Students -->
            <div class="stat-card card-blue-theme">
                <div class="stat-top">
                    <div class="stat-icon-box icon-blue" title="Student Icon by Iconpacks">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="11" r="3"/><path d="M12 2L4 6l8 4 8-4-8-4z"/><path d="M20 6v3"/></svg>
                    </div>
                </div>
                <div class="stat-main">
                    <div class="stat-value"><?php echo number_format($metrics['total_students']); ?></div>
                    <div class="stat-label">Total Students</div>
                </div>
            </div>

            <!-- Total Teachers -->
            <div class="stat-card card-green-theme">
                <div class="stat-top">
                    <div class="stat-icon-box icon-green" title="Teacher Icon">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 19v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/><rect x="14" y="3" width="8" height="9" rx="1"/><path d="M17 12v3"/></svg>
                    </div>
                </div>
                <div class="stat-main">
                    <div class="stat-value"><?php echo number_format($metrics['active_teachers']); ?></div>
                    <div class="stat-label">Total Teachers</div>
                </div>
            </div>

            <!-- Total Courses -->
            <div class="stat-card card-yellow-theme">
                <div class="stat-top">
                    <div class="stat-icon-box icon-yellow" title="Course Icon">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
                    </div>
                </div>
                <div class="stat-main">
                    <div class="stat-value"><?php echo number_format($metrics['total_courses']); ?></div>
                    <div class="stat-label">Total Subjects</div>
                </div>
            </div>

            <!-- Enrollments -->
            <div class="stat-card card-purple-theme">
                <div class="stat-top">
                    <div class="stat-icon-box icon-purple" title="Enrollment Icon">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><rect x="8" y="2" width="8" height="4" rx="1" ry="1"/><path d="M9 12l2 2 4-4"/><path d="M9 16h6"/></svg>
                    </div>
                </div>
                <div class="stat-main">
                    <div class="stat-value"><?php echo number_format($metrics['active_enrollments']); ?></div>
                    <div class="stat-label">Enrollments</div>
                </div>
            </div>
        </div>

        <!-- 2. Middle Row: Student Enrollment Trend & Course Distribution (Original Layout) -->
        <div class="dashboard-row row-middle">
            <!-- Student Enrollment Trend -->
            <div class="dash-card card-large">
                <div class="dash-card-header">
                    <div>
                        <h2 class="dash-card-title">Student Enrollment Trend</h2>
                        <span class="dash-card-subtitle">Academic Year 2024</span>
                    </div>
                </div>
                <div class="chart-box" style="height: 260px;">
                    <canvas id="enrollmentChart"></canvas>
                </div>
            </div>

            <!-- Subject Distribution -->
            <div class="dash-card card-small">
                <div class="dash-card-header">
                    <div>
                        <h2 class="dash-card-title">Subject Distribution</h2>
                        <span class="dash-card-subtitle">By enrollment count</span>
                    </div>
                </div>
                <div class="course-dist-wrapper">
                    <div class="doughnut-container" style="height: 180px;">
                        <canvas id="courseDistChart"></canvas>
                    </div>
                    <div class="course-legend-list">
                        <?php foreach ($courseDist as $c): ?>
                            <div class="legend-item">
                                <div class="legend-left">
                                    <span class="legend-dot" style="background: <?php echo $c['color']; ?>;"></span>
                                    <span class="legend-name"><?php echo htmlspecialchars($c['name']); ?></span>
                                </div>
                                <span class="legend-count"><?php echo $c['value']; ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. Bottom Row: Students by Grade & Recently Added Students (Original Layout) -->
        <div class="dashboard-row row-bottom">
            <!-- Students by Grade -->
            <div class="dash-card card-small">
                <div class="dash-card-header">
                    <div>
                        <h2 class="dash-card-title">Students by Grade</h2>
                        <span class="dash-card-subtitle">Current enrollment</span>
                    </div>
                </div>
                <div class="chart-box" style="height: 240px;">
                    <canvas id="gradeChart"></canvas>
                </div>
            </div>

            <!-- Recently Added Students -->
            <div class="dash-card card-large">
                <div class="dash-card-header flex-between">
                    <h2 class="dash-card-title">Recently Added Students</h2>
                    <a href="students.php" class="view-all-link">View all →</a>
                </div>
                <div class="recent-students-list">
                    <?php foreach ($recentStudents as $st): ?>
                        <div class="recent-student-item">
                            <div class="recent-student-left">
                                <div class="student-avatar" style="background: <?php echo $st['bg']; ?>;">
                                    <?php echo htmlspecialchars($st['initials']); ?>
                                </div>
                                <div class="student-info">
                                    <span class="student-name"><?php echo htmlspecialchars($st['name']); ?></span>
                                    <span class="student-meta"><?php echo htmlspecialchars($st['code']); ?>  •  <?php echo htmlspecialchars($st['grade']); ?></span>
                                </div>
                            </div>
                            <span class="status-badge status-<?php echo strtolower($st['status']); ?>">
                                • <?php echo htmlspecialchars($st['status']); ?>
                            </span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </main>
</div>

<!-- Transfer PHP Data to JavaScript for Chart Rendering -->
<script>
window.dashboardData = {
    enrollmentTrends: <?php echo json_encode($enrollmentTrends); ?>,
    courseDist: <?php echo json_encode($courseDist); ?>,
    gradeBreakdown: <?php echo json_encode($gradeBreakdown); ?>
};
</script>

<?php 
// Include Shared Footer
include_once __DIR__ . '/includes/footer.php'; 
?>
