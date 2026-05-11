# Task 28: Scam Report Submission - Manual Testing Guide

## Prerequisites

Before testing, ensure:
1. Database is set up with migrations run
2. At least one approved app exists in the database
3. At least one user account exists (or create one during testing)
4. Web server is running (Apache/Nginx with PHP 8.2+)
5. MySQL database is accessible

## Test Scenarios

### Test 1: View Scam Report Form (Authenticated User)

**Steps:**
1. Log in to the platform
2. Navigate to any approved app's detail page
3. Scroll down to the "Scam Reports" section

**Expected Results:**
- ✅ Scam report submission form is visible
- ✅ Form has red border and header
- ✅ Form contains:
  - Title input field
  - Risk level radio buttons (Low, Medium, High)
  - Description textarea with character counter
  - 5 evidence URL input fields
  - Submit button labeled "Submit Scam Report"
- ✅ Character counter shows "0 / 3000 characters (minimum 100)"

---

### Test 2: View Scam Report Form (Unauthenticated User)

**Steps:**
1. Log out (if logged in)
2. Navigate to any approved app's detail page
3. Scroll down to the "Scam Reports" section

**Expected Results:**
- ✅ Scam report submission form is NOT visible
- ✅ Message displayed: "Login or register to report a scam"
- ✅ Links to login and register pages are present

---

### Test 3: Submit Valid Scam Report

**Steps:**
1. Log in to the platform
2. Navigate to any approved app's detail page
3. Scroll to the scam report form
4. Fill in the form:
   - **Title**: "Suspicious payment requests"
   - **Risk Level**: Select "High"
   - **Description**: "This app requests payment through unofficial channels and does not provide receipts. Multiple users have reported unauthorized charges. The app also asks for sensitive banking information that is not necessary for its stated functionality. Customer support is unresponsive to refund requests."
   - **Evidence URL 1**: "https://example.com/evidence1"
   - **Evidence URL 2**: "https://example.com/evidence2"
5. Click "Submit Scam Report"

**Expected Results:**
- ✅ Page redirects back to app detail page
- ✅ Success message displayed: "Your scam report has been submitted and is pending verification. Thank you for helping keep the community safe!"
- ✅ Success message has green background
- ✅ Scam report form is replaced with pending indicator
- ✅ Pending indicator shows:
  - "Your scam report is pending verification"
  - Report title
  - Risk level badge (red for High)
  - Submission date

---

### Test 4: Description Minimum Length Validation

**Steps:**
1. Log in to the platform
2. Navigate to any approved app's detail page (different from Test 3)
3. Fill in the scam report form:
   - **Title**: "Short description test"
   - **Risk Level**: Select "Low"
   - **Description**: "Too short" (less than 100 characters)
4. Click "Submit Scam Report"

**Expected Results:**
- ✅ Page redirects back with error
- ✅ Error message displayed: "Description must be at least 100 characters"
- ✅ Form retains entered values (title, risk level)
- ✅ Character counter shows red color for insufficient characters
- ✅ No scam report created in database

---

### Test 5: Description Maximum Length Validation

**Steps:**
1. Log in to the platform
2. Navigate to any approved app's detail page
3. Fill in the scam report form:
   - **Title**: "Long description test"
   - **Risk Level**: Select "Medium"
   - **Description**: Copy and paste a text longer than 3000 characters
4. Click "Submit Scam Report"

**Expected Results:**
- ✅ Page redirects back with error
- ✅ Error message displayed: "Description cannot exceed 3000 characters"
- ✅ Form retains entered values
- ✅ No scam report created in database

---

### Test 6: Risk Level Required Validation

**Steps:**
1. Log in to the platform
2. Navigate to any approved app's detail page
3. Fill in the scam report form:
   - **Title**: "Missing risk level"
   - **Risk Level**: Do NOT select any option
   - **Description**: "This is a valid description with more than one hundred characters to meet the minimum requirement for scam report submission."
