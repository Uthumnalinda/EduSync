<?php
/**
 * EDUsync School Management System - Term Test Marks & Report Cards Module
 * Sri Lankan Evaluation System (Term 1, Term 2, Term 3 with A, B, C, S, F Grades)
 */

require_once __DIR__ . '/classes/Mark.php';
require_once __DIR__ . '/classes/Enrollment.php';
require_once __DIR__ . '/classes/Subject.php';

$markService = new Mark();
$enrollmentService = new Enrollment();
$subjectService = new Subject();

$message = '';
$error = '';

// Handle Actions (Add, Edit, Delete)
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'create') {
        if (empty($_POST['student_id']) || empty($_POST['course_id']) || !isset($_POST['marks_obtained'])) {
            $error = "Please fill in all required mark entry fields.";
        } else {
            $marks = (float)$_POST['marks_obtained'];
            if ($marks < 0 || $marks > 100) {
                $error = "Marks obtained must be between 0 and 100.";
            } else {
                $result = $markService->create($_POST);
                if ($result) {
                    $message = "Term test mark recorded successfully!";
                } else {
                    $error = "Failed to record term test mark.";
                }
            }
        }
    } elseif ($action === 'update') {
        $id = (int)($_POST['id'] ?? 0);
        $marks = (float)($_POST['marks_obtained'] ?? 0);
        if ($marks < 0 || $marks > 100) {
            $error = "Marks obtained must be between 0 and 100.";
        } else {
            $result = $markService->update($id, $_POST);
            if ($result) {
                $message = "Term test mark updated successfully!";
            } else {
                $error = "Failed to update term test mark.";
            }
        }
    } elseif ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        $result = $markService->delete($id);
        if ($result) {
            $message = "Term test mark deleted successfully!";
        } else {
            $error = "Failed to delete term test mark.";
        }
    }
}

// Fetch Search & Filters
$search = $_GET['search'] ?? '';
$termFilter = $_GET['term'] ?? '';
$courseFilter = $_GET['course_id'] ?? '';

$marksList = $markService->getAll($search, $termFilter, $courseFilter);
$summaryStats = $markService->getSummaryStats();
$activeStudents = $enrollmentService->getActiveStudents();
$allSubjects = $subjectService->getAll();

$pageTitle = "Term Test Marks";

include_once __DIR__ . '/includes/header.php';
include_once __DIR__ . '/includes/sidebar.php';
?>

