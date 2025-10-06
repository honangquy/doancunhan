# Phase 8.4: Author Features - Implementation Plan

**Start Date:** October 5, 2025  
**Estimated Time:** 6-10 hours  
**Status:** 🚀 In Progress

---

## 🎯 OBJECTIVES

Triển khai đầy đủ các tính năng cho Author role, bao gồm:
1. Paper submission system
2. File upload system
3. Co-author management
4. Paper management (view, edit, withdraw)
5. Review tracking

---

## 📋 TASKS BREAKDOWN

### **Task 1: Paper Submission Form** (2-3 hours)
**Priority:** HIGH  
**Files to create/modify:**
- `resources/views/author/papers/create.blade.php` - Submission form
- `app/Http/Controllers/Author/PaperController.php` - Paper CRUD operations
- `routes/web.php` - Add author paper routes
- `app/Http/Requests/StorePaperRequest.php` - Form validation

**Features:**
- [ ] 1.1. Create submission form UI với Tailwind
- [ ] 1.2. Conference selection dropdown (only ACTIVE conferences)
- [ ] 1.3. Paper details form (title, abstract, keywords)
- [ ] 1.4. File upload field (PDF only)
- [ ] 1.5. Co-authors section
- [ ] 1.6. Form validation (client-side + server-side)
- [ ] 1.7. Submit button với loading state
- [ ] 1.8. Success/error messages

**Database operations:**
```sql
INSERT INTO BaiBao (
    conference_id,
    submitter_id,
    title,
    abstract,
    keywords,
    file_path,
    status_code,
    created_at
)
```

---

### **Task 2: File Upload System** (1-2 hours)
**Priority:** HIGH  
**Files to create/modify:**
- `app/Http/Controllers/Author/PaperController.php` - Upload logic
- `config/filesystems.php` - Storage configuration
- `.env` - Storage path settings

**Features:**
- [ ] 2.1. Create storage directory structure: `storage/app/papers/{conference_id}/{paper_id}/`
- [ ] 2.2. Validate file type (PDF only)
- [ ] 2.3. Validate file size (max 10MB)
- [ ] 2.4. Generate unique filename: `{paper_id}_{timestamp}.pdf`
- [ ] 2.5. Store file metadata in database
- [ ] 2.6. Handle upload errors gracefully
- [ ] 2.7. Create symlink for storage access

**Security considerations:**
- Sanitize filename
- Check MIME type (not just extension)
- Prevent directory traversal
- Limit file size
- Store outside public directory

---

### **Task 3: Co-Author Management** (1-2 hours)
**Priority:** MEDIUM  
**Files to create/modify:**
- `resources/views/author/papers/create.blade.php` - Co-author section
- `app/Http/Controllers/Author/PaperController.php` - Co-author logic
- Alpine.js component for dynamic add/remove

**Features:**
- [ ] 3.1. Dynamic co-author fields (Alpine.js)
- [ ] 3.2. Add co-author button
- [ ] 3.3. Remove co-author button
- [ ] 3.4. Author order (drag & drop or up/down buttons)
- [ ] 3.5. Contact author checkbox
- [ ] 3.6. Validate at least 1 author
- [ ] 3.7. Save to TacGiaBaiBao table

**Database operations:**
```sql
-- Submitter is always first author
INSERT INTO TacGiaBaiBao (paper_id, user_id, author_order, is_contact)

-- Additional co-authors
INSERT INTO TacGiaBaiBao (paper_id, user_id, author_order, is_contact)
```

**UI Design:**
```
┌─────────────────────────────────────────┐
│ Authors                                 │
├─────────────────────────────────────────┤
│ 1. [You] (Submitter) ☑ Contact Author  │
├─────────────────────────────────────────┤
│ 2. [John Doe      ] ☐ Contact Author  │
│    [↑] [↓] [Remove]                    │
├─────────────────────────────────────────┤
│ 3. [Jane Smith    ] ☐ Contact Author  │
│    [↑] [↓] [Remove]                    │
├─────────────────────────────────────────┤
│ [+ Add Co-Author]                      │
└─────────────────────────────────────────┘
```

---

### **Task 4: Paper List & Details** (1-2 hours)
**Priority:** HIGH  
**Files to create/modify:**
- `resources/views/author/papers/index.blade.php` - Paper list
- `resources/views/author/papers/show.blade.php` - Paper details
- `app/Http/Controllers/Author/PaperController.php` - Index & show methods

**Features:**
- [ ] 4.1. Paper list table với filters (conference, status)
- [ ] 4.2. Search functionality
- [ ] 4.3. Sort by date, title, status
- [ ] 4.4. Pagination (20 papers/page)
- [ ] 4.5. Status badges (color-coded)
- [ ] 4.6. Quick actions (View, Edit, Withdraw)
- [ ] 4.7. Paper details page
- [ ] 4.8. Display co-authors
- [ ] 4.9. Display reviews (if available)
- [ ] 4.10. Display final decision

