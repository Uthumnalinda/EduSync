<?php
/**
 * EDUsync School Management System - Class & Stream Allocation Module
 */

require_once __DIR__ . '/classes/Enrollment.php';

$enrollmentService = new Enrollment();
$message = '';
$error = '';

// Handle Actions (Add, Edit, Delete)
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'create') {
        if (empty($_POST['student_id']) || empty($_POST['course_id'])) {
            $error = "Please select both a student and a target class section.";
        } else {
            $result = $enrollmentService->create($_POST);
            if ($result) {
                $message = "Student allocated to class section successfully!";
            } else {
                $error = "Failed to allocate student. Student may already be assigned to this class.";
            }
        }
    } elseif ($action === 'update') {
        $id = (int)($_POST['id'] ?? 0);
        $result = $enrollmentService->update($id, $_POST);
        if ($result) {
            $message = "Class allocation updated successfully!";
        } else {
            $error = "Failed to update class allocation.";
        }
    } elseif ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        $result = $enrollmentService->delete($id);
        if ($result) {
            $message = "Student allocation removed successfully!";
        } else {
            $error = "Failed to remove allocation.";
        }
    }
}

// Fetch Search & Filters
$search = $_GET['search'] ?? '';
$streamFilter = $_GET['stream'] ?? '';
$classFilter = $_GET['class_name'] ?? '';

$allocations = $enrollmentService->getAll($search, $streamFilter, $classFilter);
$activeStudents = $enrollmentService->getActiveStudents();
$classSections = $enrollmentService->getClassSections();

$pageTitle = "Class & Stream Allocation";

include_once __DIR__ . '/includes/header.php';
include_once __DIR__ . '/includes/sidebar.php';
?>

