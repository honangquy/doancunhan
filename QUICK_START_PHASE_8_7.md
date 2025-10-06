# 🚀 Quick Start Guide - Phase 8.7 Testing

## Instant Testing (Copy-Paste Ready)

### 1. Start Your Environment
```bash
# Start XAMPP
# - Open XAMPP Control Panel
# - Start Apache
# - Start MySQL
```

### 2. Quick Database Check
```bash
cd c:\xampp\htdocs\qly_hthao\qlyhoithao
php test_assignment.php
```
**Expected**: All 8 tests pass ✅

### 3. Open Browser
```
http://localhost/qly_hthao/qlyhoithao/public/chair/dashboard
```

### 4. Login as Chair
- **Option A**: If you have chair@test.com account → use that
- **Option B**: Use User ID #7 credentials (check your database)

### 5. Navigate to Assignment Form
**Method 1** (Click through UI):
1. Dashboard → Click "Chi tiết" on any paper
2. Click "+ Phân công thêm" button

**Method 2** (Direct URL):
```
http://localhost/qly_hthao/qlyhoithao/public/chair/papers/52/assign
```

---

## 🎯 Essential Tests (5 minutes)

### Test 1: View Assignment Form ⏱️ 30 seconds
- [ ] Paper title "Nhân Văn" visible
- [ ] Author "Test Author" shown with (Liên hệ) badge
- [ ] "Chưa có reviewer nào được phân công" message
- [ ] Grid of reviewer cards displayed
- [ ] Search box visible

✅ **PASS** if all elements visible

### Test 2: Create Assignment ⏱️ 1 minute
1. Click any reviewer card (e.g., "Reviewer User 25")
2. Card gets blue border ✓
3. Set deadline: `2025-11-04`
4. Click "Phân công reviewer"
5. Success message appears
6. Page reloads

✅ **PASS** if assignment appears in "Current Assignments" table

### Test 3: Search Function ⏱️ 30 seconds
1. Type "User 25" in search box
2. Grid filters to show only matching reviewers
3. Clear search
4. All reviewers shown again

✅ **PASS** if filtering works

### Test 4: Prevent Duplicate ⏱️ 1 minute
1. Try to assign the same reviewer again
2. Select same reviewer
3. Set deadline
4. Click assign
5. Error message: "Reviewer này đã được phân công"

✅ **PASS** if error shown

### Test 5: Remove Assignment ⏱️ 1 minute
1. Click "Xóa" button on an assignment
2. Confirm dialog appears
3. Click OK
4. Success message: "Đã xóa phân công thành công"
5. Assignment removed from table

✅ **PASS** if deletion successful

---

## 🐛 Common Issues & Quick Fixes

### Issue 1: "404 Not Found" on assignment page
**Fix**: Check routes are registered
```bash
php artisan route:list --name=chair
```
Should show 8 routes including `chair.papers.assign`

### Issue 2: "Table 'TacGia' doesn't exist"
**Fix**: Already fixed! Table corrected to `TacGiaBaiBao`
Verify: Search "TacGia')" in ChairController.php → should find 0 results

### Issue 3: No reviewers displayed
**Cause**: All reviewers already assigned or are authors
**Check**: 
```bash
php artisan tinker --execute="echo DB::table('VaiTroNguoiDung')->where('role_code','REVIEWER')->count();"
```
Should return 68+

### Issue 4: CSRF token error on assignment
**Fix**: Check meta tag in view:
```html
<meta name="csrf-token" content="{{ csrf_token() }}">
```
Should be in `<head>` of assign.blade.php

### Issue 5: Assignment succeeds but doesn't appear
**Fix**: Check browser console for JavaScript errors
- Open DevTools (F12)
- Check Console tab
- Look for fetch errors

---

## 📊 Test Data Reference

### Pre-loaded Test Data
| Type | ID | Details |
|------|-----|---------|
| Chair | User #7 | Conference #1 |
| Paper | #52 | "Nhân Văn" [SUBMITTED] |
| Author | User #251 | author@test.com (excluded from reviewers) |
| Reviewers | #25-92, #252 | 68 available users |
| Current Assignments | 0 | Clean slate for Paper #52 |

