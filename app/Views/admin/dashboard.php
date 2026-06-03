<?= $this->extend('admin/admin_layout') ?>

<?= $this->section('topbar_actions') ?>
<div class="btn-group">
    <button type="button" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-calendar"></i> Today
    </button>
</div>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- Statistics Cards -->
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card stat-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stat-label">Total Apps</div>
                        <div class="stat-value text-success"><?= number_format($totalApps) ?></div>
                    </div>
                    <div class="stat-icon" style="background:#e8f5e9;">
                        <i class="bi bi-app text-success"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stat-label">Total Reviews</div>
                        <div class="stat-value text-info"><?= number_format($totalReviews) ?></div>
                    </div>
                    <div class="stat-icon" style="background:#e0f2fe;">
                        <i class="bi bi-star-fill text-info"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stat-label">Scam Reports</div>
                        <div class="stat-value text-danger"><?= number_format($totalScamReports) ?></div>
                    </div>
                    <div class="stat-icon" style="background:#fee2e2;">
                        <i class="bi bi-exclamation-triangle-fill text-danger"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stat-label">Active Users</div>
                        <div class="stat-value text-primary"><?= number_format($totalUsers) ?></div>
                    </div>
                    <div class="stat-icon" style="background:#eef2ff;">
                        <i class="bi bi-people-fill text-primary"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Pending Moderation -->
<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="card stat-card border-start border-warning border-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stat-label">Pending Reviews</div>
                        <div class="stat-value text-warning"><?= number_format($pendingReviews) ?></div>
                    </div>
                    <a href="<?= base_url('admin/reviews?status=pending') ?>" class="btn btn-warning btn-sm">
                        <i class="bi bi-eye"></i> Review
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card stat-card border-start border-warning border-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stat-label">Pending Scam Reports</div>
                        <div class="stat-value text-warning"><?= number_format($pendingScamReports) ?></div>
                    </div>
                    <a href="<?= base_url('admin/scam-reports?status=pending') ?>" class="btn btn-warning btn-sm">
                        <i class="bi bi-eye"></i> Review
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card stat-card border-start border-warning border-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stat-label">Pending Apps</div>
                        <div class="stat-value text-warning"><?= number_format($pendingApps) ?></div>
                    </div>
                    <a href="<?= base_url('admin/apps?status=pending') ?>" class="btn btn-warning btn-sm">
                        <i class="bi bi-eye"></i> Review
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Charts -->
<div class="row g-4 mb-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">Review Submissions (Last 30 Days)</div>
            <div class="card-body">
                <div style="position:relative;height:280px;">
                    <canvas id="reviewTrendChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">Scam Report Submissions (Last 30 Days)</div>
            <div class="card-body">
                <div style="position:relative;height:280px;">
                    <canvas id="scamReportTrendChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Top Apps Tables -->
<div class="row g-4 mb-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">Top 10 Apps by Trust Score</div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>App Name</th>
                                <th>Trust Score</th>
                                <th>Views</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($topAppsByTrustScore as $index => $app): ?>
                            <tr>
                                <td class="text-muted"><?= $index + 1 ?></td>
                                <td>
                                    <a href="<?= base_url('apps/' . esc($app['slug'])) ?>" target="_blank" class="text-decoration-none fw-medium">
                                        <?= esc($app['name']) ?>
                                    </a>
                                </td>
                                <td>
                                    <span class="badge bg-<?= $app['trust_score'] >= 80 ? 'success' : ($app['trust_score'] >= 50 ? 'warning text-dark' : 'danger') ?>">
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
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">Top 10 Apps by Views</div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>App Name</th>
                                <th>Views</th>
                                <th>Trust Score</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($topAppsByViews as $index => $app): ?>
                            <tr>
                                <td class="text-muted"><?= $index + 1 ?></td>
                                <td>
                                    <a href="<?= base_url('apps/' . esc($app['slug'])) ?>" target="_blank" class="text-decoration-none fw-medium">
                                        <?= esc($app['name']) ?>
                                    </a>
                                </td>
                                <td><?= number_format($app['view_count']) ?></td>
                                <td>
                                    <span class="badge bg-<?= $app['trust_score'] >= 80 ? 'success' : ($app['trust_score'] >= 50 ? 'warning text-dark' : 'danger') ?>">
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

<!-- Recent Users -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">Recent User Registrations (Last 7 Days)</div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
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
                            <tr><td colspan="5" class="text-center text-muted py-4">No recent registrations</td></tr>
                            <?php else: ?>
                                <?php foreach ($recentUsers as $user): ?>
                                <tr>
                                    <td class="fw-medium"><?= esc($user['username']) ?></td>
                                    <td><?= esc($user['email']) ?></td>
                                    <td>
                                        <span class="badge bg-<?= $user['role'] === 'admin' ? 'danger' : 'secondary' ?>">
                                            <?= esc($user['role']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?= $user['status'] === 'active' ? 'success' : 'warning text-dark' ?>">
                                            <?= esc($user['status']) ?>
                                        </span>
                                    </td>
                                    <td class="text-muted"><?= date('M d, Y H:i', strtotime($user['created_at'])) ?></td>
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

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
const reviewTrendCtx = document.getElementById('reviewTrendChart').getContext('2d');
new Chart(reviewTrendCtx, {
    type: 'line',
    data: {
        labels: <?= json_encode($reviewTrend['labels']) ?>,
        datasets: [{
            label: 'Reviews',
            data: <?= json_encode($reviewTrend['data']) ?>,
            borderColor: '#6366f1',
            backgroundColor: 'rgba(99,102,241,0.08)',
            tension: 0.4,
            fill: true,
            pointRadius: 3,
            borderWidth: 2
        }]
    },
    options: {
        responsive: true, maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
            y: { beginAtZero: true, ticks: { precision: 0 } },
            x: { grid: { display: false } }
        }
    }
});
const scamReportTrendCtx = document.getElementById('scamReportTrendChart').getContext('2d');
new Chart(scamReportTrendCtx, {
    type: 'line',
    data: {
        labels: <?= json_encode($scamReportTrend['labels']) ?>,
        datasets: [{
            label: 'Scam Reports',
            data: <?= json_encode($scamReportTrend['data']) ?>,
            borderColor: '#ef4444',
            backgroundColor: 'rgba(239,68,68,0.08)',
            tension: 0.4,
            fill: true,
            pointRadius: 3,
            borderWidth: 2
        }]
    },
    options: {
        responsive: true, maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
            y: { beginAtZero: true, ticks: { precision: 0 } },
            x: { grid: { display: false } }
        }
    }
});
</script>
<?= $this->endSection() ?>
