# 📋 MENU FEATURES ROADMAP

## 🎯 DASHBOARD SIDEBAR MENU STATUS

### ✅ **ĐÃ HOẠT ĐỘNG (4/7 = 57%)**

#### 1. **Dashboard** ✅ HOÀN THÀNH
- **Status:** Fully functional
- **Phase:** 8.6
- **Features:**
  - Stats cards (4 cards)
  - Recent papers table
  - Conferences list
  - Pending actions
  - SPA navigation

#### 2. **Quản lý bài báo** ✅ HOÀN THÀNH
- **Status:** Fully functional
- **Phase:** 8.6
- **Features:**
  - Papers list with filters
  - Search functionality
  - Status filters
  - Conference filters
  - Pagination
  - Paper detail view
  - SPA navigation

#### 3. **Phân công phản biện** ✅ HOÀN THÀNH
- **Status:** Fully functional
- **Phase:** 8.7
- **Features:**
  - Assignment form
  - Available reviewers grid
  - COI check
  - Current assignments table
  - Remove assignment
  - SPA navigation
- **Note:** Menu leads to papers list (logical flow)

#### 4. **Reviews (Hidden but functional)** ✅ HOÀN THÀNH
- **Status:** Accessible via paper detail
- **Phase:** 8.8 (Just completed!)
- **Features:**
  - View all reviews
  - Statistics dashboard
  - Consensus indicator
  - Expandable review cards
  - Export to PDF/Excel (placeholder)
  - SPA navigation

---

### ⏸️ **CHƯA HOẠT ĐỘNG (3/7 = 43%)**

#### 5. **Quản lý reviewer** ⏸️ ĐANG PHÁT TRIỂN
- **Status:** Shows alert "Chức năng đang phát triển"
- **Phase:** 8.10 - Reviewers Management
- **Priority:** ⭐⭐⭐ Medium (Important but not critical)
- **Estimated Time:** 3-4 hours
- **Planned Features:**
  - 📋 List all reviewers for conference
  - 👤 Reviewer profile page:
    - Personal info
    - Expertise/keywords
    - Organization
    - Biography
  - 📊 Reviewer statistics:
    - Total assignments
    - Completed reviews
    - Pending reviews
    - Average response time
    - Average score given
    - Accept/reject ratio
  - 💼 Workload monitoring:
    - Current assignments count
    - Workload chart
    - Comparison with other reviewers
  - 📚 Assignment history:
    - List of all past assignments
    - Papers reviewed
    - Scores given
    - Timeline view
  - 🔍 Search & filter:
    - By name, email, organization
    - By expertise
    - By workload status
  - 📈 Performance metrics:
    - Review quality
    - Punctuality rate
    - Acceptance by chairs

**Routes to implement:**
```php
Route::get('/reviewers', [ChairController::class, 'listReviewers'])->name('reviewers.index');
Route::get('/reviewers/{id}', [ChairController::class, 'showReviewer'])->name('reviewers.show');
Route::get('/reviewers/{id}/assignments', [ChairController::class, 'reviewerAssignments'])->name('reviewers.assignments');
```

**Views to create:**
- `reviewers/index.blade.php` (List page)
- `reviewers/show.blade.php` (Profile page)

---

#### 6. **Kiểm tra COI** ⏸️ ĐANG PHÁT TRIỂN
- **Status:** Shows alert "Chức năng đang phát triển"
- **Phase:** 8.11 - COI Management (Advanced)
- **Priority:** ⭐⭐ Low (Nice to have)
- **Estimated Time:** 2-3 hours
- **Planned Features:**
  - 🔍 Bulk COI check:
    - Check all papers against all reviewers
    - Generate COI matrix
    - Highlight potential conflicts
  - 📝 COI declarations:
    - Reviewers can declare COI
    - Form to add COI reasons
    - COI types (co-author, same institution, etc.)
  - 📊 COI dashboard:
    - List all COI cases
    - Filter by type, severity
    - Unresolved COI warnings
  - 📜 COI history & audit log:
    - Track all COI checks
    - Who checked, when, result
    - Changes to COI status
  - 📄 Export COI report:
    - PDF report of all COI
    - Excel spreadsheet
    - Summary for conference
  - 🤖 Auto-detect patterns:
    - Same institution detection
    - Co-authorship detection
    - Recent collaboration detection
    - Email domain matching

