<?= $this->extend('base_template') ?>

<?= $this->section('content') ?>

<style>
    /* ===== MAIN CONTENT STYLES ===== */
    :root {
        --primary-color: #6366f1;
        --secondary-color: #6b7280;
        --success-color: #10b981;
        --warning-color: #f59e0b;
        --danger-color: #ef4444;
        --light-bg: #f8fafc;
        --border-color: #e5e7eb;
        --card-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        --hover-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
    }

    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        background-color: var(--light-bg);
        color: #1a1a2e;
        line-height: 1.6;
    }

    /* Main Content */
    .main-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 2rem 1rem;
    }

    /* Header Section */
    .trending-header {
        text-align: center;
        margin-bottom: 3rem;
    }

    .trending-title {
        font-size: 2.5rem;
        font-weight: 800;
        color: #1a1a2e;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
    }

    .trending-title i {
        color: var(--primary-color);
    }

    .trending-subtitle {
        color: var(--secondary-color);
        font-size: 1.1rem;
        max-width: 600px;
        margin: 0 auto;
    }

    /* Filter Section */
    .filter-section {
        background: white;
        border-radius: 12px;
        padding: 1.5rem;
        box-shadow: var(--card-shadow);
        margin-bottom: 2rem;
    }

    .filter-controls {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 1.5rem;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .filter-buttons {
        display: flex;
        gap: 0.5rem;
        margin-bottom: 1.5rem;
        flex-wrap: wrap;
    }

    .filter-btn {
        background: var(--light-bg);
        border: 1px solid var(--border-color);
        border-radius: 8px;
        padding: 0.5rem 1rem;
        font-size: 0.85rem;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s;
    }

    .filter-btn:hover {
        background: var(--primary-color);
        color: white;
        border-color: var(--primary-color);
    }

    .filter-btn.active {
        background: var(--primary-color);
        color: white;
        border-color: var(--primary-color);
    }

    .date-filter {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        background: var(--light-bg);
        border: 1px solid var(--border-color);
        border-radius: 8px;
        padding: 0.5rem 1rem;
        font-size: 0.85rem;
    }

    .request-section {
        background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%);
        border-radius: 8px;
        padding: 1.5rem;
        text-align: center;
    }

    .request-section h3 {
        font-size: 1rem;
        font-weight: 600;
        color: #1a1a2e;
        margin-bottom: 0.5rem;
    }

    .request-section p {
        color: var(--secondary-color);
        font-size: 0.85rem;
        margin-bottom: 1rem;
    }

    .btn-request {
        background: var(--primary-color);
        color: white;
        border: none;
        border-radius: 8px;
        padding: 0.75rem 2rem;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s;
    }

    .btn-request:hover {
        background: #5558e3;
    }

    /* Middle Column - Apps Grid */
    .apps-section {
        background: white;
        border-radius: 12px;
        padding: 1.5rem;
        box-shadow: var(--card-shadow);
    }

    .apps-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
    }

    .apps-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: #1a1a2e;
    }

    .view-more-btn {
        color: var(--primary-color);
        text-decoration: none;
        font-weight: 500;
        font-size: 0.9rem;
    }

    .view-more-btn:hover {
        text-decoration: underline;
    }

    .apps-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 1.5rem;
        margin-top: 2rem;
    }

    .app-card {
        background: white;
        border: 1px solid var(--border-color);
        border-radius: 12px;
        padding: 1.5rem;
        transition: all 0.2s;
        cursor: pointer;
        position: relative;
    }

    .app-card:hover {
        box-shadow: var(--hover-shadow);
        transform: translateY(-2px);
    }

    .trend-badge {
        position: absolute;
        top: -8px;
        left: -8px;
        background: var(--primary-color);
        color: white;
        border-radius: 20px;
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .trend-badge.up {
        background: var(--success-color);
    }

    .trend-badge.down {
        background: var(--danger-color);
    }

    .trend-badge.new {
        background: var(--warning-color);
    }

    .app-header {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-bottom: 1rem;
    }

    .app-icon {
        width: 48px;
        height: 48px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        font-weight: bold;
        color: white;
    }

    .app-info {
        flex: 1;
    }

    .app-name {
        font-size: 1.1rem;
        font-weight: 700;
        color: #1a1a2e;
        margin-bottom: 0.25rem;
    }

    .app-category {
        font-size: 0.85rem;
        color: var(--secondary-color);
        margin-bottom: 0.5rem;
    }

    .app-rating {
        display: flex;
        align-items: center;
        gap: 0.25rem;
        margin-bottom: 1rem;
    }

    .stars {
        color: #fbbf24;
    }

    .rating-count {
        font-size: 0.85rem;
        color: var(--secondary-color);
    }

    .trust-score {
        background: var(--success-color);
        color: white;
        border-radius: 20px;
        padding: 0.25rem 0.75rem;
        font-size: 0.85rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
    }

    
    /* Responsive */
    @media (max-width: 768px) {
        .trending-title {
            font-size: 2rem;
        }
        
        .filter-controls {
            flex-direction: column;
            align-items: stretch;
        }
        
        .filter-buttons {
            justify-content: center;
        }
        
        .apps-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="main-container">
    <!-- Header Section -->
    <div class="trending-header">
        <h1 class="trending-title">
            <i class="bi bi-graph-up"></i>
            Trending Apps
        </h1>
        <p class="trending-subtitle">Discover most popular and trending apps & websites right now</p>
    </div>
    
    <!-- Filter Section -->
    <div class="filter-section">
        <div class="filter-buttons">
            <button class="filter-btn active">All</button>
            <button class="filter-btn">Apps</button>
            <button class="filter-btn">Websites</button>
            <button class="filter-btn">Earning Apps</button>
            <button class="filter-btn">AI Tools</button>
            <button class="filter-btn">New Launch</button>
            <button class="filter-btn">Popular</button>
        </div>
    </div>

    <!-- Apps Grid Section -->
    <div class="apps-section">
        <div class="apps-header">
            <h2 class="apps-title">Trending Apps</h2>
            <a href="#" class="view-more-btn">View More Trending Apps →</a>
        </div>
        
        <div class="apps-grid">
            <?php if (!empty($trending_apps)): ?>
                <?php foreach ($trending_apps as $index => $app): ?>
                    <div class="app-card">
                        <?php
                        $trendType = '';
                        $trendIcon = '';
                        if ($index === 0) {
                            $trendType = 'up';
                            $trendIcon = '↑3';
                        } elseif ($index === 1) {
                            $trendType = 'down';
                            $trendIcon = '↓1';
                        } else {
                            $trendType = 'new';
                            $trendIcon = 'New';
                        }
                        ?>
                        
                        <div class="trend-badge <?= $trendType ?>">
                            <?= $trendIcon ?>
                        </div>
                        
                        <div class="app-header">
                            <div class="app-icon" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                <?= substr(esc($app['name']), 0, 1) ?>
                            </div>
                            
                            <div class="app-info">
                                <div class="app-name"><?= esc($app['name']) ?></div>
                                <div class="app-category"><?= esc($app['category_name'] ?? 'Productivity') ?></div>
                                
                                <div class="app-rating">
                                    <span class="stars">★★★★★</span>
                                    <span class="rating-count"><?= number_format($app['review_count'] ?? 1247) ?></span>
                                </div>
                                
                                <div class="trust-score">
                                    <i class="bi bi-shield-check"></i>
                                    Trust Score <?= number_format($app['trust_score'] ?? 92) ?>/100
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <!-- Sample data for demonstration -->
                <div class="app-card">
                    <div class="trend-badge up">↑3</div>
                    <div class="app-header">
                        <div class="app-icon" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                            C
                        </div>
                        <div class="app-info">
                            <div class="app-name">CapCut</div>
                            <div class="app-category">Video Editing</div>
                            <div class="app-rating">
                                <span class="stars">★★★★★</span>
                                <span class="rating-count">12,547</span>
                            </div>
                            <div class="trust-score">
                                <i class="bi bi-shield-check"></i>
                                Trust Score 95/100
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="app-card">
                    <div class="trend-badge down">↓1</div>
                    <div class="app-header">
                        <div class="app-icon" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                            T
                        </div>
                        <div class="app-info">
                            <div class="app-name">TikTok</div>
                            <div class="app-category">Social Media</div>
                            <div class="app-rating">
                                <span class="stars">★★★★☆</span>
                                <span class="rating-count">8,923</span>
                            </div>
                            <div class="trust-score">
                                <i class="bi bi-shield-check"></i>
                                Trust Score 78/100
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="app-card">
                    <div class="trend-badge new">New</div>
                    <div class="app-header">
                        <div class="app-icon" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                            C
                        </div>
                        <div class="app-info">
                            <div class="app-name">ChatGPT</div>
                            <div class="app-category">AI Tools</div>
                            <div class="app-rating">
                                <span class="stars">★★★★★</span>
                                <span class="rating-count">15,234</span>
                            </div>
                            <div class="trust-score">
                                <i class="bi bi-shield-check"></i>
                                Trust Score 92/100
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="app-card">
                    <div class="trend-badge up">↑2</div>
                    <div class="app-header">
                        <div class="app-icon" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);">
                            I
                        </div>
                        <div class="app-info">
                            <div class="app-name">Instagram</div>
                            <div class="app-category">Social Media</div>
                            <div class="app-rating">
                                <span class="stars">★★★★☆</span>
                                <span class="rating-count">9,876</span>
                            </div>
                            <div class="trust-score">
                                <i class="bi bi-shield-check"></i>
                                Trust Score 85/100
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="app-card">
                    <div class="trend-badge new">New</div>
                    <div class="app-header">
                        <div class="app-icon" style="background: linear-gradient(135deg, #30cfd0 0%, #330867 100%);">
                            L
                        </div>
                        <div class="app-info">
                            <div class="app-name">Lightroom</div>
                            <div class="app-category">Photo Editing</div>
                            <div class="app-rating">
                                <span class="stars">★★★★★</span>
                                <span class="rating-count">6,543</span>
                            </div>
                            <div class="trust-score">
                                <i class="bi bi-shield-check"></i>
                                Trust Score 88/100
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="app-card">
                    <div class="trend-badge up">↑5</div>
                    <div class="app-header">
                        <div class="app-icon" style="background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);">
                            W
                        </div>
                        <div class="app-info">
                            <div class="app-name">WhatsApp</div>
                            <div class="app-category">Messaging</div>
                            <div class="app-rating">
                                <span class="stars">★★★★★</span>
                                <span class="rating-count">18,765</span>
                            </div>
                            <div class="trust-score">
                                <i class="bi bi-shield-check"></i>
                                Trust Score 94/100
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    </div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Sample app data for different categories
    const appData = {
        all: [
            { name: 'CapCut', category: 'Video Editing', rating: 5, reviews: 12547, trust: 95, trend: 'up', trendValue: '↑3', gradient: '#667eea 0%, #764ba2 100%' },
            { name: 'TikTok', category: 'Social Media', rating: 4, reviews: 8923, trust: 78, trend: 'down', trendValue: '↓1', gradient: '#f093fb 0%, #f5576c 100%' },
            { name: 'ChatGPT', category: 'AI Tools', rating: 5, reviews: 15234, trust: 92, trend: 'new', trendValue: 'New', gradient: '#4facfe 0%, #00f2fe 100%' },
            { name: 'Instagram', category: 'Social Media', rating: 4, reviews: 9876, trust: 85, trend: 'up', trendValue: '↑2', gradient: '#fa709a 0%, #fee140 100%' },
            { name: 'Lightroom', category: 'Photo Editing', rating: 5, reviews: 6543, trust: 88, trend: 'new', trendValue: 'New', gradient: '#30cfd0 0%, #330867 100%' },
            { name: 'WhatsApp', category: 'Messaging', rating: 5, reviews: 18765, trust: 94, trend: 'up', trendValue: '↑5', gradient: '#a8edea 0%, #fed6e3 100%' }
        ],
        apps: [
            { name: 'CapCut', category: 'Video Editing', rating: 5, reviews: 12547, trust: 95, trend: 'up', trendValue: '↑3', gradient: '#667eea 0%, #764ba2 100%' },
            { name: 'Lightroom', category: 'Photo Editing', rating: 5, reviews: 6543, trust: 88, trend: 'new', trendValue: 'New', gradient: '#30cfd0 0%, #330867 100%' },
            { name: 'Canva', category: 'Design', rating: 5, reviews: 11234, trust: 91, trend: 'up', trendValue: '↑4', gradient: '#f093fb 0%, #f5576c 100%' },
            { name: 'Spotify', category: 'Music', rating: 4, reviews: 14567, trust: 87, trend: 'up', trendValue: '↑2', gradient: '#4facfe 0%, #00f2fe 100%' }
        ],
        websites: [
            { name: 'YouTube', category: 'Video Platform', rating: 4, reviews: 23456, trust: 82, trend: 'up', trendValue: '↑1', gradient: '#ff0000 0%, #cc0000 100%' },
            { name: 'GitHub', category: 'Development', rating: 5, reviews: 8765, trust: 93, trend: 'up', trendValue: '↑3', gradient: '#24292e 0%, #0366d6 100%' },
            { name: 'Figma', category: 'Design', rating: 5, reviews: 5432, trust: 89, trend: 'new', trendValue: 'New', gradient: '#f24e1e 0%, #a259ff 100%' },
            { name: 'Notion', category: 'Productivity', rating: 5, reviews: 9876, trust: 90, trend: 'up', trendValue: '↑2', gradient: '#000000 0%, #ffffff 100%' }
        ],
        'earning-apps': [
            { name: 'Swagbucks', category: 'Survey', rating: 3, reviews: 4532, trust: 72, trend: 'down', trendValue: '↓2', gradient: '#ff6b35 0%, #f77737 100%' },
            { name: 'Upwork', category: 'Freelance', rating: 4, reviews: 7654, trust: 85, trend: 'up', trendValue: '↑1', gradient: '#6FDA44 0%, #2D8F00 100%' },
            { name: 'Fiverr', category: 'Freelance', rating: 4, reviews: 8901, trust: 83, trend: 'up', trendValue: '↑3', gradient: '#1DBF73 0%, #00B049 100%' },
            { name: 'Amazon Mechanical Turk', category: 'Micro-tasks', rating: 3, reviews: 3210, trust: 68, trend: 'down', trendValue: '↓1', gradient: '#ff9900 0%, #ff6600 100%' }
        ],
        'ai-tools': [
            { name: 'ChatGPT', category: 'AI Assistant', rating: 5, reviews: 15234, trust: 92, trend: 'new', trendValue: 'New', gradient: '#4facfe 0%, #00f2fe 100%' },
            { name: 'Midjourney', category: 'AI Art', rating: 5, reviews: 6789, trust: 88, trend: 'up', trendValue: '↑5', gradient: '#7c3aed 0%, #ec4899 100%' },
            { name: 'DALL-E', category: 'AI Art', rating: 5, reviews: 5432, trust: 90, trend: 'new', trendValue: 'New', gradient: '#10b981 0%, #3b82f6 100%' },
            { name: 'Claude', category: 'AI Assistant', rating: 5, reviews: 4321, trust: 91, trend: 'up', trendValue: '↑2', gradient: '#f59e0b 0%, #ef4444 100%' }
        ],
        'new-launch': [
            { name: 'ChatGPT', category: 'AI Tools', rating: 5, reviews: 15234, trust: 92, trend: 'new', trendValue: 'New', gradient: '#4facfe 0%, #00f2fe 100%' },
            { name: 'Lightroom', category: 'Photo Editing', rating: 5, reviews: 6543, trust: 88, trend: 'new', trendValue: 'New', gradient: '#30cfd0 0%, #330867 100%' },
            { name: 'DALL-E', category: 'AI Art', rating: 5, reviews: 5432, trust: 90, trend: 'new', trendValue: 'New', gradient: '#10b981 0%, #3b82f6 100%' },
            { name: 'Figma', category: 'Design', rating: 5, reviews: 5432, trust: 89, trend: 'new', trendValue: 'New', gradient: '#f24e1e 0%, #a259ff 100%' }
        ],
        popular: [
            { name: 'WhatsApp', category: 'Messaging', rating: 5, reviews: 18765, trust: 94, trend: 'up', trendValue: '↑5', gradient: '#a8edea 0%, #fed6e3 100%' },
            { name: 'YouTube', category: 'Video Platform', rating: 4, reviews: 23456, trust: 82, trend: 'up', trendValue: '↑1', gradient: '#ff0000 0%, #cc0000 100%' },
            { name: 'Instagram', category: 'Social Media', rating: 4, reviews: 9876, trust: 85, trend: 'up', trendValue: '↑2', gradient: '#fa709a 0%, #fee140 100%' },
            { name: 'TikTok', category: 'Social Media', rating: 4, reviews: 8923, trust: 78, trend: 'down', trendValue: '↓1', gradient: '#f093fb 0%, #f5576c 100%' }
        ]
    };

    // Function to generate star rating HTML
    function generateStars(rating) {
        let stars = '';
        for (let i = 1; i <= 5; i++) {
            stars += i <= rating ? '★' : '☆';
        }
        return stars;
    }

    // Function to render app cards
    function renderApps(category) {
        const appsGrid = document.querySelector('.apps-grid');
        const apps = appData[category] || appData.all;
        
        let html = '';
        apps.forEach((app, index) => {
            html += `
                <div class="app-card">
                    <div class="trend-badge ${app.trend}">${app.trendValue}</div>
                    <div class="app-header">
                        <div class="app-icon" style="background: linear-gradient(135deg, ${app.gradient});">
                            ${app.name.charAt(0)}
                        </div>
                        <div class="app-info">
                            <div class="app-name">${app.name}</div>
                            <div class="app-category">${app.category}</div>
                            <div class="app-rating">
                                <span class="stars">${generateStars(app.rating)}</span>
                                <span class="rating-count">${app.reviews.toLocaleString()}</span>
                            </div>
                            <div class="trust-score">
                                <i class="bi bi-shield-check"></i>
                                Trust Score ${app.trust}/100
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });
        
        appsGrid.innerHTML = html;
    }

    // Function to handle tab switching
    function switchTabs(activeBtn) {
        // Remove active class from all buttons
        document.querySelectorAll('.filter-btn').forEach(btn => {
            btn.classList.remove('active');
        });
        
        // Add active class to clicked button
        activeBtn.classList.add('active');
        
        // Get the category from button text
        const category = activeBtn.textContent.toLowerCase().replace(' ', '-').replace('earning-apps', 'earning-apps');
        
        // Render apps for the selected category
        renderApps(category);
    }

    // Add click event listeners to filter buttons
    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            switchTabs(this);
        });
    });

    // Initialize with 'All' category
    const allBtn = document.querySelector('.filter-btn.active');
    if (allBtn) {
        switchTabs(allBtn);
    }
});
</script>

<?= $this->endSection() ?>
