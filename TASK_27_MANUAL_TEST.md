# Task 27: Review Submission - Manual Testing Guide

## Quick Test Checklist

### Prerequisites
- ✅ Database migrated with all tables
- ✅ At least one approved app in database
- ✅ At least one user account created

### Test Scenarios

#### 1. Unauthenticated User Experience
**Steps:**
1. Open browser and navigate to an app detail page (e.g., `http://localhost/app-review/apps/test-app`)
2. Scroll to the "User Reviews" section

**Expected Results:**
- ✅ Login/register prompt is displayed
- ✅ Review submission form is NOT displayed
- ✅ Message: "Login or register to write a review"

---

#### 2. Authenticated User - First Review
**Steps:**
1. Login to the platform
2. Navigate to an app detail page
3. Scroll to the "User Reviews" section

**Expected Results:**
- ✅ Review submission form is displayed
- ✅ Form has purple gradient header "Write a Review"
- ✅ Star rating input (1-5 stars) is visible
- ✅ Title input field is present
- ✅ Review text textarea is present
- ✅ Character counter shows "0 / 2000 characters (minimum 50)"
- ✅ Optional "Pros" textarea is present
- ✅ Optional "Cons" textarea is present
- ✅ "Submit Review" button is present

---

#### 3. Valid Review Submission
**Steps:**
1. Login and navigate to app detail page
2. Click on a star rating (e.g., 4 stars)
3. Enter title: "Great app!"
4. Enter review text (at least 50 characters): "This is an excellent app with great features. I highly recommend it to anyone looking for this type of functionality."
5. (Optional) Enter pros: "Easy to use, fast performance"
6. (Optional) Enter cons: "Could use more customization"
7. Click "Submit Review"

**Expected Results:**
- ✅ Page redirects back to app detail page
- ✅ Green success alert appears: "Your review has been submitted and is pending approval. Thank you for your feedback!"
- ✅ Review form is hidden
- ✅ Yellow pending review indicator appears
- ✅ Pending indicator shows: "Your review is pending approval"
- ✅ Pending indicator displays rating (⭐⭐⭐⭐), title, and submission date

---

#### 4. Character Counter Functionality
**Steps:**
1. Login and navigate to app detail page
2. Click in the review text textarea
3. Start typing

**Expected Results:**
- ✅ Character count updates in real-time
- ✅ Count is RED when < 50 characters
- ✅ Count is GREEN when 50-1900 characters
- ✅ Count is YELLOW when > 1900 characters
- ✅ Count shows current/2000 format

---

#### 5. Star Rating Interaction
**Steps:**
1. Login and navigate to app detail page
2. Hover over stars in the rating input

**Expected Results:**
- ✅ Stars turn yellow on hover
- ✅ All stars to the right of hovered star also turn yellow
- ✅ Clicking a star selects that rating
- ✅ Selected stars remain yellow

---

#### 6. Validation - Rating Required
**Steps:**
1. Login and navigate to app detail page
2. Fill out title and review text (valid)
3. Do NOT select a rating
4. Click "Submit Review"

**Expected Results:**
- ✅ Form does not submit (HTML5 validation)
- ✅ Browser shows "Please fill out this field" or similar message

---

#### 7. Validation - Text Too Short
**Steps:**
1. Login and navigate to app detail page
2. Select a rating (e.g., 4 stars)
3. Enter title: "Test"
4. Enter review text: "Too short" (< 50 characters)
5. Click "Submit Review"

**Expected Results:**
- ✅ Page redirects back
- ✅ Red error alert appears
- ✅ Error message: "Review must be at least 50 characters"
- ✅ Form retains entered data (old input)

---

#### 8. Validation - Text Too Long
**Steps:**
1. Login and navigate to app detail page
2. Select a rating
3. Enter title
4. Enter review text > 2000 characters
5. Click "Submit Review"

**Expected Results:**
- ✅ Page redirects back
- ✅ Red error alert appears
- ✅ Error message: "Review cannot exceed 2000 characters"

---

#### 9. Duplicate Review Prevention
**Steps:**
1. Login and submit a review for an app (follow Test 3)
2. Navigate away and come back to the same app detail page
3. Scroll to "User Reviews" section

**Expected Results:**
- ✅ Review submission form is NOT displayed
- ✅ Pending review indicator IS displayed
- ✅ Indicator shows the previously submitted review details

**Additional Test:**
1. Try to submit another review by directly posting to the endpoint (using browser dev tools or Postman)

