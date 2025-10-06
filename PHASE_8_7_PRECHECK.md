# ✅ PRE-IMPLEMENTATION CHECK - PHASE 8.7: REVIEWER ASSIGNMENT

## 📊 SYSTEM STATUS: ALL GREEN ✅

**Date:** January 2025  
**Task:** Implement Reviewer Assignment Feature  
**Status:** Ready to implement

---

## ✅ DATABASE VERIFICATION

### **1. PhanCongPhanBien Table** ✅ VERIFIED
```sql
Columns:
- assignment_id (PK, auto_increment)
- paper_id (FK → BaiBao)
- reviewer_id (FK → NguoiDung)
- chair_id (nullable)
- status_code (default: 'INVITED', FK → TrangThaiPhanCong)
- token (unique, 36 chars)
- assigned_at (timestamp, auto)
- deadline (date, nullable)

Constraints:
- UNIQUE(token)
- UNIQUE(paper_id, reviewer_id) ← Prevents duplicate assignments
```

**Status:** ✅ Structure correct, ready to use

---

### **2. COI Table** ✅ VERIFIED
```sql
Columns:
- coi_id (PK, auto_increment)
- paper_id (FK → BaiBao)
- reviewer_id (FK → NguoiDung)
- coi_code (FK → LoaiCOI)
- source_type (ENUM: 'DECLARED', 'DETECTED')
- evidence (varchar 500, nullable)
- created_at (timestamp)
```

**Status:** ✅ Structure correct, COI check ready

---

### **3. Test Data** ✅ VERIFIED

#### **Reviewers Available:**
- Total: **68 reviewers** (ID 25-92 + reviewer@test.com ID 252)
- All reviewers have:
  - ✅ Full name
  - ✅ Email (pattern: reviewerXX@huit.edu.vn)
  - ✅ Role REVIEWER in VaiTroNguoiDung
  - ⚠️ conference_id = NULL (global reviewers, not conference-specific)

#### **Papers Available:**
```
Paper #52: "Nhân Văm" - SUBMITTED, Conference 1
Paper #53: "Nhân Văm" - SUBMITTED, Conference 1
Author: Test Author (author@test.com)
```

#### **Current Assignments:**
- Total: **0 assignments** (fresh start, ready to test)

---

## ✅ CONTROLLER VERIFICATION

### **ChairController.php** ✅ WORKING
**Current Methods:**
1. ✅ `dashboard()` - Stats, recent papers, conferences, pending actions
2. ✅ `papers()` - Papers list with filters
3. ✅ `showPaper()` - Paper detail with authors, assignments, reviews

**Authorization:**
- ✅ Middleware: `auth`, `role:CHAIR`
- ✅ All queries check via VaiTroNguoiDung table
- ✅ User 253 (chair@test.com) has CHAIR role for Conference 1

**Status:** ✅ All methods working, no errors

---

## ✅ ROUTES VERIFICATION

### **Existing Routes** ✅ WORKING
```php
Route::prefix('chair')->middleware('role:CHAIR')->name('chair.')->group(function () {
    Route::get('/dashboard', [ChairController::class, 'dashboard'])->name('dashboard');
    Route::get('/papers', [ChairController::class, 'papers'])->name('papers');
    Route::get('/papers/{id}', [ChairController::class, 'showPaper'])->name('papers.show');
});
```

**Test Results:**
- ✅ `/chair/dashboard` - Working with SPA
- ✅ `/chair/papers` - Working with SPA
- ✅ `/chair/papers/53` - Working (shows paper detail)

---

## ✅ VIEW VERIFICATION

### **Existing Views** ✅ ALL WORKING

1. **dashboard.blade.php** (559 lines)
   - ✅ SPA implementation with Alpine.js
   - ✅ Stats cards
   - ✅ Recent papers table with "Chi tiết" button
   - ✅ Click → loads paper detail without reload

2. **papers/index.blade.php** (385 lines)
   - ✅ SPA implementation
   - ✅ Filters (conference, status, search)
   - ✅ Papers table with "Chi tiết" button
   - ✅ Click → loads paper detail without reload

