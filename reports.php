<?php
/**
 * EDUsync School Management System - Academic Reports Page
 */

session_start();
if (!isset($_SESSION['user_email'])) {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . '/config/Database.php';
require_once __DIR__ . '/classes/Report.php';
require_once __DIR__ . '/classes/Student.php';

$pageTitle = "Academic Reports";
$reportTab = isset($_GET['tab']) ? $_GET['tab'] : 'student_report';

$reportModel = new Report();
$studentModel = new Student();

$allStudents = $studentModel->getAll();
$selectedStudentId = isset($_GET['student_id']) ? (int)$_GET['student_id'] : ($allStudents[0]['id'] ?? 0);

$reportCardData = false;
if ($selectedStudentId > 0) {
    $reportCardData = $reportModel->getStudentReportCard($selectedStudentId);
}

$streamData = $reportModel->getStreamPerformance();
$subjectDistribution = $reportModel->getSubjectGradeDistribution();

include_once __DIR__ . '/includes/header.php';
include_once __DIR__ . '/includes/sidebar.php';
?>

<style>
@media print {
    body * {
        visibility: hidden;
    }
    #printableReportCard, #printableReportCard * {
        visibility: visible;
    }
    #printableReportCard {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        padding: 0;
        background: #ffffff !important;
        color: #000000 !important;
    }
    .no-print {
        display: none !important;
    }
}
</style>

