<?= $this->extend('base_template') ?>
<?= $this->section('content') ?>

<section class="sr-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-7">
                <nav aria-label="breadcrumb" class="mb-4">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= base_url('/') ?>">Home</a></li>
                        <li class="breadcrumb-item"><a href="<?= base_url('scam-alerts') ?>">Scam Alerts</a></li>
                        <li class="breadcrumb-item active">Report a Scam</li>
                    </ol>
                </nav>

                <div class="sr-card">
                    <div class="sr-header">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                        <h2>Report a Scam</h2>
                        <p>Help protect the community by reporting suspicious apps.</p>
                    </div>

                    <?php if (session()->has('error')): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?= session('error') ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <?php if (session()->has('errors')): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <ul class="mb-0">
                                <?php foreach (session('errors') as $error): ?>
                                    <li><?= esc($error) ?></li>
                                <?php endforeach; ?>
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <?php if (!session()->get('isLoggedIn')): ?>
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i>
                            <a href="<?= base_url('login') ?>" class="alert-link">Login</a> or
                            <a href="<?= base_url('register') ?>" class="alert-link">register</a> to report a scam.
                        </div>
                    <?php else: ?>
                        <form action="<?= base_url('scam-alerts/report') ?>" method="POST">
                            <?= csrf_field() ?>

                            <div class="mb-3">
                                <label for="app_name" class="form-label fw-bold">App Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="app_name" name="app_name"
                                       placeholder="Enter the exact app name"
                                       maxlength="255" required
                                       value="<?= old('app_name', $app_name) ?>">
                                <small class="text-muted">Enter the name of the app you want to report</small>
                            </div>

                            <div class="mb-3">
                                <label for="title" class="form-label fw-bold">Report Title <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="title" name="title"
                                       placeholder="Brief summary of the issue"
                                       maxlength="255" required
                                       value="<?= old('title') ?>">
                                <small class="text-muted">Maximum 255 characters</small>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Risk Level <span class="text-danger">*</span></label>
                                <div class="d-flex gap-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="risk_level" id="risk_low" value="low" required <?= old('risk_level') === 'low' ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="risk_low">
                                            <span class="badge bg-success">Low</span>
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="risk_level" id="risk_medium" value="medium" <?= old('risk_level') === 'medium' ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="risk_medium">
                                            <span class="badge bg-warning text-dark">Medium</span>
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="risk_level" id="risk_high" value="high" <?= old('risk_level') === 'high' ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="risk_high">
                                            <span class="badge bg-danger">High</span>
                                        </label>
                                    </div>
                                </div>
                                <small class="text-muted">Select the severity of the issue</small>
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label fw-bold">Detailed Description <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="description" name="description"
                                          rows="6"
                                          placeholder="Provide detailed information about the scam or suspicious behavior (minimum 100 characters)"
                                          minlength="100" maxlength="3000" required><?= old('description') ?></textarea>
                                <small class="text-muted">
                                    <span id="charCount">0</span> / 3000 characters (minimum 100)
                                </small>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Evidence URLs (Optional, max 5)</label>
                                <small class="text-muted d-block mb-2">Provide links to screenshots, articles, or other evidence</small>
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <div class="mb-2">
                                        <input type="url" class="form-control" name="evidence_url_<?= $i ?>"
                                               placeholder="https://example.com/evidence-<?= $i ?>"
                                               maxlength="500"
                                               value="<?= old("evidence_url_{$i}") ?>">
                                    </div>
                                <?php endfor; ?>
                            </div>

                            <div class="alert alert-info">
                                <i class="bi bi-info-circle"></i>
                                <strong>Note:</strong> Your report will be reviewed by our moderation team before being published. False reports may result in account suspension.
                            </div>

                            <button type="submit" class="btn btn-danger btn-lg w-100">
                                <i class="bi bi-send"></i> Submit Scam Report
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
.sr-section {
    padding: 3rem 0;
    min-height: 60vh;
    background: #F8FAFC;
}
.sr-card {
    background: #fff;
    border-radius: 18px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.04);
    border: 1px solid #F3F4F6;
    padding: 2.5rem;
}
.sr-header {
    text-align: center;
    margin-bottom: 2rem;
}
.sr-header i {
    font-size: 2.5rem;
    color: #DC2626;
    display: block;
    margin-bottom: 0.75rem;
}
.sr-header h2 {
    font-size: 1.6rem;
    font-weight: 800;
    color: #111827;
}
.sr-header p {
    color: #6B7280;
    margin: 0;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const textarea = document.getElementById('description');
    const charCount = document.getElementById('charCount');
    if (textarea && charCount) {
        charCount.textContent = textarea.value.length;
        textarea.addEventListener('input', function() {
            charCount.textContent = this.value.length;
        });
    }
});
</script>

<?= $this->endSection() ?>
