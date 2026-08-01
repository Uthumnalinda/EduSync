<?php
/**
 * EDUsync School Management System - Subjects Directory Module
 */

require_once __DIR__ . '/classes/Subject.php';

$subjectService = new Subject();
$message = '';
$error = '';

// Handle Form Actions (Add, Edit, Delete)
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'create') {
        $validationErrors = $subjectService->validate($_POST);
        if (!empty($validationErrors)) {
            $error = implode("<br>", $validationErrors);
        } else {
            $result = $subjectService->create($_POST);
            if ($result) {
                $message = "Subject record created successfully!";
            } else {
                $error = "Failed to create subject record. Code might already exist.";
            }
        }
    } elseif ($action === 'update') {
        $id = (int)($_POST['id'] ?? 0);
        $validationErrors = $subjectService->validate($_POST, $id);
        if (!empty($validationErrors)) {
            $error = implode("<br>", $validationErrors);
        } else {
            $result = $subjectService->update($id, $_POST);
            if ($result) {
                $message = "Subject record updated successfully!";
            } else {
                $error = "Failed to update subject record.";
            }
        }
    } elseif ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        $result = $subjectService->delete($id);
        if ($result) {
            $message = "Subject record deleted successfully!";
        } else {
            $error = "Failed to delete subject record.";
        }
    }
}

// Fetch Search and Filters
$search = $_GET['search'] ?? '';
$gradeFilter = $_GET['grade'] ?? '';

$subjects = $subjectService->getAll($search, $gradeFilter);
$activeTeachers = $subjectService->getActiveTeachers();

$pageTitle = "Subjects Directory";

include_once __DIR__ . '/includes/header.php';
include_once __DIR__ . '/includes/sidebar.php';
?>

