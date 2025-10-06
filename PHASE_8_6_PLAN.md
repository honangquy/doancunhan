# 🎯 PHASE 8.6: CHAIR FEATURES - IMPLEMENTATION PLAN

**Ngày bắt đầu:** 05/10/2025  
**Thời gian dự kiến:** 8-10 giờ  
**Mục tiêu:** Triển khai đầy đủ tính năng cho Conference Chair

---

## 📋 TỔNG QUAN

Chair là người quản lý conference, có trách nhiệm:
- Quản lý papers trong conference của mình
- Assign reviewers cho papers
- Kiểm tra Conflict of Interest (COI)
- Xem tổng hợp reviews
- Ra quyết định cuối cùng (Accept/Reject/Revise)
- Quản lý timeline và deadlines

---

## 🎯 CÁC THÀNH PHẦN CẦN TẠO

### **1. Backend - ChairController** (300-400 lines)

#### **Methods cần implement:**

```php
// Dashboard
dashboard()                          // Conference overview với statistics

// Paper Management
papers()                            // List all papers in chair's conferences
showPaper($paperId)                 // View paper details với reviews aggregated

// Reviewer Assignment
assignReviewers($paperId)           // Show reviewer assignment form
storeAssignment()                   // Save reviewer assignments
removeReviewer($assignmentId)       // Remove a reviewer from paper
checkCOI($reviewerId, $paperId)     // Check conflicts of interest

// Review Management
reviews($paperId)                   // View all reviews for a paper
reviewSummary($paperId)             // Aggregated review scores & recommendations

// Decision Making
makeDecision($paperId)              // Show decision form
storeDecision()                     // Save final decision (ACCEPTED/REJECTED/REVISION_REQUIRED)
sendNotifications($paperId)         // Notify authors of decision

// Statistics
statistics($conferenceId)           // Detailed conference statistics
```

**Total: ~10 methods**

---

### **2. Routes** (15-18 routes)

```php
// Dashboard
GET  /chair/dashboard

// Papers
GET  /chair/papers                              // List all papers
GET  /chair/papers/{id}                        // Paper details
GET  /chair/papers/{id}/reviews                // All reviews for paper
GET  /chair/papers/{id}/summary                // Review summary

// Reviewer Assignment
GET  /chair/papers/{id}/assign-reviewers       // Assignment form
POST /chair/papers/{id}/assign-reviewers       // Save assignments
DELETE /chair/assignments/{id}                  // Remove reviewer
POST /chair/check-coi                          // AJAX COI check

// Decisions
GET  /chair/papers/{id}/decision               // Decision form
POST /chair/papers/{id}/decision               // Save decision
POST /chair/papers/{id}/notify                 // Send notifications

// Statistics
GET  /chair/statistics                         // Overall statistics
GET  /chair/conferences/{id}/statistics        // Per-conference stats
```

---

### **3. Frontend Views** (6-8 views)

#### **a) Shared Layout** (`layouts/chair.blade.php`)
- Orange/Indigo gradient navbar
- Sidebar with conference selector
- Statistics overview
- User menu
- Similar structure to reviewer layout

#### **b) Dashboard** (`chair/dashboard.blade.php`)
- Conference selector dropdown
- Statistics cards:
  - Total submissions
  - Papers under review
  - Completed reviews
  - Pending decisions
  - Average review time
- Recent papers list
- Pending actions list
- Timeline visualization

#### **c) Papers List** (`chair/papers/index.blade.php`)
- Filter by:
  - Conference
  - Status (Submitted, Under Review, Reviewed, Decided)
  - Track (if applicable)
- Search by title/author
- Sortable columns
- Papers table:
  - Paper ID, Title, Authors
  - Submission date
  - # Reviews (completed/total)
  - Average score
  - Status
  - Actions (View, Assign, Decide)
- Bulk actions
- Export to CSV

#### **d) Paper Details** (`chair/papers/show.blade.php`)
- Paper information section
- Authors list with emails
- Abstract, keywords
- Download PDF button
- Review assignments section:
  - Table of assigned reviewers
  - Status (Invited, Accepted, Completed)
  - Add reviewer button
  - Remove reviewer button
- Reviews display:
  - All submitted reviews
  - Scores visualization (chart/graph)
  - Recommendations summary
  - Comments
