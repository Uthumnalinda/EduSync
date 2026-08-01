<?php
/**
 * EDUsync School Management System - Students Directory
 */

require_once __DIR__ . '/classes/Student.php';

$studentService = new Student();
$message = '';
$error = '';

// Handle Actions (Add, Edit, Delete)
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'create') {
        $validationErrors = $studentService->validate($_POST);
        if (!empty($validationErrors)) {
            $error = implode("<br>", $validationErrors);
        } else {
            $result = $studentService->create($_POST);
            if ($result) {
                $message = "Student record created successfully!";
            } else {
                $error = "Failed to create student record. Please try again.";
            }
        }
    } elseif ($action === 'update') {
        $id = (int)($_POST['id'] ?? 0);
        $validationErrors = $studentService->validate($_POST, $id);
        if (!empty($validationErrors)) {
            $error = implode("<br>", $validationErrors);
        } else {
            $result = $studentService->update($id, $_POST);
            if ($result) {
                $message = "Student record updated successfully!";
            } else {
                $error = "Failed to update student record.";
            }
        }
    } elseif ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        $result = $studentService->delete($id);
        if ($result) {
            $message = "Student record deleted successfully!";
        } else {
            $error = "Failed to delete student record.";
        }
    }
}

// Fetch Search and Filters
$search = $_GET['search'] ?? '';
$gradeFilter = $_GET['grade'] ?? '';
$statusFilter = $_GET['status'] ?? '';

$students = $studentService->getAll($search, $gradeFilter, $statusFilter);
$gradesList = $studentService->getGrades();

$pageTitle = "Student Management";

include_once __DIR__ . '/includes/header.php';
include_once __DIR__ . '/includes/sidebar.php';
?>

