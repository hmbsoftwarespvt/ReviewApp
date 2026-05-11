<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title) ?> - AppTrust Platform</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        .sidebar {
            min-height: 100vh;
            background-color: #212529;
        }
        .sidebar .nav-link {
            color: #adb5bd;
        }
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            color: #fff;
            background-color: #495057;
        }
        .user-card {
            transition: transform 0.2s;
        }
        .user-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .search-section {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1.5rem;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-2 d-md-block sidebar">
                <div class="position-sticky pt-3">
                    <h5 class="text-white px-3 mb-3">AppTrust Admin</h5>
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link" href="<?= base_url('admin/dashboard') ?>">
                                <i class="bi bi-speedometer2"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= base_url('admin/apps') ?>">
                                <i class="bi bi-app"></i> Apps
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= base_url('admin/reviews') ?>">
                                <i class="bi bi-star"></i> Reviews
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= base_url('admin/scam-reports') ?>">
                                <i class="bi bi-exclamation-triangle"></i> Scam Reports
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="<?= base_url('admin/users') ?>">
                                <i class="bi bi-people"></i> Users
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= base_url('admin/blog') ?>">
                                <i class="bi bi-newspaper"></i> Blog
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= base_url('admin/settings') ?>">
                                <i class="bi bi-gear"></i> Settings
                            </a>
                        </li>
                        <li class="nav-item mt-3">
                            <a class="nav-link" href="<?= base_url('/') ?>">
                                <i class="bi bi-house"></i> View Site
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= base_url('auth/logout') ?>">
                                <i class="bi bi-box-arrow-right"></i> Logout
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- Main content -->
            <main class="col-md-10 ms-sm-auto px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2"><?= esc($title) ?></h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <span class="badge bg-primary me-2">Total: <?= number_format($pagination['total']) ?></span>
                    </div>
                </div>

                <!-- Flash Messages -->
                <?php if (session()->getFlashdata('success')): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle"></i> <?= session()->getFlashdata('success') ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-circle"></i> <?= session()->getFlashdata('error') ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <!-- Search Section -->
                <div class="search-section">
                    <form method="GET" action="<?= base_url('admin/users') ?>" class="row g-3">
                        <div class="col-md-10">
                            <label for="search" class="form-label">Search Users</label>
                            <input type="text" name="search" id="search" class="form-control" 
                                   placeholder="Search by username or email..." 
                                   value="<?= esc($search) ?>">
                        </div>

                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-search"></i> Search
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Users List -->
                <?php if (empty($users)): ?>
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> No users found<?= !empty($search) ? ' matching your search' : '' ?>.
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Username</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Status</th>
                                    <th>Reviews</th>
                                    <th>Reports</th>
                                    <th>Registered</th>
                                    <th>Last Login</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($users as $user): ?>
                                    <tr>
                                        <td><?= esc($user['id']) ?></td>
                                        <td>
                                            <strong><?= esc($user['username']) ?></strong>
                                            <?php if ($user['email_verified']): ?>
                                                <i class="bi bi-patch-check-fill text-success" title="Email Verified"></i>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= esc($user['email']) ?></td>
                                        <td>
                                            <span class="badge bg-<?= $user['role'] === 'admin' ? 'danger' : 'secondary' ?>">
                                                <?= ucfirst(esc($user['role'])) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?= $user['status'] === 'active' ? 'success' : ($user['status'] === 'suspended' ? 'warning' : 'dark') ?>">
                                                <?= ucfirst(esc($user['status'])) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-info"><?= number_format($user['review_count']) ?></span>
                                        </td>
                                        <td>
                                            <span class="badge bg-warning"><?= number_format($user['scam_report_count']) ?></span>
                                        </td>
                                        <td>
                                            <small><?= date('M d, Y', strtotime($user['created_at'])) ?></small>
                                        </td>
                                        <td>
                                            <?php if ($user['last_login']): ?>
                                                <small><?= date('M d, Y', strtotime($user['last_login'])) ?></small>
                                            <?php else: ?>
                                                <small class="text-muted">Never</small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm" role="group">
                                                <a href="<?= base_url('admin/users/view/' . $user['id']) ?>" 
                                                   class="btn btn-outline-primary" 
                                                   title="View Details">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                
                                                <?php if ($user['role'] !== 'admin'): ?>
                                                    <?php if ($user['status'] === 'active'): ?>
                                                        <form method="POST" action="<?= base_url('admin/users/suspend/' . $user['id']) ?>" class="d-inline">
                                                            <?= csrf_field() ?>
                                                            <button type="submit" 
                                                                    class="btn btn-outline-warning" 
                                                                    title="Suspend User"
                                                                    onclick="return confirm('Suspend this user? They will not be able to login.')">
                                                                <i class="bi bi-pause-circle"></i>
                                                            </button>
                                                        </form>
                                                    <?php elseif ($user['status'] === 'suspended'): ?>
                                                        <form method="POST" action="<?= base_url('admin/users/reactivate/' . $user['id']) ?>" class="d-inline">
                                                            <?= csrf_field() ?>
                                                            <button type="submit" 
                                                                    class="btn btn-outline-success" 
                                                                    title="Reactivate User"
                                                                    onclick="return confirm('Reactivate this user? They will be able to login again.')">
                                                                <i class="bi bi-play-circle"></i>
                                                            </button>
                                                        </form>
                                                    <?php endif; ?>
                                                    
                                                    <form method="POST" action="<?= base_url('admin/users/delete/' . $user['id']) ?>" class="d-inline">
                                                        <?= csrf_field() ?>
                                                        <button type="submit" 
                                                                class="btn btn-outline-danger" 
                                                                title="Delete User"
                                                                onclick="return confirm('Permanently delete this user? Their content will be anonymized. This action cannot be undone.')">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </form>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <?php if ($pagination['total_pages'] > 1): ?>
                        <nav aria-label="User pagination">
                            <ul class="pagination justify-content-center">
                                <!-- Previous Page -->
                                <li class="page-item <?= $pagination['current_page'] <= 1 ? 'disabled' : '' ?>">
                                    <a class="page-link" href="<?= base_url('admin/users?' . http_build_query(['search' => $search, 'page' => $pagination['current_page'] - 1])) ?>">
                                        Previous
                                    </a>
                                </li>

                                <!-- Page Numbers -->
                                <?php
                                $start = max(1, $pagination['current_page'] - 2);
                                $end = min($pagination['total_pages'], $pagination['current_page'] + 2);
                                ?>

                                <?php if ($start > 1): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="<?= base_url('admin/users?' . http_build_query(['search' => $search, 'page' => 1])) ?>">1</a>
                                    </li>
                                    <?php if ($start > 2): ?>
                                        <li class="page-item disabled"><span class="page-link">...</span></li>
                                    <?php endif; ?>
                                <?php endif; ?>

                                <?php for ($i = $start; $i <= $end; $i++): ?>
                                    <li class="page-item <?= $i === $pagination['current_page'] ? 'active' : '' ?>">
                                        <a class="page-link" href="<?= base_url('admin/users?' . http_build_query(['search' => $search, 'page' => $i])) ?>">
                                            <?= $i ?>
                                        </a>
                                    </li>
                                <?php endfor; ?>

                                <?php if ($end < $pagination['total_pages']): ?>
                                    <?php if ($end < $pagination['total_pages'] - 1): ?>
                                        <li class="page-item disabled"><span class="page-link">...</span></li>
                                    <?php endif; ?>
                                    <li class="page-item">
                                        <a class="page-link" href="<?= base_url('admin/users?' . http_build_query(['search' => $search, 'page' => $pagination['total_pages']])) ?>">
                                            <?= $pagination['total_pages'] ?>
                                        </a>
                                    </li>
                                <?php endif; ?>

                                <!-- Next Page -->
                                <li class="page-item <?= $pagination['current_page'] >= $pagination['total_pages'] ? 'disabled' : '' ?>">
                                    <a class="page-link" href="<?= base_url('admin/users?' . http_build_query(['search' => $search, 'page' => $pagination['current_page'] + 1])) ?>">
                                        Next
                                    </a>
                                </li>
                            </ul>
                        </nav>
                    <?php endif; ?>
                <?php endif; ?>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
