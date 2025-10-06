# ⚡ Phase 4: Paper Management - Quick Start Guide

## 🎯 Quick Overview

**Phase 4 APIs:** 13 APIs mới
- **Paper Management:** 8 APIs (submit, list, update, delete, download, statistics)
- **Version Management:** 5 APIs (upload, list, compare versions)

**Key Features:**
- ✅ Submit papers với file upload (PDF/DOC/DOCX, max 10MB)
- ✅ Multi-author support (existing users + external authors)
- ✅ Version control system
- ✅ Permission-based access (author, submitter, reviewer, admin)
- ✅ Paper status workflow (SUBMITTED → UNDER_REVIEW → REVISION_REQUIRED → REVISED → ACCEPTED/REJECTED)

---

## 🚀 Quick Start (5 phút)

### Step 1: Login as Author
```bash
POST /api/auth/login
Body: {"email": "author2@huit.edu.vn", "password": "password123"}
→ Lưu token
```

### Step 2: Submit Paper
```bash
POST /api/papers
Authorization: Bearer {token}
Content-Type: multipart/form-data

Body:
- conference_id: 1
- track_id: 1
- title: "My First AI Paper"
- abstract: "This paper discusses AI applications..."
- authors[0][user_id]: 3
- authors[0][is_contact]: true
- file: paper.pdf (upload file)
```

### Step 3: View My Papers
```bash
GET /api/my-papers
Authorization: Bearer {token}
→ Xem paper vừa submit
```

### Step 4: Download Paper
```bash
GET /api/papers/{id}/download
Authorization: Bearer {token}
→ Download file PDF
```

### Step 5: Upload New Version (sau khi có REVISION_REQUIRED)
```bash
POST /api/papers/{id}/versions
Authorization: Bearer {token}
Content-Type: multipart/form-data

Body:
- file: paper_revised.pdf
- note: "Đã sửa theo yêu cầu reviewer"
```

---

## 📋 13 APIs Chi tiết

### Paper Management (8 APIs)

#### 1. GET /api/papers
**Filters:** conference_id, track_id, status, search, my_papers, sort_by, per_page

```bash
# List all papers
GET /api/papers

# Filter by conference
GET /api/papers?conference_id=1

# Filter by status
GET /api/papers?status=SUBMITTED

# Search
GET /api/papers?search=AI

# My papers only
GET /api/papers?my_papers=true
```

#### 2. POST /api/papers
**Body:** conference_id, track_id, title, abstract, authors[], file

```bash
POST /api/papers
Content-Type: multipart/form-data

conference_id: 1
track_id: 1
title: "Paper Title"
abstract: "Paper abstract..."
authors[0][user_id]: 3
authors[0][is_contact]: true
file: @paper.pdf
```

#### 3. GET /api/papers/{id}
**Includes:** hoiThao, tieuBan, submitter, authors, versions, statusHistory

```bash
GET /api/papers/1
```

#### 4. PUT /api/papers/{id}
**Permissions:** Submitter only, status = SUBMITTED or REVISION_REQUIRED

```bash
PUT /api/papers/1
Body: {
  "title": "Updated Title",
  "abstract": "Updated abstract...",
  "track_id": 2
}
```

#### 5. DELETE /api/papers/{id}
**Action:** Withdraw paper (status → WITHDRAWN)

```bash
DELETE /api/papers/1
```

#### 6. GET /api/my-papers
**Filters:** status, conference_id, sort_by, per_page

```bash
GET /api/my-papers
GET /api/my-papers?status=SUBMITTED
GET /api/my-papers?conference_id=1
```

#### 7. GET /api/papers/statistics
**Stats:** total, by_status, by_track

```bash
GET /api/papers/statistics
GET /api/papers/statistics?conference_id=1
```

#### 8. GET /api/papers/{id}/download
**Returns:** File download (PDF/DOC/DOCX)

```bash
GET /api/papers/1/download
```

---

### Version Management (5 APIs)

#### 9. GET /api/papers/{paper_id}/versions
**List:** All versions sorted by version_no desc

```bash
GET /api/papers/1/versions
```

#### 10. POST /api/papers/{paper_id}/versions
**Body:** file (required), note (optional)

