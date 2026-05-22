<!-- Navigation -->
<nav class="at-navbar">
    <div class="container">
        <div class="d-flex align-items-center justify-content-between gap-3 flex-wrap">
            <!-- Logo -->
            <a href="<?= base_url('/') ?>" class="navbar-brand">
                <i class="bi bi-shield-fill-check brand-icon"></i>
                AppTrust
            </a>

            <!-- Center Nav Links -->
            <ul class="nav d-none d-lg-flex align-items-center mb-0">
                <li class="nav-item">
                    <a class="nav-link" href="<?= base_url('/') ?>">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= base_url('categories') ?>">Categories</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= base_url('trending') ?>">Trending</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= base_url('scam-alerts') ?>">Scam Alerts</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= base_url('blog') ?>">Blog</a>
                </li>
            </ul>

            <!-- Right: Buttons -->
            <div class="d-flex align-items-center gap-2">
                <?php if (session()->get('isLoggedIn')): ?>
                    <a href="<?= base_url('profile') ?>" class="btn-nav-login">My Profile</a>
                    <?php if (session()->get('role') === 'admin'): ?>
                        <a href="<?= base_url('admin/dashboard') ?>" class="btn-nav-login">Admin</a>
                    <?php endif; ?>
                    <a href="<?= base_url('logout') ?>" class="btn-nav-login">Logout</a>
                <?php else: ?>
                    <a href="<?= base_url('login') ?>" class="btn-nav-login">Login</a>
                    <a href="<?= base_url('register') ?>" class="btn-nav-login">Sign Up</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>
