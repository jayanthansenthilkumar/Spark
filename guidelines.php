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
    <title>Guidelines | SPARK'26</title>
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
                    <h1>Guidelines</h1>
                </div>
                <div class="header-right">
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
                <div class="guidelines-container">
                    <div class="guideline-section">
                        <h2><i class="ri-information-line"></i> General Guidelines</h2>
                        <ul>
                            <li>All projects must be original work created by the team members</li>
                            <li>Teams can have a maximum of 4 members</li>
                            <li>Each participant can only be part of one team</li>
                            <li>Projects must be submitted before the deadline</li>
                            <li>All team members must be currently enrolled students</li>
                        </ul>
                    </div>

                    <div class="guideline-section">
                        <h2><i class="ri-folder-line"></i> Project Requirements</h2>
                        <ul>
                            <li>Project must have a clear problem statement and solution</li>
                            <li>Documentation must include project overview, architecture, and usage instructions</li>
                            <li>Source code must be submitted via GitHub repository</li>
                            <li>A working demo or prototype is mandatory</li>
                            <li>Projects should be innovative and practical</li>
                        </ul>
                    </div>

                    <div class="guideline-section">
                        <h2><i class="ri-presentation-line"></i> Presentation Guidelines</h2>
                        <ul>
                            <li>Presentations should be 10-15 minutes long</li>
                            <li>Include live demo of your project</li>
                            <li>Be prepared for Q&A session</li>
                            <li>All team members should participate in the presentation</li>
                            <li>Presentation slides should be submitted 24 hours before</li>
                        </ul>
                    </div>

                    <div class="guideline-section">
                        <h2><i class="ri-award-line"></i> Judging Criteria</h2>
                        <ul>
                            <li><strong>Innovation (25%)</strong> - Uniqueness and creativity of the solution</li>
                            <li><strong>Technical Complexity (25%)</strong> - Technical implementation and architecture</li>
                            <li><strong>Practicality (20%)</strong> - Real-world applicability and impact</li>
                            <li><strong>Presentation (15%)</strong> - Quality of presentation and demo</li>
                            <li><strong>Documentation (15%)</strong> - Code quality and documentation</li>
                        </ul>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="assets/js/script.js"></script>
</body>

</html>
