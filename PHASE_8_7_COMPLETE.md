# Phase 8.7 Implementation Complete - Reviewer Assignment

## Implementation Date: October 5, 2025

## ✅ COMPLETED COMPONENTS

### 1. Controller Methods (ChairController.php)
**Total: 5 new methods added**

#### Method 1: `assignReviewers($paperId)`
- **Purpose**: Display reviewer assignment form
- **Lines**: ~140 lines
- **Features**:
  - Verifies chair access via VaiTroNguoiDung
  - Gets paper info with conference details
  - Retrieves paper authors
  - Lists current assignments with review status
  - Queries available reviewers (excludes authors and already assigned)
  - Calculates reviewer workload (current assignments count)
  - Checks COI for all available reviewers
  - Returns assign.blade.php view with all data
- **Authorization**: VaiTroNguoiDung WHERE user_id=Auth::id() AND role_code='CHAIR'
- **Key Queries**:
  - Paper: JOIN HoiThao, VaiTroNguoiDung
  - Authors: JOIN NguoiDung with author_order
  - Current assignments: JOIN NguoiDung, LEFT JOIN PhanBien
  - Available reviewers: VaiTroNguoiDung WHERE role_code='REVIEWER'
  - Workload: GROUP BY reviewer_id COUNT assignments
  - COI: WHERE paper_id + reviewer_id

#### Method 2: `storeAssignment(Request $request, $paperId)`
- **Purpose**: Create new reviewer assignment
- **Lines**: ~100 lines
- **Features**:
  - Validates input (reviewer_id required|exists, deadline required|date|after:today)
  - Verifies chair access to paper
  - Prevents self-review (checks if reviewer is author)
  - Checks for duplicate assignments (UNIQUE constraint)
  - Checks for COI conflicts
  - Generates UUID token for review access
  - Inserts to PhanCongPhanBien with chair_id, status='INVITED', assigned_at=now()
  - Returns JSON response with success/error message
- **Authorization**: Same VaiTroNguoiDung check
- **Validation**:
  - reviewer_id: required|integer|exists:NguoiDung,user_id
  - deadline: required|date|after:today
- **Business Rules**:
  - ❌ Cannot assign author as reviewer
  - ❌ Cannot assign same reviewer twice (UNIQUE paper_id + reviewer_id)
  - ⚠️ Warning if COI exists
- **Returns**: JSON with success, message, assignment info

#### Method 3: `removeAssignment($assignmentId)`
- **Purpose**: Delete reviewer assignment
- **Lines**: ~50 lines
- **Features**:
  - Verifies chair access via paper->conference->VaiTroNguoiDung
  - Checks if review has been submitted
  - Prevents deletion if review completed (data integrity)
  - Deletes assignment record
  - Returns JSON success/error
- **Authorization**: JOIN through BaiBao and VaiTroNguoiDung
- **Business Rules**:
  - ❌ Cannot delete if review submitted (submitted_at NOT NULL)
  - ✅ Can delete INVITED, ACCEPTED, DECLINED if no review
- **Returns**: JSON with success, message

#### Method 4: `checkCOI($paperId, $reviewerId)`
- **Purpose**: Check conflicts of interest
- **Lines**: ~60 lines
- **Features**:
  - Verifies chair access
  - Checks if reviewer is author (automatic COI)
  - Queries COI table with LoaiCOI details
  - Returns detailed COI information
- **Authorization**: Standard VaiTroNguoiDung check
- **COI Types Checked**:
  1. AUTHOR: Reviewer is paper author
  2. Database COI: From COI table with coi_code, source_type, evidence
- **Returns**: JSON with has_coi, coi_info, message

#### Method 5: `suggestReviewers($paperId)`
- **Purpose**: Suggest reviewers (future AI enhancement)
- **Lines**: ~70 lines
- **Features**:
  - Verifies chair access
  - Gets paper info
  - Excludes authors and already assigned
  - Queries available reviewers with workload
  - Sorts by workload ASC (least busy first)
  - Returns top 10 suggestions
- **Authorization**: Standard VaiTroNguoiDung check
- **Algorithm**: Simple workload-based (TODO: Add expertise matching in Phase 8.8)
- **Returns**: JSON with success, suggestions array, message

