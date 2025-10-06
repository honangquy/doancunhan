# 📋 PHASE 8.5: REVIEWER FEATURES - KẾ HOẠCH CHI TIẾT

**Ngày bắt đầu:** 05/10/2025  
**Ước tính:** 8-10 giờ  
**Mục tiêu:** Xây dựng đầy đủ tính năng cho Reviewer

---

## 🎯 MỤC TIÊU

Cho phép reviewer:
1. Xem các paper được phân công
2. Chấp nhận/từ chối phân công
3. Đấu thầu (bidding) cho các paper
4. Viết và submit reviews
5. Xem lịch sử reviews

---

## 📊 CẤU TRÚC TASKS

### **Task 1: Reviewer Dashboard Enhancement** (30 phút)
- [ ] Cập nhật statistics queries
- [ ] Hiển thị pending assignments
- [ ] Hiển thị approaching deadlines
- [ ] Quick actions (Accept/Review/View)

### **Task 2: ReviewerController** (2 giờ)
File: `app/Http/Controllers/Reviewer/ReviewerController.php`

**Methods cần tạo:**
```php
// Assignments
assignments()        // Danh sách phân công
acceptAssignment()   // Chấp nhận phân công
declineAssignment()  // Từ chối phân công

// Bidding
bidding()           // Danh sách papers để bid
submitBid()         // Submit bid (INTERESTED/NOT_INTERESTED/CONFLICT)

// Reviews
reviews()           // Danh sách reviews
createReview()      // Form viết review
storeReview()       // Lưu review
showReview()        // Xem review đã submit
editReview()        // Form sửa review (nếu chưa submit)
updateReview()      // Update review
```

### **Task 3: Routes** (15 phút)
File: `routes/web.php`

```php
Route::prefix('reviewer')->middleware(['auth', 'role:REVIEWER'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'reviewerDashboard'])->name('reviewer.dashboard');
    
    // Assignments
    Route::get('/assignments', [ReviewerController::class, 'assignments'])->name('reviewer.assignments');
    Route::post('/assignments/{id}/accept', [ReviewerController::class, 'acceptAssignment'])->name('reviewer.assignments.accept');
    Route::post('/assignments/{id}/decline', [ReviewerController::class, 'declineAssignment'])->name('reviewer.assignments.decline');
    
    // Bidding
    Route::get('/bidding', [ReviewerController::class, 'bidding'])->name('reviewer.bidding');
    Route::post('/bidding/{paperId}', [ReviewerController::class, 'submitBid'])->name('reviewer.bidding.submit');
    
    // Reviews
    Route::get('/reviews', [ReviewerController::class, 'reviews'])->name('reviewer.reviews');
    Route::get('/reviews/create/{assignmentId}', [ReviewerController::class, 'createReview'])->name('reviewer.reviews.create');
    Route::post('/reviews', [ReviewerController::class, 'storeReview'])->name('reviewer.reviews.store');
    Route::get('/reviews/{id}', [ReviewerController::class, 'showReview'])->name('reviewer.reviews.show');
    Route::get('/reviews/{id}/edit', [ReviewerController::class, 'editReview'])->name('reviewer.reviews.edit');
    Route::put('/reviews/{id}', [ReviewerController::class, 'updateReview'])->name('reviewer.reviews.update');
});
```

### **Task 4: Frontend Views** (4-5 giờ)

#### **4.1: Assignments View** (1 giờ)
File: `resources/views/reviewer/assignments.blade.php`

**Sections:**
- Statistics cards (Total, Pending, Accepted, Completed)
- Assignments table:
  - Paper title & conference
  - Status (PENDING/ACCEPTED/DECLINED/COMPLETED)
  - Assigned date & deadline
  - Actions (Accept/Decline/Review/View)
- Filter by status
- Pagination

#### **4.2: Bidding View** (1 giờ)
File: `resources/views/reviewer/bidding.blade.php`

**Sections:**
- Instructions
- Available papers list:
  - Paper title, abstract (truncated)
  - Authors (hidden names, show count only)
  - Keywords
  - Conference
  - Bid status (Not yet / INTERESTED / NOT_INTERESTED / CONFLICT)
  - Action buttons
- COI declaration checkbox
- Submit bid buttons

#### **4.3: Review Form** (1.5 giờ)
File: `resources/views/reviewer/reviews/create.blade.php`

**Sections:**
- Paper information (title, abstract, download link)
- Review criteria với scoring (1-10):
  - Originality
  - Technical Quality
  - Clarity
  - Relevance
- Overall recommendation:
  - Accept (ACCEPT)
  - Accept with Minor Revision (MINOR_REVISION)
  - Major Revision Required (MAJOR_REVISION)
  - Reject (REJECT)
- Detailed comments for authors
- Confidential comments for chair
- File attachment (optional)
- Submit & Save as Draft buttons

#### **4.4: Reviews List** (45 phút)
File: `resources/views/reviewer/reviews/index.blade.php`

