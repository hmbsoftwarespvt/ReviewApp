# Factory Usage Examples

This document provides practical examples of using the model factories for testing and database seeding.

## Basic Usage

### Generate Data Only (No Database Insertion)

```php
use App\Database\Factories\UserFactory;

$factory = new UserFactory();

// Generate a single user
$userData = $factory->make();

// Generate with custom data
$userData = $factory->make(['email' => 'test@example.com']);

// Generate multiple users
$users = $factory->makeMany(10);
```

### Create Records in Database

```php
use App\Database\Factories\UserFactory;

$factory = new UserFactory();

// Create and insert a single user
$userId = $factory->create();

// Create with custom data
$userId = $factory->create(['email' => 'test@example.com']);

// Create multiple users
$userIds = $factory->createMany(10);
```

## Working with Relationships

### Create App with Reviews and Scam Reports

```php
use App\Database\Factories\UserFactory;
use App\Database\Factories\AppFactory;
use App\Database\Factories\ReviewFactory;
use App\Database\Factories\ScamReportFactory;
use App\Database\Factories\ScreenshotFactory;
use App\Database\Factories\CategoryFactory;
use App\Models\AppModel;

// Create users
$userFactory = new UserFactory();
$userIds = $userFactory->createMany(20, $userFactory->verified());

// Create categories
$categoryFactory = new CategoryFactory();
$categoryIds = $categoryFactory->createAllPredefined();

// Create app
$appFactory = new AppFactory();
$appId = $appFactory->create($appFactory->approved());

// Attach categories to app
$appModel = new AppModel();
$appModel->attachCategories($appId, [$categoryIds[0], $categoryIds[1], $categoryIds[2]]);

// Create screenshots for app
$screenshotFactory = new ScreenshotFactory();
$screenshotFactory->createForApp($appId, 5);

// Create reviews for app
$reviewFactory = new ReviewFactory();
for ($i = 0; $i < 15; $i++) {
    $reviewFactory->create([
        'app_id' => $appId,
        'user_id' => $userIds[array_rand($userIds)],
        'approval_status' => 'approved',
    ]);
}

// Create scam reports for app
$scamFactory = new ScamReportFactory();
for ($i = 0; $i < 3; $i++) {
    $scamFactory->create([
        'app_id' => $appId,
        'user_id' => $userIds[array_rand($userIds)],
        'approval_status' => 'approved',
    ]);
}
```

### Create Blog Posts with Authors

```php
use App\Database\Factories\UserFactory;
use App\Database\Factories\BlogPostFactory;

// Create admin users (authors)
$userFactory = new UserFactory();
$authorIds = $userFactory->createMany(5, $userFactory->admin());

// Create blog posts
$blogFactory = new BlogPostFactory();
foreach ($authorIds as $authorId) {
    // Create 3 published posts per author
    for ($i = 0; $i < 3; $i++) {
        $blogFactory->create([
            'author_id' => $authorId,
            'publication_status' => 'published',
            'published_at' => date('Y-m-d H:i:s', strtotime("-{$i} days")),
        ]);
    }
    
    // Create 1 draft post per author
    $blogFactory->create([
        'author_id' => $authorId,
        'publication_status' => 'draft',
    ]);
}
```

## Using Helper Methods

### User Factory Helpers

```php
use App\Database\Factories\UserFactory;

$factory = new UserFactory();

// Create admin user
$adminId = $factory->create($factory->admin());

// Create regular user
$userId = $factory->create($factory->user());

// Create verified user
$verifiedUserId = $factory->create($factory->verified());

// Create suspended user
$suspendedUserId = $factory->create($factory->suspended());

// Create user with password reset token
$userWithTokenId = $factory->create($factory->withResetToken());
```

### App Factory Helpers

