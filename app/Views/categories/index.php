<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title) ?> - AppTrust Platform</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        /* ===== BASE ===== */
        *, *::before, *::after { box-sizing: border-box; }
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            color: #1a1a2e;
            background: #fff;
            margin: 0;
        }
        a { text-decoration: none; }

        /* ===== NAVBAR ===== */
        .at-navbar {
            background: #fff;
            border-bottom: 1px solid #e5e7eb;
            padding: 0.75rem 0;
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        .at-navbar .navbar-brand {
            font-weight: 800;
            font-size: 1.35rem;
            color: #1a1a2e;
            display: flex;
            align-items: center;
            gap: 0.4rem;
        }
        .at-navbar .navbar-brand .brand-icon {
            color: #2563EB;
            font-size: 1.5rem;
        }
        .at-navbar .nav-link {
            color: #4B5563;
            font-weight: 500;
            font-size: 0.92rem;
            padding: 0.4rem 0.75rem;
            border-bottom: 2px solid transparent;
            transition: color 0.2s, border-color 0.2s;
        }
        .at-navbar .nav-link:hover { color: #2563EB; }
        .at-navbar .nav-link.active {
            color: #2563EB;
            border-bottom-color: #2563EB;
        }
        .btn-nav-login {
            border: 1.5px solid #2563EB;
            color: #2563EB;
            background: transparent;
            border-radius: 8px;
            padding: 0.38rem 1rem;
            font-size: 0.88rem;
            font-weight: 600;
            transition: all 0.2s;
        }
        .btn-nav-login:hover { background: #EFF6FF; }

        /* ===== HERO ===== */
        .at-hero {
            background: linear-gradient(135deg, #F8FAFC 0%, #E0E7FF 100%);
            padding: 5rem 0 4rem;
            position: relative;
            overflow: hidden;
        }
        .at-hero::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -10%;
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(37, 99, 235, 0.1) 0%, transparent 70%);
            border-radius: 50%;
        }
        .at-hero::after {
            content: '';
            position: absolute;
            bottom: -30%;
            left: -5%;
            width: 300px;
            height: 300px;
            background: radial-gradient(circle, rgba(124, 58, 237, 0.08) 0%, transparent 70%);
            border-radius: 50%;
        }
        .at-hero .container {
            position: relative;
            z-index: 1;
        }
        .at-hero h1 {
            font-size: 3.2rem;
            font-weight: 800;
            line-height: 1.2;
            color: #111827;
            margin-bottom: 1.5rem;
        }
        .at-hero h1 .highlight { 
            color: #2563EB;
            position: relative;
        }
        .at-hero h1 .highlight::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, #2563EB 0%, #7C3AED 100%);
            border-radius: 2px;
        }
        .at-hero .hero-sub {
            color: #6B7280;
            font-size: 1.1rem;
            margin: 0 auto 2rem;
            max-width: 600px;
            line-height: 1.6;
        }

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
        .at-categories-section h2 {
            font-size: 2rem;
            font-weight: 700;
            color: #111827;
            text-align: center;
            margin-bottom: 3rem;
            position: relative;
        }
        .at-categories-section h2::after {
            content: '';
            position: absolute;
            bottom: -12px;
            left: 50%;
            transform: translateX(-50%);
            width: 60px;
            height: 3px;
            background: linear-gradient(90deg, #2563EB 0%, #7C3AED 100%);
            border-radius: 2px;
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
        .category-icon-card:hover::before {
            opacity: 1;
        }
        .category-icon-card .cat-circle {
            width: 90px;
            height: 90px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.2rem;
            font-weight: 700;
            color: #fff;
            transition: all 0.3s;
            background: linear-gradient(135deg, #2563EB 0%, #7C3AED 100%);
            box-shadow: 0 6px 20px rgba(37, 99, 235, 0.2);
            position: relative;
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
            justify-content: center;
            gap: 0.4rem;
            background: linear-gradient(135deg, #F3F4F6 0%, #E5E7EB 100%);
            color: #374151;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
            border: 1px solid #E5E7EB;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
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

        /* ===== FOOTER ===== */
        .at-footer {
            background: #1a1a2e;
            color: #9CA3AF;
            padding: 3rem 0 0;
            margin-top: 3rem;
        }
        .at-footer .footer-brand {
            font-size: 1.2rem;
            font-weight: 800;
            color: #fff;
            display: flex;
            align-items: center;
            gap: 0.4rem;
            margin-bottom: 0.75rem;
        }
        .at-footer .footer-brand i { color: #2563EB; }
        .at-footer .footer-tagline { font-size: 0.85rem; color: #6B7280; max-width: 220px; }
        .at-footer h6 {
            color: #fff;
            font-weight: 700;
            font-size: 0.9rem;
            margin-bottom: 1rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .at-footer ul { list-style: none; padding: 0; margin: 0; }
        .at-footer ul li { margin-bottom: 0.5rem; }
        .at-footer ul li a {
            color: #9CA3AF;
            font-size: 0.88rem;
            transition: color 0.2s;
        }
        .at-footer ul li a:hover { color: #fff; }
        .social-icons { display: flex; gap: 0.6rem; margin-top: 0.5rem; }
        .social-icons a {
            width: 36px; height: 36px;
            border-radius: 8px;
            background: #2d2d4e;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #9CA3AF;
            font-size: 1rem;
            transition: all 0.2s;
        }
        .social-icons a:hover { background: #2563EB; color: #fff; }
        .footer-bottom {
            border-top: 1px solid #2d2d4e;
            padding: 1.25rem 0;
            margin-top: 2.5rem;
            text-align: center;
            font-size: 0.82rem;
            color: #6B7280;
        }

        /* ===== EMPTY STATE ===== */
        .empty-state {
            text-align: center;
            padding: 3rem 2rem;
            color: #9CA3AF;
            font-size: 0.9rem;
        }
        .empty-state i { font-size: 3rem; display: block; margin-bottom: 1rem; color: #6B7280; }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 768px) {
            .at-hero { padding: 2.5rem 0; }
            .at-hero h1 { font-size: 1.7rem; }
            .category-icon-card .cat-circle {
                width: 64px;
                height: 64px;
                font-size: 1.6rem;
            }
        }
    </style>
</head>
<body>
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
                        <a class="nav-link active" href="<?= base_url('categories') ?>">Categories</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('trending') ?>">Trending</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('scam-alerts') ?>">Scam Alerts</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('compare') ?>">Compare</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('blog') ?>">Blog</a>
                    </li>
                </ul>

                <!-- Right: Buttons -->
                <div class="d-flex align-items-center gap-2">
                    <?php if (session()->get('isLoggedIn')): ?>
                        <a href="<?= base_url('dashboard') ?>" class="btn-nav-login">Dashboard</a>
                        <a href="<?= base_url('logout') ?>" class="btn-nav-login">Logout</a>
                    <?php else: ?>
                        <a href="<?= base_url('login') ?>" class="btn-nav-login">Login</a>
                        <a href="<?= base_url('register') ?>" class="btn-nav-login">Sign Up</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <!-- Page Header -->
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

    <!-- Categories Grid -->
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
            <?php endif; ?>

            <!-- Scam Alerts Section -->
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
        </div>
    </section>

    <!-- Footer -->
    <footer class="at-footer">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 mb-4">
                    <div class="footer-brand">
                        <i class="bi bi-shield-fill-check"></i>
                        AppTrust
                    </div>
                    <p class="footer-tagline">
                        Your trusted source for app reviews, trust scores, and scam alerts. Make informed decisions about app safety.
                    </p>
                    <div class="social-icons">
                        <a href="#"><i class="bi bi-twitter"></i></a>
                        <a href="#"><i class="bi bi-facebook"></i></a>
                        <a href="#"><i class="bi bi-linkedin"></i></a>
                        <a href="#"><i class="bi bi-instagram"></i></a>
                    </div>
                </div>
                <div class="col-lg-2 col-md-6 mb-4">
                    <h6>Platform</h6>
                    <ul>
                        <li><a href="<?= base_url('apps') ?>">Browse Apps</a></li>
                        <li><a href="<?= base_url('categories') ?>">Categories</a></li>
                        <li><a href="<?= base_url('scam-alerts') ?>">Scam Alerts</a></li>
                        <li><a href="<?= base_url('compare') ?>">Compare Apps</a></li>
                    </ul>
                </div>
                <div class="col-lg-2 col-md-6 mb-4">
                    <h6>Resources</h6>
                    <ul>
                        <li><a href="<?= base_url('blog') ?>">Blog</a></li>
                        <li><a href="#">About Us</a></li>
                        <li><a href="#">Contact</a></li>
                        <li><a href="#">FAQ</a></li>
                    </ul>
                </div>
                <div class="col-lg-2 col-md-6 mb-4">
                    <h6>Legal</h6>
                    <ul>
                        <li><a href="#">Privacy Policy</a></li>
                        <li><a href="#">Terms of Service</a></li>
                        <li><a href="#">Cookie Policy</a></li>
                    </ul>
                </div>
                <div class="col-lg-2 col-md-6 mb-4">
                    <h6>Newsletter</h6>
                    <p style="color: #9CA3AF; font-size: 0.85rem; margin-bottom: 1rem;">Get the latest scam alerts and trusted app reviews</p>
                    <form style="display: flex; gap: 0.5rem;">
                        <input type="email" placeholder="Your email" style="flex: 1; padding: 0.5rem; border: 1px solid #2d2d4e; background: #2d2d4e; border-radius: 6px; color: #fff; font-size: 0.85rem;">
                        <button type="submit" style="background: #2563EB; color: #fff; border: none; padding: 0.5rem 1rem; border-radius: 6px; font-size: 0.85rem; font-weight: 600;">Subscribe</button>
                    </form>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; <?= date('Y') ?> AppTrust Platform. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

