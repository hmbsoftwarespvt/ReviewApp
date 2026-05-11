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
        .badge-draft {
            background-color: #6c757d;
        }
        .badge-published {
            background-color: #198754;
        }
        .post-excerpt {
            max-width: 400px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
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
                            <a class="nav-link active" href="<?= base_url('admin/blog') ?>">
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
                        <a href="<?= base_url('admin/blog/create') ?>" class="btn btn-primary">
                            <i class="bi bi-plus-circle"></i> Create Blog Post
                        </a>
                    </div>
                </div>

                <!-- Flash Messages -->
                <?php if (session('success')): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= session('success') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>

                <?php if (session('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= session('error') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>

                <!-- Filters -->
                <div class="card mb-4">
                    <div class="card-body">
                        <form method="get" action="<?= base_url('admin/blog') ?>" class="row g-3">
                            <div class="col-md-4">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="">All Statuses</option>
                                    <option value="draft" <?= $status === 'draft' ? 'selected' : '' ?>>Draft</option>
                                    <option value="published" <?= $status === 'published' ? 'selected' : '' ?>>Published</option>
                                </select>
                            </div>
                            
                            <div class="col-md-4">
                                <label for="category" class="form-label">Category</label>
                                <select class="form-select" id="category" name="category">
                                    <option value="">All Categories</option>
                                    <?php foreach ($categories as $key => $label): ?>
                                    <option value="<?= $key ?>" <?= $category === $key ? 'selected' : '' ?>>
                                        <?= esc($label) ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="col-md-4 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary me-2">
                                    <i class="bi bi-funnel"></i> Filter
                                </button>
                                <a href="<?= base_url('admin/blog') ?>" class="btn btn-secondary">
                                    <i class="bi bi-x-circle"></i> Clear
                                </a>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Blog Posts Table -->
                <div class="card">
                    <div class="card-body">
                        <?php if (empty($posts)): ?>
                        <div class="text-center py-5">
                            <i class="bi bi-newspaper" style="font-size: 3rem; color: #ccc;"></i>
                            <p class="text-muted mt-3">No blog posts found.</p>
                            <a href="<?= base_url('admin/blog/create') ?>" class="btn btn-primary">
                                <i class="bi bi-plus-circle"></i> Create First Blog Post
                            </a>
                        </div>
                        <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Title</th>
                                        <th>Category</th>
                                        <th>Author</th>
                                        <th>Status</th>
                                        <th>Views</th>
                                        <th>Updated</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($posts as $post): ?>
                                    <tr>
                                        <td>
                                            <strong><?= esc($post['title']) ?></strong>
                                            <?php if (!empty($post['excerpt'])): ?>
                                            <br>
                                            <small class="text-muted post-excerpt"><?= esc($post['excerpt']) ?></small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">
                                                <?= esc($categories[$post['category']] ?? $post['category']) ?>
                                            </span>
                                        </td>
                                        <td><?= esc($post['author_name']) ?></td>
                                        <td>
                                            <span class="badge badge-<?= $post['publication_status'] ?>">
                                                <?= ucfirst($post['publication_status']) ?>
                                            </span>
                                        </td>
                                        <td><?= number_format($post['view_count']) ?></td>
                                        <td>
                                            <small><?= date('M d, Y', strtotime($post['updated_at'])) ?></small>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm" role="group">
                                                <a href="<?= base_url('admin/blog/edit/' . $post['id']) ?>" 
                                                   class="btn btn-outline-primary" title="Edit">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                
                                                <?php if ($post['publication_status'] === 'draft'): ?>
                                                <a href="<?= base_url('admin/blog/publish/' . $post['id']) ?>" 
                                                   class="btn btn-outline-success" title="Publish"
                                                   onclick="return confirm('Publish this blog post?')">
                                                    <i class="bi bi-check-circle"></i>
                                                </a>
                                                <?php else: ?>
                                                <a href="<?= base_url('admin/blog/unpublish/' . $post['id']) ?>" 
                                                   class="btn btn-outline-warning" title="Unpublish"
                                                   onclick="return confirm('Set this blog post to draft?')">
                                                    <i class="bi bi-dash-circle"></i>
                                                </a>
                                                <?php endif; ?>
                                                
                                                <a href="<?= base_url('admin/blog/delete/' . $post['id']) ?>" 
                                                   class="btn btn-outline-danger" title="Delete"
                                                   onclick="return confirm('Are you sure you want to delete this blog post?')">
                                                    <i class="bi bi-trash"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <?php if ($pagination['total_pages'] > 1): ?>
                        <nav aria-label="Blog posts pagination" class="mt-4">
                            <ul class="pagination justify-content-center">
                                <li class="page-item <?= !$pagination['has_previous'] ? 'disabled' : '' ?>">
                                    <a class="page-link" href="<?= base_url('admin/blog?page=' . ($pagination['current_page'] - 1) . ($status ? '&status=' . $status : '') . ($category ? '&category=' . $category : '')) ?>">
                                        Previous
                                    </a>
                                </li>
                                
                                <?php for ($i = 1; $i <= $pagination['total_pages']; $i++): ?>
                                <li class="page-item <?= $i === $pagination['current_page'] ? 'active' : '' ?>">
                                    <a class="page-link" href="<?= base_url('admin/blog?page=' . $i . ($status ? '&status=' . $status : '') . ($category ? '&category=' . $category : '')) ?>">
                                        <?= $i ?>
                                    </a>
                                </li>
                                <?php endfor; ?>
                                
                                <li class="page-item <?= !$pagination['has_next'] ? 'disabled' : '' ?>">
                                    <a class="page-link" href="<?= base_url('admin/blog?page=' . ($pagination['current_page'] + 1) . ($status ? '&status=' . $status : '') . ($category ? '&category=' . $category : '')) ?>">
                                        Next
                                    </a>
                                </li>
                            </ul>
                        </nav>
                        <?php endif; ?>

                        <div class="text-muted small text-center mt-3">
                            Showing <?= count($posts) ?> of <?= $pagination['total_items'] ?> blog posts
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
