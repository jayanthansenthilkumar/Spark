<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Projects | SPARK'26</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">

</head>

<body>

    <div class="app-container">
        <!-- Sidebar -->
        <aside class="d-sidebar">
            <div class="sidebar-header">
                <a href="index.php" class="logo">
                    <div style="width:30px; height:30px; background:var(--primary); border-radius:8px;"></div>
                    SPARK <span>'26</span>
                </a>
            </div>
            <div class="sidebar-menu">
                <a href="studentDashboard.php" class="menu-item"><i class="ri-home-line"></i> Home</a>
                <a href="studentProjects.php" class="menu-item active"><i class="ri-stack-line"></i> My Projects</a>
                <a href="studentMessages.php" class="menu-item"><i class="ri-mail-line"></i> Messages</a>
                <a href="studentResources.php" class="menu-item"><i class="ri-book-open-line"></i> Resources</a>
            </div>
            <div style="padding: 1.5rem;">
                <a href="login.php" class="menu-item" style="color: #ef4444;"><i class="ri-logout-box-r-line"></i>
                    Logout</a>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="d-main">
            <!-- Header -->
            <header class="d-header">
                <div class="header-search">
                    <i class="ri-search-line"></i>
                    <input type="text" placeholder="Search resources...">
                </div>
                <!-- Profile -->
                <div class="header-profile" onclick="toggleDropdown()">
                    <div class="user-info">
                        <span class="user-name">Alex Morgan</span>
                        <span class="user-role">Student</span>
                    </div>
                    <div class="user-avatar">AM</div>
                    <div class="profile-dropdown" id="profileDropdown">
                        <a href="userProfile.php" class="dropdown-item"><i class="ri-user-line"></i> My Profile</a>
                        <div class="dropdown-divider"></div>
                        <a href="login.php" class="dropdown-item" style="color: #ef4444;"><i
                                class="ri-logout-box-r-line"></i> Logout</a>
                    </div>
                </div>
            </header>

            <!-- Dashboard Content -->
            <div class="d-content">
                <!-- Page Header -->
                <div class="welcome-card" style="min-height: 150px;">
                    <div class="welcome-text">
                        <h2>My Projects</h2>
                        <p>Manage my projects here.</p>
                    </div>
                </div>

                <div style="text-align:center; padding: 4rem; color: var(--text-muted);">
                    <i class="ri-stack-line" style="font-size: 4rem; opacity: 0.2; margin-bottom: 1rem;"></i>
                    <h3>My Projects Module</h3>
                    <p>This page has been created and linked.</p>
                </div>
            </div>
        </main>
    </div>

    <script>
        function toggleDropdown() {
            document.getElementById('profileDropdown').classList.toggle('show');
        }
        window.onclick = function (event) {
            if (!event.target.closest('.header-profile')) {
                var dropdowns = document.getElementsByClassName("profile-dropdown");
                for (var i = 0; i < dropdowns.length; i++) {
                    var openDropdown = dropdowns[i];
                    if (openDropdown.classList.contains('show')) {
                        openDropdown.classList.remove('show');
                    }
                }
            }
        }
    </script>
</body>

</html>