<div class="main-wrapper">
    <?php include_once __DIR__ . '/includes/navbar.php'; ?>

    <main class="content-body">
        <div class="page-header">
            <div>
                <h1 class="page-title">Subjects Directory</h1>
                <p class="page-subtitle">Manage school curriculum, subject allocations, and teacher assignments.</p>
            </div>
            <div>
                <button type="button" class="btn btn-primary" id="openAddModalBtn">+ Add New Subject</button>
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

        <!-- Table Filters & Search -->
        <div class="table-filter-bar">
            <form action="subject.php" method="GET" class="search-filter-group">
                <div style="position: relative; flex: 1;">
                    <span style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: var(--text-muted);">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                    </span>
                    <input type="text" name="search" class="filter-input" placeholder="Search subject by code, name, teacher..." value="<?php echo htmlspecialchars($search); ?>">
                </div>

                <select name="grade" class="filter-select" onchange="this.form.submit()">
                    <option value="">All Grades</option>
                    <option value="Grade 9" <?php echo $gradeFilter === 'Grade 9' ? 'selected' : ''; ?>>Grade 9</option>
                    <option value="Grade 10" <?php echo $gradeFilter === 'Grade 10' ? 'selected' : ''; ?>>Grade 10</option>
                    <option value="Grade 11" <?php echo $gradeFilter === 'Grade 11' ? 'selected' : ''; ?>>Grade 11</option>
                    <option value="Grade 12" <?php echo $gradeFilter === 'Grade 12' ? 'selected' : ''; ?>>Grade 12</option>
                    <option value="Grade 13" <?php echo $gradeFilter === 'Grade 13' ? 'selected' : ''; ?>>Grade 13</option>
                </select>

                <?php if ($search || $gradeFilter): ?>
                    <a href="subject.php" class="btn btn-secondary btn-sm" style="height: 40px; padding: 0 14px;">Clear</a>
                <?php endif; ?>
            </form>
            <div style="color: var(--text-muted); font-size: 13px; font-weight: 600;">
                Total Subjects: <?php echo count($subjects); ?>
            </div>
        </div>

        <!-- Subjects Data Table Card -->
        <div class="dash-card card-full" style="padding: 0; overflow: hidden;">
            <div style="overflow-x: auto;">
                <table class="students-table" style="width: 100%; border-collapse: collapse; font-size: 14px;">
                    <thead>
                        <tr style="background: var(--body-bg); border-bottom: 1px solid var(--card-border); text-align: left; color: var(--text-muted); font-size: 12px; font-weight: 700; text-transform: uppercase;">
                            <th style="padding: 16px 20px;">Code</th>
                            <th style="padding: 16px 20px;">Subject Name</th>
                            <th style="padding: 16px 20px;">Grade / Class</th>
                            <th style="padding: 16px 20px;">Assigned Teacher</th>
                            <th style="padding: 16px 20px;">Duration</th>
                            <th style="padding: 16px 20px; text-align: right;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($subjects)): ?>
                            <tr>
                                <td colspan="6" style="padding: 40px; text-align: center; color: var(--text-muted);">
                                    No subject records found.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($subjects as $s): ?>
                                <tr style="border-bottom: 1px solid var(--card-border);">
                                    <td style="padding: 16px 20px; font-weight: 700; color: var(--text-main); white-space: nowrap;">
                                        <?php echo htmlspecialchars($s['course_code']); ?>
                                    </td>
                                    <td style="padding: 16px 20px;">
                                        <div style="font-weight: 600; color: var(--text-main);"><?php echo htmlspecialchars($s['course_name']); ?></div>
                                        <div style="font-size: 12px; color: var(--text-muted);"><?php echo htmlspecialchars($s['description']); ?></div>
                                    </td>
                                    <td style="padding: 16px 20px; white-space: nowrap;">
                                        <span class="subject-badge">
                                            <?php echo htmlspecialchars($s['grade']); ?>
                                        </span>
                                    </td>
                                    <td style="padding: 16px 20px; font-size: 13px; color: var(--text-main);">
                                        <?php echo htmlspecialchars($s['teacher_name'] ?? 'Unassigned'); ?>
                                    </td>
                                    <td style="padding: 16px 20px; font-size: 13px; color: var(--text-muted); white-space: nowrap;">
                                        <?php echo htmlspecialchars($s['duration']); ?>
                                    </td>
                                    <td style="padding: 16px 20px; text-align: right; white-space: nowrap;">
                                        <div style="display: flex; align-items: center; justify-content: flex-end; gap: 8px;">
                                            <button type="button" class="btn btn-secondary btn-sm edit-subject-btn" 
                                                    data-subject='<?php echo json_encode($s, JSON_HEX_APOS | JSON_HEX_QUOT); ?>'>
                                                Edit
                                            </button>
                                            <form action="subject.php" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this subject?');">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="id" value="<?php echo $s['id']; ?>">
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

    <!-- Subject Modal (Add / Edit) -->
    <div class="modal-overlay" id="subjectModal">
        <div class="modal-card">
            <div class="modal-header">
                <h3 class="modal-title" id="modalTitle">Add New Subject</h3>
                <button type="button" class="modal-close-btn" id="closeModalBtn">&times;</button>
            </div>
            <form action="subject.php" method="POST" id="subjectForm">
                <input type="hidden" name="action" id="formAction" value="create">
                <input type="hidden" name="id" id="subjectId" value="">

                <div class="modal-body">
                    <div class="form-grid-2">
                        <div class="input-field-group">
                            <label class="input-label">Subject Code (Auto if blank)</label>
                            <input type="text" name="course_code" id="courseCode" class="login-input" style="height: 42px; background: var(--card-bg); border: 1px solid var(--card-border); color: var(--text-main);" placeholder="e.g. SUB006">
                        </div>
                        <div class="input-field-group">
                            <label class="input-label">Subject Name *</label>
                            <input type="text" name="course_name" id="courseName" class="login-input" style="height: 42px; background: var(--card-bg); border: 1px solid var(--card-border); color: var(--text-main);" placeholder="e.g. Mathematics" required>
                        </div>
                    </div>

                    <div class="form-grid-2" style="margin-top: 14px;">
                        <div class="input-field-group">
                            <label class="input-label">Grade / Class Level *</label>
                            <select name="grade" id="grade" class="login-input" style="height: 42px; background: var(--card-bg); border: 1px solid var(--card-border); color: var(--text-main);" required>
                                <option value="">Select Grade</option>
                                <option value="Grade 9">Grade 9</option>
                                <option value="Grade 10">Grade 10</option>
                                <option value="Grade 11">Grade 11</option>
                                <option value="Grade 12">Grade 12</option>
                                <option value="Grade 13">Grade 13</option>
                            </select>
                        </div>
                        <div class="input-field-group">
                            <label class="input-label">Assigned Teacher</label>
                            <select name="teacher_id" id="teacherId" class="login-input" style="height: 42px; background: var(--card-bg); border: 1px solid var(--card-border); color: var(--text-main);">
                                <option value="">-- Unassigned --</option>
                                <?php foreach ($activeTeachers as $t): ?>
                                    <option value="<?php echo $t['id']; ?>"><?php echo htmlspecialchars($t['full_name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-grid-2" style="margin-top: 14px;">
                        <div class="input-field-group">
                            <label class="input-label">Duration *</label>
                            <input type="text" name="duration" id="duration" class="login-input" style="height: 42px; background: var(--card-bg); border: 1px solid var(--card-border); color: var(--text-main);" value="40 weeks" required>
                        </div>
                    </div>

                    <div class="input-field-group" style="margin-top: 14px;">
                        <label class="input-label">Description / Syllabus Overview</label>
                        <textarea name="description" id="description" class="login-input" style="height: 64px; padding: 10px 14px; background: var(--card-bg); border: 1px solid var(--card-border); color: var(--text-main);" placeholder="Brief subject syllabus description..."></textarea>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" id="cancelModalBtn">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="modalSubmitBtn">Save Subject</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('subjectModal');
    const openAddBtn = document.getElementById('openAddModalBtn');
    const closeBtn = document.getElementById('closeModalBtn');
    const cancelBtn = document.getElementById('cancelModalBtn');
    const subjectForm = document.getElementById('subjectForm');
    const modalTitle = document.getElementById('modalTitle');
    const formAction = document.getElementById('formAction');
    const subjectId = document.getElementById('subjectId');

    function openModal() {
        modal.classList.add('active');
    }

    function closeModal() {
        modal.classList.remove('active');
    }

    if (openAddBtn) {
        openAddBtn.addEventListener('click', function() {
            subjectForm.reset();
            formAction.value = 'create';
            subjectId.value = '';
            modalTitle.textContent = 'Add New Subject';
            document.getElementById('modalSubmitBtn').textContent = 'Save Subject';
            openModal();
        });
    }

    if (closeBtn) closeBtn.addEventListener('click', closeModal);
    if (cancelBtn) cancelBtn.addEventListener('click', closeModal);

    // Edit Subject Buttons
    document.querySelectorAll('.edit-subject-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const data = JSON.parse(this.getAttribute('data-subject'));
            formAction.value = 'update';
            subjectId.value = data.id;

            document.getElementById('courseCode').value = data.course_code || '';
            document.getElementById('courseName').value = data.course_name || '';
            document.getElementById('grade').value = data.grade || '';
            document.getElementById('teacherId').value = data.teacher_id || '';
            document.getElementById('duration').value = data.duration || '40 weeks';
            document.getElementById('description').value = data.description || '';

            modalTitle.textContent = 'Edit Subject Details';
            document.getElementById('modalSubmitBtn').textContent = 'Update Subject';
            openModal();
        });
    });
});
</script>

<?php include_once __DIR__ . '/includes/footer.php'; ?>
