<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title) ?> - AppTrust Platform</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #f8fafc;
            margin: 0;
            padding: 2rem 0;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1rem;
        }
        .hero {
            text-align: center;
            margin-bottom: 3rem;
        }
        .hero h1 {
            font-size: 2.5rem;
            font-weight: 800;
            color: #1a1a2e;
            margin-bottom: 1rem;
        }
        .hero p {
            font-size: 1.1rem;
            color: #6b7280;
            max-width: 600px;
            margin: 0 auto;
        }
        .apps-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        .app-card {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            transition: transform 0.2s;
            text-decoration: none;
            color: inherit;
        }
        .app-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 16px rgba(0,0,0,0.15);
        }
        .app-name {
            font-size: 1.2rem;
            font-weight: 700;
            color: #1a1a2e;
            margin-bottom: 0.5rem;
        }
        .app-stats {
            display: flex;
            gap: 1rem;
            font-size: 0.9rem;
            color: #6b7280;
        }
        .trust-score {
            background: #10b981;
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.85rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="hero">
            <h1>🔥 Trending Apps</h1>
            <p>Discover the most popular and trusted apps this week</p>
        </div>
        
        <div class="apps-grid">
            <?php if (!empty($trending_apps)): ?>
                <?php foreach ($trending_apps as $app): ?>
                    <a href="<?= base_url('apps/' . esc($app['slug'])) ?>" class="app-card">
                        <div class="app-name"><?= esc($app['name']) ?></div>
                        <div class="app-stats">
                            <span>⭐ <?= number_format($app['rating'] ?? 4.5) ?></span>
                            <span>👁 <?= number_format($app['view_count'] ?? 0) ?></span>
                            <span class="trust-score">Trust: <?= number_format($app['trust_score'] ?? 75) ?>/100</span>
                        </div>
                    </a>
                <?php endforeach; ?>
            <?php else: ?>
                <div style="text-align: center; padding: 3rem;">
                    <h3>No trending apps available</h3>
                    <p>Check back soon for the latest trending applications.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