**Paper List UI:**
```
┌─────────────────────────────────────────────────────────────┐
│ My Papers                              [+ Submit New Paper] │
├─────────────────────────────────────────────────────────────┤
│ [Search...] [Conference ▾] [Status ▾]                      │
├────┬─────────────────────┬──────────┬─────────┬───────────┤
│ ID │ Title               │Conference│ Status  │ Actions   │
├────┼─────────────────────┼──────────┼─────────┼───────────┤
│ 45 │ Machine Learning... │ ICT 2025 │ACCEPTED │View Edit  │
│ 42 │ Cloud Computing...  │ AI 2025  │REJECTED │View       │
│ 38 │ Neural Networks...  │ ICT 2025 │UNDER_RE │View Edit  │
└────┴─────────────────────┴──────────┴─────────┴───────────┘
```

---

### **Task 5: Edit Paper** (1 hour)
**Priority:** MEDIUM  
**Files to create/modify:**
- `resources/views/author/papers/edit.blade.php` - Edit form
- `app/Http/Controllers/Author/PaperController.php` - Update method
- `app/Http/Requests/UpdatePaperRequest.php` - Update validation

**Features:**
- [ ] 5.1. Pre-populate form với existing data
- [ ] 5.2. Only allow edit if status = DRAFT or SUBMITTED
- [ ] 5.3. Check submission deadline
- [ ] 5.4. Allow replacing PDF file
- [ ] 5.5. Update co-authors
- [ ] 5.6. Show edit history (optional)
- [ ] 5.7. Confirmation before save

**Business Rules:**
- Cannot edit if status = UNDER_REVIEW, ACCEPTED, or REJECTED
- Cannot edit after conference submission deadline
- Keep old file if no new file uploaded
- Log changes for audit trail

---

### **Task 6: Withdraw Paper** (30 minutes)
**Priority:** LOW  
**Files to create/modify:**
- `app/Http/Controllers/Author/PaperController.php` - Withdraw method
- Modal confirmation in view

**Features:**
- [ ] 6.1. Withdraw button with confirmation modal
- [ ] 6.2. Only allow withdraw if status != ACCEPTED
- [ ] 6.3. Update status to WITHDRAWN
- [ ] 6.4. Send notification to chair (future)
- [ ] 6.5. Show withdrawal reason (optional)

**Database operation:**
```sql
UPDATE BaiBao 
SET status_code = 'WITHDRAWN',
    withdrawal_reason = 'User reason',
    updated_at = NOW()
WHERE paper_id = ? AND submitter_id = ?
```

---

### **Task 7: Review Tracking** (1 hour)
**Priority:** MEDIUM  
**Files to create/modify:**
- `resources/views/author/papers/show.blade.php` - Reviews section
- `app/Http/Controllers/Author/PaperController.php` - Get reviews

**Features:**
- [ ] 7.1. Display review status (Pending, In Progress, Completed)
- [ ] 7.2. Show review scores (if allowed by conference policy)
- [ ] 7.3. Show review comments (if allowed)
- [ ] 7.4. Show reviewer recommendations
- [ ] 7.5. Show final decision
- [ ] 7.6. Timeline visualization
- [ ] 7.7. Respond to revision requests (future)

**Privacy considerations:**
- Hide reviewer identities (blind review)
- Only show reviews after all reviews completed
- Follow conference policy on what to reveal

**Reviews Display:**
```
┌─────────────────────────────────────────┐
│ Review Status                           │
├─────────────────────────────────────────┤
│ ● Review 1: Completed ✅                │
│   Score: 8/10                           │
│   Recommendation: Accept with revisions │
│   [View Comments]                       │
├─────────────────────────────────────────┤
│ ● Review 2: In Progress ⏳             │
│   Assigned: 02/10/2025                  │
├─────────────────────────────────────────┤
│ ● Review 3: Pending ⏸️                 │
│   Assigned: 03/10/2025                  │
└─────────────────────────────────────────┘
```

---

## 🗄️ DATABASE SCHEMA REFERENCE

### **Tables to use:**

#### 1. **BaiBao** (Papers)
```sql
paper_id (PK)
conference_id (FK → HoiThao)
submitter_id (FK → NguoiDung)
title VARCHAR(500)
abstract TEXT
keywords VARCHAR(500)
file_path VARCHAR(255)
status_code VARCHAR(50) -- DRAFT, SUBMITTED, UNDER_REVIEW, ACCEPTED, REJECTED, WITHDRAWN
created_at DATETIME
updated_at DATETIME
```

