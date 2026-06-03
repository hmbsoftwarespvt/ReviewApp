<?= $this->extend('admin/admin_layout') ?>

<?= $this->section('topbar_actions') ?>
<span class="badge bg-primary">Total: <?= number_format($pagination['total']) ?></span>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- Search -->
<div class="card mb-4">
    <div class="card-body">
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
</div>

<!-- Users Table -->
<?php if (empty($users)): ?>
<div class="card">
    <div class="card-body text-center py-5">
        <i class="bi bi-people" style="font-size:3rem;color:#cbd5e1;"></i>
        <p class="text-muted mt-3 mb-0">No users found<?= !empty($search) ? ' matching your search' : '' ?>.</p>
    </div>
</div>
<?php else: ?>
<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
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
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                    <tr>
                        <td class="text-muted"><?= esc($user['id']) ?></td>
                        <td>
                            <span class="fw-medium"><?= esc($user['username']) ?></span>
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
                            <span class="badge bg-<?= $user['status'] === 'active' ? 'success' : ($user['status'] === 'suspended' ? 'warning text-dark' : 'dark') ?>">
                                <?= ucfirst(esc($user['status'])) ?>
                            </span>
                        </td>
                        <td><span class="badge bg-info"><?= number_format($user['review_count']) ?></span></td>
                        <td><span class="badge bg-warning text-dark"><?= number_format($user['scam_report_count']) ?></span></td>
                        <td class="small text-muted"><?= date('M d, Y', strtotime($user['created_at'])) ?></td>
                        <td class="small text-muted">
                            <?php if ($user['last_login']): ?>
                                <?= date('M d, Y', strtotime($user['last_login'])) ?>
                            <?php else: ?>
                                Never
                            <?php endif; ?>
                        </td>
                        <td class="text-end">
                            <div class="btn-group btn-group-sm">
                                <a href="<?= base_url('admin/users/view/' . $user['id']) ?>" class="btn btn-outline-primary" title="View Details">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <?php if ($user['role'] !== 'admin'): ?>
                                    <?php if ($user['status'] === 'active'): ?>
                                        <form method="POST" action="<?= base_url('admin/users/suspend/' . $user['id']) ?>" class="d-inline">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-outline-warning" title="Suspend"
                                                    onclick="return confirm('Suspend this user? They will not be able to login.')">
                                                <i class="bi bi-pause-circle"></i>
                                            </button>
                                        </form>
                                    <?php elseif ($user['status'] === 'suspended'): ?>
                                        <form method="POST" action="<?= base_url('admin/users/reactivate/' . $user['id']) ?>" class="d-inline">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-outline-success" title="Reactivate"
                                                    onclick="return confirm('Reactivate this user? They will be able to login again.')">
                                                <i class="bi bi-play-circle"></i>
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                    <form method="POST" action="<?= base_url('admin/users/delete/' . $user['id']) ?>" class="d-inline">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn btn-outline-danger" title="Delete"
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
    </div>
    <?php if ($pagination['total_pages'] > 1): ?>
    <div class="card-body border-top">
        <nav>
            <ul class="pagination justify-content-center mb-0">
                <li class="page-item <?= $pagination['current_page'] <= 1 ? 'disabled' : '' ?>">
                    <a class="page-link" href="<?= base_url('admin/users?' . http_build_query(['search' => $search, 'page' => $pagination['current_page'] - 1])) ?>">Previous</a>
                </li>
                <?php
                $start = max(1, $pagination['current_page'] - 2);
                $end = min($pagination['total_pages'], $pagination['current_page'] + 2);
                ?>
                <?php if ($start > 1): ?>
                    <li class="page-item"><a class="page-link" href="<?= base_url('admin/users?' . http_build_query(['search' => $search, 'page' => 1])) ?>">1</a></li>
                    <?php if ($start > 2): ?><li class="page-item disabled"><span class="page-link">...</span></li><?php endif; ?>
                <?php endif; ?>
                <?php for ($i = $start; $i <= $end; $i++): ?>
                    <li class="page-item <?= $i === $pagination['current_page'] ? 'active' : '' ?>">
                        <a class="page-link" href="<?= base_url('admin/users?' . http_build_query(['search' => $search, 'page' => $i])) ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>
                <?php if ($end < $pagination['total_pages']): ?>
                    <?php if ($end < $pagination['total_pages'] - 1): ?><li class="page-item disabled"><span class="page-link">...</span></li><?php endif; ?>
                    <li class="page-item"><a class="page-link" href="<?= base_url('admin/users?' . http_build_query(['search' => $search, 'page' => $pagination['total_pages']])) ?>"><?= $pagination['total_pages'] ?></a></li>
                <?php endif; ?>
                <li class="page-item <?= $pagination['current_page'] >= $pagination['total_pages'] ? 'disabled' : '' ?>">
                    <a class="page-link" href="<?= base_url('admin/users?' . http_build_query(['search' => $search, 'page' => $pagination['current_page'] + 1])) ?>">Next</a>
                </li>
            </ul>
        </nav>
    </div>
    <?php endif; ?>
</div>
<?php endif; ?>

<?= $this->endSection() ?>
