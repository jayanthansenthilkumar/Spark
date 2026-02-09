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
    <title>Messages | SPARK'26</title>
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
                    <h1>Messages</h1>
                </div>
                <div class="header-right">
                    <div class="header-icon">
                        <i class="ri-notification-3-line"></i>
                        <span class="badge"></span>
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
                <div class="messages-container">
                    <div class="messages-sidebar">
                        <div class="messages-header">
                            <h3>Inbox</h3>
                            <button class="btn-icon" title="Compose">
                                <i class="ri-edit-line"></i>
                            </button>
                        </div>
                        <div class="messages-search">
                            <i class="ri-search-line"></i>
                            <input type="text" placeholder="Search messages...">
                        </div>
                        <div class="messages-list">
                            <div class="empty-state small">
                                <i class="ri-mail-line"></i>
                                <p>No messages yet</p>
                            </div>
                        </div>
                    </div>

                    <div class="messages-content">
                        <div class="empty-state">
                            <i class="ri-mail-open-line"></i>
                            <h3>No Message Selected</h3>
                            <p>Select a message from the list to view its contents</p>
                        </div>
                    </div>
                </div>

                <div class="compose-modal" id="composeModal" style="display: none;">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h3>New Message</h3>
                            <button class="btn-icon" onclick="closeModal()">
                                <i class="ri-close-line"></i>
                            </button>
                        </div>
                        <form action="sparkBackend.php" method="POST">
                            <input type="hidden" name="action" value="send_message">
                            <div class="form-group">
                                <label for="recipient">To</label>
                                <input type="text" id="recipient" name="recipient" placeholder="Enter recipient email">
                            </div>
                            <div class="form-group">
                                <label for="subject">Subject</label>
                                <input type="text" id="subject" name="subject" placeholder="Enter subject">
                            </div>
                            <div class="form-group">
                                <label for="message">Message</label>
                                <textarea id="message" name="message" rows="6" placeholder="Type your message here..."></textarea>
                            </div>
                            <div class="modal-actions">
                                <button type="button" class="btn-secondary" onclick="closeModal()">Cancel</button>
                                <button type="submit" class="btn-primary">Send Message</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="assets/js/script.js"></script>
    <script>
        function openModal() {
            document.getElementById('composeModal').style.display = 'flex';
        }
        function closeModal() {
            document.getElementById('composeModal').style.display = 'none';
        }
    </script>
</body>

</html>
