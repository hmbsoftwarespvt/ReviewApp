# Task 21 - Manual Testing Guide

## Home Page Implementation - Manual Test Checklist

### Prerequisites
1. Ensure database is set up with migrations run
2. Ensure at least some test data exists (apps, categories, users, reviews)
3. Access the application at: `http://localhost/app-review/` (or your configured base URL)

### Test Cases

#### 1. Home Page Loads Successfully
- [ ] Navigate to the home page
- [ ] Page loads without errors
- [ ] Page displays "AppTrust" branding
- [ ] Page displays "Discover Trustworthy Apps" heading

#### 2. Trending Apps Section
- [ ] "Trending Apps" section is visible
- [ ] Maximum of 12 trending apps are displayed
- [ ] Each app card shows:
  - [ ] App name
  - [ ] Trust score badge (with correct color: green 80-100, yellow 50-79, red 0-49)
  - [ ] Category badge
  - [ ] Brief description (truncated to 80 characters)
  - [ ] View count
  - [ ] "View Details" button
- [ ] If no trending apps exist, displays "No trending apps available" message
- [ ] Clicking on an app name or "View Details" navigates to app detail page

#### 3. Category Navigation Menu
- [ ] "Browse by Category" section is visible
- [ ] All categories from database are displayed as pills
- [ ] Category pills are clickable
- [ ] Clicking a category pill navigates to category page

#### 4. Search Form in Header
- [ ] Search form is visible in the hero section
- [ ] Search input field has placeholder "Search for apps..."
- [ ] Search button is present with search icon
- [ ] Form submits to `/search` endpoint
- [ ] Entering a query and submitting redirects to search results page

#### 5. Platform Statistics
- [ ] Four stat cards are displayed:
  - [ ] Verified Apps (with app icon)
  - [ ] User Reviews (with star icon)
  - [ ] Scam Reports (with warning icon)
  - [ ] Active Users (with people icon)
- [ ] Each stat card shows the correct count from database
- [ ] Numbers are formatted with commas (e.g., 1,234)
- [ ] Stat cards have hover effect (slight lift)

#### 6. Newsletter Subscription
- [ ] Newsletter subscription section is visible
- [ ] Section displays "Stay Protected" heading
- [ ] Email input field is present
- [ ] "Subscribe" button is present
- [ ] Form submits to `/newsletter/subscribe` endpoint

#### 7. Navigation Bar
- [ ] Navigation bar is visible at top
- [ ] AppTrust logo/brand is visible
- [ ] Navigation links are present:
  - [ ] Home
  - [ ] Apps
  - [ ] Categories
  - [ ] Scam Alerts
  - [ ] Blog
- [ ] If user is logged in, "Logout" link is shown
- [ ] If user is not logged in, "Login" and "Register" links are shown

#### 8. Footer
- [ ] Footer is visible at bottom
- [ ] Footer sections are present:
  - [ ] Platform (with links to Browse Apps, Categories, Scam Alerts, Compare Apps)
  - [ ] Resources (with links to Blog, About Us, Contact, FAQ)
  - [ ] Legal (with links to Privacy Policy, Terms of Service, Cookie Policy)
  - [ ] Connect (with social media icons)
- [ ] Copyright notice is displayed with current year

#### 9. Performance
- [ ] Page loads in less than 1 second (check browser developer tools Network tab)
- [ ] No console errors in browser developer tools
- [ ] Images load properly (or placeholder icons are shown)

#### 10. Responsive Design
- [ ] Page displays correctly on desktop (1920x1080)
- [ ] Page displays correctly on tablet (768x1024)
- [ ] Page displays correctly on mobile (375x667)
- [ ] Navigation collapses to hamburger menu on mobile

### Database Verification Queries

Run these queries to verify data is being fetched correctly:

```sql
-- Check trending apps (should return top 12 by trending_score)
SELECT id, name, slug, trust_score, trending_score, approval_status
FROM apps
WHERE approval_status = 'approved'
ORDER BY trending_score DESC
LIMIT 12;

-- Check categories (should return all ordered by display_order)
SELECT id, name, slug, display_order
FROM categories
ORDER BY display_order ASC, name ASC;

-- Check statistics
SELECT 
    (SELECT COUNT(*) FROM apps WHERE approval_status = 'approved') as total_apps,
    (SELECT COUNT(*) FROM reviews WHERE approval_status = 'approved') as total_reviews,
    (SELECT COUNT(*) FROM scam_reports WHERE approval_status = 'approved') as total_scam_reports,
    (SELECT COUNT(*) FROM users WHERE status = 'active') as total_users;
```

### Expected Results

1. **Trending Apps**: Should display apps with highest trending_score, limited to 12
2. **Trust Score Colors**:
   - Green badge: trust_score >= 80
   - Yellow badge: trust_score >= 50 and < 80
   - Red badge: trust_score < 50
3. **Categories**: All categories should be visible and clickable
4. **Statistics**: Should match database counts
5. **Performance**: Page should load quickly without delays

### Common Issues and Solutions

**Issue**: No trending apps displayed
- **Solution**: Check if apps exist with `approval_status = 'approved'` and have `trending_score` values

**Issue**: Categories not showing
- **Solution**: Check if categories exist in database

**Issue**: Statistics showing 0
- **Solution**: Ensure test data exists for apps, reviews, scam_reports, and users

**Issue**: Page not loading
- **Solution**: Check PHP error logs, ensure all models and repositories are properly loaded

**Issue**: Trust score colors not correct
- **Solution**: Verify trust_score values in database and check CSS classes (trust-high, trust-medium, trust-low)

### Browser Testing

Test in multiple browsers:
- [ ] Chrome/Edge (Chromium)
- [ ] Firefox
- [ ] Safari (if available)

### Accessibility Testing

- [ ] Tab through all interactive elements (links, buttons, form fields)
- [ ] Ensure proper focus indicators
- [ ] Check color contrast for readability
- [ ] Verify alt text for images (or appropriate aria-labels for icon placeholders)

## Test Data Setup (Optional)

If you need to create test data, run these SQL commands:

```sql
-- Create test categories
INSERT INTO categories (name, slug, display_order, created_at, updated_at) VALUES
('Finance', 'finance', 1, NOW(), NOW()),
('AI Tools', 'ai-tools', 2, NOW(), NOW()),
('Gaming', 'gaming', 3, NOW(), NOW()),
('Education', 'education', 4, NOW(), NOW());

-- Create test apps with trending scores
INSERT INTO apps (name, slug, description, platform_type, developer_name, approval_status, trust_score, trending_score, view_count, created_at, updated_at) VALUES
('Budget Tracker Pro', 'budget-tracker-pro', 'Professional budget tracking application', 'android', 'FinTech Solutions', 'approved', 85, 100, 1250, NOW(), NOW()),
('AI Assistant Plus', 'ai-assistant-plus', 'Advanced AI-powered personal assistant', 'ios', 'AI Innovations', 'approved', 78, 95, 980, NOW(), NOW()),
('Math Master', 'math-master', 'Interactive math learning platform', 'web', 'EduTech Inc', 'approved', 92, 88, 750, NOW(), NOW());

-- Link apps to categories
INSERT INTO app_categories (app_id, category_id, created_at)
SELECT a.id, c.id, NOW()
FROM apps a, categories c
WHERE (a.slug = 'budget-tracker-pro' AND c.slug = 'finance')
   OR (a.slug = 'ai-assistant-plus' AND c.slug = 'ai-tools')
   OR (a.slug = 'math-master' AND c.slug = 'education');
```

## Completion Checklist

- [ ] All test cases pass
- [ ] No console errors
- [ ] Performance is acceptable (< 1 second load time)
- [ ] Responsive design works on all screen sizes
- [ ] All links navigate correctly
- [ ] Data displays correctly from database
