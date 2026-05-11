<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title) ?> - AppTrust Platform</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <style>
        .stat-card {
            border-left: 4px solid #0d6efd;
            transition: transform 0.2s;
        }
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .stat-card.pending {
            border-left-color: #ffc107;
        }
        .stat-card.success {
            border-left-color: #198754;
        }
        .stat-card.danger {
            border-left-color: #dc3545;
        }
        .stat-card.info {
            border-left-color: #0dcaf0;
        }
        .chart-container {
            position: relative;
            height: 300px;
        }
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
                            <a class="nav-link active" href="<?= base_url('admin/dashboard') ?>">
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
                        <div class="btn-group me-2">
                            <button type="button" class="btn btn-sm btn-outline-secondary">
                                <i class="bi bi-calendar"></i> Today
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Statistics Cards -->
                <div class="row mb-4">
                    <div class="col-md-3 mb-3">
                        <div class="card stat-card success">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="text-muted mb-1">Total Apps</h6>
                                        <h2 class="mb-0"><?= number_format($totalApps) ?></h2>
                                    </div>
                                    <div class="text-success">
                                        <i class="bi bi-app" style="font-size: 2rem;"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3 mb-3">
                        <div class="card stat-card info">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="text-muted mb-1">Total Reviews</h6>
                                        <h2 class="mb-0"><?= number_format($totalReviews) ?></h2>
                                    </div>
                                    <div class="text-info">
                                        <i class="bi bi-star-fill" style="font-size: 2rem;"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3 mb-3">
                        <div class="card stat-card danger">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="text-muted mb-1">Scam Reports</h6>
                                        <h2 class="mb-0"><?= number_format($totalScamReports) ?></h2>
                                    </div>
                                    <div class="text-danger">
                                        <i class="bi bi-exclamation-triangle-fill" style="font-size: 2rem;"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3 mb-3">
                        <div class="card stat-card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="text-muted mb-1">Active Users</h6>
                                        <h2 class="mb-0"><?= number_format($totalUsers) ?></h2>
                                    </div>
                                    <div class="text-primary">
                                        <i class="bi bi-people-fill" style="font-size: 2rem;"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pending Moderation Cards -->
                <div class="row mb-4">
                    <div class="col-md-4 mb-3">
                        <div class="card stat-card pending">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="text-muted mb-1">Pending Reviews</h6>
                                        <h3 class="mb-0 text-warning"><?= number_format($pendingReviews) ?></h3>
                                    </div>
                                    <div>
                                        <a href="<?= base_url('admin/reviews?status=pending') ?>" class="btn btn-warning btn-sm">
                                            <i class="bi bi-eye"></i> Review
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4 mb-3">
                        <div class="card stat-card pending">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="text-muted mb-1">Pending Scam Reports</h6>
                                        <h3 class="mb-0 text-warning"><?= number_format($pendingScamReports) ?></h3>
                                    </div>
                                    <div>
                                        <a href="<?= base_url('admin/scam-reports?status=pending') ?>" class="btn btn-warning btn-sm">
                                            <i class="bi bi-eye"></i> Review
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4 mb-3">
                        <div class="card stat-card pending">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="text-muted mb-1">Pending Apps</h6>
                                        <h3 class="mb-0 text-warning"><?= number_format($pendingApps) ?></h3>
                                    </div>
                                    <div>
                                        <a href="<?= base_url('admin/apps?status=pending') ?>" class="btn btn-warning btn-sm">
                                            <i class="bi bi-eye"></i> Review
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Charts -->
                <div class="row mb-4">
                    <div class="col-md-6 mb-3">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Review Submissions (Last 30 Days)</h5>
                            </div>
                            <div class="card-body">
                                <div class="chart-container">
                                    <canvas id="reviewTrendChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Scam Report Submissions (Last 30 Days)</h5>
                            </div>
                            <div class="card-body">
                                <div class="chart-container">
                                    <canvas id="scamReportTrendChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Top Apps -->
                <div class="row mb-4">
                    <div class="col-md-6 mb-3">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Top 10 Apps by Trust Score</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Rank</th>
                                                <th>App Name</th>
                                                <th>Trust Score</th>
                                                <th>Views</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($topAppsByTrustScore as $index => $app): ?>
                                            <tr>
                                                <td><?= $index + 1 ?></td>
                                                <td>
                                                    <a href="<?= base_url('app/' . esc($app['slug'])) ?>" target="_blank">
                                                        <?= esc($app['name']) ?>
                                                    </a>
                                                </td>
                                                <td>
                                                    <span class="badge bg-<?= $app['trust_score'] >= 80 ? 'success' : ($app['trust_score'] >= 50 ? 'warning' : 'danger') ?>">
                                                        <?= number_format($app['trust_score'], 1) ?>
                                                    </span>
                                                </td>
                                                <td><?= number_format($app['view_count']) ?></td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Top 10 Apps by Views</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Rank</th>
                                                <th>App Name</th>
                                                <th>Views</th>
                                                <th>Trust Score</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($topAppsByViews as $index => $app): ?>
                                            <tr>
                                                <td><?= $index + 1 ?></td>
                                                <td>
                                                    <a href="<?= base_url('app/' . esc($app['slug'])) ?>" target="_blank">
                                                        <?= esc($app['name']) ?>
                                                    </a>
                                                </td>
                                                <td><?= number_format($app['view_count']) ?></td>
                                                <td>
                                                    <span class="badge bg-<?= $app['trust_score'] >= 80 ? 'success' : ($app['trust_score'] >= 50 ? 'warning' : 'danger') ?>">
                                                        <?= number_format($app['trust_score'], 1) ?>
                                                    </span>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent User Registrations -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Recent User Registrations (Last 7 Days)</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Username</th>
                                                <th>Email</th>
                                                <th>Role</th>
                                                <th>Status</th>
                                                <th>Registered</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (empty($recentUsers)): ?>
                                            <tr>
                                                <td colspan="5" class="text-center text-muted">No recent registrations</td>
                                            </tr>
                                            <?php else: ?>
                                                <?php foreach ($recentUsers as $user): ?>
                                                <tr>
                                                    <td><?= esc($user['username']) ?></td>
                                                    <td><?= esc($user['email']) ?></td>
                                                    <td>
                                                        <span class="badge bg-<?= $user['role'] === 'admin' ? 'danger' : 'secondary' ?>">
                                                            <?= esc($user['role']) ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-<?= $user['status'] === 'active' ? 'success' : 'warning' ?>">
                                                            <?= esc($user['status']) ?>
                                                        </span>
                                                    </td>
                                                    <td><?= date('M d, Y H:i', strtotime($user['created_at'])) ?></td>
                                                </tr>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Review Trend Chart
        const reviewTrendCtx = document.getElementById('reviewTrendChart').getContext('2d');
        new Chart(reviewTrendCtx, {
            type: 'line',
            data: {
                labels: <?= json_encode($reviewTrend['labels']) ?>,
                datasets: [{
                    label: 'Reviews',
                    data: <?= json_encode($reviewTrend['data']) ?>,
                    borderColor: 'rgb(13, 110, 253)',
                    backgroundColor: 'rgba(13, 110, 253, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                }
            }
        });

        // Scam Report Trend Chart
        const scamReportTrendCtx = document.getElementById('scamReportTrendChart').getContext('2d');
        new Chart(scamReportTrendCtx, {
            type: 'line',
            data: {
                labels: <?= json_encode($scamReportTrend['labels']) ?>,
                datasets: [{
                    label: 'Scam Reports',
                    data: <?= json_encode($scamReportTrend['data']) ?>,
                    borderColor: 'rgb(220, 53, 69)',
                    backgroundColor: 'rgba(220, 53, 69, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>
