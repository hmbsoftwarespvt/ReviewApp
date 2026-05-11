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
        .report-card {
            transition: transform 0.2s;
        }
        .report-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .filter-section {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1.5rem;
        }
        .risk-badge-high {
            background-color: #dc3545;
        }
        .risk-badge-medium {
            background-color: #fd7e14;
        }
        .risk-badge-low {
            background-color: #ffc107;
        }
        .evidence-link {
            font-size: 0.875rem;
            word-break: break-all;
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
                            <a class="nav-link active" href="<?= base_url('admin/scam-reports') ?>">
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
                    <form method="GET" action="<?= base_url('admin/scam-reports') ?>" class="row g-3">
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
                            <label for="risk_level" class="form-label">Risk Level</label>
                            <select name="risk_level" id="risk_level" class="form-select">
                                <option value="">All Levels</option>
                                <option value="high" <?= $filters['risk_level'] === 'high' ? 'selected' : '' ?>>High</option>
                                <option value="medium" <?= $filters['risk_level'] === 'medium' ? 'selected' : '' ?>>Medium</option>
                                <option value="low" <?= $filters['risk_level'] === 'low' ? 'selected' : '' ?>>Low</option>
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

                <!-- Scam Reports List -->
                <?php if (empty($reports)): ?>
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> No scam reports found matching the selected filters.
                    </div>
                <?php else: ?>
                    <div class="row">
                        <?php foreach ($reports as $report): ?>
                            <div class="col-12 mb-3">
                                <div class="card report-card">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-8">
                                                <!-- Report Header -->
                                                <div class="d-flex justify-content-between align-items-start mb-2">
                                                    <div>
                                                        <h5 class="card-title mb-1">
                                                            <?= esc($report['title']) ?>
                                                        </h5>
                                                        <div class="mb-2">
                                                            <span class="badge risk-badge-<?= esc($report['risk_level']) ?> me-2">
                                                                <i class="bi bi-exclamation-triangle"></i> <?= ucfirst($report['risk_level']) ?> Risk
                                                            </span>
                                                            <span class="badge bg-<?= $report['approval_status'] === 'pending' ? 'warning' : ($report['approval_status'] === 'approved' ? 'success' : 'danger') ?>">
                                                                <?= ucfirst($report['approval_status']) ?>
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Report Content -->
                                                <p class="card-text mb-2"><?= esc($report['description']) ?></p>

                                                <!-- Evidence URLs -->
                                                <?php if (!empty($report['evidence_urls'])): ?>
                                                    <?php 
                                                    $evidenceUrls = is_string($report['evidence_urls']) 
                                                        ? json_decode($report['evidence_urls'], true) 
                                                        : $report['evidence_urls'];
                                                    ?>
                                                    <?php if (is_array($evidenceUrls) && count($evidenceUrls) > 0): ?>
                                                        <div class="mb-2">
                                                            <strong><i class="bi bi-link-45deg"></i> Evidence URLs:</strong>
                                                            <ul class="list-unstyled ms-3 mt-1">
                                                                <?php foreach ($evidenceUrls as $url): ?>
                                                                    <li class="evidence-link">
                                                                        <a href="<?= esc($url) ?>" target="_blank" rel="noopener noreferrer">
                                                                            <?= esc($url) ?> <i class="bi bi-box-arrow-up-right"></i>
                                                                        </a>
                                                                    </li>
                                                                <?php endforeach; ?>
                                                            </ul>
                                                        </div>
                                                    <?php endif; ?>
                                                <?php endif; ?>

                                                <!-- Verification Notes -->
                                                <?php if (!empty($report['verification_notes'])): ?>
                                                    <div class="mb-2 p-2 bg-light rounded">
                                                        <strong><i class="bi bi-clipboard-check"></i> Verification Notes:</strong>
                                                        <p class="mb-0 mt-1"><?= esc($report['verification_notes']) ?></p>
                                                    </div>
                                                <?php endif; ?>

                                                <!-- Report Meta -->
                                                <div class="text-muted small mt-3">
                                                    <i class="bi bi-app"></i> App: 
                                                    <a href="<?= base_url('apps/' . esc($report['app_slug'])) ?>" target="_blank">
                                                        <?= esc($report['app_name']) ?>
                                                    </a>
                                                    <span class="mx-2">|</span>
                                                    <i class="bi bi-person"></i> User: <?= esc($report['username']) ?> (<?= esc($report['email']) ?>)
                                                    <span class="mx-2">|</span>
                                                    <i class="bi bi-calendar"></i> <?= date('M d, Y H:i', strtotime($report['created_at'])) ?>
                                                </div>
                                            </div>

                                            <!-- Action Buttons -->
                                            <div class="col-md-4 d-flex flex-column justify-content-center">
                                                <div class="d-grid gap-2">
                                                    <?php if ($report['approval_status'] === 'pending'): ?>
                                                        <!-- Verification Notes Input -->
                                                        <div class="mb-2">
                                                            <label for="notes-<?= $report['id'] ?>" class="form-label small">Verification Notes:</label>
                                                            <textarea 
                                                                id="notes-<?= $report['id'] ?>" 
                                                                class="form-control form-control-sm" 
                                                                rows="2" 
                                                                placeholder="Add notes (optional)"
                                                                form="verify-form-<?= $report['id'] ?>"
                                                                name="verification_notes"
                                                            ></textarea>
                                                        </div>

                                                        <!-- Risk Level Update -->
                                                        <div class="mb-2">
                                                            <label for="risk-<?= $report['id'] ?>" class="form-label small">Update Risk Level:</label>
                                                            <select 
                                                                id="risk-<?= $report['id'] ?>" 
                                                                class="form-select form-select-sm"
                                                                form="risk-form-<?= $report['id'] ?>"
                                                                name="risk_level"
                                                            >
                                                                <option value="low" <?= $report['risk_level'] === 'low' ? 'selected' : '' ?>>Low</option>
                                                                <option value="medium" <?= $report['risk_level'] === 'medium' ? 'selected' : '' ?>>Medium</option>
                                                                <option value="high" <?= $report['risk_level'] === 'high' ? 'selected' : '' ?>>High</option>
                                                            </select>
                                                        </div>

                                                        <form id="risk-form-<?= $report['id'] ?>" method="POST" action="<?= base_url('admin/scam-reports/update-risk/' . $report['id']) ?>" class="d-inline">
                                                            <?= csrf_field() ?>
                                                            <button type="submit" class="btn btn-info btn-sm w-100" onclick="return confirm('Update risk level for this report?')">
                                                                <i class="bi bi-arrow-repeat"></i> Update Risk
                                                            </button>
                                                        </form>

                                                        <form id="verify-form-<?= $report['id'] ?>" method="POST" action="<?= base_url('admin/scam-reports/verify/' . $report['id']) ?>" class="d-inline">
                                                            <?= csrf_field() ?>
                                                            <button type="submit" class="btn btn-success w-100" onclick="return confirm('Verify this scam report? Trust score will be recalculated. <?= $report['risk_level'] === 'high' ? 'Email notifications will be sent to subscribers.' : '' ?>')">
                                                                <i class="bi bi-check-circle"></i> Verify
                                                            </button>
                                                        </form>

                                                        <form method="POST" action="<?= base_url('admin/scam-reports/reject/' . $report['id']) ?>" class="d-inline">
                                                            <?= csrf_field() ?>
                                                            <textarea 
                                                                class="form-control form-control-sm mb-2" 
                                                                rows="2" 
                                                                placeholder="Rejection notes (optional)"
                                                                name="verification_notes"
                                                            ></textarea>
                                                            <button type="submit" class="btn btn-warning w-100" onclick="return confirm('Reject this scam report?')">
                                                                <i class="bi bi-x-circle"></i> Reject
                                                            </button>
                                                        </form>
                                                    <?php elseif ($report['approval_status'] === 'approved'): ?>
                                                        <button class="btn btn-success w-100" disabled>
                                                            <i class="bi bi-check-circle"></i> Already Verified
                                                        </button>
                                                        
                                                        <!-- Allow risk level update even after approval -->
                                                        <div class="mb-2">
                                                            <label for="risk-approved-<?= $report['id'] ?>" class="form-label small">Update Risk Level:</label>
                                                            <select 
                                                                id="risk-approved-<?= $report['id'] ?>" 
                                                                class="form-select form-select-sm"
                                                                form="risk-approved-form-<?= $report['id'] ?>"
                                                                name="risk_level"
                                                            >
                                                                <option value="low" <?= $report['risk_level'] === 'low' ? 'selected' : '' ?>>Low</option>
                                                                <option value="medium" <?= $report['risk_level'] === 'medium' ? 'selected' : '' ?>>Medium</option>
                                                                <option value="high" <?= $report['risk_level'] === 'high' ? 'selected' : '' ?>>High</option>
                                                            </select>
                                                        </div>

                                                        <form id="risk-approved-form-<?= $report['id'] ?>" method="POST" action="<?= base_url('admin/scam-reports/update-risk/' . $report['id']) ?>" class="d-inline">
                                                            <?= csrf_field() ?>
                                                            <button type="submit" class="btn btn-info btn-sm w-100" onclick="return confirm('Update risk level for this report?')">
                                                                <i class="bi bi-arrow-repeat"></i> Update Risk
                                                            </button>
                                                        </form>
                                                    <?php else: ?>
                                                        <button class="btn btn-secondary w-100" disabled>
                                                            <i class="bi bi-x-circle"></i> Rejected
                                                        </button>
                                                    <?php endif; ?>
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
                        <nav aria-label="Scam report pagination">
                            <ul class="pagination justify-content-center">
                                <!-- Previous Page -->
                                <li class="page-item <?= $pagination['current_page'] <= 1 ? 'disabled' : '' ?>">
                                    <a class="page-link" href="<?= base_url('admin/scam-reports?' . http_build_query(array_merge($filters, ['page' => $pagination['current_page'] - 1]))) ?>">
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
                                        <a class="page-link" href="<?= base_url('admin/scam-reports?' . http_build_query(array_merge($filters, ['page' => 1]))) ?>">1</a>
                                    </li>
                                    <?php if ($start > 2): ?>
                                        <li class="page-item disabled"><span class="page-link">...</span></li>
                                    <?php endif; ?>
                                <?php endif; ?>

                                <?php for ($i = $start; $i <= $end; $i++): ?>
                                    <li class="page-item <?= $i === $pagination['current_page'] ? 'active' : '' ?>">
                                        <a class="page-link" href="<?= base_url('admin/scam-reports?' . http_build_query(array_merge($filters, ['page' => $i]))) ?>">
                                            <?= $i ?>
                                        </a>
                                    </li>
                                <?php endfor; ?>

                                <?php if ($end < $pagination['total_pages']): ?>
                                    <?php if ($end < $pagination['total_pages'] - 1): ?>
                                        <li class="page-item disabled"><span class="page-link">...</span></li>
                                    <?php endif; ?>
                                    <li class="page-item">
                                        <a class="page-link" href="<?= base_url('admin/scam-reports?' . http_build_query(array_merge($filters, ['page' => $pagination['total_pages']]))) ?>">
                                            <?= $pagination['total_pages'] ?>
                                        </a>
                                    </li>
                                <?php endif; ?>

                                <!-- Next Page -->
                                <li class="page-item <?= $pagination['current_page'] >= $pagination['total_pages'] ? 'disabled' : '' ?>">
                                    <a class="page-link" href="<?= base_url('admin/scam-reports?' . http_build_query(array_merge($filters, ['page' => $pagination['current_page'] + 1]))) ?>">
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