3. **papers/show.blade.php** (267 lines)
   - ✅ Paper header
   - ✅ Authors list
   - ✅ Review statistics
   - ✅ Assignments table (currently empty)
   - ✅ Reviews list (currently empty)

**Issues Fixed:**
- ✅ No `updated_at` error (fallback to created_at)
- ✅ All schema mismatches corrected
- ✅ Array access for `$pendingActions` working

---

## ✅ FRONTEND DEPENDENCIES

### **Libraries** ✅ LOADED
- ✅ Tailwind CSS (CDN)
- ✅ Alpine.js (CDN)
- ✅ Google Fonts (Inter)

### **JavaScript** ✅ WORKING
- ✅ Alpine.js state management
- ✅ AJAX content loading
- ✅ DOMParser for HTML extraction
- ✅ No page reload navigation

---

## 🎯 READY TO IMPLEMENT

### **Phase 8.7 Requirements**

#### **Backend Tasks:**
1. ✅ Database ready (PhanCongPhanBien, COI)
2. ✅ Test data available (68 reviewers, 2 papers)
3. ⏸️ Need to create:
   - `assignReviewers($paperId)` - GET form
   - `storeAssignment(Request $request, $paperId)` - POST save
   - `removeAssignment($assignmentId)` - DELETE remove
   - `checkCOI($paperId, $reviewerId)` - Check conflicts
   - `suggestReviewers($paperId)` - Auto-suggest algorithm

#### **Frontend Tasks:**
1. ✅ Layout ready (SPA pattern established)
2. ⏸️ Need to create:
   - `papers/assign.blade.php` - Assignment form view
   - Add "Phân công reviewer" button in paper detail
   - Reviewer selection UI (cards/list)
   - COI warning display
   - Assignment confirmation

#### **Business Logic:**
1. ⏸️ COI Check Rules:
   - Same institution (co-author check)
   - Same organization
   - Recent collaboration
   - Advisor-student relationship
   
2. ⏸️ Auto-Suggest Algorithm:
   - Match expertise (keywords)
   - Check availability (workload)
   - Exclude COI
   - Rank by relevance

3. ⏸️ Assignment Rules:
   - Minimum 3 reviewers per paper (configurable)
   - Cannot assign paper author as reviewer
   - Cannot duplicate assignment (enforced by DB)
   - Generate unique token for each assignment

---

## 📋 IMPLEMENTATION CHECKLIST

### **Step 1: Controller Methods** (2 hours)
- [ ] Create `assignReviewers($paperId)`
  - Get paper info
  - Get available reviewers (exclude COI, exclude assigned)
  - Get current assignments
  - Calculate reviewer workload
  - Return view with data

- [ ] Create `storeAssignment(Request $request, $paperId)`
  - Validate input (reviewer_id, deadline)
  - Check duplicate (UNIQUE constraint)
  - Check COI
  - Generate token (UUID)
  - Insert into PhanCongPhanBien
  - Set chair_id = Auth::id()
  - Send email notification (optional Phase 8.8)
  - Return success/error

