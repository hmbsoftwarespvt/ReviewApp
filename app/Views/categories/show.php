<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title) ?> - AppTrust Platform</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        :root {
            --trust-high: #198754;
            --trust-medium: #ffc107;
            --trust-low: #dc3545;
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .navbar {
            background: var(--primary-gradient);
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .category-header {
            background: var(--primary-gradient);
            color: white;
            padding: 60px 0 40px;
            margin-bottom: 40px;
        }
        
        .category-icon-large {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background: rgba(255,255,255,0.2);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            margin-bottom: 20px;
        }
        
        .breadcrumb {
            background: transparent;
            padding: 0;
            margin-bottom: 20px;
        }
        
        .breadcrumb-item a {
            color: rgba(255,255,255,0.8);
            text-decoration: none;
        }
        
        .breadcrumb-item a:hover {
            color: white;
        }
        
        .breadcrumb-item.active {
            color: white;
        }
        
        .breadcrumb-item + .breadcrumb-item::before {
            color: rgba(255,255,255,0.6);
        }
        
        .app-card {
            border: none;
            border-radius: 15px;
            overflow: hidden;
            transition: all 0.3s;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            height: 100%;
            background: white;
        }
        
        .app-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(0,0,0,0.15);
        }
        
        .app-thumbnail {
            width: 100%;
            height: 180px;
            object-fit: cover;
            background: var(--primary-gradient);
        }
        
        .trust-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            padding: 8px 15px;
            border-radius: 20px;
            font-weight: bold;
            font-size: 14px;
            color: white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }
        
        .trust-high {
            background-color: var(--trust-high);
        }
        
        .trust-medium {
            background-color: var(--trust-medium);
        }
        
        .trust-low {
            background-color: var(--trust-low);
        }
        
        .category-badge {
            display: inline-block;
            padding: 4px 12px;
            background: #e9ecef;
            border-radius: 12px;
            font-size: 0.85rem;
            color: #495057;
            margin-top: 8px;
        }
        
        .no-apps {
            text-align: center;
            padding: 60px 20px;
        }
        
        .no-apps i {
            font-size: 4rem;
            color: #6c757d;
            margin-bottom: 20px;
        }
        
        .pagination {
            margin-top: 40px;
        }
        
        .page-link {
            color: #667eea;
            border-color: #dee2e6;
        }
        
        .page-link:hover {
            color: #764ba2;
            background-color: #f8f9fa;
            border-color: #dee2e6;
        }
        
        .page-item.active .page-link {
            background-color: #667eea;
            border-color: #667eea;
        }
        
        footer {
            background: #2d3748;
            color: white;
            padding: 40px 0 20px;
            margin-top: 60px;
        }
        
        footer a {
            color: #a0aec0;
            text-decoration: none;
        }
        
        footer a:hover {
            color: white;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand fw-bold" href="<?= base_url('/') ?>">
                <i class="bi bi-shield-check"></i> AppTrust
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('/') ?>">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('apps') ?>">Apps</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="<?= base_url('categories') ?>">Categories</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('scam-alerts') ?>">Scam Alerts</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('blog') ?>">Blog</a>
                    </li>
                    <?php if (session()->get('isLoggedIn')): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= base_url('auth/logout') ?>">Logout</a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= base_url('auth/login') ?>">Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= base_url('auth/register') ?>">Register</a>
                        </li>
                    <?php endif; ?>
                </ul>
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
                
                <div class="d-inline-block px-4 py-2 bg-white bg-opacity-25 rounded-pill">
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
                <a href="<?= base_url('categories') ?>" class="btn btn-primary">
                    <i class="bi bi-arrow-left"></i> Browse Other Categories
                </a>
            </div>
        <?php else: ?>
            <!-- Results Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4>
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
                                        <i class="bi bi-eye"></i> <?= number_format($app['view_count']) ?>
                                    </small>
                                    <a href="<?= base_url('apps/' . esc($app['slug'])) ?>" 
                                       class="btn btn-sm btn-outline-primary">
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

