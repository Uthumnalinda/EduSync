<?php
/**
 * EDUsync School Management System - Settings & Profile Page
 * 100% Integrated with MySQL Database (users & user_preferences tables)
 */

session_start();
if (!isset($_SESSION['user_email'])) {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . '/config/Database.php';

$pageTitle = "System Settings & Profile";
$activeTab = isset($_GET['tab']) ? $_GET['tab'] : 'profile';

$database = new Database();
$db = $database->getConnection();

$userEmail = $_SESSION['user_email'] ?? 'admin@edusync.edu';
$userName = $_SESSION['user_name'] ?? 'Admin User';
$userRole = $_SESSION['user_role'] ?? 'Administrator';

// Default Preferences & Theme
$systemTheme = 'light';
$emailNotifications = 1;
$autoRefresh = 1;

if ($db !== null) {
    // 1. Fetch live user details
    $stmt = $db->prepare("SELECT full_name, role FROM users WHERE email = :email LIMIT 1");
    $stmt->execute([':email' => $userEmail]);
    $dbUser = $stmt->fetch();
    if ($dbUser) {
        $userName = $dbUser['full_name'];
        $userRole = $dbUser['role'];
        $_SESSION['user_name'] = $userName;
        $_SESSION['user_role'] = $userRole;
    }

    // 2. Fetch live theme & preferences from `user_preferences` table
    $stmtPref = $db->prepare("SELECT theme, email_notifications, auto_refresh FROM user_preferences WHERE email = :email LIMIT 1");
    $stmtPref->execute([':email' => $userEmail]);
    $pref = $stmtPref->fetch();
    if ($pref) {
        $systemTheme = !empty($pref['theme']) ? $pref['theme'] : 'light';
        $emailNotifications = (int)$pref['email_notifications'];
        $autoRefresh = (int)$pref['auto_refresh'];
    } else {
        $initPref = $db->prepare("INSERT INTO user_preferences (email, theme, email_notifications, auto_refresh) VALUES (:email, 'light', 1, 1)");
        $initPref->execute([':email' => $userEmail]);
    }
}

$_SESSION['system_theme'] = $systemTheme;

$successMessage = '';
$errorMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_profile'])) {
        $newName = trim(filter_input(INPUT_POST, 'full_name', FILTER_SANITIZE_SPECIAL_CHARS));
        $newPassword = $_POST['new_password'] ?? '';
        
        if (!empty($newName)) {
            if ($db !== null) {
                try {
                    if (!empty($newPassword)) {
                        $stmt = $db->prepare("UPDATE users SET full_name = :full_name, password = :password WHERE email = :email");
                        $stmt->execute([
                            ':full_name' => $newName,
                            ':password' => $newPassword,
                            ':email' => $userEmail
                        ]);
                    } else {
                        $stmt = $db->prepare("UPDATE users SET full_name = :full_name WHERE email = :email");
                        $stmt->execute([
                            ':full_name' => $newName,
                            ':email' => $userEmail
                        ]);
                    }

                    $_SESSION['user_name'] = $newName;
                    $userName = $newName;
                    $successMessage = "Profile updated successfully!";
                } catch (Exception $e) {
                    $errorMessage = "Database update error: " . $e->getMessage();
                }
            }
        }
    } else if (isset($_POST['update_preferences'])) {
        $selectedTheme = $_POST['system_theme'] ?? 'light';
        if (!in_array($selectedTheme, ['light', 'dark'])) {
            $selectedTheme = 'light';
        }

        if ($db !== null) {
            try {
                $stmtSave = $db->prepare("
                    INSERT INTO user_preferences (email, theme, email_notifications, auto_refresh) 
                    VALUES (:email, :theme, 1, 1) 
                    ON DUPLICATE KEY UPDATE theme = VALUES(theme)
                ");
                $stmtSave->execute([
                    ':email' => $userEmail,
                    ':theme' => $selectedTheme
                ]);

                $systemTheme = $selectedTheme;
                $_SESSION['system_theme'] = $selectedTheme;
                $successMessage = "System theme updated successfully!";
            } catch (Exception $e) {
                $errorMessage = "Failed to save theme: " . $e->getMessage();
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
                <h1 class="page-title">Settings & Preferences</h1>
                <p class="page-subtitle">Manage your account profile and dashboard system theme.</p>
            </div>
        </div>

        <?php if (!empty($successMessage)): ?>
            <div style="background: #ecfdf5; color: #047857; border: 1px solid #a7f3d0; padding: 12px 16px; border-radius: 8px; font-size: 14px; margin-bottom: 20px; font-weight: 500;">
                <?php echo htmlspecialchars($successMessage); ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($errorMessage)): ?>
            <div style="background: #fef2f2; color: #ef4444; border: 1px solid #fca5a5; padding: 12px 16px; border-radius: 8px; font-size: 14px; margin-bottom: 20px; font-weight: 500;">
                <?php echo htmlspecialchars($errorMessage); ?>
            </div>
        <?php endif; ?>

        <!-- Tabs Header -->
        <div style="display: flex; gap: 12px; margin-bottom: 24px; border-bottom: 1px solid var(--card-border); padding-bottom: 12px;">
            <a href="settings.php?tab=profile" style="display: flex; align-items: center; gap: 8px; padding: 10px 18px; border-radius: 8px; text-decoration: none; font-size: 14px; font-weight: 600; transition: all 0.2s; <?php echo ($activeTab === 'profile') ? 'background: var(--primary); color: #ffffff;' : 'background: var(--card-bg); color: var(--text-muted); border: 1px solid var(--card-border);'; ?>">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                My Profile
            </a>
            <a href="settings.php?tab=preferences" style="display: flex; align-items: center; gap: 8px; padding: 10px 18px; border-radius: 8px; text-decoration: none; font-size: 14px; font-weight: 600; transition: all 0.2s; <?php echo ($activeTab === 'preferences') ? 'background: var(--primary); color: #ffffff;' : 'background: var(--card-bg); color: var(--text-muted); border: 1px solid var(--card-border);'; ?>">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
                System Preferences
            </a>
        </div>

        <?php if ($activeTab === 'profile'): ?>
            <!-- My Profile Tab Content -->
            <div class="dash-card">
                <div class="dash-card-header" style="margin-bottom: 24px;">
                    <div>
                        <h2 class="dash-card-title">Account Settings & Info</h2>
                        <span class="dash-card-subtitle">Update your personal account information</span>
                    </div>
                </div>

                <form action="settings.php?tab=profile" method="POST" style="display: flex; flex-direction: column; gap: 20px; max-width: 600px;">
                    <input type="hidden" name="update_profile" value="1">
                    
                    <div class="input-field-group">
                        <label class="input-label" style="font-weight: 600; font-size: 13px; color: var(--text-main); margin-bottom: 6px; display: block;">Full Name</label>
                        <input type="text" name="full_name" class="login-input" style="background: var(--body-bg); color: var(--text-main); border: 1px solid var(--card-border);" value="<?php echo htmlspecialchars($userName); ?>" required>
                    </div>

                    <div class="input-field-group">
                        <label class="input-label" style="font-weight: 600; font-size: 13px; color: var(--text-main); margin-bottom: 6px; display: block;">Email Address</label>
                        <input type="email" class="login-input" style="background: var(--body-bg); border: 1px solid var(--card-border); color: var(--text-muted);" value="<?php echo htmlspecialchars($userEmail); ?>" readonly disabled>
                    </div>

                    <div class="input-field-group">
                        <label class="input-label" style="font-weight: 600; font-size: 13px; color: var(--text-main); margin-bottom: 6px; display: block;">System Role</label>
                        <input type="text" class="login-input" style="background: var(--body-bg); border: 1px solid var(--card-border); color: var(--text-muted);" value="<?php echo htmlspecialchars($userRole); ?>" readonly disabled>
                    </div>

                    <div class="input-field-group">
                        <label class="input-label" style="font-weight: 600; font-size: 13px; color: var(--text-main); margin-bottom: 6px; display: block;">New Password (Optional)</label>
                        <input type="password" name="new_password" class="login-input" style="background: var(--body-bg); color: var(--text-main); border: 1px solid var(--card-border);" placeholder="Leave blank to keep current password">
                    </div>

                    <div>
                        <button type="submit" class="btn btn-primary" style="padding: 10px 24px; font-weight: 600;">Save Changes</button>
                    </div>
                </form>
            </div>
        <?php else: ?>
            <!-- System Preferences (Theme Chooser) Tab Content -->
            <div class="dash-card">
                <div class="dash-card-header" style="margin-bottom: 24px;">
                    <div>
                        <h2 class="dash-card-title">System Theme</h2>
                        <span class="dash-card-subtitle">Choose your preferred visual appearance for the school management system</span>
                    </div>
                </div>

                <form action="settings.php?tab=preferences" method="POST" style="display: flex; flex-direction: column; gap: 24px; max-width: 600px;">
                    <input type="hidden" name="update_preferences" value="1">

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 18px;">
                        <!-- Light Theme Option -->
                        <label style="border: 2px solid <?php echo ($systemTheme === 'light') ? 'var(--primary)' : 'var(--card-border)'; ?>; background: #ffffff; padding: 20px; border-radius: 12px; cursor: pointer; display: flex; flex-direction: column; gap: 12px; transition: all 0.2s;">
                            <div style="display: flex; align-items: center; justify-content: space-between;">
                                <div style="display: flex; align-items: center; gap: 10px; font-weight: 700; color: #0f172a;">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#f59e0b" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="4"/><path d="M12 2v2"/><path d="M12 20v2"/><path d="m4.93 4.93 1.41 1.41"/><path d="m17.66 17.66 1.41 1.41"/><path d="M2 12h2"/><path d="M20 12h2"/><path d="m6.34 17.66-1.41 1.41"/><path d="m19.07 4.93-1.41 1.41"/></svg>
                                    Light Theme
                                </div>
                                <input type="radio" name="system_theme" value="light" <?php echo ($systemTheme === 'light') ? 'checked' : ''; ?> style="width: 18px; height: 18px; accent-color: var(--primary);">
                            </div>
                            <span style="font-size: 13px; color: #64748b;">Clean light background with high contrast dark text.</span>
                        </label>

                        <!-- Dark Theme Option -->
                        <label style="border: 2px solid <?php echo ($systemTheme === 'dark') ? 'var(--primary)' : 'var(--card-border)'; ?>; background: #1e293b; padding: 20px; border-radius: 12px; cursor: pointer; display: flex; flex-direction: column; gap: 12px; transition: all 0.2s;">
                            <div style="display: flex; align-items: center; justify-content: space-between;">
                                <div style="display: flex; align-items: center; gap: 10px; font-weight: 700; color: #f8fafc;">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#3b82f6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9Z"/></svg>
                                    Dark Theme
                                </div>
                                <input type="radio" name="system_theme" value="dark" <?php echo ($systemTheme === 'dark') ? 'checked' : ''; ?> style="width: 18px; height: 18px; accent-color: var(--primary);">
                            </div>
                            <span style="font-size: 13px; color: #94a3b8;">Sleek dark mode interface for comfortable low-light viewing.</span>
                        </label>
                    </div>

                    <div>
                        <button type="submit" class="btn btn-primary" style="padding: 10px 24px; font-weight: 600;">Apply Theme</button>
                    </div>
                </form>
            </div>
        <?php endif; ?>
    </main>

<?php include_once __DIR__ . '/includes/footer.php'; ?>
