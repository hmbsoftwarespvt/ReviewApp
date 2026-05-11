# Design Document: AppTrust Platform

## Overview

The AppTrust Platform is a comprehensive web application built on CodeIgniter 4 (PHP 8.2+) that provides app review, trust verification, and community-driven scam reporting capabilities. The platform serves two primary user groups: public visitors seeking app information and administrators managing content and moderation workflows.

### Core Objectives

1. **Trust Verification**: Provide transparent, algorithm-based trust scores for mobile and web applications
2. **Community Engagement**: Enable users to submit reviews and scam reports
3. **Content Management**: Offer administrators comprehensive tools for moderation and configuration
4. **Information Discovery**: Facilitate app search, categorization, and comparison

### Technology Stack

- **Framework**: CodeIgniter 4.5+
- **PHP Version**: 8.2+
- **Database**: MySQL 8.0+ / MariaDB 10.6+
- **Frontend**: Bootstrap 5, Alpine.js for interactivity
- **Caching**: Redis (primary) / File-based (fallback)
- **Email**: CodeIgniter Email library with SMTP
- **Session**: Database-backed sessions

## Architecture

### Architectural Pattern

The platform follows a **layered architecture** with clear separation of concerns:

```
┌─────────────────────────────────────────────────────────┐
│                    Presentation Layer                    │
│              (Controllers + Views + Assets)              │
└─────────────────────────────────────────────────────────┘
                            │
┌─────────────────────────────────────────────────────────┐
│                     Service Layer                        │
│        (Business Logic + Trust Algorithm + Events)       │
└─────────────────────────────────────────────────────────┘
                            │
┌─────────────────────────────────────────────────────────┐
│                   Repository Layer                       │
│              (Data Access + Query Building)              │
└─────────────────────────────────────────────────────────┘
                            │
┌─────────────────────────────────────────────────────────┐
│                      Data Layer                          │
│                (Models + Database)                       │
└─────────────────────────────────────────────────────────┘
```

### Directory Structure

```
app/
├── Config/
│   ├── Routes.php
│   ├── TrustScore.php          # Trust algorithm configuration
│   └── AppTrust.php            # Platform settings
├── Controllers/
│   ├── Public/
│   │   ├── HomeController.php
│   │   ├── AppController.php
│   │   ├── SearchController.php
│   │   ├── CategoryController.php
│   │   ├── ScamAlertController.php
│   │   ├── BlogController.php
│   │   ├── ComparisonController.php
│   │   └── AuthController.php
│   └── Admin/
│       ├── DashboardController.php
│       ├── AppManagementController.php
│       ├── ReviewModerationController.php
│       ├── ScamReportModerationController.php
│       ├── UserManagementController.php
│       ├── BlogManagementController.php
│       ├── CategoryManagementController.php
│       └── SettingsController.php
├── Models/
│   ├── AppModel.php
│   ├── UserModel.php
│   ├── ReviewModel.php
│   ├── ScamReportModel.php
│   ├── CategoryModel.php
│   ├── BlogPostModel.php
│   ├── NewsletterSubscriberModel.php
│   ├── ScreenshotModel.php
│   └── SettingModel.php
├── Services/
│   ├── TrustScoreService.php
│   ├── SecurityScoreService.php
│   ├── DeveloperReputationService.php
│   ├── TrendingService.php
│   ├── RecommendationService.php
│   ├── NotificationService.php
│   └── SearchService.php
├── Repositories/
│   ├── AppRepository.php
│   ├── ReviewRepository.php
│   ├── ScamReportRepository.php
│   └── UserRepository.php
├── Events/
│   ├── ReviewApproved.php
│   ├── ReviewRejected.php
│   ├── ScamReportApproved.php
│   └── AppDataChanged.php
├── Listeners/
│   ├── RecalculateTrustScore.php
│   ├── SendScamAlert.php
│   └── UpdateTrendingApps.php
├── Filters/
│   ├── AuthFilter.php
│   ├── AdminFilter.php
│   ├── RateLimitFilter.php
│   └── CSRFFilter.php
├── Libraries/
│   ├── TrustAlgorithm.php
│   └── CacheManager.php
└── Views/
    ├── public/
    │   ├── home.php
    │   ├── app_detail.php
    │   ├── search_results.php
    │   ├── category.php
    │   ├── scam_alerts.php
    │   ├── blog/
    │   ├── comparison.php
    │   └── auth/
    └── admin/
        ├── dashboard.php
        ├── apps/
        ├── reviews/
        ├── scam_reports/
        ├── users/
        ├── blog/
        └── settings/
```

### Design Patterns

1. **Repository Pattern**: Abstracts data access logic from business logic
2. **Service Layer Pattern**: Encapsulates complex business operations
3. **Event-Driven Architecture**: Decouples trust score recalculation and notifications
4. **Factory Pattern**: Used for creating trust score calculators
5. **Strategy Pattern**: Allows different trust score calculation strategies
6. **Observer Pattern**: Implemented via CodeIgniter's Events system

## Components and Interfaces

### 1. Trust Score Service

**Purpose**: Orchestrates trust score calculation using configurable weights and component services.

**Interface**:

```php
namespace App\Services;

class TrustScoreService
{
    public function calculateTrustScore(int $appId): float;
    public function getTrustScoreBreakdown(int $appId): array;
    public function recalculateAllScores(): void;
    public function getScoreColor(float $score): string;
}
```

**Key Methods**:

- `calculateTrustScore()`: Computes final score (0-100) using weighted components
- `getTrustScoreBreakdown()`: Returns array with individual component contributions
- `recalculateAllScores()`: Batch recalculation for all apps (admin function)
- `getScoreColor()`: Returns CSS class based on score range

**Dependencies**:
- `SecurityScoreService`
- `DeveloperReputationService`
- `ReviewRepository`
- `ScamReportRepository`
- `AppRepository`
- `SettingModel` (for weights configuration)

### 2. Security Score Service

**Purpose**: Calculates security component (0-25 points) based on permissions and encryption.

**Interface**:

```php
namespace App\Services;

class SecurityScoreService
{
    public function calculateSecurityScore(int $appId): float;
    public function analyzePermissions(array $permissions): float;
    public function checkEncryption(int $appId): bool;
    public function countThirdPartySDKs(int $appId): int;
}
```

**Calculation Logic**:
- Permission count: 2-8 points
- Sensitive permissions: -3 points each (location, contacts, camera, microphone)
- Encryption: +5 points
- Third-party SDKs: -2 points if > 5

### 3. Developer Reputation Service

**Purpose**: Calculates developer reputation (0-20 points) based on publisher history.

**Interface**:

```php
namespace App\Services;

class DeveloperReputationService
{
    public function calculateReputation(string $developerName): float;
    public function getDeveloperStats(string $developerName): array;
    public function getAverageTrustScore(string $developerName): float;
}
```

**Calculation Logic**:
- App count: 1-5 points
- Average trust score: 2-10 points
- Total scam reports: -5 points if > 20

### 4. Trending Service

**Purpose**: Calculates and updates daily trending apps based on activity metrics.

**Interface**:

```php
namespace App\Services;

class TrendingService
{
    public function calculateTrendingScore(int $appId): float;
    public function updateDailyTrending(): void;
    public function getTrendingApps(int $limit = 12): array;
}
```

**Trending Score Formula**:
```
trending_score = (views_24h > 100 ? 10 : 0) 
               + (reviews_24h > 10 ? 15 : 0) 
               - (scam_reports_24h > 5 ? 20 : 0)
```

### 5. Recommendation Service

**Purpose**: Generates similar app recommendations based on category and features.

**Interface**:

```php
namespace App\Services;

class RecommendationService
{
    public function getSimilarApps(int $appId, int $limit = 6): array;
    public function calculateSimilarity(int $appId1, int $appId2): float;
}
```

**Similarity Algorithm**:
1. Category match: +50 points
2. Trust score proximity (±10): +30 points
3. Same platform type: +20 points

### 6. Notification Service

**Purpose**: Handles email notifications for scam alerts and newsletters.

**Interface**:

```php
namespace App\Services;

class NotificationService
{
    public function sendScamAlert(int $scamReportId): void;
    public function sendWelcomeEmail(int $userId): void;
    public function sendNewsletterConfirmation(string $email): void;
    public function checkDailyLimit(string $email): bool;
}
```

**Rate Limiting**: Maximum 5 emails per subscriber per day.

### 7. Search Service

**Purpose**: Provides full-text search with relevance ranking.

**Interface**:

```php
namespace App\Services;

class SearchService
{
    public function search(string $query, array $filters = []): array;
    public function buildSearchQuery(string $query): string;
    public function highlightMatches(string $text, string $query): string;
}
```

**Search Fields**:
- App name (weight: 3x)
- Developer name (weight: 2x)
- Description (weight: 1x)

### 8. App Repository

**Purpose**: Encapsulates all database operations for apps.

**Interface**:

```php
namespace App\Repositories;

class AppRepository
{
    public function find(int $id): ?array;
    public function findBySlug(string $slug): ?array;
    public function getAll(array $filters = [], int $page = 1, int $perPage = 24): array;
    public function create(array $data): int;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
    public function incrementViewCount(int $id): void;
    public function getByCategory(int $categoryId, int $page = 1): array;
    public function getByDeveloper(string $developerName): array;
    public function getTrending(int $limit = 12): array;
}
```

### 9. Review Repository

**Purpose**: Manages review data access with moderation status filtering.

**Interface**:

```php
namespace App\Repositories;

class ReviewRepository
{
    public function find(int $id): ?array;
    public function getByApp(int $appId, string $status = 'approved', int $page = 1): array;
    public function getByUser(int $userId): array;
    public function getPending(int $page = 1): array;
    public function create(array $data): int;
    public function updateStatus(int $id, string $status): bool;
    public function delete(int $id): bool;
    public function getAverageRating(int $appId): float;
    public function getReviewCount(int $appId, string $status = 'approved'): int;
    public function userHasReviewed(int $userId, int $appId): bool;
    public function incrementHelpfulCount(int $id): void;
}
```

### 10. Scam Report Repository

**Purpose**: Manages scam report data with verification workflows.

**Interface**:

