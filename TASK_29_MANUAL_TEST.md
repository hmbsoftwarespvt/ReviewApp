# Task 29: Newsletter Subscription - Manual Testing Guide

## Quick Test Command

Run the automated test command:
```bash
php spark test:newsletter
```

Expected output: All 8 tests should pass.

## Manual Browser Testing

### Prerequisites
1. Ensure the development server is running
2. Database migrations are up to date
3. Navigate to `http://localhost/app-review/` (or your local URL)

### Test 1: Newsletter Form Visibility
1. Open the home page
2. Scroll to the "Stay Protected" section (before footer)
3. **Verify:** Newsletter subscription form is visible with:
   - Email input field
   - "Subscribe" button
   - Attractive gradient background

### Test 2: Valid Email Subscription
1. Enter a valid email: `test@example.com`
2. Click "Subscribe"
3. **Expected:** Redirect to home page with success message
4. **Verify in database:**
   ```sql
   SELECT * FROM newsletter_subscribers WHERE email = 'test@example.com';
   ```
5. **Expected fields:**
   - `email`: test@example.com
   - `is_confirmed`: 0 (false)
   - `unsubscribe_token`: 64-character hex string
   - `confirmation_token`: 64-character hex string
   - `subscribed_at`: Current timestamp

### Test 3: Invalid Email Rejection
1. Try subscribing with invalid emails:
   - `notanemail` (no @ symbol)
   - `missing@domain` (no TLD)
   - `@nodomain.com` (no local part)
2. **Expected:** Error message for each attempt
3. **Verify:** No records created in database

### Test 4: Duplicate Email Prevention
1. Subscribe with `duplicate@example.com`
2. Try subscribing again with the same email
3. **Expected:** Info message: "This email is already subscribed"
4. **Verify in database:** Only one record exists

### Test 5: Confirmation Link
1. Get confirmation token from database:
   ```sql
   SELECT confirmation_token FROM newsletter_subscribers WHERE email = 'test@example.com';
   ```
2. Visit: `http://localhost/app-review/newsletter/confirm/{token}`
3. **Expected:** Success message and redirect to home
4. **Verify in database:**
   ```sql
   SELECT is_confirmed, confirmation_token FROM newsletter_subscribers WHERE email = 'test@example.com';
   ```
5. **Expected:**
   - `is_confirmed`: 1 (true)
   - `confirmation_token`: NULL

### Test 6: Invalid Confirmation Token
1. Visit: `http://localhost/app-review/newsletter/confirm/invalidtoken123`
2. **Expected:** Error message: "Invalid or expired confirmation link"

### Test 7: Unsubscribe Page Display
1. Get unsubscribe token from database:
   ```sql
   SELECT unsubscribe_token FROM newsletter_subscribers WHERE email = 'test@example.com';
   ```
2. Visit: `http://localhost/app-review/newsletter/unsubscribe/{token}`
3. **Expected:** Unsubscribe confirmation page showing:
   - Subscriber email
   - Warning about what they'll miss
   - "Yes, Unsubscribe Me" button
   - "No, Keep Me Subscribed" link

### Test 8: Unsubscribe Functionality
1. On the unsubscribe page, click "Yes, Unsubscribe Me"
2. **Expected:** Success message and redirect to home
3. **Verify in database:**
   ```sql
   SELECT unsubscribed_at FROM newsletter_subscribers WHERE email = 'test@example.com';
   ```
4. **Expected:** `unsubscribed_at` has a timestamp

### Test 9: Invalid Unsubscribe Token
1. Visit: `http://localhost/app-review/newsletter/unsubscribe/invalidtoken456`
2. **Expected:** Error message: "Invalid unsubscribe link"

### Test 10: Already Unsubscribed
1. Try to unsubscribe again using the same token
2. **Expected:** Info message: "You have already unsubscribed"

### Test 11: Empty Email
1. Leave email field empty and click "Subscribe"
2. **Expected:** HTML5 validation error (required field)

### Test 12: Email Too Long
1. Enter an email longer than 255 characters
2. Click "Subscribe"
3. **Expected:** Error message about email length

## Database Queries for Verification

### View All Subscribers
```sql
SELECT id, email, is_confirmed, subscribed_at, unsubscribed_at 
FROM newsletter_subscribers 
ORDER BY id DESC;
```

### View Confirmed Subscribers
```sql
SELECT email, subscribed_at 
FROM newsletter_subscribers 
WHERE is_confirmed = 1 AND unsubscribed_at IS NULL;
```

### View Unsubscribed Users
```sql
SELECT email, subscribed_at, unsubscribed_at 
FROM newsletter_subscribers 
WHERE unsubscribed_at IS NOT NULL;
```

### Clean Up Test Data
```sql
DELETE FROM newsletter_subscribers WHERE email LIKE '%@example.com';
```

## Expected Behavior Summary

| Action | Expected Result |
|--------|----------------|
| Valid email subscription | Success message, record created with is_confirmed=0 |
| Invalid email format | Error message, no record created |
| Duplicate email | Info message, no new record |
| Confirmation link click | Success message, is_confirmed=1 |
| Invalid confirmation token | Error message |
| Unsubscribe page visit | Display confirmation page |
| Unsubscribe confirmation | Success message, unsubscribed_at set |
| Invalid unsubscribe token | Error message |
| Already unsubscribed | Info message |

## Security Checks

### CSRF Protection
1. View page source on home page
2. **Verify:** CSRF token field present in newsletter form
3. **Look for:** `<input type="hidden" name="csrf_test_name" value="...">`

### Rate Limiting
1. Rapidly submit subscription form 10+ times
2. **Expected:** Some requests should be rate-limited
3. **Note:** Exact behavior depends on RateLimitFilter configuration

### Token Security
1. Check token length in database
2. **Expected:** 64 characters (32 bytes hex-encoded)
3. **Verify:** Tokens are unique across all subscribers

## Common Issues and Solutions

### Issue: "Table 'newsletter_subscribers' doesn't exist"
**Solution:** Run migrations: `php spark migrate`

### Issue: "Class 'NewsletterController' not found"
**Solution:** Clear cache: `php spark cache:clear`

### Issue: "CSRF token mismatch"
**Solution:** 
1. Check CSRF is enabled in `app/Config/Security.php`
2. Ensure form includes `<?= csrf_field() ?>`

### Issue: Routes not working
**Solution:** 
1. Check `app/Config/Routes.php` has newsletter routes
2. Clear route cache if exists

## Success Criteria

✅ All 8 automated tests pass  
✅ Newsletter form visible on home page  
✅ Valid emails can subscribe  
✅ Invalid emails are rejected  
✅ Duplicate emails are prevented  
✅ Confirmation link works  
✅ Unsubscribe page displays correctly  
✅ Unsubscribe functionality works  
✅ Invalid tokens are rejected  
✅ CSRF protection is active  

## Next Steps

After Task 29 is verified:
1. **Task 33**: Implement email notification service
2. Add actual email sending for confirmations
3. Add scam alert email functionality
4. Test complete end-to-end workflow with real emails

