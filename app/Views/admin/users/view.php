<?= $this->extend('admin/admin_layout') ?>

<?= $this->section('topbar_actions') ?>
<?php if ($user['role'] !== 'admin'): ?>
    <?php if ($user['status'] === 'active'): ?>
        <form method="POST" action="<?= base_url('admin/users/suspend/' . $user['id']) ?>" class="d-inline">
            <?= csrf_field() ?>
            <button type="submit" class="btn btn-warning btn-sm" onclick="return confirm('Suspend this user? They will not be able to login.')">
                <i class="bi bi-pause-circle"></i> Suspend
            </button>
        </form>
    <?php elseif ($user['status'] === 'suspended'): ?>
        <form method="POST" action="<?= base_url('admin/users/reactivate/' . $user['id']) ?>" class="d-inline">
            <?= csrf_field() ?>
            <button type="submit" class="btn btn-success btn-sm" onclick="return confirm('Reactivate this user? They will be able to login again.')">
                <i class="bi bi-play-circle"></i> Reactivate
            </button>
        </form>
    <?php endif; ?>
    <form method="POST" action="<?= base_url('admin/users/delete/' . $user['id']) ?>">
        <?= csrf_field() ?>
        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Permanently delete this user? Their content will be anonymized. This action cannot be undone.')">
            <i class="bi bi-trash"></i> Delete
        </button>
    </form>
<?php endif; ?>
<a href="<?= base_url('admin/users') ?>" class="btn btn-outline-secondary btn-sm">
    <i class="bi bi-arrow-left"></i> Back
</a>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- User Info -->
<div class="card mb-4">
    <div class="card-body">
        <div class="row g-4">
            <div class="col-md-6">
                <h5 class="mb-3"><i class="bi bi-person-circle text-primary"></i> User Information</h5>
                <table class="table table-sm mb-0">
                    <tbody>
                        <tr>
                            <th class="text-muted" style="width:40%">Username:</th>
                            <td class="fw-medium">
                                <?= esc($user['username']) ?>
                                <?php if ($user['email_verified']): ?>
                                    <i class="bi bi-patch-check-fill text-success" title="Email Verified"></i>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <th class="text-muted">Email:</th>
                            <td><?= esc($user['email']) ?></td>
                        </tr>
                        <tr>
                            <th class="text-muted">Role:</th>
                            <td><span class="badge bg-<?= $user['role'] === 'admin' ? 'danger' : 'secondary' ?>"><?= ucfirst(esc($user['role'])) ?></span></td>
                        </tr>
                        <tr>
                            <th class="text-muted">Status:</th>
                            <td><span class="badge bg-<?= $user['status'] === 'active' ? 'success' : ($user['status'] === 'suspended' ? 'warning text-dark' : 'dark') ?>"><?= ucfirst(esc($user['status'])) ?></span></td>
                        </tr>
                        <tr>
                            <th class="text-muted">Email Verified:</th>
                            <td>
                                <?php if ($user['email_verified']): ?>
                                    <span class="text-success"><i class="bi bi-check-circle-fill"></i> Yes</span>
                                <?php else: ?>
                                    <span class="text-warning"><i class="bi bi-x-circle-fill"></i> No</span>
                                    <?php if ($user['role'] !== 'admin'): ?>
                                        <form method="POST" action="<?= base_url('admin/users/verify/' . $user['id']) ?>" class="d-inline ms-2">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-sm btn-outline-success"><i class="bi bi-patch-check"></i> Verify Manually</button>
                                        </form>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="col-md-6">
                <h5 class="mb-3"><i class="bi bi-clock-history text-primary"></i> Account Activity</h5>
                <table class="table table-sm mb-0">
                    <tbody>
                        <tr>
                            <th class="text-muted" style="width:40%">Registered:</th>
                            <td><?= date('F d, Y \a\t g:i A', strtotime($user['created_at'])) ?></td>
                        </tr>
                        <tr>
                            <th class="text-muted">Last Login:</th>
                            <td><?= $user['last_login'] ? date('F d, Y \a\t g:i A', strtotime($user['last_login'])) : '<span class="text-muted">Never</span>' ?></td>
                        </tr>
                        <tr>
                            <th class="text-muted">Failed Logins:</th>
                            <td><?= $user['failed_login_count'] > 0 ? '<span class="badge bg-warning text-dark">' . $user['failed_login_count'] . '</span>' : '<span class="text-muted">0</span>' ?></td>
                        </tr>
                        <tr>
                            <th class="text-muted">Account Locked:</th>
                            <td>
                                <?php if ($user['account_locked_until'] && strtotime($user['account_locked_until']) > time()): ?>
                                    <span class="text-danger"><i class="bi bi-lock-fill"></i> Locked until <?= date('F d, Y \a\t g:i A', strtotime($user['account_locked_until'])) ?></span>
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
</div>

