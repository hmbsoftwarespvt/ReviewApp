<?= $this->extend('admin/admin_layout') ?>

<?= $this->section('topbar_actions') ?>
<a href="<?= base_url('admin/categories') ?>" class="btn btn-outline-secondary btn-sm">
    <i class="bi bi-arrow-left"></i> Back to List
</a>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<form method="post" action="<?= $category ? base_url('admin/categories/update/' . $category['id']) : base_url('admin/categories/store') ?>">
    <?= csrf_field() ?>
    
    <div class="card mb-4">
        <div class="card-header">Category Information</div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="name" class="form-label">Category Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control <?= isset($errors['name']) ? 'is-invalid' : '' ?>" 
                           id="name" name="name" required
                           value="<?= esc($old['name'] ?? $category['name'] ?? '') ?>">
                </div>
                <div class="col-md-6">
                    <label for="slug" class="form-label">Slug</label>
                    <input type="text" class="form-control <?= isset($errors['slug']) ? 'is-invalid' : '' ?>" 
                           id="slug" name="slug"
                           value="<?= esc($old['slug'] ?? $category['slug'] ?? '') ?>">
                    <small class="text-muted">URL-friendly identifier (auto-generated from name)</small>
                </div>
            </div>
            <div class="mt-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description" rows="4"><?= esc($old['description'] ?? $category['description'] ?? '') ?></textarea>
                <small class="text-muted">Brief description of the category</small>
            </div>
            <div class="row g-3 mt-2">
                <div class="col-md-6">
                    <label for="icon" class="form-label">Icon (Bootstrap Icons)</label>
                    <input type="text" class="form-control" id="icon" name="icon"
                           placeholder="e.g., bi-star, bi-heart, bi-gear"
                           value="<?= esc($old['icon'] ?? $category['icon'] ?? '') ?>">
                    <small class="text-muted">Bootstrap icon class name (with 'bi-' prefix)</small>
                </div>
                <div class="col-md-6">
                    <label for="display_order" class="form-label">Display Order</label>
                    <input type="number" class="form-control" id="display_order" name="display_order" 
                           min="0"
                           value="<?= esc($old['display_order'] ?? $category['display_order'] ?? '0') ?>">
                    <small class="text-muted">Lower numbers appear first</small>
                </div>
            </div>
        </div>
    </div>
    
    <div class="d-flex gap-2 mb-4">
        <button type="submit" class="btn btn-primary btn-lg">
            <i class="bi bi-save"></i> <?= $category ? 'Update Category' : 'Create Category' ?>
        </button>
        <a href="<?= base_url('admin/categories') ?>" class="btn btn-outline-secondary btn-lg">
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
