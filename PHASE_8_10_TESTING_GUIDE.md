# 🧪 PHASE 8.10 TESTING GUIDE - COI MANAGEMENT UI

**Date:** January 5, 2025  
**Phase:** 8.10 - COI Management UI (Chair + Reviewer)  
**Server:** XAMPP (Apache + MySQL)  
**URL:** http://localhost/qly_hthao/qlyhoithao/public

---

## 🎯 TEST OBJECTIVES

Verify that COI Management UI works correctly for both Chair and Reviewer roles:
- ✅ Chair can view, resolve COI cases
- ✅ Reviewer can declare, view, retract COI
- ✅ AJAX search works properly
- ✅ Database updates correctly
- ✅ Navigation flows smoothly

---

## 📋 PRE-TEST CHECKLIST

### 1. Verify XAMPP is Running
- ✅ Apache: Running on port 80
- ✅ MySQL: Running on port 3306
- ✅ Access: http://localhost/qly_hthao/qlyhoithao/public

### 2. Check Database
```sql
-- Verify tables exist
SHOW TABLES LIKE 'COI';
SHOW TABLES LIKE 'XuLyCOI';
SHOW TABLES LIKE 'LoaiCOI';
SHOW TABLES LIKE 'LoaiXuLyCOI';

-- Check existing COI data
SELECT COUNT(*) as total_coi FROM COI;
SELECT COUNT(*) as resolved FROM XuLyCOI;
```

### 3. Check Test Accounts
```sql
-- Chair account
SELECT * FROM NguoiDung WHERE email = 'chair@test.com';

-- Reviewer account  
SELECT * FROM NguoiDung WHERE email = 'reviewer@test.com';

-- Verify roles
SELECT u.email, r.role_name 
FROM NguoiDung u
JOIN VaiTroNguoiDung vtn ON u.user_id = vtn.user_id
JOIN VaiTro r ON vtn.role_code = r.role_code
WHERE u.email IN ('chair@test.com', 'reviewer@test.com');
```

### 4. Routes Verification
```bash
# In terminal (already done)
php artisan route:list --name=chair.coi
php artisan route:list --name=reviewer.coi
```

**Expected:**
- 5 chair routes ✅
- 6 reviewer routes ✅
- Total: 11 routes ✅

---

## 🔬 TEST SCENARIOS

## PART 1: CHAIR TESTS (30 minutes)

### Test 1.1: Access COI Management
**Steps:**
1. Open browser: http://localhost/qly_hthao/qlyhoithao/public/login
2. Login as Chair:
   - Email: `chair@test.com`
   - Password: `password123` (or your test password)
3. Click "Dashboard" menu
4. In sidebar, click "Kiểm tra COI" button

**Expected Results:**
- ✅ Redirects to `/chair/coi` (COI list page)
- ✅ Orange gradient navbar displays "Quản lý COI"
- ✅ 5 statistics cards show:
  - Total COI
  - Unresolved COI (red)
  - Resolved COI (green)
  - Declared by reviewer (purple)
  - Auto-detected (yellow)
- ✅ COI table displays with columns:
  - Paper, Reviewer, COI Type, Source, Date, Status, Actions

**Screenshot:** Take screenshot and save as `test_1.1_chair_coi_list.png`

---

### Test 1.2: View COI Statistics
**Steps:**
1. On COI list page, observe the statistics cards
2. Note the numbers

**Expected Results:**
- ✅ Numbers match database query results
- ✅ Cards have correct colors (red, green, purple, yellow)
- ✅ Hover effects work on cards

**Verify with SQL:**
```sql
-- Total COI
SELECT COUNT(*) FROM COI WHERE paper_id IN (
    SELECT paper_id FROM BaiBao WHERE conference_id IN (
        SELECT DISTINCT conference_id FROM HoiThao h
        JOIN BaiBao b ON h.conference_id = b.conference_id
        JOIN PhanCong p ON b.paper_id = p.paper_id
        WHERE p.reviewer_id IN (
            SELECT user_id FROM VaiTroNguoiDung WHERE role_code = 'CHAIR'
        )
    )
);

-- Unresolved
SELECT COUNT(*) FROM COI WHERE coi_id NOT IN (SELECT coi_id FROM XuLyCOI);

-- Resolved
SELECT COUNT(*) FROM XuLyCOI;

-- By Source
SELECT source_type, COUNT(*) FROM COI GROUP BY source_type;
```

