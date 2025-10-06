# 🎯 PHASE 8: REMAINING TASKS - DETAILED BREAKDOWN

## 📊 CURRENT STATUS

**✅ Completed (55%):**
- Phase 8.6: Dashboard & Paper Management
- Phase 8.7: Reviewer Assignment

**⏸️ Remaining (45%):**
- Phase 8.8: Reviews Management (View & Monitor)
- Phase 8.9: Final Decision Making
- Phase 8.10: Reviewers Management
- Phase 8.11: COI Management (Advanced)

---

## 🎯 PHASE 8.8: REVIEWS MANAGEMENT

**Goal:** Chair can view all reviews for papers, monitor review progress, and export reviews

**Priority:** ⭐⭐⭐⭐⭐ (Critical)  
**Estimated Time:** 4-5 hours  
**Dependencies:** Phase 8.7 (Assignment) completed ✅

### **8.8.1: Backend - Reviews Controller Methods** (1.5 hours)

#### **Method 1: `reviews()` - GET /papers/{id}/reviews**
```php
public function reviews($paperId)
{
    // Get paper with all reviews
    // Join: BaiBao, PhanCongPhanBien, PhieuNhanXet, NguoiDung
    // Calculate statistics
    // Return view with reviews data
}
```

**Features:**
- ✅ Get all reviews for paper (completed + pending)
- ✅ Calculate review statistics (avg score, distribution)
- ✅ Filter by status (completed, pending, overdue)
- ✅ Sort by date, score, reviewer name
- ✅ Authorization check (chair of conference)

**Database Queries:**
```sql
-- Main query
SELECT pn.*, nd.name, pc.assigned_date, pc.deadline
FROM PhieuNhanXet pn
JOIN PhanCongPhanBien pc ON pn.assignment_id = pc.assignment_id
JOIN NguoiDung nd ON pc.reviewer_id = nd.user_id
WHERE pc.paper_id = ?

-- Statistics
SELECT 
    COUNT(*) as total,
    AVG(overall_score) as avg_score,
    SUM(CASE WHEN recommendation = 'ACCEPT' THEN 1 ELSE 0 END) as accept_count,
    SUM(CASE WHEN recommendation = 'REJECT' THEN 1 ELSE 0 END) as reject_count
FROM PhieuNhanXet pn
JOIN PhanCongPhanBien pc ON pn.assignment_id = pc.assignment_id
WHERE pc.paper_id = ?
```

#### **Method 2: `exportReviews()` - GET /papers/{id}/reviews/export**
```php
public function exportReviews($paperId, Request $request)
{
    // Get all reviews for paper
    // Format: PDF or Excel based on request
    // Include: Paper info, all reviews, comments, scores
    // Return downloadable file
}
```

**Features:**
- ✅ Export to PDF (using DomPDF)
- ✅ Export to Excel (using Maatwebsite/Excel)
- ✅ Include all review details
- ✅ Professional formatting
- ✅ Watermark with conference name

---

### **8.8.2: Frontend - Reviews View** (2.5 hours)

#### **File: `resources/views/chair/papers/reviews.blade.php`**