#### 2. **TacGiaBaiBao** (Paper Authors)
```sql
paper_id (FK → BaiBao)
user_id (FK → NguoiDung)
author_order INT
is_contact TINYINT(1)
```

#### 3. **PhanCongPhanBien** (Review Assignments)
```sql
assignment_id (PK)
paper_id (FK → BaiBao)
reviewer_id (FK → NguoiDung)
status VARCHAR(50) -- PENDING, ACCEPTED, DECLINED, COMPLETED
assigned_date DATETIME
due_date DATETIME
```

#### 4. **PhanBien** (Reviews)
```sql
review_id (PK)
assignment_id (FK → PhanCongPhanBien)
review_content TEXT
score INT
recommendation VARCHAR(50) -- ACCEPT, REJECT, REVISE
submitted_at DATETIME
```

---

## 🎨 UI DESIGN GUIDELINES

### **Color Scheme (Author Role)**
- Primary: Blue (#1e40af)
- Gradient: `from-blue-800 via-blue-700 to-blue-600`
- Success: Green (#10b981)
- Danger: Red (#ef4444)
- Warning: Yellow (#f59e0b)

### **Status Badges**
```css
DRAFT       → bg-gray-200 text-gray-800
SUBMITTED   → bg-blue-100 text-blue-800
UNDER_REVIEW→ bg-yellow-100 text-yellow-800
ACCEPTED    → bg-green-100 text-green-800
REJECTED    → bg-red-100 text-red-800
WITHDRAWN   → bg-gray-300 text-gray-600
```

### **Form Layout**
- Use 2-column layout for desktop
- Single column for mobile
- Clear section headings
- Helpful tooltips
- Progress indicator for multi-step forms

---

## 🧪 TESTING CHECKLIST

### **Functional Testing**
- [ ] Submit paper with valid data → Success
- [ ] Submit paper with invalid data → Show errors
- [ ] Upload valid PDF → File stored correctly
- [ ] Upload invalid file → Show error
- [ ] Add co-authors → Saved to database
- [ ] Edit paper before deadline → Updated
- [ ] Edit paper after deadline → Blocked
- [ ] Withdraw paper → Status changed
- [ ] View paper details → All data displayed
- [ ] View reviews → Reviews displayed correctly

### **Security Testing**
- [ ] Cannot edit other authors' papers
- [ ] Cannot view other authors' papers
- [ ] Cannot upload non-PDF files
- [ ] Cannot upload files > 10MB
- [ ] SQL injection protection
- [ ] XSS protection
- [ ] CSRF token validation

### **UI/UX Testing**
- [ ] Form validation messages clear
- [ ] Loading states work
- [ ] Mobile responsive
- [ ] Error handling graceful
- [ ] Success messages shown

---

## 📁 FILE STRUCTURE

```
app/
├── Http/
│   ├── Controllers/
│   │   └── Author/
│   │       └── PaperController.php          [CREATE]
│   └── Requests/
│       ├── StorePaperRequest.php            [CREATE]
│       └── UpdatePaperRequest.php           [CREATE]
resources/
└── views/
    └── author/
        └── papers/
            ├── index.blade.php              [CREATE]
            ├── create.blade.php             [CREATE]
            ├── show.blade.php               [CREATE]
            └── edit.blade.php               [CREATE]
routes/
└── web.php                                  [MODIFY]
storage/
└── app/
    └── papers/                              [CREATE DIRECTORY]
```

---

## 🚀 IMPLEMENTATION ORDER

### **Day 1 (3-4 hours):**
1. ✅ Create PaperController
2. ✅ Create routes
3. ✅ Create paper submission form view
4. ✅ Implement file upload system
5. ✅ Test basic submission

### **Day 2 (3-4 hours):**
6. ✅ Implement co-author management
7. ✅ Create paper list view
8. ✅ Create paper details view
9. ✅ Implement edit functionality
10. ✅ Implement withdraw functionality

### **Day 3 (1-2 hours):**
11. ✅ Add review tracking display
12. ✅ Testing & bug fixes
13. ✅ UI polish
14. ✅ Documentation

---

## 📝 NOTES

### **Important considerations:**
1. **File Storage:** Use `storage/app/papers/` NOT `public/` for security
2. **Validation:** Validate MIME type, not just file extension
3. **Permissions:** Check author owns the paper before edit/delete
4. **Deadlines:** Check conference submission deadline
5. **Status Flow:** DRAFT → SUBMITTED → UNDER_REVIEW → ACCEPTED/REJECTED

### **Future enhancements (Phase 9+):**
- Email notifications
- Revision submission
- Camera-ready upload
- Presentation file upload
- Co-author invitations via email

---

**Status:** 📋 Plan Complete, Ready to Start Implementation  
**Next Action:** Create PaperController and routes