**Sections:**
- Statistics (Total reviews, Average score)
- Reviews table:
  - Paper title
  - Conference
  - Score
  - Recommendation
  - Submit date
  - Status
  - Actions (View/Edit)

#### **4.5: Review Detail** (30 phút)
File: `resources/views/reviewer/reviews/show.blade.php`

**Sections:**
- Paper info
- All review scores
- Recommendation
- Comments
- Chair comments (if any)
- Submitted date
- Actions (Edit if not submitted, Back)

### **Task 5: Database Schema Check** (30 phút)
Kiểm tra các bảng:
- `PhanCongPhanBien` (Review Assignments)
- `PhanBien` (Reviews)
- `DauThau` (Bidding) - Có thể cần tạo

### **Task 6: Testing** (1-1.5 giờ)

#### **6.1: Backend Testing**
- [ ] Test assignment queries
- [ ] Test accept/decline logic
- [ ] Test bidding logic
- [ ] Test review submission
- [ ] Test validation rules

#### **6.2: Browser Testing**
- [ ] Login as reviewer
- [ ] View assignments
- [ ] Accept assignment
- [ ] Submit bid
- [ ] Create review
- [ ] Submit review
- [ ] View review history

#### **6.3: Edge Cases**
- [ ] Accept already accepted assignment
- [ ] Decline after accepting
- [ ] Submit review after deadline
- [ ] Submit review without assignment
- [ ] Bid on own paper (COI check)

### **Task 7: Bug Fixes & Polish** (30 phút)
- [ ] Fix any validation errors
- [ ] Polish UI/UX
- [ ] Add loading states
- [ ] Add success/error messages
- [ ] Check responsive design

---

## 📋 CHECKLIST TỔNG

### **Backend** ☐
- [ ] ReviewerController created
- [ ] 10 methods implemented
- [ ] All routes registered
- [ ] Validation rules defined
- [ ] Authorization checks added
- [ ] Database queries optimized

### **Frontend** ☐
- [ ] Assignments view
- [ ] Bidding view
- [ ] Review form (create/edit)
- [ ] Reviews list
- [ ] Review detail view
- [ ] All views use Inter font
- [ ] Responsive design
- [ ] Alpine.js for interactivity

### **Testing** ☐
- [ ] Backend tests passing
- [ ] Browser testing complete
- [ ] Edge cases handled
- [ ] No console errors
- [ ] No SQL errors

### **Integration** ☐
- [ ] Dashboard updated
- [ ] Navigation links working
- [ ] Statistics accurate
- [ ] Routes properly named
- [ ] No broken links

---

## 🗄️ DATABASE TABLES

### **PhanCongPhanBien** (Review Assignments)
```
assignment_id       BIGINT (PK)
paper_id           BIGINT (FK → BaiBao)
reviewer_id        BIGINT (FK → NguoiDung)
chair_id           BIGINT (FK → NguoiDung)
status_code        VARCHAR(20)  -- PENDING/ACCEPTED/DECLINED/COMPLETED
token              CHAR(36)
assigned_at        TIMESTAMP
deadline           DATE
```

### **PhanBien** (Reviews)
```
review_id          BIGINT (PK)
assignment_id      BIGINT (FK → PhanCongPhanBien)
paper_id           BIGINT (FK → BaiBao)
reviewer_id        BIGINT (FK → NguoiDung)
rating_*           INT (1-10) -- Các tiêu chí
recommendation     VARCHAR(20) -- ACCEPT/MINOR_REVISION/MAJOR_REVISION/REJECT
comments           TEXT
confidential_comments TEXT
created_at         TIMESTAMP
submitted_at       TIMESTAMP
```

### **DauThau** (Bidding) - Cần kiểm tra
```
bidding_id         BIGINT (PK)
paper_id           BIGINT (FK → BaiBao)
reviewer_id        BIGINT (FK → NguoiDung)
bid_type           VARCHAR(20) -- INTERESTED/NOT_INTERESTED/CONFLICT
created_at         TIMESTAMP
```

---

## 🎯 EXPECTED OUTCOMES

Sau khi hoàn thành Phase 8.5:
1. ✅ Reviewer có thể xem tất cả assignments
2. ✅ Reviewer có thể accept/decline assignments
3. ✅ Reviewer có thể bid cho papers
4. ✅ Reviewer có thể viết và submit reviews
5. ✅ Reviewer có thể xem lịch sử reviews
6. ✅ Dashboard hiển thị thống kê chính xác
7. ✅ Tất cả routes hoạt động đúng
8. ✅ UI/UX nhất quán với author features

---

## 🚀 BẮT ĐẦU TRIỂN KHAI

**Thứ tự thực hiện:**
1. Kiểm tra database schema
2. Tạo ReviewerController
3. Định nghĩa routes
4. Tạo views (theo thứ tự: assignments → bidding → review form → reviews list)
5. Testing
6. Bug fixes

**Sẵn sàng bắt đầu!** 🎯
