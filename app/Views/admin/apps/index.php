<?= $this->extend('admin/admin_layout') ?>

<?= $this->section('topbar_actions') ?>
<a href="<?= base_url('admin/apps/create') ?>" class="btn btn-primary btn-sm">
    <i class="bi bi-plus-circle"></i> Create App
</a>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- Search and Filter -->
<div class="card mb-4">
    <div class="card-body">
        <form method="get" action="<?= base_url('admin/apps') ?>" class="row g-3">
            <div class="col-md-5">
                <label for="search" class="form-label">Search</label>
                <input type="text" class="form-control" id="search" name="search" 
                       placeholder="Search by name or developer..." value="<?= esc($search) ?>">
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
            <div class="col-md-4 d-flex align-items-end gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-search"></i> Search
                </button>
                <a href="<?= base_url('admin/apps') ?>" class="btn btn-outline-secondary">
                    <i class="bi bi-x-circle"></i> Clear
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Apps Table -->
<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Developer</th>
                        <th>Platform</th>
                        <th>Trust Score</th>
                        <th>Status</th>
                        <th>Views</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($apps)): ?>
                    <tr>
                        <td colspan="8" class="text-center text-muted py-5">No apps found</td>
                    </tr>
                    <?php else: ?>
                        <?php foreach ($apps as $app): ?>
                        <tr>
                            <td class="text-muted"><?= esc($app['id']) ?></td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <?php if (!empty($app['thumbnail'])): ?>
                                    <img src="<?= base_url('uploads/thumbnails/' . esc($app['thumbnail'])) ?>" 
                                         alt="" style="width:32px;height:32px;border-radius:8px;object-fit:cover;">
                                    <?php endif; ?>
                                    <div>
                                        <div class="fw-medium"><?= esc($app['name']) ?></div>
                                        <small class="text-muted"><?= esc($app['slug']) ?></small>
                                    </div>
                                </div>
                            </td>
                            <td><?= esc($app['developer_name']) ?></td>
                            <td>
                                <span class="badge bg-secondary"><?= esc(ucfirst($app['platform_type'])) ?></span>
                            </td>
                            <td>
                                <?php
                                $score = $app['trust_score'];
                                $badgeClass = $score >= 80 ? 'bg-success' : ($score >= 50 ? 'bg-warning text-dark' : 'bg-danger');
                                ?>
                                <span class="badge <?= $badgeClass ?>"><?= number_format($score, 1) ?></span>
                            </td>
                            <td>
                                <?php $sb = ['pending' => 'warning text-dark', 'approved' => 'success', 'rejected' => 'danger']; ?>
                                <span class="badge bg-<?= $sb[$app['approval_status']] ?>">
                                    <?= esc(ucfirst($app['approval_status'])) ?>
                                </span>
                            </td>
                            <td><?= number_format($app['view_count']) ?></td>
                            <td class="text-end">
                                <div class="btn-group btn-group-sm">
                                    <a href="<?= base_url('admin/apps/edit/' . $app['id']) ?>" class="btn btn-outline-primary" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <?php if ($app['approval_status'] === 'pending'): ?>
                                    <form method="post" action="<?= base_url('admin/apps/approve/' . $app['id']) ?>" class="d-inline" onsubmit="return confirm('Approve this app?')">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn btn-outline-success" title="Approve">
                                            <i class="bi bi-check-circle"></i>
                                        </button>
                                    </form>
                                    <form method="post" action="<?= base_url('admin/apps/reject/' . $app['id']) ?>" class="d-inline" onsubmit="return confirm('Reject this app?')">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn btn-outline-warning" title="Reject">
                                            <i class="bi bi-x-circle"></i>
                                        </button>
                                    </form>
                                    <?php endif; ?>
                                    <form method="post" action="<?= base_url('admin/apps/delete/' . $app['id']) ?>" class="d-inline" onsubmit="return confirm('Delete this app and all associated data? This cannot be undone.')">
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
    </div>
    <?php if ($pagination['total_pages'] > 1): ?>
    <div class="card-body border-top">
        <nav>
            <ul class="pagination justify-content-center mb-0">
                <li class="page-item <?= $pagination['current_page'] <= 1 ? 'disabled' : '' ?>">
                    <a class="page-link" href="<?= base_url('admin/apps?page=' . ($pagination['current_page'] - 1) . '&search=' . urlencode($search) . '&status=' . urlencode($status)) ?>">Previous</a>
                </li>
                <?php for ($i = 1; $i <= $pagination['total_pages']; $i++): ?>
                    <?php if ($i == 1 || $i == $pagination['total_pages'] || abs($i - $pagination['current_page']) <= 2): ?>
                    <li class="page-item <?= $i == $pagination['current_page'] ? 'active' : '' ?>">
                        <a class="page-link" href="<?= base_url('admin/apps?page=' . $i . '&search=' . urlencode($search) . '&status=' . urlencode($status)) ?>"><?= $i ?></a>
                    </li>
                    <?php elseif (abs($i - $pagination['current_page']) == 3): ?>
                    <li class="page-item disabled"><span class="page-link">...</span></li>
                    <?php endif; ?>
                <?php endfor; ?>
                <li class="page-item <?= $pagination['current_page'] >= $pagination['total_pages'] ? 'disabled' : '' ?>">
                    <a class="page-link" href="<?= base_url('admin/apps?page=' . ($pagination['current_page'] + 1) . '&search=' . urlencode($search) . '&status=' . urlencode($status)) ?>">Next</a>
                </li>
            </ul>
        </nav>
        <div class="text-center text-muted small mt-2">
            Page <?= $pagination['current_page'] ?> of <?= $pagination['total_pages'] ?> (<?= number_format($pagination['total']) ?> total apps)
        </div>
    </div>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>