**Layout:**
```
┌─────────────────────────────────────────────────────────┐
│ [← Quay lại chi tiết bài báo]                          │
│ Tất cả nhận xét - Bài báo #53                          │
├─────────────────────────────────────────────────────────┤
│ PAPER HEADER                                            │
│ ┌───────────────────────────────────────────────────┐   │
│ │ #53 | Nhân Văm ... | HUIT ICT | Đã nộp          │   │
│ └───────────────────────────────────────────────────┘   │
├─────────────────────────────────────────────────────────┤
│ REVIEW STATISTICS (5 cards)                             │
│ ┌──────┐ ┌──────┐ ┌──────┐ ┌──────┐ ┌──────┐          │
│ │Total │ │Compl.│ │Pend. │ │Avg   │ │Accept│          │
│ │  3   │ │  2   │ │  1   │ │ 7.5  │ │  2   │          │
│ └──────┘ └──────┘ └──────┘ └──────┘ └──────┘          │
├─────────────────────────────────────────────────────────┤
│ FILTERS & ACTIONS                                       │
│ [Status ▼] [Sort ▼] [Search...] [Export PDF] [Excel]  │
├─────────────────────────────────────────────────────────┤
│ REVIEWS LIST (Accordion)                                │
│                                                         │
│ ┌───────────────────────────────────────────────────┐   │
│ │ ▼ Nhận xét #1 - Dr. Nguyen Van A                 │   │
│ │   Submitted: 10/01/2025 | Score: 8.5 | [ACCEPT] │   │
│ │ ├─────────────────────────────────────────────────┤   │
│ │ │ SCORES:                                         │   │
│ │ │ • Originality: 9/10                             │   │
│ │ │ • Quality: 8/10                                 │   │
│ │ │ • Clarity: 8/10                                 │   │
│ │ │ • Relevance: 9/10                               │   │
│ │ ├─────────────────────────────────────────────────┤   │
│ │ │ COMMENTS:                                       │   │
│ │ │ This paper presents...                          │   │
│ │ ├─────────────────────────────────────────────────┤   │
│ │ │ STRENGTHS: Well-written, clear methodology...  │   │
│ │ │ WEAKNESSES: Limited dataset, need more...      │   │
│ │ │ SUGGESTIONS: Add more experiments...           │   │
│ │ └─────────────────────────────────────────────────┘   │
│ └───────────────────────────────────────────────────┘   │
│                                                         │
│ ┌───────────────────────────────────────────────────┐   │
│ │ ▶ Nhận xét #2 - Dr. Tran Thi B (Collapsed)      │   │
│ │   Submitted: 09/01/2025 | Score: 6.5 | [ACCEPT] │   │
│ └───────────────────────────────────────────────────┘   │
│                                                         │
│ ┌───────────────────────────────────────────────────┐   │
│ │ ⏳ Nhận xét #3 - Dr. Le Van C (Pending)          │   │
│ │   Deadline: 15/01/2025 | Not submitted yet       │   │
│ └───────────────────────────────────────────────────┘   │
└─────────────────────────────────────────────────────────┘
```

**Components:**

1. **Paper Header Card** (reusable)
   - Paper ID, Title, Conference
   - Status badge
   - Compact design

2. **Statistics Cards** (5 cards)
   - Total reviews
   - Completed count
   - Pending count
   - Average score (color-coded)
   - Accept/Reject ratio

3. **Filters & Actions Bar**
   - Status dropdown (All, Completed, Pending, Overdue)
   - Sort dropdown (Date, Score, Reviewer)
   - Search box (reviewer name, keywords)
   - Export buttons (PDF, Excel)

4. **Reviews Accordion List**
   - Each review card expandable/collapsible
   - Header shows: Reviewer, date, score, recommendation
   - Body shows:
     - Individual scores table (4 criteria)
     - Comments section
     - Strengths/Weaknesses/Suggestions
   - Pending reviews show status only
   - Color-coding by status (green=complete, yellow=pending, red=overdue)

**Interactive Features:**
- ✅ Click to expand/collapse review
- ✅ Filter by status (instant update)
- ✅ Sort reviews (instant update)
- ✅ Search in reviews (highlight matches)
- ✅ Export to PDF (download)
- ✅ Export to Excel (download)
- ✅ Back button (SPA navigation)

**Alpine.js State:**
```javascript
{
    filterStatus: 'all',
    sortBy: 'date',
    searchQuery: '',
    expandedReviews: [],
    toggleReview(reviewId) { ... },
    filteredReviews() { ... }
}
```

---

### **8.8.3: SPA Integration** (30 minutes)

#### **Update dashboard.blade.php**
```javascript
// Add new state
reviewsData: null,

// Add new method
async viewReviews(paperId) {
    this.selectedPaperId = paperId;
    this.currentView = 'reviews';
    this.loading = true;
    this.reviewsData = null;
    
    const response = await fetch(`/qly_hthao/qlyhoithao/public/chair/papers/${paperId}/reviews`);
    const html = await response.text();
    const parser = new DOMParser();
    const doc = parser.parseFromString(html, 'text/html');
    const content = doc.querySelector('.main-content');
    
    this.reviewsData = content ? content.innerHTML : html;
    this.loading = false;
}

// Add view section
<div x-show="currentView === 'reviews'" class="p-6">
    <div x-html="reviewsData"></div>
</div>
```

#### **Update papers/show.blade.php**
Add button to view all reviews:
```html
<button onclick="if(window.Alpine && Alpine.$data(document.body).viewReviews) { 
    Alpine.$data(document.body).viewReviews({{ $paper->paper_id }}); 
} else { 
    window.location.href = '{{ route('chair.papers.reviews', $paper->paper_id) }}'; 
}" class="px-4 py-2 bg-blue-600...">
    📋 Xem tất cả nhận xét
</button>
```

---

### **8.8.4: Routes** (5 minutes)

```php
// Add to routes/web.php
Route::get('/papers/{id}/reviews', [ChairController::class, 'reviews'])->name('papers.reviews');
Route::get('/papers/{id}/reviews/export', [ChairController::class, 'exportReviews'])->name('papers.reviews.export');
```

