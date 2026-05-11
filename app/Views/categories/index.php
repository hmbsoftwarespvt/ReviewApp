<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title) ?> - AppTrust Platform</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="<?= base_url('css/apptrust-theme.css') ?>">
    <style>
        .category-card {
            background: var(--bg-primary);
            border-radius: var(--border-radius-lg);
            padding: var(--spacing-xl);
            text-align: center;
            transition: all 0.3s;
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--border-color);
            height: 100%;
            cursor: pointer;
        }
        
        .category-card:hover {
            transform: translateY(-8px);
            box-shadow: var(--shadow-xl);
        }
        
        .category-icon {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary-blue) 0%, var(--primary-purple) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto var(--spacing-lg);
            font-size: 2.5rem;
            color: white;
        }
        
        .category-name {
            font-size: var(--font-size-xl);
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: var(--spacing-sm);
        }
        
        .category-description {
            color: var(--text-secondary);
            font-size: var(--font-size-sm);
            margin-bottom: var(--spacing-md);
            min-height: 60px;
        }
        
        .app-count-badge {
            display: inline-block;
            padding: 8px 20px;
            background: var(--gray-100);
            border-radius: var(--border-radius);
            font-weight: 600;
            color: var(--text-secondary);
        }
        
        .category-link {
            text-decoration: none;
            color: inherit;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar-apptrust">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center w-100">
                <a class="navbar-brand" href="<?= base_url('/') ?>">
                    <div class="brand-icon">
                        <i class="bi bi-shield-check"></i>
                    </div>
                    <span>AppTrust</span>
                </a>
                
                <div class="d-none d-lg-flex align-items-center gap-2">
                    <a class="nav-link" href="<?= base_url('/') ?>">Home</a>
                    <a class="nav-link" href="<?= base_url('apps') ?>">Apps</a>
                    <a class="nav-link active" href="<?= base_url('categories') ?>">Categories</a>
                    <a class="nav-link" href="<?= base_url('scam-alerts') ?>">Scam Alerts</a>
                    <a class="nav-link" href="<?= base_url('blog') ?>">Blog</a>
                </div>
                
                <div class="d-flex align-items-center gap-3">
                    <?php if (session()->get('isLoggedIn')): ?>
                        <a href="<?= base_url('auth/logout') ?>" class="btn-outline-apptrust">Logout</a>
                    <?php else: ?>
                        <a href="<?= base_url('auth/login') ?>" class="btn-outline-apptrust">Login</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <!-- Page Header -->
    <section class="hero-section">
        <div class="container text-center">
            <h1 class="hero-title">
                <i class="bi bi-grid-3x3-gap"></i> Browse <span class="highlight">Categories</span>
            </h1>
            <p class="hero-subtitle">
                Discover apps organized by category. Find the perfect app for your needs.
            </p>
        </div>
    </section>

    <!-- Categories Grid -->
    <section class="container mb-5">
        <?php if (empty($categories)): ?>
            <div class="alert alert-info text-center">
                <i class="bi bi-info-circle"></i> No categories available at the moment.
            </div>
        <?php else: ?>
            <div class="row g-4">
                <?php foreach ($categories as $category): ?>
                    <div class="col-md-6 col-lg-4 col-xl-3">
                        <a href="<?= base_url('categories/' . esc($category['slug'])) ?>" class="category-link">
                            <div class="category-card">
                                <div class="category-icon">
                                    <?php if (!empty($category['icon'])): ?>
                                        <i class="bi bi-<?= esc($category['icon']) ?>"></i>
                                    <?php else: ?>
                                        <i class="bi bi-app"></i>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="category-name">
                                    <?= esc($category['name']) ?>
                                </div>
                                
                                <div class="category-description">
                                    <?= esc($category['description'] ?? 'Explore apps in this category') ?>
                                </div>
                                
                                <div class="app-count-badge">
                                    <i class="bi bi-app-indicator"></i>
                                    <?= number_format($category['app_count']) ?> 
                                    <?= $category['app_count'] == 1 ? 'App' : 'Apps' ?>
                                </div>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4">
                    <h5 class="fw-bold mb-3">
                        <i class="bi bi-shield-check"></i> AppTrust
                    </h5>
                    <p class="text-muted">
                        Your trusted source for app reviews, trust scores, and scam alerts. Make informed decisions about app safety.
                    </p>
                </div>
                <div class="col-md-2 mb-4">
                    <h6 class="fw-bold mb-3">Platform</h6>
                    <ul class="list-unstyled">
                        <li><a href="<?= base_url('apps') ?>">Browse Apps</a></li>
                        <li><a href="<?= base_url('categories') ?>">Categories</a></li>
                        <li><a href="<?= base_url('scam-alerts') ?>">Scam Alerts</a></li>
                        <li><a href="<?= base_url('compare') ?>">Compare Apps</a></li>
                    </ul>
                </div>
                <div class="col-md-2 mb-4">
                    <h6 class="fw-bold mb-3">Resources</h6>
                    <ul class="list-unstyled">
                        <li><a href="<?= base_url('blog') ?>">Blog</a></li>
                        <li><a href="#">About Us</a></li>
                        <li><a href="#">Contact</a></li>
                        <li><a href="#">FAQ</a></li>
                    </ul>
                </div>
                <div class="col-md-2 mb-4">
                    <h6 class="fw-bold mb-3">Legal</h6>
                    <ul class="list-unstyled">
                        <li><a href="#">Privacy Policy</a></li>
                        <li><a href="#">Terms of Service</a></li>
                        <li><a href="#">Cookie Policy</a></li>
                    </ul>
                </div>
                <div class="col-md-2 mb-4">
                    <h6 class="fw-bold mb-3">Connect</h6>
                    <div class="d-flex gap-3">
                        <a href="#"><i class="bi bi-twitter" style="font-size: 1.5rem;"></i></a>
                        <a href="#"><i class="bi bi-facebook" style="font-size: 1.5rem;"></i></a>
                        <a href="#"><i class="bi bi-linkedin" style="font-size: 1.5rem;"></i></a>
                    </div>
                </div>
            </div>
            <hr class="my-4" style="border-color: rgba(255,255,255,0.1);">
            <div class="text-center text-muted">
                <p>&copy; <?= date('Y') ?> AppTrust Platform. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