```bash
POST /api/papers/1/versions
Content-Type: multipart/form-data

file: @paper_v2.pdf
note: "Đã sửa theo yêu cầu reviewer"
```

#### 11. GET /api/papers/{paper_id}/versions/{version_no}
**Details:** Specific version info

```bash
GET /api/papers/1/versions/2
```

#### 12. GET /api/papers/{paper_id}/versions/{version_no}/download
**Returns:** Version file download

```bash
GET /api/papers/1/versions/2/download
```

#### 13. GET /api/papers/{paper_id}/versions/compare
**Query:** version1, version2

```bash
GET /api/papers/1/versions/compare?version1=1&version2=2
```

---

## 🔐 Permission Matrix

| Action | Admin | Submitter | Co-Author | Track Chair | Reviewer |
|--------|-------|-----------|-----------|-------------|----------|
| View Paper | ✅ | ✅ | ✅ | ✅ (own track) | ✅ (assigned) |
| Submit Paper | ✅ | ✅ | ✅ | ✅ | ✅ |
| Edit Paper | ✅ | ✅ | ❌ | ❌ | ❌ |
| Withdraw Paper | ✅ | ✅ | ❌ | ❌ | ❌ |
| Upload Version | ✅ | ✅ | ❌ | ❌ | ❌ |
| Download Paper | ✅ | ✅ | ✅ | ✅ (own track) | ✅ (assigned) |

---

## 📊 Paper Status Workflow

```
SUBMITTED → UNDER_REVIEW → REVISION_REQUIRED → REVISED → ACCEPTED
                                                           ↓
                                                        REJECTED
                                                           ↓
                                                        WITHDRAWN
```

**Status Details:**
- **SUBMITTED:** Vừa nộp, chờ assign reviewer
- **UNDER_REVIEW:** Đang được phản biện
- **REVISION_REQUIRED:** Reviewer yêu cầu sửa
- **REVISED:** Author đã nộp bản sửa
- **ACCEPTED:** Chấp nhận xuất bản
- **REJECTED:** Từ chối
- **CAMERA_READY:** Bản in cuối cùng
- **WITHDRAWN:** Author rút bài

**Edit/Upload Rules:**
- ✅ Edit: SUBMITTED, REVISION_REQUIRED
- ✅ Upload version: SUBMITTED, REVISION_REQUIRED, REVISED
- ❌ Withdraw: ACCEPTED, CAMERA_READY, WITHDRAWN

---

## 🧪 Testing Scenarios

### Scenario 1: Submit New Paper
```bash
# 1. Login as Author
POST /api/auth/login
Body: {"email": "author2@huit.edu.vn", "password": "password123"}

# 2. Get conferences
GET /api/conferences?status=OPEN

# 3. Get tracks
GET /api/conferences/1/tracks

# 4. Submit paper
POST /api/papers
conference_id: 1
track_id: 1
title: "AI in Healthcare"
abstract: "This paper..."
authors[0][user_id]: 3
authors[0][is_contact]: true
file: @paper.pdf

# 5. Verify
GET /api/my-papers
```

### Scenario 2: Multi-Author Paper
```bash
POST /api/papers
authors[0][user_id]: 3              # Existing user (contact)
authors[0][is_contact]: true
authors[1][full_name]: "External"   # New external author
authors[1][email]: "ext@uni.edu"
authors[1][organization]: "XYZ Uni"
authors[1][is_contact]: false
```

### Scenario 3: Version Management
```bash
# 1. Submit paper
POST /api/papers → paper_id = 5

# 2. View versions
GET /api/papers/5/versions → version_no = 1

# 3. Upload v2 (after REVISION_REQUIRED)
POST /api/papers/5/versions
file: @paper_v2.pdf
note: "Fixed grammar and added results"

# 4. Compare versions
GET /api/papers/5/versions/compare?version1=1&version2=2

# 5. Download v1
GET /api/papers/5/versions/1/download
```

### Scenario 4: Search & Filter
```bash
# Search by keyword
GET /api/papers?search=machine learning

# Filter by status
GET /api/papers?status=SUBMITTED&conference_id=1

# My papers only
GET /api/my-papers?status=REVISION_REQUIRED

# Sort by title
GET /api/papers?sort_by=title&sort_order=asc
```