4. Click "Submit Scam Report"

**Expected Results:**
- ✅ Page redirects back with error
- ✅ Error message displayed: "Risk level is required"
- ✅ Form retains entered values
- ✅ No scam report created in database

---

### Test 7: Invalid Evidence URL Validation

**Steps:**
1. Log in to the platform
2. Navigate to any approved app's detail page
3. Fill in the scam report form:
   - **Title**: "Invalid URL test"
   - **Risk Level**: Select "High"
   - **Description**: "This is a valid description with more than one hundred characters to meet the minimum requirement for scam report submission."
   - **Evidence URL 1**: "not-a-valid-url"
4. Click "Submit Scam Report"

**Expected Results:**
- ✅ Page redirects back with error
- ✅ Error message displayed about invalid URL format
- ✅ Form retains entered values
- ✅ No scam report created in database

---

### Test 8: Multiple Evidence URLs (Max 5)

**Steps:**
1. Log in to the platform
2. Navigate to any approved app's detail page
3. Fill in the scam report form:
   - **Title**: "Multiple evidence URLs"
   - **Risk Level**: Select "Medium"
   - **Description**: "This is a valid description with more than one hundred characters to meet the minimum requirement for scam report submission."
   - **Evidence URL 1**: "https://example.com/evidence1"
   - **Evidence URL 2**: "https://example.com/evidence2"
   - **Evidence URL 3**: "https://example.com/evidence3"
   - **Evidence URL 4**: "https://example.com/evidence4"
   - **Evidence URL 5**: "https://example.com/evidence5"
4. Click "Submit Scam Report"

**Expected Results:**
- ✅ Page redirects back to app detail page
- ✅ Success message displayed
- ✅ Scam report created with all 5 evidence URLs
- ✅ Pending indicator shows the report

---

### Test 9: Character Counter Functionality

**Steps:**
1. Log in to the platform
2. Navigate to any approved app's detail page
3. Click in the description textarea
4. Start typing

**Expected Results:**
- ✅ Character counter updates in real-time
- ✅ Counter shows red color when < 100 characters
- ✅ Counter shows green color when 100-2800 characters
- ✅ Counter shows yellow color when > 2800 characters
- ✅ Counter format: "X / 3000 characters (minimum 100)"

---

### Test 10: Risk Level Badge Styling

**Steps:**
1. Log in to the platform
2. Navigate to any approved app's detail page
3. Observe the risk level selection options

**Expected Results:**
- ✅ Low risk badge has yellow background
- ✅ Medium risk badge has orange background
- ✅ High risk badge has red background
- ✅ All badges have white text (except Low which has black)
- ✅ Badges are styled consistently

---

### Test 11: Pending Report Prevents Duplicate Submission

**Steps:**
1. Log in with an account that has already submitted a pending scam report (from Test 3)
2. Navigate to the same app's detail page
3. Scroll to the "Scam Reports" section

**Expected Results:**
- ✅ Scam report submission form is NOT visible
- ✅ Pending report indicator is displayed instead
- ✅ Indicator shows:
  - "Your scam report is pending verification"
  - Report title
  - Risk level badge
  - Submission date
- ✅ User cannot submit another report for this app

---

### Test 12: CSRF Protection

**Steps:**
1. Log in to the platform
2. Navigate to any approved app's detail page
3. Open browser developer tools (F12)
4. Go to the Network tab
5. Fill in and submit the scam report form
6. Inspect the POST request

**Expected Results:**
- ✅ Request includes CSRF token in form data
- ✅ Token field name is "csrf_test_name" or similar
- ✅ Request succeeds with valid token

---

### Test 13: Authentication Redirect

**Steps:**
1. Log out of the platform
2. Manually navigate to: `http://your-domain/apps/submit-scam-report/1` (POST request)
   - Use a tool like Postman or curl
   - Or use browser console to submit form programmatically

