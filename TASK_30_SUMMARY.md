# Task 30: Trending Service - Implementation Summary

## Overview
Implemented the TrendingService class to calculate and manage trending apps based on 24-hour activity metrics.

## Files Created

### 1. TrendingService (`app/Services/TrendingService.php`)
**Purpose**: Calculate trending scores based on 24-hour activity metrics

**Key Methods**:
- `calculateTrendingScore(int $appId): float` - Calculates trending score for a specific app
- `updateDailyTrending(): int` - Updates trending scores for all apps (scheduled job)
- `getTrendingApps(int $limit = 12): array` - Returns top trending apps with caching
- `trackView(int $appId): void` - Tracks app view activity
- `trackReview(int $appId): void` - Tracks review submission activity
- `trackScamReport(int $appId): void` - Tracks scam report submission activity

**Trending Score Formula**:
```
trending_score = (views_24h > 100 ? 10 : 0) 
               + (reviews_24h > 10 ? 15 : 0) 
               - (scam_reports_24h > 5 ? 20 : 0)
```

**Features**:
- Tracks 24-hour metrics (views, reviews, scam reports)
- Stores trending scores in activity_logs table
- Caches trending results for 1 hour
- Provides activity tracking methods for integration

### 2. UpdateTrending Command (`app/Commands/UpdateTrending.php`)
**Purpose**: CLI command for scheduled daily trending updates

**Usage**: `php spark trending:update`

**Features**:
- Updates trending scores for all approved apps
- Invalidates trending cache
- Provides console output for monitoring
- Should be scheduled to run at 00:00 UTC daily

### 3. Unit Tests (`tests/unit/Services/TrendingServiceTest.php`)
**Test Coverage**:
- ✓ Calculate trending score with high views (+10 points)
- ✓ Calculate trending score with high reviews (+15 points)
- ✓ Calculate trending score with high scam reports (-20 points)
- ✓ Calculate combined trending score
- ✓ Calculate trending score with no activity (0 points)
- ✓ Get trending apps returns top apps sorted by score
- ✓ Trending apps results are cached
- ✓ Track view increments activity log
- ✓ Update daily trending updates all apps

## Database Integration

### Activity Logs Table
The service uses the existing `activity_logs` table to:
- Track daily view counts per app
- Track daily review submission counts per app
- Track daily scam report submission counts per app
- Store historical trending scores

**Activity Types**:
- `view` - App detail page views
- `review` - Review submissions
- `scam_report` - Scam report submissions
- `trending_score` - Historical trending scores

## Caching Strategy

### Trending Apps Cache
- **Key**: `trending_apps`
- **TTL**: 1 hour (3600 seconds)
- **Content**: Top 50 trending apps
- **Invalidation**: On daily trending update

## Integration Points

### 1. App Detail Page (Task 22)
Add view tracking:
```php
$trendingService = new \App\Services\TrendingService();
$trendingService->trackView($appId);
```

### 2. Review Submission (Task 27)
Add review tracking:
```php
$trendingService = new \App\Services\TrendingService();
$trendingService->trackReview($appId);
```

### 3. Scam Report Submission (Task 28)
Add scam report tracking:
```php
$trendingService = new \App\Services\TrendingService();
$trendingService->trackScamReport($appId);
```

### 4. Home Page (Task 21)
Use trending apps:
```php
$trendingService = new \App\Services\TrendingService();
$trendingApps = $trendingService->getTrendingApps(12);
```

## Scheduled Job Setup

### Cron Configuration
Add to crontab for daily execution at 00:00 UTC:
```bash
0 0 * * * cd /path/to/app-review && php spark trending:update >> /var/log/trending-update.log 2>&1
```

### Windows Task Scheduler
Create a scheduled task:
- **Program**: `php`
- **Arguments**: `spark trending:update`
- **Start in**: `D:\workspace\d8-2\htdocs\app-review`
- **Trigger**: Daily at 00:00

## Verification

Run verification script:
```bash
php verify_task30.php
```

**Expected Output**:
```
=== Task 30: Trending Service Verification ===

Test 1: TrendingService file exists... ✓ PASS
Test 2: UpdateTrending command file exists... ✓ PASS
Test 3: TrendingServiceTest file exists... ✓ PASS
Test 4: TrendingService class structure... ✓ PASS
Test 5: UpdateTrending command structure... ✓ PASS

=== All Tests Passed! ===
```

## Acceptance Criteria Status

✅ **All acceptance criteria met**:
- ✓ TrendingService class created
- ✓ calculateTrendingScore() method implemented
- ✓ updateDailyTrending() method implemented (scheduled job)
- ✓ Tracks 24-hour metrics (views, reviews, scam reports)
- ✓ Stores trending scores in activity_logs table
- ✓ Caches trending results (1-hour TTL)
- ✓ Views > 100: +10 points
- ✓ Reviews > 10: +15 points
- ✓ Scam reports > 5: -20 points
- ✓ Daily update runs via command
- ✓ Results cached for 1 hour

## Notes

1. **RecommendationService**: Already exists from Task 22 (noted in task description)
2. **Activity Tracking**: Integration with existing controllers needed for full functionality
3. **Scheduled Execution**: Requires cron job or task scheduler configuration
4. **Cache Driver**: Uses configured cache driver (Redis or file-based)

## Next Steps

1. Integrate activity tracking into existing controllers (Tasks 22, 27, 28)
2. Set up cron job for daily trending updates
3. Update home page to display trending apps
4. Monitor trending score calculations and adjust thresholds if needed
