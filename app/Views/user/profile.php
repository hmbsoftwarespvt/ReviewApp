<?= $this->extend('base_template') ?>
<?= $this->section('content') ?>

<section class="profile-section">
    <div class="container">
        <div class="row">
            <div class="col-lg-4 mb-4">
                <div class="profile-card">
                    <div class="text-center">
                        <div class="profile-avatar">
                            <?= strtoupper(substr($user['username'] ?? 'U', 0, 2)) ?>
                        </div>
                        <h4><?= esc($user['username']) ?></h4>
                        <p class="text-muted"><?= esc($user['email']) ?></p>
                        <span class="badge bg-<?= $user['role'] === 'admin' ? 'danger' : 'primary' ?>">
                            <?= ucfirst(esc($user['role'])) ?>
                        </span>
                        <p class="mt-3 small text-muted">
                            <i class="bi bi-calendar3"></i> Joined <?= date('M Y', strtotime($user['created_at'] ?? 'now')) ?>
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <ul class="nav nav-tabs mb-4" id="profileTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="reviews-tab" data-bs-toggle="tab" data-bs-target="#reviews" type="button" role="tab">
                            <i class="bi bi-star"></i> My Reviews (<?= count($reviews) ?>)
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="reports-tab" data-bs-toggle="tab" data-bs-target="#reports" type="button" role="tab">
                            <i class="bi bi-exclamation-triangle"></i> My Reports (<?= count($scam_reports) ?>)
                        </button>
                    </li>
                </ul>

                <div class="tab-content" id="profileTabsContent">
                    <div class="tab-pane fade show active" id="reviews" role="tabpanel">
                        <?php if (empty($reviews)): ?>
                            <div class="empty-state">
                                <i class="bi bi-star"></i>
                                <h5>No reviews yet</h5>
                                <p class="text-muted">You haven't submitted any reviews.</p>
                                <a href="<?= base_url('search') ?>" class="btn btn-primary">Browse Apps</a>
                            </div>
                        <?php else: ?>
                            <?php foreach ($reviews as $review): ?>
                                <div class="item-card">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h6><?= esc($review['title'] ?? 'Review') ?></h6>
                                            <span class="badge bg-<?= $review['approval_status'] === 'approved' ? 'success' : ($review['approval_status'] === 'pending' ? 'warning text-dark' : 'secondary') ?>">
                                                <?= ucfirst(esc($review['approval_status'] ?? 'pending')) ?>
                                            </span>
                                        </div>
                                        <small class="text-muted"><?= date('M j, Y', strtotime($review['created_at'])) ?></small>
                                    </div>
                                    <p class="mt-2 mb-0 text-muted small"><?= esc(substr($review['review_text'] ?? $review['comment'] ?? '', 0, 150)) ?></p>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>

                    <div class="tab-pane fade" id="reports" role="tabpanel">
                        <?php if (empty($scam_reports)): ?>
                            <div class="empty-state">
                                <i class="bi bi-shield-exclamation"></i>
                                <h5>No scam reports</h5>
                                <p class="text-muted">You haven't submitted any scam reports.</p>
                                <a href="<?= base_url('scam-alerts/report') ?>" class="btn btn-danger">Report a Scam</a>
                            </div>
                        <?php else: ?>
                            <?php foreach ($scam_reports as $report): ?>
                                <div class="item-card">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h6><?= esc($report['title'] ?? 'Report') ?></h6>
                                            <span class="badge bg-<?= $report['risk_level'] === 'high' ? 'danger' : ($report['risk_level'] === 'medium' ? 'warning text-dark' : 'success') ?>">
                                                <?= strtoupper(esc($report['risk_level'])) ?>
                                            </span>
                                            <span class="badge bg-<?= $report['approval_status'] === 'approved' ? 'success' : ($report['approval_status'] === 'pending' ? 'warning text-dark' : 'secondary') ?> ms-1">
                                                <?= ucfirst(esc($report['approval_status'] ?? 'pending')) ?>
                                            </span>
                                        </div>
                                        <small class="text-muted"><?= date('M j, Y', strtotime($report['created_at'])) ?></small>
                                    </div>
                                    <p class="mt-2 mb-0 text-muted small"><?= esc(substr($report['description'] ?? '', 0, 150)) ?></p>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
.profile-section {
    padding: 3rem 0;
    min-height: 60vh;
    background: #F8FAFC;
}
.profile-card {
    background: #fff;
    border-radius: 18px;
    padding: 2rem;
    box-shadow: 0 2px 12px rgba(0,0,0,0.04);
    border: 1px solid #F3F4F6;
}
.profile-avatar {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: linear-gradient(135deg, #2563EB, #7C3AED);
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    font-weight: 700;
    margin: 0 auto 1rem;
}
.item-card {
    background: #fff;
    border-radius: 14px;
    padding: 1.25rem;
    margin-bottom: 0.75rem;
    box-shadow: 0 1px 4px rgba(0,0,0,0.04);
    border: 1px solid #F3F4F6;
}
.empty-state {
    text-align: center;
    padding: 3rem 2rem;
    background: #fff;
    border-radius: 18px;
    border: 1px solid #F3F4F6;
}
.empty-state i {
    font-size: 2.5rem;
    color: #9CA3AF;
    display: block;
    margin-bottom: 1rem;
}
.nav-tabs .nav-link {
    color: #6B7280;
    font-weight: 600;
    border: none;
    padding: 0.75rem 1.25rem;
}
.nav-tabs .nav-link.active {
    color: #2563EB;
    border-bottom: 2px solid #2563EB;
    background: transparent;
}
</style>

<?= $this->endSection() ?>
