<?= $this->extend('base_template') ?>
<?= $this->section('content') ?>

<section class="at-hero">
    <div class="container text-center">
        <h1>
            Browse <span class="highlight">Categories</span>
        </h1>
        <p class="hero-sub">
            Discover apps organized by category. Find the perfect app for your needs.
        </p>
    </div>
</section>

<section class="at-categories-section">
    <div class="container">
        <?php if (empty($categories)): ?>
            <div class="empty-state">
                <i class="bi bi-folder-x"></i>
                <h3>No categories available</h3>
                <p>Check back soon for new app categories.</p>
            </div>
        <?php else: ?>
            <div class="row g-4">
                <?php foreach ($categories as $category): ?>
                    <div class="col-md-6 col-lg-4 col-xl-3">
                        <a href="<?= base_url('categories/' . esc($category['slug'])) ?>" class="category-icon-card">
                            <div class="cat-circle">
                                <?php if (!empty($category['icon'])): ?>
                                    <i class="bi bi-<?= esc($category['icon']) ?>"></i>
                                <?php else: ?>
                                    <i class="bi bi-app"></i>
                                <?php endif; ?>
                            </div>
                            <div class="cat-label">
                                <?= esc($category['name']) ?>
                            </div>
                            <div class="cat-description">
                                <?= esc($category['description'] ?? 'Explore apps in this category') ?>
                            </div>
                            <div class="cat-count">
                                <i class="bi bi-app-indicator"></i>
                                <?= number_format($category['app_count']) ?> 
                                <?= $category['app_count'] == 1 ? 'App' : 'Apps' ?>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="row mt-5">
                <div class="col-12">
                    <div class="scam-alerts-box">
                        <div class="scam-alerts-header">
                            <h3><i class="bi bi-exclamation-triangle"></i> Recent Scam Alerts</h3>
                            <a href="<?= base_url('scam-alerts') ?>">View All</a>
                        </div>
                        <?php if (!empty($scam_reports)): ?>
                            <?php foreach (array_slice($scam_reports, 0, 3) as $report): ?>
                                <div class="scam-alert-item">
                                    <div class="sa-icon">
                                        <i class="bi bi-shield-x"></i>
                                    </div>
                                    <div class="sa-info">
                                        <div class="sa-name"><?= esc($report['app_name'] ?? 'Unknown App') ?></div>
                                        <div class="sa-desc"><?= esc(substr($report['description'] ?? 'Potential scam detected', 0, 80)) ?>...</div>
                                    </div>
                                    <div class="sa-time">
                                        <?= date('M j', strtotime($report['created_at'] ?? 'now')) ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="scam-alert-item">
                                <div class="sa-icon">
                                    <i class="bi bi-check-circle"></i>
                                </div>
                                <div class="sa-info">
                                    <div class="sa-name">No active scam alerts</div>
                                    <div class="sa-desc">All apps are currently safe</div>
                                </div>
                            </div>
                        <?php endif; ?>
                        <div class="scam-alerts-footer">
                            <a href="<?= base_url('scam-alerts/report') ?>" class="btn-report-scam">
                                <i class="bi bi-flag"></i> Report a Scam
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<style>
/* ===== CATEGORIES ===== */
.at-categories-section {
    background: #fff;
    padding: 4rem 0;
    position: relative;
}
.at-categories-section::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 1px;
    background: linear-gradient(90deg, transparent 0%, #E5E7EB 50%, transparent 100%);
}
.category-icon-card {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 1rem;
    cursor: pointer;
    transition: all 0.3s;
    text-decoration: none;
    color: inherit;
    background: #fff;
    border-radius: 20px;
    padding: 2rem 1.5rem;
    border: 1px solid #F3F4F6;
    box-shadow: 0 4px 12px rgba(0,0,0,0.04);
    height: 100%;
    position: relative;
    overflow: hidden;
}
.category-icon-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, #2563EB 0%, #7C3AED 100%);
    opacity: 0;
    transition: opacity 0.3s;
}
.category-icon-card:hover { 
    transform: translateY(-6px);
    box-shadow: 0 12px 28px rgba(37, 99, 235, 0.15);
    border-color: #E5E7EB;
}
.category-icon-card:hover::before { opacity: 1; }
.category-icon-card .cat-circle {
    width: 90px;
    height: 90px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2.2rem;
    color: #fff;
    transition: all 0.3s;
    background: linear-gradient(135deg, #2563EB 0%, #7C3AED 100%);
    box-shadow: 0 6px 20px rgba(37, 99, 235, 0.2);
}
.category-icon-card:hover .cat-circle {
    transform: scale(1.05);
    box-shadow: 0 8px 25px rgba(37, 99, 235, 0.3);
}
.category-icon-card .cat-label {
    font-size: 1rem;
    font-weight: 600;
    color: #374151;
    text-align: center;
    margin-bottom: 0.5rem;
}
.category-icon-card .cat-description {
    font-size: 0.85rem;
    color: #6B7280;
    text-align: center;
    line-height: 1.4;
    margin-bottom: 1rem;
    min-height: 3.5rem;
}
.category-icon-card .cat-count {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    background: linear-gradient(135deg, #F3F4F6 0%, #E5E7EB 100%);
    color: #374151;
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 600;
    border: 1px solid #E5E7EB;
    transition: all 0.2s;
}
.category-icon-card .cat-count:hover {
    background: linear-gradient(135deg, #E5E7EB 0%, #D1D5DB 100%);
    transform: translateY(-1px);
}
.category-icon-card .cat-count i {
    font-size: 0.9rem;
    flex-shrink: 0;
}

/* ===== SCAM ALERTS ===== */
.scam-alerts-box {
    background: #fff;
    border-radius: 14px;
    overflow: hidden;
    box-shadow: 0 1px 4px rgba(0,0,0,0.06);
    margin-top: 2rem;
}
.scam-alerts-header {
    background: #FEF2F2;
    padding: 0.85rem 1.1rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
    border-bottom: 1px solid #FECACA;
}
.scam-alerts-header h3 {
    font-size: 1rem;
    font-weight: 700;
    color: #991B1B;
    margin: 0;
}
.scam-alerts-header a { font-size: 0.82rem; color: #DC2626; font-weight: 600; }
.scam-alert-item {
    display: flex;
    align-items: flex-start;
    gap: 0.75rem;
    padding: 0.9rem 1.1rem;
    border-bottom: 1px solid #F9FAFB;
}
.scam-alert-item:last-of-type { border-bottom: none; }
.scam-alert-item .sa-icon {
    width: 36px; height: 36px;
    border-radius: 50%;
    background: #FEE2E2;
    color: #DC2626;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1rem;
    flex-shrink: 0;
}
.scam-alert-item .sa-info .sa-name {
    font-size: 0.9rem;
    font-weight: 700;
    color: #111827;
}
.scam-alert-item .sa-info .sa-desc {
    font-size: 0.8rem;
    color: #6B7280;
    margin-top: 0.1rem;
}
.scam-alert-item .sa-time {
    font-size: 0.75rem;
    color: #9CA3AF;
    margin-left: auto;
    white-space: nowrap;
    flex-shrink: 0;
}
.scam-alerts-footer {
    padding: 0.85rem 1.1rem;
    border-top: 1px solid #F3F4F6;
}
.btn-report-scam {
    display: block;
    width: 100%;
    border: 1.5px solid #DC2626;
    color: #DC2626;
    background: transparent;
    border-radius: 8px;
    padding: 0.5rem;
    font-size: 0.88rem;
    font-weight: 600;
    text-align: center;
    transition: all 0.2s;
    text-decoration: none;
}
.btn-report-scam:hover { background: #FEF2F2; }

/* ===== EMPTY STATE ===== */
.empty-state {
    text-align: center;
    padding: 3rem 2rem;
    color: #9CA3AF;
    font-size: 0.9rem;
}
.empty-state i { font-size: 3rem; display: block; margin-bottom: 1rem; color: #6B7280; }

@media (max-width: 768px) {
    .category-icon-card .cat-circle {
        width: 64px;
        height: 64px;
        font-size: 1.6rem;
    }
}
</style>

<?= $this->endSection() ?>
