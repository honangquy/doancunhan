# API Reviewer - Theo dõi Revision & Xác nhận (Mobile App)

## 🎯 Tổng quan

API này cho phép **Reviewer** trong mobile app:
- ✅ Xem danh sách bài báo được assign kèm trạng thái revision
- ✅ Theo dõi lịch sử chỉnh sửa (các phiên bản) của từng bài báo
- ✅ Xác nhận kết quả chỉnh sửa (approve hoặc yêu cầu sửa thêm)
- ✅ So sánh 2 phiên bản bài báo

---

## 📋 Danh sách API

| Method | Endpoint | Mô tả |
|--------|----------|-------|
| GET | `/api/reviewer/papers-with-revisions` | Lấy danh sách bài có revision |
| GET | `/api/reviewer/papers/{id}/revision-history` | Xem lịch sử revision |
| POST | `/api/reviewer/papers/{id}/confirm-revision` | Xác nhận kết quả revision |
| GET | `/api/reviewer/papers/{id}/compare-versions` | So sánh 2 versions |

---

## 1️⃣ Lấy danh sách bài có revision

### Request
```http
GET /api/reviewer/papers-with-revisions
Authorization: Bearer {token}
```

### Response Success (200)
```json
{
  "success": true,
  "data": [
    {
      "assignment_id": 1,
      "paper_id": 5,
      "title": "Machine Learning in Healthcare Systems",
      "current_version": 2,
      "total_versions": 2,
      "last_revision_date": "2025-11-14 10:30:00",
      "revision_status": "pending_review",
      "author_name": "Nguyễn Văn A",
      "author_email": "nva@huit.edu.vn",
      "submission_date": "2025-11-01",
      "paper_status": "UNDER_REVIEW"
    },
    {
      "assignment_id": 2,
      "paper_id": 8,
      "title": "Blockchain Applications in Supply Chain",
      "current_version": 3,
      "total_versions": 3,
      "last_revision_date": "2025-11-13 14:20:00",
      "revision_status": "approved",
      "author_name": "Trần Thị B",
      "author_email": "ttb@huit.edu.vn",
      "submission_date": "2025-10-28",
      "paper_status": "ACCEPTED"
    }
  ],
  "total": 2
}
```

### Giải thích các trường
- `revision_status`: 
  - `pending_review` - Chưa review phiên bản hiện tại
  - `approved` - Đã approve phiên bản hiện tại
  - `needs_changes` - Yêu cầu tác giả chỉnh sửa thêm
- `current_version`: Số phiên bản hiện tại (1, 2, 3...)
- `total_versions`: Tổng số phiên bản đã upload

---

## 2️⃣ Xem lịch sử revision của bài báo

### Request
```http
GET /api/reviewer/papers/5/revision-history
Authorization: Bearer {token}
```

### Response Success (200)
```json
{
  "success": true,
  "data": {
    "paper": {
      "paper_id": 5,
      "title": "Machine Learning in Healthcare Systems",
      "author_name": "Nguyễn Văn A",
      "author_email": "nva@huit.edu.vn",
      "submission_date": "2025-11-01",
      "status": "UNDER_REVIEW",
      "current_version_id": 12
    },
    "versions": [
      {
        "version_id": 11,
        "version_no": 1,
        "file_path": "papers/2025/11/paper_5_v1.pdf",
        "file_url": "http://127.0.0.1:8000/storage/papers/2025/11/paper_5_v1.pdf",
        "uploaded_at": "2025-11-01 08:30:00",
        "is_current": false,
        "review_status": "reviewed",
        "reviewer_decision": "MAJOR_REVISION",
        "reviewer_comments": "Cần bổ sung phần literature review và thêm experiments",
        "review_date": "2025-11-05 14:20:00"
      },
      {
        "version_id": 12,
        "version_no": 2,
        "file_path": "papers/2025/11/paper_5_v2.pdf",
        "file_url": "http://127.0.0.1:8000/storage/papers/2025/11/paper_5_v2.pdf",
        "uploaded_at": "2025-11-10 16:45:00",
        "is_current": true,
        "review_status": "pending",
        "reviewer_decision": null,
        "reviewer_comments": null,
        "review_date": null
      }
    ],
    "total_versions": 2
  }
}
```

### Response Error (403) - Không có quyền
```json
{
  "success": false,
  "message": "Bạn không có quyền xem bài báo này"
}
```

---

## 3️⃣ Xác nhận kết quả chỉnh sửa

