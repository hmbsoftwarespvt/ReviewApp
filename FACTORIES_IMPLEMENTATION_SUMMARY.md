# Model Factories Implementation Summary

## Task 5: Models - Create Model Factories for Testing

**Status:** ✅ COMPLETED

All factory classes have been successfully created for generating realistic test data for all models.

---

## Overview

The factory system provides a clean, fluent API for generating test data that passes model validation. All factories extend `BaseFactory` which provides common functionality like `make()`, `makeMany()`, `create()`, and `createMany()` methods.

### Factory Architecture

```
BaseFactory (abstract)
├── UserFactory
├── AppFactory
├── ReviewFactory
├── ScamReportFactory
├── CategoryFactory
├── BlogPostFactory
├── ScreenshotFactory
├── NewsletterSubscriberFactory
├── SettingFactory
├── ActivityLogFactory
└── ReviewHelpfulVoteFactory
```

---

## Created Factories

### 1. **BaseFactory** (`app/Database/Factories/BaseFactory.php`)

Abstract base class providing common factory functionality.

**Key Methods:**
- `make(array $overrides = []): array` - Generate a single record (data only)
- `makeMany(int $count, array $overrides = []): array` - Generate multiple records
- `create(array $overrides = []): int` - Generate and insert a single record
- `createMany(int $count, array $overrides = []): array` - Generate and insert multiple records
- `getModel()` - Abstract method to get the model instance
- `mergeOverrides(array $data, array $overrides): array` - Merge custom data with generated data

**Features:**
- Uses Faker for realistic data generation
- Supports data overrides for customization
- Automatic model insertion with error handling
- Consistent API across all factories

---

### 2. **UserFactory** (`app/Database/Factories/UserFactory.php`)

Generates user data with hashed passwords and authentication fields.

**Generated Fields:**
- `username` - Unique username
- `email` - Unique email address
- `password_hash` - Hashed password (default: "password123")
- `role` - user or admin
- `status` - active, suspended, or deleted
- `email_verified` - Boolean (80% verified)
- `verification_token` - Token for unverified users
- `failed_login_count` - 0-3 failed attempts
- `last_login` - Recent login timestamp

**Helper Methods:**
- `admin()` - Generate admin user
- `user()` - Generate regular user
- `verified()` - Generate verified user
- `suspended()` - Generate suspended user
- `withResetToken()` - Generate user with password reset token

**Example Usage:**
```php
$userFactory = new \App\Database\Factories\UserFactory();

// Generate data only
$userData = $userFactory->make();

// Create and insert user
$userId = $userFactory->create(['email' => 'test@example.com']);

// Create admin user
$adminId = $userFactory->create($userFactory->admin());

// Create multiple verified users
$userIds = $userFactory->createMany(10, $userFactory->verified());
```

---

### 3. **AppFactory** (`app/Database/Factories/AppFactory.php`)

Generates app data with realistic information and trust scores.

**Generated Fields:**
- `name` - App name (3 words)
- `slug` - URL-friendly slug
- `description` - Multi-paragraph description
- `version` - Semantic version (e.g., "1.2.3")
- `size` - File size (5MB - 250MB)
- `platform_type` - android, ios, web, or desktop
- `price` - 0.00 to 19.99
- `developer_name` - Company name
- `release_date` - Date within last 3 years
- `download_url` - URL
- `trust_score` - 0-100
- `security_score` - 0-25
- `developer_reputation` - 0-20
- `view_count` - 0-10000
- `trending_score` - 0-50
- `approval_status` - pending, approved, or rejected
- `permissions` - JSON array of permissions
- `has_encryption` - Boolean (60% true)
- `third_party_sdk_count` - 0-15

**Helper Methods:**
- `approved()` - Generate approved app
- `pending()` - Generate pending app
- `highTrust()` - Generate high trust score app (80-100)
- `lowTrust()` - Generate low trust score app (0-49)
- `trending()` - Generate trending app
- `free()` - Generate free app
- `paid()` - Generate paid app
- `android()` - Generate Android app
- `ios()` - Generate iOS app
- `web()` - Generate web app

**Example Usage:**
```php
$appFactory = new \App\Database\Factories\AppFactory();

// Create approved Android app
$appId = $appFactory->create($appFactory->approved(['platform_type' => 'android']));

// Create high trust iOS app
$appId = $appFactory->create($appFactory->highTrust($appFactory->ios()));

// Create 20 approved apps
$appIds = $appFactory->createMany(20, $appFactory->approved());
```

---

### 4. **ReviewFactory** (`app/Database/Factories/ReviewFactory.php`)

