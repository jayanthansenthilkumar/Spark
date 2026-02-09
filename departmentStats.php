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
    <title>Department Stats | SPARK'26</title>
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
                    <h1>Department Statistics</h1>
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
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon blue">
                            <i class="ri-folder-line"></i>
                        </div>
                        <div class="stat-info">
                            <h3>0</h3>
                            <p>Total Projects</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon green">
                            <i class="ri-user-line"></i>
                        </div>
                        <div class="stat-info">
                            <h3>0</h3>
                            <p>Students</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon amber">
                            <i class="ri-time-line"></i>
                        </div>
                        <div class="stat-info">
                            <h3>0</h3>
                            <p>Pending Review</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon purple">
                            <i class="ri-team-line"></i>
                        </div>
                        <div class="stat-info">
                            <h3>0</h3>
                            <p>Teams</p>
                        </div>
                    </div>
                </div>

                <div class="analytics-charts">
                    <div class="chart-card">
                        <h3>Projects by Category</h3>
                        <div class="chart-placeholder">
                            <i class="ri-pie-chart-line"></i>
                            <p>No data available</p>
                        </div>
                    </div>

                    <div class="chart-card">
                        <h3>Submissions This Month</h3>
                        <div class="chart-placeholder">
                            <i class="ri-line-chart-line"></i>
                            <p>No data available</p>
                        </div>
                    </div>

                    <div class="chart-card">
                        <h3>Approval Rate</h3>
                        <div class="chart-placeholder">
                            <i class="ri-donut-chart-line"></i>
                            <p>No data available</p>
                        </div>
                    </div>

                    <div class="chart-card">
                        <h3>Student Participation</h3>
                        <div class="chart-placeholder">
                            <i class="ri-bar-chart-line"></i>
                            <p>No data available</p>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="assets/js/script.js"></script>
</body>

</html>
