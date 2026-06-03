<?= $this->extend('admin/admin_layout') ?>

<?= $this->section('topbar_actions') ?>
<a href="<?= base_url('admin/categories/create') ?>" class="btn btn-primary btn-sm">
    <i class="bi bi-plus-lg"></i> Add New Category
</a>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="card">
    <div class="card-body p-0">
        <?php if (empty($categories)): ?>
        <div class="text-center py-5">
            <i class="bi bi-tags" style="font-size:3rem;color:#cbd5e1;"></i>
            <h5 class="mt-3 text-muted">No Categories Found</h5>
            <p class="text-muted">Create your first category to get started.</p>
            <a href="<?= base_url('admin/categories/create') ?>" class="btn btn-primary">
                <i class="bi bi-plus-lg"></i> Add Category
            </a>
        </div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Slug</th>
                        <th>Icon</th>
                        <th>Display Order</th>
                        <th>Apps</th>
                        <th>Created</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($categories as $category): ?>
                    <tr>
                        <td class="text-muted"><?= $category['id'] ?></td>
                        <td>
                            <div class="fw-medium"><?= esc($category['name']) ?></div>
                            <?php if (!empty($category['description'])): ?>
                            <small class="text-muted"><?= esc(substr($category['description'], 0, 60)) ?>...</small>
                            <?php endif; ?>
                        </td>
                        <td><code class="small"><?= esc($category['slug']) ?></code></td>
                        <td>
                            <?php if (!empty($category['icon'])): ?>
                            <i class="bi <?= esc($category['icon']) ?>"></i>
                            <?php else: ?>
                            <span class="text-muted">-</span>
                            <?php endif; ?>
                        </td>
                        <td><?= $category['display_order'] ?></td>
                        <td><span class="badge bg-info"><?= $category['app_count'] ?> apps</span></td>
                        <td class="text-muted small"><?= date('M j, Y', strtotime($category['created_at'])) ?></td>
                        <td class="text-end">
                            <div class="btn-group btn-group-sm">
                                <a href="<?= base_url('admin/categories/edit/' . $category['id']) ?>" 
                                   class="btn btn-outline-primary" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="<?= base_url('admin/categories/delete/' . $category['id']) ?>" 
                                      method="post" class="d-inline"
                                      onsubmit="return confirm('Are you sure you want to delete this category? This action cannot be undone.');">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn btn-outline-danger" title="Delete">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>
