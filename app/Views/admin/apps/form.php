<?= $this->extend('admin/admin_layout') ?>

<?= $this->section('topbar_actions') ?>
<a href="<?= base_url('admin/apps') ?>" class="btn btn-outline-secondary btn-sm">
    <i class="bi bi-arrow-left"></i> Back to List
</a>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<form method="post" action="<?= $app ? base_url('admin/apps/update/' . $app['id']) : base_url('admin/apps/store') ?>" 
      enctype="multipart/form-data">
    <?= csrf_field() ?>
    
    <div class="card mb-4">
        <div class="card-header">Basic Information</div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="name" class="form-label">App Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control <?= isset($errors['name']) ? 'is-invalid' : '' ?>" 
                           id="name" name="name" required
                           value="<?= esc($old['name'] ?? $app['name'] ?? '') ?>">
                </div>
                <div class="col-md-6">
                    <label for="slug" class="form-label">Slug</label>
                    <input type="text" class="form-control <?= isset($errors['slug']) ? 'is-invalid' : '' ?>" 
                           id="slug" name="slug"
                           value="<?= esc($old['slug'] ?? $app['slug'] ?? '') ?>">
                    <small class="text-muted">URL-friendly identifier (auto-generated from name)</small>
                </div>
            </div>
            <div class="mt-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description" rows="4"><?= esc($old['description'] ?? $app['description'] ?? '') ?></textarea>
            </div>
            <div class="row g-3 mt-2">
                <div class="col-md-6">
                    <label for="developer_name" class="form-label">Developer Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control <?= isset($errors['developer_name']) ? 'is-invalid' : '' ?>" 
                           id="developer_name" name="developer_name" required
                           value="<?= esc($old['developer_name'] ?? $app['developer_name'] ?? '') ?>">
                </div>
                <div class="col-md-6">
                    <label for="platform_type" class="form-label">Platform Type <span class="text-danger">*</span></label>
                    <select class="form-select <?= isset($errors['platform_type']) ? 'is-invalid' : '' ?>" 
                            id="platform_type" name="platform_type" required>
                        <option value="">Select Platform</option>
                        <?php
                        $platforms = ['android', 'ios', 'web', 'desktop'];
                        $selected = $old['platform_type'] ?? $app['platform_type'] ?? '';
                        foreach ($platforms as $platform):
                        ?>
                        <option value="<?= $platform ?>" <?= $selected === $platform ? 'selected' : '' ?>><?= ucfirst($platform) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="row g-3 mt-2">
                <div class="col-md-4">
                    <label for="version" class="form-label">Version</label>
                    <input type="text" class="form-control" id="version" name="version"
                           value="<?= esc($old['version'] ?? $app['version'] ?? '') ?>">
                </div>
                <div class="col-md-4">
                    <label for="size" class="form-label">Size</label>
                    <input type="text" class="form-control" id="size" name="size"
                           placeholder="e.g., 25 MB"
                           value="<?= esc($old['size'] ?? $app['size'] ?? '') ?>">
                </div>
                <div class="col-md-4">
                    <label for="price" class="form-label">Price</label>
                    <input type="number" class="form-control" id="price" name="price" 
                           step="0.01" min="0"
                           value="<?= esc($old['price'] ?? $app['price'] ?? '0.00') ?>">
                </div>
            </div>
            <div class="row g-3 mt-2">
                <div class="col-md-6">
                    <label for="release_date" class="form-label">Release Date</label>
                    <input type="date" class="form-control" id="release_date" name="release_date"
                           value="<?= esc($old['release_date'] ?? $app['release_date'] ?? '') ?>">
                </div>
                <div class="col-md-6">
                    <label for="download_url" class="form-label">Download URL</label>
                    <input type="url" class="form-control" id="download_url" name="download_url"
                           placeholder="https://..."
                           value="<?= esc($old['download_url'] ?? $app['download_url'] ?? '') ?>">
                </div>
            </div>
            <div class="mt-3">
                <label for="thumbnail" class="form-label">App Icon / Thumbnail</label>
                <?php if ($app && !empty($app['thumbnail'])): ?>
                <div class="mb-2">
                    <img src="<?= base_url('uploads/thumbnails/' . esc($app['thumbnail'])) ?>"
                         alt="Current thumbnail"
                         style="max-width:80px;max-height:80px;border-radius:12px;object-fit:cover;border:2px solid #e2e8f0;">
                    <div class="text-muted small mt-1">Current icon. Upload a new one to replace it.</div>
                </div>
                <?php endif; ?>
                <input type="file" class="form-control" id="thumbnail" name="thumbnail"
                       accept="image/png,image/jpeg,image/webp">
                <small class="text-muted">Recommended: 256x256px PNG, JPG, or WebP. Max 2MB.</small>
            </div>
            <div class="mt-3">
                <label for="youtube_link" class="form-label">YouTube Review Link</label>
                <input type="url" class="form-control" id="youtube_link" name="youtube_link"
                       placeholder="https://www.youtube.com/watch?v=..."
                       value="<?= esc($old['youtube_link'] ?? $app['youtube_link'] ?? '') ?>">
                <small class="text-muted">Link to YouTube review video for this app</small>
            </div>
        </div>
    </div>
    
    <div class="card mb-4">
        <div class="card-header">Categories</div>
        <div class="card-body">
            <div class="row g-2">
                <?php foreach ($categories as $category): ?>
                <div class="col-md-4">
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
        <div class="card-header">Security Information</div>
        <div class="card-body">
            <div class="mb-3">
                <label for="permissions" class="form-label">Permissions</label>
                <input type="text" class="form-control" id="permissions" name="permissions"
                       placeholder="e.g., camera, location, contacts (comma-separated)"
                       value="<?= esc($old['permissions'] ?? $app['permissions'] ?? '') ?>">
                <small class="text-muted">Enter permissions as comma-separated values</small>
            </div>
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="form-check">
                        <?php $hasEncryption = $old['has_encryption'] ?? $app['has_encryption'] ?? 0; ?>
                        <input class="form-check-input" type="checkbox" 
                               name="has_encryption" value="1" id="has_encryption"
                               <?= $hasEncryption ? 'checked' : '' ?>>
                        <label class="form-check-label" for="has_encryption">Uses Encryption</label>
                    </div>
                </div>
                <div class="col-md-6">
                    <label for="third_party_sdk_count" class="form-label">Third-Party SDK Count</label>
                    <input type="number" class="form-control" id="third_party_sdk_count" 
                           name="third_party_sdk_count" min="0"
                           value="<?= esc($old['third_party_sdk_count'] ?? $app['third_party_sdk_count'] ?? '0') ?>">
                </div>
            </div>
        </div>
    </div>
    
    <div class="card mb-4">
        <div class="card-header">Screenshots <small class="text-muted fw-normal">(Max 10)</small></div>
        <div class="card-body">
            <?php if ($app && !empty($app['screenshots'])): ?>
            <div class="mb-3">
                <label class="form-label">Current Screenshots</label>
                <div class="d-flex flex-wrap gap-2">
                    <?php foreach ($app['screenshots'] as $screenshot): ?>
                    <div class="position-relative">
                        <img src="<?= base_url('writable/' . esc($screenshot['file_path'])) ?>" 
                             style="max-width:120px;max-height:120px;object-fit:cover;border-radius:8px;border:2px solid #e2e8f0;" alt="Screenshot">
                        <div class="form-check position-absolute top-0 end-0 m-1 bg-white rounded p-1 shadow-sm">
                            <input class="form-check-input" type="checkbox" 
                                   name="delete_screenshots[]" value="<?= $screenshot['id'] ?>" 
                                   id="delete_<?= $screenshot['id'] ?>">
                            <label class="form-check-label small" for="delete_<?= $screenshot['id'] ?>">Delete</label>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
            <div>
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
        <div class="card-header">Approval Status</div>
        <div class="card-body">
            <div class="mb-3">
                <label for="approval_status" class="form-label">Status</label>
                <select class="form-select" id="approval_status" name="approval_status">
                    <?php
                    $statuses = ['pending', 'approved', 'rejected'];
                    $selected = $old['approval_status'] ?? $app['approval_status'] ?? 'pending';
                    foreach ($statuses as $status):
                    ?>
                    <option value="<?= $status ?>" <?= $selected === $status ? 'selected' : '' ?>><?= ucfirst($status) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
    </div>
    
    <div class="d-flex gap-2 mb-4">
        <button type="submit" class="btn btn-primary btn-lg">
            <i class="bi bi-save"></i> <?= $app ? 'Update App' : 'Create App' ?>
        </button>
        <a href="<?= base_url('admin/apps') ?>" class="btn btn-outline-secondary btn-lg">
            <i class="bi bi-x-circle"></i> Cancel
        </a>
    </div>
</form>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.getElementById('name').addEventListener('input', function() {
    const slug = this.value.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-+|-+$/g, '');
    document.getElementById('slug').value = slug;
});
</script>
<?= $this->endSection() ?>
