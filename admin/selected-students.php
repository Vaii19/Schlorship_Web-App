<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit();
}

// === FILTERS / SEARCH / SORT ===
$search     = trim($_GET['search'] ?? '');
$type_filter = $_GET['type'] ?? 'all';
$sort       = $_GET['sort'] ?? 'newest';

$where  = ["status = 'approved'"];
$params = [];

if ($search !== '') {
    $like = "%$search%";
    $where[] = "(full_name LIKE ? OR email LIKE ? OR phone LIKE ? OR chin_ethnicity LIKE ?)";
    $params = [$like, $like, $like, $like];
}

if ($type_filter !== 'all') {
    $where[] = "scholarship_type = ?";
    $params[] = $type_filter;
}

$orderBy = match ($sort) {
    'name_asc'   => "ORDER BY full_name ASC",
    'name_desc'  => "ORDER BY full_name DESC",
    'oldest'     => "ORDER BY created_at ASC",
    default      => "ORDER BY created_at DESC",
};

$sql = "SELECT * FROM applications";
if ($where) {
    $sql .= " WHERE " . implode(" AND ", $where);
}
$sql .= " $orderBy";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$selected = $stmt->fetchAll();

// Statistics
$total_selected = count($selected);
$total_awarded  = array_sum(array_column($selected, 'scholarship_amount')) ?? 0;
$avg_award      = $total_selected > 0 ? $total_awarded / $total_selected : 0;

// Unread messages count
$msg_stmt = $pdo->query("SELECT COUNT(*) as unread FROM contact_messages WHERE is_read = 0");
$unread_count = $msg_stmt->fetch()['unread'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Selected Students - CSI EduAid Admin</title>
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
            <a href="dashboard.php"          class="nav-link"><i class="bi bi-speedometer2 me-3"></i> Dashboard</a>
            <a href="all-applicants.php"     class="nav-link"><i class="bi bi-people me-3"></i> All Applicants</a>
            <a href="selected-students.php"  class="nav-link active">
                <i class="bi bi-check-circle me-3"></i> Selected Students
            </a>
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
        <h1 class="fw-bold mb-4" style="color: #41514a;">Selected / Awarded Students</h1>

        <!-- Quick Statistics -->
        <div class="row g-4 mb-5">
            <div class="col-md-4">
                <div class="stat-card bg-white p-4 text-center">
                    <i class="bi bi-people-fill fs-1 text-success mb-3 d-block"></i>
                    <h5 class="text-muted mb-1">Total Awarded Students</h5>
                    <h2 class="fw-bold text-success"><?= number_format($total_selected) ?></h2>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card bg-white p-4 text-center">
                    <i class="bi bi-currency-dollar fs-1 text-primary mb-3 d-block"></i>
                    <h5 class="text-muted mb-1">Total Scholarship Funds</h5>
                    <h2 class="fw-bold text-primary">$<?= number_format($total_awarded, 2) ?></h2>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card bg-white p-4 text-center">
                    <i class="bi bi-graph-up fs-1 text-info mb-3 d-block"></i>
                    <h5 class="text-muted mb-1">Average Award per Student</h5>
                    <h2 class="fw-bold text-info">$<?= number_format($avg_award, 2) ?></h2>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="filter-bar p-4 mb-4">
            <form method="get" class="row g-3 align-items-end">
                <div class="col-md-5 col-lg-4">
                    <label class="form-label fw-bold">Search Student</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" class="form-control"
                               placeholder="Name, email, phone or ethnicity..."
                               value="<?= htmlspecialchars($search) ?>">
                    </div>
                </div>

                <div class="col-md-3 col-lg-3">
                    <label class="form-label fw-bold">Scholarship Type</label>
                    <select name="type" class="form-select">
                        <option value="all"     <?= $type_filter === 'all'     ? 'selected' : '' ?>>All Types</option>
                        <option value="Full"    <?= $type_filter === 'Full'    ? 'selected' : '' ?>>Full Scholarship</option>
                        <option value="Half"    <?= $type_filter === 'Half'    ? 'selected' : '' ?>>Half Scholarship</option>
                        <option value="Partial" <?= $type_filter === 'Partial' ? 'selected' : '' ?>>Partial Scholarship</option>
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
                    <a href="selected-students.php" class="btn btn-outline-secondary px-4 flex-fill">
                        <i class="bi bi-x-circle me-1"></i> Clear
                    </a>
                </div>
            </form>
        </div>

        <?php if (empty($selected)): ?>
            <div class="alert alert-info text-center py-5">
                <i class="bi bi-search fs-3 me-2"></i>
                No awarded students match the current filters.
            </div>
        <?php else: ?>
            <div class="card shadow">
                <div class="card-header d-flex justify-content-between align-items-center" style="background-color: #41514a; color: white;">
                    <h5 class="mb-0">Awarded Students (<?= number_format(count($selected)) ?> records)</h5>
                    <small>Click View to see full details</small>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 align-middle">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Student Name</th>
                                    <th>DOB</th>
                                    <th>Phone</th>
                                    <th>Selected On</th>
                                    <th>Amount</th>
                                    <th>Type</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($selected as $student): ?>
                                    <tr>
                                        <td>#<?= $student['id'] ?></td>
                                        <td class="fw-medium"><?= htmlspecialchars($student['full_name']) ?></td>
                                        <td><?= htmlspecialchars($student['dob'] ?? '—') ?></td>
                                        <td><?= htmlspecialchars($student['phone'] ?? '—') ?></td>
                                        <td><?= date('d M Y', strtotime($student['created_at'])) ?></td>
                                        <td>
                                            <?php if (!empty($student['scholarship_amount'])): ?>
                                                <strong class="text-success">$<?= number_format($student['scholarship_amount'], 2) ?></strong>
                                            <?php else: ?>
                                                <span class="text-muted">—</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="badge <?= $student['scholarship_type']==='Full' ? 'bg-success' : ($student['scholarship_type']==='Half' ? 'bg-info' : 'bg-secondary') ?>">
                                                <?= htmlspecialchars($student['scholarship_type'] ?? 'Full') ?>
                                            </span>
                                        </td>
                                        <td>
                                            <a href="view.php?id=<?= $student['id'] ?>" class="btn btn-sm btn-warning">
                                                <i class="bi bi-eye"></i> View
                                            </a>
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