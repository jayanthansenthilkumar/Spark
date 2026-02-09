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
    <title>Departments | SPARK'26</title>
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
                    <h1>Departments</h1>
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
                    <h2>Department Management</h2>
                    <button class="btn-primary">
                        <i class="ri-add-line"></i> Add Department
                    </button>
                </div>

                <div class="departments-grid">
                    <div class="department-card">
                        <div class="dept-icon">
                            <i class="ri-computer-line"></i>
                        </div>
                        <h3>Computer Science</h3>
                        <div class="dept-stats">
                            <span><i class="ri-user-line"></i> 0 Students</span>
                            <span><i class="ri-folder-line"></i> 0 Projects</span>
                        </div>
                        <div class="dept-coordinator">
                            <span>Coordinator: Not Assigned</span>
                        </div>
                        <div class="dept-actions">
                            <button class="btn-icon" title="Edit"><i class="ri-edit-line"></i></button>
                            <button class="btn-icon" title="View"><i class="ri-eye-line"></i></button>
                        </div>
                    </div>

                    <div class="department-card">
                        <div class="dept-icon">
                            <i class="ri-cpu-line"></i>
                        </div>
                        <h3>Electronics</h3>
                        <div class="dept-stats">
                            <span><i class="ri-user-line"></i> 0 Students</span>
                            <span><i class="ri-folder-line"></i> 0 Projects</span>
                        </div>
                        <div class="dept-coordinator">
                            <span>Coordinator: Not Assigned</span>
                        </div>
                        <div class="dept-actions">
                            <button class="btn-icon" title="Edit"><i class="ri-edit-line"></i></button>
                            <button class="btn-icon" title="View"><i class="ri-eye-line"></i></button>
                        </div>
                    </div>

                    <div class="department-card">
                        <div class="dept-icon">
                            <i class="ri-settings-line"></i>
                        </div>
                        <h3>Mechanical</h3>
                        <div class="dept-stats">
                            <span><i class="ri-user-line"></i> 0 Students</span>
                            <span><i class="ri-folder-line"></i> 0 Projects</span>
                        </div>
                        <div class="dept-coordinator">
                            <span>Coordinator: Not Assigned</span>
                        </div>
                        <div class="dept-actions">
                            <button class="btn-icon" title="Edit"><i class="ri-edit-line"></i></button>
                            <button class="btn-icon" title="View"><i class="ri-eye-line"></i></button>
                        </div>
                    </div>

                    <div class="department-card add-new">
                        <i class="ri-add-line"></i>
                        <span>Add New Department</span>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="assets/js/script.js"></script>
</body>

</html>
