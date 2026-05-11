<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title) ?> - AppTrust Platform</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="<?= base_url('css/apptrust-theme.css') ?>">
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
                    <a class="nav-link" href="<?= base_url('categories') ?>">Categories</a>
                    <a class="nav-link active" href="<?= base_url('scam-alerts') ?>">Scam Alerts</a>
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
    <section class="hero-section" style="padding: var(--spacing-2xl) 0;">
        <div class="container">
            <div class="row">
                <div class="col-lg-8">
                    <h1 class="hero-title" style="font-size: var(--font-size-4xl); margin-bottom: var(--spacing-md);">
                        <i class="bi bi-exclamation-triangle-fill" style="color: var(--danger-red);"></i> Scam Alerts
                    </h1>
                    <p class="hero-subtitle" style="font-size: var(--font-size-lg);">
                        Stay informed about dangerous apps and potential scams reported by our community.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <div class="container">
        <div class="row">
            <!-- Filters Sidebar -->
            <div class="col-lg-3 mb-4">
                <div class="filter-sidebar">
                    <h5 style="font-weight: 700; margin-bottom: var(--spacing-lg);">
                        <i class="bi bi-funnel"></i> Filters
                    </h5>
                    
                    <form method="get" action="<?= base_url('scam-alerts') ?>" id="filterForm">
                        <div class="filter-group">
                            <label for="category">
                                <i class="bi bi-tag"></i> Category
                            </label>
                            <select name="category" id="category" onchange="document.getElementById('filterForm').submit()">
                                <option value="">All Categories</option>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?= esc($category['id']) ?>" <?= ($filters['category'] == $category['id']) ? 'selected' : '' ?>>
                                        <?= esc($category['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="filter-group">
                            <label for="risk_level">
                                <i class="bi bi-shield-exclamation"></i> Risk Level
                            </label>
                            <select name="risk_level" id="risk_level" onchange="document.getElementById('filterForm').submit()">
                                <option value="">All Risk Levels</option>
                                <option value="high" <?= ($filters['risk_level'] === 'high') ? 'selected' : '' ?>>High Risk</option>
                                <option value="medium" <?= ($filters['risk_level'] === 'medium') ? 'selected' : '' ?>>Medium Risk</option>
                                <option value="low" <?= ($filters['risk_level'] === 'low') ? 'selected' : '' ?>>Low Risk</option>
                            </select>
                        </div>
                        
                        <button type="submit" class="btn-primary-apptrust w-100 mb-2">
                            <i class="bi bi-funnel"></i> Apply Filters
                        </button>
                        
                        <?php if (!empty($filters['category']) || !empty($filters['risk_level'])): ?>
                            <a href="<?= base_url('scam-alerts') ?>" class="btn-outline-apptrust w-100">
                                <i class="bi bi-x-circle"></i> Clear Filters
                            </a>
                        <?php endif; ?>
                    </form>
                    
                    <!-- Stats -->
                    <div style="margin-top: var(--spacing-xl); padding-top: var(--spacing-lg); border-top: 1px solid var(--border-color);">
                        <div class="stats-card" style="padding: var(--spacing-md);">
                            <div class="stats-icon danger" style="width: 48px; height: 48px; font-size: 20px;">
                                <i class="bi bi-exclamation-triangle-fill"></i>
                            </div>
                            <div class="stats-content" style="margin-top: var(--spacing-sm);">
                                <h3 style="font-size: var(--font-size-2xl);"><?= number_format($pagination['total']) ?></h3>
                                <p style="font-size: var(--font-size-sm);">Total Reports</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Scam Reports List -->
            <div class="col-lg-9">
                <!-- Results Summary -->
                <div class="mb-4">
                    <p style="color: var(--text-secondary); font-size: var(--font-size-sm);">
                        <strong><?= number_format($pagination['total']) ?></strong> scam report<?= $pagination['total'] !== 1 ? 's' : '' ?> found
                        <?php if (!empty($filters['category']) || !empty($filters['risk_level'])): ?>
                            with current filters
                        <?php endif; ?>
                    </p>
                </div>
                <?php if (empty($scam_reports)): ?>
                    <div class="card-apptrust text-center" style="padding: var(--spacing-2xl);">
                        <i class="bi bi-inbox" style="font-size: 4rem; color: var(--text-tertiary); opacity: 0.5;"></i>
                        <h3 style="margin-top: var(--spacing-lg); color: var(--text-secondary);">No Scam Reports Found</h3>
                        <p style="color: var(--text-tertiary);">There are no scam reports matching your current filters.</p>
                        <?php if (!empty($filters['category']) || !empty($filters['risk_level'])): ?>
                            <a href="<?= base_url('scam-alerts') ?>" class="btn-primary-apptrust" style="margin-top: var(--spacing-md);">
                                View All Reports
                            </a>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <?php foreach ($scam_reports as $report): ?>
                        <?php
                        // Determine risk badge class
                        $riskClass = match($report['risk_level']) {
                            'high' => 'high',
                            'medium' => 'medium',
                            'low' => 'low',
                            default => 'low',
                        };
                        ?>
                        
                        <div class="card-apptrust mb-4" style="border-left: 4px solid <?= $riskClass === 'high' ? 'var(--danger-red)' : ($riskClass === 'medium' ? 'var(--warning-orange)' : 'var(--success-green)') ?>;">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div class="flex-grow-1">
                                    <a href="<?= base_url('apps/' . esc($report['app_slug'])) ?>" style="color: var(--primary-blue); text-decoration: none; font-weight: 700; font-size: var(--font-size-xl);">
                                        <?= esc($report['app_name']) ?>
                                    </a>
                                </div>
                                <span class="risk-badge <?= $riskClass ?>">
                                    <i class="bi bi-exclamation-triangle-fill"></i>
                                    <?= ucfirst(esc($report['risk_level'])) ?> Risk
                                </span>
                            </div>
                            
                            <h5 style="font-weight: 600; color: var(--text-primary); margin-bottom: var(--spacing-sm);">
                                <?= esc($report['title']) ?>
                            </h5>
                            
                            <div style="color: var(--text-secondary); font-size: var(--font-size-sm); margin-bottom: var(--spacing-md);">
                                <i class="bi bi-person"></i> Reported by <strong><?= esc($report['username']) ?></strong>
                                <span class="mx-2">•</span>
                                <i class="bi bi-calendar"></i> <?= date('F j, Y', strtotime($report['created_at'])) ?>
                            </div>
                            
                            <div style="color: var(--text-secondary); line-height: 1.6; margin-bottom: var(--spacing-md);">
                                <?php
                                // Show excerpt (first 200 characters)
                                $description = esc($report['description']);
                                if (strlen($description) > 200) {
                                    echo substr($description, 0, 200) . '...';
                                } else {
                                    echo $description;
                                }
                                ?>
                            </div>
                            
                            <div>
                                <a href="<?= base_url('apps/' . esc($report['app_slug'])) ?>" class="btn-outline-apptrust">
                                    <i class="bi bi-arrow-right"></i> View App Details
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    <!-- Pagination -->
                    <?php if ($pagination['total_pages'] > 1): ?>
                        <nav aria-label="Scam reports pagination" style="margin-top: var(--spacing-xl);">
                            <ul class="pagination justify-content-center">
                                <!-- Previous Page -->
                                <?php if ($pagination['current_page'] > 1): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="<?= base_url('scam-alerts?' . http_build_query(array_merge($filters, ['page' => $pagination['current_page'] - 1]))) ?>" style="color: var(--primary-blue);">
                                            <i class="bi bi-chevron-left"></i> Previous
                                        </a>
                                    </li>
                                <?php else: ?>
                                    <li class="page-item disabled">
                                        <span class="page-link"><i class="bi bi-chevron-left"></i> Previous</span>
                                    </li>
                                <?php endif; ?>
                                
                                <!-- Page Numbers -->
                                <?php
                                $startPage = max(1, $pagination['current_page'] - 2);
                                $endPage = min($pagination['total_pages'], $pagination['current_page'] + 2);
                                
                                if ($startPage > 1): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="<?= base_url('scam-alerts?' . http_build_query(array_merge($filters, ['page' => 1]))) ?>" style="color: var(--primary-blue);">1</a>
                                    </li>
                                    <?php if ($startPage > 2): ?>
                                        <li class="page-item disabled"><span class="page-link">...</span></li>
                                    <?php endif; ?>
                                <?php endif; ?>
                                
                                <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                                    <li class="page-item <?= ($i === $pagination['current_page']) ? 'active' : '' ?>">
                                        <a class="page-link" href="<?= base_url('scam-alerts?' . http_build_query(array_merge($filters, ['page' => $i]))) ?>" style="<?= ($i === $pagination['current_page']) ? 'background-color: var(--primary-blue); border-color: var(--primary-blue); color: white;' : 'color: var(--primary-blue);' ?>">
                                            <?= $i ?>
                                        </a>
                                    </li>
                                <?php endfor; ?>
                                
                                <?php if ($endPage < $pagination['total_pages']): ?>
                                    <?php if ($endPage < $pagination['total_pages'] - 1): ?>
                                        <li class="page-item disabled"><span class="page-link">...</span></li>
                                    <?php endif; ?>
                                    <li class="page-item">
                                        <a class="page-link" href="<?= base_url('scam-alerts?' . http_build_query(array_merge($filters, ['page' => $pagination['total_pages']]))) ?>" style="color: var(--primary-blue);">
                                            <?= $pagination['total_pages'] ?>
                                        </a>
                                    </li>
                                <?php endif; ?>
                                
                                <!-- Next Page -->
                                <?php if ($pagination['current_page'] < $pagination['total_pages']): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="<?= base_url('scam-alerts?' . http_build_query(array_merge($filters, ['page' => $pagination['current_page'] + 1]))) ?>" style="color: var(--primary-blue);">
                                            Next <i class="bi bi-chevron-right"></i>
                                        </a>
                                    </li>
                                <?php else: ?>
                                    <li class="page-item disabled">
                                        <span class="page-link">Next <i class="bi bi-chevron-right"></i></span>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </nav>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer-apptrust">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4">
                    <h5>
                        <i class="bi bi-shield-check"></i> AppTrust
                    </h5>
                    <p style="color: var(--gray-400); margin-top: var(--spacing-md);">
                        Your trusted source for app reviews, trust scores, and scam alerts. Make informed decisions about app safety.
                    </p>
                </div>
                <div class="col-md-2 mb-4">
                    <h5>Platform</h5>
                    <ul class="list-unstyled" style="margin-top: var(--spacing-md);">
                        <li class="mb-2"><a href="<?= base_url('apps') ?>">Browse Apps</a></li>
                        <li class="mb-2"><a href="<?= base_url('categories') ?>">Categories</a></li>
                        <li class="mb-2"><a href="<?= base_url('scam-alerts') ?>">Scam Alerts</a></li>
                        <li class="mb-2"><a href="<?= base_url('compare') ?>">Compare Apps</a></li>
                    </ul>
                </div>
                <div class="col-md-2 mb-4">
                    <h5>Resources</h5>
                    <ul class="list-unstyled" style="margin-top: var(--spacing-md);">
                        <li class="mb-2"><a href="<?= base_url('blog') ?>">Blog</a></li>
                        <li class="mb-2"><a href="#">About Us</a></li>
                        <li class="mb-2"><a href="#">Contact</a></li>
                        <li class="mb-2"><a href="#">FAQ</a></li>
                    </ul>
                </div>
                <div class="col-md-2 mb-4">
                    <h5>Legal</h5>
                    <ul class="list-unstyled" style="margin-top: var(--spacing-md);">
                        <li class="mb-2"><a href="#">Privacy Policy</a></li>
                        <li class="mb-2"><a href="#">Terms of Service</a></li>
                        <li class="mb-2"><a href="#">Cookie Policy</a></li>
                    </ul>
                </div>
                <div class="col-md-2 mb-4">
                    <h5>Connect</h5>
                    <div class="d-flex gap-3" style="margin-top: var(--spacing-md);">
                        <a href="#"><i class="bi bi-twitter" style="font-size: 1.5rem;"></i></a>
                        <a href="#"><i class="bi bi-facebook" style="font-size: 1.5rem;"></i></a>
                        <a href="#"><i class="bi bi-linkedin" style="font-size: 1.5rem;"></i></a>
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; <?= date('Y') ?> AppTrust Platform. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
