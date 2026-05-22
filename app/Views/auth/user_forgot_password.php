<?= $this->extend('base_template') ?>
<?= $this->section('content') ?>

<section class="at-forgot-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="at-forgot-card">
                    <div class="text-center mb-4">
                        <h2>Forgot Password</h2>
                        <p class="text-muted">Enter your email to receive a password reset link</p>
                    </div>

                    <?php if (session()->has('success')): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?= session('success') ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

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

                    <form action="<?= base_url('forgot-password') ?>" method="post">
                        <?= csrf_field() ?>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email"
                                   class="form-control <?= session('errors.email') ? 'is-invalid' : '' ?>"
                                   id="email"
                                   name="email"
                                   value="<?= old('email') ?>"
                                   placeholder="Enter your email address"
                                   required>
                            <?php if (session('errors.email')): ?>
                                <div class="invalid-feedback"><?= session('errors.email') ?></div>
                            <?php endif; ?>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 at-btn-forgot">
                            Send Reset Link
                        </button>
                    </form>

                    <div class="text-center mt-4">
                        <p class="mb-0">Remember your password?
                            <a href="<?= base_url('login') ?>" class="at-link">Login here</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
.at-forgot-section {
    padding: 4rem 0;
    min-height: 70vh;
    display: flex;
    align-items: center;
    background: #F8FAFC;
}
.at-forgot-card {
    background: #fff;
    border-radius: 18px;
    box-shadow: 0 4px 24px rgba(0,0,0,0.06);
    padding: 2.5rem;
    border: 1px solid #F3F4F6;
}
.at-forgot-card h2 {
    font-size: 1.6rem;
    font-weight: 800;
    color: #111827;
}
.at-btn-forgot {
    background: #2563EB;
    border: none;
    padding: 0.7rem;
    font-weight: 600;
    border-radius: 10px;
    transition: background 0.2s;
}
.at-btn-forgot:hover {
    background: #1D4ED8;
}
.at-link {
    color: #2563EB;
    font-weight: 500;
    text-decoration: none;
}
.at-link:hover {
    text-decoration: underline;
}
</style>

<?= $this->endSection() ?>
