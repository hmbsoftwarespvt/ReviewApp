<?= $this->extend('admin/admin_layout') ?>

<?= $this->section('topbar_actions') ?>
<a href="<?= base_url('admin/blog') ?>" class="btn btn-outline-secondary btn-sm">
    <i class="bi bi-arrow-left"></i> Back to List
</a>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- TinyMCE -->
<script src="https://cdn.jsdelivr.net/npm/tinymce@6/tinymce.min.js"></script>

<style>
    .featured-image-preview {
        max-width: 300px;
        max-height: 200px;
        object-fit: cover;
        margin-top: 10px;
    }
</style>

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

<!-- Blog Post Form -->
<form method="post" action="<?= $post ? base_url('admin/blog/update/' . $post['id']) : base_url('admin/blog/store') ?>" 
      enctype="multipart/form-data">
    <?= csrf_field() ?>
    
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title mb-3">Basic Information</h5>
            
            <div class="mb-3">
                <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                <input type="text" class="form-control <?= isset($errors['title']) ? 'is-invalid' : '' ?>" 
                       id="title" name="title" required
                       value="<?= esc($old['title'] ?? $post['title'] ?? '') ?>">
            </div>
            
            <div class="mb-3">
                <label for="slug" class="form-label">Slug <span class="text-danger">*</span></label>
                <input type="text" class="form-control <?= isset($errors['slug']) ? 'is-invalid' : '' ?>" 
                       id="slug" name="slug" required
                       value="<?= esc($old['slug'] ?? $post['slug'] ?? '') ?>">
                <small class="text-muted">URL-friendly identifier (e.g., my-blog-post-title)</small>
            </div>
            
            <div class="mb-3">
                <label for="excerpt" class="form-label">Excerpt</label>
                <textarea class="form-control" id="excerpt" name="excerpt" rows="3"><?= esc($old['excerpt'] ?? $post['excerpt'] ?? '') ?></textarea>
                <small class="text-muted">Brief summary of the blog post (optional)</small>
            </div>
            
            <div class="mb-3">
                <label for="category" class="form-label">Category <span class="text-danger">*</span></label>
                <select class="form-select <?= isset($errors['category']) ? 'is-invalid' : '' ?>" 
                        id="category" name="category" required>
                    <option value="">Select Category</option>
                    <?php
                    $selected = $old['category'] ?? $post['category'] ?? '';
                    foreach ($categories as $key => $label):
                    ?>
                    <option value="<?= $key ?>" <?= $selected === $key ? 'selected' : '' ?>>
                        <?= esc($label) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="mb-3">
                <label for="publication_status" class="form-label">Publication Status</label>
                <select class="form-select" id="publication_status" name="publication_status">
                    <?php
                    $statuses = ['draft' => 'Draft', 'published' => 'Published'];
                    $selected = $old['publication_status'] ?? $post['publication_status'] ?? 'draft';
                    foreach ($statuses as $value => $label):
                    ?>
                    <option value="<?= $value ?>" <?= $selected === $value ? 'selected' : '' ?>>
                        <?= esc($label) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
    </div>
    
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title mb-3">Content <span class="text-danger">*</span></h5>
            <textarea id="content" name="content" class="form-control"><?= esc($old['content'] ?? $post['content'] ?? '') ?></textarea>
        </div>
    </div>
    
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title mb-3">Featured Image</h5>
            
            <?php if ($post && !empty($post['featured_image'])): ?>
            <div class="mb-3">
                <label class="form-label">Current Featured Image</label>
                <div>
                    <img src="<?= base_url(esc($post['featured_image'])) ?>" 
                         class="featured-image-preview border" alt="Featured Image">
                </div>
                <div class="form-check mt-2">
                    <input class="form-check-input" type="checkbox" 
                           name="delete_featured_image" value="1" id="delete_featured_image">
                    <label class="form-check-label" for="delete_featured_image">
                        Delete current featured image
                    </label>
                </div>
            </div>
            <?php endif; ?>
            
            <div class="mb-3">
                <label for="featured_image" class="form-label">
                    <?= ($post && !empty($post['featured_image'])) ? 'Replace Featured Image' : 'Upload Featured Image' ?>
                </label>
                <input type="file" class="form-control" id="featured_image" name="featured_image" 
                       accept="image/*">
                <small class="text-muted">Max file size: 2MB. Recommended size: 1200x630px</small>
            </div>
        </div>
    </div>
    
    <div class="mb-4 d-flex gap-2">
        <button type="submit" class="btn btn-primary">
            <i class="bi bi-save"></i> <?= $post ? 'Update Blog Post' : 'Create Blog Post' ?>
        </button>
        <a href="<?= base_url('admin/blog') ?>" class="btn btn-secondary">
            <i class="bi bi-x-circle"></i> Cancel
        </a>
    </div>
</form>

<script>
    // Auto-generate slug from title
    document.getElementById('title').addEventListener('input', function() {
        const slug = this.value
            .toLowerCase()
            .replace(/[^a-z0-9]+/g, '-')
            .replace(/^-+|-+$/g, '');
        document.getElementById('slug').value = slug;
    });
    
    // Initialize TinyMCE
    tinymce.init({
        selector: '#content',
        height: 500,
        menubar: true,
        plugins: [
            'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
            'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
            'insertdatetime', 'media', 'table', 'code', 'help', 'wordcount'
        ],
        toolbar: 'undo redo | blocks | ' +
            'bold italic forecolor | alignleft aligncenter ' +
            'alignright alignjustify | bullist numlist outdent indent | ' +
            'removeformat | link image | code | help',
        content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:14px }',
        image_advtab: true,
        image_title: true,
        automatic_uploads: true,
        file_picker_types: 'image',
        relative_urls: false,
        remove_script_host: false,
        convert_urls: true
    });
</script>

<?= $this->endSection() ?>
