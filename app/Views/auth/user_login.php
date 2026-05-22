<?= $this->extend('base_template') ?>
<?= $this->section('content') ?>

<section class="at-login-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="at-login-card">
                    <div class="text-center mb-4">
                        <h2>Welcome Back</h2>
                        <p class="text-muted">Sign in to your account</p>
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

                    <form action="<?= base_url('auth/login') ?>" method="post">
                        <?= csrf_field() ?>

                        <div class="mb-3">
                            <label for="identifier" class="form-label">Email or Username</label>
                            <input type="text"
                                   class="form-control <?= session('errors.identifier') ? 'is-invalid' : '' ?>"
                                   id="identifier"
                                   name="identifier"
                                   value="<?= old('identifier') ?>"
                                   placeholder="Enter email or username"
                                   required>
                            <?php if (session('errors.identifier')): ?>
                                <div class="invalid-feedback"><?= session('errors.identifier') ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <div class="input-group">
                                <input type="password"
                                       class="form-control <?= session('errors.password') ? 'is-invalid' : '' ?>"
                                       id="password"
                                       name="password"
                                       placeholder="Enter password"
                                       required>
                                <button type="button" class="btn btn-outline-secondary" id="togglePassword" tabindex="-1">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                            <?php if (session('errors.password')): ?>
                                <div class="invalid-feedback d-block"><?= session('errors.password') ?></div>
                            <?php endif; ?>
                        </div>

                        <script>
                        document.getElementById('togglePassword')?.addEventListener('click', function() {
                            const input = document.getElementById('password');
                            const icon = this.querySelector('i');
                            if (input.type === 'password') {
                                input.type = 'text';
                                icon.classList.replace('bi-eye', 'bi-eye-slash');
                            } else {
                                input.type = 'password';
                                icon.classList.replace('bi-eye-slash', 'bi-eye');
                            }
                        });
                        </script>

                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="remember" name="remember">
                            <label class="form-check-label" for="remember">Remember me</label>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 at-btn-login">
                            Sign In
                        </button>
                    </form>

                    <div class="text-center mt-4">
                        <p class="mb-1">
                            <a href="<?= base_url('auth/forgot-password') ?>" class="at-link">Forgot password?</a>
                        </p>
                        <p class="mb-0">Don't have an account?
                            <a href="<?= base_url('register') ?>" class="at-link">Register here</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
.at-login-section {
    padding: 4rem 0;
    min-height: 70vh;
    display: flex;
    align-items: center;
    background: #F8FAFC;
}
.at-login-card {
    background: #fff;
    border-radius: 18px;
    box-shadow: 0 4px 24px rgba(0,0,0,0.06);
    padding: 2.5rem;
    border: 1px solid #F3F4F6;
}
.at-login-card h2 {
    font-size: 1.6rem;
    font-weight: 800;
    color: #111827;
}
.at-btn-login {
    background: #2563EB;
    border: none;
    padding: 0.7rem;
    font-weight: 600;
    border-radius: 10px;
    transition: background 0.2s;
}
.at-btn-login:hover {
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