**Expected Results:**
- ✅ Request redirects to login page
- ✅ Error message: "You must be logged in to submit a scam report"
- ✅ No scam report created

---

### Test 14: Database Verification

**Steps:**
1. Complete Test 3 (submit valid scam report)
2. Open database management tool (phpMyAdmin, MySQL Workbench, etc.)
3. Query the `scam_reports` table:
   ```sql
   SELECT * FROM scam_reports ORDER BY created_at DESC LIMIT 1;
   ```

**Expected Results:**
- ✅ New record exists in `scam_reports` table
- ✅ `approval_status` = 'pending'
- ✅ `app_id` matches the app you reported
- ✅ `user_id` matches your user ID
- ✅ `title` matches what you entered
- ✅ `description` matches what you entered
- ✅ `risk_level` matches what you selected
- ✅ `evidence_urls` is a JSON array with the URLs you provided
- ✅ `created_at` timestamp is recent

---

### Test 15: Error Handling

**Steps:**
1. Temporarily stop the MySQL database service
2. Log in to the platform
3. Navigate to any approved app's detail page
4. Fill in and submit a valid scam report form
5. Restart the MySQL database service

**Expected Results:**
- ✅ Error message displayed: "An error occurred while submitting your scam report. Please try again."
- ✅ Error is logged in application logs
- ✅ Page does not crash
- ✅ User can retry submission after database is restored

---

## Database Queries for Verification

### Check Pending Scam Reports
```sql
SELECT 
    sr.id,
    sr.title,
    sr.risk_level,
    sr.approval_status,
    sr.created_at,
    u.username,
    a.name as app_name
FROM scam_reports sr
JOIN users u ON sr.user_id = u.id
JOIN apps a ON sr.app_id = a.id
WHERE sr.approval_status = 'pending'
ORDER BY sr.created_at DESC;
```

### Check Evidence URLs
```sql
SELECT 
    id,
    title,
    evidence_urls
FROM scam_reports
WHERE evidence_urls IS NOT NULL
ORDER BY created_at DESC;
```

### Count Reports by Risk Level
```sql
SELECT 
    risk_level,
    COUNT(*) as count
FROM scam_reports
WHERE approval_status = 'pending'
GROUP BY risk_level;
```

---

## Troubleshooting

### Issue: Form not visible when logged in
**Solution:**
- Check if user has a pending scam report for this app
- Check if app is approved (form only shows for approved apps)
- Clear browser cache and reload page

### Issue: Validation errors not displaying
**Solution:**
- Check if session is working properly
- Verify CodeIgniter validation library is loaded
- Check browser console for JavaScript errors

### Issue: Character counter not updating
**Solution:**
- Check browser console for JavaScript errors
- Verify JavaScript is enabled in browser
- Clear browser cache and reload page

### Issue: Success message not displaying
**Solution:**
- Check if session flash data is working
- Verify redirect is happening correctly
- Check if success message div is present in view

---

## Test Results Checklist

Use this checklist to track your testing progress:

- [ ] Test 1: View form (authenticated)
- [ ] Test 2: View form (unauthenticated)
- [ ] Test 3: Submit valid report
- [ ] Test 4: Minimum length validation
- [ ] Test 5: Maximum length validation
- [ ] Test 6: Risk level required
- [ ] Test 7: Invalid URL validation
- [ ] Test 8: Multiple evidence URLs
- [ ] Test 9: Character counter
- [ ] Test 10: Risk level badges
- [ ] Test 11: Pending report prevents duplicate
- [ ] Test 12: CSRF protection
- [ ] Test 13: Authentication redirect
- [ ] Test 14: Database verification
- [ ] Test 15: Error handling

---

## Conclusion

After completing all tests, verify that:
- ✅ All acceptance criteria are met
- ✅ Form validation works correctly
- ✅ User experience is smooth and intuitive
- ✅ Security measures are in place
- ✅ Database records are created correctly
- ✅ Error handling is robust

**If all tests pass, Task 28 is successfully implemented!**