- [ ] Create `removeAssignment($assignmentId)`
  - Check authorization (chair owns this conference)
  - Check if review submitted (don't allow removal)
  - Delete assignment
  - Return success

- [ ] Create `checkCOI($paperId, $reviewerId)`
  - Check COI table
  - Check author list (same user)
  - Return COI info or null

- [ ] Create `suggestReviewers($paperId)` (optional, later)
  - Get paper keywords
  - Match with reviewer expertise
  - Exclude COI
  - Sort by relevance score
  - Return top 10 reviewers

---

### **Step 2: Routes** (15 minutes)
- [ ] Add routes to `web.php`:
```php
Route::get('/papers/{id}/assign', [ChairController::class, 'assignReviewers'])
    ->name('papers.assign');
    
Route::post('/papers/{id}/assign', [ChairController::class, 'storeAssignment'])
    ->name('papers.assign.store');
    
Route::delete('/assignments/{id}', [ChairController::class, 'removeAssignment'])
    ->name('assignments.remove');
    
Route::get('/papers/{paperId}/coi/{reviewerId}', [ChairController::class, 'checkCOI'])
    ->name('papers.coi.check');
```

---

### **Step 3: View - assign.blade.php** (2-3 hours)
- [ ] Create `resources/views/chair/papers/assign.blade.php`
  - Paper info header
  - Current assignments list (with remove button)
  - Available reviewers section:
    - Search/filter reviewers
    - Reviewer cards with:
      - Name, email, organization
      - Expertise tags (if available)
      - Current workload (X papers assigned)
      - COI badge (if conflict exists)
      - "Phân công" button
  - Assignment form:
    - Reviewer selection
    - Deadline picker
    - Submit button
  - AJAX submission (no page reload)

---

### **Step 4: Integration** (1 hour)
- [ ] Add "Phân công reviewer" button in `papers/show.blade.php`
  - Position: Near assignments table
  - Click → Load assign view in SPA
  
- [ ] Update paper detail view to show assignments:
  - Reviewer info
  - Status (INVITED, ACCEPTED, DECLINED)
  - Deadline
  - Remove button (if not reviewed)

---

### **Step 5: Testing** (1 hour)
- [ ] Test COI check (create test COI record)
- [ ] Test assignment creation
  - Valid assignment
  - Duplicate assignment (should fail)
  - Assignment with COI (should warn)
- [ ] Test assignment removal
  - Before review submitted (should work)
  - After review submitted (should fail)
- [ ] Test in SPA mode (no page reload)

---

## ⚠️ POTENTIAL ISSUES & SOLUTIONS

### **Issue 1: Reviewer Pool Management**
**Problem:** All reviewers have `conference_id = NULL` (global pool)

**Solution Options:**
- **Option A (Current):** Use global pool, allow any reviewer
- **Option B (Future):** Filter by conference when assigning role
- **Option C (Recommended):** Both - allow both conference-specific and global reviewers

**Implementation:** Start with Option A (simplest)

---

### **Issue 2: Email Notifications**
**Problem:** Mail not configured yet

**Solution:** 
- Phase 8.7: Skip email, just database record
- Phase 8.8: Add email notifications with queue

---

### **Issue 3: Expertise Matching**
**Problem:** No ReviewerExpertise table yet

**Solution:**
- Phase 8.7: Manual selection only
- Phase 9.x: Add expertise table and auto-suggest

---

### **Issue 4: Token Generation**
**Problem:** Need unique 36-char token

**Solution:**
```php
use Illuminate\Support\Str;
$token = Str::uuid()->toString(); // Generates UUID v4
```

---

### **Issue 5: COI Detection**
**Problem:** No automatic COI detection yet

**Solution:**
- Phase 8.7: Manual COI check (same author check only)
- Phase 9.x: Advanced COI detection (institution, collaboration history)

---

## 🎯 SUCCESS CRITERIA

### **Minimum Viable Product (MVP):**
- ✅ Chair can view list of available reviewers
- ✅ Chair can assign reviewer to paper
- ✅ System prevents duplicate assignments
- ✅ System prevents author self-assignment
- ✅ Chair can remove assignment (if review not submitted)
- ✅ Assignment records saved in database
- ✅ SPA navigation works (no page reload)

### **Nice to Have (Future):**
- ⏸️ Email notifications
- ⏸️ Auto-suggest reviewers
- ⏸️ Bulk assignment
- ⏸️ Drag-drop UI
- ⏸️ Advanced COI detection
- ⏸️ Reviewer expertise matching

---

## 📊 ESTIMATED TIME

| Task | Time | Status |
|------|------|--------|
| Controller methods | 2h | ⏸️ Not started |
| Routes | 15min | ⏸️ Not started |
| View creation | 2-3h | ⏸️ Not started |
| Integration | 1h | ⏸️ Not started |
| Testing | 1h | ⏸️ Not started |
| **TOTAL** | **6-7 hours** | **0% complete** |

---

## 🚀 READY TO START!

**All prerequisites met:**
- ✅ Database tables verified
- ✅ Test data available
- ✅ Controller structure ready
- ✅ Routes ready to add
- ✅ View pattern established (SPA)
- ✅ No blocking issues

**Next Command:**
```bash
# Start implementation with controller methods
```

---

**Status:** 🟢 GREEN LIGHT - Ready to implement Phase 8.7!