**Routes to implement:**
```php
Route::get('/coi', [ChairController::class, 'coiIndex'])->name('coi.index');
Route::get('/coi/bulk-check', [ChairController::class, 'bulkCoiCheck'])->name('coi.bulk-check');
Route::post('/coi/declare', [ChairController::class, 'declareCoi'])->name('coi.declare');
Route::get('/coi/export', [ChairController::class, 'exportCoi'])->name('coi.export');
Route::get('/coi/audit-log', [ChairController::class, 'coiAuditLog'])->name('coi.audit');
```

**Views to create:**
- `coi/index.blade.php` (Dashboard)
- `coi/bulk-check.blade.php` (Bulk check page)
- `coi/audit-log.blade.php` (Audit log)

**Database tables needed:**
```sql
-- COI_Declaration table
CREATE TABLE COI_Declaration (
    coi_id INT PRIMARY KEY AUTO_INCREMENT,
    paper_id INT,
    reviewer_id INT,
    declared_by INT, -- user who declared
    coi_type ENUM('CO_AUTHOR', 'SAME_INSTITUTION', 'PERSONAL', 'OTHER'),
    reason TEXT,
    severity ENUM('LOW', 'MEDIUM', 'HIGH'),
    declared_at TIMESTAMP,
    resolved BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (paper_id) REFERENCES BaiBao(paper_id),
    FOREIGN KEY (reviewer_id) REFERENCES NguoiDung(user_id)
);

-- COI_AuditLog table
CREATE TABLE COI_AuditLog (
    log_id INT PRIMARY KEY AUTO_INCREMENT,
    paper_id INT,
    reviewer_id INT,
    checked_by INT,
    check_type VARCHAR(50),
    result VARCHAR(50),
    details TEXT,
    checked_at TIMESTAMP,
    FOREIGN KEY (checked_by) REFERENCES NguoiDung(user_id)
);
```

---

#### 7. **Trợ giúp** ⏸️ ĐANG PHÁT TRIỂN
- **Status:** Shows alert "Chức năng đang phát triển"
- **Phase:** Optional (UI Enhancement)
- **Priority:** ⭐ Very Low (Can be done last)
- **Estimated Time:** 1-2 hours
- **Planned Features:**
  - 📚 Help documentation:
    - Getting started guide
    - Step-by-step tutorials
    - Feature explanations
  - 🎥 Video tutorials:
    - How to assign reviewers
    - How to make decisions
    - How to export reports
  - ❓ FAQ section:
    - Common questions
    - Troubleshooting
    - Best practices
  - 📧 Contact support:
    - Support ticket form
    - Contact admin
    - Report bugs
  - 🔍 Search help articles
  - 💡 Tips & tricks
  - 📖 User manual download (PDF)

**Routes to implement:**
```php
Route::get('/help', [ChairController::class, 'help'])->name('help.index');
Route::get('/help/tutorials', [ChairController::class, 'tutorials'])->name('help.tutorials');
Route::get('/help/faq', [ChairController::class, 'faq'])->name('help.faq');
Route::post('/help/contact', [ChairController::class, 'contactSupport'])->name('help.contact');
```

**Views to create:**
- `help/index.blade.php`
- `help/tutorials.blade.php`
- `help/faq.blade.php`

---

## 📊 IMPLEMENTATION PRIORITY

### **MUST HAVE (Critical)** ⭐⭐⭐⭐⭐

Before these 3 menu items, we MUST complete:

#### **Phase 8.9: Final Decision Making** (Not in menu but critical)
- **Time:** 3-4 hours
- **Why critical:** Completes the paper review workflow
- **Features:**
  - Decision form (Accept/Reject/Revise)
  - Reviews summary for decision
  - Revision deadline setting
  - Comments (min 50 chars)
  - Email notification to author
  - Update paper status
- **Accessible from:** Paper detail page (button)
- **Must do FIRST** before other menu items

---

### **SHOULD HAVE (Important)** ⭐⭐⭐

#### **Phase 8.10: Reviewers Management**
- **Menu:** "Quản lý reviewer"
- **Why important:** Helps chair manage reviewer pool
- **Time:** 3-4 hours
- **Do SECOND** after Phase 8.9

---

### **COULD HAVE (Nice to have)** ⭐⭐

#### **Phase 8.11: COI Management**
- **Menu:** "Kiểm tra COI"
- **Why nice:** Improves COI handling but basic COI already working
- **Time:** 2-3 hours
- **Do THIRD** or optional

---

### **WON'T HAVE (Optional)** ⭐

