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
        .settings-section {
            margin-bottom: 2rem;
        }
        .weight-input {
            max-width: 150px;
        }
        .weight-sum {
            font-size: 1.2rem;
            font-weight: bold;
        }
        .weight-sum.valid {
            color: #198754;
        }
        .weight-sum.invalid {
            color: #dc3545;
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
                            <a class="nav-link" href="<?= base_url('admin/reviews') ?>">
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
                            <a class="nav-link active" href="<?= base_url('admin/settings') ?>">
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
                </div>

                <!-- Success/Error Messages -->
                <?php if (session()->getFlashdata('success')): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle"></i> <?= session()->getFlashdata('success') ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-triangle"></i> <?= session()->getFlashdata('error') ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <!-- Trust Algorithm Weights Section -->
                <div class="settings-section">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">
                                <i class="bi bi-calculator"></i> Trust Algorithm Component Weights
                            </h5>
                        </div>
                        <div class="card-body">
                            <p class="text-muted">
                                Configure the weight of each component in the trust score calculation. 
                                All weights must sum to exactly 100.
                            </p>

                            <form action="<?= base_url('admin/settings/update') ?>" method="post" id="trustAlgorithmForm">
                                <?= csrf_field() ?>
                                <input type="hidden" name="setting_type" value="trust_algorithm">

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="review_rating" class="form-label">
                                                <i class="bi bi-star-fill text-warning"></i> User Review Ratings
                                            </label>
                                            <div class="input-group weight-input">
                                                <input type="number" 
                                                       class="form-control weight-field" 
                                                       id="review_rating" 
                                                       name="review_rating" 
                                                       value="<?= esc($trustAlgorithmWeights['review_rating']) ?>" 
                                                       min="0" 
                                                       max="100" 
                                                       step="1"
                                                       required>
                                                <span class="input-group-text">%</span>
                                            </div>
                                            <small class="form-text text-muted">
                                                Weight for average user review ratings
                                            </small>
                                        </div>

                                        <div class="mb-3">
                                            <label for="security_score" class="form-label">
                                                <i class="bi bi-shield-check text-success"></i> Security Score
                                            </label>
                                            <div class="input-group weight-input">
                                                <input type="number" 
                                                       class="form-control weight-field" 
                                                       id="security_score" 
                                                       name="security_score" 
                                                       value="<?= esc($trustAlgorithmWeights['security_score']) ?>" 
                                                       min="0" 
                                                       max="100" 
                                                       step="1"
                                                       required>
                                                <span class="input-group-text">%</span>
                                            </div>
                                            <small class="form-text text-muted">
                                                Weight for security analysis score
                                            </small>
                                        </div>

                                        <div class="mb-3">
                                            <label for="developer_reputation" class="form-label">
                                                <i class="bi bi-person-badge text-info"></i> Developer Reputation
                                            </label>
                                            <div class="input-group weight-input">
                                                <input type="number" 
                                                       class="form-control weight-field" 
                                                       id="developer_reputation" 
                                                       name="developer_reputation" 
                                                       value="<?= esc($trustAlgorithmWeights['developer_reputation']) ?>" 
                                                       min="0" 
                                                       max="100" 
                                                       step="1"
                                                       required>
                                                <span class="input-group-text">%</span>
                                            </div>
                                            <small class="form-text text-muted">
                                                Weight for developer reputation score
                                            </small>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="scam_report_count" class="form-label">
                                                <i class="bi bi-exclamation-triangle text-danger"></i> Scam Report Count
                                            </label>
                                            <div class="input-group weight-input">
                                                <input type="number" 
                                                       class="form-control weight-field" 
                                                       id="scam_report_count" 
                                                       name="scam_report_count" 
                                                       value="<?= esc($trustAlgorithmWeights['scam_report_count']) ?>" 
                                                       min="0" 
                                                       max="100" 
                                                       step="1"
                                                       required>
                                                <span class="input-group-text">%</span>
                                            </div>
                                            <small class="form-text text-muted">
                                                Weight for scam report count impact
                                            </small>
                                        </div>

                                        <div class="mb-3">
                                            <label for="app_age" class="form-label">
                                                <i class="bi bi-calendar-check text-secondary"></i> App Age
                                            </label>
                                            <div class="input-group weight-input">
                                                <input type="number" 
                                                       class="form-control weight-field" 
                                                       id="app_age" 
                                                       name="app_age" 
                                                       value="<?= esc($trustAlgorithmWeights['app_age']) ?>" 
                                                       min="0" 
                                                       max="100" 
                                                       step="1"
                                                       required>
                                                <span class="input-group-text">%</span>
                                            </div>
                                            <small class="form-text text-muted">
                                                Weight for app age factor
                                            </small>
                                        </div>

                                        <div class="alert alert-info">
                                            <strong>Total Weight:</strong>
                                            <span class="weight-sum" id="weightSum">100</span>%
                                            <div class="mt-2">
                                                <small id="weightSumMessage" class="text-muted">
                                                    Weights must sum to exactly 100%
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-end">
                                    <button type="submit" class="btn btn-primary" id="saveTrustAlgorithmBtn">
                                        <i class="bi bi-save"></i> Save Trust Algorithm Settings
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Email Configuration Section -->
                <div class="settings-section">
                    <div class="card">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0">
                                <i class="bi bi-envelope"></i> Email Configuration
                            </h5>
                        </div>
                        <div class="card-body">
                            <p class="text-muted">
                                Configure the sender information for email notifications sent by the platform.
                            </p>

                            <form action="<?= base_url('admin/settings/update') ?>" method="post">
                                <?= csrf_field() ?>
                                <input type="hidden" name="setting_type" value="email">

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="sender_name" class="form-label">Sender Name</label>
                                            <input type="text" 
                                                   class="form-control" 
                                                   id="sender_name" 
                                                   name="sender_name" 
                                                   value="<?= esc($emailSettings['sender_name']) ?>" 
                                                   maxlength="255"
                                                   required>
                                            <small class="form-text text-muted">
                                                The name that appears in the "From" field of emails
                                            </small>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="sender_email" class="form-label">Sender Email Address</label>
                                            <input type="email" 
                                                   class="form-control" 
                                                   id="sender_email" 
                                                   name="sender_email" 
                                                   value="<?= esc($emailSettings['sender_email']) ?>" 
                                                   maxlength="255"
                                                   required>
                                            <small class="form-text text-muted">
                                                The email address that appears in the "From" field
                                            </small>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-end">
                                    <button type="submit" class="btn btn-success">
                                        <i class="bi bi-save"></i> Save Email Settings
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Pagination Limits Section -->
                <div class="settings-section">
                    <div class="card">
                        <div class="card-header bg-warning text-dark">
                            <h5 class="mb-0">
                                <i class="bi bi-list-ol"></i> Pagination Limits
                            </h5>
                        </div>
                        <div class="card-body">
                            <p class="text-muted">
                                Configure the number of items displayed per page in various sections of the platform.
                            </p>

                            <form action="<?= base_url('admin/settings/update') ?>" method="post">
                                <?= csrf_field() ?>
                                <input type="hidden" name="setting_type" value="pagination">

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="search_results" class="form-label">
                                                <i class="bi bi-search"></i> Search Results
                                            </label>
                                            <input type="number" 
                                                   class="form-control" 
                                                   id="search_results" 
                                                   name="search_results" 
                                                   value="<?= esc($paginationSettings['search_results']) ?>" 
                                                   min="1" 
                                                   max="100"
                                                   required>
                                            <small class="form-text text-muted">
                                                Items per page for search results (1-100)
                                            </small>
                                        </div>

                                        <div class="mb-3">
                                            <label for="category_pages" class="form-label">
                                                <i class="bi bi-grid"></i> Category Pages
                                            </label>
                                            <input type="number" 
                                                   class="form-control" 
                                                   id="category_pages" 
                                                   name="category_pages" 
                                                   value="<?= esc($paginationSettings['category_pages']) ?>" 
                                                   min="1" 
                                                   max="100"
                                                   required>
                                            <small class="form-text text-muted">
                                                Items per page for category listings (1-100)
                                            </small>
                                        </div>

                                        <div class="mb-3">
                                            <label for="blog_listings" class="form-label">
                                                <i class="bi bi-newspaper"></i> Blog Listings
                                            </label>
                                            <input type="number" 
                                                   class="form-control" 
                                                   id="blog_listings" 
                                                   name="blog_listings" 
                                                   value="<?= esc($paginationSettings['blog_listings']) ?>" 
                                                   min="1" 
                                                   max="100"
                                                   required>
                                            <small class="form-text text-muted">
                                                Items per page for blog post listings (1-100)
                                            </small>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="reviews_per_page" class="form-label">
                                                <i class="bi bi-star"></i> Reviews Per Page
                                            </label>
                                            <input type="number" 
                                                   class="form-control" 
                                                   id="reviews_per_page" 
                                                   name="reviews_per_page" 
                                                   value="<?= esc($paginationSettings['reviews_per_page']) ?>" 
                                                   min="1" 
                                                   max="100"
                                                   required>
                                            <small class="form-text text-muted">
                                                Reviews displayed per page (1-100)
                                            </small>
                                        </div>

                                        <div class="mb-3">
                                            <label for="scam_reports_per_page" class="form-label">
                                                <i class="bi bi-exclamation-triangle"></i> Scam Reports Per Page
                                            </label>
                                            <input type="number" 
                                                   class="form-control" 
                                                   id="scam_reports_per_page" 
                                                   name="scam_reports_per_page" 
                                                   value="<?= esc($paginationSettings['scam_reports_per_page']) ?>" 
                                                   min="1" 
                                                   max="100"
                                                   required>
                                            <small class="form-text text-muted">
                                                Scam reports displayed per page (1-100)
                                            </small>
                                        </div>

                                        <div class="alert alert-info">
                                            <i class="bi bi-info-circle"></i>
                                            <strong>Note:</strong> Changes to pagination limits take effect immediately.
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-end">
                                    <button type="submit" class="btn btn-warning">
                                        <i class="bi bi-save"></i> Save Pagination Settings
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Information Section -->
                <div class="settings-section">
                    <div class="card border-info">
                        <div class="card-body">
                            <h5 class="card-title">
                                <i class="bi bi-info-circle text-info"></i> Important Information
                            </h5>
                            <ul class="mb-0">
                                <li>Trust algorithm weight changes will apply to all trust score calculations within 60 seconds due to cache invalidation.</li>
                                <li>Email configuration changes take effect immediately for all new email notifications.</li>
                                <li>Pagination limit changes are applied instantly across the platform.</li>
                                <li>All settings are validated before saving to ensure data integrity.</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Real-time weight sum calculation
        document.addEventListener('DOMContentLoaded', function() {
            const weightFields = document.querySelectorAll('.weight-field');
            const weightSumDisplay = document.getElementById('weightSum');
            const weightSumMessage = document.getElementById('weightSumMessage');
            const saveButton = document.getElementById('saveTrustAlgorithmBtn');

            function calculateWeightSum() {
                let sum = 0;
                weightFields.forEach(field => {
                    sum += parseFloat(field.value) || 0;
                });

                weightSumDisplay.textContent = sum.toFixed(0);

                if (Math.abs(sum - 100) < 0.01) {
                    weightSumDisplay.classList.remove('invalid');
                    weightSumDisplay.classList.add('valid');
                    weightSumMessage.textContent = 'Perfect! Weights sum to 100%';
                    weightSumMessage.className = 'text-success';
                    saveButton.disabled = false;
                } else {
                    weightSumDisplay.classList.remove('valid');
                    weightSumDisplay.classList.add('invalid');
                    weightSumMessage.textContent = 'Warning: Weights must sum to exactly 100%';
                    weightSumMessage.className = 'text-danger';
                    saveButton.disabled = true;
                }
            }

            weightFields.forEach(field => {
                field.addEventListener('input', calculateWeightSum);
            });

            // Initial calculation
            calculateWeightSum();
        });
    </script>
</body>
</html>
