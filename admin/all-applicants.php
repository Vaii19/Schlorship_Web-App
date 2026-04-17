<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit();
}

// === SEARCH & FILTER / SORT LOGIC ===
$search = trim($_GET['search'] ?? '');
$sort   = $_GET['sort'] ?? 'newest';

$where  = [];
$params = [];

if ($search !== '') {
    $like = "%$search%";
    $where[] = "(full_name LIKE ? OR email LIKE ? OR phone LIKE ? OR chin_ethnicity LIKE ?)";
    $params = [$like, $like, $like, $like];
}

// Sort
$orderBy = match ($sort) {
    'name_asc'   => "ORDER BY full_name ASC",
    'name_desc'  => "ORDER BY full_name DESC",
    'oldest'     => "ORDER BY created_at ASC",
    'id_asc'     => "ORDER BY id ASC",
    'id_desc'    => "ORDER BY id DESC",
    default      => "ORDER BY created_at DESC",
};

// Query
$sql = "SELECT * FROM applications";
if ($where) {
    $sql .= " WHERE " . implode(" AND ", $where);
}
$sql .= " $orderBy";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$applications = $stmt->fetchAll();

// Unread messages
$msg_stmt = $pdo->query("SELECT COUNT(*) as unread FROM contact_messages WHERE is_read = 0");
$unread_count = $msg_stmt->fetch()['unread'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Applicants - CSI EduAid Admin</title>
    <link rel="icon" type="image/png" href="../img/logoo.jpg">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <style>
        body { background-color: #f8f9fa; }
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
        tr:hover { background-color: #f8f9fa; }
        .filter-bar {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.06);
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
            <a href="all-applicants.php" class="nav-link active"><i class="bi bi-people me-3"></i> All Applicants</a>
            <a href="selected-students.php" class="nav-link"><i class="bi bi-check-circle me-3"></i> Selected Students</a>
            <a href="messages.php" class="nav-link">
                <i class="bi bi-envelope me-3"></i> Messages
                <?php if ($unread_count > 0): ?>
                    <span class="badge bg-danger ms-2"><?= $unread_count ?></span>
                <?php endif; ?>
            </a>
            <a href="settings.php" class="nav-link"><i class="bi bi-gear me-3"></i> Settings</a>
            <a href="report.php" class="nav-link"><i class="bi bi-clipboard-data me-3"></i> Report</a>
        </div>

        <!-- Logout moved here -->
        <hr class="mx-3 my-3">
        <a href="../logout.php" class="nav-link text-danger">
            <i class="bi bi-box-arrow-right me-3"></i> Logout
        </a>
    </div>

    <!-- Main Content -->
    <div class="main-content flex-grow-1" id="mainContent">
        <h1 class="fw-bold mb-4" style="color: #41514a;">All Applicants</h1>

        <!-- Filter Bar -->
        <div class="filter-bar p-4 mb-4">
            <form method="get" class="row g-3 align-items-end">
                <div class="col-md-5 col-lg-4">
                    <label class="form-label fw-bold">Search Applicants</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" class="form-control" 
                               placeholder="Name, email, phone or ethnicity..." 
                               value="<?= htmlspecialchars($search) ?>">
                    </div>
                </div>

                <div class="col-md-4 col-lg-3">
                    <label class="form-label fw-bold">Sort by</label>
                    <select name="sort" class="form-select">
                        <option value="newest"    <?= $sort === 'newest'    ? 'selected' : '' ?>>Newest first</option>
                        <option value="oldest"    <?= $sort === 'oldest'    ? 'selected' : '' ?>>Oldest first</option>
                        <option value="name_asc"  <?= $sort === 'name_asc'  ? 'selected' : '' ?>>Name A–Z</option>
                        <option value="name_desc" <?= $sort === 'name_desc' ? 'selected' : '' ?>>Name Z–A</option>
                        <option value="id_asc"    <?= $sort === 'id_asc'    ? 'selected' : '' ?>>ID ascending</option>
                        <option value="id_desc"   <?= $sort === 'id_desc'   ? 'selected' : '' ?>>ID descending</option>
                    </select>
                </div>

                <div class="col-md-3 col-lg-5 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-primary px-4 flex-fill">
                        <i class="bi bi-funnel-fill me-1"></i> Apply
                    </button>
                    <a href="all-applicants.php" class="btn btn-outline-secondary px-4 flex-fill">
                        <i class="bi bi-x-circle me-1"></i> Clear
                    </a>
                </div>
            </form>
        </div>

        <?php if (empty($applications)): ?>
            <div class="alert alert-info text-center py-5">
                <i class="bi bi-search fs-3 me-2"></i>
                No applications found matching your criteria.
            </div>
        <?php else: ?>
            <div class="card shadow">
                <div class="card-header d-flex justify-content-between align-items-center" style="background-color: #41514a; color: white;">
                    <h5 class="mb-0">Applications (<?= number_format(count($applications)) ?> found)</h5>
                    <small>All actions available per record</small>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Full Name</th>
                                    <th>Chin Ethnicity</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Status</th>
                                    <th>Submitted</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($applications as $app): ?>
                                    <tr>
                                        <td>#<?= $app['id'] ?></td>
                                        <td><?= htmlspecialchars($app['full_name']) ?></td>
                                        <td><?= htmlspecialchars($app['chin_ethnicity'] ?? '—') ?></td>
                                        <td><?= htmlspecialchars($app['email']) ?></td>
                                        <td><?= htmlspecialchars($app['phone'] ?? '—') ?></td>
                                        <td>
                                            <span class="badge <?= $app['status']==='approved'?'bg-success':($app['status']==='rejected'?'bg-danger':'bg-warning text-dark') ?>">
                                                <?= ucfirst($app['status'] ?: 'Pending') ?>
                                            </span>
                                        </td>
                                        <td><?= date('d M Y', strtotime($app['created_at'])) ?></td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="view.php?id=<?= $app['id'] ?>" class="btn btn-warning">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <a href="update.php?id=<?= $app['id'] ?>" class="btn btn-primary">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <a href="delete.php?id=<?= $app['id'] ?>" class="btn btn-danger"
                                                   onclick="return confirm('Delete this application permanently?');">
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