---

### Test 1.3: View COI Details
**Steps:**
1. In COI table, find a COI case
2. Click "Chi tiết" link

**Expected Results:**
- ✅ Redirects to `/chair/coi/{id}` (detail page)
- ✅ Page shows 3-column layout:
  - **Left:** COI Information (red border)
  - **Middle:** Paper Information (blue border)
  - **Right Sidebar:** Reviewer Info (purple) + Status + Actions
- ✅ All information displays correctly:
  - COI type badge (red)
  - Source badge (purple or yellow)
  - Evidence text
  - Paper title, abstract, keywords
  - Reviewer name, email, organization
  - Resolution status (if resolved)

**Screenshot:** Take screenshot as `test_1.3_chair_coi_detail.png`

---

### Test 1.4: Resolve COI (Unresolved Case)
**Steps:**
1. From COI detail page, click "Giải quyết COI" button (green)
2. Or from list page, click "Giải quyết" link for unresolved case

**Expected Results:**
- ✅ Redirects to `/chair/coi/{id}/resolve`
- ✅ Page displays:
  - COI summary card (paper, reviewer, COI type)
  - Resolution options (radio buttons)
  - Each option has description and warning/info
  - Note textarea (optional)
  - Yellow warning card
  - Cancel and Confirm buttons

**Screenshot:** Take screenshot as `test_1.4_chair_resolve_form.png`

---

### Test 1.5: Submit COI Resolution
**Steps:**
1. On resolve form, select a resolution option:
   - Try "REMOVE_ASSIGNMENT" (Xóa phân công)
2. Add note: "Test resolution for COI case"
3. Click "Xác nhận giải quyết" button
4. Confirmation modal appears
5. Click "Xác nhận" in modal

**Expected Results:**
- ✅ Confirmation modal shows before submit
- ✅ After confirm, redirects to COI list
- ✅ Success message displays: "Giải quyết COI thành công"
- ✅ COI case now shows "Đã xử lý" (green badge)
- ✅ If "REMOVE_ASSIGNMENT", assignment deleted from database

**Verify with SQL:**
```sql
-- Check resolution was saved
SELECT * FROM XuLyCOI WHERE coi_id = {your_coi_id} ORDER BY resolved_at DESC LIMIT 1;

-- Check assignment was removed (if REMOVE_ASSIGNMENT)
SELECT * FROM PhanCong WHERE paper_id = {paper_id} AND reviewer_id = {reviewer_id};
-- Should return 0 rows
```

**Screenshot:** Take screenshot of success message as `test_1.5_chair_resolution_success.png`

---

### Test 1.6: View Resolved COI
**Steps:**
1. Click "Chi tiết" for a resolved COI case

**Expected Results:**
- ✅ Status card shows "Đã xử lý" (green)
- ✅ Resolution details display:
  - Resolution type name
  - Note from Chair
  - Resolved by name
  - Resolution date
- ✅ "Giải quyết COI" button is hidden (already resolved)

**Screenshot:** Take screenshot as `test_1.6_chair_resolved_detail.png`

---

### Test 1.7: Conference Filter (Multi-Conference Chair)
**Steps:**
1. On COI list page, check conference selector dropdown
2. If available, select different conference
3. Observe COI list updates

**Expected Results:**
- ✅ Dropdown shows all conferences chair manages
- ✅ COI list filters by selected conference
- ✅ Statistics update accordingly

---

## PART 2: REVIEWER TESTS (30 minutes)

### Test 2.1: Access COI Declaration
**Steps:**
1. Logout from Chair account
2. Login as Reviewer:
   - Email: `reviewer@test.com`
   - Password: `password123`
3. Click "Dashboard" menu
4. In sidebar, click "Khai báo COI" button (new menu item)

