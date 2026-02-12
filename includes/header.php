<?php
// includes/header.php

// Ensure session is started (usually done in auth.php, but safety check)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Ensure DB connection if not present
if (!isset($conn)) {
    $rootPath = dirname(__DIR__);
    if (file_exists($rootPath . '/db.php')) {
        require_once $rootPath . '/db.php';
    }
}

$userName = $_SESSION['name'] ?? 'User';
$userInitials = strtoupper(substr($userName, 0, 2));
$userRole = $_SESSION['role'] ?? 'guest';
$userId = $_SESSION['user_id'] ?? 0;

// Default Notification Data
$notificationCount = 0;
$notificationTitle = "No new notifications";
$notifications = []; // Array to hold notification items for dropdown

if (isset($conn) && $userId) {

    // =============================================
    // SHARED: Unread messages (all roles have messages.php)
    // =============================================
    $msgStmt = mysqli_prepare($conn, "
        SELECT m.id, m.subject, m.created_at, u.name as sender_name
        FROM messages m
        JOIN users u ON m.sender_id = u.id
        WHERE m.recipient_id = ? AND m.is_read = 0
        ORDER BY m.created_at DESC
        LIMIT 5
    ");
    if ($msgStmt) {
        mysqli_stmt_bind_param($msgStmt, "i", $userId);
        mysqli_stmt_execute($msgStmt);
        $msgRes = mysqli_stmt_get_result($msgStmt);
        while ($row = mysqli_fetch_assoc($msgRes)) {
            $notifications[] = [
                'id' => $row['id'],
                'type' => 'message',
                'icon' => 'ri-mail-line',
                'color' => '#3B82F6',
                'title' => 'New Message',
                'message' => '<strong>' . htmlspecialchars($row['sender_name']) . '</strong>: ' . htmlspecialchars($row['subject']),
                'time' => $row['created_at'],
                'link' => 'messages.php'
            ];
        }
        mysqli_stmt_close($msgStmt);
    }

    // =============================================
    // SHARED: Announcements targeted at this role (all roles have announcements.php)
    // Show last 7 days of announcements
    // =============================================
    $annStmt = mysqli_prepare($conn, "
        SELECT id, title, message, created_at
        FROM announcements
        WHERE (target_role = 'all' OR target_role = ?)
        AND created_at >= NOW() - INTERVAL 7 DAY
        ORDER BY created_at DESC
        LIMIT 5
    ");
    if ($annStmt) {
        mysqli_stmt_bind_param($annStmt, "s", $userRole);
        mysqli_stmt_execute($annStmt);
        $annRes = mysqli_stmt_get_result($annStmt);
        while ($row = mysqli_fetch_assoc($annRes)) {
            $notifications[] = [
                'id' => $row['id'],
                'type' => 'announcement',
                'icon' => 'ri-megaphone-line',
                'color' => '#10B981',
                'title' => htmlspecialchars($row['title']),
                'message' => htmlspecialchars(substr($row['message'], 0, 80)) . (strlen($row['message']) > 80 ? '...' : ''),
                'time' => $row['created_at'],
                'link' => 'announcements.php'
            ];
        }
        mysqli_stmt_close($annStmt);
    }

    // =============================================
    // SHARED: Upcoming schedule events (next 3 days)
    // =============================================
    $schedStmt = mysqli_query($conn, "
        SELECT id, title, description, event_date, event_type
        FROM schedule
        WHERE event_date BETWEEN NOW() AND NOW() + INTERVAL 3 DAY
        ORDER BY event_date ASC
        LIMIT 3
    ");
    if ($schedStmt) {
        while ($row = mysqli_fetch_assoc($schedStmt)) {
            $evtIcon = 'ri-calendar-event-line';
            $evtColor = '#6366F1';
            if ($row['event_type'] === 'deadline') { $evtIcon = 'ri-alarm-warning-line'; $evtColor = '#EF4444'; }
            elseif ($row['event_type'] === 'milestone') { $evtIcon = 'ri-flag-line'; $evtColor = '#F59E0B'; }
            elseif ($row['event_type'] === 'event') { $evtIcon = 'ri-star-line'; $evtColor = '#8B5CF6'; }

            $notifications[] = [
                'id' => $row['id'],
                'type' => 'schedule',
                'icon' => $evtIcon,
                'color' => $evtColor,
                'title' => htmlspecialchars($row['title']),
                'message' => date('M j, g:i A', strtotime($row['event_date'])),
                'time' => $row['event_date'],
                'link' => 'schedule.php'
            ];
        }
    }

    // =============================================
    // ROLE: Student
    // =============================================
    if ($userRole === 'student') {

        // Pending team invitations (only if student has no team)
        $teamCheckHeader = mysqli_prepare($conn, "SELECT id FROM team_members WHERE user_id = ?");
        if ($teamCheckHeader) {
            mysqli_stmt_bind_param($teamCheckHeader, "i", $userId);
            mysqli_stmt_execute($teamCheckHeader);
            mysqli_stmt_store_result($teamCheckHeader);
            $hasTeam = mysqli_stmt_num_rows($teamCheckHeader) > 0;
            mysqli_stmt_close($teamCheckHeader);

            if (!$hasTeam) {
                $invStmt = mysqli_prepare($conn, "
                    SELECT ti.id, ti.created_at, t.team_name, u.name as inviter_name
                    FROM team_invitations ti
                    JOIN teams t ON ti.team_id = t.id
                    JOIN users u ON ti.invited_by = u.id
                    WHERE ti.invited_user_id = ? AND ti.status = 'pending'
                    ORDER BY ti.created_at DESC
                    LIMIT 10
                ");
                if ($invStmt) {
                    mysqli_stmt_bind_param($invStmt, "i", $userId);
                    mysqli_stmt_execute($invStmt);
                    $res = mysqli_stmt_get_result($invStmt);
                    while ($row = mysqli_fetch_assoc($res)) {
                        $notifications[] = [
                            'id' => $row['id'],
                            'type' => 'invitation',
                            'icon' => 'ri-team-line',
                            'color' => '#D97706',
                            'title' => 'Team Invitation',
                            'message' => htmlspecialchars($row['inviter_name']) . ' invited you to join <strong>' . htmlspecialchars($row['team_name']) . '</strong>',
                            'time' => $row['created_at'],
                            'link' => 'myTeam.php'
                        ];
                    }
                    mysqli_stmt_close($invStmt);
                }
            }
        }

        // Project status updates (approved/rejected in last 7 days)
        $projStatusStmt = mysqli_prepare($conn, "
            SELECT p.id, p.title, p.status, p.reviewed_at, p.review_comments
            FROM projects p
            WHERE p.student_id = ? AND p.status IN ('approved', 'rejected')
            AND p.reviewed_at >= NOW() - INTERVAL 7 DAY
            ORDER BY p.reviewed_at DESC
            LIMIT 5
        ");
        if ($projStatusStmt) {
            mysqli_stmt_bind_param($projStatusStmt, "i", $userId);
            mysqli_stmt_execute($projStatusStmt);
            $projRes = mysqli_stmt_get_result($projStatusStmt);
            while ($row = mysqli_fetch_assoc($projRes)) {
                $isApproved = $row['status'] === 'approved';
                $notifications[] = [
                    'id' => $row['id'],
                    'type' => 'project_status',
                    'icon' => $isApproved ? 'ri-checkbox-circle-line' : 'ri-close-circle-line',
                    'color' => $isApproved ? '#10B981' : '#EF4444',
                    'title' => 'Project ' . ucfirst($row['status']),
                    'message' => '"' . htmlspecialchars($row['title']) . '"' . ($row['review_comments'] ? ' — ' . htmlspecialchars(substr($row['review_comments'], 0, 50)) : ''),
                    'time' => $row['reviewed_at'],
                    'link' => 'myProjects.php'
                ];
            }
            mysqli_stmt_close($projStatusStmt);
        }

    // =============================================
    // ROLE: Department Coordinator
    // =============================================
    } elseif ($userRole === 'departmentcoordinator') {
        $deptCoord = $_SESSION['department'] ?? '';

        // Pending project reviews for their department
        $projStmt = mysqli_prepare($conn, "
            SELECT p.id, p.title, p.created_at, u.name as student_name
            FROM projects p
            JOIN users u ON p.student_id = u.id
            WHERE p.department = ? AND p.status = 'pending'
            ORDER BY p.created_at DESC
            LIMIT 5
        ");
        if ($projStmt) {
            mysqli_stmt_bind_param($projStmt, "s", $deptCoord);
            mysqli_stmt_execute($projStmt);
            $projRes = mysqli_stmt_get_result($projStmt);
            while ($row = mysqli_fetch_assoc($projRes)) {
                $notifications[] = [
                    'id' => $row['id'],
                    'type' => 'review',
                    'icon' => 'ri-file-search-line',
                    'color' => '#F59E0B',
                    'title' => 'Project Pending Review',
                    'message' => '<strong>' . htmlspecialchars($row['student_name']) . '</strong> submitted "' . htmlspecialchars($row['title']) . '"',
                    'time' => $row['created_at'],
                    'link' => 'reviewApprove.php'
                ];
            }
            mysqli_stmt_close($projStmt);
        }

        // New teams created in their department (last 7 days)
        $teamStmt = mysqli_prepare($conn, "
            SELECT t.id, t.team_name, t.created_at, u.name as leader_name
            FROM teams t
            JOIN users u ON t.leader_id = u.id
            WHERE t.department = ? AND t.created_at >= NOW() - INTERVAL 7 DAY
            ORDER BY t.created_at DESC
            LIMIT 5
        ");
        if ($teamStmt) {
            mysqli_stmt_bind_param($teamStmt, "s", $deptCoord);
            mysqli_stmt_execute($teamStmt);
            $teamRes = mysqli_stmt_get_result($teamStmt);
            while ($row = mysqli_fetch_assoc($teamRes)) {
                $notifications[] = [
                    'id' => $row['id'],
                    'type' => 'new_team',
                    'icon' => 'ri-group-line',
                    'color' => '#0EA5E9',
                    'title' => 'New Team Registered',
                    'message' => '<strong>' . htmlspecialchars($row['team_name']) . '</strong> by ' . htmlspecialchars($row['leader_name']),
                    'time' => $row['created_at'],
                    'link' => 'teams.php'
                ];
            }
            mysqli_stmt_close($teamStmt);
        }

        // New student registrations in department (last 7 days)
        $newStudStmt = mysqli_prepare($conn, "
            SELECT id, name, reg_no, created_at
            FROM users
            WHERE department = ? AND role = 'student' AND created_at >= NOW() - INTERVAL 7 DAY
            ORDER BY created_at DESC
            LIMIT 5
        ");
        if ($newStudStmt) {
            mysqli_stmt_bind_param($newStudStmt, "s", $deptCoord);
            mysqli_stmt_execute($newStudStmt);
            $studRes = mysqli_stmt_get_result($newStudStmt);
            while ($row = mysqli_fetch_assoc($studRes)) {
                $notifications[] = [
                    'id' => $row['id'],
                    'type' => 'new_student',
                    'icon' => 'ri-user-add-line',
                    'color' => '#14B8A6',
                    'title' => 'New Student',
                    'message' => '<strong>' . htmlspecialchars($row['name']) . '</strong>' . ($row['reg_no'] ? ' (' . htmlspecialchars($row['reg_no']) . ')' : '') . ' registered',
                    'time' => $row['created_at'],
                    'link' => 'studentList.php'
                ];
            }
            mysqli_stmt_close($newStudStmt);
        }

    // =============================================
    // ROLE: Admin
    // =============================================
    } elseif ($userRole === 'admin') {

        // Pending projects across all departments
        $adminProjStmt = mysqli_query($conn, "
            SELECT p.id, p.title, p.created_at, u.name as student_name, p.department
            FROM projects p
            JOIN users u ON p.student_id = u.id
            WHERE p.status = 'pending'
            ORDER BY p.created_at DESC
            LIMIT 5
        ");
        if ($adminProjStmt) {
            while ($row = mysqli_fetch_assoc($adminProjStmt)) {
                $notifications[] = [
                    'id' => $row['id'],
                    'type' => 'admin_review',
                    'icon' => 'ri-shield-check-line',
                    'color' => '#8B5CF6',
                    'title' => 'Pending: ' . htmlspecialchars($row['department'] ?? 'N/A'),
                    'message' => '"' . htmlspecialchars($row['title']) . '" by ' . htmlspecialchars($row['student_name']),
                    'time' => $row['created_at'],
                    'link' => 'approvals.php'
                ];
            }
        }

        // New user registrations (last 7 days)
        $newUsersStmt = mysqli_query($conn, "
            SELECT id, name, role, department, created_at
            FROM users
            WHERE created_at >= NOW() - INTERVAL 7 DAY
            ORDER BY created_at DESC
            LIMIT 5
        ");
        if ($newUsersStmt) {
            while ($row = mysqli_fetch_assoc($newUsersStmt)) {
                $roleBadge = ucfirst($row['role']);
                $dept = $row['department'] ? ' — ' . htmlspecialchars($row['department']) : '';
                $notifications[] = [
                    'id' => $row['id'],
                    'type' => 'new_user',
                    'icon' => 'ri-user-add-line',
                    'color' => '#14B8A6',
                    'title' => 'New ' . $roleBadge . ' Joined',
                    'message' => '<strong>' . htmlspecialchars($row['name']) . '</strong>' . $dept,
                    'time' => $row['created_at'],
                    'link' => 'users.php'
                ];
            }
        }

        // New teams created (last 7 days)
        $adminTeamStmt = mysqli_query($conn, "
            SELECT t.id, t.team_name, t.department, t.created_at, u.name as leader_name
            FROM teams t
            JOIN users u ON t.leader_id = u.id
            WHERE t.created_at >= NOW() - INTERVAL 7 DAY
            ORDER BY t.created_at DESC
            LIMIT 5
        ");
        if ($adminTeamStmt) {
            while ($row = mysqli_fetch_assoc($adminTeamStmt)) {
                $notifications[] = [
                    'id' => $row['id'],
                    'type' => 'new_team',
                    'icon' => 'ri-group-line',
                    'color' => '#0EA5E9',
                    'title' => 'New Team: ' . htmlspecialchars($row['department'] ?? ''),
                    'message' => '<strong>' . htmlspecialchars($row['team_name']) . '</strong> by ' . htmlspecialchars($row['leader_name']),
                    'time' => $row['created_at'],
                    'link' => 'teams.php'
                ];
            }
        }

    // =============================================
    // ROLE: Student Affairs
    // =============================================
    } elseif ($userRole === 'studentaffairs') {

        // Pending projects across all departments
        $saProjStmt = mysqli_query($conn, "
            SELECT p.id, p.title, p.created_at, u.name as student_name, p.department
            FROM projects p
            JOIN users u ON p.student_id = u.id
            WHERE p.status = 'pending'
            ORDER BY p.created_at DESC
            LIMIT 5
        ");
        if ($saProjStmt) {
            while ($row = mysqli_fetch_assoc($saProjStmt)) {
                $notifications[] = [
                    'id' => $row['id'],
                    'type' => 'admin_review',
                    'icon' => 'ri-shield-check-line',
                    'color' => '#8B5CF6',
                    'title' => 'Pending: ' . htmlspecialchars($row['department'] ?? 'N/A'),
                    'message' => '"' . htmlspecialchars($row['title']) . '" by ' . htmlspecialchars($row['student_name']),
                    'time' => $row['created_at'],
                    'link' => 'approvals.php'
                ];
            }
        }

        // New user registrations (last 7 days)
        $saUsersStmt = mysqli_query($conn, "
            SELECT id, name, role, department, created_at
            FROM users
            WHERE created_at >= NOW() - INTERVAL 7 DAY
            ORDER BY created_at DESC
            LIMIT 5
        ");
        if ($saUsersStmt) {
            while ($row = mysqli_fetch_assoc($saUsersStmt)) {
                $roleBadge = ucfirst($row['role']);
                $dept = $row['department'] ? ' — ' . htmlspecialchars($row['department']) : '';
                $notifications[] = [
                    'id' => $row['id'],
                    'type' => 'new_user',
                    'icon' => 'ri-user-add-line',
                    'color' => '#14B8A6',
                    'title' => 'New ' . $roleBadge . ' Registered',
                    'message' => '<strong>' . htmlspecialchars($row['name']) . '</strong>' . $dept,
                    'time' => $row['created_at'],
                    'link' => 'users.php'
                ];
            }
        }

        // New teams created (last 7 days)
        $saTeamStmt = mysqli_query($conn, "
            SELECT t.id, t.team_name, t.department, t.created_at, u.name as leader_name
            FROM teams t
            JOIN users u ON t.leader_id = u.id
            WHERE t.created_at >= NOW() - INTERVAL 7 DAY
            ORDER BY t.created_at DESC
            LIMIT 5
        ");
        if ($saTeamStmt) {
            while ($row = mysqli_fetch_assoc($saTeamStmt)) {
                $notifications[] = [
                    'id' => $row['id'],
                    'type' => 'new_team',
                    'icon' => 'ri-group-line',
                    'color' => '#0EA5E9',
                    'title' => 'New Team: ' . htmlspecialchars($row['department'] ?? ''),
                    'message' => '<strong>' . htmlspecialchars($row['team_name']) . '</strong> by ' . htmlspecialchars($row['leader_name']),
                    'time' => $row['created_at'],
                    'link' => 'teams.php'
                ];
            }
        }

        // Overall registration stats as a summary notification
        $statsRes = mysqli_query($conn, "SELECT COUNT(*) as cnt FROM users WHERE role = 'student'");
        $totalStudents = $statsRes ? mysqli_fetch_assoc($statsRes)['cnt'] : 0;
        $teamCountRes = mysqli_query($conn, "SELECT COUNT(*) as cnt FROM teams");
        $totalTeams = $teamCountRes ? mysqli_fetch_assoc($teamCountRes)['cnt'] : 0;
        $projCountRes = mysqli_query($conn, "SELECT COUNT(*) as cnt FROM projects");
        $totalProjects = $projCountRes ? mysqli_fetch_assoc($projCountRes)['cnt'] : 0;

        $notifications[] = [
            'id' => 0,
            'type' => 'stats',
            'icon' => 'ri-bar-chart-box-line',
            'color' => '#6366F1',
            'title' => 'Platform Overview',
            'message' => $totalStudents . ' students • ' . $totalTeams . ' teams • ' . $totalProjects . ' projects',
            'time' => date('Y-m-d H:i:s'),
            'link' => 'analytics.php'
        ];
    }

    // Final count
    $notificationCount = count($notifications);
    if ($notificationCount > 0) {
        $notificationTitle = $notificationCount . ' notification(s)';
    }
}

// Sort all notifications by time (newest first)
usort($notifications, function($a, $b) {
    return strtotime($b['time']) - strtotime($a['time']);
});

// Helper: relative time
if (!function_exists('notifTimeAgo')) {
    function notifTimeAgo($datetime) {
        $now = new DateTime();
        $past = new DateTime($datetime);
        $diff = $now->diff($past);
        if ($diff->y > 0) return $diff->y . 'y ago';
        if ($diff->m > 0) return $diff->m . 'mo ago';
        if ($diff->d > 0) return $diff->d . 'd ago';
        if ($diff->h > 0) return $diff->h . 'h ago';
        if ($diff->i > 0) return $diff->i . 'm ago';
        return 'Just now';
    }
}
?>
<header class="dashboard-header">
    <div class="header-left">
        <button class="mobile-toggle" onclick="toggleSidebar()">
            <i class="ri-menu-line"></i>
        </button>
        <h1>
            <?php echo isset($pageTitle) ? htmlspecialchars($pageTitle) : 'Dashboard'; ?>
        </h1>
    </div>
    <div class="header-right">
        <div class="header-search">
            <i class="ri-search-line"></i>
            <input type="text" placeholder="Search...">
        </div>

        <div class="header-icon notification-bell-wrapper" title="<?php echo htmlspecialchars($notificationTitle); ?>" onclick="toggleNotifDropdown(event)">
            <i class="ri-notification-3-line"></i>
            <?php if ($notificationCount > 0): ?>
                <span class="notif-badge"><?php echo $notificationCount > 9 ? '9+' : $notificationCount; ?></span>
            <?php endif; ?>

            <!-- Notification Dropdown -->
            <div class="notif-dropdown" id="notifDropdown">
                <div class="notif-dropdown-header">
                    <span class="notif-dropdown-title">Notifications</span>
                    <?php if ($notificationCount > 0): ?>
                        <span class="notif-count-badge"><?php echo $notificationCount; ?> new</span>
                    <?php endif; ?>
                </div>

                <div class="notif-dropdown-body">
                    <?php if (empty($notifications)): ?>
                        <div class="notif-empty">
                            <i class="ri-notification-off-line"></i>
                            <p>No new notifications</p>
                            <span>You're all caught up!</span>
                        </div>
                    <?php else: ?>
                        <?php foreach ($notifications as $notif): ?>
                            <a href="<?php echo htmlspecialchars($notif['link']); ?>" class="notif-item" data-notif-type="<?php echo htmlspecialchars($notif['type']); ?>">
                                <div class="notif-item-icon" style="background: <?php echo $notif['color']; ?>15; color: <?php echo $notif['color']; ?>;">
                                    <i class="<?php echo htmlspecialchars($notif['icon']); ?>"></i>
                                </div>
                                <div class="notif-item-content">
                                    <div class="notif-item-title"><?php echo $notif['title']; ?></div>
                                    <div class="notif-item-msg"><?php echo $notif['message']; ?></div>
                                    <div class="notif-item-time">
                                        <i class="ri-time-line"></i>
                                        <?php echo notifTimeAgo($notif['time']); ?>
                                    </div>
                                </div>
                                <div class="notif-item-dot"></div>
                            </a>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <?php if (!empty($notifications)): ?>
                    <div class="notif-dropdown-footer">
                        <a href="<?php
                            if ($userRole === 'student') echo 'announcements.php';
                            elseif ($userRole === 'departmentcoordinator') echo 'reviewApprove.php';
                            elseif ($userRole === 'admin') echo 'sparkAdmin.php';
                            elseif ($userRole === 'studentaffairs') echo 'studentAffairs.php';
                            else echo 'announcements.php';
                        ?>">
                            View all notifications <i class="ri-arrow-right-s-line"></i>
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="user-profile" onclick="toggleUserDropdown(event)">
            <div class="user-avatar">
                <?php echo $userInitials; ?>
            </div>
            <div class="user-info">
                <span class="user-name">
                    <?php echo htmlspecialchars($userName); ?>
                </span>
                <span class="user-role">
                    <?php echo ucfirst($userRole); ?>
                </span>
            </div>

            <!-- User Dropdown -->
            <div class="user-dropdown" id="userDropdown">
                <div class="dropdown-header">
                    <span>
                        <?php echo htmlspecialchars($userName); ?>
                    </span>
                    <small>
                        <?php echo ucfirst($userRole); ?>
                    </small>
                </div>
                <a href="profile.php"><i class="ri-user-line"></i> Profile</a>
                <a href="settings.php"><i class="ri-settings-line"></i> Settings</a>
                <div class="dropdown-divider"></div>
                <a href="logout.php" style="color: #ef4444;"><i class="ri-logout-box-line"></i> Logout</a>
            </div>
        </div>
    </div>
</header>