---

### 2. Routes (web.php)
**Total: 5 new routes added to chair group**

```php
// Reviewer Assignment Routes
Route::get('/papers/{id}/assign', [ChairController::class, 'assignReviewers'])
    ->name('chair.papers.assign');

Route::post('/papers/{id}/assign', [ChairController::class, 'storeAssignment'])
    ->name('chair.papers.assign.store');

Route::delete('/assignments/{id}', [ChairController::class, 'removeAssignment'])
    ->name('chair.assignments.remove');

Route::get('/papers/{paperId}/coi/{reviewerId}', [ChairController::class, 'checkCOI'])
    ->name('chair.papers.coi.check');

Route::get('/papers/{id}/suggest-reviewers', [ChairController::class, 'suggestReviewers'])
    ->name('chair.papers.suggest');
```

**Route Summary:**
- **GET** `/chair/papers/{id}/assign` - Show assignment form
- **POST** `/chair/papers/{id}/assign` - Store new assignment
- **DELETE** `/chair/assignments/{id}` - Remove assignment
- **GET** `/chair/papers/{paperId}/coi/{reviewerId}` - Check COI
- **GET** `/chair/papers/{id}/suggest-reviewers` - Get suggestions

**Middleware:** auth + role:CHAIR (applied to entire chair group)

---

### 3. View: assign.blade.php
**Location**: `resources/views/chair/papers/assign.blade.php`
**Lines**: 420+ lines
**Pattern**: Alpine.js SPA-style interaction

#### View Structure:

**1. Alpine.js State Management:**
```javascript
x-data="{
    searchQuery: '',           // Filter reviewers
    selectedReviewer: null,    // Currently selected reviewer
    deadline: '',              // Assignment deadline
    loading: false,            // Loading state
    message: '',               // Alert message
    messageType: '',           // success/error
    
    // Computed property for filtering
    get filteredReviewers() { ... },
    
    // Methods
    selectReviewer(reviewer),
    assignReviewer(),          // AJAX POST to store
    removeAssignment(id),      // AJAX DELETE
    showMessage(text, type)
}"
```

**2. Paper Info Header:**
- Paper ID badge
- Status badge (color-coded)
- Title (h1)
- Conference name
- Authors list with contact badge
- "Quay lại" back link

**3. Current Assignments Section:**
- Table with 7 columns:
  - Reviewer (name + email + organization)
  - Trạng thái (status badge)
  - Ngày phân công
  - Hạn chót
  - Đã nộp (checkmark if submitted)
  - Thao tác (Delete button - disabled if reviewed)
- Empty state: SVG icon + message
- Remove button: Calls `removeAssignment()` via AJAX DELETE
- Business rule: Cannot delete if submitted_at NOT NULL

**4. Assignment Form:**
- Selected reviewer display
- Deadline date picker (min: tomorrow, max: conference deadline)
- "Phân công reviewer" button
- AJAX POST submission with fetch()
- Success → reload page to show new assignment

**5. Available Reviewers Grid:**
- Search box (filters by name, email, organization)
- 3-column responsive grid (1 on mobile, 2 on tablet, 3 on desktop)
- Reviewer cards:
  - Name + email
  - Organization
  - Workload count ("X bài")
  - COI badge (red) if has_coi = true
  - Click to select (blue border)
  - Red border if COI exists
- Empty state if no results
- Filter updates in real-time with Alpine.js

#### Key Features:

**AJAX Operations:**
- Store assignment: POST with JSON body (reviewer_id, deadline)
- Remove assignment: DELETE with CSRF token
- Both return JSON response
- Success: Show message + reload after 1.5s
- Error: Show error message

**COI Warning:**
- Visual: Red badge on reviewer card
- Behavior: Confirm dialog before assignment
- Data: has_coi and coi_info from backend

**Real-time Search:**
- Filter reviewers by name, email, organization
- Case-insensitive
- Updates immediately (Alpine.js computed property)

**Responsive Design:**
- Mobile: 1 column grid
- Tablet: 2 columns
- Desktop: 3 columns
- All using Tailwind CSS