**Expected Results:**
- ✅ Redirects to `/reviewer/coi` (COI list page)
- ✅ Purple gradient navbar displays "Quản lý COI"
- ✅ 4 statistics cards show:
  - Total COI
  - Unresolved (yellow)
  - Resolved (green)
  - By Type breakdown
- ✅ "Khai báo COI mới" button prominent (purple)
- ✅ COI table displays declared COI

**Screenshot:** Take screenshot as `test_2.1_reviewer_coi_list.png`

---

### Test 2.2: View Declared COI Details
**Steps:**
1. If COI list has entries, click "Chi tiết" link
2. View COI detail page

**Expected Results:**
- ✅ Redirects to `/reviewer/coi/{id}`
- ✅ Page shows:
  - COI Information (red border)
  - Paper Information (blue border)
  - Resolution Status sidebar
- ✅ If unresolved:
  - "Rút lại khai báo" button displays (red)
  - Status shows "Chờ xử lý" (yellow)
- ✅ If resolved:
  - Resolution details display
  - Chair's note visible
  - "Rút lại" button hidden

**Screenshot:** Take screenshot as `test_2.2_reviewer_coi_detail.png`

---

### Test 2.3: Declare New COI
**Steps:**
1. On reviewer COI list, click "Khai báo COI mới" button

**Expected Results:**
- ✅ Redirects to `/reviewer/coi/create`
- ✅ Form displays with sections:
  - Info card explaining when to declare COI (blue)
  - Conference dropdown
  - Paper search input
  - COI type radio buttons
  - Evidence textarea (required)
  - Note textarea (optional)
  - Warning card (yellow)
  - Cancel and Submit buttons
- ✅ Submit button disabled initially

**Screenshot:** Take screenshot as `test_2.3_reviewer_declare_form.png`

---

### Test 2.4: AJAX Paper Search
**Steps:**
1. On declare form, select a conference from dropdown
2. Wait 1-2 seconds for papers to load
3. Type in search box: partial paper title or ID
4. Observe results update

**Expected Results:**
- ✅ After selecting conference, papers load automatically
- ✅ Loading spinner shows during fetch
- ✅ Papers display as radio button list with:
  - Paper title (bold)
  - Paper ID
  - Status badge
  - Assignment date
- ✅ Search filters papers in real-time (500ms debounce)
- ✅ Only shows papers:
  - Assigned to this reviewer
  - Not yet declared COI
  - In selected conference
- ✅ If no papers: "Không tìm thấy bài báo phù hợp" message

**Screenshot:** Take screenshot as `test_2.4_reviewer_ajax_search.png`

**Test AJAX in DevTools:**
- Open browser DevTools (F12)
- Go to Network tab
- Type in search box
- Verify AJAX request to `/reviewer/coi/search-papers`
- Check response JSON contains paper data

---

### Test 2.5: Submit COI Declaration
**Steps:**
1. Select a conference
2. Select a paper from list
3. Select a COI type (e.g., "Đồng tác giả")
4. Enter evidence: "I am co-author with the paper author on another publication in 2024"
5. (Optional) Enter note: "Conference paper ABC-2024"
6. Click "Gửi khai báo" button

**Expected Results:**
- ✅ Form validates before submit
- ✅ Submit button enabled only when all required fields filled
- ✅ Character counters update as you type
- ✅ After submit, redirects to `/reviewer/coi` (list page)
- ✅ Success message: "Khai báo COI thành công. Chair sẽ xem xét và xử lý."
- ✅ New COI appears in table with "Chờ xử lý" status

**Verify with SQL:**
```sql
-- Check COI was saved
SELECT * FROM COI 
WHERE reviewer_id = (SELECT user_id FROM NguoiDung WHERE email = 'reviewer@test.com')
AND source_type = 'DECLARED'
ORDER BY created_at DESC LIMIT 1;

-- Verify fields
-- paper_id, reviewer_id, coi_code, source_type, evidence, note, detected_at
```

**Screenshot:** Take screenshot as `test_2.5_reviewer_declaration_success.png`

---

### Test 2.6: Duplicate COI Prevention
**Steps:**
1. Try to declare COI for the same paper again
2. Select same conference and paper
3. Fill form and submit