### Scenario 5: Statistics
```bash
# Overall stats
GET /api/papers/statistics

# Conference stats
GET /api/papers/statistics?conference_id=1

# Track stats from conference
GET /api/conferences/1/statistics
```

---

## ⚠️ Common Errors & Solutions

### Error 400: "Hội thảo không đang mở nhận bài"
**Cause:** Conference status ≠ OPEN
**Solution:** Check conference status: `GET /api/conferences/1`

### Error 400: "Đã hết hạn nộp bài"
**Cause:** Past deadline_submission
**Solution:** Check deadline: `GET /api/conferences/1`

### Error 400: "Track không thuộc hội thảo này"
**Cause:** track_id không thuộc conference_id
**Solution:** `GET /api/conferences/1/tracks` để lấy đúng track_id

### Error 403: "Chỉ người nộp bài mới có thể chỉnh sửa"
**Cause:** Không phải submitter
**Solution:** Chỉ submitter mới edit được

### Error 400: "Không thể chỉnh sửa bài báo ở trạng thái hiện tại"
**Cause:** Status không phải SUBMITTED hoặc REVISION_REQUIRED
**Solution:** Check paper status: `GET /api/papers/{id}`

### Error 422: "File phải là PDF, DOC hoặc DOCX"
**Cause:** File type không hợp lệ
**Solution:** Upload file .pdf, .doc, hoặc .docx (max 10MB)

### Error 422: "authors là bắt buộc và phải có ít nhất 1 author"
**Cause:** Thiếu authors array
**Solution:** Thêm ít nhất 1 author

---

## 💾 File Storage

**Storage Path:** `storage/app/public/papers/{conference_id}/`

**File Naming:** `paper_{paper_id}_v{version_no}_{timestamp}.{ext}`

**Example:** `paper_5_v2_1728131400.pdf`

**Max Size:** 10MB

**Allowed Types:** PDF, DOC, DOCX

**Access:** Via API only (với permission check)

---

## 🔗 Integration với Phase 3

### Conference → Papers
```bash
# Get conference papers
GET /api/papers?conference_id=1

# Get conference statistics (includes paper count)
GET /api/conferences/1/statistics
```

### Track → Papers
```bash
# Get track papers
GET /api/tracks/1/papers

# Filter by status
GET /api/tracks/1/papers?status=SUBMITTED
```

### User → Papers
```bash
# My papers (as author or submitter)
GET /api/my-papers

# My conferences (includes paper counts)
GET /api/my-conferences
```

---

## 📊 Total APIs Count

**Phase 1:** Setup (Database, Migrations, Seeders)
**Phase 2:** Authentication (7 APIs)
**Phase 3:** Conference Management (22 APIs)
**Phase 4:** Paper Management (13 APIs)

**Total APIs so far:** 42 APIs ✅

**Next:** Phase 5 - Review System (~15 APIs)

---

## ✅ Phase 4 Checklist

- [x] BaiBao model với relationships
- [x] PhienBanBaiBao model
- [x] PaperController với 8 methods
- [x] PaperVersionController với 5 methods
- [x] File upload system (multipart/form-data)
- [x] Version control system
- [x] Permission checks (submitter, co-author, reviewer, admin)
- [x] Paper status workflow
- [x] Statistics APIs
- [x] Download APIs (current + specific version)
- [x] Multi-author support (existing + external users)
- [x] 13 routes added to api.php
- [x] API documentation (PHASE4_API_DOCS.md)
- [x] Quick start guide (PHASE4_QUICK.md)

**Phase 4 Complete! 🎉**

---

## 🚀 Next Steps

1. **Test Phase 4 APIs** với Postman/Thunder Client
2. **Phase 5: Review System**
   - Assign reviewers to papers
   - Submit reviews
   - COI (Conflict of Interest) management
   - Bidding system
   - Review statistics
3. **Phase 6: Decision & Notifications**
   - Chair makes decision (accept/reject)
   - Email notifications
   - Announcement system

**Ready to test Phase 4! 🎯**
