<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="container my-5">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1>Compare Apps</h1>
            <p class="text-muted">Compare up to 4 apps side-by-side to make informed decisions.</p>
        </div>
        <div class="col-md-4 text-end">
            <?php if (!empty($apps)): ?>
                <a href="<?= base_url('comparison/clear') ?>" class="btn btn-outline-danger">
                    <i class="bi bi-trash"></i> Clear All
                </a>
            <?php endif; ?>
        </div>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Add App Section -->
    <?php if ($canAddMore): ?>
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title">Add App to Comparison</h5>
                <form action="<?= base_url('comparison/add') ?>" method="post" class="row g-3">
                    <?= csrf_field() ?>
                    <div class="col-md-10">
                        <input type="text" class="form-control" id="appSearch" placeholder="Search for an app..." autocomplete="off">
                        <input type="hidden" name="app_id" id="selectedAppId">
                        <div id="searchResults" class="list-group mt-2" style="display: none;"></div>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">Add</button>
                    </div>
                </form>
            </div>
        </div>
    <?php endif; ?>

    <!-- Comparison Table -->
    <?php if ($canCompare): ?>
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 200px;">Metric</th>
                                <?php foreach ($apps as $item): ?>
                                    <th class="text-center">
                                        <?= esc($item['app']['name']) ?>
                                        <br>
                                        <small class="text-muted"><?= esc($item['app']['developer_name']) ?></small>
                                        <br>
                                        <a href="<?= base_url('comparison/remove/' . $item['app']['id']) ?>" class="btn btn-sm btn-outline-danger mt-2">
                                            <i class="bi bi-x"></i> Remove
                                        </a>
                                    </th>
                                <?php endforeach; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Trust Score -->
                            <tr>
                                <td><strong>Trust Score</strong></td>
                                <?php foreach ($apps as $item): ?>
                                    <?php
                                    $score = $item['app']['trust_score'];
                                    $isHighest = ($score == $highestScore && $highestScore != $lowestScore);
                                    $isLowest = ($score == $lowestScore && $highestScore != $lowestScore);
                                    $colorClass = $this->trustScoreService->getScoreColorClass($score);
                                    $bgClass = '';
                                    
                                    if ($isHighest) {
                                        $bgClass = 'table-success';
                                    } elseif ($isLowest) {
                                        $bgClass = 'table-danger';
                                    }
                                    ?>
                                    <td class="text-center <?= $bgClass ?>">
                                        <h3 class="<?= $colorClass ?> mb-0"><?= number_format($score, 1) ?></h3>
                                        <?php if ($isHighest): ?>
                                            <small class="text-success"><i class="bi bi-trophy-fill"></i> Highest</small>
                                        <?php elseif ($isLowest): ?>
                                            <small class="text-danger"><i class="bi bi-arrow-down"></i> Lowest</small>
                                        <?php endif; ?>
                                    </td>
                                <?php endforeach; ?>
                            </tr>

                            <!-- Trust Score Breakdown -->
                            <tr>
                                <td colspan="<?= count($apps) + 1 ?>" class="table-secondary">
                                    <strong>Trust Score Breakdown</strong>
                                </td>
                            </tr>

                            <!-- User Reviews -->
                            <tr>
                                <td>User Reviews</td>
                                <?php foreach ($apps as $item): ?>
                                    <td class="text-center">
                                        <?= number_format($item['breakdown']['review_rating']['score'], 1) ?> / <?= $item['breakdown']['review_rating']['max_points'] ?>
                                        <br>
                                        <small class="text-muted">
                                            <?= number_format($item['breakdown']['review_rating']['average_rating'], 1) ?> ★ 
                                            (<?= $item['breakdown']['review_rating']['review_count'] ?> reviews)
                                        </small>
                                    </td>
                                <?php endforeach; ?>
                            </tr>

                            <!-- Security Score -->
                            <tr>
                                <td>Security Analysis</td>
                                <?php foreach ($apps as $item): ?>
                                    <td class="text-center">
                                        <?= number_format($item['breakdown']['security_score']['score'], 1) ?> / <?= $item['breakdown']['security_score']['max_points'] ?>
                                    </td>
                                <?php endforeach; ?>
                            </tr>

                            <!-- Developer Reputation -->
                            <tr>
                                <td>Developer Reputation</td>
                                <?php foreach ($apps as $item): ?>
                                    <td class="text-center">
                                        <?= number_format($item['breakdown']['developer_reputation']['score'], 1) ?> / <?= $item['breakdown']['developer_reputation']['max_points'] ?>
                                    </td>
                                <?php endforeach; ?>
                            </tr>

                            <!-- Scam Reports -->
                            <tr>
                                <td>Scam Reports Impact</td>
                                <?php foreach ($apps as $item): ?>
                                    <td class="text-center">
                                        <?= number_format($item['breakdown']['scam_report_count']['score'], 1) ?> / <?= $item['breakdown']['scam_report_count']['max_points'] ?>
                                        <br>
                                        <small class="text-muted">
                                            (<?= $item['breakdown']['scam_report_count']['scam_report_count'] ?> reports)
                                        </small>
                                    </td>
                                <?php endforeach; ?>
                            </tr>

                            <!-- App Age -->
                            <tr>
                                <td>App Age</td>
                                <?php foreach ($apps as $item): ?>
                                    <td class="text-center">
                                        <?= number_format($item['breakdown']['app_age']['score'], 1) ?> / <?= $item['breakdown']['app_age']['max_points'] ?>
                                        <br>
                                        <small class="text-muted">
                                            (<?= $item['breakdown']['app_age']['age_days'] ?> days)
                                        </small>
                                    </td>
                                <?php endforeach; ?>
                            </tr>

                            <!-- App Details -->
                            <tr>
                                <td colspan="<?= count($apps) + 1 ?>" class="table-secondary">
                                    <strong>App Details</strong>
                                </td>
                            </tr>

                            <!-- Platform -->
                            <tr>
                                <td>Platform</td>
                                <?php foreach ($apps as $item): ?>
                                    <td class="text-center">
                                        <span class="badge bg-secondary"><?= ucfirst(esc($item['app']['platform_type'])) ?></span>
                                    </td>
                                <?php endforeach; ?>
                            </tr>

                            <!-- Version -->
                            <tr>
                                <td>Version</td>
                                <?php foreach ($apps as $item): ?>
                                    <td class="text-center"><?= esc($item['app']['version'] ?? 'N/A') ?></td>
                                <?php endforeach; ?>
                            </tr>

                            <!-- Size -->
                            <tr>
                                <td>Size</td>
                                <?php foreach ($apps as $item): ?>
                                    <td class="text-center"><?= esc($item['app']['size'] ?? 'N/A') ?></td>
                                <?php endforeach; ?>
                            </tr>

                            <!-- Price -->
                            <tr>
                                <td>Price</td>
                                <?php foreach ($apps as $item): ?>
                                    <td class="text-center">
                                        <?php if ($item['app']['price'] == 0): ?>
                                            <span class="badge bg-success">Free</span>
                                        <?php else: ?>
                                            $<?= number_format($item['app']['price'], 2) ?>
                                        <?php endif; ?>
                                    </td>
                                <?php endforeach; ?>
                            </tr>

                            <!-- Release Date -->
                            <tr>
                                <td>Release Date</td>
                                <?php foreach ($apps as $item): ?>
                                    <td class="text-center">
                                        <?= date('M d, Y', strtotime($item['app']['release_date'])) ?>
                                    </td>
                                <?php endforeach; ?>
                            </tr>

                            <!-- Actions -->
                            <tr>
                                <td>Actions</td>
                                <?php foreach ($apps as $item): ?>
                                    <td class="text-center">
                                        <a href="<?= base_url('app/' . $item['app']['slug']) ?>" class="btn btn-sm btn-primary">
                                            View Details
                                        </a>
                                    </td>
                                <?php endforeach; ?>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="alert alert-info">
            <h5><i class="bi bi-info-circle"></i> No Apps Selected</h5>
            <p class="mb-0">Please add at least 2 apps to start comparing. You can compare up to 4 apps at once.</p>
        </div>
    <?php endif; ?>
