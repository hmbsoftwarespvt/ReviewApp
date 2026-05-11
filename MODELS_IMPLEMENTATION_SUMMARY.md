# Models Implementation Summary

## Task 4: Models - Create Base Models with Relationships

**Status:** ✅ COMPLETED

All CodeIgniter 4 models have been successfully created with proper validation rules, relationships, and helper methods.

---

## Created Models

### 1. **UserModel** (`app/Models/UserModel.php`)
- **Table:** `users`
- **Primary Key:** `id`
- **Timestamps:** Enabled (created_at, updated_at)
- **Relationships:**
  - hasMany: reviews (ReviewModel)
  - hasMany: scam_reports (ScamReportModel)
  - hasMany: blog_posts (BlogPostModel) as author

**Key Features:**
- Authentication fields (password_hash, verification_token, reset_token)
- Account security (failed_login_count, account_locked_until)
- Role-based access (user, admin)
- Status management (active, suspended, deleted)

**Validation Rules:**
- Username: 3-50 chars, alphanumeric, unique
- Email: valid email format, unique
- Role: user or admin
- Status: active, suspended, or deleted

**Helper Methods:**
- `findByEmailOrUsername()` - Find user by email or username
- `getReviews()` - Get user's reviews
- `getScamReports()` - Get user's scam reports
- `isAccountLocked()` - Check if account is locked
- `incrementFailedLogin()` - Increment failed login count
- `resetFailedLogin()` - Reset failed login count
- `lockAccount()` - Lock account for specified minutes

---

### 2. **AppModel** (`app/Models/AppModel.php`)
- **Table:** `apps`
- **Primary Key:** `id`
- **Timestamps:** Enabled (created_at, updated_at)
- **Relationships:**
  - hasMany: reviews (ReviewModel)
  - hasMany: scam_reports (ScamReportModel)
  - hasMany: screenshots (ScreenshotModel)
  - belongsToMany: categories (CategoryModel) via app_categories
  - hasMany: activity_logs (ActivityLogModel)

**Key Features:**
- Trust score tracking (trust_score, security_score, developer_reputation)
- Platform type support (android, ios, web, desktop)
- Approval workflow (pending, approved, rejected)
- Security data (permissions JSON, has_encryption, third_party_sdk_count)
- Trending metrics (view_count, trending_score)

**Validation Rules:**
- Name: required, max 255 chars
- Slug: required, unique, alphanumeric with dashes
- Platform type: android, ios, web, or desktop
- Developer name: required, max 255 chars
- Price: decimal, >= 0
- Trust score: 0-100
- Security score: 0-25
- Developer reputation: 0-20

**Helper Methods:**
- `findBySlug()` - Find app by slug
- `getReviews()` - Get app reviews with status filter
- `getScamReports()` - Get app scam reports with status filter
- `getScreenshots()` - Get app screenshots ordered by display_order
- `getCategories()` - Get app categories
- `attachCategories()` - Attach categories to app
- `detachCategories()` - Remove all categories from app
- `syncCategories()` - Sync categories (detach all, attach new)
- `incrementViewCount()` - Increment view count
- `getByCategory()` - Get apps by category
- `getByDeveloper()` - Get apps by developer
- `getTrending()` - Get trending apps
- `search()` - Search apps with filters

---

### 3. **ReviewModel** (`app/Models/ReviewModel.php`)
- **Table:** `reviews`
- **Primary Key:** `id`
- **Timestamps:** Enabled (created_at, updated_at)
- **Relationships:**
  - belongsTo: app (AppModel)
  - belongsTo: user (UserModel)
  - hasMany: review_helpful_votes (ReviewHelpfulVoteModel)

**Key Features:**
- Rating system (1-5 stars)
- Approval workflow (pending, approved, rejected)
- Helpful vote tracking
- Pros and cons fields
- Unique constraint: one review per user per app

**Validation Rules:**
- Rating: required, 1-5
- Title: required, max 255 chars
- Review text: required, 50-2000 chars
- Pros/Cons: optional, max 1000 chars

