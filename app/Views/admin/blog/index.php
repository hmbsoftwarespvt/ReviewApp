<?= $this->extend('admin/admin_layout') ?>

<?= $this->section('topbar_actions') ?>
<a href="<?= base_url('admin/blog/create') ?>" class="btn btn-primary btn-sm">
    <i class="bi bi-plus-circle"></i> Create Blog Post
</a>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

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
    <div class="card-body p-0">
        <?php if (empty($posts)): ?>
        <div class="text-center py-5">
            <i class="bi bi-newspaper" style="font-size: 3rem; color: #cbd5e1;"></i>
            <p class="text-muted mt-3">No blog posts found.</p>
            <a href="<?= base_url('admin/blog/create') ?>" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Create First Blog Post
            </a>
        </div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Category</th>
                        <th>Author</th>
                        <th>Status</th>
                        <th>Views</th>
                        <th>Updated</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($posts as $post): ?>
                    <tr>
                        <td>
                            <span class="fw-medium"><?= esc($post['title']) ?></span>
                            <?php if (!empty($post['excerpt'])): ?>
                            <br>
                            <small class="text-muted"><?= esc($post['excerpt']) ?></small>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="badge bg-info">
                                <?= esc($categories[$post['category']] ?? $post['category']) ?>
                            </span>
                        </td>
                        <td><?= esc($post['author_name']) ?></td>
                        <td>
                            <span class="badge bg-<?= $post['publication_status'] === 'published' ? 'success' : 'secondary' ?>">
                                <?= ucfirst($post['publication_status']) ?>
                            </span>
                        </td>
                        <td><?= number_format($post['view_count']) ?></td>
                        <td class="small text-muted"><?= date('M d, Y', strtotime($post['updated_at'])) ?></td>
                        <td class="text-end">
                            <div class="btn-group btn-group-sm">
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
        <div class="card-body border-top">
            <nav>
                <ul class="pagination justify-content-center mb-0">
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
        </div>
        <?php endif; ?>

        <div class="card-body border-top text-center">
            <small class="text-muted">Showing <?= count($posts) ?> of <?= $pagination['total_items'] ?> blog posts</small>
        </div>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>
