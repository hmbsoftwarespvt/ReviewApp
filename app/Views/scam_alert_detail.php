<?= $this->extend('base_template') ?>
<?= $this->section('content') ?>

<section class="sa-detail-section">
    <div class="container">
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= base_url('/') ?>">Home</a></li>
                <li class="breadcrumb-item"><a href="<?= base_url('scam-alerts') ?>">Scam Alerts</a></li>
                <li class="breadcrumb-item active"><?= esc($app['name']) ?></li>
            </ol>
        </nav>

        <div class="sa-detail-header">
            <div class="row align-items-center g-4">
                <div class="col-md-8">
                    <h1><?= esc($app['name']) ?></h1>
                    <p class="sa-meta">
                        <i class="bi bi-building"></i> <?= esc($app['developer_name'] ?? 'Unknown') ?>
                        &nbsp;•&nbsp;
                        <i class="bi bi-shield-exclamation"></i> <?= $total_reports ?> report<?= $total_reports !== 1 ? 's' : '' ?>
                    </p>
                    <p class="sa-desc"><?= esc($app['description'] ?? 'No description available') ?></p>
                </div>
                <div class="col-md-4 text-md-end">
                    <a href="<?= base_url('apps/' . esc($app['slug'])) ?>" class="btn btn-outline-primary">
                        <i class="bi bi-arrow-left"></i> View App Details
                    </a>
                </div>
            </div>
        </div>

        <?php if (!empty($reports)): ?>
            <?php foreach ($reports as $report): ?>
                <?php
                $risk = $report['risk_level'] ?? 'low';
                $riskColors = [
                    'high' => ['bg' => '#FEE2E2', 'color' => '#DC2626', 'label' => 'HIGH RISK'],
                    'medium' => ['bg' => '#FEF3C7', 'color' => '#D97706', 'label' => 'MEDIUM RISK'],
                    'low' => ['bg' => '#D1FAE5', 'color' => '#059669', 'label' => 'LOW RISK'],
                ];
                $rc = $riskColors[$risk] ?? $riskColors['low'];
                ?>
                <div class="sa-report-card">
                    <div class="sa-report-header">
                        <span class="sa-risk-badge" style="background:<?= $rc['bg'] ?>;color:<?= $rc['color'] ?>;">
                            <?= $rc['label'] ?>
                        </span>
                        <span class="sa-date">
                            <i class="bi bi-clock"></i> <?= date('M j, Y g:i A', strtotime($report['created_at'])) ?>
                        </span>
                    </div>
                    <p class="sa-report-desc"><?= esc($report['description'] ?? '') ?></p>
                    <?php if (!empty($report['evidence_url'])): ?>
                        <div class="sa-evidence">
                            <i class="bi bi-link-45deg"></i>
                            <a href="<?= esc($report['evidence_url']) ?>" target="_blank" rel="noopener noreferrer">
                                <?= esc($report['evidence_url']) ?>
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>

            <?php if ($pagination['total_pages'] > 1): ?>
                <nav aria-label="Reports pagination" class="mt-4">
                    <ul class="pagination justify-content-center">
                        <?php for ($p = 1; $p <= $pagination['total_pages']; $p++): ?>
                            <li class="page-item <?= ($p === (int)$pagination['current_page']) ? 'active' : '' ?>">
                                <a class="page-link" href="<?= base_url('scam-alerts/' . esc($app['slug']) . '?page=' . $p) ?>">
                                    <?= $p ?>
                                </a>
                            </li>
                        <?php endfor; ?>
                    </ul>
                </nav>
            <?php endif; ?>
        <?php else: ?>
            <div class="sa-empty">
                <i class="bi bi-shield-check"></i>
                <h3>No scam reports</h3>
                <p>There are currently no verified scam reports for this app.</p>
            </div>
        <?php endif; ?>
    </div>
</section>

<style>
.sa-detail-section {
    padding: 3rem 0;
    min-height: 60vh;
    background: #F8FAFC;
}
.sa-detail-header {
    background: #fff;
    border-radius: 18px;
    padding: 2rem;
    box-shadow: 0 2px 12px rgba(0,0,0,0.04);
    border: 1px solid #F3F4F6;
    margin-bottom: 2rem;
}
.sa-detail-header h1 {
    font-size: 1.8rem;
    font-weight: 800;
    color: #111827;
    margin-bottom: 0.5rem;
}
.sa-meta {
    color: #6B7280;
    font-size: 0.95rem;
    margin-bottom: 0.75rem;
}
.sa-desc {
    color: #4B5563;
    font-size: 0.95rem;
    line-height: 1.6;
}
.sa-report-card {
    background: #fff;
    border-radius: 14px;
    padding: 1.25rem 1.5rem;
    margin-bottom: 1rem;
    box-shadow: 0 1px 4px rgba(0,0,0,0.04);
    border: 1px solid #F3F4F6;
}
.sa-report-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 0.75rem;
}
.sa-risk-badge {
    display: inline-block;
    padding: 0.3rem 0.8rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 700;
    letter-spacing: 0.03em;
}
.sa-date {
    font-size: 0.82rem;
    color: #9CA3AF;
}
.sa-report-desc {
    font-size: 0.95rem;
    color: #374151;
    line-height: 1.6;
    margin-bottom: 0.5rem;
}
.sa-evidence {
    font-size: 0.85rem;
    padding: 0.5rem 0.75rem;
    background: #F9FAFB;
    border-radius: 8px;
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
}
.sa-evidence a {
    color: #2563EB;
    word-break: break-all;
}
.sa-empty {
    text-align: center;
    padding: 4rem 2rem;
    color: #9CA3AF;
}
.sa-empty i {
    font-size: 3rem;
    color: #10B981;
    display: block;
    margin-bottom: 1rem;
}
.sa-empty h3 {
    color: #374151;
    font-weight: 700;
}
</style>

<?= $this->endSection() ?>