### Request
```http
POST /api/reviewer/papers/5/confirm-revision
Authorization: Bearer {token}
Content-Type: application/json

{
  "version_id": 12,
  "decision": "APPROVE",
  "comments": "Tác giả đã chỉnh sửa tốt, bổ sung đầy đủ literature review và experiments. Bài báo đạt yêu cầu để publish."
}
```

### Request Body
| Field | Type | Required | Values | Mô tả |
|-------|------|----------|--------|-------|
| version_id | integer | ✅ | - | ID của phiên bản cần xác nhận |
| decision | string | ✅ | `APPROVE`, `REQUEST_CHANGES` | Quyết định của reviewer |
| comments | string | ✅ | min: 10, max: 5000 | Nhận xét chi tiết |

### Response Success (200) - APPROVE
```json
{
  "success": true,
  "message": "Đã xác nhận phiên bản chỉnh sửa đạt yêu cầu",
  "data": {
    "review_id": 25,
    "decision": "ACCEPT",
    "created_at": "2025-11-14 15:30:00"
  }
}
```

### Response Success (200) - REQUEST_CHANGES
```json
{
  "success": true,
  "message": "Đã yêu cầu tác giả chỉnh sửa thêm",
  "data": {
    "review_id": 26,
    "decision": "MAJOR_REVISION",
    "created_at": "2025-11-14 15:35:00"
  }
}
```

### Response Error (422) - Validation
```json
{
  "success": false,
  "message": "Dữ liệu không hợp lệ",
  "errors": {
    "comments": ["Comments phải có ít nhất 10 ký tự"],
    "decision": ["Decision phải là APPROVE hoặc REQUEST_CHANGES"]
  }
}
```

### Hành vi sau khi xác nhận
1. **APPROVE**:
   - Tạo review với decision = `ACCEPT`
   - Cập nhật assignment status = `COMPLETED`
   - Nếu tất cả reviewer approve → cập nhật paper status = `ACCEPTED`

2. **REQUEST_CHANGES**:
   - Tạo review với decision = `MAJOR_REVISION`
   - Cập nhật paper status = `NEEDS_REVISION`
   - Tác giả sẽ nhận được thông báo yêu cầu chỉnh sửa

---

## 4️⃣ So sánh 2 phiên bản

### Request
```http
GET /api/reviewer/papers/5/compare-versions?old_version=1&new_version=2
Authorization: Bearer {token}
```

### Query Parameters
| Param | Type | Required | Mô tả |
|-------|------|----------|-------|
| old_version | integer | ✅ | Số phiên bản cũ (1, 2, 3...) |
| new_version | integer | ✅ | Số phiên bản mới (1, 2, 3...) |

### Response Success (200)
```json
{
  "success": true,
  "data": {
    "old_version": {
      "version_id": 11,
      "version_no": 1,
      "file_path": "papers/2025/11/paper_5_v1.pdf",
      "file_url": "http://127.0.0.1:8000/storage/papers/2025/11/paper_5_v1.pdf",
      "uploaded_at": "2025-11-01 08:30:00"
    },
    "new_version": {
      "version_id": 12,
      "version_no": 2,
      "file_path": "papers/2025/11/paper_5_v2.pdf",
      "file_url": "http://127.0.0.1:8000/storage/papers/2025/11/paper_5_v2.pdf",
      "uploaded_at": "2025-11-10 16:45:00"
    },
    "changes_summary": "Phiên bản 2 được tải lên 10/11/2025 16:45 (cách phiên bản 1 9 ngày)"
  }
}
```

### Use case
Mobile app sẽ tải 2 file PDF về và hiển thị side-by-side hoặc cho phép reviewer xem tuần tự để so sánh thay đổi.

---

## 🔐 Authentication

Tất cả API đều yêu cầu Bearer token:

```http
Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...
```

Token lấy từ API `/api/login` (đã có sẵn).

---

## 📱 Workflow cho Mobile App

### 1. Dashboard Reviewer
```
GET /api/reviewer/papers-with-revisions
```
Hiển thị danh sách bài báo với badges:
- 🔵 Pending review (màu xanh)
- ✅ Approved (màu xanh lá)
- ⚠️ Needs changes (màu vàng)

### 2. Chi tiết bài báo
```
GET /api/reviewer/papers/{id}/revision-history
```
Hiển thị timeline các phiên bản:
```
Version 2 ● [Current] - 10/11/2025
  Status: Pending review
  
Version 1 ● - 01/11/2025
  Reviewed: ✓
  Decision: Major revision required
  Comments: "Cần bổ sung literature review..."
```

