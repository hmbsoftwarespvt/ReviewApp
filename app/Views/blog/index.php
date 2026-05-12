<?= $this->extend('base_template') ?>

<?= $this->section('content') ?>

<style>
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .navbar {
            background: var(--primary-gradient);
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .blog-header {
            background: var(--primary-gradient);
            color: white;
            padding: 60px 0 40px;
            margin-bottom: 40px;
        }
        
        .category-filter {
            background: white;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        
        .category-filter .btn {
            margin: 5px;
        }
        
        .blog-card {
            border: none;
            border-radius: 15px;
            overflow: hidden;
            transition: all 0.3s;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            height: 100%;
            background: white;
        }
        
        .blog-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(0,0,0,0.15);
        }
        
        .blog-featured-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
            background: var(--primary-gradient);
        }
        
        .blog-category-badge {
            position: absolute;
            top: 10px;
            left: 10px;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
            color: white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }
        
        .category-guides {
            background: #667eea;
        }
        
        .category-tips_tricks {
            background: #f093fb;
        }
        
        .category-scam_alerts {
            background: #dc3545;
        }
        
        .category-news_updates {
            background: #4facfe;
        }
        
        .category-reviews {
            background: #43e97b;
        }
        
        .blog-meta {
            font-size: 0.9rem;
            color: #6c757d;
        }
        
        .blog-meta i {
            margin-right: 5px;
        }
        
        .no-posts {
            text-align: center;
            padding: 60px 20px;
        }
        
        .no-posts i {
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

    <!-- Blog Header -->
    <section class="blog-header">
        <div class="container text-center">
            <h1 class="display-4 fw-bold mb-3">
                <i class="bi bi-newspaper"></i> Blog
            </h1>
            <p class="lead mb-0">
                Stay informed with guides, tips, scam alerts, and app reviews
            </p>
        </div>
    </section>

    <!-- Category Filter -->
    <section class="container">
        <div class="category-filter">
            <div class="d-flex flex-wrap align-items-center justify-content-center">
                <span class="fw-bold me-3 mb-2">Filter by Category:</span>
                <a href="<?= base_url('blog') ?>" 
                   class="btn btn-sm <?= empty($currentCategory) ? 'btn-primary' : 'btn-outline-primary' ?>">
                    All Posts
                </a>
                <?php foreach ($categories as $key => $name): ?>
                    <a href="<?= base_url('blog?category=' . $key) ?>" 
                       class="btn btn-sm <?= $currentCategory === $key ? 'btn-primary' : 'btn-outline-primary' ?>">
                        <?= esc($name) ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Blog Posts Grid -->
    <section class="container mb-5">
        <?php if (empty($posts)): ?>
            <!-- No Posts Message -->
            <div class="no-posts">
                <i class="bi bi-inbox"></i>
                <h3>No blog posts found</h3>
                <p class="text-muted mb-4">
                    <?php if ($currentCategory): ?>
                        No posts in this category yet. Try browsing other categories.
                    <?php else: ?>
                        Check back soon for new articles.
                    <?php endif; ?>
                </p>
                <?php if ($currentCategory): ?>
                    <a href="<?= base_url('blog') ?>" class="btn btn-primary">
                        <i class="bi bi-arrow-left"></i> View All Posts
                    </a>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <!-- Results Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4>
                    <?php if ($currentCategory): ?>
                        <?= esc($categories[$currentCategory]) ?> - 
                    <?php endif; ?>
                    <?= number_format($pagination['total']) ?> 
                    <?= $pagination['total'] == 1 ? 'Post' : 'Posts' ?>
                </h4>
                <div class="text-muted">
                    Page <?= $pagination['current_page'] ?> of <?= $pagination['total_pages'] ?>
                </div>
            </div>
            
            <!-- Posts Grid -->
            <div class="row g-4">
                <?php foreach ($posts as $post): ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="card blog-card">
                            <div class="position-relative">
                                <span class="blog-category-badge category-<?= esc($post['category']) ?>">
                                    <?php
                                    $categoryName = str_replace('_', ' ', $post['category']);
                                    echo esc(ucwords($categoryName));
                                    ?>
                                </span>
                                
                                <?php if (!empty($post['featured_image'])): ?>
                                    <img src="<?= base_url('uploads/blog/' . esc($post['featured_image'])) ?>" 
                                         class="blog-featured-image" 
                                         alt="<?= esc($post['title']) ?>">
                                <?php else: ?>
                                    <div class="blog-featured-image d-flex align-items-center justify-content-center">
                                        <i class="bi bi-newspaper text-white" style="font-size: 4rem;"></i>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="card-body">
                                <h5 class="card-title mb-3">
                                    <a href="<?= base_url('blog/' . esc($post['slug'])) ?>" 
                                       class="text-decoration-none text-dark">
                                        <?= esc($post['title']) ?>
                                    </a>
                                </h5>
                                
                                <p class="card-text text-muted">
                                    <?php if (!empty($post['excerpt'])): ?>
                                        <?= esc(substr($post['excerpt'], 0, 120)) ?>...
                                    <?php else: ?>
                                        <?= esc(substr(strip_tags($post['content']), 0, 120)) ?>...
                                    <?php endif; ?>
                                </p>
                                
                                <div class="blog-meta d-flex justify-content-between align-items-center mt-3">
                                    <small>
                                        <i class="bi bi-calendar"></i>
                                        <?= date('M d, Y', strtotime($post['published_at'] ?? $post['created_at'])) ?>
                                    </small>
                                    <small>
                                        <i class="bi bi-eye"></i>
                                        <?= number_format($post['view_count']) ?>
                                    </small>
                                </div>
                                
                                <a href="<?= base_url('blog/' . esc($post['slug'])) ?>" 
                                   class="btn btn-sm btn-outline-primary mt-3 w-100">
                                    Read More <i class="bi bi-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <!-- Pagination -->
            <?php if ($pagination['total_pages'] > 1): ?>
                <nav aria-label="Blog pagination" class="mt-5">
                    <ul class="pagination justify-content-center">
                        <!-- Previous Page -->
                        <?php if ($pagination['current_page'] > 1): ?>
                            <li class="page-item">
                                <a class="page-link" 
                                   href="<?= base_url('blog?page=' . ($pagination['current_page'] - 1) . ($currentCategory ? '&category=' . $currentCategory : '')) ?>">
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
                                   href="<?= base_url('blog?page=1' . ($currentCategory ? '&category=' . $currentCategory : '')) ?>">
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
                                   href="<?= base_url('blog?page=' . $i . ($currentCategory ? '&category=' . $currentCategory : '')) ?>">
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
                                   href="<?= base_url('blog?page=' . $pagination['total_pages'] . ($currentCategory ? '&category=' . $currentCategory : '')) ?>">
                                    <?= $pagination['total_pages'] ?>
                                </a>
                            </li>
                        <?php endif; ?>
                        
                        <!-- Next Page -->
                        <?php if ($pagination['current_page'] < $pagination['total_pages']): ?>
                            <li class="page-item">
                                <a class="page-link" 
                                   href="<?= base_url('blog?page=' . ($pagination['current_page'] + 1) . ($currentCategory ? '&category=' . $currentCategory : '')) ?>">
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

    <?= $this->endSection() ?>

