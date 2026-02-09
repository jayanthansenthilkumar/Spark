<?php
require_once 'includes/auth.php';
require_once 'db.php';

checkUserAccess();

$userName = $_SESSION['name'] ?? 'User';
$userInitials = strtoupper(substr($userName, 0, 2));
$userRole = ucfirst($_SESSION['role'] ?? $_SESSION['user_role'] ?? 'User');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Announcements | SPARK'26</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
</head>

<body>
    <div class="dashboard-container">
        <?php include 'includes/sidebar.php'; ?>

        <main class="main-content">
            <header class="dashboard-header">
                <div class="header-left">
                    <button class="mobile-toggle" onclick="toggleSidebar()">
                        <i class="ri-menu-line"></i>
                    </button>
                    <h1>Announcements</h1>
                </div>
                <div class="header-right">
                    <div class="header-icon">
                        <i class="ri-notification-3-line"></i>
                        <span class="badge"></span>
                    </div>
                    <div class="user-profile">
                        <div class="user-avatar"><?php echo $userInitials; ?></div>
                        <div class="user-info">
                            <span class="user-name"><?php echo htmlspecialchars($userName); ?></span>
                            <span class="user-role"><?php echo htmlspecialchars($userRole); ?></span>
                        </div>
                    </div>
                </div>
            </header>

            <div class="dashboard-content">
                <div class="announcements-container">
                    <div class="announcement-card featured">
                        <div class="announcement-header">
                            <span class="announcement-badge new">New</span>
                            <span class="announcement-date">February 9, 2026</span>
                        </div>
                        <h3>Welcome to SPARK'26!</h3>
                        <p>We're excited to announce that SPARK'26 registration is now open! This year's event promises to be bigger and better than ever. Get ready to showcase your innovative projects and compete with the brightest minds on campus.</p>
                        <div class="announcement-footer">
                            <span><i class="ri-user-line"></i> Admin</span>
                        </div>
                    </div>

                    <div class="announcement-card">
                        <div class="announcement-header">
                            <span class="announcement-badge">Important</span>
                            <span class="announcement-date">February 8, 2026</span>
                        </div>
                        <h3>Submission Guidelines Updated</h3>
                        <p>Please review the updated project submission guidelines. We've added new categories and revised the documentation requirements. Make sure to check the Guidelines page for complete details.</p>
                        <div class="announcement-footer">
                            <span><i class="ri-user-line"></i> Event Committee</span>
                        </div>
                    </div>

                    <div class="announcement-card">
                        <div class="announcement-header">
                            <span class="announcement-date">February 5, 2026</span>
                        </div>
                        <h3>Workshop: Project Presentation Tips</h3>
                        <p>Join us for a special workshop on how to effectively present your projects. Learn tips and tricks from previous winners and industry experts.</p>
                        <div class="announcement-footer">
                            <span><i class="ri-user-line"></i> Student Affairs</span>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="assets/js/script.js"></script>
</body>

</html>
