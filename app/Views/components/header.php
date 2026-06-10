<!-- Navigation -->
<nav class="at-navbar">
    <div class="container">
        <div class="nav-top-row">
            <a href="<?= base_url('/') ?>" class="navbar-brand">
                <i class="bi bi-shield-fill-check brand-icon"></i>
                AppTrust
            </a>

            <ul class="nav-links nav-links-dt">
                <li><a class="nav-link" href="<?= base_url('/') ?>">Home</a></li>
                <li><a class="nav-link" href="<?= base_url('categories') ?>">Categories</a></li>
                <li><a class="nav-link" href="<?= base_url('trending') ?>">Trending</a></li>
                <li><a class="nav-link" href="<?= base_url('scam-alerts') ?>">Scam Alerts</a></li>
                <li><a class="nav-link" href="<?= base_url('blog') ?>">Blog</a></li>
            </ul>

            <button class="navbar-toggler" type="button" onclick="toggleMobileMenu()"
                    aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="nav-auth nav-auth-dt">
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

        <div class="nav-collapse" id="mainNav">
            <ul class="nav-links nav-links-mb">
                <li><a class="nav-link" href="<?= base_url('/') ?>">Home</a></li>
                <li><a class="nav-link" href="<?= base_url('categories') ?>">Categories</a></li>
                <li><a class="nav-link" href="<?= base_url('trending') ?>">Trending</a></li>
                <li><a class="nav-link" href="<?= base_url('scam-alerts') ?>">Scam Alerts</a></li>
                <li><a class="nav-link" href="<?= base_url('blog') ?>">Blog</a></li>
            </ul>
            <div class="nav-auth nav-auth-mb">
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

<script>
function toggleMobileMenu() {
    document.getElementById('mainNav').classList.toggle('show');
    var btn = document.querySelector('.navbar-toggler');
    var expanded = btn.getAttribute('aria-expanded') === 'true' ? 'false' : 'true';
    btn.setAttribute('aria-expanded', expanded);
}
</script>
