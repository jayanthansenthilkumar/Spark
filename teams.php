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
    <title>Teams | SPARK'26</title>
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
                    <h1>Teams</h1>
                </div>
                <div class="header-right">
                    <div class="header-search">
                        <i class="ri-search-line"></i>
                        <input type="text" placeholder="Search teams...">
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
                    <h2>Project Teams</h2>
                </div>

                <div class="teams-grid">
                    <div class="empty-state">
                        <i class="ri-team-line"></i>
                        <h3>No Teams Yet</h3>
                        <p>Teams will appear here once students submit projects with team members.</p>
                    </div>
                </div>

                <!-- Team Card Template (hidden, for reference) -->
                <div class="team-card-template" style="display: none;">
                    <div class="team-card">
                        <div class="team-header">
                            <h3>Team Name</h3>
                            <span class="team-badge">4 Members</span>
                        </div>
                        <div class="team-project">
                            <i class="ri-folder-line"></i>
                            <span>Project Name</span>
                        </div>
                        <div class="team-members">
                            <div class="member">
                                <div class="member-avatar">JD</div>
                                <div class="member-info">
                                    <span class="member-name">John Doe</span>
                                    <span class="member-role">Team Lead</span>
                                </div>
                            </div>
                            <div class="member">
                                <div class="member-avatar">JS</div>
                                <div class="member-info">
                                    <span class="member-name">Jane Smith</span>
                                    <span class="member-role">Member</span>
                                </div>
                            </div>
                        </div>
                        <div class="team-actions">
                            <button class="btn-secondary">View Details</button>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="assets/js/script.js"></script>
</body>

</html>
