<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title) ?> - AppTrust Platform</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        .sidebar {
            min-height: 100vh;
            background-color: #212529;
        }
        .sidebar .nav-link {
            color: #adb5bd;
        }
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            color: #fff;
            background-color: #495057;
        }
        .review-card {
            transition: transform 0.2s;
        }
        .review-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .rating-stars {
            color: #ffc107;
        }
        .filter-section {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1.5rem;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-2 d-md-block sidebar">
                <div class="position-sticky pt-3">
                    <h5 class="text-white px-3 mb-3">AppTrust Admin</h5>
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link" href="<?= base_url('admin/dashboard') ?>">
                                <i class="bi bi-speedometer2"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= base_url('admin/apps') ?>">
                                <i class="bi bi-app"></i> Apps
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="<?= base_url('admin/reviews') ?>">
                                <i class="bi bi-star"></i> Reviews
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= base_url('admin/scam-reports') ?>">
                                <i class="bi bi-exclamation-triangle"></i> Scam Reports
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= base_url('admin/users') ?>">
                                <i class="bi bi-people"></i> Users
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= base_url('admin/blog') ?>">
                                <i class="bi bi-newspaper"></i> Blog
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= base_url('admin/settings') ?>">
                                <i class="bi bi-gear"></i> Settings
                            </a>
                        </li>
                        <li class="nav-item mt-3">
                            <a class="nav-link" href="<?= base_url('/') ?>">
                                <i class="bi bi-house"></i> View Site
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= base_url('auth/logout') ?>">
                                <i class="bi bi-box-arrow-right"></i> Logout
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- Main content -->
            <main class="col-md-10 ms-sm-auto px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2"><?= esc($title) ?></h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <span class="badge bg-primary me-2">Total: <?= number_format($pagination['total']) ?></span>
                    </div>
                </div>

                <!-- Flash Messages -->
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

                <!-- Filter Section -->
                <div class="filter-section">
                    <form method="GET" action="<?= base_url('admin/reviews') ?>" class="row g-3">
                        <div class="col-md-3">
                            <label for="status" class="form-label">Status</label>
                            <select name="status" id="status" class="form-select">
                                <option value="">All Statuses</option>
                                <option value="pending" <?= $filters['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                                <option value="approved" <?= $filters['status'] === 'approved' ? 'selected' : '' ?>>Approved</option>
                                <option value="rejected" <?= $filters['status'] === 'rejected' ? 'selected' : '' ?>>Rejected</option>
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label for="rating" class="form-label">Rating</label>
                            <select name="rating" id="rating" class="form-select">
                                <option value="">All Ratings</option>
                                <option value="5" <?= $filters['rating'] == 5 ? 'selected' : '' ?>>5 Stars</option>
                                <option value="4" <?= $filters['rating'] == 4 ? 'selected' : '' ?>>4 Stars</option>
                                <option value="3" <?= $filters['rating'] == 3 ? 'selected' : '' ?>>3 Stars</option>
                                <option value="2" <?= $filters['rating'] == 2 ? 'selected' : '' ?>>2 Stars</option>
                                <option value="1" <?= $filters['rating'] == 1 ? 'selected' : '' ?>>1 Star</option>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label for="date_from" class="form-label">Date From</label>
                            <input type="date" name="date_from" id="date_from" class="form-control" value="<?= esc($filters['date_from']) ?>">
                        </div>

                        <div class="col-md-3">
                            <label for="date_to" class="form-label">Date To</label>
                            <input type="date" name="date_to" id="date_to" class="form-control" value="<?= esc($filters['date_to']) ?>">
                        </div>

                        <div class="col-md-1 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-funnel"></i> Filter
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Reviews List -->
                <?php if (empty($reviews)): ?>
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> No reviews found matching the selected filters.
                    </div>
                <?php else: ?>
                    <div class="row">
                        <?php foreach ($reviews as $review): ?>
                            <div class="col-12 mb-3">
                                <div class="card review-card">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-8">
                                                <!-- Review Header -->
                                                <div class="d-flex justify-content-between align-items-start mb-2">
                                                    <div>
                                                        <h5 class="card-title mb-1">
                                                            <?= esc($review['title']) ?>
                                                        </h5>
                                                        <div class="rating-stars mb-2">
                                                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                                                <i class="bi bi-star<?= $i <= $review['rating'] ? '-fill' : '' ?>"></i>
                                                            <?php endfor; ?>
                                                            <span class="text-muted ms-2"><?= $review['rating'] ?>/5</span>
                                                        </div>
                                                    </div>
                                                    <span class="badge bg-<?= $review['approval_status'] === 'pending' ? 'warning' : ($review['approval_status'] === 'approved' ? 'success' : 'danger') ?>">
                                                        <?= ucfirst($review['approval_status']) ?>
                                                    </span>
                                                </div>

                                                <!-- Review Content -->
                                                <p class="card-text mb-2"><?= esc($review['review_text']) ?></p>

                                                <?php if (!empty($review['pros'])): ?>
                                                    <div class="mb-2">
                                                        <strong class="text-success"><i class="bi bi-plus-circle"></i> Pros:</strong>
                                                        <p class="mb-0"><?= esc($review['pros']) ?></p>
                                                    </div>
                                                <?php endif; ?>

                                                <?php if (!empty($review['cons'])): ?>
                                                    <div class="mb-2">
                                                        <strong class="text-danger"><i class="bi bi-dash-circle"></i> Cons:</strong>
                                                        <p class="mb-0"><?= esc($review['cons']) ?></p>
                                                    </div>
                                                <?php endif; ?>

                                                <!-- Review Meta -->
                                                <div class="text-muted small mt-3">
                                                    <i class="bi bi-app"></i> App: 
                                                    <a href="<?= base_url('app/' . esc($review['app_slug'])) ?>" target="_blank">
                                                        <?= esc($review['app_name']) ?>
                                                    </a>
                                                    <span class="mx-2">|</span>
                                                    <i class="bi bi-person"></i> User: <?= esc($review['username']) ?> (<?= esc($review['email']) ?>)
                                                    <span class="mx-2">|</span>
                                                    <i class="bi bi-calendar"></i> <?= date('M d, Y H:i', strtotime($review['created_at'])) ?>
                                                    <span class="mx-2">|</span>
                                                    <i class="bi bi-hand-thumbs-up"></i> <?= number_format($review['helpful_count']) ?> helpful
                                                </div>
                                            </div>

                                            <!-- Action Buttons -->
                                            <div class="col-md-4 d-flex flex-column justify-content-center">
                                                <div class="d-grid gap-2">
                                                    <?php if ($review['approval_status'] === 'pending'): ?>
                                                        <form method="POST" action="<?= base_url('admin/reviews/approve/' . $review['id']) ?>" class="d-inline">
                                                            <?= csrf_field() ?>
                                                            <button type="submit" class="btn btn-success w-100" onclick="return confirm('Approve this review? Trust score will be recalculated.')">
                                                                <i class="bi bi-check-circle"></i> Approve
                                                            </button>
                                                        </form>

                                                        <form method="POST" action="<?= base_url('admin/reviews/reject/' . $review['id']) ?>" class="d-inline">
                                                            <?= csrf_field() ?>
                                                            <button type="submit" class="btn btn-warning w-100" onclick="return confirm('Reject this review?')">
                                                                <i class="bi bi-x-circle"></i> Reject
                                                            </button>
                                                        </form>
                                                    <?php elseif ($review['approval_status'] === 'approved'): ?>
                                                        <button class="btn btn-success w-100" disabled>
                                                            <i class="bi bi-check-circle"></i> Already Approved
                                                        </button>
                                                    <?php else: ?>
                                                        <button class="btn btn-secondary w-100" disabled>
                                                            <i class="bi bi-x-circle"></i> Rejected
                                                        </button>
                                                    <?php endif; ?>

                                                    <form method="POST" action="<?= base_url('admin/reviews/delete/' . $review['id']) ?>" class="d-inline">
                                                        <?= csrf_field() ?>
                                                        <button type="submit" class="btn btn-danger w-100" onclick="return confirm('Permanently delete this review? This action cannot be undone.')">
                                                            <i class="bi bi-trash"></i> Delete
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Pagination -->
                    <?php if ($pagination['total_pages'] > 1): ?>
                        <nav aria-label="Review pagination">
                            <ul class="pagination justify-content-center">
                                <!-- Previous Page -->
                                <li class="page-item <?= $pagination['current_page'] <= 1 ? 'disabled' : '' ?>">
                                    <a class="page-link" href="<?= base_url('admin/reviews?' . http_build_query(array_merge($filters, ['page' => $pagination['current_page'] - 1]))) ?>">
                                        Previous
                                    </a>
                                </li>

                                <!-- Page Numbers -->
                                <?php
                                $start = max(1, $pagination['current_page'] - 2);
                                $end = min($pagination['total_pages'], $pagination['current_page'] + 2);
                                ?>

                                <?php if ($start > 1): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="<?= base_url('admin/reviews?' . http_build_query(array_merge($filters, ['page' => 1]))) ?>">1</a>
                                    </li>
                                    <?php if ($start > 2): ?>
                                        <li class="page-item disabled"><span class="page-link">...</span></li>
                                    <?php endif; ?>
                                <?php endif; ?>

                                <?php for ($i = $start; $i <= $end; $i++): ?>
                                    <li class="page-item <?= $i === $pagination['current_page'] ? 'active' : '' ?>">
                                        <a class="page-link" href="<?= base_url('admin/reviews?' . http_build_query(array_merge($filters, ['page' => $i]))) ?>">
                                            <?= $i ?>
                                        </a>
                                    </li>
                                <?php endfor; ?>

                                <?php if ($end < $pagination['total_pages']): ?>
                                    <?php if ($end < $pagination['total_pages'] - 1): ?>
                                        <li class="page-item disabled"><span class="page-link">...</span></li>
                                    <?php endif; ?>
                                    <li class="page-item">
                                        <a class="page-link" href="<?= base_url('admin/reviews?' . http_build_query(array_merge($filters, ['page' => $pagination['total_pages']]))) ?>">
                                            <?= $pagination['total_pages'] ?>
                                        </a>
                                    </li>
                                <?php endif; ?>

                                <!-- Next Page -->
                                <li class="page-item <?= $pagination['current_page'] >= $pagination['total_pages'] ? 'disabled' : '' ?>">
                                    <a class="page-link" href="<?= base_url('admin/reviews?' . http_build_query(array_merge($filters, ['page' => $pagination['current_page'] + 1]))) ?>">
                                        Next
                                    </a>
                                </li>
                            </ul>
                        </nav>
                    <?php endif; ?>
                <?php endif; ?>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