#### **Help Section**
- **Menu:** "Trợ giúp"
- **Why optional:** Not critical for core functionality
- **Time:** 1-2 hours
- **Do LAST** or skip if time limited

---

## 🎯 RECOMMENDED IMPLEMENTATION ORDER

```
Priority Queue:
┌─────────────────────────────────────────────────────────┐
│ 1. Phase 8.9: Final Decision (3-4h) ⭐⭐⭐⭐⭐        │
│    → CRITICAL: Completes workflow                       │
│    → Not in menu but essential                          │
│                                                         │
│ 2. Phase 8.10: Reviewers Management (3-4h) ⭐⭐⭐      │
│    → IMPORTANT: Menu "Quản lý reviewer" works           │
│    → Useful for chair operations                        │
│                                                         │
│ 3. Phase 8.11: COI Advanced (2-3h) ⭐⭐               │
│    → NICE: Menu "Kiểm tra COI" works                    │
│    → Basic COI already functional                       │
│                                                         │
│ 4. Help Section (1-2h) ⭐                               │
│    → OPTIONAL: Menu "Trợ giúp" works                    │
│    → Can be done last or skipped                        │
└─────────────────────────────────────────────────────────┘

Total time: 9-13 hours
```

---

## 📈 CURRENT PROGRESS

### **Phase 8: Chair Features**

| Phase | Feature | Status | Progress | Time Spent |
|-------|---------|--------|----------|------------|
| 8.6 | Dashboard & Papers | ✅ Done | 100% | 8h |
| 8.7 | Assignment | ✅ Done | 100% | 4h |
| 8.8 | Reviews | ✅ Done | 100% | 1.5h |
| 8.9 | **Decision** | ⏸️ Todo | 0% | 0h |
| 8.10 | **Reviewers** | ⏸️ Todo | 0% | 0h |
| 8.11 | **COI Advanced** | ⏸️ Todo | 0% | 0h |
| - | **Help** | ⏸️ Todo | 0% | 0h |

**Overall Phase 8 Progress:** 65% (13.5h / ~25h total)

---

## 🚀 NEXT STEPS

### **Immediate (Today/Tomorrow):**
1. ✅ Test Phase 8.8 (Reviews) - just completed
2. 🎯 Start Phase 8.9 (Final Decision) - CRITICAL
3. 🧪 Test Phase 8.9

### **Short-term (This Week):**
4. Start Phase 8.10 (Reviewers Management)
5. Test Phase 8.10
6. Menu "Quản lý reviewer" will work ✅

### **Medium-term (Next Week):**
7. Start Phase 8.11 (COI Advanced) - optional
8. Menu "Kiểm tra COI" will work ✅

### **Long-term (Future):**
9. Help section - optional
10. Menu "Trợ giúp" will work ✅

---

## 💡 NOTES

### **Why Phase 8.9 (Decision) is not in menu?**
- It's accessed from paper detail page
- Button: "Đưa ra quyết định cuối cùng"
- Only shows when all reviews completed
- More logical as action on specific paper

### **Menu items vs. Workflow actions**
- **Menu items:** Broad categories (Papers, Reviewers, COI)
- **Workflow actions:** Specific tasks on items (Assign, Review, Decide)
- Both are important but serve different purposes

### **Current menu structure is good:**
```
Dashboard
  └─ Overview
Papers
  ├─ List papers
  ├─ View detail
  ├─ Assign reviewers (8.7 ✅)
  ├─ View reviews (8.8 ✅)
  └─ Make decision (8.9 ⏸️)
Reviewers (8.10 ⏸️)
  ├─ List reviewers
  ├─ View profile
  └─ Workload
COI (8.11 ⏸️)
  ├─ Bulk check
  ├─ Declarations
  └─ Audit log
Help (Optional ⏸️)
  ├─ Tutorials
  ├─ FAQ
  └─ Contact
```

---

## ✅ SUMMARY

**3 menu items "đang phát triển":**
1. **Quản lý reviewer** → Phase 8.10 (3-4h) ⭐⭐⭐
2. **Kiểm tra COI** → Phase 8.11 (2-3h) ⭐⭐
3. **Trợ giúp** → Optional (1-2h) ⭐

**Critical work before menu items:**
- **Phase 8.9: Final Decision** (3-4h) ⭐⭐⭐⭐⭐ - DO FIRST!

**Total remaining:** 9-13 hours to complete all menu features

**Recommendation:** Do Phase 8.9 first (critical), then 8.10 (important), skip 8.11 & Help if time limited.
