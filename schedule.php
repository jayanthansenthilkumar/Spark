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
    <title>Schedule | SPARK'26</title>
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
                    <h1>Event Schedule</h1>
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
                <div class="schedule-container">
                    <div class="schedule-header">
                        <h2>SPARK'26 Timeline</h2>
                        <p>Important dates and deadlines for the event</p>
                    </div>

                    <div class="timeline">
                        <div class="timeline-item">
                            <div class="timeline-marker upcoming"></div>
                            <div class="timeline-content">
                                <span class="timeline-date">February 1, 2026</span>
                                <h3>Registration Opens</h3>
                                <p>Start registering your projects and teams</p>
                            </div>
                        </div>

                        <div class="timeline-item">
                            <div class="timeline-marker upcoming"></div>
                            <div class="timeline-content">
                                <span class="timeline-date">February 15, 2026</span>
                                <h3>Project Submission Deadline</h3>
                                <p>Last date to submit your project details</p>
                            </div>
                        </div>

                        <div class="timeline-item">
                            <div class="timeline-marker"></div>
                            <div class="timeline-content">
                                <span class="timeline-date">February 20, 2026</span>
                                <h3>Review Phase</h3>
                                <p>Projects will be reviewed by department coordinators</p>
                            </div>
                        </div>

                        <div class="timeline-item">
                            <div class="timeline-marker"></div>
                            <div class="timeline-content">
                                <span class="timeline-date">February 25, 2026</span>
                                <h3>Final Presentations</h3>
                                <p>Present your projects to the judging panel</p>
                            </div>
                        </div>

                        <div class="timeline-item">
                            <div class="timeline-marker"></div>
                            <div class="timeline-content">
                                <span class="timeline-date">February 28, 2026</span>
                                <h3>Awards Ceremony</h3>
                                <p>Winners announcement and prize distribution</p>
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