- Decision section:
  - Current decision status
  - Make/Change decision button
- Activity timeline

#### **e) Assign Reviewers Form** (`chair/papers/assign.blade.php`)
- Paper info sidebar
- Reviewer search:
  - Search by name, email, expertise
  - Filter by availability
  - COI indicator
- Available reviewers list:
  - Name, email, organization
  - Current workload (# papers assigned)
  - Expertise match score
  - COI warning
  - Assign button
- Currently assigned reviewers:
  - Name, status
  - Due date
  - Remove button
- Conflict of Interest (COI) section:
  - Same organization warning
  - Co-author check
  - Previous collaboration check
- Bulk assign option

#### **f) Review Summary** (`chair/papers/reviews.blade.php`)
- Paper header
- Overall statistics:
  - Average score
  - Recommendation distribution (pie chart)
  - Reviewer agreement level
- Individual reviews grid:
  - Reviewer name (anonymized option)
  - Overall score (with visual bar)
  - Recommendation badge
  - Strengths summary
  - Weaknesses summary
  - Expand for full comments
- Download all reviews (PDF)
- Side-by-side comparison view

#### **g) Decision Form** (`chair/papers/decision.blade.php`)
- Paper summary
- Reviews overview
- Decision options:
  - ✅ Accept
  - ❌ Reject
  - 🔄 Revision Required (Major/Minor)
  - ⏸️ Hold
- Comments to authors (rich text editor)
- Internal notes (not visible to authors)
- Deadline for revisions (if applicable)
- Notify authors checkbox
- Save decision button

#### **h) Statistics Dashboard** (`chair/statistics.blade.php`)
- Conference selector
- Overall metrics:
  - Total submissions
  - Acceptance rate
  - Average review score
  - Review completion rate
  - Average time to decision
- Charts:
  - Submissions over time (line chart)
  - Status distribution (pie chart)
  - Score distribution (histogram)
  - Reviewer workload (bar chart)
- Tables:
  - Top reviewers (by # reviews)
  - Papers by track
  - Papers by country/organization
- Export report button

---

## 🎨 DESIGN SYSTEM

### **Color Theme:**
```css
Primary: Orange (#ea580c - orange-600)
Gradient: from-orange-800 via-orange-700 to-orange-600
Accent: Indigo (#4f46e5)
Success: Green (#059669)
Warning: Amber (#d97706)
Danger: Red (#dc2626)
```

### **Sidebar Menu Items:**
```
📊 Dashboard
📄 Papers
  ├─ All Papers
  ├─ Pending Review
  ├─ Completed Reviews
  └─ Decisions Made
👥 Reviewers
  ├─ All Reviewers
  ├─ Workload
  └─ Assign Reviewers
📊 Statistics
⚙️ Conference Settings
❓ Help
```

---

## 🗄️ DATABASE QUERIES

### **Key Queries:**

#### **1. Chair's Conferences:**
```sql
SELECT * FROM HoiThao ht
JOIN VaiTroNguoiDung vt ON ht.chair_id = vt.user_id
WHERE vt.user_id = :userId AND vt.role_code = 'CHAIR'
```

#### **2. Papers in Conference:**
```sql
SELECT 
    bb.*,
    nd.full_name as author_name,
    COUNT(DISTINCT pc.assignment_id) as total_reviews,
    COUNT(DISTINCT pb.review_id) as completed_reviews,
    AVG(pb.overall_score) as avg_score
FROM BaiBao bb
JOIN NguoiDung nd ON bb.submitter_id = nd.user_id
LEFT JOIN PhanCongPhanBien pc ON bb.paper_id = pc.paper_id
LEFT JOIN PhanBien pb ON pc.assignment_id = pb.assignment_id
WHERE bb.conference_id = :conferenceId
GROUP BY bb.paper_id
ORDER BY bb.submission_date DESC
```

#### **3. Available Reviewers (with COI check):**
```sql
SELECT 
    nd.*,
    COUNT(pc.assignment_id) as current_workload,
    GROUP_CONCAT(DISTINCT cd.name) as expertise
FROM NguoiDung nd
JOIN VaiTroNguoiDung vt ON nd.user_id = vt.user_id
LEFT JOIN PhanCongPhanBien pc ON nd.user_id = pc.reviewer_id 
    AND pc.response_status IN ('ACCEPTED', 'INVITED')
LEFT JOIN ChuyenDe cd ON nd.expertise_area = cd.topic_id
WHERE vt.role_code = 'REVIEWER'
    AND nd.user_id NOT IN (
        -- Exclude co-authors
        SELECT tg.user_id FROM TacGiaBaiBao tg WHERE tg.paper_id = :paperId
    )
    AND nd.user_id NOT IN (
        -- Exclude same organization as authors
        SELECT nd2.user_id FROM NguoiDung nd2
        JOIN TacGiaBaiBao tg ON nd2.email = tg.email
        WHERE tg.paper_id = :paperId
    )
GROUP BY nd.user_id
ORDER BY current_workload ASC, expertise DESC
```

#### **4. Review Summary:**
```sql
SELECT 
    pb.*,
    nd.full_name as reviewer_name,
    pc.response_status
FROM PhanBien pb
JOIN PhanCongPhanBien pc ON pb.assignment_id = pc.assignment_id
JOIN NguoiDung nd ON pc.reviewer_id = nd.user_id
WHERE pc.paper_id = :paperId
    AND pb.review_date IS NOT NULL
ORDER BY pb.review_date DESC
```

---

## 🔐 AUTHORIZATION

### **ChairController Middleware:**
```php
public function __construct()
{
    $this->middleware('auth');
    $this->middleware('role:CHAIR');
}
```

### **Per-Method Authorization:**
```php
// Check if user is chair of the conference
$paper = DB::table('BaiBao')->where('paper_id', $paperId)->first();
$conference = DB::table('HoiThao')->where('conference_id', $paper->conference_id)->first();

if ($conference->chair_id !== Auth::id()) {
    abort(403, 'Unauthorized access to this conference');
}
```

---

## ✅ IMPLEMENTATION CHECKLIST

### **Phase 1: Backend Setup** (2-3 hours)
- [ ] Create `ChairController.php`
- [ ] Implement `dashboard()` method
- [ ] Implement `papers()` method
- [ ] Implement `showPaper()` method
- [ ] Add routes for dashboard and papers
- [ ] Test with chair@test.com account

### **Phase 2: Reviewer Assignment** (2-3 hours)
- [ ] Implement `assignReviewers()` method
- [ ] Implement `storeAssignment()` method
- [ ] Implement `checkCOI()` AJAX method
- [ ] Create assignment form view
- [ ] Test COI detection logic
- [ ] Test assignment creation

### **Phase 3: Review Management** (2 hours)
- [ ] Implement `reviews()` method
- [ ] Implement `reviewSummary()` method
- [ ] Create review summary view
- [ ] Add score visualization (charts)
- [ ] Test review aggregation

### **Phase 4: Decision Making** (2 hours)
- [ ] Implement `makeDecision()` method
- [ ] Implement `storeDecision()` method
- [ ] Create decision form view
- [ ] Add decision workflow
- [ ] Test decision saving

### **Phase 5: Frontend Views** (3-4 hours)
- [ ] Create shared chair layout
- [ ] Create dashboard view
- [ ] Create papers list view
- [ ] Create paper details view
- [ ] Polish UI/UX
- [ ] Test all navigation

### **Phase 6: Statistics & Polish** (1-2 hours)
- [ ] Implement `statistics()` method
- [ ] Create statistics dashboard
- [ ] Add charts and visualizations
- [ ] Bug fixes
- [ ] Documentation

---

## 🎯 SUCCESS CRITERIA

✅ **Chair can:**
- Log in and see their conferences
- View all papers in their conference
- Assign multiple reviewers to a paper
- See COI warnings when assigning
- View aggregated review scores
- Make final decisions on papers
- See conference statistics

✅ **System:**
- Properly checks COI
- Prevents unauthorized access
- Shows real-time statistics
- Handles errors gracefully

---

## 🚀 GETTING STARTED

**First Steps:**
1. Create ChairController
2. Add basic routes
3. Test with chair@test.com
4. Build incrementally

**Test Account:**
```
Email: chair@test.com
Password: password123
Conference: HUIT-ICI-2025 (hoặc check DB)
```

---

**Ready to start? Let's build! 💪**