### Quick Data Queries
```bash
# Check chair user
php artisan tinker --execute="DB::table('VaiTroNguoiDung')->where('role_code','CHAIR')->first()"

# Check paper
php artisan tinker --execute="DB::table('BaiBao')->where('paper_id',52)->first()"

# Check reviewers count
php artisan tinker --execute="DB::table('VaiTroNguoiDung')->where('role_code','REVIEWER')->count()"

# Check assignments
php artisan tinker --execute="DB::table('PhanCongPhanBien')->where('paper_id',52)->get()"
```

---

## 🎬 Demo Scenario (For Stakeholders)

### Scenario: Chair assigns 3 reviewers to a paper

**Setup** (30 seconds):
- Login as chair
- Navigate to Dashboard
- Click "Quản lý bài báo"
- Click "Chi tiết" on "Nhân Văn" paper
- Click "+ Phân công thêm"

**Demo** (2 minutes):

1. **Show Available Reviewers** (10 sec)
   - "Here we see 68 available reviewers"
   - "Each shows their workload"
   - "Search filters in real-time"

2. **Assign First Reviewer** (30 sec)
   - Click "Reviewer User 25"
   - Set deadline: 1 month from now
   - Click "Phán công reviewer"
   - Success! Assignment created

3. **Assign Second Reviewer** (30 sec)
   - Back to assignment page
   - Now shows 1 current assignment
   - Click "Reviewer User 26"
   - Set same deadline
   - Assign successfully

4. **Show Features** (30 sec)
   - "System prevents duplicate assignments"
   - "Authors excluded automatically"
   - "COI warnings shown if applicable"
   - "Can remove assignments before review"

5. **Return to Paper Detail** (20 sec)
   - Click "Quay lại"
   - Shows 2 assignments in table
   - Status: "Đã mời" (Invited)
   - Next: Reviewers accept/decline

**Key Points**:
- ✅ Simple, intuitive interface
- ✅ Real-time search and filtering
- ✅ Automatic validation (no duplicates, no self-review)
- ✅ COI awareness
- ✅ Workload visibility for balanced assignment

---

## 📱 Browser Compatibility

**Tested**: Modern browsers with JavaScript enabled

**Requirements**:
- JavaScript: Enabled (Alpine.js)
- CSS: Tailwind CDN
- Minimum screen width: 375px (mobile)

**Responsive breakpoints**:
- Mobile (< 768px): 1 column
- Tablet (768-1024px): 2 columns
- Desktop (> 1024px): 3 columns

---

## 🔄 Quick Reset (If Needed)

### Reset Paper #52 Assignments
```sql
DELETE FROM PhanCongPhanBien WHERE paper_id = 52;
```

### Reset All Test Data
```sql
-- Remove all assignments for Paper #52 and #53
DELETE FROM PhanCongPhanBien WHERE paper_id IN (52, 53);

-- Remove any test reviews
DELETE FROM PhanBien WHERE assignment_id NOT IN (SELECT assignment_id FROM PhanCongPhanBien);

-- Remove test COI records
DELETE FROM COI WHERE paper_id IN (52, 53);
```

**Then**: Refresh browser, should see clean slate

---

## ✅ Success Criteria Checklist

Phase 8.7 is successful if:
- [ ] Assignment form loads without errors
- [ ] Reviewers displayed in grid (68 available)
- [ ] Can assign reviewer with deadline
- [ ] Assignment appears in current assignments table
- [ ] Cannot assign same reviewer twice (error shown)
- [ ] Author excluded from reviewer list (User #251 not visible)
- [ ] Can delete assignment before review
- [ ] Cannot delete after review submitted
- [ ] Search filters reviewers in real-time
- [ ] Workload displays correctly
- [ ] Page is responsive (mobile, tablet, desktop)

**Minimum to pass**: First 7 items ✓

---

## 📞 Support Information

**Documentation**:
- Implementation: `PHASE_8_7_COMPLETE.md`
- Testing: `PHASE_8_7_TEST_REPORT.md`
- This guide: `QUICK_START_PHASE_8_7.md`

**Test Scripts**:
- Automated tests: `php test_assignment.php`
- Manual checklist: 31 tests in TEST_REPORT.md

**Files Modified**:
- `app/Http/Controllers/Chair/ChairController.php` (+400 lines)
- `routes/web.php` (+5 routes)
- `resources/views/chair/papers/assign.blade.php` (NEW, 420 lines)
- `resources/views/chair/papers/show.blade.php` (+1 line)

---

**Last Updated**: October 5, 2025  
**Status**: ✅ Ready for UAT  
**Estimated Testing Time**: 5-15 minutes (basic to comprehensive)
