<?php
/**
 * Shared Navbar Header Layout Component
 * Matches custom navbar design with Page Title, Breadcrumbs, Pill Search, Bell Badge, Notification Popup, and Profile Dropdown Popup
 */
$displayTitle = isset($pageTitle) ? $pageTitle : 'Dashboard';

// Fetch live database notifications
if (!isset($dashboardService)) {
    require_once __DIR__ . '/../classes/Dashboard.php';
    $dashboardService = new Dashboard();
}
$navNotifications = $dashboardService->getNotifications();
$notifCount = count($navNotifications);
?>
<header class="navbar">
    <!-- Left Section: Page Title & Breadcrumb -->
    <div class="navbar-title-container">
        <h1 class="navbar-page-title"><?php echo htmlspecialchars($displayTitle); ?></h1>
        <div class="navbar-breadcrumb">
            <span class="breadcrumb-brand">EDUsync</span>
            <span class="breadcrumb-separator">/</span>
            <span class="breadcrumb-current"><?php echo htmlspecialchars($displayTitle); ?></span>
        </div>
    </div>

    <!-- Right Section: Search, Notification Bell & Profile Dropdown -->
    <div class="navbar-right">
        <!-- Search Input Box -->
        <form action="search.php" method="GET" class="navbar-search-box" style="margin: 0;">
            <span class="search-box-icon">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            </span>
            <input type="text" name="search" placeholder="Search students, teachers, subjects..." id="globalSearchInput" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
        </form>

        <!-- Notification Bell Container -->
        <div class="navbar-bell-dropdown-wrapper">
            <button type="button" class="navbar-bell-btn <?php echo ($notifCount > 0) ? 'has-unread' : ''; ?>" id="navbarBellBtn" title="System Notifications" onclick="var notif=document.getElementById('notificationDropdownMenu'); var prof=document.getElementById('profileDropdownMenu'); if(prof){ prof.style.display='none'; prof.classList.remove('show'); } if(notif){ var open=(notif.style.display==='block'||notif.classList.contains('show')); notif.style.display=open?'none':'block'; notif.classList.toggle('show', !open); } event.stopPropagation();">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
                <?php if ($notifCount > 0): ?>
                    <span class="bell-badge-count"><?php echo $notifCount; ?></span>
                <?php endif; ?>
            </button>

            <!-- Notifications Dropdown Popup Menu (Live MySQL Data) -->
            <div class="notification-dropdown-menu" id="notificationDropdownMenu">
                <div class="notif-header">
                    <div class="notif-header-left">
                        <span class="notif-title">Notifications</span>
                        <span class="notif-badge" id="notifBadgeCount"><?php echo $notifCount; ?> new</span>
                    </div>
                    <?php if ($notifCount > 0): ?>
                        <button type="button" class="mark-read-btn" id="markReadBtn" title="Mark all as read" onclick="markAllNotificationsRead();">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/><polyline points="20 12 12 20"/></svg>
                        </button>
                    <?php endif; ?>
                </div>
                <div class="notif-body">
                    <?php if (!empty($navNotifications)): ?>
                        <?php foreach ($navNotifications as $n): ?>
                            <div class="notif-item">
                                <div class="notif-dot <?php echo $n['dot']; ?>"></div>
                                <div class="notif-content">
                                    <span class="notif-item-title"><?php echo htmlspecialchars($n['title']); ?></span>
                                    <span class="notif-item-desc"><?php echo htmlspecialchars($n['desc']); ?></span>
                                    <span class="notif-item-time"><?php echo htmlspecialchars($n['time']); ?></span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="notif-item">
                            <span class="notif-item-desc">No new notifications.</span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- User Profile Pill Container -->
        <div class="user-profile-dropdown-wrapper">
            <div class="navbar-user-pill" id="userProfilePill" title="User Profile Menu" onclick="var notif=document.getElementById('notificationDropdownMenu'); var prof=document.getElementById('profileDropdownMenu'); if(notif){ notif.style.display='none'; notif.classList.remove('show'); } if(prof){ var open=(prof.style.display==='block'||prof.classList.contains('show')); prof.style.display=open?'none':'block'; prof.classList.toggle('show', !open); } event.stopPropagation();">
                <div class="user-pill-avatar">AD</div>
                <span class="user-pill-name">Admin</span>
                <span class="user-pill-chevron">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
                </span>
            </div>

            <!-- Admin Profile Dropdown Popup Menu -->
            <div class="profile-dropdown-menu" id="profileDropdownMenu">
                <div class="dropdown-user-header">
                    <div class="dropdown-avatar">AD</div>
                    <div class="dropdown-user-details">
                        <span class="dropdown-user-name">Admin User</span>
                        <span class="dropdown-user-email">admin@edusync.edu</span>
                        <span class="dropdown-user-badge">Administrator</span>
                    </div>
                </div>
                <div class="dropdown-body">
                    <a href="settings.php?tab=profile" class="dropdown-item">
                        <div class="dropdown-item-icon">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                        </div>
                        <div class="dropdown-item-text">
                            <span class="item-title">My Profile</span>
                            <span class="item-subtitle">Account settings & info</span>
                        </div>
                    </a>
                    <a href="settings.php?tab=preferences" class="dropdown-item">
                        <div class="dropdown-item-icon">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
                        </div>
                        <div class="dropdown-item-text">
                            <span class="item-title">System Preferences</span>
                            <span class="item-subtitle">Dashboard options</span>
                        </div>
                    </a>
                </div>
                <div class="dropdown-footer">
                    <a href="logout.php" class="dropdown-item">
                        <div class="dropdown-item-icon logout-icon-box">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                        </div>
                        <div class="dropdown-item-text">
                            <span class="item-title logout-title">Log Out</span>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</header>
