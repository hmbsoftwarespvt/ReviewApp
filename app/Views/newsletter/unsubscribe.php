<?= $this->extend('base_template') ?>
<?= $this->section('content') ?>

<style>
.unsubscribe-container {
    min-height: 70vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 40px 0;
}

.unsubscribe-card {
    max-width: 600px;
    border: none;
    border-radius: 15px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.unsubscribe-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 30px;
    border-radius: 15px 15px 0 0;
    text-align: center;
}

.unsubscribe-body {
    padding: 40px;
}

.icon-sad {
    font-size: 4rem;
    color: rgba(255,255,255,0.9);
}
</style>

<div class="unsubscribe-container">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card unsubscribe-card">
                    <div class="unsubscribe-header">
                        <i class="bi bi-emoji-frown icon-sad"></i>
                        <h2 class="mt-3 mb-0">Unsubscribe from Newsletter</h2>
                    </div>
                    <div class="unsubscribe-body">
                        <div class="text-center mb-4">
                            <p class="lead">We're sorry to see you go!</p>
                            <p class="text-muted">
                                You are about to unsubscribe from our newsletter alerts for:
                            </p>
                            <p class="fw-bold text-primary fs-5">
                                <?= esc($subscriber['email']) ?>
                            </p>
                        </div>

                        <div class="alert alert-warning" role="alert">
                            <i class="bi bi-exclamation-triangle-fill"></i>
                            <strong>Important:</strong> If you unsubscribe, you will no longer receive:
                            <ul class="mt-2 mb-0">
                                <li>High-risk scam alerts</li>
                                <li>Security warnings about dangerous apps</li>
                                <li>Important platform updates</li>
                            </ul>
                        </div>

                        <div class="d-grid gap-3 mt-4">
                            <form action="<?= base_url('newsletter/unsubscribe/' . esc($token)) ?>" method="post">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn btn-danger btn-lg w-100">
                                    <i class="bi bi-x-circle"></i> Yes, Unsubscribe Me
                                </button>
                            </form>

                            <a href="<?= base_url('/') ?>" class="btn btn-outline-primary btn-lg">
                                <i class="bi bi-arrow-left"></i> No, Keep Me Subscribed
                            </a>
                        </div>

                        <div class="text-center mt-4">
                            <p class="text-muted small">
                                Changed your mind? You can always resubscribe later from our homepage.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