---

### **8.8.5: Testing Checklist** (30 minutes)

- [ ] Load reviews page for paper with reviews
- [ ] Statistics cards show correct numbers
- [ ] All reviews displayed correctly
- [ ] Expand/collapse reviews works
- [ ] Filter by status works (all, completed, pending)
- [ ] Sort by date/score/reviewer works
- [ ] Search functionality works
- [ ] Export to PDF works
- [ ] Export to Excel works
- [ ] Back button navigates to paper detail
- [ ] SPA navigation (no page reload)
- [ ] Load page for paper with no reviews (empty state)
- [ ] Authorization check works (only chair can view)

---

## 🎯 PHASE 8.9: FINAL DECISION MAKING

**Goal:** Chair can make final decisions on papers (accept/reject/revise) after reviews

**Priority:** ⭐⭐⭐⭐⭐ (Critical)  
**Estimated Time:** 3-4 hours  
**Dependencies:** Phase 8.8 (Reviews) completed

### **8.9.1: Backend - Decision Controller Methods** (1.5 hours)

#### **Method 1: `makeDecision()` - GET /papers/{id}/decision**
```php
public function makeDecision($paperId)
{
    // Get paper with all reviews
    // Calculate recommendation summary
    // Check if all reviews completed
    // Get current decision if exists
    // Return decision form view
}
```

**Features:**
- ✅ Show paper info
- ✅ Show all reviews summary
- ✅ Show reviewer recommendations
- ✅ Calculate average score
- ✅ Show previous decision if exists
- ✅ Validation: all reviews must be completed
- ✅ Authorization check

**Database Queries:**
```sql
-- Get paper with reviews summary
SELECT bb.*, 
    COUNT(pc.assignment_id) as total_reviews,
    AVG(pn.overall_score) as avg_score,
    SUM(CASE WHEN pn.recommendation = 'ACCEPT' THEN 1 ELSE 0 END) as accept_count,
    SUM(CASE WHEN pn.recommendation = 'REJECT' THEN 1 ELSE 0 END) as reject_count
FROM BaiBao bb
JOIN PhanCongPhanBien pc ON bb.paper_id = pc.paper_id
LEFT JOIN PhieuNhanXet pn ON pc.assignment_id = pn.assignment_id
WHERE bb.paper_id = ?
GROUP BY bb.paper_id

-- Check if all reviews completed
SELECT COUNT(*) as pending
FROM PhanCongPhanBien pc
LEFT JOIN PhieuNhanXet pn ON pc.assignment_id = pn.assignment_id
WHERE pc.paper_id = ? AND pn.review_id IS NULL
```

#### **Method 2: `storeDecision()` - POST /papers/{id}/decision**
```php
public function storeDecision(Request $request, $paperId)
{
    // Validate input
    $validated = $request->validate([
        'decision' => 'required|in:ACCEPT,REJECT,REVISE',
        'comments' => 'required|min:50',
        'deadline_revision' => 'required_if:decision,REVISE|date|after:today'
    ]);
    
    // Update paper status
    // Create decision record
    // Send notification to author
    // Log action
    // Return success
}
```

**Validation Rules:**
- Decision: required, must be ACCEPT/REJECT/REVISE
- Comments: required, min 50 characters
- Revision deadline: required if REVISE, must be future date

**Business Logic:**
- Update `BaiBao.status_id`:
  - ACCEPT → status "Đã chấp nhận"
  - REJECT → status "Đã từ chối"
  - REVISE → status "Cần sửa lại"
- Create record in `QuyetDinhBaiBao` table (if exists) or update status
- Send email notification to author
- Update `decision_date` timestamp

---

### **8.9.2: Frontend - Decision View** (1.5 hours)

#### **File: `resources/views/chair/papers/decision.blade.php`**