**Expected Results:**
- ✅ Error message: "You have already submitted a review for this app"
- ✅ No duplicate review created in database

---

#### 10. Unauthenticated Submission Attempt
**Steps:**
1. Logout from the platform
2. Using browser dev tools or Postman, attempt to POST to `/apps/submit-review/1` with review data

**Expected Results:**
- ✅ Redirect to login page
- ✅ Error message: "You must be logged in to submit a review"
- ✅ No review created in database

---

#### 11. Validation - Missing Required Fields
**Steps:**
1. Login and navigate to app detail page
2. Try submitting with only rating (no title or text)

**Expected Results:**
- ✅ HTML5 validation prevents submission
- ✅ Browser highlights missing required fields

**Steps:**
1. Try submitting with only title (no rating or text)

**Expected Results:**
- ✅ HTML5 validation prevents submission

---

#### 12. Optional Fields
**Steps:**
1. Login and navigate to app detail page
2. Fill out required fields (rating, title, text)
3. Leave "Pros" and "Cons" empty
4. Submit review

**Expected Results:**
- ✅ Review submits successfully
- ✅ Success message appears
- ✅ Review created with NULL pros and cons

**Steps:**
1. Submit another review (different app) with pros and cons filled

**Expected Results:**
- ✅ Review submits successfully
- ✅ Pros and cons are saved in database

---

#### 13. Database Verification
**Steps:**
1. Submit a review following Test 3
2. Check database `reviews` table

**Expected Results:**
- ✅ New review record exists
- ✅ `app_id` matches the app
- ✅ `user_id` matches logged-in user
- ✅ `rating` is correct (1-5)
- ✅ `title` is correct
- ✅ `review_text` is correct
- ✅ `approval_status` is 'pending'
- ✅ `created_at` is current timestamp
- ✅ `helpful_count` is 0

---

#### 14. Multiple Apps
**Steps:**
1. Login and submit a review for App A
2. Navigate to App B detail page
3. Check if review form is displayed

**Expected Results:**
- ✅ Review form IS displayed for App B
- ✅ User can submit a review for App B
- ✅ Each app can have one review per user

---

#### 15. Admin View (if applicable)
**Steps:**
1. Login as admin
2. Navigate to Admin > Reviews
3. Check pending reviews list

**Expected Results:**
- ✅ Newly submitted review appears in pending list
- ✅ Review details are correct
- ✅ Admin can approve/reject the review

---

## Database Queries for Verification

### Check Review Count for User
```sql
SELECT COUNT(*) FROM reviews WHERE user_id = 1;
```

### Check Pending Reviews
```sql
SELECT * FROM reviews WHERE approval_status = 'pending' ORDER BY created_at DESC;
```

### Check User's Review for Specific App
```sql
SELECT * FROM reviews WHERE user_id = 1 AND app_id = 1;
```

### Check Review Details
```sql
SELECT 
    r.id,
    r.rating,
    r.title,
    r.review_text,
    r.approval_status,
    r.created_at,
    u.username,
    a.name as app_name
FROM reviews r
JOIN users u ON r.user_id = u.id
JOIN apps a ON r.app_id = a.id
WHERE r.id = 1;
```

---

## Common Issues and Solutions

### Issue: Form not displaying
**Solution:** Check if user is logged in and hasn't already reviewed the app

### Issue: Character counter not working
**Solution:** Check browser console for JavaScript errors

### Issue: Star rating not clickable
**Solution:** Check if CSS is loaded correctly

### Issue: Validation not working
**Solution:** Check if validation rules are defined in ReviewModel

### Issue: Duplicate review error
**Solution:** This is expected behavior - user can only review each app once

### Issue: Review not appearing on page
**Solution:** Reviews are pending by default - check admin panel to approve

---

## Success Criteria Summary

✅ All 15 test scenarios pass
✅ No JavaScript errors in browser console
✅ No PHP errors in server logs
✅ Database records created correctly
✅ User experience is smooth and intuitive
✅ Validation works on both client and server side
✅ Security measures (auth, CSRF, rate limiting) are in place

---

## Test Environment

- **Browser:** Chrome/Firefox/Edge (latest version)
- **PHP Version:** 8.2+
- **Database:** MySQL 8.0+ / MariaDB 10.6+
- **Server:** Apache/Nginx with mod_rewrite enabled

---

## Notes

- Tests should be performed in order for best results
- Clear browser cache if CSS/JS changes don't appear
- Check server logs for any PHP errors
- Use browser dev tools to inspect network requests
- Verify CSRF tokens are being sent with form submissions

