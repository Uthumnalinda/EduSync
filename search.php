<?php
/**
 * EDUsync School Management System - Universal Global Search Page
 * Searches across Students, Teachers, and Subjects simultaneously.
 */

session_start();
if (!isset($_SESSION['user_email'])) {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . '/config/Database.php';
require_once __DIR__ . '/classes/Student.php';
require_once __DIR__ . '/classes/Teacher.php';
require_once __DIR__ . '/classes/Subject.php';

$query = isset($_GET['search']) ? trim($_GET['search']) : '';
$pageTitle = "Global Search Results";

$studentModel = new Student();
$teacherModel = new Teacher();
$subjectModel = new Subject();

$matchingStudents = [];
$matchingTeachers = [];
$matchingSubjects = [];

if ($query !== '') {
    $matchingStudents = $studentModel->getAll($query);
    $matchingTeachers = $teacherModel->getAll($query);
    $matchingSubjects = $subjectModel->getAll($query);
}

$totalResults = count($matchingStudents) + count($matchingTeachers) + count($matchingSubjects);

include_once __DIR__ . '/includes/header.php';
include_once __DIR__ . '/includes/sidebar.php';
?>

<div class="main-wrapper">
    <?php include_once __DIR__ . '/includes/navbar.php'; ?>

    <main class="content-body">
        <div class="page-header">
            <div>
                <h1 class="page-title">Search Results</h1>
                <p class="page-subtitle">
                    <?php if ($query !== ''): ?>
                        Showing <?php echo $totalResults; ?> results matching "<strong><?php echo htmlspecialchars($query); ?></strong>"
                    <?php else: ?>
                        Enter a query in the top search bar to search across students, teachers, and subjects.
                    <?php endif; ?>
                </p>
            </div>
        </div>

        <?php if ($query !== ''): ?>
            <!-- 1. Students Results -->
            <div class="dash-card card-full" style="margin-bottom: 24px;">
                <div class="dash-card-header" style="margin-bottom: 16px;">
                    <div>
                        <h2 class="dash-card-title">Students (<?php echo count($matchingStudents); ?>)</h2>
                    </div>
                    <a href="students.php?search=<?php echo urlencode($query); ?>" class="btn btn-secondary btn-sm">View in Students Directory</a>
                </div>

                <?php if (empty($matchingStudents)): ?>
                    <div style="font-size: 13px; color: var(--text-muted); padding: 12px 0;">No matching students found.</div>
                <?php else: ?>
                    <div style="overflow-x: auto;">
                        <table class="students-table" style="width: 100%; border-collapse: collapse; font-size: 13px;">
                            <thead>
                                <tr style="background: var(--body-bg); border-bottom: 1px solid var(--card-border); text-align: left; color: var(--text-muted); font-size: 11px; font-weight: 700; text-transform: uppercase;">
                                    <th style="padding: 10px 12px;">Student Code</th>
                                    <th style="padding: 10px 12px;">Full Name</th>
                                    <th style="padding: 10px 12px;">Grade & Stream</th>
                                    <th style="padding: 10px 12px;">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach (array_slice($matchingStudents, 0, 5) as $st): ?>
                                    <tr style="border-bottom: 1px solid var(--card-border);">
                                        <td style="padding: 10px 12px; font-weight: 700; color: var(--text-main);"><?php echo htmlspecialchars($st['student_code']); ?></td>
                                        <td style="padding: 10px 12px; font-weight: 600; color: var(--text-main);"><?php echo htmlspecialchars($st['first_name'] . ' ' . $st['last_name']); ?></td>
                                        <td style="padding: 10px 12px; color: var(--text-muted);"><?php echo htmlspecialchars($st['grade']); ?></td>
                                        <td style="padding: 10px 12px;">
                                            <span class="status-badge <?php echo ($st['status'] === 'Active') ? 'status-active' : 'status-inactive'; ?>">
                                                <?php echo htmlspecialchars($st['status']); ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>

            <!-- 2. Teachers Results -->
            <div class="dash-card card-full" style="margin-bottom: 24px;">
                <div class="dash-card-header" style="margin-bottom: 16px;">
                    <div>
                        <h2 class="dash-card-title">Teachers (<?php echo count($matchingTeachers); ?>)</h2>
                    </div>
                    <a href="teachers.php?search=<?php echo urlencode($query); ?>" class="btn btn-secondary btn-sm">View in Teachers Directory</a>
                </div>

                <?php if (empty($matchingTeachers)): ?>
                    <div style="font-size: 13px; color: var(--text-muted); padding: 12px 0;">No matching teachers found.</div>
                <?php else: ?>
                    <div style="overflow-x: auto;">
                        <table class="students-table" style="width: 100%; border-collapse: collapse; font-size: 13px;">
                            <thead>
                                <tr style="background: var(--body-bg); border-bottom: 1px solid var(--card-border); text-align: left; color: var(--text-muted); font-size: 11px; font-weight: 700; text-transform: uppercase;">
                                    <th style="padding: 10px 12px;">Teacher Code</th>
                                    <th style="padding: 10px 12px;">Full Name</th>
                                    <th style="padding: 10px 12px;">Subject / Specialty</th>
                                    <th style="padding: 10px 12px;">Qualification</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach (array_slice($matchingTeachers, 0, 5) as $tc): ?>
                                    <tr style="border-bottom: 1px solid var(--card-border);">
                                        <td style="padding: 10px 12px; font-weight: 700; color: var(--text-main);"><?php echo htmlspecialchars($tc['teacher_code']); ?></td>
                                        <td style="padding: 10px 12px; font-weight: 600; color: var(--text-main);"><?php echo htmlspecialchars($tc['first_name'] . ' ' . $tc['last_name']); ?></td>
                                        <td style="padding: 10px 12px; color: var(--text-muted);"><?php echo htmlspecialchars($tc['subject']); ?></td>
                                        <td style="padding: 10px 12px; color: var(--text-muted);"><?php echo htmlspecialchars($tc['qualification']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>

            <!-- 3. Subjects Results -->
            <div class="dash-card card-full">
                <div class="dash-card-header" style="margin-bottom: 16px;">
                    <div>
                        <h2 class="dash-card-title">Subjects (<?php echo count($matchingSubjects); ?>)</h2>
                    </div>
                    <a href="subject.php?search=<?php echo urlencode($query); ?>" class="btn btn-secondary btn-sm">View in Subjects Directory</a>
                </div>

                <?php if (empty($matchingSubjects)): ?>
                    <div style="font-size: 13px; color: var(--text-muted); padding: 12px 0;">No matching subjects found.</div>
                <?php else: ?>
                    <div style="overflow-x: auto;">
                        <table class="students-table" style="width: 100%; border-collapse: collapse; font-size: 13px;">
                            <thead>
                                <tr style="background: var(--body-bg); border-bottom: 1px solid var(--card-border); text-align: left; color: var(--text-muted); font-size: 11px; font-weight: 700; text-transform: uppercase;">
                                    <th style="padding: 10px 12px;">Subject Code</th>
                                    <th style="padding: 10px 12px;">Subject Name</th>
                                    <th style="padding: 10px 12px;">Grade Level</th>
                                    <th style="padding: 10px 12px;">Assigned Master Teacher</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach (array_slice($matchingSubjects, 0, 5) as $sb): ?>
                                    <tr style="border-bottom: 1px solid var(--card-border);">
                                        <td style="padding: 10px 12px; font-weight: 700; color: var(--text-main);"><?php echo htmlspecialchars($sb['course_code']); ?></td>
                                        <td style="padding: 10px 12px; font-weight: 600; color: var(--text-main);"><?php echo htmlspecialchars($sb['course_name']); ?></td>
                                        <td style="padding: 10px 12px; color: var(--text-muted);"><?php echo htmlspecialchars($sb['grade']); ?></td>
                                        <td style="padding: 10px 12px; color: var(--text-muted);"><?php echo htmlspecialchars($sb['teacher_name'] ?? 'Unassigned'); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </main>

<?php include_once __DIR__ . '/includes/footer.php'; ?>
