<?= $this->extend('base_template') ?>
<?= $this->section('content') ?>

<section class="at-register-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="at-register-card">
                    <div class="text-center mb-4">
                        <h2>Create Account</h2>
                        <p class="text-muted">Join AppTrust Platform</p>
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

                    <form action="<?= base_url('auth/register') ?>" method="post">
                        <?= csrf_field() ?>

                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text"
                                   class="form-control <?= session('errors.username') ? 'is-invalid' : '' ?>"
                                   id="username"
                                   name="username"
                                   value="<?= old('username') ?>"
                                   placeholder="Enter username"
                                   required>
                            <?php if (session('errors.username')): ?>
                                <div class="invalid-feedback"><?= session('errors.username') ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email"
                                   class="form-control <?= session('errors.email') ? 'is-invalid' : '' ?>"
                                   id="email"
                                   name="email"
                                   value="<?= old('email') ?>"
                                   placeholder="Enter email"
                                   required>
                            <?php if (session('errors.email')): ?>
                                <div class="invalid-feedback"><?= session('errors.email') ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password"
                                   class="form-control <?= session('errors.password') ? 'is-invalid' : '' ?>"
                                   id="password"
                                   name="password"
                                   placeholder="Enter password (min 8 characters)"
                                   required>
                            <?php if (session('errors.password')): ?>
                                <div class="invalid-feedback"><?= session('errors.password') ?></div>
                            <?php endif; ?>
                            <small class="text-muted">Minimum 8 characters</small>
                        </div>

                        <div class="mb-4">
                            <label for="password_confirm" class="form-label">Confirm Password</label>
                            <input type="password"
                                   class="form-control <?= session('errors.password_confirm') ? 'is-invalid' : '' ?>"
                                   id="password_confirm"
                                   name="password_confirm"
                                   placeholder="Confirm password"
                                   required>
                            <?php if (session('errors.password_confirm')): ?>
                                <div class="invalid-feedback"><?= session('errors.password_confirm') ?></div>
                            <?php endif; ?>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 at-btn-register">
                            Create Account
                        </button>
                    </form>

                    <div class="alert alert-info mt-3">
                        <i class="bi bi-info-circle"></i>
                        After registration, you must verify your email address before you can log in.
                    </div>

                    <div class="text-center mt-4">
                        <p class="mb-0">Already have an account?
                            <a href="<?= base_url('login') ?>" class="at-link">Login here</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
.at-register-section {
    padding: 4rem 0;
    min-height: 70vh;
    display: flex;
    align-items: center;
    background: #F8FAFC;
}
.at-register-card {
    background: #fff;
    border-radius: 18px;
    box-shadow: 0 4px 24px rgba(0,0,0,0.06);
    padding: 2.5rem;
    border: 1px solid #F3F4F6;
}
.at-register-card h2 {
    font-size: 1.6rem;
    font-weight: 800;
    color: #111827;
}
.at-btn-register {
    background: #2563EB;
    border: none;
    padding: 0.7rem;
    font-weight: 600;
    border-radius: 10px;
    transition: background 0.2s;
}
.at-btn-register:hover {
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