<div class="main-wrapper">
    <?php include_once __DIR__ . '/includes/navbar.php'; ?>

    <main class="content-body">
        <div class="page-header no-print">
            <div>
                <h1 class="page-title">Reports</h1>
                <p class="page-subtitle">View student progress report cards and academic performance breakdowns.</p>
            </div>
            <?php if ($reportTab === 'student_report' && $reportCardData): ?>
                <div>
                    <button type="button" class="btn btn-primary" onclick="window.print();" style="height: 40px; padding: 0 18px; font-weight: 600;">
                        Print Report
                    </button>
                </div>
            <?php endif; ?>
        </div>

        <!-- Filter & Tab Navigation Bar -->
        <div class="table-filter-bar no-print" style="margin-bottom: 20px;">
            <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap; flex: 1;">
                <a href="reports.php?tab=student_report<?php echo $selectedStudentId ? '&student_id=' . $selectedStudentId : ''; ?>" style="padding: 8px 14px; font-weight: 600; font-size: 13px; text-decoration: none; border-radius: 6px; background: <?php echo ($reportTab === 'student_report') ? 'var(--primary)' : 'var(--body-bg)'; ?>; color: <?php echo ($reportTab === 'student_report') ? '#ffffff' : 'var(--text-main)'; ?>; border: 1px solid var(--card-border);">
                    Student Report Cards
                </a>
                <a href="reports.php?tab=stream_analytics" style="padding: 8px 14px; font-weight: 600; font-size: 13px; text-decoration: none; border-radius: 6px; background: <?php echo ($reportTab === 'stream_analytics') ? 'var(--primary)' : 'var(--body-bg)'; ?>; color: <?php echo ($reportTab === 'stream_analytics') ? '#ffffff' : 'var(--text-main)'; ?>; border: 1px solid var(--card-border);">
                    Stream Performance
                </a>
                <a href="reports.php?tab=subject_distribution" style="padding: 8px 14px; font-weight: 600; font-size: 13px; text-decoration: none; border-radius: 6px; background: <?php echo ($reportTab === 'subject_distribution') ? 'var(--primary)' : 'var(--body-bg)'; ?>; color: <?php echo ($reportTab === 'subject_distribution') ? '#ffffff' : 'var(--text-main)'; ?>; border: 1px solid var(--card-border);">
                    Grade Distribution
                </a>
            </div>

            <?php if ($reportTab === 'student_report'): ?>
                <form action="reports.php" method="GET" style="display: flex; align-items: center; gap: 10px;">
                    <input type="hidden" name="tab" value="student_report">
                    <input type="text" id="reportStudentFilter" class="login-input" placeholder="Type student name or ID..." style="height: 40px; width: 220px; font-size: 13px; padding: 4px 12px; background: var(--card-bg); border: 1px solid var(--card-border); color: var(--text-main);">
                    <select name="student_id" id="reportStudentSelect" class="filter-select" style="min-width: 280px; height: 40px;" onchange="this.form.submit()">
                        <?php foreach ($allStudents as $st): ?>
                            <option value="<?php echo $st['id']; ?>" <?php echo ($selectedStudentId == $st['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($st['student_code'] . ' - ' . $st['first_name'] . ' ' . $st['last_name'] . ' (' . $st['grade'] . ')'); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </form>
            <?php endif; ?>
        </div>

        <?php if ($reportTab === 'student_report'): ?>
            <?php if ($reportCardData): ?>
                <?php 
                    $st = $reportCardData['student'];
                    $subjectMatrix = $reportCardData['subject_matrix'];
                    
                    $totalScoreSum = 0;
                    $totalScoreCount = 0;
                    foreach ($subjectMatrix as $sub) {
                        foreach ($sub['scores'] as $sc) {
                            $totalScoreSum += $sc;
                            $totalScoreCount++;
                        }
                    }
                    $annualAvg = ($totalScoreCount > 0) ? round($totalScoreSum / $totalScoreCount, 1) : 0;
                ?>
                
                <!-- Clean Student Report Card -->
                <div class="dash-card card-full" id="printableReportCard" style="padding: 24px; border: 1px solid var(--card-border);">
                    
                    <!-- Header -->
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 1px solid var(--card-border); padding-bottom: 14px; margin-bottom: 20px;">
                        <div>
                            <h2 style="font-size: 18px; font-weight: 700; color: var(--text-main); margin: 0 0 4px 0;">
                                Student Progress Report
                            </h2>
                            <div style="font-size: 13px; color: var(--text-muted);">
                                Academic Evaluation Session
                            </div>
                        </div>
                        <div style="text-align: right; font-size: 12px; color: var(--text-muted);">
                            Date: <?php echo date('M d, Y'); ?>
                        </div>
                    </div>

                    <!-- Student Details Grid -->
                    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 24px; padding: 14px; background: var(--body-bg); border-radius: 6px; border: 1px solid var(--card-border);">
                        <div>
                            <span style="font-size: 11px; font-weight: 700; color: var(--text-muted); display: block; text-transform: uppercase;">Student Name</span>
                            <strong style="font-size: 13px; color: var(--text-main);"><?php echo htmlspecialchars($st['first_name'] . ' ' . $st['last_name']); ?></strong>
                        </div>
                        <div>
                            <span style="font-size: 11px; font-weight: 700; color: var(--text-muted); display: block; text-transform: uppercase;">Student Code</span>
                            <strong style="font-size: 13px; color: var(--text-main);"><?php echo htmlspecialchars($st['student_code']); ?></strong>
                        </div>
                        <div>
                            <span style="font-size: 11px; font-weight: 700; color: var(--text-muted); display: block; text-transform: uppercase;">Admission No</span>
                            <strong style="font-size: 13px; color: var(--text-main);"><?php echo htmlspecialchars($st['adm_no']); ?></strong>
                        </div>
                        <div>
                            <span style="font-size: 11px; font-weight: 700; color: var(--text-muted); display: block; text-transform: uppercase;">Grade & Stream</span>
                            <strong style="font-size: 13px; color: var(--text-main);"><?php echo htmlspecialchars($st['grade']); ?></strong>
                        </div>
                    </div>

                    <!-- Evaluation Table -->
                    <div style="overflow-x: auto; margin-bottom: 20px;">
                        <table style="width: 100%; border-collapse: collapse; font-size: 13px; border: 1px solid var(--card-border);">
                            <thead>
                                <tr style="background: var(--body-bg); border-bottom: 1px solid var(--card-border); text-align: left; color: var(--text-muted); font-weight: 700; font-size: 11px; text-transform: uppercase;">
                                    <th style="padding: 10px 12px; border: 1px solid var(--card-border);">Code</th>
                                    <th style="padding: 10px 12px; border: 1px solid var(--card-border);">Subject</th>
                                    <th style="padding: 10px 12px; border: 1px solid var(--card-border); text-align: center;">Term 1</th>
                                    <th style="padding: 10px 12px; border: 1px solid var(--card-border); text-align: center;">Term 2</th>
                                    <th style="padding: 10px 12px; border: 1px solid var(--card-border); text-align: center;">Term 3</th>
                                    <th style="padding: 10px 12px; border: 1px solid var(--card-border); text-align: center;">Average</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($subjectMatrix)): ?>
                                    <tr>
                                        <td colspan="6" style="padding: 16px; text-align: center; color: var(--text-muted);">
                                            No evaluation marks recorded for this student yet.
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($subjectMatrix as $row): ?>
                                        <?php 
                                            $avg = (count($row['scores']) > 0) ? round(array_sum($row['scores']) / count($row['scores']), 1) : null;
                                        ?>
                                        <tr style="border-bottom: 1px solid var(--card-border);">
                                            <td style="padding: 10px 12px; font-weight: 700; color: var(--text-main); border: 1px solid var(--card-border); white-space: nowrap;"><?php echo htmlspecialchars($row['code']); ?></td>
                                            <td style="padding: 10px 12px; font-weight: 600; color: var(--text-main); border: 1px solid var(--card-border); white-space: nowrap;"><?php echo htmlspecialchars($row['name']); ?></td>
                                            
                                            <td style="padding: 10px 12px; text-align: center; border: 1px solid var(--card-border); white-space: nowrap;">
                                                <?php if ($row['t1_marks'] !== null): ?>
                                                    <?php echo number_format($row['t1_marks'], 1); ?>% (<?php echo $row['t1_grade']; ?>)
                                                <?php else: ?>
                                                    -
                                                <?php endif; ?>
                                            </td>
                                            
                                            <td style="padding: 10px 12px; text-align: center; border: 1px solid var(--card-border); white-space: nowrap;">
                                                <?php if ($row['t2_marks'] !== null): ?>
                                                    <?php echo number_format($row['t2_marks'], 1); ?>% (<?php echo $row['t2_grade']; ?>)
                                                <?php else: ?>
                                                    -
                                                <?php endif; ?>
                                            </td>
                                            
                                            <td style="padding: 10px 12px; text-align: center; border: 1px solid var(--card-border); white-space: nowrap;">
                                                <?php if ($row['t3_marks'] !== null): ?>
                                                    <?php echo number_format($row['t3_marks'], 1); ?>% (<?php echo $row['t3_grade']; ?>)
                                                <?php else: ?>
                                                    -
                                                <?php endif; ?>
                                            </td>

                                            <td style="padding: 10px 12px; text-align: center; font-weight: 700; color: var(--primary); border: 1px solid var(--card-border); white-space: nowrap;">
                                                <?php echo ($avg !== null) ? $avg . '%' : '-'; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Summary Row -->
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 10px 14px; background: var(--body-bg); border-radius: 6px; font-size: 13px; margin-bottom: 24px; border: 1px solid var(--card-border);">
                        <span>Overall Average: <strong style="color: var(--primary);"><?php echo $annualAvg; ?>%</strong></span>
                        <span style="color: var(--text-muted);">Total Marks: <strong style="color: var(--text-main);"><?php echo $totalScoreSum; ?></strong></span>
                    </div>

                    <!-- Signature Area -->
                    <div style="display: flex; justify-content: space-between; margin-top: 40px; padding-top: 10px;">
                        <div style="text-align: center; width: 200px;">
                            <div style="border-bottom: 1px solid var(--text-main); margin-bottom: 6px; height: 30px;"></div>
                            <span style="font-size: 12px; color: var(--text-muted);">Class Teacher Signature</span>
                        </div>
                        <div style="text-align: center; width: 200px;">
                            <div style="border-bottom: 1px solid var(--text-main); margin-bottom: 6px; height: 30px;"></div>
                            <span style="font-size: 12px; color: var(--text-muted);">Principal Signature</span>
                        </div>
                    </div>

                </div>
            <?php endif; ?>

        <?php elseif ($reportTab === 'stream_analytics'): ?>
            <div class="dash-card card-full" style="padding: 0; overflow: hidden;">
                <div style="overflow-x: auto;">
                    <table class="students-table" style="width: 100%; border-collapse: collapse; font-size: 14px;">
                        <thead>
                            <tr style="background: var(--body-bg); border-bottom: 1px solid var(--card-border); text-align: left; color: var(--text-muted); font-size: 12px; font-weight: 700; text-transform: uppercase;">
                                <th style="padding: 12px 14px; white-space: nowrap;">Stream</th>
                                <th style="padding: 12px 14px; white-space: nowrap;">Students</th>
                                <th style="padding: 12px 14px; white-space: nowrap;">Evaluations</th>
                                <th style="padding: 12px 14px; white-space: nowrap;">Average Score</th>
                                <th style="padding: 12px 14px; white-space: nowrap;">Pass Rate</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($streamData as $row): ?>
                                <tr style="border-bottom: 1px solid var(--card-border);">
                                    <td style="padding: 12px 14px; font-weight: 700; color: var(--text-main); white-space: nowrap;">
                                        <?php echo htmlspecialchars($row['stream_name']); ?>
                                    </td>
                                    <td style="padding: 12px 14px; white-space: nowrap;"><?php echo $row['total_students']; ?></td>
                                    <td style="padding: 12px 14px; white-space: nowrap;"><?php echo $row['total_evaluations']; ?></td>
                                    <td style="padding: 12px 14px; font-weight: 700; color: var(--primary); white-space: nowrap;">
                                        <?php echo number_format($row['avg_mark'] ?: 0, 1); ?>%
                                    </td>
                                    <td style="padding: 12px 14px; font-weight: 700; color: var(--text-main); white-space: nowrap;">
                                        <?php echo ($row['total_evaluations'] > 0) ? round(($row['count_credit'] / $row['total_evaluations']) * 100, 1) : 0; ?>%
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        <?php elseif ($reportTab === 'subject_distribution'): ?>
            <div class="dash-card card-full" style="padding: 0; overflow: hidden;">
                <div style="overflow-x: auto;">
                    <table class="students-table" style="width: 100%; border-collapse: collapse; font-size: 14px;">
                        <thead>
                            <tr style="background: var(--body-bg); border-bottom: 1px solid var(--card-border); text-align: left; color: var(--text-muted); font-size: 12px; font-weight: 700; text-transform: uppercase;">
                                <th style="padding: 12px 14px; white-space: nowrap;">Code</th>
                                <th style="padding: 12px 14px; white-space: nowrap;">Subject</th>
                                <th style="padding: 12px 14px; white-space: nowrap;">Average Score</th>
                                <th style="padding: 12px 14px; text-align: center; white-space: nowrap;">Grade A</th>
                                <th style="padding: 12px 14px; text-align: center; white-space: nowrap;">Grade B</th>
                                <th style="padding: 12px 14px; text-align: center; white-space: nowrap;">Grade C</th>
                                <th style="padding: 12px 14px; text-align: center; white-space: nowrap;">Grade S</th>
                                <th style="padding: 12px 14px; text-align: center; white-space: nowrap;">Grade F</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($subjectDistribution as $s): ?>
                                <tr style="border-bottom: 1px solid var(--card-border);">
                                    <td style="padding: 12px 14px; font-weight: 700; color: var(--text-main); white-space: nowrap;"><?php echo htmlspecialchars($s['course_code']); ?></td>
                                    <td style="padding: 12px 14px; font-weight: 600; color: var(--text-main); white-space: nowrap;"><?php echo htmlspecialchars($s['course_name']); ?></td>
                                    <td style="padding: 12px 14px; font-weight: 700; color: var(--primary); white-space: nowrap;"><?php echo number_format($s['avg_score'], 1); ?>%</td>
                                    <td style="padding: 12px 14px; text-align: center; font-weight: 700; color: #15803d; white-space: nowrap;"><?php echo $s['count_a']; ?></td>
                                    <td style="padding: 12px 14px; text-align: center; font-weight: 700; color: #1d4ed8; white-space: nowrap;"><?php echo $s['count_b']; ?></td>
                                    <td style="padding: 12px 14px; text-align: center; font-weight: 700; color: #0284c7; white-space: nowrap;"><?php echo $s['count_c']; ?></td>
                                    <td style="padding: 12px 14px; text-align: center; font-weight: 700; color: #d97706; white-space: nowrap;"><?php echo $s['count_s']; ?></td>
                                    <td style="padding: 12px 14px; text-align: center; font-weight: 700; color: #b91c1c; white-space: nowrap;"><?php echo $s['count_f']; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>
    </main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const filterInput = document.getElementById('reportStudentFilter');
    const studentSelect = document.getElementById('reportStudentSelect');
    if (filterInput && studentSelect) {
        filterInput.addEventListener('input', function() {
            const filterVal = this.value.toLowerCase().trim();
            Array.from(studentSelect.options).forEach(opt => {
                const text = opt.text.toLowerCase();
                opt.style.display = text.includes(filterVal) ? '' : 'none';
            });
        });
    }
});
</script>

<?php include_once __DIR__ . '/includes/footer.php'; ?>
