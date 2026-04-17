<?php
/**
 * CSI EduAid - Messages Page
 * Updated Actions Column to match all-applicants.php style
 */

session_start();
require_once '../config/db.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit();
}

// Handle POST actions (mark as read)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'mark_read') {
        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0) {
            $pdo->prepare("UPDATE contact_messages SET is_read = 1 WHERE id = ?")
                ->execute([$id]);
        }
    }
    header("Location: messages.php?" . http_build_query($_GET));
    exit();
}

// === FILTERS / SEARCH / SORT ===
$search       = trim($_GET['search'] ?? '');
$status_filter = $_GET['status'] ?? 'all';
$sort         = $_GET['sort'] ?? 'newest';

$where  = [];
$params = [];

if ($search !== '') {
    $like = "%$search%";
    $where[] = "(full_name LIKE ? OR email LIKE ? OR phone LIKE ? OR message LIKE ?)";
    $params = [$like, $like, $like, $like];
}

if ($status_filter === 'unread') {
    $where[] = "is_read = 0";
} elseif ($status_filter === 'read') {
    $where[] = "is_read = 1";
}

$orderBy = match ($sort) {
    'oldest'     => "ORDER BY created_at ASC",
    'name_asc'   => "ORDER BY full_name ASC",
    'name_desc'  => "ORDER BY full_name DESC",
    default      => "ORDER BY created_at DESC",
};

$sql = "SELECT * FROM contact_messages";
if ($where) {
    $sql .= " WHERE " . implode(" AND ", $where);
}
$sql .= " $orderBy";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$messages = $stmt->fetchAll();

$total_messages = count($messages);
$unread_count   = count(array_filter($messages, fn($m) => $m['is_read'] == 0));