</div>

<script>
// App search functionality
const searchInput = document.getElementById('appSearch');
const searchResults = document.getElementById('searchResults');
const selectedAppId = document.getElementById('selectedAppId');

let searchTimeout;

searchInput?.addEventListener('input', function() {
    clearTimeout(searchTimeout);
    const query = this.value.trim();
    
    if (query.length < 2) {
        searchResults.style.display = 'none';
        return;
    }
    
    searchTimeout = setTimeout(() => {
        fetch(`<?= base_url('comparison/search') ?>?q=${encodeURIComponent(query)}`)
            .then(response => response.json())
            .then(data => {
                if (data.length === 0) {
                    searchResults.innerHTML = '<div class="list-group-item">No apps found</div>';
                    searchResults.style.display = 'block';
                    return;
                }
                
                searchResults.innerHTML = data.map(app => `
                    <a href="#" class="list-group-item list-group-item-action" data-app-id="${app.id}">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <strong>${app.name}</strong>
                                <br>
                                <small class="text-muted">${app.developer_name}</small>
                            </div>
                            <div class="text-end">
                                <span class="badge bg-primary">${app.trust_score}</span>
                                <br>
                                <small class="text-muted">${app.platform_type}</small>
                            </div>
                        </div>
                    </a>
                `).join('');
                
                searchResults.style.display = 'block';
                
                // Add click handlers
                searchResults.querySelectorAll('a').forEach(link => {
                    link.addEventListener('click', function(e) {
                        e.preventDefault();
                        const appId = this.dataset.appId;
                        const appName = this.querySelector('strong').textContent;
                        
                        selectedAppId.value = appId;
                        searchInput.value = appName;
                        searchResults.style.display = 'none';
                    });
                });
            });
    }, 300);
});

// Hide search results when clicking outside
document.addEventListener('click', function(e) {
    if (!searchInput?.contains(e.target) && !searchResults?.contains(e.target)) {
        searchResults.style.display = 'none';
    }
});
</script>

<?= $this->endSection() ?>