---

### 4. View Update: show.blade.php
**Change**: Added link to "+ Phân công thêm" button

**Before:**
```blade
<button class="px-4 py-2 bg-orange-600 ...">
    + Phân công thêm
</button>
```

**After:**
```blade
<a href="{{ route('chair.papers.assign', $paper->paper_id) }}" 
   class="px-4 py-2 bg-orange-600 ...">
    + Phân công thêm
</a>
```

**Impact**: Clicking button now navigates to assignment form

---

## 🧪 TESTING CHECKLIST

### Database Verification
- [x] PhanCongPhanBien table: 8 columns confirmed
- [x] UNIQUE constraint on (paper_id, reviewer_id): Verified
- [x] COI table: 7 columns with ENUM source_type: Verified
- [x] Test data: 68 reviewers available
- [x] Test papers: Paper #1, #2, #3 confirmed

### Controller Testing
- [ ] **Test 1**: Access /chair/papers/1/assign (should show form)
  - Expected: Paper info header, current assignments (if any), available reviewers grid
  - Authorization: Only accessible to chair of conference
  
- [ ] **Test 2**: Assign reviewer to paper
  - Method: POST /chair/papers/1/assign
  - Body: `{reviewer_id: 25, deadline: '2025-10-30'}`
  - Expected: Success JSON, new record in PhanCongPhanBien
  - Verify: assignment_id created, status='INVITED', token=UUID
  
- [ ] **Test 3**: Duplicate assignment (should fail)
  - Assign same reviewer twice
  - Expected: 400 error with message "Reviewer này đã được phân công"
  
- [ ] **Test 4**: Assign author as reviewer (should fail)
  - Get author user_id from paper
  - Try to assign as reviewer
  - Expected: 400 error with message "Không thể phân công tác giả"
  
- [ ] **Test 5**: Remove assignment (before review)
  - Method: DELETE /chair/assignments/{id}
  - Expected: Success, record deleted from PhanCongPhanBien
  
- [ ] **Test 6**: Remove assignment (after review submitted)
  - Create review with submitted_at
  - Try to delete assignment
  - Expected: 400 error with message "Không thể xóa phân công đã có bài phản biện"
  
- [ ] **Test 7**: Check COI
  - Method: GET /chair/papers/1/coi/25
  - Expected: JSON with has_coi: false (or true if exists)
  
- [ ] **Test 8**: Suggest reviewers
  - Method: GET /chair/papers/1/suggest-reviewers
  - Expected: JSON with top 10 reviewers sorted by workload ASC

### View Testing
- [ ] **Test 9**: Assignment form display
  - Navigate to /chair/papers/1/assign
  - Check: Paper header visible, authors list, current assignments table
  - Check: Available reviewers grid with cards
  
- [ ] **Test 10**: Search functionality
  - Type in search box
  - Expected: Reviewers filtered in real-time
  
- [ ] **Test 11**: Select reviewer
  - Click reviewer card
  - Expected: Blue border, name appears in "Reviewer được chọn"
  
- [ ] **Test 12**: Assign via form
  - Select reviewer + deadline
  - Click "Phân công reviewer"
  - Expected: Success message, page reloads, new assignment in table
  
- [ ] **Test 13**: Remove assignment via UI
  - Click "Xóa" button on assignment
  - Confirm dialog
  - Expected: Success message, assignment removed
  
- [ ] **Test 14**: COI warning
  - Create COI record for a reviewer
  - Click that reviewer card
  - Expected: Red border, "COI" badge visible
  - Try to assign: Confirm dialog appears
  
- [ ] **Test 15**: Responsive design
  - Test on mobile width (375px)
  - Test on tablet width (768px)
  - Test on desktop width (1024px+)
  - Expected: Grid adjusts (1/2/3 columns)

### Integration Testing
- [ ] **Test 16**: Full workflow
  1. Navigate to /chair/dashboard
  2. Click paper "Chi tiết"
  3. Click "+ Phân công thêm" button
  4. Select reviewer
  5. Set deadline
  6. Submit assignment
  7. Return to paper detail
  8. Verify assignment appears in table
  