**Layout:**
```
┌─────────────────────────────────────────────────────────┐
│ [← Quay lại chi tiết bài báo]                          │
│ Quyết định cuối cùng - Bài báo #53                     │
├─────────────────────────────────────────────────────────┤
│ PAPER SUMMARY                                           │
│ ┌───────────────────────────────────────────────────┐   │
│ │ #53 | Nhân Văm Research...                        │   │
│ │ HUIT International Conference on ICT 2025        │   │
│ │ Status: Under Review                              │   │
│ └───────────────────────────────────────────────────┘   │
├─────────────────────────────────────────────────────────┤
│ REVIEWS SUMMARY                                         │
│ ┌──────────────────────────────────────────────────┐    │
│ │ 📊 Review Statistics                             │    │
│ │ • Total Reviews: 3 completed                     │    │
│ │ • Average Score: 7.5/10                          │    │
│ │ • Recommendations: 2 Accept, 1 Revise           │    │
│ │ • Reviewer Consensus: ⚠️ Mixed opinions          │    │
│ └──────────────────────────────────────────────────┘    │
│                                                         │
│ ┌──────────────────────────────────────────────────┐    │
│ │ 📝 Individual Reviews                            │    │
│ │ • Dr. Nguyen A: 8.5/10 - ACCEPT                 │    │
│ │ • Dr. Tran B: 6.5/10 - REVISE                   │    │
│ │ • Dr. Le C: 7.5/10 - ACCEPT                     │    │
│ └──────────────────────────────────────────────────┘    │
├─────────────────────────────────────────────────────────┤
│ DECISION FORM                                           │
│ ┌───────────────────────────────────────────────────┐   │
│ │ Quyết định của chủ tịch *                        │   │
│ │ ○ Chấp nhận (Accept)                             │   │
│ │ ● Yêu cầu sửa lại (Revise) - SELECTED            │   │
│ │ ○ Từ chối (Reject)                               │   │
│ └───────────────────────────────────────────────────┘   │
│                                                         │
│ ┌───────────────────────────────────────────────────┐   │
│ │ [ONLY IF REVISE SELECTED]                        │   │
│ │ Deadline for revision: [Date Picker]             │   │
│ └───────────────────────────────────────────────────┘   │
│                                                         │
│ ┌───────────────────────────────────────────────────┐   │
│ │ Nhận xét của chủ tịch * (min 50 characters)     │   │
│ │ ┌─────────────────────────────────────────────┐  │   │
│ │ │ [Large text area - 8 rows]                  │  │   │
│ │ │                                              │  │   │
│ │ │ Based on the reviews, the paper shows...    │  │   │
│ │ └─────────────────────────────────────────────┘  │   │
│ │ Character count: 125/50 ✅                        │   │
│ └───────────────────────────────────────────────────┘   │
│                                                         │
│ ┌───────────────────────────────────────────────────┐   │
│ │ ⚠️ Important Notes:                               │   │
│ │ • Author will receive email notification         │   │
│ │ • Decision cannot be changed after submission    │   │
│ │ • Comments will be visible to author             │   │
│ └───────────────────────────────────────────────────┘   │
│                                                         │
│ [Hủy]  [💾 Lưu quyết định]                             │
└─────────────────────────────────────────────────────────┘
```

**Components:**

1. **Paper Summary Card**
   - Paper ID, title, conference
   - Current status
   - Author name

2. **Reviews Summary Section**
   - Statistics card (total, avg score, recommendations)
   - Individual reviews list (name, score, recommendation)
   - Consensus indicator (Strong Accept, Mixed, Strong Reject)

3. **Decision Form**
   - Radio buttons (Accept, Revise, Reject)
   - Conditional field: Revision deadline (if Revise selected)
   - Large text area for comments
   - Character counter
   - Warning box about consequences

4. **Action Buttons**
   - Cancel button (go back)
   - Submit button (with confirmation dialog)

**Interactive Features:**
- ✅ Radio button selection triggers UI updates
- ✅ Show/hide revision deadline based on selection
- ✅ Character counter for comments
- ✅ Validation before submit
- ✅ Confirmation dialog: "Are you sure? This decision is final."
- ✅ Success message after submit
- ✅ Auto-navigate to paper detail after submit

**Alpine.js/JavaScript:**
```javascript
{
    decision: '',
    comments: '',
    deadlineRevision: '',
    
    validateForm() {
        if (!this.decision) {
            alert('Please select a decision');
            return false;
        }
        if (this.comments.length < 50) {
            alert('Comments must be at least 50 characters');
            return false;
        }
        if (this.decision === 'REVISE' && !this.deadlineRevision) {
            alert('Please specify revision deadline');
            return false;
        }
        return true;
    },
    
    async submitDecision() {
        if (!this.validateForm()) return;
        
        if (!confirm('Are you sure? This decision is final and cannot be changed.')) {
            return;
        }
        
        const formData = new FormData();
        formData.append('decision', this.decision);
        formData.append('comments', this.comments);
        formData.append('deadline_revision', this.deadlineRevision);
        
        const response = await fetch('/qly_hthao/qlyhoithao/public/chair/papers/{{ $paper->paper_id }}/decision', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: formData
        });
        
        if (response.ok) {
            alert('Decision saved successfully!');
            // Navigate back or reload
        }
    }
}
```