<div class="main-wrapper">
    <?php include_once __DIR__ . '/includes/navbar.php'; ?>

    <main class="content-body">
        <div class="page-header">
            <div>
                <h1 class="page-title">Term Test Marks</h1>
                <p class="page-subtitle">Record 1st, 2nd, and 3rd Term test scores and auto-generate Sri Lankan A, B, C, S, F grades.</p>
            </div>
            <div>
                <button type="button" class="btn btn-primary" id="openAddModalBtn">+ Record Term Marks</button>
            </div>
        </div>

        <?php if (!empty($message)): ?>
            <div class="flash-alert" style="background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; padding: 12px 16px; border-radius: 12px; margin-bottom: 20px; font-size: 14px; font-weight: 500;">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($error)): ?>
            <div class="flash-alert" style="background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; padding: 12px 16px; border-radius: 12px; margin-bottom: 20px; font-size: 14px; font-weight: 500;">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <!-- Stat Summary Cards Row (3 Cards in 1 Row) -->
        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 24px;">
            <div class="stat-card card-blue-theme">
                <div class="stat-main">
                    <div class="stat-value"><?php echo $summaryStats['avg_score']; ?>%</div>
                    <div class="stat-label">Average Term Score</div>
                </div>
            </div>
            <div class="stat-card card-green-theme">
                <div class="stat-main">
                    <div class="stat-value"><?php echo $summaryStats['a_pass_rate']; ?>%</div>
                    <div class="stat-label">Distinction (A) Pass Rate</div>
                </div>
            </div>
            <div class="stat-card card-yellow-theme">
                <div class="stat-main">
                    <div class="stat-value"><?php echo $summaryStats['total_evaluated']; ?></div>
                    <div class="stat-label">Total Exam Records</div>
                </div>
            </div>
        </div>

        <!-- Table Filters & Search -->
        <div class="table-filter-bar">
            <form action="marks.php" method="GET" class="search-filter-group">
                <div style="position: relative; flex: 1;">
                    <span style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: var(--text-muted);">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                    </span>
                    <input type="text" name="search" class="filter-input" placeholder="Search by student name, code, subject..." value="<?php echo htmlspecialchars($search); ?>">
                </div>

                <select name="term" class="filter-select" onchange="this.form.submit()">
                    <option value="">All Terms</option>
                    <option value="Term 1" <?php echo $termFilter === 'Term 1' ? 'selected' : ''; ?>>Term 1</option>
                    <option value="Term 2" <?php echo $termFilter === 'Term 2' ? 'selected' : ''; ?>>Term 2</option>
                    <option value="Term 3" <?php echo $termFilter === 'Term 3' ? 'selected' : ''; ?>>Term 3</option>
                </select>

                <?php if ($search || $termFilter || $courseFilter): ?>
                    <a href="marks.php" class="btn btn-secondary btn-sm" style="height: 40px; padding: 0 14px;">Clear</a>
                <?php endif; ?>
            </form>
            <div style="color: var(--text-muted); font-size: 13px; font-weight: 600;">
                Total Evaluations: <?php echo count($marksList); ?>
            </div>
        </div>

        <!-- Marks Data Table Card -->
        <div class="dash-card card-full" style="padding: 0; overflow: hidden;">
            <div style="overflow-x: auto;">
                <table class="students-table" style="width: 100%; border-collapse: collapse; font-size: 14px;">
                    <thead>
                        <tr style="background: var(--body-bg); border-bottom: 1px solid var(--card-border); text-align: left; color: var(--text-muted); font-size: 12px; font-weight: 700; text-transform: uppercase;">
                            <th style="padding: 12px 14px; white-space: nowrap;">Code / Adm</th>
                            <th style="padding: 12px 14px; white-space: nowrap;">Student Name</th>
                            <th style="padding: 12px 14px; white-space: nowrap;">Subject</th>
                            <th style="padding: 12px 14px; white-space: nowrap;">Evaluation Term</th>
                            <th style="padding: 12px 14px; white-space: nowrap;">Marks (100)</th>
                            <th style="padding: 12px 14px; white-space: nowrap;">Sri Lankan Grade</th>
                            <th style="padding: 12px 14px; white-space: nowrap;">Remarks</th>
                            <th style="padding: 12px 14px; text-align: right; white-space: nowrap;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($marksList)): ?>
                            <tr>
                                <td colspan="8" style="padding: 40px; text-align: center; color: var(--text-muted);">
                                    No term test marks recorded yet.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($marksList as $m): ?>
                                <tr style="border-bottom: 1px solid var(--card-border);">
                                    <td style="padding: 12px 14px; white-space: nowrap;">
                                        <div style="font-weight: 700; color: var(--text-main);"><?php echo htmlspecialchars($m['student_code']); ?></div>
                                        <div style="font-size: 12px; color: var(--text-muted);"><?php echo htmlspecialchars($m['adm_no']); ?></div>
                                    </td>
                                    <td style="padding: 12px 14px; white-space: nowrap;">
                                        <div style="font-weight: 600; color: var(--text-main);"><?php echo htmlspecialchars($m['student_name']); ?></div>
                                        <div style="font-size: 12px; color: var(--text-muted);"><?php echo htmlspecialchars($m['student_grade']); ?></div>
                                    </td>
                                    <td style="padding: 12px 14px; white-space: nowrap;">
                                        <span class="subject-badge">
                                            <?php echo htmlspecialchars($m['subject_name']); ?>
                                        </span>
                                    </td>
                                    <td style="padding: 12px 14px; font-weight: 600; color: var(--text-main); white-space: nowrap;">
                                        <?php echo htmlspecialchars($m['term']); ?>
                                    </td>
                                    <td style="padding: 12px 14px; font-weight: 700; color: var(--text-main); white-space: nowrap;">
                                        <?php echo number_format($m['marks_obtained'], 1); ?>
                                    </td>
                                    <td style="padding: 12px 14px; white-space: nowrap;">
                                        <?php 
                                            $g = $m['grade'];
                                            $badgeClass = 'status-active';
                                            if ($g === 'A') $badgeClass = 'status-active';
                                            elseif ($g === 'B' || $g === 'C') $badgeClass = 'status-badge';
                                            elseif ($g === 'S') $badgeClass = 'status-inactive';
                                            elseif ($g === 'F') $badgeClass = 'status-inactive';
                                        ?>
                                        <span class="status-badge <?php echo $badgeClass; ?>" style="font-weight: 800; padding: 4px 10px;">
                                            Grade <?php echo htmlspecialchars($g); ?>
                                        </span>
                                    </td>
                                    <td style="padding: 12px 14px; font-size: 13px; color: var(--text-muted);">
                                        <?php echo htmlspecialchars($m['remarks'] ?? '-'); ?>
                                    </td>
                                    <td style="padding: 12px 14px; text-align: right; white-space: nowrap;">
                                        <div style="display: flex; align-items: center; justify-content: flex-end; gap: 6px;">
                                            <button type="button" class="btn btn-secondary btn-sm edit-mark-btn" 
                                                    data-mark='<?php echo json_encode($m, JSON_HEX_APOS | JSON_HEX_QUOT); ?>'>
                                                Edit
                                            </button>
                                            <form action="marks.php" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this term mark?');">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="id" value="<?php echo $m['id']; ?>">
                                                <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <!-- Marks Modal (Add / Edit) -->
    <div class="modal-overlay" id="markModal">
        <div class="modal-card">
            <div class="modal-header">
                <h3 class="modal-title" id="modalTitle">Record Term Test Mark</h3>
                <button type="button" class="modal-close-btn" id="closeModalBtn">&times;</button>
            </div>
            <form action="marks.php" method="POST" id="markForm">
                <input type="hidden" name="action" id="formAction" value="create">
                <input type="hidden" name="id" id="markId" value="">

                <div class="modal-body">
                    <div class="input-field-group">
                        <label class="input-label">Select Student *</label>
                        <select name="student_id" id="studentId" class="login-input" style="height: 42px; background: var(--card-bg); border: 1px solid var(--card-border); color: var(--text-main);" required>
                            <option value="">-- Choose Student --</option>
                            <?php foreach ($activeStudents as $st): ?>
                                <option value="<?php echo $st['id']; ?>"><?php echo htmlspecialchars($st['full_name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="input-field-group" style="margin-top: 14px;">
                        <label class="input-label">Select Subject *</label>
                        <select name="course_id" id="courseId" class="login-input" style="height: 42px; background: var(--card-bg); border: 1px solid var(--card-border); color: var(--text-main);" required>
                            <option value="">-- Choose Subject --</option>
                            <?php foreach ($allSubjects as $sub): ?>
                                <option value="<?php echo $sub['id']; ?>">
                                    <?php echo htmlspecialchars($sub['course_name'] . ' (' . $sub['grade'] . ')'); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-grid-2" style="margin-top: 14px;">
                        <div class="input-field-group">
                            <label class="input-label">Evaluation Term *</label>
                            <select name="term" id="term" class="login-input" style="height: 42px; background: var(--card-bg); border: 1px solid var(--card-border); color: var(--text-main);" required>
                                <option value="Term 1">Term 1</option>
                                <option value="Term 2">Term 2</option>
                                <option value="Term 3">Term 3</option>
                            </select>
                        </div>
                        <div class="input-field-group">
                            <label class="input-label">Marks Obtained (0 - 100) *</label>
                            <input type="number" step="0.5" min="0" max="100" name="marks_obtained" id="marksObtained" class="login-input" style="height: 42px; background: var(--card-bg); border: 1px solid var(--card-border); color: var(--text-main);" placeholder="e.g. 78.5" required>
                        </div>
                    </div>

                    <div class="input-field-group" style="margin-top: 14px;">
                        <label class="input-label">Teacher Remarks</label>
                        <input type="text" name="remarks" id="remarks" class="login-input" style="height: 42px; background: var(--card-bg); border: 1px solid var(--card-border); color: var(--text-main);" placeholder="e.g. Excellent analytical performance">
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" id="cancelModalBtn">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="modalSubmitBtn">Save Term Mark</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('markModal');
    const openAddBtn = document.getElementById('openAddModalBtn');
    const closeBtn = document.getElementById('closeModalBtn');
    const cancelBtn = document.getElementById('cancelModalBtn');
    const markForm = document.getElementById('markForm');
    const modalTitle = document.getElementById('modalTitle');
    const formAction = document.getElementById('formAction');
    const markId = document.getElementById('markId');

    function openModal() {
        modal.classList.add('active');
    }

    function closeModal() {
        modal.classList.remove('active');
    }

    if (openAddBtn) {
        openAddBtn.addEventListener('click', function() {
            markForm.reset();
            formAction.value = 'create';
            markId.value = '';
            modalTitle.textContent = 'Record Term Test Mark';
            document.getElementById('modalSubmitBtn').textContent = 'Save Term Mark';
            openModal();
        });
    }

    if (closeBtn) closeBtn.addEventListener('click', closeModal);
    if (cancelBtn) cancelBtn.addEventListener('click', closeModal);

    // Edit Mark Buttons
    document.querySelectorAll('.edit-mark-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const data = JSON.parse(this.getAttribute('data-mark'));
            formAction.value = 'update';
            markId.value = data.id;

            document.getElementById('studentId').value = data.student_id || '';
            document.getElementById('courseId').value = data.course_id || '';
            document.getElementById('term').value = data.term || 'Term 1';
            document.getElementById('marksObtained').value = data.marks_obtained || '';
            document.getElementById('remarks').value = data.remarks || '';

            modalTitle.textContent = 'Edit Term Mark Details';
            document.getElementById('modalSubmitBtn').textContent = 'Update Term Mark';
            openModal();
        });
    });
});
</script>

<?php include_once __DIR__ . '/includes/footer.php'; ?>
