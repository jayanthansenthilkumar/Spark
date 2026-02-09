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
    <title>Coordinators | SPARK'26</title>
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
                    <h1>Coordinators</h1>
                </div>
                <div class="header-right">
                    <div class="header-search">
                        <i class="ri-search-line"></i>
                        <input type="text" placeholder="Search coordinators...">
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
                <div class="content-header">
                    <h2>Department Coordinators</h2>
                    <button class="btn-primary">
                        <i class="ri-add-line"></i> Assign Coordinator
                    </button>
                </div>

                <div class="coordinators-grid">
                    <div class="coordinator-card">
                        <div class="coordinator-avatar">
                            <i class="ri-user-line"></i>
                        </div>
                        <div class="coordinator-info">
                            <h3>No Coordinator Assigned</h3>
                            <p class="coordinator-dept">Computer Science</p>
                            <p class="coordinator-email">-</p>
                        </div>
                        <div class="coordinator-stats">
                            <div class="stat">
                                <span class="stat-value">0</span>
                                <span class="stat-label">Projects</span>
                            </div>
                            <div class="stat">
                                <span class="stat-value">0</span>
                                <span class="stat-label">Reviewed</span>
                            </div>
                        </div>
                        <div class="coordinator-actions">
                            <button class="btn-secondary">Assign</button>
                        </div>
                    </div>

                    <div class="coordinator-card">
                        <div class="coordinator-avatar">
                            <i class="ri-user-line"></i>
                        </div>
                        <div class="coordinator-info">
                            <h3>No Coordinator Assigned</h3>
                            <p class="coordinator-dept">Electronics</p>
                            <p class="coordinator-email">-</p>
                        </div>
                        <div class="coordinator-stats">
                            <div class="stat">
                                <span class="stat-value">0</span>
                                <span class="stat-label">Projects</span>
                            </div>
                            <div class="stat">
                                <span class="stat-value">0</span>
                                <span class="stat-label">Reviewed</span>
                            </div>
                        </div>
                        <div class="coordinator-actions">
                            <button class="btn-secondary">Assign</button>
                        </div>
                    </div>

                    <div class="coordinator-card">
                        <div class="coordinator-avatar">
                            <i class="ri-user-line"></i>
                        </div>
                        <div class="coordinator-info">
                            <h3>No Coordinator Assigned</h3>
                            <p class="coordinator-dept">Mechanical</p>
                            <p class="coordinator-email">-</p>
                        </div>
                        <div class="coordinator-stats">
                            <div class="stat">
                                <span class="stat-value">0</span>
                                <span class="stat-label">Projects</span>
                            </div>
                            <div class="stat">
                                <span class="stat-value">0</span>
                                <span class="stat-label">Reviewed</span>
                            </div>
                        </div>
                        <div class="coordinator-actions">
                            <button class="btn-secondary">Assign</button>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="assets/js/script.js"></script>
</body>

</html>