Generates review data with ratings 1-5 and realistic text.

**Generated Fields:**
- `app_id` - Must be provided
- `user_id` - Must be provided
- `rating` - 1-5 stars
- `title` - Rating-appropriate title
- `review_text` - 50-2000 characters, rating-appropriate
- `pros` - Optional pros (70% chance)
- `cons` - Optional cons (70% chance)
- `approval_status` - pending, approved, or rejected
- `helpful_count` - 0-50

**Helper Methods:**
- `approved()` - Generate approved review
- `pending()` - Generate pending review
- `rejected()` - Generate rejected review
- `fiveStars()` - Generate 5-star review
- `oneStar()` - Generate 1-star review
- `helpful()` - Generate review with high helpful count

**Smart Features:**
- Titles match rating sentiment (positive for 4-5 stars, negative for 1-2 stars)
- Review text matches rating sentiment
- Automatically meets 50-character minimum requirement
- Realistic pros and cons

**Example Usage:**
```php
$reviewFactory = new \App\Database\Factories\ReviewFactory();

// Create approved 5-star review
$reviewId = $reviewFactory->create([
    'app_id' => 1,
    'user_id' => 1,
    'rating' => 5,
    'approval_status' => 'approved',
]);

// Create multiple reviews for an app
for ($i = 0; $i < 10; $i++) {
    $reviewFactory->create([
        'app_id' => 1,
        'user_id' => $i + 1,
    ]);
}
```

---

### 5. **ScamReportFactory** (`app/Database/Factories/ScamReportFactory.php`)

Generates scam report data with risk levels and evidence.

**Generated Fields:**
- `app_id` - Must be provided
- `user_id` - Must be provided
- `title` - Risk-appropriate title
- `description` - 100-3000 characters, risk-appropriate
- `risk_level` - low, medium, or high
- `evidence_urls` - JSON array (0-5 URLs)
- `approval_status` - pending, approved, or rejected
- `verification_notes` - Optional notes (40% chance)

**Helper Methods:**
- `approved()` - Generate approved report
- `pending()` - Generate pending report
- `highRisk()` - Generate high risk report
- `mediumRisk()` - Generate medium risk report
- `lowRisk()` - Generate low risk report
- `verified()` - Generate verified report with notes

**Smart Features:**
- Titles match risk level (critical for high, suspicious for medium, minor for low)
- Descriptions match risk level severity
- Automatically meets 100-character minimum
- Evidence URLs limited to 5 (validation requirement)

**Example Usage:**
```php
$scamFactory = new \App\Database\Factories\ScamReportFactory();

// Create high risk scam report
$reportId = $scamFactory->create([
    'app_id' => 1,
    'user_id' => 1,
    'risk_level' => 'high',
    'approval_status' => 'approved',
]);
```

---

### 6. **CategoryFactory** (`app/Database/Factories/CategoryFactory.php`)

Generates category data with predefined categories support.

**Generated Fields:**
- `name` - Category name (2 words)
- `slug` - URL-friendly slug
- `description` - Category description
- `icon` - FontAwesome icon class
- `display_order` - 0-100

**Helper Methods:**
- `predefined(string $categoryName)` - Generate predefined category
- `createAllPredefined()` - Create all 13 predefined categories

**Predefined Categories:**
1. Earning Apps
2. AI Tools
3. Video Editing
4. Finance
5. Shopping
6. Crypto
7. Design Tools
8. Social Media
9. Productivity
10. Gaming
11. Education
12. Health
13. Travel

**Example Usage:**
```php
$categoryFactory = new \App\Database\Factories\CategoryFactory();

// Create all predefined categories
$categoryIds = $categoryFactory->createAllPredefined();

// Create custom category
$categoryId = $categoryFactory->create(['name' => 'Custom Category']);
```

---

### 7. **BlogPostFactory** (`app/Database/Factories/BlogPostFactory.php`)

Generates blog post data with rich HTML content.

**Generated Fields:**
- `title` - Blog post title
- `slug` - URL-friendly slug
- `content` - Rich HTML content (5-15 paragraphs)
- `excerpt` - Short excerpt (3 paragraphs)
- `featured_image` - Image URL (70% chance)
- `author_id` - Must be provided
- `category` - guides, tips_tricks, scam_alerts, news_updates, or reviews
- `publication_status` - draft or published
- `published_at` - Publication timestamp
- `view_count` - 0-5000

**Helper Methods:**
- `published()` - Generate published post
- `draft()` - Generate draft post
- `guides()` - Generate guides category post
- `tipsTricks()` - Generate tips & tricks post
- `scamAlerts()` - Generate scam alerts post
- `newsUpdates()` - Generate news updates post
- `reviews()` - Generate reviews post
- `popular()` - Generate popular post (high view count)

