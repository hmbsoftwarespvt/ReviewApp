<?= $this->extend('base_template') ?>
<?= $this->section('content') ?>

<section class="article-header">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= base_url('/') ?>">Home</a></li>
                <li class="breadcrumb-item"><a href="<?= base_url('blog') ?>">Blog</a></li>
                <li class="breadcrumb-item active" aria-current="page"><?= esc($post['title']) ?></li>
            </ol>
        </nav>

        <div class="text-center">
            <span class="category-badge category-<?= esc($post['category']) ?>">
                <?= esc($categoryNames[$post['category']] ?? ucwords(str_replace('_', ' ', $post['category']))) ?>
            </span>

            <h1 class="display-5 fw-bold mb-4">
                <?= esc($post['title']) ?>
            </h1>

            <div class="article-meta">
                <i class="bi bi-person-circle"></i>
                <?= esc($post['author_name'] ?? 'Admin') ?>
                <span class="mx-3">|</span>
                <i class="bi bi-calendar"></i>
                <?= date('F d, Y', strtotime($post['published_at'] ?? $post['created_at'])) ?>
                <span class="mx-3">|</span>
                <i class="bi bi-eye"></i>
                <?= number_format($post['view_count']) ?> views
            </div>
        </div>
    </div>
</section>

<section class="container mb-5">
    <div class="row">
        <div class="col-lg-8">
            <a href="<?= base_url('blog') ?>" class="back-to-blog">
                <i class="bi bi-arrow-left"></i> Back to Blog
            </a>

            <article class="article-content">
                <?php if (!empty($post['featured_image'])): ?>
                    <img src="<?= base_url('uploads/blog/' . esc($post['featured_image'])) ?>"
                         class="featured-image"
                         alt="<?= esc($post['title']) ?>">
                <?php endif; ?>

                <?php if (!empty($post['excerpt'])): ?>
                    <p class="lead">
                        <?= esc($post['excerpt']) ?>
                    </p>
                    <hr class="my-4">
                <?php endif; ?>

                <div>
                    <?= $post['content'] ?>
                </div>
            </article>
        </div>

        <div class="col-lg-4">
            <?php if (!empty($relatedPosts)): ?>
                <div class="related-articles">
                    <h4 class="mb-4">
                        <i class="bi bi-bookmark"></i> Related Articles
                    </h4>

                    <?php foreach ($relatedPosts as $relatedPost): ?>
                        <a href="<?= base_url('blog/' . esc($relatedPost['slug'])) ?>"
                           class="related-article-card">
                            <div class="related-article-title">
                                <?= esc($relatedPost['title']) ?>
                            </div>
                            <div class="related-article-meta">
                                <i class="bi bi-calendar"></i>
                                <?= date('M d, Y', strtotime($relatedPost['published_at'] ?? $relatedPost['created_at'])) ?>
                                <span class="mx-2">|</span>
                                <i class="bi bi-eye"></i>
                                <?= number_format($relatedPost['view_count']) ?>
                            </div>
                        </a>
                    <?php endforeach; ?>

                    <a href="<?= base_url('blog?category=' . $post['category']) ?>"
                       class="btn btn-outline-primary w-100 mt-3">
                        View All in <?= esc($categoryNames[$post['category']]) ?>
                    </a>
                </div>
            <?php endif; ?>

            <div class="related-articles mt-4">
                <h5 class="mb-3">
                    <i class="bi bi-envelope"></i> Stay Updated
                </h5>
                <p class="text-muted small">
                    Subscribe to our newsletter for the latest app security news and scam alerts.
                </p>
                <form action="<?= base_url('newsletter/subscribe') ?>" method="post">
                    <?= csrf_field() ?>
                    <div class="input-group mb-3">
                        <input type="email"
                               class="form-control"
                               name="email"
                               placeholder="Your email"
                               required>
                        <button class="btn btn-primary" type="submit">Subscribe</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<style>
:root {
    --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.article-header {
    background: var(--primary-gradient);
    color: white;
    padding: 60px 0 40px;
    margin-bottom: 40px;
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

.breadcrumb-item a:hover { color: white; }

.breadcrumb-item.active { color: white; }

.breadcrumb-item + .breadcrumb-item::before { color: rgba(255,255,255,0.6); }

.category-badge {
    display: inline-block;
    padding: 8px 16px;
    border-radius: 20px;
    font-size: 0.9rem;
    font-weight: 600;
    color: white;
    margin-bottom: 20px;
}

.category-guides { background: #667eea; }
.category-tips_tricks { background: #f093fb; }
.category-scam_alerts { background: #dc3545; }
.category-news_updates { background: #4facfe; }
.category-reviews { background: #43e97b; }

.article-meta {
    font-size: 1rem;
    opacity: 0.9;
}

.article-meta i { margin-right: 8px; }

.article-content {
    background: white;
    border-radius: 15px;
    padding: 40px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    margin-bottom: 40px;
}

.featured-image {
    width: 100%;
    max-height: 500px;
    object-fit: cover;
    border-radius: 15px;
    margin-bottom: 30px;
}

.article-content h1,
.article-content h2,
.article-content h3,
.article-content h4 {
    margin-top: 30px;
    margin-bottom: 15px;
    color: #2d3748;
}

.article-content p {
    line-height: 1.8;
    margin-bottom: 20px;
    color: #4a5568;
}

.article-content ul,
.article-content ol {
    margin-bottom: 20px;
    padding-left: 30px;
}

.article-content li {
    margin-bottom: 10px;
    line-height: 1.6;
}

.article-content img {
    max-width: 100%;
    height: auto;
    border-radius: 10px;
    margin: 20px 0;
}

.article-content blockquote {
    border-left: 4px solid #667eea;
    padding-left: 20px;
    margin: 30px 0;
    font-style: italic;
    color: #4a5568;
}

.related-articles {
    background: white;
    border-radius: 15px;
    padding: 30px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.related-article-card {
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    padding: 15px;
    margin-bottom: 15px;
    transition: all 0.3s;
    text-decoration: none;
    display: block;
    color: inherit;
}

.related-article-card:hover {
    border-color: #667eea;
    box-shadow: 0 4px 8px rgba(102, 126, 234, 0.1);
    transform: translateX(5px);
}

.related-article-title {
    font-weight: 600;
    color: #2d3748;
    margin-bottom: 8px;
}

.related-article-meta {
    font-size: 0.85rem;
    color: #718096;
}

.back-to-blog {
    display: inline-block;
    margin-bottom: 20px;
    color: #667eea;
    text-decoration: none;
    font-weight: 500;
}

.back-to-blog:hover { color: #764ba2; }
</style>

<?= $this->endSection() ?>
