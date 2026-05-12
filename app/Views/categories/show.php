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

        /* ===== CATEGORY HEADER ===== */
        .category-header {
            background: linear-gradient(135deg, #F8FAFC 0%, #E0E7FF 100%);
            color: #111827;
            padding: 4rem 0 3rem;
            margin-bottom: 3rem;
        }
        .category-icon-large {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background: linear-gradient(135deg, #2563EB 0%, #7C3AED 100%);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            margin-bottom: 1.5rem;
            color: white;
            box-shadow: 0 8px 25px rgba(37, 99, 235, 0.25);
        }
        .breadcrumb {
            background: transparent;
            padding: 0;
            margin-bottom: 1.5rem;
        }
        .breadcrumb-item a {
            color: #6B7280;
            text-decoration: none;
            font-weight: 500;
        }
        .breadcrumb-item a:hover {
            color: #2563EB;
        }
        .breadcrumb-item.active {
            color: #111827;
            font-weight: 600;
        }
        .breadcrumb-item + .breadcrumb-item::before {
            color: #9CA3AF;
        }

        /* ===== APP CARDS ===== */
        .app-card {
            border: none;
            border-radius: 16px;
            overflow: hidden;
            transition: all 0.3s;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            height: 100%;
            background: #fff;
            border: 1px solid #F3F4F6;
        }
        .app-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 24px rgba(0,0,0,0.1);
        }
        .app-thumbnail {
            width: 100%;
            height: 180px;
            object-fit: cover;
            background: linear-gradient(135deg, #2563EB 0%, #7C3AED 100%);
        }
        .trust-badge {
            position: absolute;
            top: 12px;
            right: 12px;
            padding: 8px 15px;
            border-radius: 20px;
            font-weight: 700;
            font-size: 14px;
            color: white;
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
        }
        .trust-high {
            background: #10B981;
        }
        .trust-medium {
            background: #F59E0B;
        }
        .trust-low {
            background: #EF4444;
        }
        .category-badge {
            display: inline-block;
            padding: 6px 12px;
            background: #F3F4F6;
            border-radius: 12px;
            font-size: 0.85rem;
            color: #374151;
            margin-top: 8px;
            font-weight: 500;
        }
        .no-apps {
            text-align: center;
            padding: 4rem 2rem;
        }
        .no-apps i {
            font-size: 4rem;
            color: #9CA3AF;
            margin-bottom: 1.5rem;
        }
        .pagination {
            margin-top: 3rem;
        }
        .page-link {
            color: #2563EB;
            border-color: #E5E7EB;
            font-weight: 500;
        }
        .page-link:hover {
            color: #1D4ED8;
            background-color: #F8FAFC;
            border-color: #E5E7EB;
        }
        .page-item.active .page-link {
            background-color: #2563EB;
            border-color: #2563EB;
        }

        /* ===== SCAM ALERTS ===== */
        .scam-alerts-box {
            background: #fff;
            border-radius: 14px;
            overflow: hidden;
            box-shadow: 0 1px 4px rgba(0,0,0,0.06);
            margin-top: 3rem;
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

        /* ===== RESPONSIVE ===== */
        @media (max-width: 768px) {
            .category-header { padding: 2.5rem 0 2rem; }
            .category-icon-large {
                width: 80px;
                height: 80px;
                font-size: 2.5rem;
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

    <!-- Category Header -->
    <section class="category-header">
        <div class="container">
            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= base_url('/') ?>">Home</a></li>
                    <li class="breadcrumb-item"><a href="<?= base_url('categories') ?>">Categories</a></li>
                    <li class="breadcrumb-item active" aria-current="page"><?= esc($category['name']) ?></li>
                </ol>
            </nav>
            
            <div class="text-center">
                <div class="category-icon-large">
                    <?php if (!empty($category['icon'])): ?>
                        <i class="bi bi-<?= esc($category['icon']) ?>"></i>
                    <?php else: ?>
                        <i class="bi bi-app"></i>
                    <?php endif; ?>
                </div>
                
                <h1 class="display-4 fw-bold mb-3">
                    <?= esc($category['name']) ?>
                </h1>
                
                <?php if (!empty($category['description'])): ?>
                    <p class="lead mb-4">
                        <?= esc($category['description']) ?>
                    </p>
                <?php endif; ?>
                
                <div class="d-inline-block px-4 py-2 bg-white bg-opacity-75 rounded-pill shadow-sm">
                    <i class="bi bi-app-indicator"></i>
                    <?= number_format($pagination['total']) ?> 
                    <?= $pagination['total'] == 1 ? 'App' : 'Apps' ?>
                </div>
            </div>
        </div>
    </section>

    <!-- Apps Grid -->
    <section class="container mb-5">
        <?php if (empty($apps)): ?>
            <!-- No Apps Message -->
            <div class="no-apps">
                <i class="bi bi-inbox"></i>
                <h3>No apps in this category yet</h3>
                <p class="text-muted mb-4">
                    Check back soon for new apps in <?= esc($category['name']) ?>.
                </p>
                <a href="<?= base_url('categories') ?>" class="btn btn-primary" style="background: #2563EB; border-color: #2563EB; border-radius: 8px; font-weight: 600;">
                    <i class="bi bi-arrow-left"></i> Browse Other Categories
                </a>
            </div>
        <?php else: ?>
            <!-- Results Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="fw-semibold">
                    Showing <?= number_format(count($apps)) ?> of <?= number_format($pagination['total']) ?> apps
                </h4>
                <div class="text-muted">
                    <i class="bi bi-sort-down"></i> Sorted by Trust Score
                </div>
            </div>
            
            <!-- Apps Grid -->
            <div class="row g-4">
                <?php foreach ($apps as $app): ?>
                    <div class="col-md-6 col-lg-4 col-xl-3">
                        <div class="card app-card">
                            <div class="position-relative">
                                <?php
                                // Determine trust score color
                                $trustScore = (float)$app['trust_score'];
                                $badgeClass = 'trust-low';
                                if ($trustScore >= 80) {
                                    $badgeClass = 'trust-high';
                                } elseif ($trustScore >= 50) {
                                    $badgeClass = 'trust-medium';
                                }
                                ?>
                                <div class="trust-badge <?= $badgeClass ?>">
                                    <?= number_format($trustScore, 0) ?>
                                </div>
                                
                                <?php if (!empty($app['thumbnail'])): ?>
                                    <img src="<?= base_url('uploads/thumbnails/' . esc($app['thumbnail'])) ?>" 
                                         class="app-thumbnail" 
                                         alt="<?= esc($app['name']) ?>">
                                <?php else: ?>
                                    <div class="app-thumbnail d-flex align-items-center justify-content-center">
                                        <i class="bi bi-app text-white" style="font-size: 4rem;"></i>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="card-body">
                                <h5 class="card-title mb-2">
                                    <a href="<?= base_url('apps/' . esc($app['slug'])) ?>" 
                                       class="text-decoration-none text-dark">
                                        <?= esc($app['name']) ?>
                                    </a>
                                </h5>
                                
                                <span class="category-badge">
                                    <i class="bi bi-building"></i>
                                    <?= esc($app['developer_name']) ?>
                                </span>
                                
                                <p class="card-text text-muted small mt-2">
                                    <?= esc(substr($app['description'] ?? 'No description available', 0, 80)) ?>...
                                </p>
                                
                                <div class="d-flex justify-content-between align-items-center mt-3">
                                    <small class="text-muted">
                                        <i class="bi bi-eye"></i> <?= number_format($app['view_count']) ?> views
                                    </small>
                                    <a href="<?= base_url('apps/' . esc($app['slug'])) ?>" 
                                       class="btn btn-sm btn-primary"
                                       style="background: #2563EB; border-color: #2563EB; border-radius: 8px; font-weight: 600;">
                                        View Details
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <!-- Pagination -->
            <?php if ($pagination['total_pages'] > 1): ?>
                <nav aria-label="Category apps pagination" class="mt-5">
                    <ul class="pagination justify-content-center">
                        <!-- Previous Page -->
                        <?php if ($pagination['current_page'] > 1): ?>
                            <li class="page-item">
                                <a class="page-link" 
                                   href="<?= base_url('categories/' . esc($category['slug']) . '?page=' . ($pagination['current_page'] - 1)) ?>">
                                    <i class="bi bi-chevron-left"></i> Previous
                                </a>
                            </li>
                        <?php else: ?>
                            <li class="page-item disabled">
                                <span class="page-link">
                                    <i class="bi bi-chevron-left"></i> Previous
                                </span>
                            </li>
                        <?php endif; ?>
                        
                        <!-- Page Numbers -->
                        <?php
                        $start = max(1, $pagination['current_page'] - 2);
                        $end = min($pagination['total_pages'], $pagination['current_page'] + 2);
                        ?>
                        
                        <?php if ($start > 1): ?>
                            <li class="page-item">
                                <a class="page-link" 
                                   href="<?= base_url('categories/' . esc($category['slug']) . '?page=1') ?>">
                                    1
                                </a>
                            </li>
                            <?php if ($start > 2): ?>
                                <li class="page-item disabled">
                                    <span class="page-link">...</span>
                                </li>
                            <?php endif; ?>
                        <?php endif; ?>
                        
                        <?php for ($i = $start; $i <= $end; $i++): ?>
                            <li class="page-item <?= $i == $pagination['current_page'] ? 'active' : '' ?>">
                                <a class="page-link" 
                                   href="<?= base_url('categories/' . esc($category['slug']) . '?page=' . $i) ?>">
                                    <?= $i ?>
                                </a>
                            </li>
                        <?php endfor; ?>
                        
                        <?php if ($end < $pagination['total_pages']): ?>
                            <?php if ($end < $pagination['total_pages'] - 1): ?>
                                <li class="page-item disabled">
                                    <span class="page-link">...</span>
                                </li>
                            <?php endif; ?>
                            <li class="page-item">
                                <a class="page-link" 
                                   href="<?= base_url('categories/' . esc($category['slug']) . '?page=' . $pagination['total_pages']) ?>">
                                    <?= $pagination['total_pages'] ?>
                                </a>
                            </li>
                        <?php endif; ?>
                        
                        <!-- Next Page -->
                        <?php if ($pagination['current_page'] < $pagination['total_pages']): ?>
                            <li class="page-item">
                                <a class="page-link" 
                                   href="<?= base_url('categories/' . esc($category['slug']) . '?page=' . ($pagination['current_page'] + 1)) ?>">
                                    Next <i class="bi bi-chevron-right"></i>
                                </a>
                            </li>
                        <?php else: ?>
                            <li class="page-item disabled">
                                <span class="page-link">
                                    Next <i class="bi bi-chevron-right"></i>
                                </span>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>
            <?php endif; ?>
        <?php endif; ?>
    </section>

    <!-- Scam Alerts Section -->
    <div class="container">
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
                    <p style="color: #9CA3AF; font-size: 0.85rem; margin-bottom: 1rem;">Get latest scam alerts and trusted app reviews</p>
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