**Smart Features:**
- Generates rich HTML content with headings, paragraphs, and lists
- Realistic content structure
- Published posts have publication dates

**Example Usage:**
```php
$blogFactory = new \App\Database\Factories\BlogPostFactory();

// Create published guide
$postId = $blogFactory->create([
    'author_id' => 1,
    'category' => 'guides',
    'publication_status' => 'published',
]);
```

---

### 8. **ScreenshotFactory** (`app/Database/Factories/ScreenshotFactory.php`)

Generates screenshot data for app galleries.

**Generated Fields:**
- `app_id` - Must be provided
- `filename` - Unique filename
- `file_path` - File path in uploads directory
- `display_order` - 0-10

**Helper Methods:**
- `forApp(int $appId, int $count = 5)` - Generate multiple screenshots for an app
- `createForApp(int $appId, int $count = 5)` - Create and insert multiple screenshots

**Example Usage:**
```php
$screenshotFactory = new \App\Database\Factories\ScreenshotFactory();

// Create 5 screenshots for an app
$screenshotIds = $screenshotFactory->createForApp(1, 5);
```

---

### 9. **NewsletterSubscriberFactory** (`app/Database/Factories/NewsletterSubscriberFactory.php`)

Generates newsletter subscriber data with tokens.

**Generated Fields:**
- `email` - Unique email address
- `unsubscribe_token` - Unique token
- `is_confirmed` - Boolean (70% confirmed)
- `confirmation_token` - Token for unconfirmed subscribers
- `email_count_today` - 0-5
- `last_email_date` - Recent date
- `subscribed_at` - Subscription timestamp
- `unsubscribed_at` - Null or unsubscribe timestamp

**Helper Methods:**
- `confirmed()` - Generate confirmed subscriber
- `unconfirmed()` - Generate unconfirmed subscriber
- `unsubscribed()` - Generate unsubscribed subscriber
- `atEmailLimit()` - Generate subscriber at daily email limit

**Example Usage:**
```php
$subscriberFactory = new \App\Database\Factories\NewsletterSubscriberFactory();

// Create confirmed subscriber
$subscriberId = $subscriberFactory->create($subscriberFactory->confirmed());
```

---

### 10. **SettingFactory** (`app/Database/Factories/SettingFactory.php`)

Generates platform settings with type casting.

**Generated Fields:**
- `setting_key` - Unique key (word.word format)
- `setting_value` - Type-appropriate value
- `setting_type` - string, integer, float, boolean, or json
- `description` - Setting description

**Helper Methods:**
- `trustAlgorithmWeights()` - Generate trust algorithm weight settings
- `createTrustAlgorithmWeights()` - Create and insert trust algorithm weights
- `emailSettings()` - Generate email notification settings
- `paginationSettings()` - Generate pagination settings

**Predefined Settings:**
- Trust algorithm weights (5 settings)
- Email settings (3 settings)
- Pagination settings (3 settings)

**Example Usage:**
```php
$settingFactory = new \App\Database\Factories\SettingFactory();

// Create trust algorithm weights
$settingIds = $settingFactory->createTrustAlgorithmWeights();

// Create custom setting
$settingId = $settingFactory->create([
    'setting_key' => 'custom.setting',
    'setting_value' => 'value',
    'setting_type' => 'string',
]);
```

---

### 11. **ActivityLogFactory** (`app/Database/Factories/ActivityLogFactory.php`)

Generates activity log data for trending calculations.

**Generated Fields:**
- `app_id` - Must be provided
- `activity_type` - view, review, or scam_report
- `activity_date` - Date within last 30 days
- `count` - 1-100

**Helper Methods:**
- `view()` - Generate view activity
- `review()` - Generate review activity
- `scamReport()` - Generate scam report activity
- `today()` - Generate today's activity
- `yesterday()` - Generate yesterday's activity
- `highActivity()` - Generate high activity count (500-5000)
- `create24HourMetrics(int $appId)` - Create 24-hour metrics for an app
- `createTrendingMetrics(int $appId)` - Create trending app metrics

**Example Usage:**
```php
$activityFactory = new \App\Database\Factories\ActivityLogFactory();

// Create 24-hour metrics for an app
$activityIds = $activityFactory->create24HourMetrics(1);

// Create trending metrics
$activityIds = $activityFactory->createTrendingMetrics(1);
```

---

### 12. **ReviewHelpfulVoteFactory** (`app/Database/Factories/ReviewHelpfulVoteFactory.php`)

