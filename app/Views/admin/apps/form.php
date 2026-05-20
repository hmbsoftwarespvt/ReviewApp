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
        .screenshot-preview {
            max-width: 150px;
            max-height: 150px;
            object-fit: cover;
            margin: 5px;
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
                            <a class="nav-link active" href="<?= base_url('admin/apps') ?>">
                                <i class="bi bi-app"></i> Apps
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= base_url('admin/categories') ?>">
                                <i class="bi bi-tags"></i> Categories
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
                        <a href="<?= base_url('admin/apps') ?>" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Back to List
                        </a>
                    </div>
                </div>

                <!-- Flash Messages -->
                <?php if (session('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= session('error') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>

                <!-- Validation Errors -->
                <?php if (!empty($errors)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong>Please fix the following errors:</strong>
                    <ul class="mb-0">
                        <?php foreach ($errors as $error): ?>
                        <li><?= esc($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>

                <!-- App Form -->
                <form method="post" action="<?= $app ? base_url('admin/apps/update/' . $app['id']) : base_url('admin/apps/store') ?>" 
                      enctype="multipart/form-data">
                    <?= csrf_field() ?>
                    
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Basic Information</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="name" class="form-label">App Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control <?= isset($errors['name']) ? 'is-invalid' : '' ?>" 
                                           id="name" name="name" required
                                           value="<?= esc($old['name'] ?? $app['name'] ?? '') ?>">
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="slug" class="form-label">Slug</label>
                                    <input type="text" class="form-control <?= isset($errors['slug']) ? 'is-invalid' : '' ?>" 
                                           id="slug" name="slug"
                                           value="<?= esc($old['slug'] ?? $app['slug'] ?? '') ?>">
                                    <small class="text-muted">URL-friendly identifier (auto-generated from name)</small>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="4"><?= esc($old['description'] ?? $app['description'] ?? '') ?></textarea>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="developer_name" class="form-label">Developer Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control <?= isset($errors['developer_name']) ? 'is-invalid' : '' ?>" 
                                           id="developer_name" name="developer_name" required
                                           value="<?= esc($old['developer_name'] ?? $app['developer_name'] ?? '') ?>">
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="platform_type" class="form-label">Platform Type <span class="text-danger">*</span></label>
                                    <select class="form-select <?= isset($errors['platform_type']) ? 'is-invalid' : '' ?>" 
                                            id="platform_type" name="platform_type" required>
                                        <option value="">Select Platform</option>
                                        <?php
                                        $platforms = ['android', 'ios', 'web', 'desktop'];
                                        $selected = $old['platform_type'] ?? $app['platform_type'] ?? '';
                                        foreach ($platforms as $platform):
                                        ?>
                                        <option value="<?= $platform ?>" <?= $selected === $platform ? 'selected' : '' ?>>
                                            <?= ucfirst($platform) ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="version" class="form-label">Version</label>
                                    <input type="text" class="form-control" id="version" name="version"
                                           value="<?= esc($old['version'] ?? $app['version'] ?? '') ?>">
                                </div>
                                
                                <div class="col-md-4 mb-3">
                                    <label for="size" class="form-label">Size</label>
                                    <input type="text" class="form-control" id="size" name="size"
                                           placeholder="e.g., 25 MB"
                                           value="<?= esc($old['size'] ?? $app['size'] ?? '') ?>">
                                </div>
                                
                                <div class="col-md-4 mb-3">
                                    <label for="price" class="form-label">Price</label>
                                    <input type="number" class="form-control" id="price" name="price" 
                                           step="0.01" min="0"
                                           value="<?= esc($old['price'] ?? $app['price'] ?? '0.00') ?>">
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="release_date" class="form-label">Release Date</label>
                                    <input type="date" class="form-control" id="release_date" name="release_date"
                                           value="<?= esc($old['release_date'] ?? $app['release_date'] ?? '') ?>">
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="download_url" class="form-label">Download URL</label>
                                    <input type="url" class="form-control" id="download_url" name="download_url"
                                           placeholder="https://..."
                                           value="<?= esc($old['download_url'] ?? $app['download_url'] ?? '') ?>">
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="youtube_link" class="form-label">YouTube Review Link</label>
                                <input type="url" class="form-control" id="youtube_link" name="youtube_link"
                                       placeholder="https://www.youtube.com/watch?v=..."
                                       value="<?= esc($old['youtube_link'] ?? $app['youtube_link'] ?? '') ?>">
                                <small class="text-muted">Link to YouTube review video for this app</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Categories</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <?php foreach ($categories as $category): ?>
                                <div class="col-md-4 mb-2">
                                    <div class="form-check">
                                        <?php
                                        $isChecked = false;
                                        if ($app && isset($selectedCategories)) {
                                            $isChecked = in_array($category['id'], $selectedCategories);
                                        }
                                        ?>
                                        <input class="form-check-input" type="checkbox" 
                                               name="categories[]" value="<?= $category['id'] ?>" 
                                               id="category_<?= $category['id'] ?>"
                                               <?= $isChecked ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="category_<?= $category['id'] ?>">
                                            <?= esc($category['name']) ?>
                                        </label>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Security Information</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="permissions" class="form-label">Permissions</label>
                                <input type="text" class="form-control" id="permissions" name="permissions"
                                       placeholder="e.g., camera, location, contacts (comma-separated)"
                                       value="<?= esc($old['permissions'] ?? $app['permissions'] ?? '') ?>">
                                <small class="text-muted">Enter permissions as comma-separated values</small>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <div class="form-check">
                                        <?php $hasEncryption = $old['has_encryption'] ?? $app['has_encryption'] ?? 0; ?>
                                        <input class="form-check-input" type="checkbox" 
                                               name="has_encryption" value="1" id="has_encryption"
                                               <?= $hasEncryption ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="has_encryption">
                                            Uses Encryption
                                        </label>
                                    </div>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="third_party_sdk_count" class="form-label">Third-Party SDK Count</label>
                                    <input type="number" class="form-control" id="third_party_sdk_count" 
                                           name="third_party_sdk_count" min="0"
                                           value="<?= esc($old['third_party_sdk_count'] ?? $app['third_party_sdk_count'] ?? '0') ?>">
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Screenshots (Max 10)</h5>
                        </div>
                        <div class="card-body">
                            <?php if ($app && !empty($app['screenshots'])): ?>
                            <div class="mb-3">
                                <label class="form-label">Current Screenshots</label>
                                <div class="d-flex flex-wrap">
                                    <?php foreach ($app['screenshots'] as $screenshot): ?>
                                    <div class="position-relative m-2">
                                        <img src="<?= base_url('writable/' . esc($screenshot['file_path'])) ?>" 
                                             class="screenshot-preview border" alt="Screenshot">
                                        <div class="form-check position-absolute top-0 end-0 m-1 bg-white rounded">
                                            <input class="form-check-input" type="checkbox" 
                                                   name="delete_screenshots[]" value="<?= $screenshot['id'] ?>" 
                                                   id="delete_<?= $screenshot['id'] ?>">
                                            <label class="form-check-label small" for="delete_<?= $screenshot['id'] ?>">
                                                Delete
                                            </label>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <?php endif; ?>
                            
                            <div class="mb-3">
                                <label for="screenshots" class="form-label">Upload New Screenshots</label>
                                <input type="file" class="form-control" id="screenshots" name="screenshots[]" 
                                       accept="image/*" multiple>
                                <small class="text-muted">
                                    You can upload up to <?= $app ? (10 - count($app['screenshots'] ?? [])) : 10 ?> more screenshots. 
                                    Max file size: 2MB per image.
                                </small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Approval Status</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="approval_status" class="form-label">Status</label>
                                <select class="form-select" id="approval_status" name="approval_status">
                                    <?php
                                    $statuses = ['pending', 'approved', 'rejected'];
                                    $selected = $old['approval_status'] ?? $app['approval_status'] ?? 'pending';
                                    foreach ($statuses as $status):
                                    ?>
                                    <option value="<?= $status ?>" <?= $selected === $status ? 'selected' : '' ?>>
                                        <?= ucfirst($status) ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="bi bi-save"></i> <?= $app ? 'Update App' : 'Create App' ?>
                        </button>
                        <a href="<?= base_url('admin/apps') ?>" class="btn btn-secondary btn-lg">
                            <i class="bi bi-x-circle"></i> Cancel
                        </a>
                    </div>
                </form>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto-generate slug from name
        document.getElementById('name').addEventListener('input', function() {
            const slug = this.value
                .toLowerCase()
                .replace(/[^a-z0-9]+/g, '-')
                .replace(/^-+|-+$/g, '');
            document.getElementById('slug').value = slug;
        });
    </script>
</body>
</html>