```php
use App\Database\Factories\AppFactory;

$factory = new AppFactory();

// Create approved app
$appId = $factory->create($factory->approved());

// Create high trust score app
$highTrustAppId = $factory->create($factory->highTrust());

// Create low trust score app
$lowTrustAppId = $factory->create($factory->lowTrust());

// Create trending app
$trendingAppId = $factory->create($factory->trending());

// Create free Android app
$freeAndroidAppId = $factory->create($factory->free($factory->android()));

// Create paid iOS app
$paidiOSAppId = $factory->create($factory->paid($factory->ios()));
```

### Review Factory Helpers

```php
use App\Database\Factories\ReviewFactory;

$factory = new ReviewFactory();

// Create 5-star review
$reviewId = $factory->create($factory->fiveStars([
    'app_id' => 1,
    'user_id' => 1,
]));

// Create 1-star review
$reviewId = $factory->create($factory->oneStar([
    'app_id' => 1,
    'user_id' => 2,
]));

// Create approved review
$reviewId = $factory->create($factory->approved([
    'app_id' => 1,
    'user_id' => 3,
]));

// Create helpful review (high helpful count)
$reviewId = $factory->create($factory->helpful([
    'app_id' => 1,
    'user_id' => 4,
]));
```

### Scam Report Factory Helpers

```php
use App\Database\Factories\ScamReportFactory;

$factory = new ScamReportFactory();

// Create high risk scam report
$reportId = $factory->create($factory->highRisk([
    'app_id' => 1,
    'user_id' => 1,
]));

// Create medium risk scam report
$reportId = $factory->create($factory->mediumRisk([
    'app_id' => 1,
    'user_id' => 2,
]));

// Create low risk scam report
$reportId = $factory->create($factory->lowRisk([
    'app_id' => 1,
    'user_id' => 3,
]));

// Create verified scam report
$reportId = $factory->create($factory->verified([
    'app_id' => 1,
    'user_id' => 4,
]));
```

## Property-Based Testing

### Testing Data Validation

```php
use App\Database\Factories\AppFactory;
use App\Models\AppModel;

public function testAppFactoryGeneratesValidData()
{
    $factory = new AppFactory();
    $model = new AppModel();
    
    // Generate 100 random apps and verify all pass validation
    for ($i = 0; $i < 100; $i++) {
        $appData = $factory->make();
        $this->assertTrue($model->validate($appData));
    }
}
```

### Testing Trust Score Range

```php
use App\Database\Factories\AppFactory;

public function testTrustScoreAlwaysInRange()
{
    $factory = new AppFactory();
    
    // Generate 100 random apps
    for ($i = 0; $i < 100; $i++) {
        $appData = $factory->make();
        
        // Verify trust score is always 0-100
        $this->assertGreaterThanOrEqual(0, $appData['trust_score']);
        $this->assertLessThanOrEqual(100, $appData['trust_score']);
        
        // Verify security score is always 0-25
        $this->assertGreaterThanOrEqual(0, $appData['security_score']);
        $this->assertLessThanOrEqual(25, $appData['security_score']);
        
        // Verify developer reputation is always 0-20
        $this->assertGreaterThanOrEqual(0, $appData['developer_reputation']);
        $this->assertLessThanOrEqual(20, $appData['developer_reputation']);
    }
}
```

### Testing Review Text Length

```php
use App\Database\Factories\ReviewFactory;

public function testReviewTextAlwaysMeetsRequirements()
{
    $factory = new ReviewFactory();
    
    // Generate 100 random reviews
    for ($i = 0; $i < 100; $i++) {
        $reviewData = $factory->make(['app_id' => 1, 'user_id' => 1]);
        
        // Verify review text is always 50-2000 chars
        $length = strlen($reviewData['review_text']);
        $this->assertGreaterThanOrEqual(50, $length);
        $this->assertLessThanOrEqual(2000, $length);
    }
}
```

## Database Seeding

### Complete Platform Seed