Generates helpful vote data for reviews.

**Generated Fields:**
- `review_id` - Must be provided
- `user_id` - Must be provided

**Helper Methods:**
- `createVotesForReview(int $reviewId, array $userIds)` - Create votes from multiple users

**Smart Features:**
- Prevents duplicate votes (checks before inserting)
- Automatically increments review helpful_count

**Example Usage:**
```php
$voteFactory = new \App\Database\Factories\ReviewHelpfulVoteFactory();

// Create votes from multiple users
$userIds = [1, 2, 3, 4, 5];
$voteIds = $voteFactory->createVotesForReview(1, $userIds);
```

---

## Usage Patterns

### Basic Usage

```php
// Generate data only (no database insertion)
$factory = new \App\Database\Factories\UserFactory();
$userData = $factory->make();

// Generate with overrides
$userData = $factory->make(['email' => 'custom@example.com']);

// Create and insert
$userId = $factory->create(['email' => 'test@example.com']);

// Create multiple records
$userIds = $factory->createMany(10);
```

### Working with Relationships

```php
// Create user
$userFactory = new \App\Database\Factories\UserFactory();
$userId = $userFactory->create($userFactory->verified());

// Create app
$appFactory = new \App\Database\Factories\AppFactory();
$appId = $appFactory->create($appFactory->approved());

// Create review for app by user
$reviewFactory = new \App\Database\Factories\ReviewFactory();
$reviewId = $reviewFactory->create([
    'app_id' => $appId,
    'user_id' => $userId,
    'approval_status' => 'approved',
]);

// Create scam report
$scamFactory = new \App\Database\Factories\ScamReportFactory();
$reportId = $scamFactory->create([
    'app_id' => $appId,
    'user_id' => $userId,
    'risk_level' => 'high',
]);

// Create screenshots for app
$screenshotFactory = new \App\Database\Factories\ScreenshotFactory();
$screenshotIds = $screenshotFactory->createForApp($appId, 5);

// Attach categories to app
$categoryFactory = new \App\Database\Factories\CategoryFactory();
$categoryIds = $categoryFactory->createAllPredefined();
$appModel = new \App\Models\AppModel();
$appModel->attachCategories($appId, [$categoryIds[0], $categoryIds[1]]);
```

### Property-Based Testing Integration

Factories are designed to work seamlessly with property-based testing:

```php
// Generate random valid data for property tests
$factory = new \App\Database\Factories\AppFactory();

for ($i = 0; $i < 100; $i++) {
    $appData = $factory->make();
    // Test that data passes validation
    $model = new \App\Models\AppModel();
    $this->assertTrue($model->validate($appData));
}
```

### Seeding Database

```php
// Seed database with realistic data
$userFactory = new \App\Database\Factories\UserFactory();
$appFactory = new \App\Database\Factories\AppFactory();
$reviewFactory = new \App\Database\Factories\ReviewFactory();
$categoryFactory = new \App\Database\Factories\CategoryFactory();

// Create categories
$categoryIds = $categoryFactory->createAllPredefined();

// Create users
$userIds = $userFactory->createMany(50, $userFactory->verified());

// Create apps
$appIds = $appFactory->createMany(100, $appFactory->approved());

// Create reviews
foreach ($appIds as $appId) {
    $reviewCount = rand(5, 20);
    for ($i = 0; $i < $reviewCount; $i++) {
        $reviewFactory->create([
            'app_id' => $appId,
            'user_id' => $userIds[array_rand($userIds)],
            'approval_status' => 'approved',
        ]);
    }
}
```

---

## Validation Compliance

All factories generate data that passes model validation:

✅ **UserFactory**
- Unique username and email
- Valid password hash (60+ chars)
- Valid role (user/admin)
- Valid status (active/suspended/deleted)

✅ **AppFactory**
- Unique slug
- Valid platform type (android/ios/web/desktop)
- Valid approval status (pending/approved/rejected)
- Trust score 0-100
- Security score 0-25
- Developer reputation 0-20
- Valid price (>= 0)
- Valid URL format

✅ **ReviewFactory**
- Rating 1-5
- Review text 50-2000 chars
- Pros/cons max 1000 chars
- Valid approval status

✅ **ScamReportFactory**
- Description 100-3000 chars
- Valid risk level (low/medium/high)
- Evidence URLs max 5
- Valid approval status

✅ **CategoryFactory**
- Unique name and slug
- Valid slug format (alphanumeric with dashes)

✅ **BlogPostFactory**
- Unique slug
- Valid category (guides/tips_tricks/scam_alerts/news_updates/reviews)
- Valid publication status (draft/published)