**Helper Methods:**
- `getByApp()` - Get reviews by app with status filter
- `getByUser()` - Get reviews by user
- `getPending()` - Get pending reviews
- `getAverageRating()` - Calculate average rating for app
- `getReviewCount()` - Count reviews for app
- `userHasReviewed()` - Check if user has reviewed app
- `incrementHelpfulCount()` - Increment helpful count
- `updateStatus()` - Update approval status
- `getWithDetails()` - Get review with user and app details
- `getByAppWithUser()` - Get reviews with user details

---

### 4. **ScamReportModel** (`app/Models/ScamReportModel.php`)
- **Table:** `scam_reports`
- **Primary Key:** `id`
- **Timestamps:** Enabled (created_at, updated_at)
- **Relationships:**
  - belongsTo: app (AppModel)
  - belongsTo: user (UserModel)

**Key Features:**
- Risk level classification (low, medium, high)
- Approval workflow (pending, approved, rejected)
- Evidence URLs (JSON, max 5)
- Verification notes

**Validation Rules:**
- Title: required, max 255 chars
- Description: required, 100-3000 chars
- Risk level: required, low/medium/high
- Evidence URLs: max 5 URLs (validated in callback)

**Helper Methods:**
- `getByApp()` - Get scam reports by app with status filter
- `getByUser()` - Get scam reports by user
- `getPending()` - Get pending scam reports
- `getAll()` - Get all scam reports with filters
- `getCountByApp()` - Count scam reports for app
- `getCountByRiskLevel()` - Count scam reports by risk level
- `updateStatus()` - Update approval status with notes
- `updateRiskLevel()` - Update risk level
- `getWithDetails()` - Get scam report with user and app details
- `getByAppWithUser()` - Get scam reports with user details

---

### 5. **CategoryModel** (`app/Models/CategoryModel.php`)
- **Table:** `categories`
- **Primary Key:** `id`
- **Timestamps:** Enabled (created_at, updated_at)
- **Relationships:**
  - belongsToMany: apps (AppModel) via app_categories

**Key Features:**
- Slug-based URLs
- Display order for sorting
- Icon support
- Description field

**Validation Rules:**
- Name: required, unique, max 100 chars
- Slug: required, unique, alphanumeric with dashes
- Icon: optional, max 100 chars
- Display order: optional, integer

**Helper Methods:**
- `findBySlug()` - Find category by slug
- `getAllOrdered()` - Get all categories ordered by display_order
- `getApps()` - Get apps in category
- `getAppCount()` - Count apps in category
- `getAllWithAppCounts()` - Get all categories with app counts

---

### 6. **ScreenshotModel** (`app/Models/ScreenshotModel.php`)
- **Table:** `screenshots`
- **Primary Key:** `id`
- **Timestamps:** created_at only
- **Relationships:**
  - belongsTo: app (AppModel)

**Key Features:**
- File path storage
- Display order for gallery
- Cascade delete with app

**Validation Rules:**
- Filename: required, max 255 chars
- File path: required, max 500 chars
- Display order: optional, integer

**Helper Methods:**
- `getByApp()` - Get screenshots by app ordered by display_order
- `getCountByApp()` - Count screenshots for app
- `deleteByApp()` - Delete all screenshots for app

---

### 7. **BlogPostModel** (`app/Models/BlogPostModel.php`)
- **Table:** `blog_posts`
- **Primary Key:** `id`
- **Timestamps:** Enabled (created_at, updated_at)
- **Relationships:**
  - belongsTo: author (UserModel)

**Key Features:**
- Publication workflow (draft, published)
- Category system (guides, tips_tricks, scam_alerts, news_updates, reviews)
- View count tracking
- Featured image support
- Excerpt field

**Validation Rules:**
- Title: required, max 255 chars
- Slug: required, unique, alphanumeric with dashes
- Content: required
- Category: required, valid category
- Publication status: draft or published

