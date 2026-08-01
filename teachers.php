<?php
/**
 * EDUsync School Management System - Teachers Directory
 */

require_once __DIR__ . '/classes/Teacher.php';

$teacherService = new Teacher();
$message = '';
$error = '';

// Handle Actions (Add, Edit, Delete)
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'create') {
        $validationErrors = $teacherService->validate($_POST);
        if (!empty($validationErrors)) {
            $error = implode("<br>", $validationErrors);
        } else {
            $result = $teacherService->create($_POST);
            if ($result) {
                $message = "Teacher record created successfully!";
            } else {
                $error = "Failed to create teacher record. Please try again.";
            }
        }
    } elseif ($action === 'update') {
        $id = (int)($_POST['id'] ?? 0);
        $validationErrors = $teacherService->validate($_POST, $id);
        if (!empty($validationErrors)) {
            $error = implode("<br>", $validationErrors);
        } else {
            $result = $teacherService->update($id, $_POST);
            if ($result) {
                $message = "Teacher record updated successfully!";
            } else {
                $error = "Failed to update teacher record.";
            }
        }
    } elseif ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        $result = $teacherService->delete($id);
        if ($result) {
            $message = "Teacher record deleted successfully!";
        } else {
            $error = "Failed to delete teacher record.";
        }
    }
}

// Fetch Search and Filters
$search = $_GET['search'] ?? '';
$subjectFilter = $_GET['subject'] ?? '';
$statusFilter = $_GET['status'] ?? '';

$teachers = $teacherService->getAll($search, $subjectFilter, $statusFilter);
$subjectsList = $teacherService->getSubjectsList();

$pageTitle = "Teachers Directory";

include_once __DIR__ . '/includes/header.php';
include_once __DIR__ . '/includes/sidebar.php';
?>