```php
use App\Database\Factories\UserFactory;
use App\Database\Factories\AppFactory;
use App\Database\Factories\ReviewFactory;
use App\Database\Factories\ScamReportFactory;
use App\Database\Factories\CategoryFactory;
use App\Database\Factories\BlogPostFactory;
use App\Database\Factories\NewsletterSubscriberFactory;
use App\Database\Factories\SettingFactory;
use App\Database\Factories\ActivityLogFactory;
use App\Models\AppModel;

// Create settings
$settingFactory = new SettingFactory();
$settingFactory->createTrustAlgorithmWeights();

// Create categories
$categoryFactory = new CategoryFactory();
$categoryIds = $categoryFactory->createAllPredefined();

// Create users (50 regular, 5 admin)
$userFactory = new UserFactory();
$regularUserIds = $userFactory->createMany(50, $userFactory->verified());
$adminUserIds = $userFactory->createMany(5, $userFactory->admin());
$allUserIds = array_merge($regularUserIds, $adminUserIds);

// Create newsletter subscribers
$subscriberFactory = new NewsletterSubscriberFactory();
$subscriberFactory->createMany(100, $subscriberFactory->confirmed());

// Create apps (100 approved, 20 pending)
$appFactory = new AppFactory();
$approvedAppIds = $appFactory->createMany(100, $appFactory->approved());
$pendingAppIds = $appFactory->createMany(20, $appFactory->pending());

// Attach categories to apps
$appModel = new AppModel();
foreach ($approvedAppIds as $appId) {
    $randomCategories = array_rand(array_flip($categoryIds), rand(1, 3));
    $appModel->attachCategories($appId, is_array($randomCategories) ? $randomCategories : [$randomCategories]);
}

// Create reviews for approved apps
$reviewFactory = new ReviewFactory();
foreach ($approvedAppIds as $appId) {
    $reviewCount = rand(5, 30);
    $usedUserIds = [];
    
    for ($i = 0; $i < $reviewCount; $i++) {
        // Ensure one review per user per app
        $availableUserIds = array_diff($allUserIds, $usedUserIds);
        if (empty($availableUserIds)) break;
        
        $userId = $availableUserIds[array_rand($availableUserIds)];
        $usedUserIds[] = $userId;
        
        $reviewFactory->create([
            'app_id' => $appId,
            'user_id' => $userId,
            'approval_status' => rand(1, 10) > 2 ? 'approved' : 'pending', // 80% approved
        ]);
    }
}

// Create scam reports for some apps
$scamFactory = new ScamReportFactory();
$appsWithScams = array_rand(array_flip($approvedAppIds), 30); // 30 apps have scam reports

foreach ($appsWithScams as $appId) {
    $reportCount = rand(1, 5);
    
    for ($i = 0; $i < $reportCount; $i++) {
        $scamFactory->create([
            'app_id' => $appId,
            'user_id' => $allUserIds[array_rand($allUserIds)],
            'approval_status' => rand(1, 10) > 3 ? 'approved' : 'pending', // 70% approved
        ]);
    }
}

// Create blog posts
$blogFactory = new BlogPostFactory();
foreach ($adminUserIds as $authorId) {
    // 5 published posts per author
    for ($i = 0; $i < 5; $i++) {
        $blogFactory->create([
            'author_id' => $authorId,
            'publication_status' => 'published',
            'published_at' => date('Y-m-d H:i:s', strtotime("-{$i} weeks")),
        ]);
    }
    
    // 2 draft posts per author
    $blogFactory->createMany(2, $blogFactory->draft(['author_id' => $authorId]));
}

// Create activity logs for trending apps
$activityFactory = new ActivityLogFactory();
$trendingApps = array_rand(array_flip($approvedAppIds), 20);

foreach ($trendingApps as $appId) {
    $activityFactory->createTrendingMetrics($appId);
}

echo "Database seeded successfully!\n";
echo "- Users: " . count($allUserIds) . "\n";
echo "- Apps: " . count($approvedAppIds) . " approved, " . count($pendingAppIds) . " pending\n";
echo "- Categories: " . count($categoryIds) . "\n";
echo "- Newsletter Subscribers: 100\n";
```

