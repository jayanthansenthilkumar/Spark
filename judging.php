<?php
require_once 'includes/auth.php';
require_once 'db.php';

checkUserAccess();

$userName = $_SESSION['name'] ?? 'Admin';
$userInitials = strtoupper(substr($userName, 0, 2));
$userRole = ucfirst($_SESSION['role'] ?? $_SESSION['user_role'] ?? 'Admin');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Judging | SPARK'26</title>
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
                    <h1>Judging Panel</h1>
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
                    <h2>Judging Management</h2>
                    <button class="btn-primary">
                        <i class="ri-add-line"></i> Add Judge
                    </button>
                </div>

                <div class="judging-section">
                    <div class="judging-criteria">
                        <h3>Judging Criteria</h3>
                        <div class="criteria-list">
                            <div class="criteria-item">
                                <span class="criteria-name">Innovation</span>
                                <span class="criteria-weight">25%</span>
                            </div>
                            <div class="criteria-item">
                                <span class="criteria-name">Technical Complexity</span>
                                <span class="criteria-weight">25%</span>
                            </div>
                            <div class="criteria-item">
                                <span class="criteria-name">Practicality</span>
                                <span class="criteria-weight">20%</span>
                            </div>
                            <div class="criteria-item">
                                <span class="criteria-name">Presentation</span>
                                <span class="criteria-weight">15%</span>
                            </div>
                            <div class="criteria-item">
                                <span class="criteria-name">Documentation</span>
                                <span class="criteria-weight">15%</span>
                            </div>
                        </div>
                        <button class="btn-secondary">
                            <i class="ri-edit-line"></i> Edit Criteria
                        </button>
                    </div>

                    <div class="judges-panel">
                        <h3>Judges Panel</h3>
                        <div class="empty-state">
                            <i class="ri-user-star-line"></i>
                            <h4>No Judges Assigned</h4>
                            <p>Add judges to start the evaluation process</p>
                            <button class="btn-primary">Add Judge</button>
                        </div>
                    </div>
                </div>

                <div class="judging-progress">
                    <h3>Judging Progress</h3>
                    <div class="progress-stats">
                        <div class="progress-item">
                            <span class="progress-label">Projects Judged</span>
                            <div class="progress-bar">
                                <div class="progress-fill" style="width: 0%"></div>
                            </div>
                            <span class="progress-value">0/0</span>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="assets/js/script.js"></script>
</body>

</html>
