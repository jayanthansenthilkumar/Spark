<?php
require_once 'auth.php';
require_once 'db.php';

checkUserAccess();

$userName = $_SESSION['name'] ?? 'Student Affairs';
$userInitials = strtoupper(substr($userName, 0, 2));
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Affairs | SPARK'26</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
</head>

<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <a href="index.php" class="logo">
                    <div style="width:30px; height:30px; background:var(--primary); border-radius:8px;"></div>
                    SPARK <span>'26</span>
                </a>
            </div>
            <nav class="sidebar-menu">
                <div class="menu-label">Overview</div>
                <a href="studentAffairs.php" class="menu-item active">
                    <i class="ri-dashboard-line"></i>
                    Dashboard
                </a>
                <a href="#" class="menu-item">
                    <i class="ri-bar-chart-line"></i>
                    Analytics
                </a>

                <div class="menu-label">Management</div>
                <a href="#" class="menu-item">
                    <i class="ri-folder-line"></i>
                    All Projects
                </a>
                <a href="#" class="menu-item">
                    <i class="ri-checkbox-circle-line"></i>
                    Approvals
                </a>
                <a href="#" class="menu-item">
                    <i class="ri-group-line"></i>
                    Students
                </a>

                <div class="menu-label">Communication</div>
                <a href="#" class="menu-item">
                    <i class="ri-megaphone-line"></i>
                    Announcements
                </a>
                <a href="#" class="menu-item">
                    <i class="ri-mail-line"></i>
                    Messages
                </a>

                <div class="menu-label">Account</div>
                <a href="#" class="menu-item">
                    <i class="ri-user-line"></i>
                    Profile
                </a>
                <a href="#" class="menu-item">
                    <i class="ri-settings-line"></i>
                    Settings
                </a>
            </nav>
            <div class="sidebar-footer">
                <a href="logout.php" class="menu-item" style="color: #ef4444;">
                    <i class="ri-logout-box-line"></i>
                    Logout
                </a>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Header -->
            <header class="dashboard-header">
                <div class="header-left">
                    <button class="mobile-toggle" onclick="toggleSidebar()">
                        <i class="ri-menu-line"></i>
                    </button>
                    <h1>Student Affairs Dashboard</h1>
                </div>
                <div class="header-right">
                    <div class="header-search">
                        <i class="ri-search-line"></i>
                        <input type="text" placeholder="Search students, projects...">
                    </div>
                    <div class="header-icon">
                        <i class="ri-notification-3-line"></i>
                        <span class="badge"></span>
                    </div>
                    <div class="user-profile">
                        <div class="user-avatar"><?php echo $userInitials; ?></div>
                        <div class="user-info">
                            <span class="user-name"><?php echo htmlspecialchars($userName); ?></span>
                            <span class="user-role">Student Affairs</span>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Dashboard Content -->
            <div class="dashboard-content">
                <!-- Welcome Card -->
                <div class="welcome-card">
                    <h2>Welcome, <?php echo htmlspecialchars(explode(' ', $userName)[0]); ?>! 👋</h2>
                    <p>Manage student projects, review submissions, and coordinate with departments for SPARK'26.</p>
                    <a href="#" class="btn-light">View Pending Approvals</a>
                    <div class="welcome-decoration">
                        <i class="ri-user-star-line"></i>
                    </div>
                </div>

                <!-- Stats Grid -->
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
                        <div class="stat-icon amber">
                            <i class="ri-time-line"></i>
                        </div>
                        <div class="stat-info">
                            <h3>0</h3>
                            <p>Pending Review</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon green">
                            <i class="ri-checkbox-circle-line"></i>
                        </div>
                        <div class="stat-info">
                            <h3>0</h3>
                            <p>Approved</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon purple">
                            <i class="ri-group-line"></i>
                        </div>
                        <div class="stat-info">
                            <h3>0</h3>
                            <p>Registered Students</p>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <h3 style="margin-bottom: 1rem; font-size: 1.1rem;">Quick Actions</h3>
                <div class="quick-actions">
                    <div class="action-card">
                        <div class="action-icon">
                            <i class="ri-checkbox-circle-line"></i>
                        </div>
                        <div>
                            <h4>Review Projects</h4>
                            <p>Approve or reject submissions</p>
                        </div>
                    </div>
                    <div class="action-card">
                        <div class="action-icon">
                            <i class="ri-megaphone-line"></i>
                        </div>
                        <div>
                            <h4>Post Announcement</h4>
                            <p>Notify all students</p>
                        </div>
                    </div>
                    <div class="action-card">
                        <div class="action-icon">
                            <i class="ri-file-download-line"></i>
                        </div>
                        <div>
                            <h4>Export Reports</h4>
                            <p>Download project data</p>
                        </div>
                    </div>
                    <div class="action-card">
                        <div class="action-icon">
                            <i class="ri-calendar-check-line"></i>
                        </div>
                        <div>
                            <h4>Schedule Event</h4>
                            <p>Manage event timeline</p>
                        </div>
                    </div>
                </div>

                <!-- Dashboard Grid -->
                <div class="dashboard-grid" style="margin-top: 2rem;">
                    <div class="dash-card">
                        <div class="dash-card-header">
                            <h3>Recent Submissions</h3>
                            <a href="#" style="color: var(--primary); font-size: 0.9rem;">View All</a>
                        </div>
                        <div class="dash-card-body">
                            <p style="color: var(--text-muted);">No submissions yet.</p>
                        </div>
                    </div>
                    <div class="dash-card">
                        <div class="dash-card-header">
                            <h3>Department Overview</h3>
                            <a href="#" style="color: var(--primary); font-size: 0.9rem;">View Details</a>
                        </div>
                        <div class="dash-card-body">
                            <p style="color: var(--text-muted);">No data available.</p>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('open');
        }
    </script>
</body>

</html>