**Expected Results:**
- ✅ Error message displays: "Bạn đã khai báo COI cho bài báo này rồi."
- ✅ Form data retained (not cleared)
- ✅ No duplicate COI created in database

**Screenshot:** Take screenshot as `test_2.6_reviewer_duplicate_error.png`

---

### Test 2.7: Retract COI Declaration
**Steps:**
1. Go to reviewer COI list
2. Find an UNRESOLVED COI (status "Chờ xử lý")
3. Click "Rút lại" button
4. JavaScript confirmation dialog appears
5. Click "OK" to confirm

**Expected Results:**
- ✅ Confirmation dialog shows before deletion
- ✅ After confirm, redirects to COI list
- ✅ Success message: "Đã rút lại khai báo COI thành công."
- ✅ COI removed from list
- ✅ COI deleted from database

**Verify with SQL:**
```sql
-- COI should be deleted
SELECT * FROM COI WHERE coi_id = {retracted_coi_id};
-- Should return 0 rows
```

**Screenshot:** Take screenshot as `test_2.7_reviewer_retraction_success.png`

---

### Test 2.8: Cannot Retract Resolved COI
**Steps:**
1. Ask Chair to resolve a COI first (Test 1.5)
2. As Reviewer, view that COI detail
3. Check if "Rút lại" button exists

