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
        }
        
        .navbar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .search-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px 0;
            margin-bottom: 40px;
        }
        
        .search-box {
            background: white;
            border-radius: 50px;
            padding: 5px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        
        .search-box input {
            border: none;
            padding: 12px 20px;
        }
        
        .search-box input:focus {
            outline: none;
            box-shadow: none;
        }
        
        .search-box button {
            border-radius: 50px;
            padding: 10px 30px;
        }
        
        .filter-card {
            background: white;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        
        .result-card {
            background: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            transition: all 0.3s;
        }
        
        .result-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        
        .app-thumbnail-small {
            width: 80px;
            height: 80px;
            border-radius: 15px;
            object-fit: cover;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .trust-badge {
            display: inline-block;
            padding: 8px 15px;
            border-radius: 20px;
            font-weight: bold;
            font-size: 1.1rem;
            color: white;
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
            margin-right: 5px;
        }
        
        .search-highlight {
            background-color: #fff3cd;
            padding: 2px 4px;
            border-radius: 3px;
            font-weight: 600;
        }
        
        .filter-badge {
            display: inline-block;
            padding: 5px 12px;
            background: #667eea;
            color: white;
            border-radius: 15px;
            font-size: 0.9rem;
            margin-right: 5px;
            margin-bottom: 5px;
        }
        
        .filter-badge .remove {
            margin-left: 8px;
            cursor: pointer;
            font-weight: bold;
        }
        
        .no-results {
            text-align: center;
            padding: 60px 20px;
        }
        
        .no-results i {
            font-size: 4rem;
            color: #6c757d;
            margin-bottom: 20px;
        }
        
        .suggestion-pill {
            display: inline-block;
            padding: 8px 20px;
            margin: 5px;
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 25px;
            text-decoration: none;
            color: #495057;
            transition: all 0.3s;
        }
        
        .suggestion-pill:hover {
            background: #667eea;
            color: white;
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
                        <a class="nav-link" href="<?= base_url('categories') ?>">Categories</a>
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

    <!-- Search Header -->
    <section class="search-header">
        <div class="container">
            <h1 class="mb-4">
                <?php if (!empty($query)): ?>
                    Search Results for "<?= esc($query) ?>"
                <?php else: ?>
                    Search Apps
                <?php endif; ?>
            </h1>
            
            <!-- Search Form -->
            <form action="<?= base_url('search') ?>" method="get" class="search-box">
                <div class="input-group">
                    <input type="text" name="q" class="form-control form-control-lg" 
                           placeholder="Search for apps..." 
                           value="<?= esc($query) ?>" 
                           required>
                    <button class="btn btn-primary" type="submit">
                        <i class="bi bi-search"></i> Search
                    </button>
                </div>
            </form>
        </div>
    </section>

    <div class="container mb-5">
        <div class="row">
            <!-- Filters Sidebar -->
            <div class="col-lg-3">
                <div class="filter-card">
                    <h5 class="mb-3">
                        <i class="bi bi-funnel"></i> Filters
                        <?php if ($active_filters > 0): ?>
                            <span class="badge bg-primary"><?= $active_filters ?></span>
                        <?php endif; ?>
                    </h5>
                    
                    <form action="<?= base_url('search') ?>" method="get" id="filterForm">
                        <input type="hidden" name="q" value="<?= esc($query) ?>">
                        
                        <!-- Category Filter -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">Category</label>
                            <select name="category" class="form-select" onchange="document.getElementById('filterForm').submit()">
                                <option value="">All Categories</option>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?= $category['id'] ?>" 
                                            <?= (isset($filters['category_id']) && $filters['category_id'] == $category['id']) ? 'selected' : '' ?>>
                                        <?= esc($category['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <!-- Platform Filter -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">Platform</label>
                            <select name="platform" class="form-select" onchange="document.getElementById('filterForm').submit()">
                                <option value="">All Platforms</option>
                                <option value="android" <?= (isset($filters['platform_type']) && $filters['platform_type'] == 'android') ? 'selected' : '' ?>>Android</option>
                                <option value="ios" <?= (isset($filters['platform_type']) && $filters['platform_type'] == 'ios') ? 'selected' : '' ?>>iOS</option>
                                <option value="web" <?= (isset($filters['platform_type']) && $filters['platform_type'] == 'web') ? 'selected' : '' ?>>Web</option>
                                <option value="desktop" <?= (isset($filters['platform_type']) && $filters['platform_type'] == 'desktop') ? 'selected' : '' ?>>Desktop</option>
                            </select>
                        </div>
                        
                        <!-- Price Filter -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">Price</label>
                            <select name="price_type" class="form-select" onchange="document.getElementById('filterForm').submit()">
                                <option value="">All Apps</option>
                                <option value="free" <?= (isset($filters['price_type']) && $filters['price_type'] == 'free') ? 'selected' : '' ?>>Free</option>
                                <option value="paid" <?= (isset($filters['price_type']) && $filters['price_type'] == 'paid') ? 'selected' : '' ?>>Paid</option>
                            </select>
                        </div>
                        
                        <!-- Price Range (for paid apps) -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">Price Range</label>
                            <div class="row g-2">
                                <div class="col-6">
                                    <input type="number" name="price_min" class="form-control" 
                                           placeholder="Min" step="0.01" min="0"
                                           value="<?= isset($filters['price_min']) ? esc($filters['price_min']) : '' ?>">
                                </div>
                                <div class="col-6">
                                    <input type="number" name="price_max" class="form-control" 
                                           placeholder="Max" step="0.01" min="0"
                                           value="<?= isset($filters['price_max']) ? esc($filters['price_max']) : '' ?>">
                                </div>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100 mb-2">
                            <i class="bi bi-check-circle"></i> Apply Filters
                        </button>
                        
                        <?php if ($active_filters > 0): ?>
                            <a href="<?= base_url('search?q=' . urlencode($query)) ?>" class="btn btn-outline-secondary w-100">
                                <i class="bi bi-x-circle"></i> Clear Filters
                            </a>
                        <?php endif; ?>
                    </form>
                </div>
            </div>

            <!-- Search Results -->
            <div class="col-lg-9">
                <!-- Results Header -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h4>
                            <?php if (!empty($results)): ?>
                                <?= number_format($pagination['total']) ?> Results Found
                            <?php else: ?>
                                No Results Found
                            <?php endif; ?>
                        </h4>
                        
                        <!-- Active Filters Display -->
                        <?php if ($active_filters > 0): ?>
                            <div class="mt-2">
                                <?php if (isset($filters['category_id'])): ?>
                                    <?php
                                    $selectedCategory = null;
                                    foreach ($categories as $cat) {
                                        if ($cat['id'] == $filters['category_id']) {
                                            $selectedCategory = $cat;
                                            break;
                                        }
                                    }
                                    ?>
                                    <?php if ($selectedCategory): ?>
                                        <span class="filter-badge">
                                            Category: <?= esc($selectedCategory['name']) ?>
                                            <a href="<?= base_url('search?q=' . urlencode($query)) ?>" class="remove text-white">&times;</a>
                                        </span>
                                    <?php endif; ?>
                                <?php endif; ?>
                                
                                <?php if (isset($filters['platform_type'])): ?>
                                    <span class="filter-badge">
                                        Platform: <?= ucfirst(esc($filters['platform_type'])) ?>
                                        <a href="<?= base_url('search?q=' . urlencode($query)) ?>" class="remove text-white">&times;</a>
                                    </span>
                                <?php endif; ?>
                                
                                <?php if (isset($filters['price_type'])): ?>
                                    <span class="filter-badge">
                                        <?= ucfirst(esc($filters['price_type'])) ?> Apps
                                        <a href="<?= base_url('search?q=' . urlencode($query)) ?>" class="remove text-white">&times;</a>
                                    </span>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Sort Options -->
                    <?php if (!empty($results)): ?>
                        <div>
                            <form action="<?= base_url('search') ?>" method="get" class="d-flex gap-2">
                                <input type="hidden" name="q" value="<?= esc($query) ?>">
                                <?php foreach ($filters as $key => $value): ?>
                                    <input type="hidden" name="<?= esc($key) ?>" value="<?= esc($value) ?>">
                                <?php endforeach; ?>
                                
                                <select name="sort" class="form-select form-select-sm" onchange="this.form.submit()">
                                    <option value="relevance" <?= $sort_by == 'relevance' ? 'selected' : '' ?>>Relevance</option>
                                    <option value="trust_score" <?= $sort_by == 'trust_score' ? 'selected' : '' ?>>Trust Score</option>
                                    <option value="date" <?= $sort_by == 'date' ? 'selected' : '' ?>>Date Added</option>
                                    <option value="name" <?= $sort_by == 'name' ? 'selected' : '' ?>>Name</option>
                                </select>
                                
                                <select name="order" class="form-select form-select-sm" onchange="this.form.submit()">
                                    <option value="DESC" <?= $sort_order == 'DESC' ? 'selected' : '' ?>>Descending</option>
                                    <option value="ASC" <?= $sort_order == 'ASC' ? 'selected' : '' ?>>Ascending</option>
                                </select>
                            </form>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Results List -->
                <?php if (empty($results)): ?>
                    <!-- No Results Message -->
                    <div class="no-results">
                        <i class="bi bi-search"></i>
                        <h3>No apps found matching your search</h3>
                        <p class="text-muted mb-4">
                            <?php if (!empty($query)): ?>
                                We couldn't find any apps matching "<?= esc($query) ?>".
                            <?php else: ?>
                                Try adjusting your filters or search for something else.
                            <?php endif; ?>
                        </p>
                        
                        <?php if (!empty($suggestions)): ?>
                            <div class="mt-4">
                                <h5>Try searching for:</h5>
                                <div class="mt-3">
                                    <?php foreach ($suggestions as $suggestion): ?>
                                        <a href="<?= base_url('search?q=' . urlencode($suggestion)) ?>" class="suggestion-pill">
                                            <?= esc($suggestion) ?>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <div class="mt-4">
                            <a href="<?= base_url('/') ?>" class="btn btn-primary">
                                <i class="bi bi-house"></i> Back to Home
                            </a>
                        </div>
                    </div>
                <?php else: ?>
                    <!-- Results -->
                    <?php foreach ($results as $app): ?>
                        <div class="result-card">
                            <div class="row">
                                <div class="col-auto">
                                    <?php if (!empty($app['thumbnail'])): ?>
                                        <img src="<?= base_url('uploads/thumbnails/' . esc($app['thumbnail'])) ?>" 
                                             class="app-thumbnail-small" 
                                             alt="<?= esc($app['name']) ?>">
                                    <?php else: ?>
                                        <div class="app-thumbnail-small d-flex align-items-center justify-content-center">
                                            <i class="bi bi-app text-white" style="font-size: 2rem;"></i>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="col">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div>
                                            <h5 class="mb-1">
                                                <a href="<?= base_url('apps/' . esc($app['slug'])) ?>" class="text-decoration-none text-dark">
                                                    <?php if (!empty($query) && isset($app['name_highlighted'])): ?>
                                                        <?= $app['name_highlighted'] ?>
                                                    <?php else: ?>
                                                        <?= esc($app['name']) ?>
                                                    <?php endif; ?>
                                                </a>
                                            </h5>
                                            <p class="text-muted mb-2">
                                                <i class="bi bi-building"></i>
                                                <?php if (!empty($query) && isset($app['developer_name_highlighted'])): ?>
                                                    <?= $app['developer_name_highlighted'] ?>
                                                <?php else: ?>
                                                    <?= esc($app['developer_name']) ?>
                                                <?php endif; ?>
                                            </p>
                                        </div>
                                        
                                        <?php
                                        $trustScore = (float)$app['trust_score'];
                                        $badgeClass = 'trust-low';
                                        if ($trustScore >= 80) {
                                            $badgeClass = 'trust-high';
                                        } elseif ($trustScore >= 50) {
                                            $badgeClass = 'trust-medium';
                                        }
                                        ?>
                                        <span class="trust-badge <?= $badgeClass ?>">
                                            <?= number_format($trustScore, 0) ?>
                                        </span>
                                    </div>
                                    
                                    <p class="mb-2">
                                        <?php if (!empty($query) && isset($app['description_highlighted'])): ?>
                                            <?= substr(strip_tags($app['description_highlighted']), 0, 200) ?>...
                                        <?php else: ?>
                                            <?= esc(substr($app['description'] ?? 'No description available', 0, 200)) ?>...
                                        <?php endif; ?>
                                    </p>
                                    
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <span class="category-badge">
                                                <i class="bi bi-phone"></i> <?= ucfirst(esc($app['platform_type'])) ?>
                                            </span>
                                            <span class="category-badge">
                                                <?= $app['price'] > 0 ? '$' . number_format($app['price'], 2) : 'Free' ?>
                                            </span>
                                            <span class="text-muted small">
                                                <i class="bi bi-eye"></i> <?= number_format($app['view_count']) ?> views
                                            </span>
                                        </div>
                                        
                                        <a href="<?= base_url('apps/' . esc($app['slug'])) ?>" class="btn btn-outline-primary btn-sm">
                                            View Details <i class="bi bi-arrow-right"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    
                    <!-- Pagination -->
                    <?php if ($pagination['total_pages'] > 1): ?>
                        <nav aria-label="Search results pagination">
                            <ul class="pagination justify-content-center">
                                <!-- Previous Page -->
                                <?php if ($pagination['current_page'] > 1): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="<?= base_url('search?' . http_build_query(array_merge(['q' => $query, 'page' => $pagination['current_page'] - 1], $filters, ['sort' => $sort_by, 'order' => $sort_order]))) ?>">
                                            <i class="bi bi-chevron-left"></i> Previous
                                        </a>
                                    </li>
                                <?php endif; ?>
                                
                                <!-- Page Numbers -->
                                <?php
                                $start = max(1, $pagination['current_page'] - 2);
                                $end = min($pagination['total_pages'], $pagination['current_page'] + 2);
                                ?>
                                
                                <?php if ($start > 1): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="<?= base_url('search?' . http_build_query(array_merge(['q' => $query, 'page' => 1], $filters, ['sort' => $sort_by, 'order' => $sort_order]))) ?>">1</a>
                                    </li>
                                    <?php if ($start > 2): ?>
                                        <li class="page-item disabled"><span class="page-link">...</span></li>
                                    <?php endif; ?>
                                <?php endif; ?>
                                
                                <?php for ($i = $start; $i <= $end; $i++): ?>
                                    <li class="page-item <?= $i == $pagination['current_page'] ? 'active' : '' ?>">
                                        <a class="page-link" href="<?= base_url('search?' . http_build_query(array_merge(['q' => $query, 'page' => $i], $filters, ['sort' => $sort_by, 'order' => $sort_order]))) ?>">
                                            <?= $i ?>
                                        </a>
                                    </li>
                                <?php endfor; ?>
                                
                                <?php if ($end < $pagination['total_pages']): ?>
                                    <?php if ($end < $pagination['total_pages'] - 1): ?>
                                        <li class="page-item disabled"><span class="page-link">...</span></li>
                                    <?php endif; ?>
                                    <li class="page-item">
                                        <a class="page-link" href="<?= base_url('search?' . http_build_query(array_merge(['q' => $query, 'page' => $pagination['total_pages']], $filters, ['sort' => $sort_by, 'order' => $sort_order]))) ?>">
                                            <?= $pagination['total_pages'] ?>
                                        </a>
                                    </li>
                                <?php endif; ?>
                                
                                <!-- Next Page -->
                                <?php if ($pagination['current_page'] < $pagination['total_pages']): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="<?= base_url('search?' . http_build_query(array_merge(['q' => $query, 'page' => $pagination['current_page'] + 1], $filters, ['sort' => $sort_by, 'order' => $sort_order]))) ?>">
                                            Next <i class="bi bi-chevron-right"></i>
                                        </a>
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
    <footer>
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4">
                    <h5 class="fw-bold mb-3">
                        <i class="bi bi-shield-check"></i> AppTrust
                    </h5>
                    <p class="text-muted">
                        Your trusted source for app reviews, trust scores, and scam alerts.
                    </p>
                </div>
                <div class="col-md-2 mb-4">
                    <h6 class="fw-bold mb-3">Platform</h6>
                    <ul class="list-unstyled">
                        <li><a href="<?= base_url('apps') ?>">Browse Apps</a></li>
                        <li><a href="<?= base_url('categories') ?>">Categories</a></li>
                        <li><a href="<?= base_url('scam-alerts') ?>">Scam Alerts</a></li>
                    </ul>
                </div>
                <div class="col-md-2 mb-4">
                    <h6 class="fw-bold mb-3">Resources</h6>
                    <ul class="list-unstyled">
                        <li><a href="<?= base_url('blog') ?>">Blog</a></li>
                        <li><a href="#">About Us</a></li>
                        <li><a href="#">Contact</a></li>
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