### 3. Xem & So sánh file
```
GET /api/reviewer/papers/{id}/compare-versions?old_version=1&new_version=2
```
- Tải 2 file PDF
- Hiển thị trong PDF viewer
- Cho phép zoom, scroll đồng bộ

### 4. Xác nhận revision
```
POST /api/reviewer/papers/{id}/confirm-revision
{
  "version_id": 12,
  "decision": "APPROVE",
  "comments": "..."
}
```
Hiển thị form với:
- Radio buttons: ✅ Approve / ⚠️ Request more changes
- Textarea: Comments (required, min 10 chars)
- Button: Submit

---

## 🎨 UI/UX Suggestions cho Mobile

### Card design cho danh sách bài
```
┌─────────────────────────────────────┐
│ Machine Learning in Healthcare      │
│ Author: Nguyễn Văn A                │
│                                     │
│ 🔵 Pending Review                   │
│ Version 2/2 • Updated 10/11/2025    │
│                                     │
│ [View History] [Review Now →]       │
└─────────────────────────────────────┘
```

### Timeline phiên bản
```
● Version 2 (Current)          10/11/2025
  📄 paper_v2.pdf
  Status: Pending your review
  
  [Compare with V1] [Review Now]
  
● Version 1                     01/11/2025
  📄 paper_v1.pdf
  ✓ Reviewed by you
  Decision: Major Revision
  "Cần bổ sung literature review..."
```

### Form xác nhận
```
┌─────────────────────────────────────┐
│ Confirm Revision                    │
├─────────────────────────────────────┤
│                                     │
│ ○ Approve - Accept this version    │
│ ○ Request Changes - Need revision  │
│                                     │
│ Comments: (required)                │
│ ┌─────────────────────────────────┐ │
│ │                                 │ │
│ │                                 │ │
│ │                                 │ │
│ └─────────────────────────────────┘ │
│                                     │
│        [Cancel]  [Submit ✓]         │
└─────────────────────────────────────┘
```

---

## 🧪 Testing với Postman/cURL

### 1. Login để lấy token
```bash
curl -X POST http://127.0.0.1:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "reviewer@huit.edu.vn",
    "password": "password123"
  }'
```

### 2. Lấy danh sách bài
```bash
curl -X GET http://127.0.0.1:8000/api/reviewer/papers-with-revisions \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### 3. Xem lịch sử revision
```bash
curl -X GET http://127.0.0.1:8000/api/reviewer/papers/5/revision-history \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### 4. Xác nhận revision
```bash
curl -X POST http://127.0.0.1:8000/api/reviewer/papers/5/confirm-revision \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "version_id": 12,
    "decision": "APPROVE",
    "comments": "Tác giả đã chỉnh sửa tốt, bài báo đạt yêu cầu."
  }'
```

---

## 📊 Database Schema liên quan

### Bảng `paperversion`
```sql
version_id INT PRIMARY KEY
paper_id INT
version_no INT
file_path VARCHAR(500)
uploaded_at DATETIME
```

### Bảng `review`
```sql
review_id INT PRIMARY KEY
assignment_id INT
paper_version_id INT
decision ENUM('ACCEPT', 'MINOR_REVISION', 'MAJOR_REVISION', 'REJECT')
comments TEXT
created_at DATETIME
```

### Bảng `reviewer_assignments`
```sql
assignment_id INT PRIMARY KEY
user_id INT (reviewer)
paper_id INT
status ENUM('PENDING', 'ACCEPTED', 'IN_PROGRESS', 'COMPLETED')
review_submitted_at DATETIME
```

---

## ✅ Checklist Mobile Integration

- [ ] Implement API calls trong mobile app
- [ ] Hiển thị danh sách bài với revision status
- [ ] Timeline lịch sử các phiên bản
- [ ] PDF viewer để xem file
- [ ] Compare 2 phiên bản side-by-side
- [ ] Form xác nhận với validation
- [ ] Push notification khi có version mới
- [ ] Offline mode (cache data)
- [ ] Error handling & retry logic
- [ ] Loading states & skeleton screens

---

## 🎯 Next Steps

1. **Mobile team** implement 4 API endpoints
2. **Backend** test với Postman
3. **Mobile** tạo UI theo design suggestions
4. **Test** end-to-end workflow
5. **Deploy** lên staging environment

---

📝 **Last updated:** 14/11/2025
👨‍💻 **Maintainer:** HUIT Conference System Team