### Quick Test Data Seed

```php
use App\Database\Factories\UserFactory;
use App\Database\Factories\AppFactory;
use App\Database\Factories\ReviewFactory;
use App\Database\Factories\CategoryFactory;
use App\Models\AppModel;

// Create minimal test data
$userFactory = new UserFactory();
$userId = $userFactory->create($userFactory->verified());
$adminId = $userFactory->create($userFactory->admin());

$categoryFactory = new CategoryFactory();
$categoryIds = $categoryFactory->createAllPredefined();

$appFactory = new AppFactory();
$appId = $appFactory->create($appFactory->approved());

$appModel = new AppModel();
$appModel->attachCategories($appId, [$categoryIds[0]]);

$reviewFactory = new ReviewFactory();
$reviewFactory->create([
    'app_id' => $appId,
    'user_id' => $userId,
    'approval_status' => 'approved',
]);

echo "Quick test data created!\n";
echo "User ID: {$userId}\n";
echo "Admin ID: {$adminId}\n";
echo "App ID: {$appId}\n";
```

## Advanced Patterns

### Creating Related Data in Bulk

```php
use App\Database\Factories\AppFactory;
use App\Database\Factories\ReviewFactory;
use App\Database\Factories\UserFactory;

// Create 10 apps with 20 reviews each
$appFactory = new AppFactory();
$reviewFactory = new ReviewFactory();
$userFactory = new UserFactory();

// Create users first
$userIds = $userFactory->createMany(50, $userFactory->verified());

// Create apps with reviews
for ($i = 0; $i < 10; $i++) {
    $appId = $appFactory->create($appFactory->approved());
    
    // Create 20 reviews for this app
    for ($j = 0; $j < 20; $j++) {
        $reviewFactory->create([
            'app_id' => $appId,
            'user_id' => $userIds[$j],
            'approval_status' => 'approved',
        ]);
    }
}
```

### Creating Test Scenarios

```php
use App\Database\Factories\AppFactory;
use App\Database\Factories\ScamReportFactory;
use App\Database\Factories\UserFactory;

// Scenario: High-risk app with multiple scam reports
$appFactory = new AppFactory();
$scamFactory = new ScamReportFactory();
$userFactory = new UserFactory();

$userIds = $userFactory->createMany(10, $userFactory->verified());

// Create low trust score app
$appId = $appFactory->create($appFactory->lowTrust());

// Create multiple high-risk scam reports
for ($i = 0; $i < 5; $i++) {
    $scamFactory->create($scamFactory->highRisk([
        'app_id' => $appId,
        'user_id' => $userIds[$i],
        'approval_status' => 'approved',
    ]));
}
```

## Tips and Best Practices

1. **Always create users before creating reviews or scam reports** - They require valid user_id foreign keys.

2. **Use helper methods for common scenarios** - They provide sensible defaults and make code more readable.

3. **Combine helper methods** - You can chain multiple helper methods:
   ```php
   $appFactory->create($appFactory->highTrust($appFactory->android()));
   ```

4. **Use makeMany() for property-based testing** - It's faster than create() when you don't need database records.

5. **Override specific fields when needed** - All factories support overrides:
   ```php
   $factory->create(['email' => 'specific@example.com']);
   ```

6. **Create categories first** - Many apps will need to be associated with categories.

7. **Respect unique constraints** - Each user can only review an app once. Track used user IDs when creating multiple reviews.

8. **Use realistic data for demos** - The factories generate realistic data that looks good in screenshots and demos.

9. **Seed in the right order** - Settings → Categories → Users → Apps → Reviews/Scam Reports → Blog Posts

10. **Clean up test data** - Use database transactions in tests to automatically rollback changes.
