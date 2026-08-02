<?php
// System settings and academic year rollover page

session_start();
if (!isset($_SESSION['user_email'])) {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . '/config/Database.php';
require_once __DIR__ . '/classes/Student.php';

$pageTitle = "System Settings & Profile";
$activeTab = isset($_GET['tab']) ? $_GET['tab'] : 'profile';

$database = new Database();
$db = $database->getConnection();

$studentService = new Student();
$currentAcademicYear = $studentService->getAcademicYear();

$userEmail = $_SESSION['user_email'] ?? 'admin@edusync.edu';
$userName = $_SESSION['user_name'] ?? 'Admin User';
$userRole = $_SESSION['user_role'] ?? 'Administrator';

$systemTheme = 'light';

$userAvatar = $_SESSION['user_avatar'] ?? null;

if ($db !== null) {
    // Ensure avatar column exists in users table
    try {
        $db->exec("ALTER TABLE users ADD COLUMN avatar LONGTEXT DEFAULT NULL");
    } catch (Exception $e) {}
    try {
        $db->exec("ALTER TABLE users MODIFY COLUMN avatar LONGTEXT DEFAULT NULL");
    } catch (Exception $e) {}

    $stmt = $db->prepare("SELECT full_name, role, avatar FROM users WHERE email = :email LIMIT 1");
    $stmt->execute([':email' => $userEmail]);
    $dbUser = $stmt->fetch();
    if ($dbUser) {
        $userName = $dbUser['full_name'];
        $userRole = $dbUser['role'];
        if (isset($dbUser['avatar'])) {
            $userAvatar = $dbUser['avatar'];
            if (!empty($userAvatar)) {
                $_SESSION['user_avatar'] = $userAvatar;
            }
        }
    }

    $stmtPref = $db->prepare("SELECT theme FROM user_preferences WHERE email = :email LIMIT 1");
    $stmtPref->execute([':email' => $userEmail]);
    $pref = $stmtPref->fetch();
    if ($pref) {
        $systemTheme = !empty($pref['theme']) ? $pref['theme'] : 'light';
    }
}

$_SESSION['system_theme'] = $systemTheme;

$successMessage = '';
$errorMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_profile'])) {
        $newName = trim(filter_input(INPUT_POST, 'full_name', FILTER_SANITIZE_SPECIAL_CHARS));
        $newPassword = $_POST['new_password'] ?? '';
        $avatarData = $userAvatar;

        // Remove profile photo
        if (isset($_POST['remove_avatar']) && $_POST['remove_avatar'] == '1') {
            $avatarData = null;
            $_SESSION['user_avatar'] = null;
            unset($_SESSION['user_avatar']);
        }

        // Upload profile photo and resize to 200x200
        if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
            $fileTmp = $_FILES['avatar']['tmp_name'];
            $fileSize = $_FILES['avatar']['size'];
            $mimeType = mime_content_type($fileTmp);
            $allowedTypes = ['image/png', 'image/jpeg', 'image/jpg', 'image/webp', 'image/gif'];

            if (!in_array($mimeType, $allowedTypes)) {
                $errorMessage = "Invalid image format. Allowed formats: PNG, JPG, WEBP, GIF.";
            } else {
                $avatarEncoded = false;

                // Resize image using GD library
                if (function_exists('imagecreatefromstring')) {
                    $rawBytes = file_get_contents($fileTmp);
                    $srcImg = @imagecreatefromstring($rawBytes);
                    if ($srcImg !== false) {
                        $origW = imagesx($srcImg);
                        $origH = imagesy($srcImg);
                        $targetSize = 200;

                        $thumb = imagecreatetruecolor($targetSize, $targetSize);
                        imagecopyresampled($thumb, $srcImg, 0, 0, 0, 0, $targetSize, $targetSize, $origW, $origH);

                        ob_start();
                        imagejpeg($thumb, null, 85);
                        $compressedBytes = ob_get_clean();

                        imagedestroy($srcImg);
                        imagedestroy($thumb);

                        if (!empty($compressedBytes)) {
                            $avatarData = 'data:image/jpeg;base64,' . base64_encode($compressedBytes);
                            $_SESSION['user_avatar'] = $avatarData;
                            $avatarEncoded = true;
                        }
                    }
                }

                // Fallback if GD library is missing
                if (!$avatarEncoded) {
                    if ($fileSize > 1 * 1024 * 1024) {
                        $errorMessage = "Image file is too large. Please select a photo under 1MB.";
                    } else {
                        $rawBytes = file_get_contents($fileTmp);
                        $avatarData = 'data:' . $mimeType . ';base64,' . base64_encode($rawBytes);
                        $_SESSION['user_avatar'] = $avatarData;
                    }
                }
            }
        }

        if (empty($errorMessage) && !empty($newName) && $db !== null) {
            try {
                if (!empty($newPassword)) {
                    $stmt = $db->prepare("UPDATE users SET full_name = :full_name, password = :password, avatar = :avatar WHERE email = :email");
                    $stmt->execute([':full_name' => $newName, ':password' => $newPassword, ':avatar' => $avatarData, ':email' => $userEmail]);
                } else {
                    $stmt = $db->prepare("UPDATE users SET full_name = :full_name, avatar = :avatar WHERE email = :email");
                    $stmt->execute([':full_name' => $newName, ':avatar' => $avatarData, ':email' => $userEmail]);
                }
                $_SESSION['user_name'] = $newName;
                $userName = $newName;
                $userAvatar = $avatarData;
                $successMessage = "Profile settings and photo saved directly to database successfully.";
            } catch (Exception $e) {
                $errorMessage = "Failed to update profile: " . $e->getMessage();
            }
        }
    } else if (isset($_POST['update_preferences'])) {
        $selectedTheme = $_POST['system_theme'] ?? 'light';
        if (!in_array($selectedTheme, ['light', 'dark'])) {
            $selectedTheme = 'light';
        }
        if ($db !== null) {
            try {
                $stmtSave = $db->prepare("INSERT INTO user_preferences (email, theme, email_notifications, auto_refresh) VALUES (:email, :theme, 1, 1) ON DUPLICATE KEY UPDATE theme = VALUES(theme)");
                $stmtSave->execute([':email' => $userEmail, ':theme' => $selectedTheme]);
                $systemTheme = $selectedTheme;
                $_SESSION['system_theme'] = $selectedTheme;
                $successMessage = "System theme updated successfully.";
            } catch (Exception $e) {
                $errorMessage = "Failed to update theme: " . $e->getMessage();
            }
        }
    } else if (isset($_POST['advance_academic_year'])) {
        $nextYear = trim($_POST['next_academic_year'] ?? '');
        if (!empty($nextYear)) {
            $res = $studentService->advanceAcademicYear($nextYear);
            if ($res) {
                $currentAcademicYear = $nextYear;
                $successMessage = "Academic year updated to {$nextYear}. Grade 12 students promoted to Grade 13.";
            } else {
                $errorMessage = "Failed to update academic year.";
            }
        }
    }
}

