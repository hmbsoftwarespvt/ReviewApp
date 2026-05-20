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
        .stat-card {
            border-left: 4px solid #0d6efd;
            transition: transform 0.2s;
        }
        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .user-info-section {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }
        .activity-item {
            border-left: 3px solid #dee2e6;
            padding-left: 1rem;
            margin-bottom: 1rem;
        }
        .activity-item:hover {
            border-left-color: #0d6efd;
            background-color: #f8f9fa;
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
                    <h1 class="h2">
                        <a href="<?= base_url('admin/users') ?>" class="text-decoration-none text-muted">
                            <i class="bi bi-arrow-left"></i>
                        </a>
                        <?= esc($title) ?>
                    </h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <?php if ($user['role'] !== 'admin'): ?>
                            <?php if ($user['status'] === 'active'): ?>
                                <form method="POST" action="<?= base_url('admin/users/suspend/' . $user['id']) ?>" class="me-2">
                                    <?= csrf_field() ?>
                                    <button type="submit" 
                                            class="btn btn-warning btn-sm" 
                                            onclick="return confirm('Suspend this user? They will not be able to login.')">
                                        <i class="bi bi-pause-circle"></i> Suspend User
                                    </button>
                                </form>
                            <?php elseif ($user['status'] === 'suspended'): ?>
                                <form method="POST" action="<?= base_url('admin/users/reactivate/' . $user['id']) ?>" class="me-2">
                                    <?= csrf_field() ?>
                                    <button type="submit" 
                                            class="btn btn-success btn-sm" 
                                            onclick="return confirm('Reactivate this user? They will be able to login again.')">
                                        <i class="bi bi-play-circle"></i> Reactivate User
                                    </button>
                                </form>
                            <?php endif; ?>
                            
                            <form method="POST" action="<?= base_url('admin/users/delete/' . $user['id']) ?>">
                                <?= csrf_field() ?>
                                <button type="submit" 
                                        class="btn btn-danger btn-sm" 
                                        onclick="return confirm('Permanently delete this user? Their content will be anonymized. This action cannot be undone.')">
                                    <i class="bi bi-trash"></i> Delete User
                                </button>
                            </form>
                        <?php endif; ?>
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

                <!-- User Information Section -->
                <div class="user-info-section">
                    <div class="row">
                        <div class="col-md-6">
                            <h5 class="mb-3">
                                <i class="bi bi-person-circle"></i> User Information
                            </h5>
                            <table class="table table-sm">
                                <tbody>
                                    <tr>
                                        <th width="40%">Username:</th>
                                        <td>
                                            <strong><?= esc($user['username']) ?></strong>
                                            <?php if ($user['email_verified']): ?>
                                                <i class="bi bi-patch-check-fill text-success" title="Email Verified"></i>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Email:</th>
                                        <td><?= esc($user['email']) ?></td>
                                    </tr>
                                    <tr>
                                        <th>Role:</th>
                                        <td>
                                            <span class="badge bg-<?= $user['role'] === 'admin' ? 'danger' : 'secondary' ?>">
                                                <?= ucfirst(esc($user['role'])) ?>
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Status:</th>
                                        <td>
                                            <span class="badge bg-<?= $user['status'] === 'active' ? 'success' : ($user['status'] === 'suspended' ? 'warning' : 'dark') ?>">
                                                <?= ucfirst(esc($user['status'])) ?>
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Email Verified:</th>
                                        <td>
                                            <?php if ($user['email_verified']): ?>
                                                <span class="text-success"><i class="bi bi-check-circle-fill"></i> Yes</span>
                                            <?php else: ?>
                                                <span class="text-warning"><i class="bi bi-x-circle-fill"></i> No</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h5 class="mb-3">
                                <i class="bi bi-clock-history"></i> Account Activity
                            </h5>
                            <table class="table table-sm">
                                <tbody>
                                    <tr>
                                        <th width="40%">Registered:</th>
                                        <td><?= date('F d, Y \a\t g:i A', strtotime($user['created_at'])) ?></td>
                                    </tr>
                                    <tr>
                                        <th>Last Login:</th>
                                        <td>
                                            <?php if ($user['last_login']): ?>
                                                <?= date('F d, Y \a\t g:i A', strtotime($user['last_login'])) ?>
                                            <?php else: ?>
                                                <span class="text-muted">Never logged in</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Failed Login Count:</th>
                                        <td>
                                            <?php if ($user['failed_login_count'] > 0): ?>
                                                <span class="badge bg-warning"><?= $user['failed_login_count'] ?></span>
                                            <?php else: ?>
                                                <span class="text-muted">0</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Account Locked Until:</th>
                                        <td>
                                            <?php if ($user['account_locked_until'] && strtotime($user['account_locked_until']) > time()): ?>
                                                <span class="text-danger">
                                                    <i class="bi bi-lock-fill"></i> 
                                                    <?= date('F d, Y \a\t g:i A', strtotime($user['account_locked_until'])) ?>
                                                </span>
                                            <?php else: ?>
                                                <span class="text-success"><i class="bi bi-unlock-fill"></i> Not locked</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Statistics Cards -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="card stat-card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="text-muted mb-1">Total Reviews</h6>
                                        <h2 class="mb-0"><?= number_format($reviewCount) ?></h2>
                                    </div>
                                    <div class="text-primary">
                                        <i class="bi bi-star-fill" style="font-size: 2.5rem;"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card stat-card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="text-muted mb-1">Total Scam Reports</h6>
                                        <h2 class="mb-0"><?= number_format($scamReportCount) ?></h2>
                                    </div>
                                    <div class="text-warning">
                                        <i class="bi bi-exclamation-triangle-fill" style="font-size: 2.5rem;"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Reviews -->
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="bi bi-star"></i> Recent Reviews (Last 10)</h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($recentReviews)): ?>
                            <p class="text-muted mb-0">No reviews submitted yet.</p>
                        <?php else: ?>
                            <?php foreach ($recentReviews as $review): ?>
                                <div class="activity-item">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1">
                                                <a href="<?= base_url('apps/' . $review['app_slug']) ?>" target="_blank">
                                                    <?= esc($review['app_name']) ?>
                                                </a>
                                            </h6>
                                            <p class="mb-1">
                                                <strong><?= esc($review['title']) ?></strong>
                                            </p>
                                            <p class="mb-1 text-muted small">
                                                <?= esc(substr($review['review_text'], 0, 150)) ?><?= strlen($review['review_text']) > 150 ? '...' : '' ?>
                                            </p>
                                            <div class="d-flex align-items-center gap-3">
                                                <span class="badge bg-warning text-dark">
                                                    <?php for ($i = 0; $i < $review['rating']; $i++): ?>
                                                        <i class="bi bi-star-fill"></i>
                                                    <?php endfor; ?>
                                                    <?php for ($i = $review['rating']; $i < 5; $i++): ?>
                                                        <i class="bi bi-star"></i>
                                                    <?php endfor; ?>
                                                </span>
                                                <span class="badge bg-<?= $review['approval_status'] === 'approved' ? 'success' : ($review['approval_status'] === 'pending' ? 'warning' : 'danger') ?>">
                                                    <?= ucfirst(esc($review['approval_status'])) ?>
                                                </span>
                                                <small class="text-muted">
                                                    <i class="bi bi-clock"></i> <?= date('M d, Y', strtotime($review['created_at'])) ?>
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Recent Scam Reports -->
                <div class="card mb-4">
                    <div class="card-header bg-warning">
                        <h5 class="mb-0"><i class="bi bi-exclamation-triangle"></i> Recent Scam Reports (Last 10)</h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($recentScamReports)): ?>
                            <p class="text-muted mb-0">No scam reports submitted yet.</p>
                        <?php else: ?>
                            <?php foreach ($recentScamReports as $report): ?>
                                <div class="activity-item">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1">
                                                <a href="<?= base_url('apps/' . $report['app_slug']) ?>" target="_blank">
                                                    <?= esc($report['app_name']) ?>
                                                </a>
                                            </h6>
                                            <p class="mb-1">
                                                <strong><?= esc($report['title']) ?></strong>
                                            </p>
                                            <p class="mb-1 text-muted small">
                                                <?= esc(substr($report['description'], 0, 150)) ?><?= strlen($report['description']) > 150 ? '...' : '' ?>
                                            </p>
                                            <div class="d-flex align-items-center gap-3">
                                                <span class="badge bg-<?= $report['risk_level'] === 'high' ? 'danger' : ($report['risk_level'] === 'medium' ? 'warning' : 'info') ?>">
                                                    Risk: <?= ucfirst(esc($report['risk_level'])) ?>
                                                </span>
                                                <span class="badge bg-<?= $report['approval_status'] === 'approved' ? 'success' : ($report['approval_status'] === 'pending' ? 'warning' : 'danger') ?>">
                                                    <?= ucfirst(esc($report['approval_status'])) ?>
                                                </span>
                                                <small class="text-muted">
                                                    <i class="bi bi-clock"></i> <?= date('M d, Y', strtotime($report['created_at'])) ?>
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
