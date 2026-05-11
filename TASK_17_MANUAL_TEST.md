# Task 17 Manual Testing Guide

## Prerequisites
1. Database is set up with the `scam_reports` table
2. Admin user account exists in the database
3. At least one app exists in the database
4. At least one regular user exists in the database

## Test Scenarios

### Scenario 1: View Pending Scam Reports
**Steps:**
1. Log in as admin user
2. Navigate to `http://localhost/app-review/admin/scam-reports`
3. Verify the page loads successfully
4. Verify the page title shows "Scam Report Verification"
5. Verify filter section is visible with Status, Risk Level, and Date filters
6. Verify pending reports are displayed (if any exist)

**Expected Result:**
- Page loads without errors
- Filter section is functional
- Reports are displayed in card format
- Pagination appears if more than 20 reports exist

### Scenario 2: Create Test Scam Report (via database)
**SQL:**
```sql
INSERT INTO scam_reports (app_id, user_id, title, description, risk_level, approval_status, created_at, updated_at)
VALUES (
    1, -- Replace with valid app_id
    2, -- Replace with valid user_id (non-admin)
    'Test Scam Report - Fake Payment Gateway',
    'This app is using a fake payment gateway to steal credit card information. Multiple users have reported unauthorized charges after using this app. Evidence includes screenshots of suspicious payment forms and user complaints on social media.',
    'high',
    'pending',
    NOW(),
    NOW()
);
```

### Scenario 3: Verify a Scam Report
**Steps:**
1. Navigate to `http://localhost/app-review/admin/scam-reports?status=pending`
2. Locate a pending scam report
3. Enter verification notes: "Verified after investigation. Evidence is credible."
4. Click the "Verify" button
5. Confirm the action in the dialog

**Expected Result:**
- Success message appears: "Scam report verified successfully. Trust score recalculated."
- Report status changes to "approved"
- Verification notes are saved
- Trust score for the associated app is recalculated
- If risk level is "high", log message indicates email notification will be sent

**Verification:**
```sql
SELECT approval_status, verification_notes, risk_level 
FROM scam_reports 
WHERE id = [report_id];
```

### Scenario 4: Reject a Scam Report
**Steps:**
1. Navigate to `http://localhost/app-review/admin/scam-reports?status=pending`
2. Locate a pending scam report
3. Enter rejection notes: "Insufficient evidence. Unable to verify claims."
4. Click the "Reject" button
5. Confirm the action in the dialog

**Expected Result:**
- Success message appears: "Scam report rejected successfully."
- Report status changes to "rejected"
- Rejection notes are saved

**Verification:**
```sql
SELECT approval_status, verification_notes 
FROM scam_reports 
WHERE id = [report_id];
```

### Scenario 5: Update Risk Level
**Steps:**
1. Navigate to `http://localhost/app-review/admin/scam-reports`
2. Locate any scam report (pending or approved)
3. Change the risk level dropdown from "medium" to "high"
4. Click the "Update Risk" button
5. Confirm the action in the dialog

**Expected Result:**
- Success message appears: "Risk level updated successfully."
- Risk level changes to "high"
- Risk badge color changes to red
- If report is approved, high-risk notification is triggered

**Verification:**
```sql
SELECT risk_level 
FROM scam_reports 
WHERE id = [report_id];
```

### Scenario 6: Filter by Status
**Steps:**
1. Navigate to `http://localhost/app-review/admin/scam-reports`
2. Select "Approved" from the Status dropdown
3. Click "Filter" button

**Expected Result:**
- Only approved scam reports are displayed
- URL includes `?status=approved`
- Filter maintains selection

### Scenario 7: Filter by Risk Level
**Steps:**
1. Navigate to `http://localhost/app-review/admin/scam-reports`
2. Select "High" from the Risk Level dropdown
3. Click "Filter" button

**Expected Result:**
- Only high-risk scam reports are displayed
- URL includes `?risk_level=high`
- Reports show red risk badges

### Scenario 8: Filter by Date Range
**Steps:**
1. Navigate to `http://localhost/app-review/admin/scam-reports`
2. Enter Date From: 7 days ago
3. Enter Date To: today
4. Click "Filter" button