include_once __DIR__ . '/includes/header.php';
include_once __DIR__ . '/includes/sidebar.php';
?>

<div class="main-wrapper">
    <?php include_once __DIR__ . '/includes/navbar.php'; ?>

    <main class="content-body">
        <div class="page-header">
            <div>
                <h1 class="page-title">Settings</h1>
                <p class="page-subtitle">Manage system preferences and administrative options.</p>
            </div>
        </div>

        <?php if (!empty($successMessage)): ?>
            <div class="flash-alert" style="background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; padding: 12px 16px; border-radius: 8px; margin-bottom: 20px; font-size: 14px; font-weight: 500;">
                <?php echo htmlspecialchars($successMessage); ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($errorMessage)): ?>
            <div class="flash-alert" style="background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; padding: 12px 16px; border-radius: 8px; margin-bottom: 20px; font-size: 14px; font-weight: 500;">
                <?php echo htmlspecialchars($errorMessage); ?>
            </div>
        <?php endif; ?>

        <!-- Settings Sub-Navigation Tabs -->
        <div style="display: flex; gap: 16px; border-bottom: 1px solid var(--card-border); margin-bottom: 24px;">
            <a href="settings.php?tab=profile" style="padding: 12px 4px; font-weight: 600; font-size: 14px; text-decoration: none; border-bottom: 2px solid <?php echo ($activeTab === 'profile') ? 'var(--primary)' : 'transparent'; ?>; color: <?php echo ($activeTab === 'profile') ? 'var(--primary)' : 'var(--text-muted)'; ?>;">
                Profile Settings
            </a>
            <a href="settings.php?tab=preferences" style="padding: 12px 4px; font-weight: 600; font-size: 14px; text-decoration: none; border-bottom: 2px solid <?php echo ($activeTab === 'preferences') ? 'var(--primary)' : 'transparent'; ?>; color: <?php echo ($activeTab === 'preferences') ? 'var(--primary)' : 'var(--text-muted)'; ?>;">
                System Theme
            </a>
            <a href="settings.php?tab=academic_year" style="padding: 12px 4px; font-weight: 600; font-size: 14px; text-decoration: none; border-bottom: 2px solid <?php echo ($activeTab === 'academic_year') ? 'var(--primary)' : 'transparent'; ?>; color: <?php echo ($activeTab === 'academic_year') ? 'var(--primary)' : 'var(--text-muted)'; ?>;">
                Academic Year
            </a>
        </div>

        <?php if ($activeTab === 'profile'): ?>
            <div class="dash-card">
                <div class="dash-card-header" style="margin-bottom: 20px;">
                    <div>
                        <h2 class="dash-card-title">Profile Settings</h2>
                        <span class="dash-card-subtitle">Update administrator name, profile photo, and account credentials</span>
                    </div>
                </div>

                <form action="settings.php?tab=profile" method="POST" enctype="multipart/form-data" style="display: flex; flex-direction: column; gap: 20px; max-width: 520px;">
                    <input type="hidden" name="update_profile" value="1">
                    <input type="hidden" name="remove_avatar" id="removeAvatarInput" value="0">

                    <!-- Profile Photo Upload Field -->
                    <div class="input-field-group" style="margin-bottom: 4px;">
                        <label class="input-label" style="font-weight: 600; font-size: 13px; color: var(--text-main); margin-bottom: 8px; display: block;">Profile Picture</label>
                        
                        <div style="display: flex; align-items: center; gap: 16px;">
                            <div style="width: 64px; height: 64px; border-radius: 50%; overflow: hidden; background: #f1f5f9; border: 1px solid var(--card-border); display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 20px; color: #475569; flex-shrink: 0;">
                                <?php if (!empty($userAvatar)): ?>
                                    <img src="<?php echo htmlspecialchars($userAvatar); ?>" alt="Avatar" style="width: 100%; height: 100%; object-fit: cover;">
                                <?php else: ?>
                                    <?php 
                                        $parts = explode(' ', trim($userName));
                                        $initials = strtoupper(substr($parts[0] ?? 'A', 0, 1) . substr($parts[1] ?? 'D', 0, 1));
                                        echo htmlspecialchars($initials);
                                    ?>
                                <?php endif; ?>
                            </div>

                            <div style="display: flex; flex-direction: column; gap: 4px;">
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <input type="file" name="avatar" id="avatarFileInput" accept="image/png, image/jpeg, image/jpg, image/webp" style="display: none;" onchange="var fn = this.files[0] ? this.files[0].name : ''; document.getElementById('avatarFileName').textContent = fn;">
                                    
                                    <button type="button" class="btn btn-secondary btn-sm" onclick="document.getElementById('avatarFileInput').click();" style="padding: 7px 14px; font-size: 13px; font-weight: 600;">
                                        Upload Photo
                                    </button>
                                    
                                    <?php if (!empty($userAvatar)): ?>
                                        <button type="button" onclick="document.getElementById('removeAvatarInput').value='1'; this.form.submit();" style="background: transparent; border: none; color: #dc2626; font-size: 13px; font-weight: 500; cursor: pointer; padding: 4px 8px;">
                                            Remove
                                        </button>
                                    <?php endif; ?>
                                </div>
                                
                                <span id="avatarFileName" style="font-size: 12px; color: var(--primary); font-weight: 600;"></span>
                                <span style="font-size: 12px; color: var(--text-muted);">JPG, PNG or WEBP (Max 2MB)</span>
                            </div>
                        </div>
                    </div>

                    <div class="input-field-group">
                        <label class="input-label" style="font-weight: 600; font-size: 13px; color: var(--text-main); margin-bottom: 6px; display: block;">Full Name</label>
                        <input type="text" name="full_name" class="login-input" style="height: 42px; background: var(--card-bg); color: var(--text-main); border: 1px solid var(--card-border);" value="<?php echo htmlspecialchars($userName); ?>" required>
                    </div>

                    <div class="input-field-group">
                        <label class="input-label" style="font-weight: 600; font-size: 13px; color: var(--text-main); margin-bottom: 6px; display: block;">Email Address</label>
                        <input type="email" class="login-input" style="height: 42px; background: var(--body-bg); border: 1px solid var(--card-border); color: var(--text-muted);" value="<?php echo htmlspecialchars($userEmail); ?>" readonly disabled>
                    </div>

                    <div class="input-field-group">
                        <label class="input-label" style="font-weight: 600; font-size: 13px; color: var(--text-main); margin-bottom: 6px; display: block;">System Role</label>
                        <input type="text" class="login-input" style="height: 42px; background: var(--body-bg); border: 1px solid var(--card-border); color: var(--text-muted);" value="<?php echo htmlspecialchars($userRole); ?>" readonly disabled>
                    </div>

                    <div class="input-field-group">
                        <label class="input-label" style="font-weight: 600; font-size: 13px; color: var(--text-main); margin-bottom: 6px; display: block;">New Password</label>
                        <input type="password" name="new_password" class="login-input" style="height: 42px; background: var(--card-bg); color: var(--text-main); border: 1px solid var(--card-border);" placeholder="Leave blank to keep current password">
                    </div>

                    <div style="margin-top: 8px;">
                        <button type="submit" class="btn btn-primary" style="height: 42px; padding: 0 20px;">Save Profile</button>
                    </div>
                </form>
            </div>

        <?php elseif ($activeTab === 'preferences'): ?>
            <div class="dash-card">
                <div class="dash-card-header" style="margin-bottom: 20px;">
                    <div>
                        <h2 class="dash-card-title">System Appearance</h2>
                        <span class="dash-card-subtitle">Select display theme for the interface</span>
                    </div>
                </div>

                <form action="settings.php?tab=preferences" method="POST" style="display: flex; flex-direction: column; gap: 20px; max-width: 500px;">
                    <input type="hidden" name="update_preferences" value="1">

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                        <label style="border: 1px solid <?php echo ($systemTheme === 'light') ? 'var(--primary)' : 'var(--card-border)'; ?>; padding: 16px; border-radius: 8px; cursor: pointer; display: flex; align-items: center; justify-content: space-between;">
                            <span style="font-weight: 600; font-size: 14px; color: var(--text-main);">Light Theme</span>
                            <input type="radio" name="system_theme" value="light" <?php echo ($systemTheme === 'light') ? 'checked' : ''; ?>>
                        </label>

                        <label style="border: 1px solid <?php echo ($systemTheme === 'dark') ? 'var(--primary)' : 'var(--card-border)'; ?>; padding: 16px; border-radius: 8px; cursor: pointer; display: flex; align-items: center; justify-content: space-between;">
                            <span style="font-weight: 600; font-size: 14px; color: var(--text-main);">Dark Theme</span>
                            <input type="radio" name="system_theme" value="dark" <?php echo ($systemTheme === 'dark') ? 'checked' : ''; ?>>
                        </label>
                    </div>

                    <div>
                        <button type="submit" class="btn btn-primary" style="height: 42px; padding: 0 20px;">Apply Theme</button>
                    </div>
                </form>
            </div>

        <?php elseif ($activeTab === 'academic_year'): ?>
            <div class="dash-card">
                <div class="dash-card-header" style="margin-bottom: 20px;">
                    <div>
                        <h2 class="dash-card-title">Academic Year Management</h2>
                        <span class="dash-card-subtitle">Configure current school session and student batch promotion</span>
                    </div>
                </div>

                <form action="settings.php?tab=academic_year" method="POST" style="display: flex; flex-direction: column; gap: 18px; max-width: 480px;" onsubmit="return confirm('Promote Grade 12 students and update academic year?');">
                    <input type="hidden" name="advance_academic_year" value="1">

                    <div class="input-field-group">
                        <label class="input-label" style="font-weight: 600; font-size: 13px; color: var(--text-main); margin-bottom: 6px; display: block;">Active Academic Year</label>
                        <input type="text" class="login-input" style="height: 42px; background: var(--body-bg); border: 1px solid var(--card-border); color: var(--text-main); font-weight: 600;" value="<?php echo htmlspecialchars($currentAcademicYear); ?>" readonly disabled>
                    </div>

                    <div class="input-field-group">
                        <label class="input-label" style="font-weight: 600; font-size: 13px; color: var(--text-main); margin-bottom: 6px; display: block;">Select New Academic Year</label>
                        <select name="next_academic_year" class="filter-select" style="width: 100%; height: 42px;" required>
                            <option value="2025/2026" <?php echo ($currentAcademicYear === '2024/2025') ? 'selected' : ''; ?>>2025/2026</option>
                            <option value="2026/2027">2026/2027</option>
                            <option value="2027/2028">2027/2028</option>
                        </select>
                    </div>

                    <div style="margin-top: 8px;">
                        <button type="submit" class="btn btn-primary" style="height: 42px; padding: 0 20px;">Update Academic Year</button>
                    </div>
                </form>
            </div>
        <?php endif; ?>
    </main>

<?php include_once __DIR__ . '/includes/footer.php'; ?>