- [ ] **Test 17**: Multiple assignments
  - Assign 3 different reviewers to same paper
  - Expected: All 3 appear in current assignments table
  - Verify: workload increases for each reviewer
  
- [ ] **Test 18**: Authorization check
  - Login as non-chair user
  - Try to access /chair/papers/1/assign
  - Expected: 403 Forbidden or redirect
  
- [ ] **Test 19**: Cross-conference access
  - Login as chair of Conference A
  - Try to assign reviewers to paper in Conference B
  - Expected: Access denied

---

## 📊 CURRENT STATUS

### Phase 8.7 Completion: 100%
**All components implemented and ready for testing**

✅ **Completed (Today - Oct 5, 2025):**
1. ✅ 5 controller methods (assignReviewers, storeAssignment, removeAssignment, checkCOI, suggestReviewers)
2. ✅ 5 routes added to web.php
3. ✅ assign.blade.php view created (420+ lines with Alpine.js)
4. ✅ show.blade.php updated with assignment button link
5. ✅ Route verification (8 chair routes confirmed)

### Phase 8 Overall Progress: 55% → 65%

**Before Phase 8.7:** 45% complete
- ✅ Dashboard view (8.6)
- ✅ Papers list view (8.6)
- ✅ Paper detail view (8.6)

**After Phase 8.7:** 65% complete
- ✅ Dashboard view
- ✅ Papers list view
- ✅ Paper detail view
- ✅ **Reviewer assignment feature** ← NEW

**Remaining (35%):**
- ⏸️ COI management view (Phase 8.8)
- ⏸️ Final decision feature (Phase 8.9)
- ⏸️ Reviews management (Phase 8.10)

---

## 🔧 TECHNICAL DETAILS

### Database Interactions

**Tables Used:**
1. **PhanCongPhanBien** (Assignments)
   - INSERT: New assignments
   - DELETE: Remove assignments
   - SELECT: Current assignments, workload calculation
   
2. **COI** (Conflicts of Interest)
   - SELECT: Check conflicts before assignment
   
3. **BaiBao** (Papers)
   - SELECT: Paper info for display
   
4. **TacGia** (Authors)
   - SELECT: Paper authors list
   - Used to prevent self-review
   
5. **NguoiDung** (Users)
   - SELECT: Reviewer info (name, email, organization)
   
6. **VaiTroNguoiDung** (Roles)
   - SELECT: Authorization checks
   - WHERE role_code='REVIEWER': Available reviewers
   - WHERE role_code='CHAIR': Access verification
   
7. **HoiThao** (Conferences)
   - SELECT: Conference info, deadlines
   
8. **PhanBien** (Reviews)
   - SELECT: Check if review submitted (for deletion prevention)
   - LEFT JOIN: Review status in assignments table
   
9. **LoaiCOI** (COI Types)
   - JOIN: COI details with names

### Key SQL Patterns

**1. Authorization Check:**
```sql
SELECT bb.* FROM BaiBao bb
JOIN VaiTroNguoiDung vt ON bb.conference_id = vt.conference_id
WHERE bb.paper_id = ? 
  AND vt.user_id = ?
  AND vt.role_code = 'CHAIR'
```

**2. Available Reviewers (Complex Exclusion):**
```sql
SELECT nd.*, vt.conference_id FROM VaiTroNguoiDung vt
JOIN NguoiDung nd ON vt.user_id = nd.user_id
WHERE vt.role_code = 'REVIEWER'
  AND vt.user_id NOT IN (
      -- Authors
      SELECT user_id FROM TacGia WHERE paper_id = ?
      UNION
      -- Already assigned
      SELECT reviewer_id FROM PhanCongPhanBien WHERE paper_id = ?
  )
```

**3. Workload Calculation:**
```sql
SELECT reviewer_id, COUNT(*) as assignment_count
FROM PhanCongPhanBien
WHERE status_code IN ('INVITED', 'ACCEPTED')
GROUP BY reviewer_id
```

**4. COI Check:**
```sql
SELECT coi.*, lc.coi_name, lc.description
FROM COI coi
JOIN LoaiCOI lc ON coi.coi_code = lc.coi_code
WHERE coi.paper_id = ? AND coi.reviewer_id = ?
```

