<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Read theme setting (light / dark)
$sysTheme = $_SESSION['system_theme'] ?? 'light';
if ($sysTheme === 'dark') {
    // If not set via DB query in page yet, check DB directly
    if (isset($db) && $db !== null && isset($_SESSION['user_email'])) {
        $stmtTh = $db->prepare("SELECT theme FROM user_preferences WHERE email = :email LIMIT 1");
        $stmtTh->execute([':email' => $_SESSION['user_email']]);
        $prefTh = $stmtTh->fetch();
        if ($prefTh && !empty($prefTh['theme'])) {
            $sysTheme = $prefTh['theme'];
            $_SESSION['system_theme'] = $sysTheme;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en" data-theme="<?php echo htmlspecialchars($sysTheme); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle . ' - EDUsync' : 'EDUsync - School Management Dashboard'; ?></title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Main Design System CSS -->
    <link rel="stylesheet" href="assets/css/style.css?v=<?php echo time(); ?>">
    
    <!-- Chart.js CDN for Analytics Visualizations -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
</head>
<body>
<div class="app-container">
