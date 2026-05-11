# AppTrust Platform - Quick Start Guide

## Foundation Complete (Tasks 1-10) ✅

The first 10 tasks are complete. Here's how to use what's been built.

---

## Database Setup

### Run Migrations

```bash
php spark migrate
```

This will create all 12 database tables:
- users, categories, apps, app_categories
- reviews, review_helpful_votes, scam_reports
- screenshots, blog_posts, newsletter_subscribers, settings, activity_logs

### Verify Migrations

```bash
php spark migrate:status
```

---

## Using the Models

### Example: Create a User

```php
use App\Models\UserModel;

$userModel = new UserModel();

$userId = $userModel->insert([
    'username' => 'johndoe',
    'email' => 'john@example.com',
    'password_hash' => password_hash('password123', PASSWORD_BCRYPT, ['cost' => 12]),
    'role' => 'user',
    'status' => 'active',
]);
```

### Example: Create an App

```php
use App\Models\AppModel;

$appModel = new AppModel();

$appId = $appModel->insert([
    'name' => 'My Awesome App',
    'slug' => 'my-awesome-app',
    'description' => 'A great app for productivity',
    'version' => '1.0.0',
    'platform_type' => 'android',
    'developer_name' => 'Awesome Dev',
    'release_date' => '2024-01-01',
    'approval_status' => 'approved',
]);

// Attach categories
$appModel->attachCategories($appId, [1, 2, 3]); // Category IDs
```

---

## Using the Factories (for Testing)

### Generate Test Data

```php
use App\Database\Factories\UserFactory;
use App\Database\Factories\AppFactory;
use App\Database\Factories\ReviewFactory;

// Create a user
$user = UserFactory::new()->create();

// Create an app
$app = AppFactory::new()->create();

// Create a review for the app
$review = ReviewFactory::new()->createForApp($app['id'], $user['id']);
```

### Generate Multiple Records

```php
// Create 10 users
$users = UserFactory::new()->createMany(10);

// Create 20 apps
$apps = AppFactory::new()->createMany(20);
```

---

## Using the Services

### Calculate Trust Score

```php
use App\Services\TrustScoreService;

$trustScoreService = new TrustScoreService();

// Calculate trust score for an app
$score = $trustScoreService->calculateTrustScore($appId);

// Get detailed breakdown
$breakdown = $trustScoreService->getTrustScoreBreakdown($appId);

// Get color classification
$color = $trustScoreService->getScoreColor($score); // 'green', 'yellow', or 'red'
```

### Calculate Security Score

```php
use App\Services\SecurityScoreService;

$securityService = new SecurityScoreService();

// Calculate security score
$securityScore = $securityService->calculateSecurityScore($appId);

// Get detailed analysis
$analysis = $securityService->getSecurityAnalysis($appId);
```

### Calculate Developer Reputation

```php
use App\Services\DeveloperReputationService;

$reputationService = new DeveloperReputationService();

// Calculate reputation for an app
$reputation = $reputationService->calculateReputation($appId);

// Get developer statistics
$stats = $reputationService->getDeveloperStats('Developer Name');

// Get detailed breakdown
$breakdown = $reputationService->getReputationBreakdown('Developer Name');
```

---

## Using the Repositories

### App Repository

```php
use App\Repositories\AppRepository;

$appRepo = new AppRepository();

// Find app by ID
$app = $appRepo->find($appId);

// Find app by slug
$app = $appRepo->findBySlug('my-awesome-app');

// Get all apps with pagination
$result = $appRepo->getAll(['approval_status' => 'approved'], $page = 1, $perPage = 24);
$apps = $result['data'];
$pagination = $result['pagination'];

// Search apps
$result = $appRepo->search('productivity', ['platform_type' => 'android'], $page = 1);

// Get trending apps
$trending = $appRepo->getTrending(12);

// Get apps by category
$result = $appRepo->getByCategory($categoryId, $page = 1);

// Increment view count
$appRepo->incrementViewCount($appId);
```

### Review Repository

```php
use App\Repositories\ReviewRepository;

$reviewRepo = new ReviewRepository();

// Get reviews for an app
$result = $reviewRepo->getByApp($appId, 'approved', $page = 1);

// Get average rating
$avgRating = $reviewRepo->getAverageRating($appId);

// Get review count
$count = $reviewRepo->getReviewCount($appId, 'approved');

// Check if user has reviewed
$hasReviewed = $reviewRepo->userHasReviewed($userId, $appId);

// Create a review
$reviewId = $reviewRepo->create([
    'app_id' => $appId,
    'user_id' => $userId,
    'rating' => 5,
    'title' => 'Great app!',
    'review_text' => 'This app is amazing and very useful for my daily tasks.',
    'approval_status' => 'pending',
]);

// Add helpful vote
$reviewRepo->addHelpfulVote($reviewId, $userId);
```

### Scam Report Repository

```php
use App\Repositories\ScamReportRepository;

$scamRepo = new ScamReportRepository();

// Get scam reports for an app
$result = $scamRepo->getByApp($appId, 'approved', $page = 1);

// Get count by app
$count = $scamRepo->getCountByApp($appId, 'approved');

// Get count by risk level
$highRiskCount = $scamRepo->getCountByRiskLevel($appId, 'high');

// Create a scam report
$reportId = $scamRepo->create([
    'app_id' => $appId,
    'user_id' => $userId,
    'title' => 'Suspicious behavior',
    'description' => 'This app is requesting unusual permissions...',
    'risk_level' => 'high',
    'evidence_urls' => json_encode(['https://example.com/screenshot1.png']),
    'approval_status' => 'pending',
]);

// Get high-risk reports
$highRisk = $scamRepo->getHighRisk(10);

// Get statistics by risk level
$stats = $scamRepo->getStatsByRiskLevel();
// Returns: ['low' => 5, 'medium' => 10, 'high' => 3]
```