### AJAX Implementation

**Frontend (Alpine.js + Fetch API):**
```javascript
// Store assignment
fetch('/chair/papers/1/assign', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': csrf_token
    },
    body: JSON.stringify({
        reviewer_id: 25,
        deadline: '2025-10-30'
    })
})

// Remove assignment
fetch('/chair/assignments/123', {
    method: 'DELETE',
    headers: {
        'X-CSRF-TOKEN': csrf_token
    }
})
```

**Backend Response Format:**
```json
{
    "success": true,
    "message": "Đã phân công reviewer thành công",
    "assignment": {
        "assignment_id": 123,
        "reviewer_name": "Reviewer User 25",
        "reviewer_email": "reviewer25@huit.edu.vn",
        "deadline": "2025-10-30"
    }
}
```

### Security Features

1. **CSRF Protection**: All POST/DELETE use CSRF token
2. **Authorization**: Every method checks VaiTroNguoiDung
3. **Input Validation**: Laravel validation rules
4. **SQL Injection Prevention**: Eloquent/Query Builder parameterization
5. **Business Logic Validation**:
   - Prevent self-review
   - Prevent duplicate assignments
   - Prevent deletion of reviewed assignments
   - COI warnings

---

## 🚀 NEXT STEPS

### Immediate (Testing Phase):
1. **Start XAMPP**: Apache + MySQL
2. **Test Route**: Navigate to /chair/papers/1/assign
3. **Test Assignment**: Assign reviewer #25 to paper #1
4. **Verify Database**: Check PhanCongPhanBien table
5. **Test Removal**: Delete assignment (before review)
6. **Test COI**: Create COI record and verify warning

### After Testing:
- **If all tests pass**: Update TODO.md, mark Phase 8.7 complete
- **If errors found**: Debug and fix issues
- **Next phase**: Phase 8.8 (COI Management) or Phase 8.9 (Final Decision)

### Manual Test Commands:
```bash
# Check routes
php artisan route:list --name=chair

# Get papers for testing
php artisan tinker --execute="DB::table('BaiBao')->select('paper_id','title')->limit(3)->get()"

# Get reviewers
php artisan tinker --execute="DB::table('VaiTroNguoiDung')->where('role_code','REVIEWER')->count()"

# Check assignments
php artisan tinker --execute="DB::table('PhanCongPhanBien')->count()"
```

---

## 📈 TIME TRACKING

**Estimated Time**: 6-7 hours  
**Actual Time**: ~2 hours (implementation only, testing pending)

**Breakdown:**
- Controller methods: 45 minutes (5 methods × ~9 min each)
- Routes: 5 minutes
- View creation: 60 minutes (assign.blade.php 420+ lines)
- View update: 5 minutes (show.blade.php button link)
- Documentation: 15 minutes (this file)

**Remaining:**
- Testing: 2-3 hours (19 test cases)
- Bug fixes: 1-2 hours (estimated)

---

## 💡 LESSONS LEARNED

1. **Alpine.js Pattern Works Well**: Reactive state management without full Vue/React
2. **AJAX + Blade Hybrid**: Good balance between SPA and traditional MVC
3. **Authorization Complexity**: VaiTroNguoiDung join needed everywhere
4. **COI Checking**: Important for academic integrity, should be automatic
5. **Workload Calculation**: Simple COUNT works, can enhance with expertise matching later

## 🎯 SUCCESS CRITERIA (MVP)

For Phase 8.7 to be considered complete:

- [x] Chair can view assignment form for any paper they manage
- [x] Chair can see list of available reviewers (excluding authors and assigned)
- [x] Chair can assign reviewer with deadline
- [x] System prevents duplicate assignments (UNIQUE constraint)
- [x] System prevents author self-review
- [x] Chair can remove assignment if review not submitted
- [x] System shows COI warnings
- [x] Reviewer workload displayed
- [ ] All 19 test cases pass ← **TESTING PENDING**

---

**Status**: ✅ Implementation Complete | ⏸️ Testing Pending  
**Next Action**: Run test cases 1-19 and verify all features work correctly
