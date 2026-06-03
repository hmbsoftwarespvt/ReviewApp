<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title) ?> - AppTrust Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --sidebar-width: 250px;
            --sidebar-bg: linear-gradient(180deg, #1e1e2f 0%, #2d2d44 100%);
            --sidebar-hover: rgba(255,255,255,0.08);
            --sidebar-active: rgba(99,102,241,0.25);
            --accent: #6366f1;
            --accent-light: #818cf8;
            --bg-main: #f1f5f9;
            --card-shadow: 0 1px 3px rgba(0,0,0,0.06), 0 1px 2px rgba(0,0,0,0.04);
            --card-shadow-hover: 0 10px 15px -3px rgba(0,0,0,0.08), 0 4px 6px -2px rgba(0,0,0,0.04);
            --radius: 12px;
            --radius-sm: 8px;
        }
        * { font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif; }
        body { background: var(--bg-main); }
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: var(--sidebar-width);
            height: 100vh;
            background: var(--sidebar-bg);
            z-index: 1000;
            overflow-y: auto;
            transition: all 0.3s;
        }
        .sidebar::-webkit-scrollbar { width: 4px; }
        .sidebar::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.15); border-radius: 4px; }
        .sidebar-brand {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid rgba(255,255,255,0.06);
        }
        .sidebar-brand .brand-text {
            font-size: 1.15rem;
            font-weight: 700;
            color: #fff;
            text-decoration: none;
        }
        .sidebar-brand .brand-text i { color: var(--accent-light); margin-right: 0.5rem; }
        .sidebar .nav { padding: 0.75rem 0; }
        .sidebar .nav-item { padding: 0 0.75rem; }
        .sidebar .nav-link {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.625rem 1rem;
            color: rgba(255,255,255,0.6);
            border-radius: var(--radius-sm);
            font-size: 0.875rem;
            font-weight: 500;
            transition: all 0.2s;
            text-decoration: none;
        }
        .sidebar .nav-link:hover {
            color: #fff;
            background: var(--sidebar-hover);
        }
        .sidebar .nav-link.active {
            color: #fff;
            background: var(--sidebar-active);
        }
        .sidebar .nav-link i { font-size: 1.1rem; width: 1.25rem; text-align: center; }
        .sidebar .nav-divider {
            margin: 0.5rem 1.25rem;
            border-color: rgba(255,255,255,0.06);
        }
        .sidebar .nav-footer { margin-top: auto; padding: 0 0.75rem 1rem; }
        .main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
        }
        .top-bar {
            background: #fff;
            padding: 0.875rem 2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 1px 3px rgba(0,0,0,0.04);
            position: sticky;
            top: 0;
            z-index: 999;
        }
        .top-bar .page-title { font-size: 1.25rem; font-weight: 700; color: #1e293b; margin: 0; }
        .top-bar .page-title i { color: var(--accent); margin-right: 0.5rem; }
        .top-bar .top-bar-actions { display: flex; align-items: center; gap: 0.75rem; }
        .page-content { padding: 1.5rem 2rem 2rem; }
        .card {
            border: none;
            border-radius: var(--radius);
            box-shadow: var(--card-shadow);
            transition: box-shadow 0.2s;
        }
        .card:hover { box-shadow: var(--card-shadow-hover); }
        .card-header {
            background: transparent;
            border-bottom: 1px solid #e2e8f0;
            padding: 1rem 1.25rem;
            font-weight: 600;
            font-size: 0.95rem;
            color: #1e293b;
        }
        .card-header h5 { font-weight: 600; font-size: 0.95rem; margin: 0; }
        .card-body { padding: 1.25rem; }
        .stat-card {
            border-radius: var(--radius);
            border: none;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .stat-card:hover { transform: translateY(-3px); box-shadow: var(--card-shadow-hover); }
        .stat-card .stat-icon {
            width: 48px; height: 48px; border-radius: var(--radius-sm);
            display: flex; align-items: center; justify-content: center;
            font-size: 1.5rem;
        }
        .stat-card .stat-value { font-size: 1.75rem; font-weight: 700; margin: 0; line-height: 1.2; }
        .stat-card .stat-label { font-size: 0.8rem; color: #64748b; font-weight: 500; text-transform: uppercase; letter-spacing: 0.03em; }
        .table { margin: 0; }
        .table thead th {
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: #64748b;
            border-bottom: 2px solid #e2e8f0;
            padding: 0.75rem 0.5rem;
        }
        .table tbody td { padding: 0.75rem 0.5rem; vertical-align: middle; font-size: 0.875rem; }
        .table tbody tr:hover { background: #f8fafc; }
        .table tbody tr:last-child td { border-bottom: none; }
        .form-label { font-size: 0.8rem; font-weight: 600; color: #334155; text-transform: uppercase; letter-spacing: 0.03em; margin-bottom: 0.375rem; }
        .form-control, .form-select {
            border-radius: var(--radius-sm);
            border: 1.5px solid #e2e8f0;
            padding: 0.5rem 0.75rem;
            font-size: 0.875rem;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        .form-control:focus, .form-select:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(99,102,241,0.12);
        }
        .btn {
            border-radius: var(--radius-sm);
            font-weight: 600;
            font-size: 0.8rem;
            padding: 0.5rem 1rem;
            transition: all 0.2s;
        }
        .btn-primary { background: var(--accent); border-color: var(--accent); }
        .btn-primary:hover { background: #4f46e5; border-color: #4f46e5; transform: translateY(-1px); box-shadow: 0 4px 8px rgba(99,102,241,0.25); }
        .btn-outline-primary { color: var(--accent); border-color: var(--accent); }
        .btn-outline-primary:hover { background: var(--accent); border-color: var(--accent); }
        .btn-sm { padding: 0.325rem 0.625rem; font-size: 0.75rem; }
        .btn-lg { padding: 0.625rem 1.25rem; font-size: 0.9rem; }
        .badge {
            font-weight: 500;
            font-size: 0.7rem;
            padding: 0.3em 0.65em;
            border-radius: 6px;
        }
        .alert {
            border: none;
            border-radius: var(--radius-sm);
        }
        .page-link {
            border-radius: var(--radius-sm);
            color: #475569;
            border: 1px solid #e2e8f0;
            padding: 0.4rem 0.75rem;
            font-size: 0.8rem;
            font-weight: 500;
        }
        .page-link:hover { background: #f1f5f9; color: var(--accent); border-color: var(--accent); }
        .page-item.active .page-link { background: var(--accent); border-color: var(--accent); }
        .page-item.disabled .page-link { opacity: 0.5; }
        .filter-section {
            background: #fff;
            border-radius: var(--radius);
            padding: 1.25rem;
            box-shadow: var(--card-shadow);
        }
        @media (max-width: 768px) {
            .sidebar { width: 60px; }
            .sidebar .nav-link span, .sidebar-brand .brand-text span { display: none; }
            .main-content { margin-left: 60px; }
            .page-content { padding: 1rem; }
            .top-bar { padding: 0.75rem 1rem; }
        }
    </style>
    <?= $this->renderSection('styles') ?>
</head>
<body>
    <?php
    $uri = service('uri');
    $segment2 = $uri->getSegment(2) ?? 'dashboard';
    $segment3 = $uri->getSegment(3) ?? '';
    $menuItems = [
        'dashboard'     => ['label' => 'Dashboard',       'icon' => 'bi-speedometer2',     'url' => 'admin/dashboard'],
        'apps'          => ['label' => 'Apps',            'icon' => 'bi-app',              'url' => 'admin/apps'],
        'categories'    => ['label' => 'Categories',      'icon' => 'bi-tags',             'url' => 'admin/categories'],
        'reviews'       => ['label' => 'Reviews',         'icon' => 'bi-star',             'url' => 'admin/reviews'],
        'scam-reports'  => ['label' => 'Scam Reports',    'icon' => 'bi-exclamation-triangle', 'url' => 'admin/scam-reports'],
        'users'         => ['label' => 'Users',           'icon' => 'bi-people',           'url' => 'admin/users'],
        'blog'          => ['label' => 'Blog',            'icon' => 'bi-newspaper',        'url' => 'admin/blog'],
        'settings'      => ['label' => 'Settings',        'icon' => 'bi-gear',             'url' => 'admin/settings'],
    ];
    $isActive = fn($key) => $segment2 === $key ? 'active' : '';
    ?>

    <div class="sidebar">
        <div class="sidebar-brand">
            <a href="<?= base_url('admin/dashboard') ?>" class="brand-text">
                <i class="bi bi-shield-check"></i><span>AppTrust</span>
            </a>
        </div>
        <ul class="nav flex-column">
            <?php foreach ($menuItems as $key => $item): ?>
            <li class="nav-item">
                <a class="nav-link <?= $isActive($key) ?>" href="<?= base_url($item['url']) ?>">
                    <i class="bi <?= $item['icon'] ?>"></i>
                    <span><?= $item['label'] ?></span>
                </a>
            </li>
            <?php endforeach; ?>
        </ul>
        <hr class="nav-divider">
        <div class="nav-footer" style="padding: 0 0.75rem 1rem;">
            <a class="nav-link" href="<?= base_url('/') ?>">
                <i class="bi bi-house"></i> <span>View Site</span>
            </a>
            <a class="nav-link" href="<?= base_url('auth/logout') ?>">
                <i class="bi bi-box-arrow-right"></i> <span>Logout</span>
            </a>
        </div>
    </div>

    <div class="main-content">
        <div class="top-bar">
            <h1 class="page-title">
                <?php
                $icon = $menuItems[$segment2]['icon'] ?? 'bi-speedometer2';
                ?>
                <i class="bi <?= $icon ?>"></i>
                <?= esc($title) ?>
            </h1>
            <div class="top-bar-actions">
                <?= $this->renderSection('topbar_actions') ?>
            </div>
        </div>

        <div class="page-content">
            <?php if (session('success')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-1"></i> <?= session('success') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>

            <?php if (session('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-circle me-1"></i> <?= session('error') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>

            <?php if (!empty($errors)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Please fix the following errors:</strong>
                <ul class="mb-0 mt-1">
                    <?php foreach ($errors as $error): ?>
                    <li><?= esc($error) ?></li>
                    <?php endforeach; ?>
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>

            <?= $this->renderSection('content') ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <?= $this->renderSection('scripts') ?>
</body>
</html>