---

## Complete Workflow Example

### Creating an App with Full Data

```php
use App\Database\Factories\AppFactory;
use App\Database\Factories\UserFactory;
use App\Database\Factories\ReviewFactory;
use App\Database\Factories\CategoryFactory;
use App\Services\TrustScoreService;
use App\Services\SecurityScoreService;
use App\Services\DeveloperReputationService;

// 1. Create categories
$categories = CategoryFactory::new()->createMany(3);

// 2. Create an app
$app = AppFactory::new()->create([
    'approval_status' => 'approved',
]);

// 3. Attach categories
$appModel = new \App\Models\AppModel();
$appModel->attachCategories($app['id'], array_column($categories, 'id'));

// 4. Create users
$users = UserFactory::new()->createMany(5);

// 5. Create reviews
foreach ($users as $user) {
    ReviewFactory::new()->createForApp($app['id'], $user['id']);
}

// 6. Calculate scores
$securityService = new SecurityScoreService();
$securityScore = $securityService->calculateSecurityScore($app['id']);

$reputationService = new DeveloperReputationService();
$reputation = $reputationService->calculateReputation($app['id']);

$trustScoreService = new TrustScoreService();
$trustScore = $trustScoreService->calculateTrustScore($app['id']);

echo "App: {$app['name']}\n";
echo "Trust Score: {$trustScore}\n";
echo "Security Score: {$securityScore}\n";
echo "Developer Reputation: {$reputation}\n";
```

---

## Testing

### Run Factory Tests

```bash
vendor/bin/phpunit tests/Database/FactoryDataTest.php
```

### Create Test Data for Development

```php
// Create a complete test dataset
use App\Database\Factories\CategoryFactory;
use App\Database\Factories\AppFactory;
use App\Database\Factories\UserFactory;
use App\Database\Factories\ReviewFactory;

// Create categories
$categories = CategoryFactory::new()->createPredefinedCategories();

// Create 50 apps
$apps = AppFactory::new()->createMany(50);

// Create 20 users
$users = UserFactory::new()->createMany(20);

// Create reviews for each app (random users)
foreach ($apps as $app) {
    $numReviews = rand(3, 10);
    for ($i = 0; $i < $numReviews; $i++) {
        $user = $users[array_rand($users)];
        ReviewFactory::new()->createForApp($app['id'], $user['id']);
    }
}
```

---

## Configuration

### Database Configuration

Edit `app/Config/Database.php`:

```php
public array $default = [
    'DSN'          => '',
    'hostname'     => 'localhost',
    'username'     => 'root',
    'password'     => '',
    'database'     => 'appreview',
    'DBDriver'     => 'MySQLi',
    'DBPrefix'     => '',
    'pConnect'     => false,
    'DBDebug'      => true,
    'charset'      => 'utf8mb4',
    'DBCollat'     => 'utf8mb4_unicode_ci',
    'swapPre'      => '',
    'encrypt'      => false,
    'compress'     => false,
    'strictOn'     => false,
    'failover'     => [],
    'port'         => 3306,
];
```

### Cache Configuration

Edit `app/Config/Cache.php`:

```php
public string $handler = 'file'; // Change to 'redis' for production
```

---

## Common Tasks

### Recalculate All Trust Scores

```php
use App\Services\TrustScoreService;

$trustScoreService = new TrustScoreService();
$count = $trustScoreService->recalculateAllScores();

echo "Recalculated trust scores for {$count} apps\n";
```

### Recalculate All Developer Reputations

```php
use App\Services\DeveloperReputationService;

$reputationService = new DeveloperReputationService();
$stats = $reputationService->recalculateAllReputations();

echo "Updated {$stats['apps_updated']} apps for {$stats['total_developers']} developers\n";
```

### Clear Trust Score Cache

```php
use App\Services\TrustScoreService;

$trustScoreService = new TrustScoreService();
$trustScoreService->invalidateCache($appId);
```

---

## Next Steps

Now that the foundation is complete, you can:

1. **Implement Authentication (Tasks 11-13)**
   - User registration and login
   - Password reset
   - Auth filters

2. **Build Admin Panel (Tasks 14-20)**
   - Dashboard
   - App management
   - Review moderation
   - Scam report verification

3. **Create Public Site (Tasks 21-29)**
   - Home page
   - App detail pages
   - Search functionality
   - Review submission

4. **Add Advanced Features (Tasks 30-35)**
   - Trending calculation
   - Recommendations
   - Email notifications

---

## Documentation

- **TASKS_1-10_COMPLETION_SUMMARY.md** - Detailed completion summary
- **MODELS_IMPLEMENTATION_SUMMARY.md** - Model documentation
- **FACTORIES_IMPLEMENTATION_SUMMARY.md** - Factory system guide
- **FACTORY_USAGE_EXAMPLES.md** - Factory usage examples

---

## Support

For questions or issues:
1. Check the documentation files
2. Review the code comments in each file
3. Refer to CodeIgniter 4 documentation: https://codeigniter.com/user_guide/

---

**Foundation Status:** Complete ✅  
**Ready for:** Next phase of development (Tasks 11-45)