**Expected Result:**
- Only reports from the last 7 days are displayed
- URL includes `?date_from=YYYY-MM-DD&date_to=YYYY-MM-DD`

### Scenario 9: Pagination
**Steps:**
1. Create 25+ scam reports in the database
2. Navigate to `http://localhost/app-review/admin/scam-reports`
3. Verify pagination appears at the bottom
4. Click "Next" or page number "2"

**Expected Result:**
- Pagination controls appear
- Clicking page numbers loads the correct page
- Current page is highlighted
- Filter parameters are maintained in pagination links

### Scenario 10: Non-Admin Access Denied
**Steps:**
1. Log in as a regular user (non-admin)
2. Navigate to `http://localhost/app-review/admin/scam-reports`

**Expected Result:**
- User is redirected to home page
- Error message appears: "Access denied. You do not have permission to access this page."

### Scenario 11: Unauthenticated Access Denied
**Steps:**
1. Log out (clear session)
2. Navigate to `http://localhost/app-review/admin/scam-reports`

**Expected Result:**
- User is redirected to login page
- Error message appears: "You must be logged in to access this page."
- After login, user is redirected back to the intended URL

### Scenario 12: Invalid Risk Level
**Steps:**
1. Use browser dev tools or API client to send POST request:
   ```
   POST /admin/scam-reports/update-risk/1
   Body: risk_level=invalid
   ```

**Expected Result:**
- Error message appears: "Invalid risk level."
- Risk level remains unchanged in database

### Scenario 13: Non-Existent Report
**Steps:**
1. Navigate to `http://localhost/app-review/admin/scam-reports/verify/99999`
   (Use an ID that doesn't exist)

**Expected Result:**
- Error message appears: "Scam report not found."
- User is redirected back to the list

### Scenario 14: Trust Score Recalculation
**Steps:**
1. Note the trust score of an app before verification
2. Verify a high-risk scam report for that app
3. Check the app's trust score after verification

**Expected Result:**
- Trust score decreases after high-risk scam report is verified
- Cache is invalidated
- New trust score is calculated and stored

**Verification:**
```sql
SELECT trust_score 
FROM apps 
WHERE id = [app_id];
```

### Scenario 15: High-Risk Notification Logging
**Steps:**
1. Verify a scam report with risk level "high"
2. Check the application logs

**Expected Result:**
- Log entry appears: "High-risk scam report approved for app: [app_name] (ID: [app_id])"
- Log entry appears: "Email notification will be sent when NotificationService is implemented (Task 33)"

**Log Location:** `writable/logs/log-[date].log`

## Database Verification Queries

### Check Scam Report Status
```sql
SELECT 
    sr.id,
    sr.title,
    sr.approval_status,
    sr.risk_level,
    sr.verification_notes,
    a.name as app_name,
    u.username
FROM scam_reports sr
JOIN apps a ON sr.app_id = a.id
JOIN users u ON sr.user_id = u.id
ORDER BY sr.created_at DESC
LIMIT 10;
```

### Check Trust Score Changes
```sql
SELECT 
    id,
    name,
    trust_score,
    updated_at
FROM apps
WHERE id = [app_id];
```

### Count Reports by Status
```sql
SELECT 
    approval_status,
    COUNT(*) as count
FROM scam_reports
GROUP BY approval_status;
```

### Count Reports by Risk Level
```sql
SELECT 
    risk_level,
    COUNT(*) as count
FROM scam_reports
WHERE approval_status = 'approved'
GROUP BY risk_level;
```

## Success Criteria

All scenarios should pass with expected results. The implementation is considered complete when:

✅ Admins can view all pending scam reports  
✅ Reports can be verified with optional notes  
✅ Reports can be rejected with optional notes  
✅ Risk level can be updated for any report  
✅ Verification notes are saved and displayed  
✅ Trust score is recalculated after verification  
✅ High-risk approvals trigger notification logging  
✅ Filters work correctly (status, risk level, date range)  
✅ Pagination works correctly  
✅ Non-admin users cannot access the page  
✅ Unauthenticated users are redirected to login  
✅ Invalid inputs are rejected with error messages  
✅ Non-existent reports return appropriate errors  

## Notes

- The email notification feature is a placeholder for Task 33
- Currently logs notification intent instead of sending emails
- All other functionality is fully implemented and operational
