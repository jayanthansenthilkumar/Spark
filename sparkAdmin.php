<?php
session_start();
require_once 'db.php';

// Check if user is logged in and has admin role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

$userName = $_SESSION['name'] ?? 'Admin';
$userInitials = strtoupper(substr($userName, 0, 2));
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | SPARK'26</title>
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
                <a href="sparkAdmin.php" class="menu-item active">
                    <i class="ri-dashboard-line"></i>
                    Dashboard
                </a>
                <a href="#" class="menu-item">
                    <i class="ri-bar-chart-box-line"></i>
                    Analytics
                </a>
                
                <div class="menu-label">Management</div>
                <a href="#" class="menu-item">
                    <i class="ri-folder-line"></i>
                    All Projects
                </a>
                <a href="#" class="menu-item">
                    <i class="ri-user-line"></i>
                    Users
                </a>
                <a href="#" class="menu-item">
                    <i class="ri-building-line"></i>
                    Departments
                </a>
                <a href="#" class="menu-item">
                    <i class="ri-team-line"></i>
                    Coordinators
                </a>
                
                <div class="menu-label">Event</div>
                <a href="#" class="menu-item">
                    <i class="ri-calendar-line"></i>
                    Schedule
                </a>
                <a href="#" class="menu-item">
                    <i class="ri-megaphone-line"></i>
                    Announcements
                </a>
                <a href="#" class="menu-item">
                    <i class="ri-award-line"></i>
                    Judging
                </a>
                
                <div class="menu-label">System</div>
                <a href="#" class="menu-item">
                    <i class="ri-settings-3-line"></i>
                    Settings
                </a>
                <a href="#" class="menu-item">
                    <i class="ri-database-line"></i>
                    Database
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
                    <h1>Admin Dashboard</h1>
                </div>
                <div class="header-right">
                    <div class="header-search">
                        <i class="ri-search-line"></i>
                        <input type="text" placeholder="Search anything...">
                    </div>
                    <div class="header-icon">
                        <i class="ri-notification-3-line"></i>
                        <span class="badge"></span>
                    </div>
                    <div class="user-profile">
                        <div class="user-avatar" style="background: #ef4444;"><?php echo $userInitials; ?></div>
                        <div class="user-info">
                            <span class="user-name"><?php echo htmlspecialchars($userName); ?></span>
                            <span class="user-role">Administrator</span>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Dashboard Content -->
            <div class="dashboard-content">
                <!-- Welcome Card -->
                <div class="welcome-card" style="background: linear-gradient(135deg, #1e40af, #7c3aed);">
                    <h2>Welcome, Admin! 🛡️</h2>
                    <p>Full control over SPARK'26. Manage users, projects, departments, and event settings from here.</p>
                    <a href="#" class="btn-light">System Settings</a>
                    <div class="welcome-decoration">
                        <i class="ri-shield-star-line"></i>
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
                        <div class="stat-icon green">
                            <i class="ri-user-line"></i>
                        </div>
                        <div class="stat-info">
                            <h3>0</h3>
                            <p>Registered Users</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon amber">
                            <i class="ri-building-line"></i>
                        </div>
                        <div class="stat-info">
                            <h3>13</h3>
                            <p>Departments</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon purple">
                            <i class="ri-calendar-event-line"></i>
                        </div>
                        <div class="stat-info">
                            <h3>6</h3>
                            <p>Days to Event</p>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <h3 style="margin-bottom: 1rem; font-size: 1.1rem;">Quick Actions</h3>
                <div class="quick-actions">
                    <div class="action-card">
                        <div class="action-icon" style="background: rgba(239, 68, 68, 0.1); color: #ef4444;">
                            <i class="ri-user-add-line"></i>
                        </div>
                        <div>
                            <h4>Add User</h4>
                            <p>Create new user account</p>
                        </div>
                    </div>
                    <div class="action-card">
                        <div class="action-icon">
                            <i class="ri-megaphone-line"></i>
                        </div>
                        <div>
                            <h4>Announcement</h4>
                            <p>Broadcast to all users</p>
                        </div>
                    </div>
                    <div class="action-card">
                        <div class="action-icon">
                            <i class="ri-file-chart-line"></i>
                        </div>
                        <div>
                            <h4>Reports</h4>
                            <p>Generate system reports</p>
                        </div>
                    </div>
                    <div class="action-card">
                        <div class="action-icon">
                            <i class="ri-shield-check-line"></i>
                        </div>
                        <div>
                            <h4>Permissions</h4>
                            <p>Manage user roles</p>
                        </div>
                    </div>
                </div>

                <!-- Dashboard Grid -->
                <div class="dashboard-grid" style="margin-top: 2rem;">
                    <div class="dash-card">
                        <div class="dash-card-header">
                            <h3>Recent Activity</h3>
                            <a href="#" style="color: var(--primary); font-size: 0.9rem;">View All</a>
                        </div>
                        <div class="dash-card-body">
                            <p style="color: var(--text-muted);">No recent activity.</p>
                        </div>
                    </div>
                    <div class="dash-card">
                        <div class="dash-card-header">
                            <h3>System Status</h3>
                            <span style="background: #dcfce7; color: #166534; padding: 0.25rem 0.75rem; border-radius: 20px; font-size: 0.8rem; font-weight: 600;">All Systems Normal</span>
                        </div>
                        <div class="dash-card-body">
                            <div style="display: flex; justify-content: space-between; padding: 0.5rem 0; border-bottom: 1px solid var(--border);">
                                <span>Database</span>
                                <span style="color: #22c55e;">● Online</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; padding: 0.5rem 0; border-bottom: 1px solid var(--border);">
                                <span>File Storage</span>
                                <span style="color: #22c55e;">● Online</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; padding: 0.5rem 0;">
                                <span>Email Service</span>
                                <span style="color: #22c55e;">● Online</span>
                            </div>
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