<div class="main-wrapper">
    <?php include_once __DIR__ . '/includes/navbar.php'; ?>

    <main class="content-body">
        <div class="page-header">
            <div>
                <h1 class="page-title">Teachers Directory</h1>
                <p class="page-subtitle">Manage teaching staff records, subject allocations, and contact details.</p>
            </div>
            <div>
                <button type="button" class="btn btn-primary" id="openAddModalBtn">+ Add New Teacher</button>
            </div>
        </div>

        <?php if (!empty($message)): ?>
            <div class="flash-alert" style="background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; padding: 12px 16px; border-radius: 12px; margin-bottom: 20px; font-size: 14px; font-weight: 500; transition: opacity 0.5s ease;">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($error)): ?>
            <div class="flash-alert" style="background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; padding: 12px 16px; border-radius: 12px; margin-bottom: 20px; font-size: 14px; font-weight: 500; transition: opacity 0.5s ease;">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <!-- Table Filters & Search -->
        <div class="table-filter-bar">
            <form action="teachers.php" method="GET" class="search-filter-group">
                <div style="position: relative; flex: 1;">
                    <span style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: var(--text-muted);">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                    </span>
                    <input type="text" name="search" class="filter-input" placeholder="Search teacher by name, code, NIC, subject..." value="<?php echo htmlspecialchars($search); ?>">
                </div>

                <select name="subject" class="filter-select" onchange="this.form.submit()">
                    <option value="">All Subjects</option>
                    <?php foreach ($subjectsList as $subj): ?>
                        <option value="<?php echo htmlspecialchars($subj); ?>" <?php echo $subjectFilter === $subj ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($subj); ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <select name="status" class="filter-select" onchange="this.form.submit()">
                    <option value="">All Statuses</option>
                    <option value="Active" <?php echo $statusFilter === 'Active' ? 'selected' : ''; ?>>Active</option>
                    <option value="Inactive" <?php echo $statusFilter === 'Inactive' ? 'selected' : ''; ?>>Inactive</option>
                </select>

                <?php if ($search || $subjectFilter || $statusFilter): ?>
                    <a href="teachers.php" class="btn btn-secondary btn-sm" style="height: 40px; padding: 0 14px;">Clear</a>
                <?php endif; ?>
            </form>
            <div style="color: var(--text-muted); font-size: 13px; font-weight: 600;">
                Total Teachers: <?php echo count($teachers); ?>
            </div>
        </div>

        <!-- Teachers Data Table Card -->
        <div class="dash-card card-full" style="padding: 0; overflow: hidden;">
            <div style="overflow-x: auto;">
                <table class="students-table" style="width: 100%; border-collapse: collapse; font-size: 14px;">
                    <thead>
                        <tr style="background: var(--body-bg); border-bottom: 1px solid var(--card-border); text-align: left; color: var(--text-muted); font-size: 12px; font-weight: 700; text-transform: uppercase;">
                            <th style="padding: 10px 10px; white-space: nowrap;">Code / NIC</th>
                            <th style="padding: 10px 10px; white-space: nowrap;">Teacher Name</th>
                            <th style="padding: 10px 10px; white-space: nowrap;">Subject</th>
                            <th style="padding: 10px 10px; white-space: nowrap;">Contact</th>
                            <th style="padding: 10px 10px; white-space: nowrap;">Date Joined</th>
                            <th style="padding: 10px 10px; white-space: nowrap;">Salary</th>
                            <th style="padding: 10px 10px; white-space: nowrap;">Status</th>
                            <th style="padding: 10px 10px; text-align: right; white-space: nowrap;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($teachers)): ?>
                            <tr>
                                <td colspan="8" style="padding: 40px; text-align: center; color: var(--text-muted);">
                                    No teacher records found.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($teachers as $t): ?>
                                <tr style="border-bottom: 1px solid var(--card-border);">
                                    <td style="padding: 10px 10px; white-space: nowrap;">
                                        <div style="font-weight: 700; color: var(--text-main);"><?php echo htmlspecialchars($t['teacher_code']); ?></div>
                                        <div style="font-size: 12px; color: var(--text-muted);"><?php echo htmlspecialchars($t['nic']); ?></div>
                                    </td>
                                    <td style="padding: 10px 10px; white-space: nowrap;">
                                        <div style="font-weight: 600; color: var(--text-main);"><?php echo htmlspecialchars($t['first_name'] . ' ' . $t['last_name']); ?></div>
                                        <div style="font-size: 12px; color: var(--text-muted);"><?php echo htmlspecialchars($t['qualification']); ?></div>
                                    </td>
                                    <td style="padding: 10px 10px; white-space: nowrap;">
                                        <span class="subject-badge" title="<?php echo htmlspecialchars($t['subject']); ?>">
                                            <?php echo htmlspecialchars($t['subject']); ?>
                                        </span>
                                    </td>
                                    <td style="padding: 10px 10px; white-space: nowrap;">
                                        <div style="color: var(--text-main); font-size: 13px; font-weight: 500;"><?php echo htmlspecialchars($t['email']); ?></div>
                                        <div style="font-size: 12px; color: var(--text-muted); font-weight: 500;"><?php echo htmlspecialchars($t['phone']); ?></div>
                                    </td>
                                    <td style="padding: 10px 10px; font-size: 13px; color: var(--text-muted); white-space: nowrap;">
                                        <?php echo date('M d, Y', strtotime($t['date_joined'])); ?>
                                    </td>
                                    <td style="padding: 10px 10px; font-weight: 700; color: var(--text-main); white-space: nowrap;">
                                        Rs. <?php echo number_format($t['salary'], 2); ?>
                                    </td>
                                    <td style="padding: 10px 10px; white-space: nowrap;">
                                        <?php if ($t['status'] === 'Active'): ?>
                                            <span class="status-badge status-active">Active</span>
                                        <?php else: ?>
                                            <span class="status-badge status-inactive">Inactive</span>
                                        <?php endif; ?>
                                    </td>
                                    <td style="padding: 10px 10px; text-align: right; white-space: nowrap;">
                                        <div style="display: flex; align-items: center; justify-content: flex-end; gap: 6px;">
                                            <button type="button" class="btn btn-secondary btn-sm edit-teacher-btn" 
                                                    data-teacher='<?php echo json_encode($t, JSON_HEX_APOS | JSON_HEX_QUOT); ?>'>
                                                Edit
                                            </button>
                                            <form action="teachers.php" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this teacher?');">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="id" value="<?php echo $t['id']; ?>">
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

    <!-- Teacher Modal (Add / Edit) -->
    <div class="modal-overlay" id="teacherModal">
        <div class="modal-card">
            <div class="modal-header">
                <h3 class="modal-title" id="modalTitle">Add New Teacher</h3>
                <button type="button" class="modal-close-btn" id="closeModalBtn">&times;</button>
            </div>
            <form action="teachers.php" method="POST" id="teacherForm">
                <input type="hidden" name="action" id="formAction" value="create">
                <input type="hidden" name="id" id="teacherId" value="">

                <div class="modal-body">
                    <div class="form-grid-2">
                        <div class="input-field-group">
                            <label class="input-label">First Name *</label>
                            <input type="text" name="first_name" id="firstName" class="login-input" style="height: 42px; background: var(--card-bg); border: 1px solid var(--card-border); color: var(--text-main);" required>
                        </div>
                        <div class="input-field-group">
                            <label class="input-label">Last Name *</label>
                            <input type="text" name="last_name" id="lastName" class="login-input" style="height: 42px; background: var(--card-bg); border: 1px solid var(--card-border); color: var(--text-main);" required>
                        </div>
                    </div>

                    <div class="form-grid-2" style="margin-top: 14px;">
                        <div class="input-field-group">
                            <label class="input-label">NIC / National ID *</label>
                            <input type="text" name="nic" id="nic" class="login-input" style="height: 42px; background: var(--card-bg); border: 1px solid var(--card-border); color: var(--text-main);" placeholder="e.g. 198514209876" required>
                        </div>
                        <div class="input-field-group">
                            <label class="input-label">Teacher Code (Auto generated if blank)</label>
                            <input type="text" name="teacher_code" id="teacherCode" class="login-input" style="height: 42px; background: var(--card-bg); border: 1px solid var(--card-border); color: var(--text-main);" placeholder="e.g. T006">
                        </div>
                    </div>

                    <div class="form-grid-2" style="margin-top: 14px;">
                        <div class="input-field-group">
                            <label class="input-label">Subject / Specialty *</label>
                            <input type="text" name="subject" id="subject" class="login-input" style="height: 42px; background: var(--card-bg); border: 1px solid var(--card-border); color: var(--text-main);" placeholder="e.g. Mathematics" required>
                        </div>
                        <div class="input-field-group">
                            <label class="input-label">Qualification *</label>
                            <input type="text" name="qualification" id="qualification" class="login-input" style="height: 42px; background: var(--card-bg); border: 1px solid var(--card-border); color: var(--text-main);" placeholder="e.g. PhD Mathematics" required>
                        </div>
                    </div>

                    <div class="form-grid-2" style="margin-top: 14px;">
                        <div class="input-field-group">
                            <label class="input-label">Email Address *</label>
                            <input type="email" name="email" id="email" class="login-input" style="height: 42px; background: var(--card-bg); border: 1px solid var(--card-border); color: var(--text-main);" required>
                        </div>
                        <div class="input-field-group">
                            <label class="input-label">Phone Number (10 Digits) *</label>
                            <input type="text" name="phone" id="phone" class="login-input" style="height: 42px; background: var(--card-bg); border: 1px solid var(--card-border); color: var(--text-main);" placeholder="e.g. 0771234567" pattern="[0-9]{10}" maxlength="10" title="Please enter exactly 10 digits" required>
                        </div>
                    </div>

                    <div class="form-grid-2" style="margin-top: 14px;">
                        <div class="input-field-group">
                            <label class="input-label">Date Joined *</label>
                            <input type="date" name="date_joined" id="dateJoined" class="login-input" style="height: 42px; background: var(--card-bg); border: 1px solid var(--card-border); color: var(--text-main);" value="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                        <div class="input-field-group">
                            <label class="input-label">Monthly Salary (Rs.) *</label>
                            <input type="number" min="0" step="0.01" name="salary" id="salary" class="login-input" style="height: 42px; background: var(--card-bg); border: 1px solid var(--card-border); color: var(--text-main);" placeholder="e.g. 45000.00" required>
                        </div>
                    </div>

                    <div class="input-field-group" style="margin-top: 14px;">
                        <label class="input-label">Status *</label>
                        <select name="status" id="status" class="login-input" style="height: 42px; background: var(--card-bg); border: 1px solid var(--card-border); color: var(--text-main);">
                            <option value="Active">Active</option>
                            <option value="Inactive">Inactive</option>
                        </select>
                    </div>

                    <div class="input-field-group" style="margin-top: 14px;">
                        <label class="input-label">Residential Address *</label>
                        <textarea name="address" id="address" class="login-input" style="height: 64px; padding: 10px 14px; background: var(--card-bg); border: 1px solid var(--card-border); color: var(--text-main);" required></textarea>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" id="cancelModalBtn">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="modalSubmitBtn">Save Teacher</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('teacherModal');
    const openAddBtn = document.getElementById('openAddModalBtn');
    const closeBtn = document.getElementById('closeModalBtn');
    const cancelBtn = document.getElementById('cancelModalBtn');
    const teacherForm = document.getElementById('teacherForm');
    const modalTitle = document.getElementById('modalTitle');
    const formAction = document.getElementById('formAction');
    const teacherId = document.getElementById('teacherId');

    function openModal() {
        modal.classList.add('active');
    }

    function closeModal() {
        modal.classList.remove('active');
    }

    if (openAddBtn) {
        openAddBtn.addEventListener('click', function() {
            teacherForm.reset();
            formAction.value = 'create';
            teacherId.value = '';
            modalTitle.textContent = 'Add New Teacher';
            document.getElementById('modalSubmitBtn').textContent = 'Save Teacher';
            openModal();
        });
    }

    if (closeBtn) closeBtn.addEventListener('click', closeModal);
    if (cancelBtn) cancelBtn.addEventListener('click', closeModal);

    // Edit Teacher Buttons
    document.querySelectorAll('.edit-teacher-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const data = JSON.parse(this.getAttribute('data-teacher'));
            formAction.value = 'update';
            teacherId.value = data.id;

            document.getElementById('firstName').value = data.first_name || '';
            document.getElementById('lastName').value = data.last_name || '';
            document.getElementById('nic').value = data.nic || '';
            document.getElementById('teacherCode').value = data.teacher_code || '';
            document.getElementById('subject').value = data.subject || '';
            document.getElementById('qualification').value = data.qualification || '';
            document.getElementById('email').value = data.email || '';
            document.getElementById('phone').value = data.phone || '';
            document.getElementById('dateJoined').value = data.date_joined || '';
            document.getElementById('salary').value = data.salary || '';
            document.getElementById('status').value = data.status || 'Active';
            document.getElementById('address').value = data.address || '';

            modalTitle.textContent = 'Edit Teacher Details';
            document.getElementById('modalSubmitBtn').textContent = 'Update Teacher';
            openModal();
        });
    });
});
</script>

<?php include_once __DIR__ . '/includes/footer.php'; ?>
