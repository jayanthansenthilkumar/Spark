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
    <title>Review & Approve | SPARK'26</title>
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
                    <h1>Review & Approve</h1>
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
                <div class="review-stats">
                    <div class="stat-card">
                        <div class="stat-icon amber">
                            <i class="ri-time-line"></i>
                        </div>
                        <div class="stat-info">
                            <h3>0</h3>
                            <p>Awaiting Review</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon blue">
                            <i class="ri-eye-line"></i>
                        </div>
                        <div class="stat-info">
                            <h3>0</h3>
                            <p>Under Review</p>
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
                        <div class="stat-icon red">
                            <i class="ri-close-circle-line"></i>
                        </div>
                        <div class="stat-info">
                            <h3>0</h3>
                            <p>Rejected</p>
                        </div>
                    </div>
                </div>

                <div class="content-header">
                    <h2>Projects Pending Review</h2>
                </div>

                <div class="review-queue">
                    <div class="empty-state">
                        <i class="ri-checkbox-circle-line"></i>
                        <h3>No Projects to Review</h3>
                        <p>All projects in your department have been reviewed. Check back later for new submissions.</p>
                    </div>
                </div>

                <!-- Review Modal Template -->
                <div class="review-modal" id="reviewModal" style="display: none;">
                    <div class="modal-content large">
                        <div class="modal-header">
                            <h3>Review Project</h3>
                            <button class="btn-icon" onclick="closeReviewModal()">
                                <i class="ri-close-line"></i>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="project-details">
                                <h4>Project Title</h4>
                                <p>Project description will appear here...</p>
                            </div>
                            <form action="sparkBackend.php" method="POST">
                                <input type="hidden" name="action" value="review_project">
                                <input type="hidden" name="project_id" value="">
                                
                                <div class="form-group">
                                    <label>Review Decision</label>
                                    <div class="decision-buttons">
                                        <label class="decision-option approve">
                                            <input type="radio" name="decision" value="approved" required>
                                            <i class="ri-checkbox-circle-line"></i>
                                            <span>Approve</span>
                                        </label>
                                        <label class="decision-option reject">
                                            <input type="radio" name="decision" value="rejected" required>
                                            <i class="ri-close-circle-line"></i>
                                            <span>Reject</span>
                                        </label>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="reviewComments">Comments</label>
                                    <textarea id="reviewComments" name="comments" rows="4" placeholder="Add your review comments..."></textarea>
                                </div>

                                <div class="modal-actions">
                                    <button type="button" class="btn-secondary" onclick="closeReviewModal()">Cancel</button>
                                    <button type="submit" class="btn-primary">Submit Review</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="assets/js/script.js"></script>
    <script>
        function openReviewModal(projectId) {
            document.getElementById('reviewModal').style.display = 'flex';
        }
        function closeReviewModal() {
            document.getElementById('reviewModal').style.display = 'none';
        }
    </script>
</body>

</html>