<div class="main-wrapper">
    <?php include_once __DIR__ . '/includes/navbar.php'; ?>

    <main class="content-body">
        <div class="page-header">
            <div>
                <h1 class="page-title">Student Directory</h1>
                <p class="page-subtitle">Manage student enrollments, profiles, and academic records.</p>
            </div>
            <div>
                <button type="button" class="btn btn-primary" id="openAddModalBtn">+ Add New Student</button>
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
            <form action="students.php" method="GET" class="search-filter-group">
                <div style="position: relative; flex: 1;">
                    <span style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: var(--text-muted);">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                    </span>
                    <input type="text" name="search" class="filter-input" placeholder="Search by name, code, or email..." value="<?php echo htmlspecialchars($search); ?>">
                </div>

                <select name="grade" class="filter-select" onchange="this.form.submit()">
                    <option value="">All Grades</option>
                    <?php foreach ($gradesList as $g): ?>
                        <option value="<?php echo htmlspecialchars($g); ?>" <?php echo $gradeFilter === $g ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($g); ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <select name="status" class="filter-select" onchange="this.form.submit()">
                    <option value="">All Statuses</option>
                    <option value="Active" <?php echo $statusFilter === 'Active' ? 'selected' : ''; ?>>Active</option>
                    <option value="Completed A/L" <?php echo $statusFilter === 'Completed A/L' ? 'selected' : ''; ?>>Completed A/L (School Leavers)</option>
                    <option value="Inactive" <?php echo $statusFilter === 'Inactive' ? 'selected' : ''; ?>>Inactive</option>
                </select>

                <?php if ($search || $gradeFilter || $statusFilter): ?>
                    <a href="students.php" class="btn btn-secondary btn-sm" style="height: 40px; padding: 0 14px;">Clear</a>
                <?php endif; ?>
            </form>
            <div style="color: var(--text-muted); font-size: 13px; font-weight: 600;">
                Total Students: <?php echo count($students); ?>
            </div>
        </div>

        <!-- Students Data Table Card -->
        <div class="dash-card card-full" style="padding: 0; overflow: hidden;">
            <div style="overflow-x: auto;">
                <table class="students-table" style="width: 100%; border-collapse: collapse; font-size: 14px;">
                    <thead>
                        <tr style="background: var(--body-bg); border-bottom: 1px solid var(--card-border); text-align: left; color: var(--text-muted); font-size: 12px; font-weight: 700; text-transform: uppercase;">
                            <th style="padding: 16px 20px;">Code / Adm</th>
                            <th style="padding: 16px 20px;">Student Name</th>
                            <th style="padding: 16px 20px;">Grade</th>
                            <th style="padding: 16px 20px;">Contact</th>
                            <th style="padding: 16px 20px;">Guardian Info</th>
                            <th style="padding: 16px 20px;">Status</th>
                            <th style="padding: 16px 20px; text-align: right;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($students)): ?>
                            <tr>
                                <td colspan="7" style="padding: 40px; text-align: center; color: var(--text-muted);">
                                    No student records found.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($students as $st): ?>
                                <tr style="border-bottom: 1px solid var(--card-border);">
                                    <td style="padding: 16px 20px;">
                                        <div style="font-weight: 700; color: var(--text-main);"><?php echo htmlspecialchars($st['student_code']); ?></div>
                                        <div style="font-size: 12px; color: var(--text-muted);"><?php echo htmlspecialchars($st['adm_no']); ?></div>
                                    </td>
                                    <td style="padding: 16px 20px;">
                                        <div style="font-weight: 600; color: var(--text-main);"><?php echo htmlspecialchars($st['first_name'] . ' ' . $st['last_name']); ?></div>
                                        <div style="font-size: 12px; color: var(--text-muted);"><?php echo htmlspecialchars($st['gender'] . ' • ' . $st['dob']); ?></div>
                                    </td>
                                    <td style="padding: 16px 20px; white-space: nowrap;">
                                        <span class="subject-badge">
                                            <?php echo htmlspecialchars($st['grade']); ?>
                                        </span>
                                    </td>
                                    <td style="padding: 16px 20px;">
                                        <div style="color: var(--text-main); font-size: 13px; font-weight: 500;"><?php echo htmlspecialchars($st['email']); ?></div>
                                        <div style="font-size: 12px; color: var(--text-muted); font-weight: 500;"><?php echo htmlspecialchars($st['phone']); ?></div>
                                    </td>
                                    <td style="padding: 16px 20px;">
                                        <div style="color: var(--text-main); font-size: 13px; font-weight: 500;"><?php echo htmlspecialchars($st['guardian_name']); ?></div>
                                        <div style="font-size: 12px; color: var(--text-muted); font-weight: 500;"><?php echo htmlspecialchars($st['guardian_phone']); ?></div>
                                    </td>
                                    <td style="padding: 16px 20px; white-space: nowrap;">
                                        <?php if ($st['status'] === 'Active'): ?>
                                            <span class="status-badge status-active">Active</span>
                                        <?php elseif ($st['status'] === 'Completed A/L'): ?>
                                            <span class="status-badge" style="background: #f3e8ff; color: #7e22ce; border: 1px solid #e9d5ff; font-weight: 700;">Completed A/L</span>
                                        <?php else: ?>
                                            <span class="status-badge status-inactive">Inactive</span>
                                        <?php endif; ?>
                                    </td>
                                    <td style="padding: 16px 20px; text-align: right; white-space: nowrap;">
                                        <div style="display: flex; align-items: center; justify-content: flex-end; gap: 8px;">
                                            <button type="button" class="btn btn-secondary btn-sm edit-student-btn" 
                                                    data-student='<?php echo json_encode($st, JSON_HEX_APOS | JSON_HEX_QUOT); ?>'>
                                                Edit
                                            </button>
                                            <form action="students.php" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this student?');">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="id" value="<?php echo $st['id']; ?>">
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

    <!-- Student Modal (Add / Edit) -->
    <div class="modal-overlay" id="studentModal">
        <div class="modal-card">
            <div class="modal-header">
                <h3 class="modal-title" id="modalTitle">Add New Student</h3>
                <button type="button" class="modal-close-btn" id="closeModalBtn">&times;</button>
            </div>
            <form action="students.php" method="POST" id="studentForm">
                <input type="hidden" name="action" id="formAction" value="create">
                <input type="hidden" name="id" id="studentId" value="">

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
                            <label class="input-label">Date of Birth *</label>
                            <input type="date" name="dob" id="dob" class="login-input" style="height: 42px; background: var(--card-bg); border: 1px solid var(--card-border); color: var(--text-main);" required>
                        </div>
                        <div class="input-field-group">
                            <label class="input-label">Gender *</label>
                            <select name="gender" id="gender" class="filter-select" style="width: 100%; height: 42px;" required>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-grid-2" style="margin-top: 14px;">
                        <div class="input-field-group">
                            <label class="input-label">Student Email *</label>
                            <input type="email" name="email" id="email" class="login-input" style="height: 42px; background: var(--card-bg); border: 1px solid var(--card-border); color: var(--text-main);" required>
                        </div>
                        <div class="input-field-group">
                            <label class="input-label">Phone Number (10 Digits)</label>
                            <input type="text" name="phone" id="phone" class="login-input" style="height: 42px; background: var(--card-bg); border: 1px solid var(--card-border); color: var(--text-main);" placeholder="e.g. 0771234567" pattern="[0-9]{10}" maxlength="10" title="Please enter exactly 10 digits">
                        </div>
                    </div>

                    <div class="form-grid-2" style="margin-top: 14px;">
                        <div class="input-field-group">
                            <label class="input-label">Grade / Class *</label>
                            <input type="text" name="grade" id="grade" class="login-input" placeholder="e.g. Grade 12 - Physical Science" style="height: 42px; background: var(--card-bg); border: 1px solid var(--card-border); color: var(--text-main);" required>
                        </div>
                        <div class="input-field-group">
                            <label class="input-label">Status *</label>
                            <select name="status" id="status" class="filter-select" style="width: 100%; height: 42px;" required>
                                <option value="Active">Active</option>
                                <option value="Inactive">Inactive</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-grid-2" style="margin-top: 14px;">
                        <div class="input-field-group">
                            <label class="input-label">Guardian Name *</label>
                            <input type="text" name="guardian_name" id="guardianName" class="login-input" style="height: 42px; background: var(--card-bg); border: 1px solid var(--card-border); color: var(--text-main);" required>
                        </div>
                        <div class="input-field-group">
                            <label class="input-label">Guardian Phone (10 Digits) *</label>
                            <input type="text" name="guardian_phone" id="guardianPhone" class="login-input" style="height: 42px; background: var(--card-bg); border: 1px solid var(--card-border); color: var(--text-main);" placeholder="e.g. 0771234567" pattern="[0-9]{10}" maxlength="10" title="Please enter exactly 10 digits" required>
                        </div>
                    </div>

                    <div style="margin-top: 14px;">
                        <label class="input-label">Home Address *</label>
                        <input type="text" name="address" id="address" class="login-input" style="height: 42px; background: var(--card-bg); border: 1px solid var(--card-border); color: var(--text-main);" required>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" id="cancelModalBtn">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="saveStudentBtn">Save Student</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const modal = document.getElementById('studentModal');
            const openAddBtn = document.getElementById('openAddModalBtn');
            const closeBtn = document.getElementById('closeModalBtn');
            const cancelBtn = document.getElementById('cancelModalBtn');
            const form = document.getElementById('studentForm');
            const modalTitle = document.getElementById('modalTitle');
            const formAction = document.getElementById('formAction');
            const studentId = document.getElementById('studentId');

            function openModal() {
                modal.classList.add('active');
            }

            function closeModal() {
                modal.classList.remove('active');
                form.reset();
                formAction.value = 'create';
                studentId.value = '';
                modalTitle.textContent = 'Add New Student';
            }

            if (openAddBtn) openAddBtn.addEventListener('click', openModal);
            if (closeBtn) closeBtn.addEventListener('click', closeModal);
            if (cancelBtn) cancelBtn.addEventListener('click', closeModal);

            // Edit button handler
            document.querySelectorAll('.edit-student-btn').forEach(btn => {
                btn.addEventListener('click', function () {
                    const data = JSON.parse(this.getAttribute('data-student'));
                    
                    formAction.value = 'update';
                    studentId.value = data.id;
                    modalTitle.textContent = 'Edit Student - ' + data.student_code;

                    document.getElementById('firstName').value = data.first_name;
                    document.getElementById('lastName').value = data.last_name;
                    document.getElementById('dob').value = data.dob;
                    document.getElementById('gender').value = data.gender;
                    document.getElementById('email').value = data.email;
                    document.getElementById('phone').value = data.phone;
                    document.getElementById('grade').value = data.grade;
                    document.getElementById('status').value = data.status;
                    document.getElementById('guardianName').value = data.guardian_name;
                    document.getElementById('guardianPhone').value = data.guardian_phone;
                    document.getElementById('address').value = data.address;

                    openModal();
                });
            });

            // Auto-hide alert banners after 5 seconds
            const alerts = document.querySelectorAll('.flash-alert');
            if (alerts.length > 0) {
                setTimeout(() => {
                    alerts.forEach(alert => {
                        alert.style.opacity = '0';
                        setTimeout(() => alert.remove(), 500);
                    });
                }, 5000);
            }
        });
    </script>

<?php include_once __DIR__ . '/includes/footer.php'; ?>