**Expected Results:**
- ✅ "Rút lại khai báo" button NOT displayed
- ✅ Only "Quay lại danh sách" button visible
- ✅ Resolution details display (Chair's decision)

---

## PART 3: INTEGRATION TESTS (15 minutes)

### Test 3.1: Full Workflow (Reviewer → Chair)
**Steps:**
1. **As Reviewer:**
   - Declare new COI for a paper
   - Note the paper ID and COI ID
2. **As Chair:**
   - View COI list, find the new COI
   - Check it shows "Chưa xử lý" status
   - View detail, then resolve with "REMOVE_ASSIGNMENT"
3. **As Reviewer:**
   - View COI list again
   - Check COI now shows "Đã xử lý"
   - View detail, see Chair's resolution

**Expected Results:**
- ✅ COI flows from Reviewer → Chair correctly
- ✅ Status updates in real-time
- ✅ Assignment removed from database
- ✅ Both users see correct information

---

### Test 3.2: Dashboard Navigation
**Steps:**
1. Test all navigation links:
   - Chair: Dashboard → COI → Papers → COI
   - Reviewer: Dashboard → COI → Assignments → COI
2. Click back buttons
3. Click breadcrumbs (if any)

**Expected Results:**
- ✅ All links work correctly
- ✅ No broken links
- ✅ Back buttons return to correct pages
- ✅ URL structure logical

---

### Test 3.3: Responsive Design
**Steps:**
1. Open DevTools (F12)
2. Toggle device toolbar (Ctrl+Shift+M)
3. Test on different screen sizes:
   - Desktop (1920x1080)
   - Tablet (768x1024)
   - Mobile (375x667)

**Expected Results:**
- ✅ Statistics cards stack correctly
- ✅ Tables scroll horizontally on mobile
- ✅ Forms remain usable
- ✅ Buttons don't overlap
- ✅ Text readable at all sizes

---

## PART 4: ERROR HANDLING TESTS (15 minutes)

### Test 4.1: Unauthorized Access
**Steps:**
1. As Reviewer, try to access Chair URLs directly:
   - http://localhost/qly_hthao/qlyhoithao/public/chair/coi
2. As Chair, try to access Reviewer URLs:
   - http://localhost/qly_hthao/qlyhoithao/public/reviewer/coi/create

**Expected Results:**
- ✅ Redirects to dashboard with error message
- ✅ OR shows 403 Forbidden page
- ✅ Does NOT show unauthorized content

---

### Test 4.2: Invalid COI ID
**Steps:**
1. Try to access non-existent COI:
   - http://localhost/qly_hthao/qlyhoithao/public/chair/coi/99999
   - http://localhost/qly_hthao/qlyhoithao/public/reviewer/coi/99999

**Expected Results:**
- ✅ Redirects to list page
- ✅ Error message: "Không tìm thấy COI..."
- ✅ No fatal errors

---

### Test 4.3: Form Validation
**Steps:**
1. On declare form, try to submit without filling:
   - No conference selected
   - No paper selected
   - No COI type selected
   - No evidence entered
2. Try each invalid state

**Expected Results:**
- ✅ Submit button disabled when invalid
- ✅ Server-side validation catches errors
- ✅ Error messages display in Vietnamese
- ✅ Form data retained after error

---

### Test 4.4: AJAX Errors
**Steps:**
1. Open DevTools Network tab
2. Throttle connection to "Slow 3G"
3. Try AJAX paper search
4. Observe loading behavior

**Expected Results:**
- ✅ Loading spinner shows
- ✅ Doesn't freeze UI
- ✅ Handles timeout gracefully
- ✅ Shows error if AJAX fails

---

## 📊 TEST RESULTS SUMMARY

### Completion Checklist

**Chair Tests (7 scenarios):**
- [ ] 1.1 Access COI Management
- [ ] 1.2 View Statistics
- [ ] 1.3 View COI Details
- [ ] 1.4 Resolve COI Form
- [ ] 1.5 Submit Resolution
- [ ] 1.6 View Resolved COI
- [ ] 1.7 Conference Filter

**Reviewer Tests (8 scenarios):**
- [ ] 2.1 Access COI Declaration
- [ ] 2.2 View Declared COI Details
- [ ] 2.3 Declare New COI Form
- [ ] 2.4 AJAX Paper Search
- [ ] 2.5 Submit Declaration
- [ ] 2.6 Duplicate Prevention
- [ ] 2.7 Retract Declaration
- [ ] 2.8 Cannot Retract Resolved

**Integration Tests (3 scenarios):**
- [ ] 3.1 Full Workflow
- [ ] 3.2 Dashboard Navigation
- [ ] 3.3 Responsive Design

**Error Handling (4 scenarios):**
- [ ] 4.1 Unauthorized Access
- [ ] 4.2 Invalid COI ID
- [ ] 4.3 Form Validation
- [ ] 4.4 AJAX Errors

**Total:** 22 test scenarios

---

## 🐛 BUG REPORTING

If you find any bugs, please note:

**Bug Report Format:**
```
Bug ID: BUG-8.10-{number}
Severity: Critical / High / Medium / Low
Test Case: {Test number}
URL: {Page URL}
Steps to Reproduce:
1. 
2. 
3. 
Expected: {What should happen}
Actual: {What actually happened}
Screenshot: {Filename}
SQL Query: {If database issue}
Error Message: {If any}
Browser: {Chrome/Firefox/Edge}
```

**Example:**
```
Bug ID: BUG-8.10-001
Severity: High
Test Case: Test 2.4
URL: http://localhost/qly_hthao/qlyhoithao/public/reviewer/coi/create
Steps:
1. Login as reviewer
2. Click "Khai báo COI mới"
3. Select conference
4. Wait for papers to load
Expected: Papers list displays
Actual: Blank screen, AJAX returns 500 error
Screenshot: bug_001_ajax_error.png
Error Message: "Call to undefined method"
Browser: Chrome 120
```

---

## ✅ SIGN-OFF

**Tester Name:** ___________________  
**Date:** ___________________  
**Pass/Fail:** ___________________  
**Comments:**
___________________________________
___________________________________
___________________________________

**Phase 8.10 Status:**
- [ ] All tests passed - Ready for Phase 8.11
- [ ] Some bugs found - Needs fixes before continuing
- [ ] Critical issues - Requires immediate attention

---

## 📝 NOTES

### Known Limitations:
1. AJAX search requires JavaScript enabled
2. Alpine.js requires modern browser (IE11 not supported)
3. Large datasets may slow statistics calculation

### Performance Expectations:
- Page load: < 2 seconds
- AJAX search: < 500ms
- Form submit: < 1 second
- Database queries: < 100ms each

### Browser Compatibility:
- ✅ Chrome 100+
- ✅ Firefox 100+
- ✅ Edge 100+
- ⚠️ Safari (may need testing)
- ❌ IE11 (not supported)

---

**Good luck with testing!** 🧪🚀

*Document created: January 5, 2025*  
*Phase 8.10 Testing Guide*