**Helper Methods:**
- `findBySlug()` - Find blog post by slug
- `getPublished()` - Get published posts
- `getByCategory()` - Get published posts by category
- `getDrafts()` - Get draft posts
- `getByAuthor()` - Get posts by author
- `incrementViewCount()` - Increment view count
- `publish()` - Publish post
- `unpublish()` - Set post to draft
- `getWithAuthor()` - Get post with author details
- `getRelated()` - Get related posts (same category)

---

### 8. **NewsletterSubscriberModel** (`app/Models/NewsletterSubscriberModel.php`)
- **Table:** `newsletter_subscribers`
- **Primary Key:** `id`
- **Timestamps:** No automatic timestamps
- **Relationships:** None

**Key Features:**
- Email confirmation workflow
- Unsubscribe token system
- Daily email limit (max 5 per day)
- Subscription tracking

**Validation Rules:**
- Email: required, valid email, unique

**Helper Methods:**
- `findByEmail()` - Find subscriber by email
- `findByUnsubscribeToken()` - Find subscriber by unsubscribe token
- `findByConfirmationToken()` - Find subscriber by confirmation token
- `confirmSubscription()` - Confirm subscription
- `unsubscribe()` - Unsubscribe user
- `getConfirmed()` - Get all confirmed subscribers
- `canReceiveEmail()` - Check daily email limit
- `incrementEmailCount()` - Increment email count
- `getSubscriberCount()` - Count confirmed subscribers

---

### 9. **SettingModel** (`app/Models/SettingModel.php`)
- **Table:** `settings`
- **Primary Key:** `id`
- **Timestamps:** updated_at only
- **Relationships:** None

**Key Features:**
- Key-value storage
- Type casting (string, integer, float, boolean, json)
- Trust algorithm weight configuration
- Prefix-based retrieval

**Validation Rules:**
- Setting key: required, unique, max 100 chars
- Setting type: string, integer, float, boolean, or json

**Helper Methods:**
- `get()` - Get setting by key with default value
- `set()` - Set setting value with type
- `getAll()` - Get all settings as key-value array
- `getByPrefix()` - Get settings by key prefix
- `deleteByKey()` - Delete setting by key
- `getTrustAlgorithmWeights()` - Get trust algorithm weights
- `setTrustAlgorithmWeights()` - Set trust algorithm weights

---

### 10. **ActivityLogModel** (`app/Models/ActivityLogModel.php`)
- **Table:** `activity_logs`
- **Primary Key:** `id`
- **Timestamps:** created_at only
- **Relationships:**
  - belongsTo: app (AppModel)

**Key Features:**
- 24-hour activity tracking
- Activity types (view, review, scam_report)
- Trending calculation support
- Automatic count aggregation

**Validation Rules:**
- Activity type: required, view/review/scam_report
- Activity date: required, valid date
- Count: optional, integer >= 0

**Helper Methods:**
- `logActivity()` - Log activity (increment or insert)
- `getActivityCount()` - Get activity count for specific date
- `get24HourMetrics()` - Get 24-hour metrics for app
- `getMetricsForDateRange()` - Get metrics for date range
- `cleanOldLogs()` - Clean logs older than specified days
- `getTotalActivityByType()` - Get total activity by type for date range

---

### 11. **ReviewHelpfulVoteModel** (`app/Models/ReviewHelpfulVoteModel.php`)
- **Table:** `review_helpful_votes`
- **Primary Key:** `id`
- **Timestamps:** created_at only
- **Relationships:**
  - belongsTo: review (ReviewModel)
  - belongsTo: user (UserModel)

**Key Features:**
- Prevents duplicate votes (unique constraint)
- Automatic helpful count update
- Vote tracking per user

**Validation Rules:**
- Review ID: required, must exist
- User ID: required, must exist

**Helper Methods:**
- `hasVoted()` - Check if user has voted for review
- `addVote()` - Add vote (if not already voted)
- `removeVote()` - Remove vote
- `getVoteCount()` - Count votes for review
- `getByUser()` - Get all votes by user
- `getByReview()` - Get all votes for review

---

## Model Relationships Summary