```php
namespace App\Repositories;

class ScamReportRepository
{
    public function find(int $id): ?array;
    public function getByApp(int $appId, string $status = 'approved', int $page = 1): array;
    public function getPending(int $page = 1): array;
    public function getAll(array $filters = [], int $page = 1): array;
    public function create(array $data): int;
    public function updateStatus(int $id, string $status, ?string $notes = null): bool;
    public function updateRiskLevel(int $id, string $riskLevel): bool;
    public function delete(int $id): bool;
    public function getCountByApp(int $appId, string $status = 'approved'): int;
    public function getCountByRiskLevel(int $appId, string $riskLevel): int;
}
```

## Data Models

### Database Schema

#### apps Table

```sql
CREATE TABLE apps (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    description TEXT,
    version VARCHAR(50),
    size VARCHAR(50),
    platform_type ENUM('android', 'ios', 'web', 'desktop') NOT NULL,
    price DECIMAL(10, 2) DEFAULT 0.00,
    developer_name VARCHAR(255) NOT NULL,
    release_date DATE,
    download_url VARCHAR(500),
    trust_score DECIMAL(5, 2) DEFAULT 0.00,
    security_score DECIMAL(5, 2) DEFAULT 0.00,
    developer_reputation DECIMAL(5, 2) DEFAULT 0.00,
    view_count INT UNSIGNED DEFAULT 0,
    trending_score DECIMAL(8, 2) DEFAULT 0.00,
    approval_status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    permissions JSON,
    has_encryption BOOLEAN DEFAULT FALSE,
    third_party_sdk_count INT DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_slug (slug),
    INDEX idx_developer (developer_name),
    INDEX idx_trust_score (trust_score),
    INDEX idx_trending (trending_score),
    INDEX idx_approval (approval_status),
    INDEX idx_platform (platform_type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

#### app_categories Table (Many-to-Many)

```sql
CREATE TABLE app_categories (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    app_id INT UNSIGNED NOT NULL,
    category_id INT UNSIGNED NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (app_id) REFERENCES apps(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE,
    UNIQUE KEY unique_app_category (app_id, category_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

#### categories Table

```sql
CREATE TABLE categories (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    slug VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    icon VARCHAR(100),
    display_order INT DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_slug (slug),
    INDEX idx_order (display_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

#### users Table

```sql
CREATE TABLE users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(255) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin') DEFAULT 'user',
    status ENUM('active', 'suspended', 'deleted') DEFAULT 'active',
    email_verified BOOLEAN DEFAULT FALSE,
    verification_token VARCHAR(100),
    reset_token VARCHAR(100),
    reset_token_expires DATETIME,
    failed_login_count INT DEFAULT 0,
    last_failed_login DATETIME,
    account_locked_until DATETIME,
    last_login DATETIME,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_username (username),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

#### reviews Table

```sql
CREATE TABLE reviews (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    app_id INT UNSIGNED NOT NULL,
    user_id INT UNSIGNED NOT NULL,
    rating TINYINT UNSIGNED NOT NULL CHECK (rating BETWEEN 1 AND 5),
    title VARCHAR(255) NOT NULL,
    review_text TEXT NOT NULL,
    pros TEXT,
    cons TEXT,
    approval_status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    helpful_count INT UNSIGNED DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (app_id) REFERENCES apps(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_app_review (user_id, app_id),
    INDEX idx_app (app_id),
    INDEX idx_user (user_id),
    INDEX idx_status (approval_status),
    INDEX idx_rating (rating),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

#### scam_reports Table

```sql
CREATE TABLE scam_reports (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    app_id INT UNSIGNED NOT NULL,
    user_id INT UNSIGNED NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    risk_level ENUM('low', 'medium', 'high') NOT NULL,
    evidence_urls JSON,
    approval_status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    verification_notes TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (app_id) REFERENCES apps(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_app (app_id),
    INDEX idx_user (user_id),
    INDEX idx_status (approval_status),
    INDEX idx_risk (risk_level),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

#### screenshots Table

```sql
CREATE TABLE screenshots (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    app_id INT UNSIGNED NOT NULL,
    filename VARCHAR(255) NOT NULL,
    file_path VARCHAR(500) NOT NULL,
    display_order INT DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (app_id) REFERENCES apps(id) ON DELETE CASCADE,
    INDEX idx_app (app_id),
    INDEX idx_order (display_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

#### blog_posts Table

```sql
CREATE TABLE blog_posts (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    content LONGTEXT NOT NULL,
    excerpt TEXT,
    featured_image VARCHAR(500),
    author_id INT UNSIGNED NOT NULL,
    category ENUM('guides', 'tips_tricks', 'scam_alerts', 'news_updates', 'reviews') NOT NULL,
    publication_status ENUM('draft', 'published') DEFAULT 'draft',
    published_at DATETIME,
    view_count INT UNSIGNED DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (author_id) REFERENCES users(id),
    INDEX idx_slug (slug),
    INDEX idx_status (publication_status),
    INDEX idx_category (category),
    INDEX idx_published (published_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

#### newsletter_subscribers Table

```sql
CREATE TABLE newsletter_subscribers (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    unsubscribe_token VARCHAR(100) UNIQUE NOT NULL,
    is_confirmed BOOLEAN DEFAULT FALSE,
    confirmation_token VARCHAR(100),
    email_count_today INT DEFAULT 0,
    last_email_date DATE,
    subscribed_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    unsubscribed_at DATETIME,
    INDEX idx_email (email),
    INDEX idx_confirmed (is_confirmed)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

#### settings Table

```sql
CREATE TABLE settings (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) NOT NULL UNIQUE,
    setting_value TEXT,
    setting_type ENUM('string', 'integer', 'float', 'boolean', 'json') DEFAULT 'string',
    description TEXT,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_key (setting_key)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

#### review_helpful_votes Table

```sql
CREATE TABLE review_helpful_votes (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    review_id INT UNSIGNED NOT NULL,
    user_id INT UNSIGNED NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (review_id) REFERENCES reviews(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_review_vote (user_id, review_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

#### activity_logs Table (24-hour metrics)

```sql
CREATE TABLE activity_logs (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    app_id INT UNSIGNED NOT NULL,
    activity_type ENUM('view', 'review', 'scam_report') NOT NULL,
    activity_date DATE NOT NULL,
    count INT UNSIGNED DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (app_id) REFERENCES apps(id) ON DELETE CASCADE,
    UNIQUE KEY unique_app_activity_date (app_id, activity_type, activity_date),
    INDEX idx_date (activity_date),
    INDEX idx_app (app_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### Model Relationships

```
App (1) ──────< (M) Review
App (1) ──────< (M) ScamReport
App (1) ──────< (M) Screenshot
App (M) ──────< (M) Category (via app_categories)
User (1) ──────< (M) Review
User (1) ──────< (M) ScamReport
User (1) ──────< (M) BlogPost (as author)
Review (1) ──────< (M) ReviewHelpfulVote
```

### Data Validation Rules

**App Entry**:
- `name`: required, max 255 chars
- `slug`: required, unique, alphanumeric with hyphens
- `platform_type`: required, enum
- `developer_name`: required, max 255 chars
- `price`: numeric, min 0
- `permissions`: valid JSON array
- `download_url`: valid URL format

**Review**:
- `rating`: required, integer 1-5
- `title`: required, max 255 chars
- `review_text`: required, min 50 chars, max 2000 chars
- `pros`: optional, max 1000 chars
- `cons`: optional, max 1000 chars

**Scam Report**:
- `title`: required, max 255 chars
- `description`: required, min 100 chars, max 3000 chars
- `risk_level`: required, enum
- `evidence_urls`: valid JSON array, max 5 URLs

**User**:
- `username`: required, unique, alphanumeric with underscores, 3-50 chars
- `email`: required, unique, valid email format
- `password`: min 8 chars, must contain uppercase, lowercase, number

## Correctness Properties

*A property is a characteristic or behavior that should hold true across all valid executions of a system—essentially, a formal statement about what the system should do. Properties serve as the bridge between human-readable specifications and machine-verifiable correctness guarantees.*

Before writing properties, I need to analyze the acceptance criteria for testability using the prework tool.

<function_calls>
<invoke name="prework">
<parameter name="featureName">app-trust-platform

### Property Reflection

After analyzing all acceptance criteria, I've identified the following testable properties. Let me review for redundancy:

**Identified Properties:**
1. App data persistence (1.4)
2. Cascade deletion (1.5)
3. Search correctness (1.7)
4. Trust score range constraint (2.1)
5. Trust score component sum (2.2)
6. Score color mapping (3.2-3.4)
7. User account creation (4.2)
8. Session creation (4.4)
9. Failed login increment (4.5)
10. Password reset token generation (4.8)
11. Review data persistence (5.2)
12. Review initial status (5.3)
13. Review associations (5.4)
14. Review uniqueness constraint (5.5)
15. Review text minimum length validation (5.7)
16. Review text maximum length validation (5.8)
17. Approved reviews filter (7.1)
18. Review sorting (7.2)
19. Helpful vote increment (7.8)
20. Scam report data persistence (8.2-8.4)
21. Scam report description minimum length (8.5)
22. Scam report description maximum length (8.6)
23. Evidence URL limit (8.8)
24. View count increment (15.8)
25. Similar apps matching (16.1-16.3)
26. Email validation (20.4)
27. Email duplicate checking (20.5)
28. Email rate limiting (21.6)
29. User deletion anonymization (23.8)
30. Security score calculation (24.1-24.8)
31. Developer reputation calculation (25.1-25.9)

**Redundancy Analysis:**
- Properties 11, 12, 13 can be combined into one comprehensive "Review submission" property
- Properties 15 and 16 can be combined into one "Review text validation" property
- Properties 21 and 22 can be combined into one "Scam report description validation" property
- Properties 30 and 31 are complex algorithms that should remain separate

**Final Property Set (after removing redundancy):**

### Property 1: App Data Persistence
*For any* valid app entry data containing name, description, version, size, platform type, price, category, developer name, release date, and download URL, creating an app entry and then retrieving it should return all fields with matching values.

**Validates: Requirements 1.4**

### Property 2: Cascade Deletion
*For any* app entry with associated reviews, scam reports, and screenshots, deleting the app entry should remove all associated records from the database.

**Validates: Requirements 1.5**

### Property 3: Search Correctness
*For any* search query string, all returned app entries should contain the query string in either the app name or developer name field (case-insensitive).

**Validates: Requirements 1.7**

### Property 4: Trust Score Range Constraint
*For any* app entry with any combination of reviews, scam reports, security data, and age, the calculated trust score should be a value between 0 and 100 (inclusive).

**Validates: Requirements 2.1**

### Property 5: Trust Score Component Sum
*For any* app entry, the sum of the five weighted trust score components (review rating contribution + security score + developer reputation + scam report impact + app age contribution) should equal the total trust score.

**Validates: Requirements 2.2**

### Property 6: Score Color Mapping
*For any* trust score value, the color classification function should return "green" for scores 80-100, "yellow" for scores 50-79, and "red" for scores 0-49.

**Validates: Requirements 3.2, 3.3, 3.4**

### Property 7: User Account Creation
*For any* valid registration data (unique email, unique username, valid password), creating a user account should persist all provided fields and set the initial status to "active" with email_verified as false.

**Validates: Requirements 4.2**

### Property 8: Session Creation
*For any* valid login credentials (matching email/username and password), successful authentication should create a session with an expiration time of exactly 30 days from the login timestamp.

**Validates: Requirements 4.4**

### Property 9: Failed Login Increment
*For any* invalid login credentials, a failed login attempt should increment the user's failed_login_count by exactly 1 and update the last_failed_login timestamp.

**Validates: Requirements 4.5**

### Property 10: Password Reset Token Generation
*For any* valid email address associated with an existing user account, requesting a password reset should generate a unique reset token with an expiration time of exactly 60 minutes from the request timestamp.

**Validates: Requirements 4.8**

### Property 11: Review Submission Completeness
*For any* valid review submission (rating 1-5, title, text 50-2000 chars, optional pros/cons), creating a review should persist all fields, set approval_status to "pending", and correctly associate the review with both the user_id and app_id.

**Validates: Requirements 5.2, 5.3, 5.4**

### Property 12: Review Uniqueness Constraint
*For any* user and app combination, if a review already exists, attempting to submit a second review should be rejected and return an error.

**Validates: Requirements 5.5**

### Property 13: Review Text Validation
*For any* review text, validation should reject text with fewer than 50 characters or more than 2000 characters, and accept text within this range.

**Validates: Requirements 5.7, 5.8**

### Property 14: Approved Reviews Filter
*For any* app entry, retrieving reviews for public display should return only reviews with approval_status equal to "approved", excluding all pending and rejected reviews.

**Validates: Requirements 7.1**

### Property 15: Review Chronological Sorting
*For any* set of reviews for an app, the returned list should be sorted such that each review's created_at timestamp is greater than or equal to the next review's created_at timestamp (descending order).

**Validates: Requirements 7.2**

### Property 16: Helpful Vote Increment
*For any* review, when a user marks it as helpful, the review's helpful_count should increment by exactly 1.

**Validates: Requirements 7.8**

### Property 17: Scam Report Description Validation
*For any* scam report description, validation should reject text with fewer than 100 characters or more than 3000 characters, and accept text within this range.

**Validates: Requirements 8.5, 8.6**

### Property 18: Evidence URL Limit
*For any* scam report submission, if the evidence_urls array contains more than 5 URLs, the submission should be rejected.

**Validates: Requirements 8.8**

### Property 19: View Count Increment
*For any* app entry, each time the detail page is loaded, the app's view_count should increment by exactly 1.

**Validates: Requirements 15.8**

### Property 20: Similar Apps Category Matching
*For any* app entry, all recommended similar apps should share at least one category with the source app.

**Validates: Requirements 16.1, 16.2**

### Property 21: Email Format Validation
*For any* email address string, validation should accept only strings matching the standard email format (local@domain.tld) and reject all other formats.

**Validates: Requirements 20.4**

### Property 22: Email Duplicate Prevention
*For any* email address, if a newsletter subscription already exists with that email, attempting to create a second subscription should be rejected.

**Validates: Requirements 20.5**

### Property 23: Email Rate Limiting
*For any* newsletter subscriber, the system should prevent sending more than 5 emails within a 24-hour period.

**Validates: Requirements 21.6**

### Property 24: User Deletion Anonymization
*For any* user account deletion, all associated reviews and scam reports should have their user_id set to null or a special "deleted user" identifier, preserving the content while removing the user association.

**Validates: Requirements 23.8**

### Property 25: Security Score Calculation
*For any* app entry with permission data, encryption status, and SDK count, the calculated security score should follow the formula: base_permission_score (2-8 points based on count) - (3 × sensitive_permission_count) + (encryption ? 5 : 0) - (sdk_count > 5 ? 2 : 0), with the result clamped between 0 and 25.

**Validates: Requirements 24.1, 24.3, 24.4, 24.5, 24.6, 24.7, 24.8**

### Property 26: Developer Reputation Calculation
*For any* developer name, the calculated reputation score should follow the formula: app_count_score (1-5 points) + average_trust_score_contribution (2-10 points) - (total_scam_reports > 20 ? 5 : 0), with the result clamped between 0 and 20.

**Validates: Requirements 25.1, 25.2, 25.3, 25.4, 25.5, 25.6, 25.7, 25.8, 25.9**

## Error Handling

### Error Categories

1. **Validation Errors**: User input that fails validation rules
2. **Authentication Errors**: Login failures, session expiration, unauthorized access
3. **Database Errors**: Connection failures, constraint violations, query errors
4. **Business Logic Errors**: Trust score calculation failures, duplicate submissions
5. **External Service Errors**: Email sending failures, cache unavailability
6. **Rate Limiting Errors**: Too many requests, account lockouts

### Error Handling Strategy

**Validation Errors**:
- Return HTTP 422 with detailed field-level error messages
- Use CodeIgniter's validation library for consistent error formatting
- Display errors inline on forms with red highlighting
- Preserve user input on validation failure

**Authentication Errors**:
- Return HTTP 401 for unauthenticated requests
- Return HTTP 403 for unauthorized access attempts
- Redirect to login page with return URL preservation
- Display user-friendly error messages (avoid revealing security details)
- Log failed authentication attempts for security monitoring

**Database Errors**:
- Catch all database exceptions in repositories
- Log full error details with stack trace
- Return generic error message to users ("An error occurred. Please try again.")
- Use database transactions for multi-step operations
- Implement retry logic for transient connection errors (max 3 attempts)

**Business Logic Errors**:
- Throw custom exceptions (e.g., `DuplicateReviewException`, `InvalidTrustScoreException`)
- Catch in controllers and convert to appropriate HTTP responses
- Log business logic errors for debugging
- Provide actionable error messages to users

**External Service Errors**:
- Implement graceful degradation (e.g., queue emails if SMTP fails)
- Use fallback mechanisms (file cache if Redis unavailable)
- Log external service failures
- Display appropriate messages ("Email will be sent shortly")

**Rate Limiting Errors**:
- Return HTTP 429 with Retry-After header
- Display countdown timer for account lockouts
- Log rate limit violations for abuse detection

### Error Logging

**Log Levels**:
- **ERROR**: Database errors, external service failures, uncaught exceptions
- **WARNING**: Business logic errors, validation failures, rate limit hits
- **INFO**: Successful operations, user actions
- **DEBUG**: Detailed execution flow (development only)

**Log Format**:
```
[timestamp] [level] [context] message {user_id: X, app_id: Y, ...}
```

**Log Storage**:
- File-based logs in `writable/logs/` directory
- Rotate daily, keep 30 days
- Separate log files for errors, authentication, and business logic

### Exception Hierarchy

```
AppTrustException (base)
├── ValidationException
├── AuthenticationException
│   ├── InvalidCredentialsException
│   ├── AccountLockedException
│   └── SessionExpiredException
├── AuthorizationException
├── DatabaseException
│   ├── RecordNotFoundException
│   └── ConstraintViolationException
├── BusinessLogicException
│   ├── DuplicateReviewException
│   ├── DuplicateSubscriptionException
│   └── TrustScoreCalculationException
└── ExternalServiceException
    ├── EmailSendException
    └── CacheUnavailableException
```

## Testing Strategy

### Testing Approach

The AppTrust platform requires a **dual testing approach** combining property-based testing for algorithmic correctness with example-based testing for specific workflows and UI interactions.

### Property-Based Testing

**Framework**: [Pest PHP](https://pestphp.com/) with [pest-plugin-faker](https://github.com/pestphp/pest-plugin-faker) for data generation

**Configuration**:
- Minimum 100 iterations per property test
- Each test tagged with feature name and property number
- Tag format: `@feature app-trust-platform @property {number}`

**Property Test Structure**:
```php
it('validates property X: description', function () {
    // Generate random test data
    $data = generateRandomAppData();
    
    // Execute operation
    $result = performOperation($data);
    
    // Assert property holds
    expect($result)->toSatisfyProperty();
})->repeat(100)->group('property-based');
```

**Properties to Implement**:
All 26 properties defined in the Correctness Properties section should be implemented as property-based tests with 100+ iterations each.

**Example Property Test**:
```php
// Property 4: Trust Score Range Constraint
it('calculates trust scores within 0-100 range for any app data', function () {
    $app = App::factory()->create([
        'security_score' => fake()->randomFloat(2, 0, 25),
        'developer_reputation' => fake()->randomFloat(2, 0, 20),
    ]);
    
    // Add random reviews
    Review::factory()
        ->count(fake()->numberBetween(0, 50))
        ->for($app)
        ->create();
    
    // Add random scam reports
    ScamReport::factory()
        ->count(fake()->numberBetween(0, 30))
        ->for($app)
        ->create();
    
    $trustScore = app(TrustScoreService::class)->calculateTrustScore($app->id);
    
    expect($trustScore)->toBeGreaterThanOrEqual(0)
        ->and($trustScore)->toBeLessThanOrEqual(100);
})->repeat(100)->group('property-based', 'trust-score');
```

### Unit Testing

**Framework**: Pest PHP

**Coverage Requirements**:
- All service classes: 90%+ coverage
- All repository classes: 85%+ coverage
- All models: 80%+ coverage
- Controllers: 70%+ coverage (focus on business logic, not views)

**Unit Test Categories**:

1. **Service Tests**: Test business logic in isolation with mocked dependencies
2. **Repository Tests**: Test data access with in-memory SQLite database
3. **Model Tests**: Test relationships, scopes, and accessors
4. **Validation Tests**: Test form request validation rules
5. **Helper Tests**: Test utility functions and libraries

**Example Unit Test**:
```php
describe('TrustScoreService', function () {
    it('calculates review rating contribution correctly', function () {
        $service = new TrustScoreService();
        
        expect($service->calculateReviewContribution(4.7))->toBe(30.0)
            ->and($service->calculateReviewContribution(4.0))->toBe(22.0)
            ->and($service->calculateReviewContribution(3.0))->toBe(15.0);
    });
});
```

### Integration Testing

**Framework**: Pest PHP with database transactions

**Integration Test Categories**:

1. **API Endpoint Tests**: Test full request/response cycle
2. **Database Integration**: Test complex queries and transactions
3. **Email Integration**: Test email sending with mail trap
4. **Cache Integration**: Test Redis caching behavior
5. **Event Integration**: Test event firing and listener execution

**Example Integration Test**:
```php
it('sends scam alert emails when high-risk report is approved', function () {
    Mail::fake();
    
    $subscribers = NewsletterSubscriber::factory()->count(5)->create();
    $report = ScamReport::factory()->create(['risk_level' => 'high']);
    
    app(ScamReportRepository::class)->updateStatus($report->id, 'approved');
    
    Mail::assertSent(ScamAlertMail::class, 5);
})->group('integration', 'email');
```

### Feature Testing

**Framework**: Pest PHP with browser testing (Laravel Dusk)

**Feature Test Categories**:

1. **User Workflows**: Registration, login, review submission
2. **Admin Workflows**: App management, moderation
3. **Search and Discovery**: Search, categories, trending
4. **Trust Score Display**: Score breakdown, color coding

**Example Feature Test**:
```php
it('allows authenticated users to submit reviews', function () {
    $user = User::factory()->create();
    $app = App::factory()->create();
    
    $this->actingAs($user)
        ->post("/apps/{$app->slug}/reviews", [
            'rating' => 4,
            'title' => 'Great app',
            'review_text' => str_repeat('This is a test review. ', 10),
        ])
        ->assertRedirect()
        ->assertSessionHas('success');
    
    expect(Review::where('user_id', $user->id)
        ->where('app_id', $app->id)
        ->exists())->toBeTrue();
})->group('feature', 'reviews');
```

### Performance Testing

**Tools**: Apache JMeter, k6

**Performance Targets**:
- Home page load: < 500ms (95th percentile)
- App detail page: < 800ms (95th percentile)
- Search results: < 2 seconds (99th percentile)
- Trust score calculation: < 100ms per app
- Database queries: < 50ms (95th percentile)

**Load Testing Scenarios**:
1. 100 concurrent users browsing apps
2. 50 concurrent users submitting reviews
3. 10 concurrent admins moderating content
4. 1000 requests/minute to search endpoint

### Security Testing

**Security Test Categories**:

1. **Authentication Tests**: Brute force protection, session security
2. **Authorization Tests**: Access control, privilege escalation
3. **Input Validation Tests**: SQL injection, XSS, CSRF
4. **Rate Limiting Tests**: API abuse, account lockout
5. **Data Protection Tests**: Password hashing, sensitive data exposure

**Tools**:
- OWASP ZAP for automated vulnerability scanning
- Manual penetration testing for critical workflows

### Test Data Management

**Factories**: Use Laravel factories for generating test data
**Seeders**: Provide realistic seed data for development and staging
**Database Transactions**: Wrap tests in transactions for isolation
**Cleanup**: Automatically rollback test data after each test

### Continuous Integration

**CI Pipeline**:
1. Run linter (PHP CS Fixer)
2. Run static analysis (PHPStan level 8)
3. Run unit tests
4. Run integration tests
5. Run feature tests
6. Generate coverage report
7. Run security scan

**CI Requirements**:
- All tests must pass
- Code coverage must be ≥ 80%
- No critical security vulnerabilities
- No PHPStan errors

### Test Organization

```
tests/
├── Unit/
│   ├── Services/
│   │   ├── TrustScoreServiceTest.php
│   │   ├── SecurityScoreServiceTest.php
│   │   └── DeveloperReputationServiceTest.php
│   ├── Repositories/
│   └── Models/
├── Integration/
│   ├── TrustScoreCalculationTest.php
│   ├── EmailNotificationTest.php
│   └── CacheIntegrationTest.php
├── Feature/
│   ├── ReviewSubmissionTest.php
│   ├── ScamReportWorkflowTest.php
│   └── AdminModerationTest.php
├── Property/
│   ├── TrustScorePropertiesTest.php
│   ├── ValidationPropertiesTest.php
│   └── DataPersistencePropertiesTest.php
└── Performance/
    └── LoadTestScenarios/
```

---

## Appendix

### Trust Score Calculation Example

**Example App Data**:
- Average review rating: 4.6 stars (30 reviews)
- Scam reports: 2 reports
- App age: 400 days
- Security score: 18/25
- Developer reputation: 15/20

**Calculation**:
```
Review contribution: 30 points (4.5-5.0 range)
Security score: 18 points
Developer reputation: 15 points
Scam report impact: 10 points (1-5 reports)
App age contribution: 10 points (>365 days)

Total Trust Score: 30 + 18 + 15 + 10 + 10 = 83/100
```

### Configuration Files

**Config/TrustScore.php**:
```php
return [
    'weights' => [
        'reviews' => 0.30,
        'security' => 0.25,
        'developer_reputation' => 0.20,
        'scam_reports' => 0.15,
        'app_age' => 0.10,
    ],
    'review_thresholds' => [
        ['min' => 4.5, 'max' => 5.0, 'points' => 30],
        ['min' => 3.5, 'max' => 4.4, 'points' => 22],
        ['min' => 2.5, 'max' => 3.4, 'points' => 15],
        ['min' => 1.5, 'max' => 2.4, 'points' => 8],
        ['min' => 0.0, 'max' => 1.4, 'points' => 0],
    ],
    'scam_report_thresholds' => [
        ['min' => 0, 'max' => 0, 'points' => 15],
        ['min' => 1, 'max' => 5, 'points' => 10],
        ['min' => 6, 'max' => 15, 'points' => 5],
        ['min' => 16, 'max' => PHP_INT_MAX, 'points' => 0],
    ],
    'age_thresholds' => [
        ['min' => 365, 'max' => PHP_INT_MAX, 'points' => 10],
        ['min' => 180, 'max' => 364, 'points' => 7],
        ['min' => 90, 'max' => 179, 'points' => 4],
        ['min' => 0, 'max' => 89, 'points' => 2],
    ],
    'recalculation_delay' => 60, // seconds
];
```

### API Endpoints (Future Extension)

While the initial platform is web-based, the architecture supports future REST API implementation:

```
GET    /api/v1/apps                    # List apps
GET    /api/v1/apps/{id}               # Get app details
GET    /api/v1/apps/{id}/reviews       # Get app reviews
GET    /api/v1/apps/{id}/scam-reports  # Get scam reports
POST   /api/v1/apps/{id}/reviews       # Submit review (auth required)
POST   /api/v1/apps/{id}/scam-reports  # Submit scam report (auth required)
GET    /api/v1/search                  # Search apps
GET    /api/v1/categories              # List categories
GET    /api/v1/trending                # Get trending apps
```

### Caching Strategy

**Cache Keys**:
```
app:{id}                           # App details (TTL: 1 hour)
app:{id}:trust_score              # Trust score (TTL: 5 minutes)
app:{id}:reviews                  # Approved reviews (TTL: 10 minutes)
app:{id}:scam_reports             # Approved reports (TTL: 10 minutes)
trending:apps                     # Trending list (TTL: 1 hour)
category:{id}:apps                # Category apps (TTL: 30 minutes)
search:{query_hash}               # Search results (TTL: 15 minutes)
```

**Cache Invalidation**:
- App data: Invalidate on update
- Trust score: Invalidate on review/report approval
- Reviews: Invalidate on approval/rejection
- Trending: Invalidate on daily update
- Search: Invalidate on app creation/update

### Database Indexes

**Critical Indexes**:
```sql
-- Apps table
CREATE INDEX idx_apps_trust_score ON apps(trust_score DESC);
CREATE INDEX idx_apps_trending ON apps(trending_score DESC);
CREATE INDEX idx_apps_developer ON apps(developer_name);
CREATE INDEX idx_apps_platform ON apps(platform_type);
CREATE FULLTEXT INDEX idx_apps_search ON apps(name, developer_name, description);

-- Reviews table
CREATE INDEX idx_reviews_app_status ON reviews(app_id, approval_status);
CREATE INDEX idx_reviews_created ON reviews(created_at DESC);

-- Scam reports table
CREATE INDEX idx_scam_reports_app_status ON scam_reports(app_id, approval_status);
CREATE INDEX idx_scam_reports_risk ON scam_reports(risk_level);

-- Activity logs table
CREATE INDEX idx_activity_date ON activity_logs(activity_date, app_id);
```

### Security Measures

1. **CSRF Protection**: Enabled globally via CSRFFilter
2. **XSS Prevention**: Use `esc()` helper for all output, Content Security Policy headers
3. **SQL Injection**: Use query builder and prepared statements exclusively
4. **Password Security**: Bcrypt hashing with cost factor 12
5. **Rate Limiting**: Implement RateLimitFilter for API endpoints and forms
6. **Session Security**: HTTP-only cookies, secure flag in production, session regeneration on login
7. **Input Validation**: Server-side validation for all user input
8. **File Upload Security**: Whitelist allowed extensions, validate MIME types, store outside webroot
9. **Authentication**: Implement account lockout after 5 failed attempts
10. **Authorization**: Role-based access control for admin panel

### Deployment Considerations

**Environment Variables**:
```
APP_ENV=production
APP_DEBUG=false
DATABASE_URL=mysql://user:pass@host:3306/apptrust
REDIS_URL=redis://localhost:6379
MAIL_HOST=smtp.example.com
MAIL_PORT=587
MAIL_USERNAME=noreply@apptrust.com
MAIL_PASSWORD=secret
CACHE_DRIVER=redis
SESSION_DRIVER=database
```

**Server Requirements**:
- PHP 8.2+ with extensions: intl, mbstring, mysqli, redis, gd
- MySQL 8.0+ or MariaDB 10.6+
- Redis 6.0+ (for caching and sessions)
- Nginx or Apache with mod_rewrite
- SSL certificate (Let's Encrypt recommended)

**Performance Optimization**:
- Enable OPcache in production
- Use Redis for session storage
- Implement CDN for static assets
- Enable Gzip compression
- Optimize database queries with EXPLAIN
- Use eager loading to prevent N+1 queries