✅ **ScreenshotFactory**
- Valid filename and file path
- Integer display order

✅ **NewsletterSubscriberFactory**
- Unique email
- Valid email format
- Unique unsubscribe token

✅ **SettingFactory**
- Unique setting key
- Valid setting type (string/integer/float/boolean/json)
- Type-appropriate values

✅ **ActivityLogFactory**
- Valid activity type (view/review/scam_report)
- Valid date format
- Count >= 0

✅ **ReviewHelpfulVoteFactory**
- Prevents duplicate votes
- Valid foreign keys

---

## Faker Integration

All factories use the Faker library for realistic data generation:

- **Names**: `$faker->userName()`, `$faker->company()`
- **Emails**: `$faker->safeEmail()`
- **Text**: `$faker->sentence()`, `$faker->paragraph()`, `$faker->paragraphs()`
- **Numbers**: `$faker->numberBetween()`, `$faker->randomFloat()`
- **Dates**: `$faker->dateTimeBetween()`
- **URLs**: `$faker->url()`, `$faker->imageUrl()`
- **Booleans**: `$faker->boolean()`
- **Arrays**: `$faker->randomElement()`, `$faker->randomElements()`

---

## Testing Recommendations

### Unit Tests
```php
public function testUserFactoryGeneratesValidData()
{
    $factory = new \App\Database\Factories\UserFactory();
    $userData = $factory->make();
    
    $model = new \App\Models\UserModel();
    $this->assertTrue($model->validate($userData));
}
```

### Integration Tests
```php
public function testFactoryCreatesRecordInDatabase()
{
    $factory = new \App\Database\Factories\UserFactory();
    $userId = $factory->create();
    
    $model = new \App\Models\UserModel();
    $user = $model->find($userId);
    
    $this->assertNotNull($user);
    $this->assertIsArray($user);
}
```

### Property-Based Tests
```php
public function testAppFactoryGeneratesValidTrustScores()
{
    $factory = new \App\Database\Factories\AppFactory();
    
    for ($i = 0; $i < 100; $i++) {
        $appData = $factory->make();
        $this->assertGreaterThanOrEqual(0, $appData['trust_score']);
        $this->assertLessThanOrEqual(100, $appData['trust_score']);
    }
}
```

---

## Acceptance Criteria Verification

✅ **Factories generate valid data that passes validation**
- All factories generate data compliant with model validation rules
- Tested with model validation methods
- Handles all field constraints (length, format, uniqueness)

✅ **Relationships can be created using factory methods**
- Factories support foreign key relationships
- Helper methods for creating related records
- Examples: `createForApp()`, `createVotesForReview()`, `create24HourMetrics()`

✅ **Faker library used for realistic data**
- All factories use Faker for data generation
- Realistic names, emails, text, dates, URLs
- Context-aware data (e.g., review text matches rating)

✅ **Factories work with property-based tests**
- Simple API: `make()` for data generation
- Supports overrides for custom scenarios
- Can generate hundreds of valid records for property testing
- No mocking required - generates real, valid data

---

## Files Created

1. `app/Database/Factories/BaseFactory.php` - Abstract base factory
2. `app/Database/Factories/UserFactory.php` - User data factory
3. `app/Database/Factories/AppFactory.php` - App data factory
4. `app/Database/Factories/ReviewFactory.php` - Review data factory
5. `app/Database/Factories/ScamReportFactory.php` - Scam report data factory
6. `app/Database/Factories/CategoryFactory.php` - Category data factory
7. `app/Database/Factories/BlogPostFactory.php` - Blog post data factory
8. `app/Database/Factories/ScreenshotFactory.php` - Screenshot data factory
9. `app/Database/Factories/NewsletterSubscriberFactory.php` - Newsletter subscriber data factory
10. `app/Database/Factories/SettingFactory.php` - Setting data factory
11. `app/Database/Factories/ActivityLogFactory.php` - Activity log data factory
12. `app/Database/Factories/ReviewHelpfulVoteFactory.php` - Review helpful vote data factory

**Total:** 12 factory classes created successfully ✅

---

## Next Steps

With all factories created, the following tasks can now proceed:
- **Task 6**: Implement TrustScoreService (can use factories for testing)
- **Task 7**: Implement SecurityScoreService (can use factories for testing)
- **Task 8**: Implement DeveloperReputationService (can use factories for testing)
- **Property-Based Tests**: Use factories to generate test data
- **Database Seeding**: Use factories to populate development/staging databases

---

**Implementation Date:** 2025
**CodeIgniter Version:** 4.5+
**PHP Version:** 8.2+
**Faker Version:** 1.24+
