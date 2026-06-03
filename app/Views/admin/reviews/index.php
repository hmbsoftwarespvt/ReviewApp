<?= $this->extend('admin/admin_layout') ?>

<?= $this->section('topbar_actions') ?>
<span class="badge bg-primary">Total: <?= number_format($pagination['total']) ?></span>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- Filter Section -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="<?= base_url('admin/reviews') ?>" class="row g-3">
            <div class="col-md-3">
                <label for="status" class="form-label">Status</label>
                <select name="status" id="status" class="form-select">
                    <option value="">All Statuses</option>
                    <option value="pending" <?= $filters['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                    <option value="approved" <?= $filters['status'] === 'approved' ? 'selected' : '' ?>>Approved</option>
                    <option value="rejected" <?= $filters['status'] === 'rejected' ? 'selected' : '' ?>>Rejected</option>
                </select>
            </div>
            <div class="col-md-2">
                <label for="rating" class="form-label">Rating</label>
                <select name="rating" id="rating" class="form-select">
                    <option value="">All Ratings</option>
                    <?php for ($r = 5; $r >= 1; $r--): ?>
                    <option value="<?= $r ?>" <?= $filters['rating'] == $r ? 'selected' : '' ?>><?= $r ?> Stars</option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label for="date_from" class="form-label">Date From</label>
                <input type="date" name="date_from" id="date_from" class="form-control" value="<?= esc($filters['date_from']) ?>">
            </div>
            <div class="col-md-3">
                <label for="date_to" class="form-label">Date To</label>
                <input type="date" name="date_to" id="date_to" class="form-control" value="<?= esc($filters['date_to']) ?>">
            </div>
            <div class="col-md-1 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-funnel"></i>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Reviews List -->
<?php if (empty($reviews)): ?>
    <div class="card">
        <div class="card-body text-center py-5">
            <i class="bi bi-star" style="font-size:3rem;color:#cbd5e1;"></i>
            <p class="text-muted mt-3 mb-0">No reviews found matching the selected filters.</p>
        </div>
    </div>
<?php else: ?>
    <div class="row g-4">
        <?php foreach ($reviews as $review): ?>
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <h5 class="mb-1"><?= esc($review['title']) ?></h5>
                                    <div class="mb-2">
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <i class="bi bi-star<?= $i <= $review['rating'] ? '-fill text-warning' : ' text-muted' ?>"></i>
                                        <?php endfor; ?>
                                        <span class="text-muted ms-2 small"><?= $review['rating'] ?>/5</span>
                                    </div>
                                </div>
                                <span class="badge bg-<?= $review['approval_status'] === 'pending' ? 'warning text-dark' : ($review['approval_status'] === 'approved' ? 'success' : 'danger') ?>">
                                    <?= ucfirst($review['approval_status']) ?>
                                </span>
                            </div>
                            <p class="card-text small mb-2"><?= esc($review['review_text']) ?></p>
                            <?php if (!empty($review['pros'])): ?>
                                <div class="mb-1 small"><strong class="text-success"><i class="bi bi-plus-circle"></i> Pros:</strong> <?= esc($review['pros']) ?></div>
                            <?php endif; ?>
                            <?php if (!empty($review['cons'])): ?>
                                <div class="mb-1 small"><strong class="text-danger"><i class="bi bi-dash-circle"></i> Cons:</strong> <?= esc($review['cons']) ?></div>
                            <?php endif; ?>
                            <div class="text-muted small mt-2">
                                <i class="bi bi-app"></i> <a href="<?= base_url('apps/' . esc($review['app_slug'])) ?>" target="_blank" class="text-decoration-none"><?= esc($review['app_name']) ?></a>
                                <span class="mx-2">|</span>
                                <i class="bi bi-person"></i> <?= esc($review['username']) ?>
                                <span class="mx-2">|</span>
                                <i class="bi bi-calendar"></i> <?= date('M d, Y H:i', strtotime($review['created_at'])) ?>
                                <span class="mx-2">|</span>
                                <i class="bi bi-hand-thumbs-up"></i> <?= number_format($review['helpful_count']) ?>
                            </div>
                        </div>
                        <div class="col-md-4 d-flex flex-column justify-content-center">
                            <div class="d-grid gap-2">
                                <?php if ($review['approval_status'] === 'pending'): ?>
                                    <form method="POST" action="<?= base_url('admin/reviews/approve/' . $review['id']) ?>">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn btn-success btn-sm w-100" onclick="return confirm('Approve this review? Trust score will be recalculated.')">
                                            <i class="bi bi-check-circle"></i> Approve
                                        </button>
                                    </form>
                                    <form method="POST" action="<?= base_url('admin/reviews/reject/' . $review['id']) ?>">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn btn-warning btn-sm w-100" onclick="return confirm('Reject this review?')">
                                            <i class="bi bi-x-circle"></i> Reject
                                        </button>
                                    </form>
                                <?php elseif ($review['approval_status'] === 'approved'): ?>
                                    <button class="btn btn-outline-success btn-sm w-100" disabled><i class="bi bi-check-circle"></i> Approved</button>
                                <?php else: ?>
                                    <button class="btn btn-outline-secondary btn-sm w-100" disabled><i class="bi bi-x-circle"></i> Rejected</button>
                                <?php endif; ?>
                                <form method="POST" action="<?= base_url('admin/reviews/delete/' . $review['id']) ?>">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn btn-outline-danger btn-sm w-100" onclick="return confirm('Permanently delete this review? This action cannot be undone.')">
                                        <i class="bi bi-trash"></i> Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <?php if ($pagination['total_pages'] > 1): ?>
    <div class="d-flex justify-content-center mt-4">
        <nav>
            <ul class="pagination">
                <li class="page-item <?= $pagination['current_page'] <= 1 ? 'disabled' : '' ?>">
                    <a class="page-link" href="<?= base_url('admin/reviews?' . http_build_query(array_merge($filters, ['page' => $pagination['current_page'] - 1]))) ?>">Previous</a>
                </li>
                <?php
                $start = max(1, $pagination['current_page'] - 2);
                $end = min($pagination['total_pages'], $pagination['current_page'] + 2);
                ?>
                <?php if ($start > 1): ?>
                    <li class="page-item"><a class="page-link" href="<?= base_url('admin/reviews?' . http_build_query(array_merge($filters, ['page' => 1]))) ?>">1</a></li>
                    <?php if ($start > 2): ?><li class="page-item disabled"><span class="page-link">...</span></li><?php endif; ?>
                <?php endif; ?>
                <?php for ($i = $start; $i <= $end; $i++): ?>
                    <li class="page-item <?= $i === $pagination['current_page'] ? 'active' : '' ?>">
                        <a class="page-link" href="<?= base_url('admin/reviews?' . http_build_query(array_merge($filters, ['page' => $i]))) ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>
                <?php if ($end < $pagination['total_pages']): ?>
                    <?php if ($end < $pagination['total_pages'] - 1): ?><li class="page-item disabled"><span class="page-link">...</span></li><?php endif; ?>
                    <li class="page-item"><a class="page-link" href="<?= base_url('admin/reviews?' . http_build_query(array_merge($filters, ['page' => $pagination['total_pages']]))) ?>"><?= $pagination['total_pages'] ?></a></li>
                <?php endif; ?>
                <li class="page-item <?= $pagination['current_page'] >= $pagination['total_pages'] ? 'disabled' : '' ?>">
                    <a class="page-link" href="<?= base_url('admin/reviews?' . http_build_query(array_merge($filters, ['page' => $pagination['current_page'] + 1]))) ?>">Next</a>
                </li>
            </ul>
        </nav>
    </div>
    <?php endif; ?>
<?php endif; ?>

<?= $this->endSection() ?>
