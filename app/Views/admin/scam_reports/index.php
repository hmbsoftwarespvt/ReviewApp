<?= $this->extend('admin/admin_layout') ?>

<?= $this->section('topbar_actions') ?>
<span class="badge bg-primary">Total: <?= number_format($pagination['total']) ?></span>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- Filter Section -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="<?= base_url('admin/scam-reports') ?>" class="row g-3">
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
                <label for="risk_level" class="form-label">Risk Level</label>
                <select name="risk_level" id="risk_level" class="form-select">
                    <option value="">All Levels</option>
                    <option value="high" <?= $filters['risk_level'] === 'high' ? 'selected' : '' ?>>High</option>
                    <option value="medium" <?= $filters['risk_level'] === 'medium' ? 'selected' : '' ?>>Medium</option>
                    <option value="low" <?= $filters['risk_level'] === 'low' ? 'selected' : '' ?>>Low</option>
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

<!-- Scam Reports -->
<?php if (empty($reports)): ?>
    <div class="card">
        <div class="card-body text-center py-5">
            <i class="bi bi-exclamation-triangle" style="font-size:3rem;color:#cbd5e1;"></i>
            <p class="text-muted mt-3 mb-0">No scam reports found matching the selected filters.</p>
        </div>
    </div>
<?php else: ?>
    <div class="row g-4">
        <?php foreach ($reports as $report): ?>
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <h5 class="mb-1"><?= esc($report['title']) ?></h5>
                                    <div class="mb-2">
                                        <span class="badge bg-<?= $report['risk_level'] === 'high' ? 'danger' : ($report['risk_level'] === 'medium' ? 'warning text-dark' : 'info') ?> me-1">
                                            <i class="bi bi-exclamation-triangle"></i> <?= ucfirst($report['risk_level']) ?> Risk
                                        </span>
                                        <span class="badge bg-<?= $report['approval_status'] === 'pending' ? 'warning text-dark' : ($report['approval_status'] === 'approved' ? 'success' : 'danger') ?>">
                                            <?= ucfirst($report['approval_status']) ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <p class="card-text small mb-2"><?= esc($report['description']) ?></p>
                            <?php if (!empty($report['evidence_urls'])): ?>
                                <?php $evidenceUrls = is_string($report['evidence_urls']) ? json_decode($report['evidence_urls'], true) : $report['evidence_urls']; ?>
                                <?php if (is_array($evidenceUrls) && count($evidenceUrls) > 0): ?>
                                <div class="mb-2 small">
                                    <strong><i class="bi bi-link-45deg"></i> Evidence:</strong>
                                    <ul class="list-unstyled ms-3 mt-1">
                                        <?php foreach ($evidenceUrls as $url): ?>
                                        <li><a href="<?= esc($url) ?>" target="_blank" class="small"><?= esc($url) ?> <i class="bi bi-box-arrow-up-right"></i></a></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                                <?php endif; ?>
                            <?php endif; ?>
                            <?php if (!empty($report['verification_notes'])): ?>
                                <div class="mb-2 p-2 bg-light rounded small">
                                    <strong><i class="bi bi-clipboard-check"></i> Notes:</strong>
                                    <p class="mb-0 mt-1"><?= esc($report['verification_notes']) ?></p>
                                </div>
                            <?php endif; ?>
                            <div class="text-muted small mt-2">
                                <i class="bi bi-app"></i> <a href="<?= base_url('apps/' . esc($report['app_slug'])) ?>" target="_blank" class="text-decoration-none"><?= esc($report['app_name']) ?></a>
                                <span class="mx-2">|</span>
                                <i class="bi bi-person"></i> <?= esc($report['username']) ?>
                                <span class="mx-2">|</span>
                                <i class="bi bi-calendar"></i> <?= date('M d, Y H:i', strtotime($report['created_at'])) ?>
                            </div>
                        </div>
                        <div class="col-md-4 d-flex flex-column justify-content-center">
                            <div class="d-grid gap-2">
                                <?php if ($report['approval_status'] === 'pending'): ?>
                                    <div class="mb-2">
                                        <label class="small fw-medium">Verification Notes:</label>
                                        <textarea class="form-control form-control-sm" rows="2" placeholder="Add notes (optional)"
                                                  form="verify-form-<?= $report['id'] ?>" name="verification_notes"></textarea>
                                    </div>
                                    <div class="mb-2">
                                        <label class="small fw-medium">Update Risk Level:</label>
                                        <select class="form-select form-select-sm" form="risk-form-<?= $report['id'] ?>" name="risk_level">
                                            <option value="low" <?= $report['risk_level'] === 'low' ? 'selected' : '' ?>>Low</option>
                                            <option value="medium" <?= $report['risk_level'] === 'medium' ? 'selected' : '' ?>>Medium</option>
                                            <option value="high" <?= $report['risk_level'] === 'high' ? 'selected' : '' ?>>High</option>
                                        </select>
                                    </div>
                                    <form id="risk-form-<?= $report['id'] ?>" method="POST" action="<?= base_url('admin/scam-reports/update-risk/' . $report['id']) ?>" class="d-inline">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn btn-info btn-sm w-100" onclick="return confirm('Update risk level for this report?')">
                                            <i class="bi bi-arrow-repeat"></i> Update Risk
                                        </button>
                                    </form>
                                    <form id="verify-form-<?= $report['id'] ?>" method="POST" action="<?= base_url('admin/scam-reports/verify/' . $report['id']) ?>" class="d-inline">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn btn-success btn-sm w-100" onclick="return confirm('Verify this scam report? <?= $report['risk_level'] === 'high' ? 'Email notifications will be sent to subscribers.' : '' ?>')">
                                            <i class="bi bi-check-circle"></i> Verify
                                        </button>
                                    </form>
                                    <form method="POST" action="<?= base_url('admin/scam-reports/reject/' . $report['id']) ?>" class="d-inline">
                                        <?= csrf_field() ?>
                                        <textarea class="form-control form-control-sm mb-2" rows="2" placeholder="Rejection notes (optional)" name="verification_notes"></textarea>
                                        <button type="submit" class="btn btn-warning btn-sm w-100" onclick="return confirm('Reject this scam report?')">
                                            <i class="bi bi-x-circle"></i> Reject
                                        </button>
                                    </form>
                                <?php elseif ($report['approval_status'] === 'approved'): ?>
                                    <button class="btn btn-outline-success btn-sm w-100" disabled><i class="bi bi-check-circle"></i> Verified</button>
                                    <div class="mb-2">
                                        <label class="small fw-medium">Update Risk Level:</label>
                                        <select class="form-select form-select-sm" form="risk-approved-form-<?= $report['id'] ?>" name="risk_level">
                                            <option value="low" <?= $report['risk_level'] === 'low' ? 'selected' : '' ?>>Low</option>
                                            <option value="medium" <?= $report['risk_level'] === 'medium' ? 'selected' : '' ?>>Medium</option>
                                            <option value="high" <?= $report['risk_level'] === 'high' ? 'selected' : '' ?>>High</option>
                                        </select>
                                    </div>
                                    <form id="risk-approved-form-<?= $report['id'] ?>" method="POST" action="<?= base_url('admin/scam-reports/update-risk/' . $report['id']) ?>">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn btn-info btn-sm w-100" onclick="return confirm('Update risk level?')">
                                            <i class="bi bi-arrow-repeat"></i> Update Risk
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <button class="btn btn-outline-secondary btn-sm w-100" disabled><i class="bi bi-x-circle"></i> Rejected</button>
                                <?php endif; ?>
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
                    <a class="page-link" href="<?= base_url('admin/scam-reports?' . http_build_query(array_merge($filters, ['page' => $pagination['current_page'] - 1]))) ?>">Previous</a>
                </li>
                <?php
                $start = max(1, $pagination['current_page'] - 2);
                $end = min($pagination['total_pages'], $pagination['current_page'] + 2);
                ?>
                <?php if ($start > 1): ?>
                    <li class="page-item"><a class="page-link" href="<?= base_url('admin/scam-reports?' . http_build_query(array_merge($filters, ['page' => 1]))) ?>">1</a></li>
                    <?php if ($start > 2): ?><li class="page-item disabled"><span class="page-link">...</span></li><?php endif; ?>
                <?php endif; ?>
                <?php for ($i = $start; $i <= $end; $i++): ?>
                    <li class="page-item <?= $i === $pagination['current_page'] ? 'active' : '' ?>">
                        <a class="page-link" href="<?= base_url('admin/scam-reports?' . http_build_query(array_merge($filters, ['page' => $i]))) ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>
                <?php if ($end < $pagination['total_pages']): ?>
                    <?php if ($end < $pagination['total_pages'] - 1): ?><li class="page-item disabled"><span class="page-link">...</span></li><?php endif; ?>
                    <li class="page-item"><a class="page-link" href="<?= base_url('admin/scam-reports?' . http_build_query(array_merge($filters, ['page' => $pagination['total_pages']]))) ?>"><?= $pagination['total_pages'] ?></a></li>
                <?php endif; ?>
                <li class="page-item <?= $pagination['current_page'] >= $pagination['total_pages'] ? 'disabled' : '' ?>">
                    <a class="page-link" href="<?= base_url('admin/scam-reports?' . http_build_query(array_merge($filters, ['page' => $pagination['current_page'] + 1]))) ?>">Next</a>
                </li>
            </ul>
        </nav>
    </div>
    <?php endif; ?>
<?php endif; ?>

<?= $this->endSection() ?>