// Handle legacy GET actions
if (isset($_GET['mark_all_read'])) {
    if ($unread_count > 0) {
        $pdo->prepare("UPDATE contact_messages SET is_read = 1 WHERE is_read = 0")->execute();
    }
    header("Location: messages.php?" . http_build_query(array_diff_key($_GET, ['mark_all_read'=>1])));
    exit();
}

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $pdo->prepare("DELETE FROM contact_messages WHERE id = ?")->execute([$id]);
    header("Location: messages.php?" . http_build_query(array_diff_key($_GET, ['delete'=>1])));
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages - CSI EduAid Admin</title>
    <link rel="icon" type="image/png" href="../img/logoo.jpg">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <style>
        body { background-color: #f8f9fa; font-family: system-ui, -apple-system, sans-serif; }
        .topbar { 
            background-color: #41514a; 
            color: white; 
            position: sticky; 
            top: 0; 
            z-index: 1030; 
        }
        .sidebar {
            background-color: #ffffff;
            box-shadow: 2px 0 10px rgba(0,0,0,0.08);
            height: 100vh;
            position: fixed;
            width: 260px;
            transition: all 0.3s;
            overflow-y: auto;
        }
        .sidebar.collapsed { margin-left: -260px; }
        .main-content { 
            margin-left: 260px; 
            transition: margin-left 0.3s; 
            padding: 80px 30px 30px; 
        }
        .main-content.collapsed { margin-left: 0; }
        .nav-link {
            color: #000000;
            padding: 12px 24px;
            transition: all 0.2s;
        }
        .nav-link:hover, .nav-link.active {
            background-color: #f1f5f1;
            color: #41514a;
            font-weight: 600;
        }
        .nav-link.text-danger:hover { color: #dc3545 !important; }
        .stat-card {
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.06);
            transition: transform 0.2s;
        }
        .stat-card:hover { transform: translateY(-3px); }
        .table thead { background-color: #41514a; color: white; }
        .sidebar-logo-container {
            padding: 30px 20px 20px;
            text-align: center;
            border-bottom: 1px solid #eee;
        }
        .sidebar-logo {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 50%;
            border: 4px solid #41514a;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        .sidebar-title {
            margin-top: 15px;
            font-size: 1.4rem;
            font-weight: 700;
            color: #41514a;
        }
        .sidebar-subtitle { font-size: 0.9rem; color: #6c757d; }
        tr.unread-row { background-color: #e3f2fd; font-weight: 500; }
        tr:hover { background-color: #f8f9fa; }
        .filter-bar {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.06);
        }
        .btn-group-sm .btn {
            padding: 6px 10px;
        }
    </style>
</head>
<body>

<!-- Top Bar -->
<nav class="topbar py-3">
    <div class="container-fluid d-flex align-items-center px-4">
        <button id="sidebarToggle" class="btn btn-light me-3">
            <i class="bi bi-list fs-4"></i>
        </button>
        <h4 class="mb-0 fw-bold">CSI EduAid Admin Dashboard</h4>
    </div>
</nav>

<div class="d-flex">
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-logo-container">
            <img src="../img/logoo.jpg" alt="CSI EduAid Logo" class="sidebar-logo">
            <h5 class="sidebar-title mt-3">CSI EduAid</h5>
            <p class="sidebar-subtitle">Scholarship Management System</p>
        </div>

        <div class="mt-3">
            <a href="dashboard.php" class="nav-link"><i class="bi bi-speedometer2 me-3"></i> Dashboard</a>
            <a href="all-applicants.php" class="nav-link"><i class="bi bi-people me-3"></i> All Applicants</a>
            <a href="selected-students.php" class="nav-link"><i class="bi bi-check-circle me-3"></i> Selected Students</a>
            <a href="messages.php" class="nav-link active">
                <i class="bi bi-envelope me-3"></i> Messages
                <?php if ($unread_count > 0): ?>
                    <span class="badge bg-danger ms-2"><?= $unread_count ?></span>
                <?php endif; ?>
            </a>
            <a href="settings.php" class="nav-link"><i class="bi bi-gear me-3"></i> Settings</a>
            <a href="report.php" class="nav-link"><i class="bi bi-clipboard-data me-3"></i> Report</a>
        </div>

        <hr class="mx-3 my-3">
        <a href="../logout.php" class="nav-link text-danger">
            <i class="bi bi-box-arrow-right me-3"></i> Logout
        </a>
    </div>

    <!-- Main Content -->
    <div class="main-content flex-grow-1" id="mainContent">
        <h1 class="fw-bold mb-4" style="color: #41514a;">Contact Messages</h1>

        <!-- Quick Stats -->
        <div class="row g-4 mb-5">
            <div class="col-md-4">
                <div class="stat-card bg-white p-4 text-center">
                    <i class="bi bi-envelope-fill fs-1 text-primary mb-3 d-block"></i>
                    <h5 class="text-muted mb-1">Total Messages</h5>
                    <h2 class="fw-bold text-primary"><?= number_format($total_messages) ?></h2>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card bg-white p-4 text-center">
                    <i class="bi bi-envelope-exclamation fs-1 text-warning mb-3 d-block"></i>
                    <h5 class="text-muted mb-1">Unread Messages</h5>
                    <h2 class="fw-bold text-warning"><?= number_format($unread_count) ?></h2>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card bg-white p-4 text-center">
                    <i class="bi bi-envelope-check fs-1 text-success mb-3 d-block"></i>
                    <h5 class="text-muted mb-1">Read Messages</h5>
                    <h2 class="fw-bold text-success"><?= number_format($total_messages - $unread_count) ?></h2>
                </div>
            </div>
        </div>

        <!-- Filter Bar -->
        <div class="filter-bar p-4 mb-4">
            <form method="get" class="row g-3 align-items-end">
                <div class="col-md-5 col-lg-4">
                    <label class="form-label fw-bold">Search Messages</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" class="form-control"
                               placeholder="Name, email, phone or message content..."
                               value="<?= htmlspecialchars($search) ?>">
                    </div>
                </div>

                <div class="col-md-3 col-lg-3">
                    <label class="form-label fw-bold">Read Status</label>
                    <select name="status" class="form-select">
                        <option value="all"   <?= $status_filter === 'all'   ? 'selected' : '' ?>>All Messages</option>
                        <option value="unread" <?= $status_filter === 'unread' ? 'selected' : '' ?>>Unread Only</option>
                        <option value="read"   <?= $status_filter === 'read'   ? 'selected' : '' ?>>Read Only</option>
                    </select>
                </div>

                <div class="col-md-2 col-lg-2">
                    <label class="form-label fw-bold">Sort by</label>
                    <select name="sort" class="form-select">
                        <option value="newest"   <?= $sort === 'newest'   ? 'selected' : '' ?>>Newest first</option>
                        <option value="oldest"   <?= $sort === 'oldest'   ? 'selected' : '' ?>>Oldest first</option>
                        <option value="name_asc" <?= $sort === 'name_asc' ? 'selected' : '' ?>>Name A–Z</option>
                        <option value="name_desc"<?= $sort === 'name_desc'? 'selected' : '' ?>>Name Z–A</option>
                    </select>
                </div>

                <div class="col-md-2 col-lg-3 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-primary px-4 flex-fill">
                        <i class="bi bi-funnel-fill me-1"></i> Filter
                    </button>
                    <a href="messages.php" class="btn btn-outline-secondary px-4 flex-fill">
                        <i class="bi bi-x-circle me-1"></i> Clear
                    </a>
                </div>
            </form>
        </div>

        <!-- Messages Table -->
        <?php if (empty($messages)): ?>
            <div class="alert alert-info text-center py-5">
                <i class="bi bi-envelope-slash fs-3 me-2"></i>
                No messages found matching the current filters.
            </div>
        <?php else: ?>
            <div class="card shadow">
                <div class="card-header d-flex justify-content-between align-items-center" style="background-color: #41514a; color: white;">
                    <h5 class="mb-0">Messages (<?= number_format(count($messages)) ?> records)</h5>
                    <?php if ($unread_count > 0): ?>
                        <a href="?mark_all_read=1&<?= http_build_query(array_diff_key($_GET, ['mark_all_read'=>1])) ?>" 
                           class="btn btn-sm btn-light"
                           onclick="return confirm('Mark all unread messages as read?');">
                            <i class="bi bi-check-all me-1"></i> Mark All as Read
                        </a>
                    <?php endif; ?>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 align-middle">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Received</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($messages as $msg): ?>
                                    <tr class="<?= $msg['is_read'] == 0 ? 'unread-row' : '' ?>">
                                        <td>#<?= $msg['id'] ?></td>
                                        <td class="fw-medium"><?= htmlspecialchars($msg['full_name']) ?></td>
                                        <td><?= htmlspecialchars($msg['email']) ?></td>
                                        <td><?= htmlspecialchars($msg['phone'] ?? '—') ?></td>
                                        <td><?= date('d M Y • H:i', strtotime($msg['created_at'])) ?></td>
                                        <td>
                                            <?php if ($msg['is_read'] == 0): ?>
                                                <span class="badge bg-warning text-dark">Unread</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">Read</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <?php if ($msg['is_read'] == 0): ?>
                                                    <form method="post" class="d-inline">
                                                        <input type="hidden" name="action" value="mark_read">
                                                        <input type="hidden" name="id" value="<?= $msg['id'] ?>">
                                                        <button type="submit" class="btn btn-success" title="Mark as Read">
                                                            <i class="bi bi-check"></i>
                                                        </button>
                                                    </form>
                                                <?php endif; ?>

                                                <a href="view_message.php?id=<?= $msg['id'] ?>" 
                                                   class="btn btn-warning" title="View Full Message">
                                                    <i class="bi bi-eye"></i>
                                                </a>

                                                <a href="?delete=<?= $msg['id'] ?>&<?= http_build_query(array_diff_key($_GET, ['delete'=>1])) ?>" 
                                                   class="btn btn-danger"
                                                   onclick="return confirm('Delete this message permanently? This cannot be undone.');"
                                                   title="Delete Message">
                                                    <i class="bi bi-trash"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const toggleBtn = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('sidebar');
    const mainContent = document.getElementById('mainContent');

    toggleBtn.addEventListener('click', () => {
        sidebar.classList.toggle('collapsed');
        mainContent.classList.toggle('collapsed');
    });
</script>
</body>
</html>