<?php
require_once 'includes/auth.php';
require_once 'db.php';

checkUserAccess();

$userName = $_SESSION['name'] ?? 'Coordinator';
$userInitials = strtoupper(substr($userName, 0, 2));
$userRole = ucfirst($_SESSION['role'] ?? $_SESSION['user_role'] ?? 'Coordinator');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Top Projects | SPARK'26</title>
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
                    <h1>Top Projects</h1>
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
                <div class="content-header">
                    <h2>Best Projects in Your Department</h2>
                    <div class="filter-controls">
                        <select class="filter-select">
                            <option value="">All Categories</option>
                            <option value="web">Web Development</option>
                            <option value="mobile">Mobile Application</option>
                            <option value="ai">AI/Machine Learning</option>
                            <option value="iot">IoT</option>
                        </select>
                    </div>
                </div>

                <div class="top-projects-grid">
                    <div class="empty-state">
                        <i class="ri-trophy-line"></i>
                        <h3>No Top Projects Yet</h3>
                        <p>Top projects will appear here once projects are reviewed and scored.</p>
                    </div>
                </div>

                <div class="leaderboard-section" style="display: none;">
                    <h3>Project Leaderboard</h3>
                    <div class="leaderboard">
                        <div class="leaderboard-item gold">
                            <span class="rank">1</span>
                            <div class="project-info">
                                <h4>Project Name</h4>
                                <p>Team Lead Name</p>
                            </div>
                            <div class="score">
                                <i class="ri-star-fill"></i>
                                <span>0.0</span>
                            </div>
                        </div>
                        <div class="leaderboard-item silver">
                            <span class="rank">2</span>
                            <div class="project-info">
                                <h4>Project Name</h4>
                                <p>Team Lead Name</p>
                            </div>
                            <div class="score">
                                <i class="ri-star-fill"></i>
                                <span>0.0</span>
                            </div>
                        </div>
                        <div class="leaderboard-item bronze">
                            <span class="rank">3</span>
                            <div class="project-info">
                                <h4>Project Name</h4>
                                <p>Team Lead Name</p>
                            </div>
                            <div class="score">
                                <i class="ri-star-fill"></i>
                                <span>0.0</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="assets/js/script.js"></script>
</body>

</html>