---

### **8.9.3: Database Migration** (30 minutes)

Create table for decisions (if not exists):

```php
// database/migrations/xxxx_create_quyet_dinh_bai_bao_table.php
Schema::create('QuyetDinhBaiBao', function (Blueprint $table) {
    $table->id('decision_id');
    $table->unsignedBigInteger('paper_id');
    $table->unsignedBigInteger('chair_id');
    $table->enum('decision', ['ACCEPT', 'REJECT', 'REVISE']);
    $table->text('comments');
    $table->date('deadline_revision')->nullable();
    $table->timestamp('decision_date')->useCurrent();
    
    $table->foreign('paper_id')->references('paper_id')->on('BaiBao')->onDelete('cascade');
    $table->foreign('chair_id')->references('user_id')->on('NguoiDung')->onDelete('cascade');
});
```

---

### **8.9.4: SPA Integration** (30 minutes)

Similar to assignment and reviews integration.

---

### **8.9.5: Testing Checklist** (30 minutes)

- [ ] Load decision form for paper with all reviews completed
- [ ] Error if trying to decide on paper with pending reviews
- [ ] Reviews summary displays correctly
- [ ] Radio buttons work
- [ ] Revision deadline field shows/hides based on selection
- [ ] Character counter works
- [ ] Validation works (empty decision, short comments, missing deadline)
- [ ] Confirmation dialog appears
- [ ] Submit works and saves to database
- [ ] Email notification sent to author
- [ ] Paper status updated correctly
- [ ] Back button works
- [ ] SPA navigation (no page reload)

---

## 🎯 PHASE 8.10: REVIEWERS MANAGEMENT

**Goal:** Chair can view all reviewers, their profiles, workload, and expertise

**Priority:** ⭐⭐⭐ (Medium)  
**Estimated Time:** 3-4 hours  
**Dependencies:** None

### **8.10.1: Backend** (1.5 hours)

#### **Method: `listReviewers()` - GET /reviewers**

Features:
- List all reviewers for chair's conferences
- Show expertise, workload, stats
- Search and filter
- Sort by various criteria

#### **Method: `showReviewer()` - GET /reviewers/{id}**

Features:
- Reviewer profile
- Assignment history
- Review statistics
- Expertise tags

---

### **8.10.2: Frontend** (2 hours)

Reviewers list page with cards showing:
- Photo, name, email
- Expertise tags
- Current assignments count
- Completed reviews count
- Average review score given
- Response rate

---

## 🎯 PHASE 8.11: COI MANAGEMENT (ADVANCED)

**Goal:** Advanced COI management, bulk COI checks, COI declarations

**Priority:** ⭐⭐ (Low)  
**Estimated Time:** 2-3 hours  
**Dependencies:** Phase 8.7

Features:
- Bulk COI check for all papers
- COI declaration by reviewers
- COI history and audit log
- Export COI report

---

## 📊 SUMMARY

### **Time Estimates**

| Phase | Task | Time | Priority |
|-------|------|------|----------|
| 8.8 | Reviews Management | 4-5 hours | ⭐⭐⭐⭐⭐ Critical |
| 8.9 | Final Decision | 3-4 hours | ⭐⭐⭐⭐⭐ Critical |
| 8.10 | Reviewers Management | 3-4 hours | ⭐⭐⭐ Medium |
| 8.11 | COI Management (Adv) | 2-3 hours | ⭐⭐ Low |
| **TOTAL** | **Remaining Work** | **12-16 hours** | |

### **Recommended Order**

1. **Phase 8.8** (Reviews) - Critical for workflow
2. **Phase 8.9** (Decision) - Critical for workflow
3. **Phase 8.10** (Reviewers) - Nice to have
4. **Phase 8.11** (COI) - Optional enhancement

### **Core vs. Optional**

**Core (Must Have):**
- ✅ Phase 8.6: Dashboard ✅ Done
- ✅ Phase 8.7: Assignment ✅ Done
- ⏸️ Phase 8.8: Reviews
- ⏸️ Phase 8.9: Decision

**Optional (Nice to Have):**
- ⏸️ Phase 8.10: Reviewers Management
- ⏸️ Phase 8.11: Advanced COI

---

## 🚀 QUICK START NEXT PHASE

To start **Phase 8.8 (Reviews Management)**:

1. Create controller method `reviews()`
2. Create view `papers/reviews.blade.php`
3. Add routes
4. Update SPA navigation
5. Test

**Est. Time:** 4-5 hours  
**Ready to start?** 🎯