### One-to-Many Relationships
- **User** → Reviews, ScamReports, BlogPosts
- **App** → Reviews, ScamReports, Screenshots, ActivityLogs
- **Review** → ReviewHelpfulVotes

### Many-to-Many Relationships
- **App** ↔ **Category** (via app_categories junction table)

### Relationship Helper Methods
All models with relationships include helper methods to:
- Retrieve related records
- Count related records
- Filter related records by status/criteria
- Join tables for efficient queries

---

## Validation Features

All models include:
- ✅ Field-level validation rules
- ✅ Custom validation messages
- ✅ Unique constraint validation
- ✅ Foreign key validation (is_not_unique checks)
- ✅ Enum validation (in_list)
- ✅ Length validation (min_length, max_length)
- ✅ Type validation (integer, decimal, valid_email, valid_url)

---

## CodeIgniter 4 Best Practices Implemented

1. **BaseModel Extension**: All models extend `CodeIgniter\Model`
2. **Protected Fields**: `$protectFields = true` with explicit `$allowedFields`
3. **Timestamps**: Enabled where appropriate with `$useTimestamps`
4. **Return Type**: Consistent array return type
5. **Validation**: Comprehensive validation rules and messages
6. **Callbacks**: Callback hooks enabled for extensibility
7. **Query Builder**: Efficient queries using CodeIgniter's Query Builder
8. **Relationships**: Documented relationships in PHPDoc comments
9. **Helper Methods**: Business logic encapsulated in model methods
10. **Type Safety**: Proper type casting in helper methods

---

## Acceptance Criteria Verification

✅ **All models extend BaseModel** (CodeIgniter\Model)
✅ **Relationships are properly defined** (documented in PHPDoc and implemented via helper methods)
✅ **Validation rules defined in models** (comprehensive rules for all fields)
✅ **Timestamps enabled where appropriate** (created_at, updated_at)

---

## Additional Features

### Security Features
- Password hashing support (UserModel)
- Token generation (UserModel, NewsletterSubscriberModel)
- Account lockout mechanism (UserModel)
- Failed login tracking (UserModel)

### Performance Features
- Efficient queries with proper indexes
- Eager loading support via join methods
- Count methods to avoid loading full datasets
- Batch operations support

### Data Integrity
- Foreign key validation
- Unique constraint enforcement
- Cascade delete handling
- Type casting for data consistency

---

## Testing Recommendations

The models are ready for:
1. **Unit Testing**: Test individual model methods
2. **Integration Testing**: Test model relationships
3. **Validation Testing**: Test validation rules
4. **Property-Based Testing**: Test data persistence and constraints

---

## Next Steps

With all models created, the following tasks can now proceed:
- **Task 5**: Create Model Factories for Testing
- **Task 6**: Implement TrustScoreService
- **Task 7**: Implement SecurityScoreService
- **Task 8**: Implement DeveloperReputationService
- **Task 9**: Create AppRepository
- **Task 10**: Create ReviewRepository and ScamReportRepository

---

## Files Created

1. `app/Models/UserModel.php` - User authentication and management
2. `app/Models/AppModel.php` - Application entries with trust scores
3. `app/Models/ReviewModel.php` - User reviews with moderation
4. `app/Models/ScamReportModel.php` - Scam reports with verification
5. `app/Models/CategoryModel.php` - App categories
6. `app/Models/ScreenshotModel.php` - App screenshots
7. `app/Models/BlogPostModel.php` - Blog posts with publication workflow
8. `app/Models/NewsletterSubscriberModel.php` - Newsletter subscriptions
9. `app/Models/SettingModel.php` - Platform configuration
10. `app/Models/ActivityLogModel.php` - Activity tracking for trending
11. `app/Models/ReviewHelpfulVoteModel.php` - Review helpful votes

**Total:** 11 models created successfully ✅

---

## Syntax Verification

All models have been verified for PHP syntax errors:
```
✅ No syntax errors detected in all 11 model files
```

---

**Implementation Date:** 2025
**CodeIgniter Version:** 4.5+
**PHP Version:** 8.2+