<div class="main-wrapper">
    <?php include_once __DIR__ . '/includes/navbar.php'; ?>

    <main class="content-body">
        <div class="page-header">
            <div>
                <h1 class="page-title">Class & Stream Allocation</h1>
                <p class="page-subtitle">Assign students to class sections (10-A, 11-B) and A/L Academic Streams (Bio, Maths, Commerce, Arts, Tech).</p>
            </div>
            <div>
                <button type="button" class="btn btn-primary" id="openAddModalBtn">+ Allocate Student to Class</button>
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
            <form action="enrollments.php" method="GET" class="search-filter-group">
                <div style="position: relative; flex: 1;">
                    <span style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: var(--text-muted);">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                    </span>
                    <input type="text" name="search" class="filter-input" placeholder="Search by student name, code, admission no, class..." value="<?php echo htmlspecialchars($search); ?>">
                </div>

                <?php if ($search): ?>
                    <a href="enrollments.php" class="btn btn-secondary btn-sm" style="height: 40px; padding: 0 14px;">Clear</a>
                <?php endif; ?>
            </form>
            <div style="color: var(--text-muted); font-size: 13px; font-weight: 600;">
                Total Allocated Students: <?php echo count($allocations); ?>
            </div>
        </div>

        <!-- Class Allocation Data Table Card -->
        <div class="dash-card card-full" style="padding: 0; overflow: hidden;">
            <div style="overflow-x: auto;">
                <table class="students-table" style="width: 100%; border-collapse: collapse; font-size: 14px;">
                    <thead>
                        <tr style="background: var(--body-bg); border-bottom: 1px solid var(--card-border); text-align: left; color: var(--text-muted); font-size: 12px; font-weight: 700; text-transform: uppercase;">
                            <th style="padding: 12px 14px; white-space: nowrap;">Code / Adm</th>
                            <th style="padding: 12px 14px; white-space: nowrap;">Student Name</th>
                            <th style="padding: 12px 14px; white-space: nowrap;">Assigned Subject / Class</th>
                            <th style="padding: 12px 14px; white-space: nowrap;">Grade Level</th>
                            <th style="padding: 12px 14px; white-space: nowrap;">Class Teacher</th>
                            <th style="padding: 12px 14px; white-space: nowrap;">Allocation Date</th>
                            <th style="padding: 12px 14px; white-space: nowrap;">Status</th>
                            <th style="padding: 12px 14px; text-align: right; white-space: nowrap;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($allocations)): ?>
                            <tr>
                                <td colspan="8" style="padding: 40px; text-align: center; color: var(--text-muted);">
                                    No student class allocations found.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($allocations as $a): ?>
                                <tr style="border-bottom: 1px solid var(--card-border);">
                                    <td style="padding: 12px 14px; white-space: nowrap;">
                                        <div style="font-weight: 700; color: var(--text-main);"><?php echo htmlspecialchars($a['student_code']); ?></div>
                                        <div style="font-size: 12px; color: var(--text-muted);"><?php echo htmlspecialchars($a['adm_no']); ?></div>
                                    </td>
                                    <td style="padding: 12px 14px; white-space: nowrap;">
                                        <div style="font-weight: 600; color: var(--text-main);"><?php echo htmlspecialchars($a['student_name']); ?></div>
                                        <div style="font-size: 12px; color: var(--text-muted);"><?php echo htmlspecialchars($a['email']); ?></div>
                                    </td>
                                    <td style="padding: 12px 14px; white-space: nowrap;">
                                        <span class="subject-badge">
                                            <?php echo htmlspecialchars($a['class_section']); ?>
                                        </span>
                                    </td>
                                    <td style="padding: 12px 14px; font-weight: 600; color: var(--text-main); white-space: nowrap;">
                                        <?php echo htmlspecialchars($a['grade']); ?>
                                    </td>
                                    <td style="padding: 12px 14px; font-size: 13px; color: var(--text-main); white-space: nowrap;">
                                        <?php echo htmlspecialchars($a['class_teacher'] ?? 'Unassigned'); ?>
                                    </td>
                                    <td style="padding: 12px 14px; font-size: 13px; color: var(--text-muted); white-space: nowrap;">
                                        <?php echo date('M d, Y', strtotime($a['enrollment_date'])); ?>
                                    </td>
                                    <td style="padding: 12px 14px; white-space: nowrap;">
                                        <?php if ($a['status'] === 'Enrolled'): ?>
                                            <span class="status-badge status-active">Active</span>
                                        <?php else: ?>
                                            <span class="status-badge status-inactive"><?php echo htmlspecialchars($a['status']); ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td style="padding: 12px 14px; text-align: right; white-space: nowrap;">
                                        <div style="display: flex; align-items: center; justify-content: flex-end; gap: 6px;">
                                            <button type="button" class="btn btn-secondary btn-sm edit-alloc-btn" 
                                                    data-alloc='<?php echo json_encode($a, JSON_HEX_APOS | JSON_HEX_QUOT); ?>'>
                                                Edit
                                            </button>
                                            <form action="enrollments.php" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to remove this class allocation?');">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="id" value="<?php echo $a['id']; ?>">
                                                <button type="submit" class="btn btn-danger btn-sm">Remove</button>
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

    <!-- Allocation Modal (Add / Edit) -->
    <div class="modal-overlay" id="allocModal">
        <div class="modal-card">
            <div class="modal-header">
                <h3 class="modal-title" id="modalTitle">Allocate Student to Class</h3>
                <button type="button" class="modal-close-btn" id="closeModalBtn">&times;</button>
            </div>
            <form action="enrollments.php" method="POST" id="allocForm">
                <input type="hidden" name="action" id="formAction" value="create">
                <input type="hidden" name="id" id="allocId" value="">

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
                        <label class="input-label">Target Class Section / Subject *</label>
                        <select name="course_id" id="courseId" class="login-input" style="height: 42px; background: var(--card-bg); border: 1px solid var(--card-border); color: var(--text-main);" required>
                            <option value="">-- Choose Class Section --</option>
                            <?php foreach ($classSections as $cs): ?>
                                <option value="<?php echo $cs['id']; ?>">
                                    <?php echo htmlspecialchars($cs['course_name'] . ' (' . $cs['grade'] . ') - Teacher: ' . ($cs['teacher_name'] ?? 'Unassigned')); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-grid-2" style="margin-top: 14px;">
                        <div class="input-field-group">
                            <label class="input-label">Allocation Date *</label>
                            <input type="date" name="enrollment_date" id="enrollmentDate" class="login-input" style="height: 42px; background: var(--card-bg); border: 1px solid var(--card-border); color: var(--text-main);" value="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                        <div class="input-field-group">
                            <label class="input-label">Status *</label>
                            <select name="status" id="status" class="login-input" style="height: 42px; background: var(--card-bg); border: 1px solid var(--card-border); color: var(--text-main);" required>
                                <option value="Enrolled">Active</option>
                                <option value="Completed">Completed</option>
                                <option value="Dropped">Dropped</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" id="cancelModalBtn">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="modalSubmitBtn">Save Allocation</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('allocModal');
    const openAddBtn = document.getElementById('openAddModalBtn');
    const closeBtn = document.getElementById('closeModalBtn');
    const cancelBtn = document.getElementById('cancelModalBtn');
    const allocForm = document.getElementById('allocForm');
    const modalTitle = document.getElementById('modalTitle');
    const formAction = document.getElementById('formAction');
    const allocId = document.getElementById('allocId');

    function openModal() {
        modal.classList.add('active');
    }

    function closeModal() {
        modal.classList.remove('active');
    }

    if (openAddBtn) {
        openAddBtn.addEventListener('click', function() {
            allocForm.reset();
            formAction.value = 'create';
            allocId.value = '';
            modalTitle.textContent = 'Allocate Student to Class';
            document.getElementById('modalSubmitBtn').textContent = 'Save Allocation';
            openModal();
        });
    }

    if (closeBtn) closeBtn.addEventListener('click', closeModal);
    if (cancelBtn) cancelBtn.addEventListener('click', closeModal);

    // Edit Allocation Buttons
    document.querySelectorAll('.edit-alloc-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const data = JSON.parse(this.getAttribute('data-alloc'));
            formAction.value = 'update';
            allocId.value = data.id;

            document.getElementById('studentId').value = data.student_id || '';
            document.getElementById('courseId').value = data.course_id || '';
            document.getElementById('enrollmentDate').value = data.enrollment_date || '';
            document.getElementById('status').value = data.status || 'Enrolled';

            modalTitle.textContent = 'Change Student Class';
            document.getElementById('modalSubmitBtn').textContent = 'Update Allocation';
            openModal();
        });
    });
});
</script>

<?php include_once __DIR__ . '/includes/footer.php'; ?>