<!-- Stats -->
<div class="row g-4 mb-4">
    <div class="col-md-6">
        <div class="card stat-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stat-label">Total Reviews</div>
                        <div class="stat-value text-primary"><?= number_format($reviewCount) ?></div>
                    </div>
                    <div class="stat-icon" style="background:#eef2ff;">
                        <i class="bi bi-star-fill text-primary"></i>
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
                        <div class="stat-label">Total Scam Reports</div>
                        <div class="stat-value text-warning"><?= number_format($scamReportCount) ?></div>
                    </div>
                    <div class="stat-icon" style="background:#fef3c7;">
                        <i class="bi bi-exclamation-triangle-fill text-warning"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Reviews -->
<div class="card mb-4">
    <div class="card-header">Recent Reviews <small class="text-muted fw-normal">(Last 10)</small></div>
    <div class="card-body">
        <?php if (empty($recentReviews)): ?>
            <p class="text-muted mb-0 small">No reviews submitted yet.</p>
        <?php else: ?>
            <?php foreach ($recentReviews as $review): ?>
            <div class="d-flex justify-content-between align-items-start pb-3 mb-3 border-bottom">
                <div>
                    <h6 class="mb-1">
                        <a href="<?= base_url('apps/' . $review['app_slug']) ?>" target="_blank" class="text-decoration-none">
                            <?= esc($review['app_name']) ?>
                        </a>
                    </h6>
                    <p class="fw-medium mb-1 small"><?= esc($review['title']) ?></p>
                    <p class="text-muted small mb-1"><?= esc(substr($review['review_text'], 0, 150)) ?><?= strlen($review['review_text']) > 150 ? '...' : '' ?></p>
                    <div class="d-flex align-items-center gap-2">
                        <span class="text-warning small">
                            <?php for ($i = 0; $i < $review['rating']; $i++): ?><i class="bi bi-star-fill"></i><?php endfor; ?>
                            <?php for ($i = $review['rating']; $i < 5; $i++): ?><i class="bi bi-star"></i><?php endfor; ?>
                        </span>
                        <span class="badge bg-<?= $review['approval_status'] === 'approved' ? 'success' : ($review['approval_status'] === 'pending' ? 'warning text-dark' : 'danger') ?>"><?= ucfirst(esc($review['approval_status'])) ?></span>
                        <small class="text-muted"><i class="bi bi-clock"></i> <?= date('M d, Y', strtotime($review['created_at'])) ?></small>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<!-- Recent Scam Reports -->
<div class="card mb-4">
    <div class="card-header">Recent Scam Reports <small class="text-muted fw-normal">(Last 10)</small></div>
    <div class="card-body">
        <?php if (empty($recentScamReports)): ?>
            <p class="text-muted mb-0 small">No scam reports submitted yet.</p>
        <?php else: ?>
            <?php foreach ($recentScamReports as $report): ?>
            <div class="d-flex justify-content-between align-items-start pb-3 mb-3 border-bottom">
                <div>
                    <h6 class="mb-1">
                        <a href="<?= base_url('apps/' . $report['app_slug']) ?>" target="_blank" class="text-decoration-none">
                            <?= esc($report['app_name']) ?>
                        </a>
                    </h6>
                    <p class="fw-medium mb-1 small"><?= esc($report['title']) ?></p>
                    <p class="text-muted small mb-1"><?= esc(substr($report['description'], 0, 150)) ?><?= strlen($report['description']) > 150 ? '...' : '' ?></p>
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-<?= $report['risk_level'] === 'high' ? 'danger' : ($report['risk_level'] === 'medium' ? 'warning text-dark' : 'info') ?>">Risk: <?= ucfirst(esc($report['risk_level'])) ?></span>
                        <span class="badge bg-<?= $report['approval_status'] === 'approved' ? 'success' : ($report['approval_status'] === 'pending' ? 'warning text-dark' : 'danger') ?>"><?= ucfirst(esc($report['approval_status'])) ?></span>
                        <small class="text-muted"><i class="bi bi-clock"></i> <?= date('M d, Y', strtotime($report['created_at'])) ?></small>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>
