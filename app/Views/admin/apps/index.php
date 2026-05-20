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
        .badge-trust-high {
            background-color: #198754;
        }
        .badge-trust-medium {
            background-color: #ffc107;
            color: #000;
        }
        .badge-trust-low {
            background-color: #dc3545;
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
                            <a class="nav-link active" href="<?= base_url('admin/apps') ?>">
                                <i class="bi bi-app"></i> Apps
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= base_url('admin/categories') ?>">
                                <i class="bi bi-tags"></i> Categories
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
                            <a class="nav-link" href="<?= base_url('admin/users') ?>">
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
                        <a href="<?= base_url('admin/apps/create') ?>" class="btn btn-primary">
                            <i class="bi bi-plus-circle"></i> Create App
                        </a>
                    </div>
                </div>

                <!-- Flash Messages -->
                <?php if (session('success')): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= session('success') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>

                <?php if (session('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= session('error') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>

                <!-- Search and Filter -->
                <div class="card mb-4">
                    <div class="card-body">
                        <form method="get" action="<?= base_url('admin/apps') ?>" class="row g-3">
                            <div class="col-md-5">
                                <label for="search" class="form-label">Search</label>
                                <input type="text" class="form-control" id="search" name="search" 
                                       placeholder="Search by name or developer" value="<?= esc($search) ?>">
                            </div>
                            <div class="col-md-3">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="">All</option>
                                    <option value="pending" <?= $status === 'pending' ? 'selected' : '' ?>>Pending</option>
                                    <option value="approved" <?= $status === 'approved' ? 'selected' : '' ?>>Approved</option>
                                    <option value="rejected" <?= $status === 'rejected' ? 'selected' : '' ?>>Rejected</option>
                                </select>
                            </div>
                            <div class="col-md-4 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary me-2">
                                    <i class="bi bi-search"></i> Search
                                </button>
                                <a href="<?= base_url('admin/apps') ?>" class="btn btn-secondary">
                                    <i class="bi bi-x-circle"></i> Clear
                                </a>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Apps Table -->
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Developer</th>
                                        <th>Platform</th>
                                        <th>Trust Score</th>
                                        <th>Status</th>
                                        <th>Views</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($apps)): ?>
                                    <tr>
                                        <td colspan="8" class="text-center text-muted">No apps found</td>
                                    </tr>
                                    <?php else: ?>
                                        <?php foreach ($apps as $app): ?>
                                        <tr>
                                            <td><?= esc($app['id']) ?></td>
                                            <td>
                                                <strong><?= esc($app['name']) ?></strong><br>
                                                <small class="text-muted"><?= esc($app['slug']) ?></small>
                                            </td>
                                            <td><?= esc($app['developer_name']) ?></td>
                                            <td>
                                                <span class="badge bg-secondary">
                                                    <?= esc(ucfirst($app['platform_type'])) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php
                                                $score = $app['trust_score'];
                                                $badgeClass = $score >= 80 ? 'badge-trust-high' : ($score >= 50 ? 'badge-trust-medium' : 'badge-trust-low');
                                                ?>
                                                <span class="badge <?= $badgeClass ?>">
                                                    <?= number_format($score, 1) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php
                                                $statusBadge = [
                                                    'pending' => 'warning',
                                                    'approved' => 'success',
                                                    'rejected' => 'danger',
                                                ];
                                                ?>
                                                <span class="badge bg-<?= $statusBadge[$app['approval_status']] ?>">
                                                    <?= esc(ucfirst($app['approval_status'])) ?>
                                                </span>
                                            </td>
                                            <td><?= number_format($app['view_count']) ?></td>
                                            <td>
                                                <div class="btn-group btn-group-sm" role="group">
                                                    <a href="<?= base_url('admin/apps/edit/' . $app['id']) ?>" 
                                                       class="btn btn-outline-primary" title="Edit">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                    
                                                    <?php if ($app['approval_status'] === 'pending'): ?>
                                                    <form method="post" action="<?= base_url('admin/apps/approve/' . $app['id']) ?>" 
                                                          class="d-inline" onsubmit="return confirm('Approve this app?')">
                                                        <?= csrf_field() ?>
                                                        <button type="submit" class="btn btn-outline-success" title="Approve">
                                                            <i class="bi bi-check-circle"></i>
                                                        </button>
                                                    </form>
                                                    
                                                    <form method="post" action="<?= base_url('admin/apps/reject/' . $app['id']) ?>" 
                                                          class="d-inline" onsubmit="return confirm('Reject this app?')">
                                                        <?= csrf_field() ?>
                                                        <button type="submit" class="btn btn-outline-warning" title="Reject">
                                                            <i class="bi bi-x-circle"></i>
                                                        </button>
                                                    </form>
                                                    <?php endif; ?>
                                                    
                                                    <form method="post" action="<?= base_url('admin/apps/delete/' . $app['id']) ?>" 
                                                          class="d-inline" onsubmit="return confirm('Delete this app and all associated data? This cannot be undone.')">
                                                        <?= csrf_field() ?>
                                                        <button type="submit" class="btn btn-outline-danger" title="Delete">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <?php if ($pagination['total_pages'] > 1): ?>
                        <nav aria-label="Page navigation">
                            <ul class="pagination justify-content-center">
                                <li class="page-item <?= $pagination['current_page'] <= 1 ? 'disabled' : '' ?>">
                                    <a class="page-link" href="<?= base_url('admin/apps?page=' . ($pagination['current_page'] - 1) . '&search=' . urlencode($search) . '&status=' . urlencode($status)) ?>">
                                        Previous
                                    </a>
                                </li>
                                
                                <?php for ($i = 1; $i <= $pagination['total_pages']; $i++): ?>
                                    <?php if ($i == 1 || $i == $pagination['total_pages'] || abs($i - $pagination['current_page']) <= 2): ?>
                                    <li class="page-item <?= $i == $pagination['current_page'] ? 'active' : '' ?>">
                                        <a class="page-link" href="<?= base_url('admin/apps?page=' . $i . '&search=' . urlencode($search) . '&status=' . urlencode($status)) ?>">
                                            <?= $i ?>
                                        </a>
                                    </li>
                                    <?php elseif (abs($i - $pagination['current_page']) == 3): ?>
                                    <li class="page-item disabled">
                                        <span class="page-link">...</span>
                                    </li>
                                    <?php endif; ?>
                                <?php endfor; ?>
                                
                                <li class="page-item <?= $pagination['current_page'] >= $pagination['total_pages'] ? 'disabled' : '' ?>">
                                    <a class="page-link" href="<?= base_url('admin/apps?page=' . ($pagination['current_page'] + 1) . '&search=' . urlencode($search) . '&status=' . urlencode($status)) ?>">
                                        Next
                                    </a>
                                </li>
                            </ul>
                        </nav>
                        
                        <div class="text-center text-muted">
                            Showing page <?= $pagination['current_page'] ?> of <?= $pagination['total_pages'] ?> 
                            (<?= number_format($pagination['total']) ?> total apps)
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
