<?= $this->extend('base_template') ?>
<?= $this->section('content') ?>

<link rel="stylesheet" href="<?= base_url('css/apptrust-theme.css') ?>">
<style>
        /* App Detail Page Specific Styles */
        .app-header {
            background: linear-gradient(135deg, var(--primary-blue) 0%, var(--primary-purple) 100%);
            color: white;
            padding: 40px 0;
            margin-bottom: 40px;
        }
        
        .app-icon-large {
            width: 120px;
            height: 120px;
            border-radius: 20px;
            object-fit: cover;
            box-shadow: var(--shadow-lg);
            background: white;
        }
        
        .trust-score-large {
            display: inline-block;
            padding: 15px 30px;
            border-radius: 50px;
            font-size: 2rem;
            font-weight: 800;
            box-shadow: var(--shadow-lg);
        }
        
        .progress-bar-custom {
            height: 25px;
            border-radius: 12px;
        }
        
        .screenshot-thumbnail {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: var(--border-radius);
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .screenshot-thumbnail:hover {
            transform: scale(1.05);
            box-shadow: var(--shadow-lg);
        }
        
        .modal-screenshot {
            max-width: 100%;
            max-height: 80vh;
            object-fit: contain;
        }
        
        .info-item {
            margin-bottom: 15px;
        }
        
        .info-label {
            font-weight: 700;
            color: var(--text-secondary);
            margin-right: 10px;
        }
        
        /* Star Rating Input */
        .star-rating-input {
            display: flex;
            flex-direction: row-reverse;
            justify-content: flex-end;
            font-size: 2.5rem;
            line-height: 1;
        }
        
        .star-rating-input input[type="radio"] {
            display: none;
        }
        
        .star-rating-input label {
            color: #ddd;
            cursor: pointer;
            transition: color 0.2s;
            margin: 0 5px;
        }
        
        .star-rating-input label:hover,
        .star-rating-input label:hover ~ label,
        .star-rating-input input[type="radio"]:checked ~ label {
            color: #FBBF24;
        }

        /* ===== SIMILAR APPS ===== */
        .sim-section-wrap {
            margin-top: 2rem;
        }
        .sim-section {
            background: #fff;
            border-radius: 18px;
            padding: 1.5rem;
            box-shadow: 0 2px 12px rgba(0,0,0,0.04);
            border: 1px solid #F3F4F6;
        }
        .sim-header {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            margin-bottom: 1.25rem;
            padding-bottom: 0.75rem;
            border-bottom: 1px solid #F3F4F6;
        }
        .sim-header i {
            font-size: 1.3rem;
            color: #2563EB;
        }
        .sim-header h3 {
            font-size: 1.05rem;
            font-weight: 700;
            color: #111827;
            margin: 0;
        }
        .sim-list {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }
        .sim-card {
            display: flex;
            align-items: center;
            gap: 0.85rem;
            padding: 0.75rem;
            border-radius: 12px;
            background: #F9FAFB;
            text-decoration: none;
            transition: all 0.2s;
        }
        .sim-card:hover {
            background: #EFF6FF;
            transform: translateX(4px);
        }
        .sim-icon {
            width: 46px;
            height: 46px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
            font-weight: 700;
            color: #fff;
            flex-shrink: 0;
            overflow: hidden;
        }
        .sim-icon img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .sim-info {
            flex: 1;
            min-width: 0;
        }
        .sim-name {
            font-size: 0.92rem;
            font-weight: 600;
            color: #111827;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .sim-cat {
            font-size: 0.75rem;
            color: #9CA3AF;
            margin-top: 0.1rem;
        }
        .sim-score {
            font-size: 0.82rem;
            font-weight: 700;
            padding: 0.25rem 0.65rem;
            border-radius: 20px;
            white-space: nowrap;
            flex-shrink: 0;
        }
        .sim-arrow {
            color: #D1D5DB;
            font-size: 0.85rem;
            flex-shrink: 0;
            transition: color 0.2s;
        }
        .sim-card:hover .sim-arrow {
            color: #2563EB;
        }
    </style>

<!-- App Header -->
    <section class="app-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-2 text-center">
                    <?php if (!empty($app['thumbnail'])): ?>
                        <img src="<?= base_url('uploads/thumbnails/' . esc($app['thumbnail'])) ?>" class="app-icon-large" alt="<?= esc($app['name']) ?>">
                    <?php else: ?>
                        <div class="app-icon-large d-flex align-items-center justify-content-center" style="background: linear-gradient(135deg, var(--primary-blue) 0%, var(--primary-purple) 100%);">
                            <i class="bi bi-app text-white" style="font-size: 3rem;"></i>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="col-md-7">
                    <h1 class="mb-2" style="font-size: var(--font-size-4xl); font-weight: 800;"><?= esc($app['name']) ?></h1>
                    <p class="mb-2" style="font-size: var(--font-size-lg);">
                        <i class="bi bi-building"></i> <?= esc($app['developer_name']) ?>
                    </p>
                    <div class="mb-2">
                        <?php foreach ($categories as $category): ?>
                            <span class="category-badge" style="background: white; color: var(--primary-blue);">
                                <?= esc($category['name']) ?>
                            </span>
                        <?php endforeach; ?>
                    </div>
                    <div class="d-flex align-items-center gap-3">
                        <div>
                            <div class="rating-stars">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <?php if ($i <= floor($averageRating)): ?>
                                        <i class="bi bi-star-fill"></i>
                                    <?php elseif ($i - $averageRating < 1): ?>
                                        <i class="bi bi-star-half"></i>
                                    <?php else: ?>
                                        <i class="bi bi-star"></i>
                                    <?php endif; ?>
                                <?php endfor; ?>
                            </div>
                            <small><?= number_format($averageRating, 1) ?> (<?= number_format($reviewCount) ?> reviews)</small>
                        </div>
                        <div>
                            <i class="bi bi-eye"></i> <?= number_format($app['view_count']) ?> views
                        </div>
                    </div>
                </div>
                <div class="col-md-3 text-center">
                    <div class="mb-2">
                        <small class="d-block mb-2" style="font-size: var(--font-size-sm);">Trust Score</small>
                        <?php
                        // Determine trust score class
                        $trustScore = (float)$app['trust_score'];
                        $badgeClass = 'low';
                        if ($trustScore >= 80) {
                            $badgeClass = 'excellent';
                        } elseif ($trustScore >= 65) {
                            $badgeClass = 'good';
                        } elseif ($trustScore >= 50) {
                            $badgeClass = 'medium';
                        }
                        ?>
                        <div class="trust-score-large trust-score-badge <?= $badgeClass ?>">
                            <?= number_format($app['trust_score'], 0) ?>
                        </div>
                    </div>
                    <?php if (!empty($app['download_url'])): ?>
                        <a href="<?= esc($app['download_url']) ?>" target="_blank" class="btn-primary-apptrust mt-3" style="padding: 12px 24px;">
                            <i class="bi bi-download"></i> Download
                        </a>
                    <?php endif; ?>
                    <?php if (!empty($app['youtube_link'])): ?>
                        <a href="<?= esc($app['youtube_link']) ?>" target="_blank" class="btn-primary-apptrust mt-3 ms-2" style="padding: 12px 24px; background-color: #FF0000;">
                            <i class="bi bi-youtube"></i> Watch Review
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <?php if (!empty($app['youtube_link'])): 
        $ytId = '';
        $parsed = parse_url($app['youtube_link']);
        if (isset($parsed['host'])) {
            if (str_contains($parsed['host'], 'youtu.be')) {
                $ytId = ltrim($parsed['path'] ?? '', '/');
            } elseif (str_contains($parsed['host'], 'youtube.com')) {
                parse_str($parsed['query'] ?? '', $ytQuery);
                $ytId = $ytQuery['v'] ?? '';
            }
        }
    ?>
    <div class="container mb-4">
        <div class="row">
            <div class="col-lg-8">
                <div class="card-apptrust">
                    <h3 style="font-size: var(--font-size-2xl); font-weight: 700; color: var(--text-primary); margin-bottom: var(--spacing-lg);">
                        <i class="bi bi-youtube" style="color:#FF0000;"></i> Video Review
                    </h3>
                    <div class="ratio ratio-16x9">
                        <iframe src="https://www.youtube.com/embed/<?= esc($ytId) ?>" 
                                title="YouTube video review" 
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                                allowfullscreen>
                        </iframe>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <div class="container mb-5">
        <div class="row">
            <!-- Main Content -->
            <div class="col-lg-8">
                <!-- Trust Score Breakdown -->
                <div class="card-apptrust">
                    <h3 style="font-size: var(--font-size-2xl); font-weight: 700; color: var(--text-primary); margin-bottom: var(--spacing-lg);">Trust Score Breakdown</h3>
                    
                    <?php foreach ($trustScoreBreakdown as $component => $data): ?>
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span><?= esc($data['label']) ?></span>
                                <span><strong><?= number_format($data['score'], 1) ?></strong> / <?= number_format($data['max_points'], 0) ?></span>
                            </div>
                            <div class="progress">
                                <?php
                                $percentage = ($data['max_points'] > 0) ? ($data['score'] / $data['max_points']) * 100 : 0;
                                $progressClass = 'bg-success';
                                if ($percentage < 50) {
                                    $progressClass = 'bg-danger';
                                } elseif ($percentage < 75) {
                                    $progressClass = 'bg-warning';
                                }
                                ?>
                                <div class="progress-bar progress-bar-custom <?= $progressClass ?>" 
                                     role="progressbar" 
                                     style="width: <?= $percentage ?>%"
                                     aria-valuenow="<?= $data['score'] ?>" 
                                     aria-valuemin="0" 
                                     aria-valuemax="<?= $data['max_points'] ?>">
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- App Information -->
                <div class="card-apptrust">
                    <h3 style="font-size: var(--font-size-2xl); font-weight: 700; color: var(--text-primary); margin-bottom: var(--spacing-lg);">App Information</h3>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-item">
                                <span class="info-label">Version:</span>
                                <span><?= esc($app['version'] ?? 'N/A') ?></span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Size:</span>
                                <span><?= esc($app['size'] ?? 'N/A') ?></span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Platform:</span>
                                <span class="text-capitalize"><?= esc($app['platform_type']) ?></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-item">
                                <span class="info-label">Price:</span>
                                <span><?= $app['price'] > 0 ? '$' . number_format($app['price'], 2) : 'Free' ?></span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Release Date:</span>
                                <span><?= !empty($app['release_date']) ? date('M d, Y', strtotime($app['release_date'])) : 'N/A' ?></span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Developer:</span>
                                <span><?= esc($app['developer_name']) ?></span>
                            </div>
                        </div>
                    </div>
                    
                    <?php if (!empty($app['description'])): ?>
                        <div class="mt-4">
                            <h5>Description</h5>
                            <p><?= nl2br(esc($app['description'])) ?></p>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Screenshots -->
                <?php if (!empty($screenshots)): ?>
                    <div class="card-apptrust">
                        <h3 style="font-size: var(--font-size-2xl); font-weight: 700; color: var(--text-primary); margin-bottom: var(--spacing-lg);">Screenshots</h3>
                        <div class="row g-3">
                            <?php foreach ($screenshots as $screenshot): ?>
                                <div class="col-md-4">
                                    <img src="<?= base_url('uploads/screenshots/' . esc($screenshot['filename'])) ?>" 
                                         class="screenshot-thumbnail" 
                                         alt="Screenshot"
                                         data-bs-toggle="modal"
                                         data-bs-target="#screenshotModal"
                                         data-screenshot="<?= base_url('uploads/screenshots/' . esc($screenshot['filename'])) ?>">
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Reviews -->
                <div class="card-apptrust">
                    <h3 style="font-size: var(--font-size-2xl); font-weight: 700; color: var(--text-primary); margin-bottom: var(--spacing-lg);">User Reviews (<?= number_format($reviewCount) ?>)</h3>
                    
                    <!-- Success/Error Messages -->
                    <?php if (session()->getFlashdata('success')): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="bi bi-check-circle"></i> <?= session()->getFlashdata('success') ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (session()->getFlashdata('error')): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="bi bi-exclamation-circle"></i> <?= session()->getFlashdata('error') ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (session()->getFlashdata('errors')): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="bi bi-exclamation-circle"></i>
                            <ul class="mb-0">
                                <?php foreach (session()->getFlashdata('errors') as $error): ?>
                                    <li><?= esc($error) ?></li>
                                <?php endforeach; ?>
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Pending Review Indicator -->
                    <?php if ($userPendingReview): ?>
                        <div class="alert alert-warning">
                            <i class="bi bi-clock-history"></i> 
                            <strong>Your review is pending approval</strong>
                            <p class="mb-0 mt-2">
                                <strong>Rating:</strong> <?= str_repeat('⭐', $userPendingReview['rating']) ?><br>
                                <strong>Title:</strong> <?= esc($userPendingReview['title']) ?><br>
                                <small class="text-muted">Submitted on <?= date('M d, Y', strtotime($userPendingReview['created_at'])) ?></small>
                            </p>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Review Submission Form -->
                    <?php if (session()->get('isLoggedIn') && !$userHasReviewed): ?>
                        <div class="card-apptrust mb-4" style="border: 2px solid var(--primary-blue);">
                            <div style="background: linear-gradient(135deg, var(--primary-blue) 0%, var(--primary-purple) 100%); color: white; padding: var(--spacing-md); border-radius: var(--border-radius-lg) var(--border-radius-lg) 0 0; margin: calc(-1 * var(--spacing-lg)) calc(-1 * var(--spacing-lg)) var(--spacing-lg);">
                                <h5 class="mb-0" style="font-weight: 700;"><i class="bi bi-pencil-square"></i> Write a Review</h5>
                            </div>
                                <form action="<?= base_url('apps/submit-review/' . $app['id']) ?>" method="POST" id="reviewForm">
                                    <?= csrf_field() ?>
                                    
                                    <!-- Rating -->
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Rating <span class="text-danger">*</span></label>
                                        <div class="star-rating-input">
                                            <input type="radio" name="rating" value="5" id="star5" required>
                                            <label for="star5" title="5 stars">★</label>
                                            <input type="radio" name="rating" value="4" id="star4">
                                            <label for="star4" title="4 stars">★</label>
                                            <input type="radio" name="rating" value="3" id="star3">
                                            <label for="star3" title="3 stars">★</label>
                                            <input type="radio" name="rating" value="2" id="star2">
                                            <label for="star2" title="2 stars">★</label>
                                            <input type="radio" name="rating" value="1" id="star1">
                                            <label for="star1" title="1 star">★</label>
                                        </div>
                                        <small class="text-muted">Click to rate from 1 to 5 stars</small>
                                    </div>
                                    
                                    <!-- Title -->
                                    <div class="mb-3">
                                        <label for="title" class="form-label fw-bold">Review Title <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="title" name="title" 
                                               placeholder="Summarize your experience" 
                                               maxlength="255" required
                                               value="<?= old('title') ?>">
                                        <small class="text-muted">Maximum 255 characters</small>
                                    </div>
                                    
                                    <!-- Review Text -->
                                    <div class="mb-3">
                                        <label for="review_text" class="form-label fw-bold">Your Review <span class="text-danger">*</span></label>
                                        <textarea class="form-control" id="review_text" name="review_text" 
                                                  rows="5" 
                                                  placeholder="Share your detailed experience with this app (minimum 50 characters)" 
                                                  minlength="50" maxlength="2000" required><?= old('review_text') ?></textarea>
                                        <small class="text-muted">
                                            <span id="charCount">0</span> / 2000 characters (minimum 50)
                                        </small>
                                    </div>
                                    
                                    <!-- Pros (Optional) -->
                                    <div class="mb-3">
                                        <label for="pros" class="form-label fw-bold">Pros (Optional)</label>
                                        <textarea class="form-control" id="pros" name="pros" 
                                                  rows="2" 
                                                  placeholder="What did you like about this app?" 
                                                  maxlength="1000"><?= old('pros') ?></textarea>
                                        <small class="text-muted">Maximum 1000 characters</small>
                                    </div>
                                    
                                    <!-- Cons (Optional) -->
                                    <div class="mb-3">
                                        <label for="cons" class="form-label fw-bold">Cons (Optional)</label>
                                        <textarea class="form-control" id="cons" name="cons" 
                                                  rows="2" 
                                                  placeholder="What could be improved?" 
                                                  maxlength="1000"><?= old('cons') ?></textarea>
                                        <small class="text-muted">Maximum 1000 characters</small>
                                    </div>
                                    
                                    <button type="submit" class="btn-primary-apptrust" style="padding: 12px 24px;">
                                        <i class="bi bi-send"></i> Submit Review
                                    </button>
                                </form>
                            </div>
                        </div>
                    <?php elseif (!session()->get('isLoggedIn')): ?>
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i> 
                            <a href="<?= base_url('login') ?>" class="alert-link">Login</a> or 
                            <a href="<?= base_url('register') ?>" class="alert-link">register</a> to write a review.
                        </div>
                    <?php endif; ?>
                    
                    <!-- Existing Reviews -->
                    <h4 class="mt-4 mb-3">Community Reviews</h4>
                    
                    <?php if (empty($reviews['data'])): ?>
                        <p class="text-muted">No reviews yet. Be the first to review this app!</p>
                    <?php else: ?>
                        <?php foreach ($reviews['data'] as $review): ?>
                            <div class="card-apptrust mb-3" style="padding: var(--spacing-md);">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <h5 class="mb-1" style="font-weight: 600;"><?= esc($review['title']) ?></h5>
                                        <div class="rating-stars">
                                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                                <?php if ($i <= $review['rating']): ?>
                                                    <i class="bi bi-star-fill"></i>
                                                <?php else: ?>
                                                    <i class="bi bi-star"></i>
                                                <?php endif; ?>
                                            <?php endfor; ?>
                                        </div>
                                    </div>
                                    <small style="color: var(--text-tertiary);"><?= date('M d, Y', strtotime($review['created_at'])) ?></small>
                                </div>
                                <p style="color: var(--text-secondary);"><?= nl2br(esc($review['review_text'])) ?></p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <small style="color: var(--text-tertiary);">
                                        By <?= esc($review['username'] ?? 'Anonymous') ?>
                                    </small>
                                    <small style="color: var(--text-tertiary);">
                                        <i class="bi bi-hand-thumbs-up"></i> <?= number_format($review['helpful_count']) ?> helpful
                                    </small>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        
                        <!-- Pagination -->
                        <?php if ($reviews['pagination']['total_pages'] > 1): ?>
                            <nav style="margin-top: var(--spacing-lg);">
                                <ul class="pagination justify-content-center">
                                    <?php for ($i = 1; $i <= $reviews['pagination']['total_pages']; $i++): ?>
                                        <li class="page-item <?= $i == $reviews['pagination']['current_page'] ? 'active' : '' ?>">
                                            <a class="page-link" href="?review_page=<?= $i ?>" style="<?= $i == $reviews['pagination']['current_page'] ? 'background-color: var(--primary-blue); border-color: var(--primary-blue); color: white;' : 'color: var(--primary-blue);' ?>"><?= $i ?></a>
                                        </li>
                                    <?php endfor; ?>
                                </ul>
                            </nav>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>

                <!-- Scam Reports -->
                <div class="card-apptrust">
                    <h3 style="font-size: var(--font-size-2xl); font-weight: 700; color: var(--text-primary); margin-bottom: var(--spacing-lg);">
                        Scam Reports (<?= number_format($totalScamReports) ?>)
                        <?php if ($totalScamReports > 0): ?>
                            <span class="ms-2">
                                <?php if ($scamReportCounts['high'] > 0): ?>
                                    <span class="risk-badge high">High: <?= $scamReportCounts['high'] ?></span>
                                <?php endif; ?>
                                <?php if ($scamReportCounts['medium'] > 0): ?>
                                    <span class="risk-badge medium">Medium: <?= $scamReportCounts['medium'] ?></span>
                                <?php endif; ?>
                                <?php if ($scamReportCounts['low'] > 0): ?>
                                    <span class="risk-badge risk-low">Low: <?= $scamReportCounts['low'] ?></span>
                                <?php endif; ?>
                            </span>
                        <?php endif; ?>
                    </h3>
                    
                    <!-- Pending Scam Report Indicator -->
                    <?php if ($userPendingScamReport): ?>
                        <div class="alert alert-warning">
                            <i class="bi bi-clock-history"></i> 
                            <strong>Your scam report is pending verification</strong>
                            <p class="mb-0 mt-2">
                                <strong>Risk Level:</strong> <span class="risk-badge risk-<?= esc($userPendingScamReport['risk_level']) ?>"><?= strtoupper(esc($userPendingScamReport['risk_level'])) ?></span><br>
                                <strong>Title:</strong> <?= esc($userPendingScamReport['title']) ?><br>
                                <small class="text-muted">Submitted on <?= date('M d, Y', strtotime($userPendingScamReport['created_at'])) ?></small>
                            </p>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Scam Report Submission Form -->
                    <?php if (session()->get('isLoggedIn') && !$userPendingScamReport): ?>
                        <div class="card mb-4" style="border: 2px solid #dc3545;">
                            <div class="card-header" style="background: linear-gradient(135deg, #dc3545 0%, #c82333 100%); color: white;">
                                <h5 class="mb-0"><i class="bi bi-exclamation-triangle"></i> Report a Scam</h5>
                            </div>
                            <div class="card-body">
                                <form action="<?= base_url('apps/submit-scam-report/' . $app['id']) ?>" method="POST" id="scamReportForm">
                                    <?= csrf_field() ?>
                                    
                                    <!-- Title -->
                                    <div class="mb-3">
                                        <label for="scam_title" class="form-label fw-bold">Report Title <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="scam_title" name="title" 
                                               placeholder="Brief summary of the issue" 
                                               maxlength="255" required
                                               value="<?= old('title') ?>">
                                        <small class="text-muted">Maximum 255 characters</small>
                                    </div>
                                    
                                    <!-- Risk Level -->
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Risk Level <span class="text-danger">*</span></label>
                                        <div class="d-flex gap-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="risk_level" id="risk_low" value="low" required <?= old('risk_level') === 'low' ? 'checked' : '' ?>>
                                                <label class="form-check-label" for="risk_low">
                                                    <span class="risk-badge risk-low">Low</span>
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="risk_level" id="risk_medium" value="medium" <?= old('risk_level') === 'medium' ? 'checked' : '' ?>>
                                                <label class="form-check-label" for="risk_medium">
                                                    <span class="risk-badge risk-medium">Medium</span>
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="risk_level" id="risk_high" value="high" <?= old('risk_level') === 'high' ? 'checked' : '' ?>>
                                                <label class="form-check-label" for="risk_high">
                                                    <span class="risk-badge risk-high">High</span>
                                                </label>
                                            </div>
                                        </div>
                                        <small class="text-muted">Select the severity of the issue</small>
                                    </div>
                                    
                                    <!-- Description -->
                                    <div class="mb-3">
                                        <label for="scam_description" class="form-label fw-bold">Detailed Description <span class="text-danger">*</span></label>
                                        <textarea class="form-control" id="scam_description" name="description" 
                                                  rows="6" 
                                                  placeholder="Provide detailed information about the scam or suspicious behavior (minimum 100 characters)" 
                                                  minlength="100" maxlength="3000" required><?= old('description') ?></textarea>
                                        <small class="text-muted">
                                            <span id="scamCharCount">0</span> / 3000 characters (minimum 100)
                                        </small>
                                    </div>
                                    
                                    <!-- Evidence URLs (Optional) -->
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Evidence URLs (Optional, max 5)</label>
                                        <small class="text-muted d-block mb-2">Provide links to screenshots, articles, or other evidence</small>
                                        
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <div class="mb-2">
                                                <input type="url" class="form-control" name="evidence_url_<?= $i ?>" 
                                                       placeholder="https://example.com/evidence-<?= $i ?>" 
                                                       maxlength="500"
                                                       value="<?= old("evidence_url_{$i}") ?>">
                                            </div>
                                        <?php endfor; ?>
                                    </div>
                                    
                                    <div class="alert alert-info">
                                        <i class="bi bi-info-circle"></i> 
                                        <strong>Note:</strong> Your report will be reviewed by our moderation team before being published. False reports may result in account suspension.
                                    </div>
                                    
                                    <button type="submit" class="btn btn-danger btn-lg">
                                        <i class="bi bi-send"></i> Submit Scam Report
                                    </button>
                                </form>
                            </div>
                        </div>
                    <?php elseif (!session()->get('isLoggedIn')): ?>
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i> 
                            <a href="<?= base_url('login') ?>" class="alert-link">Login</a> or 
                            <a href="<?= base_url('register') ?>" class="alert-link">register</a> to report a scam.
                        </div>
                    <?php endif; ?>
                    
                    <!-- Existing Scam Reports -->
                    <h4 class="mt-4 mb-3">Community Reports</h4>
                    
                    <?php if (session()->get('isLoggedIn')): ?>
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle"></i> 
                            <a href="<?= base_url('scam-alerts/report?app=' . urlencode($app['name'])) ?>" class="alert-link">Report a scam</a> if you've experienced issues with this app.
                        </div>
                    <?php endif; ?>
                    
                    <?php if (empty($scamReports['data'])): ?>
                        <p class="text-muted">No scam reports for this app.</p>
                    <?php else: ?>
                        <?php foreach ($scamReports['data'] as $report): ?>
                            <div class="scam-report-card">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <h5 class="mb-1"><?= esc($report['title']) ?></h5>
                                        <span class="risk-badge risk-<?= esc($report['risk_level']) ?>">
                                            <?= strtoupper(esc($report['risk_level'])) ?> RISK
                                        </span>
                                    </div>
                                    <small class="text-muted"><?= date('M d, Y', strtotime($report['created_at'])) ?></small>
                                </div>
                                <p><?= nl2br(esc($report['description'])) ?></p>
                                
                                <?php if (!empty($report['verification_notes'])): ?>
                                    <div class="alert alert-info mt-2">
                                        <strong>Verification Notes:</strong> <?= nl2br(esc($report['verification_notes'])) ?>
                                    </div>
                                <?php endif; ?>
                                
                                <small class="text-muted">
                                    Reported by <?= esc($report['username'] ?? 'Anonymous') ?>
                                </small>
                            </div>
                        <?php endforeach; ?>
                        
                        <!-- Pagination -->
                        <?php if ($scamReports['pagination']['total_pages'] > 1): ?>
                            <nav>
                                <ul class="pagination justify-content-center">
                                    <?php for ($i = 1; $i <= $scamReports['pagination']['total_pages']; $i++): ?>
                                        <li class="page-item <?= $i == $scamReports['pagination']['current_page'] ? 'active' : '' ?>">
                                            <a class="page-link" href="?scam_page=<?= $i ?>"><?= $i ?></a>
                                        </li>
                                    <?php endfor; ?>
                                </ul>
                            </nav>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </div>

    <!-- Similar Apps - Full Width -->
    <?php if (!empty($similarApps)): ?>
    <div class="container sim-section-wrap">
        <div class="sim-section">
            <div class="sim-header">
                <i class="bi bi-diagram-3"></i>
                <h3>Similar Apps</h3>
            </div>
            <div class="sim-list">
                <?php 
                $iconColors = ['#2563EB','#7C3AED','#10B981','#F59E0B','#EF4444','#EC4899','#0EA5E9','#00C4A1'];
                $simIdx = 0;
                foreach ($similarApps as $similarApp): 
                    $similarTrustScore = (float)$similarApp['trust_score'];
                    $simColor = $iconColors[$simIdx % count($iconColors)];
                    $simInitial = strtoupper(substr($similarApp['name'] ?? 'A', 0, 1));
                    $simIdx++;
                    $simMeta = $similarTrustScore >= 80 ? ['color' => '#10B981', 'bg' => '#D1FAE5', 'label' => 'Great'] : ($similarTrustScore >= 50 ? ['color' => '#F59E0B', 'bg' => '#FEF3C7', 'label' => 'Fair'] : ['color' => '#EF4444', 'bg' => '#FEE2E2', 'label' => 'Low']);
                ?>
                    <a href="<?= base_url('apps/' . esc($similarApp['slug'])) ?>" class="sim-card">
                        <div class="sim-icon" style="background:<?= $simColor ?>;">
                            <?php if (!empty($similarApp['thumbnail'])): ?>
                                <img src="<?= base_url('uploads/thumbnails/' . esc($similarApp['thumbnail'])) ?>" alt="<?= esc($similarApp['name']) ?>">
                            <?php else: ?>
                                <?= esc($simInitial) ?>
                            <?php endif; ?>
                        </div>
                        <div class="sim-info">
                            <div class="sim-name"><?= esc($similarApp['name']) ?></div>
                            <div class="sim-cat"><?= esc($similarApp['category_name'] ?? 'App') ?></div>
                        </div>
                        <div class="sim-score" style="background:<?= $simMeta['bg'] ?>;color:<?= $simMeta['color'] ?>;">
                            <?= number_format($similarTrustScore, 0) ?>
                        </div>
                        <i class="bi bi-chevron-right sim-arrow"></i>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Screenshot Modal -->
    <div class="modal fade" id="screenshotModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Screenshot</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center">
                    <img src="" id="modalScreenshot" class="modal-screenshot" alt="Screenshot">
                </div>
            </div>
        </div>
    </div>

    <script>
        // Screenshot modal handler
        const screenshotModal = document.getElementById('screenshotModal');
        if (screenshotModal) {
            screenshotModal.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                const screenshotUrl = button.getAttribute('data-screenshot');
                const modalImg = document.getElementById('modalScreenshot');
                modalImg.src = screenshotUrl;
            });
        }
        
        // Character counter for review text
        const reviewTextarea = document.getElementById('review_text');
        const charCount = document.getElementById('charCount');
        
        if (reviewTextarea && charCount) {
            // Update count on page load
            charCount.textContent = reviewTextarea.value.length;
            
            // Update count on input
            reviewTextarea.addEventListener('input', function() {
                charCount.textContent = this.value.length;
                
                // Change color based on length
                if (this.value.length < 50) {
                    charCount.style.color = '#dc3545'; // Red
                } else if (this.value.length > 1900) {
                    charCount.style.color = '#ffc107'; // Yellow
                } else {
                    charCount.style.color = '#198754'; // Green
                }
            });
        }
        
        // Character counter for scam report description
        const scamTextarea = document.getElementById('scam_description');
        const scamCharCount = document.getElementById('scamCharCount');
        
        if (scamTextarea && scamCharCount) {
            // Update count on page load
            scamCharCount.textContent = scamTextarea.value.length;
            
            // Update count on input
            scamTextarea.addEventListener('input', function() {
                scamCharCount.textContent = this.value.length;
                
                // Change color based on length
                if (this.value.length < 100) {
                    scamCharCount.style.color = '#dc3545'; // Red
                } else if (this.value.length > 2800) {
                    scamCharCount.style.color = '#ffc107'; // Yellow
                } else {
                    scamCharCount.style.color = '#198754'; // Green
                }
            });
        }
    </script>

<?= $this->endSection() ?>
