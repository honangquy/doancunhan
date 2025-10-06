# 📚 API DOCUMENTATION - HUIT CONFERENCES

## Base URL

### Với Virtual Host (khuyến nghị)
```
http://huit-conferences.local/api
```

### Không có Virtual Host
```
http://localhost/qly_hthao/qlyhoithao/public/api
```

**Xem hướng dẫn setup Virtual Host:** [XAMPP_SETUP.md](XAMPP_SETUP.md)

## Authentication
Tất cả protected endpoints yêu cầu JWT token trong header:
```
Authorization: Bearer {token}
```

---

## 🔐 AUTHENTICATION APIs

### 1. Register (Đăng ký)
**Endpoint:** `POST /auth/register`

**Request Body:**
```json
{
  "email": "user@huit.edu.vn",
  "password": "password123",
  "password_confirmation": "password123",
  "full_name": "Nguyễn Văn A",
  "is_student": false,
  "faculty_id": 1,
  "organization": "Trường Đại học Công nghệ TP.HCM"
}
```

**Response Success (201):**
```json
{
  "success": true,
  "message": "Đăng ký thành công",
  "data": {
    "user": {
      "user_id": 11,
      "email": "user@huit.edu.vn",
      "full_name": "Nguyễn Văn A",
      "is_student": false,
      "faculty_id": 1,
      "organization": "Trường Đại học Công nghệ TP.HCM"
    },
    "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9..."
  }
}
```

---

### 2. Login (Đăng nhập)
**Endpoint:** `POST /auth/login`

**Request Body:**
```json
{
  "email": "admin@huit.edu.vn",
  "password": "admin123"
}
```

**Response Success (200):**
```json
{
  "success": true,
  "message": "Đăng nhập thành công",
  "data": {
    "user": {
      "user_id": 1,
      "email": "admin@huit.edu.vn",
      "full_name": "Quản trị viên",
      "is_student": false,
      "faculty_id": 1,
      "organization": "Trường Đại học Công nghệ TP.HCM - HUIT",
      "roles": [
        {
          "role_code": "ADMIN",
          "role_name": "Quản trị viên",
          "conference_id": null
        }
      ]
    },
    "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9..."
  }
}
```

**Response Error (401):**
```json
{
  "success": false,
  "message": "Email không tồn tại"
}
```

---

### 3. Get Profile (Lấy thông tin user)
**Endpoint:** `GET /auth/profile`

**Headers:**
```
Authorization: Bearer {token}
```

**Response Success (200):**
```json
{
  "success": true,
  "data": {
    "user_id": 1,
    "email": "admin@huit.edu.vn",
    "full_name": "Quản trị viên",
    "is_student": false,
    "faculty_id": 1,
    "organization": "Trường Đại học Công nghệ TP.HCM - HUIT",
    "created_at": "2025-10-04T11:30:00.000000Z",
    "roles": [
      {
        "role_code": "ADMIN",
        "role_name": "Quản trị viên",
        "conference_id": null
      }
    ],
    "khoa": {
      "faculty_id": 1,
      "faculty_code": "CNTT",
      "faculty_name": "Khoa Công nghệ thông tin"
    }
  }
}
```

---

### 4. Update Profile (Cập nhật profile)
**Endpoint:** `PUT /auth/profile`

**Headers:**
```
Authorization: Bearer {token}
```

**Request Body:**
```json
{
  "full_name": "Nguyễn Văn A (Updated)",
  "faculty_id": 2,
  "organization": "HUIT - Updated"
}
```

**Response Success (200):**
```json
{
  "success": true,
  "message": "Cập nhật thông tin thành công",
  "data": {
    "user_id": 1,
    "email": "admin@huit.edu.vn",
    "full_name": "Nguyễn Văn A (Updated)",
    "faculty_id": 2,
    "organization": "HUIT - Updated"
  }
}
```

---

### 5. Change Password (Đổi mật khẩu)
**Endpoint:** `POST /auth/change-password`

**Headers:**
```
Authorization: Bearer {token}
```

**Request Body:**
```json
{
  "current_password": "oldpassword123",
  "new_password": "newpassword123",
  "new_password_confirmation": "newpassword123"
}
```

**Response Success (200):**
```json
{
  "success": true,
  "message": "Đổi mật khẩu thành công"
}
```

---

### 6. Logout (Đăng xuất)
**Endpoint:** `POST /auth/logout`

**Headers:**
```
Authorization: Bearer {token}
```

**Response Success (200):**
```json
{
  "success": true,
  "message": "Đăng xuất thành công"
}
```

---

### 7. Refresh Token (Làm mới token)
**Endpoint:** `POST /auth/refresh`

**Headers:**
```
Authorization: Bearer {token}
```

**Response Success (200):**
```json
{
  "success": true,
  "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.new_token..."
}
```

---

## 🏥 HEALTH CHECK

### Health Status
**Endpoint:** `GET /health`

**Response:**
```json
{
  "status": "ok",
  "message": "HUIT Conference API is running",
  "timestamp": "2025-10-04 11:30:45"
}
```

---

## 🧪 TESTING với cURL

### 1. Register
```bash
curl -X POST http://localhost:8000/api/auth/register \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "email": "test@huit.edu.vn",
    "password": "password123",
    "password_confirmation": "password123",
    "full_name": "Test User",
    "is_student": false,
    "faculty_id": 1,
    "organization": "HUIT"
  }'
```

### 2. Login
```bash
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "email": "admin@huit.edu.vn",
    "password": "admin123"
  }'
```

### 3. Get Profile
```bash
curl -X GET http://localhost:8000/api/auth/profile \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Accept: application/json"
```

---

## 📋 TEST ACCOUNTS

| Role | Email | Password | Description |
|------|-------|----------|-------------|
| Admin | admin@huit.edu.vn | admin123 | Quản trị viên hệ thống |
| Author | author2@huit.edu.vn | password123 | Tác giả (Sinh viên) |
| Author | author3@huit.edu.vn | password123 | Tác giả (Sinh viên) |
| Author | author4@huit.edu.vn | password123 | Tác giả (Giảng viên) |
| Author | author5@huit.edu.vn | password123 | Tác giả (Giảng viên) |
| Reviewer | reviewer6@huit.edu.vn | password123 | Phản biện viên |
| Reviewer | reviewer7@huit.edu.vn | password123 | Phản biện viên |
| Reviewer | reviewer8@huit.edu.vn | password123 | Phản biện viên |
| Reviewer | reviewer9@huit.edu.vn | password123 | Phản biện viên |
| Reviewer | reviewer10@huit.edu.vn | password123 | Phản biện viên |

---

## ⚠️ ERROR CODES

| Code | Message | Description |
|------|---------|-------------|
| 200 | OK | Success |
| 201 | Created | Resource created |
| 401 | Unauthorized | Authentication failed |
| 403 | Forbidden | Account locked |
| 404 | Not Found | Resource not found |
| 422 | Unprocessable Entity | Validation errors |
| 500 | Internal Server Error | Server error |

---

---

## � CONFERENCE MANAGEMENT APIs

### 1. List Conferences (Public)
**Endpoint:** `GET /conferences`

**Query Parameters:**
- `status` (optional): OPEN, CLOSED, CANCELLED
- `level_code` (optional): NATIONAL, INTERNATIONAL, WORKSHOP
- `year` (optional): 2025
- `faculty_id` (optional): 1
- `search` (optional): Search by title
- `parent_only` (optional): true/false (chỉ hội thảo cha)
- `sort_by` (optional): start_date, year, title (default: start_date)
- `sort_order` (optional): asc, desc (default: desc)
- `per_page` (optional): 15 (default)

**Response Success (200):**
```json
{
  "success": true,
  "data": {
    "current_page": 1,
    "data": [
      {
        "conference_id": 1,
        "title": "Hội thảo Khoa học Công nghệ 2025",
        "year": 2025,
        "start_date": "2025-12-01",
        "end_date": "2025-12-03",
        "status": "OPEN",
        "level_code": "NATIONAL",
        "faculty_id": 1,
        "khoa": {
          "faculty_id": 1,
          "faculty_name": "Khoa Công nghệ Thông tin"
        }
      }
    ],
    "per_page": 15,
    "total": 2
  }
}
```

### 2. Get Conference Details (Public)
**Endpoint:** `GET /conferences/{id}`

**Response Success (200):**
```json
{
  "success": true,
  "data": {
    "conference_id": 1,
    "title": "Hội thảo Khoa học Công nghệ 2025",
    "year": 2025,
    "start_date": "2025-12-01",
    "end_date": "2025-12-03",
    "deadline_submission": "2025-10-15",
    "deadline_review": "2025-11-01",
    "deadline_camera_ready": "2025-11-20",
    "status": "OPEN",
    "khoa": {},
    "tieuBans": [],
    "children": []
  },
  "statistics": {
    "total_tracks": 3,
    "total_papers": 0,
    "submitted_papers": 0,
    "accepted_papers": 0,
    "rejected_papers": 0,
    "sub_conferences": 0
  }
}
```

### 3. Create Conference (Admin/Chair only)
**Endpoint:** `POST /conferences`
**Auth Required:** Yes

**Request Body:**
```json
{
  "title": "Hội thảo Khoa học Công nghệ 2026",
  "year": 2026,
  "start_date": "2026-12-01",
  "end_date": "2026-12-03",
  "deadline_submission": "2026-10-15",
  "deadline_review": "2026-11-01",
  "deadline_camera_ready": "2026-11-20",
  "level_code": "NATIONAL",
  "faculty_id": 1,
  "parent_id": null,
  "status": "OPEN"
}
```

**Response Success (201):**
```json
{
  "success": true,
  "message": "Conference created successfully",
  "data": {}
}
```

### 4. Update Conference (Admin/Chair only)
**Endpoint:** `PUT /conferences/{id}`
**Auth Required:** Yes

### 5. Delete Conference (Admin only)
**Endpoint:** `DELETE /conferences/{id}`
**Auth Required:** Yes

### 6. Get Conference Statistics (Public)
**Endpoint:** `GET /conferences/{id}/statistics`

**Response Success (200):**
```json
{
  "success": true,
  "data": {
    "conference_info": {
      "title": "Hội thảo...",
      "year": 2025,
      "status": "OPEN",
      "is_submission_open": true,
      "is_review_open": true
    },
    "tracks": {
      "total": 3,
      "list": []
    },
    "papers": {
      "total": 0,
      "by_status": [],
      "by_track": []
    },
    "users": {
      "total_authors": 0,
      "total_reviewers": 0
    },
    "reviews": {
      "total_assignments": 0,
      "completed_reviews": 0
    },
    "deadlines": {
      "submission": "2025-10-15",
      "review": "2025-11-01",
      "camera_ready": "2025-11-20",
      "days_until_submission": 11,
      "days_until_review": 28
    }
  }
}
```

### 7. Get My Conferences
**Endpoint:** `GET /my-conferences`
**Auth Required:** Yes

**Query Parameters:**
- `role` (optional): all, chair, reviewer, author (default: all)

---

## 🎯 TRACK MANAGEMENT APIs

### 1. List Tracks
**Endpoint:** `GET /conferences/{conference_id}/tracks`
**Auth Required:** Yes

**Response Success (200):**
```json
{
  "success": true,
  "data": [
    {
      "track_id": 1,
      "track_name": "AI & Machine Learning",
      "description": "Track về trí tuệ nhân tạo",
      "chair_id": 2,
      "chair": {
        "user_id": 2,
        "full_name": "Chair User",
        "email": "chair@huit.edu.vn"
      },
      "bai_baos_count": 0
    }
  ]
}
```

### 2. Create Track (Admin/Chair only)
**Endpoint:** `POST /conferences/{conference_id}/tracks`
**Auth Required:** Yes

**Request Body:**
```json
{
  "track_name": "AI & Machine Learning",
  "description": "Track về trí tuệ nhân tạo và học máy",
  "chair_id": 2
}
```

**Response Success (201):**
```json
{
  "success": true,
  "message": "Track created successfully",
  "data": {}
}
```

### 3. Get Track Details
**Endpoint:** `GET /tracks/{id}`
**Auth Required:** Yes

### 4. Update Track (Admin/Chair only)
**Endpoint:** `PUT /tracks/{id}`
**Auth Required:** Yes

### 5. Delete Track (Admin only)
**Endpoint:** `DELETE /tracks/{id}`
**Auth Required:** Yes

### 6. Get Track Papers
**Endpoint:** `GET /tracks/{id}/papers`
**Auth Required:** Yes

**Query Parameters:**
- `status` (optional): SUBMITTED, ACCEPTED, REJECTED
- `sort_by` (optional): submission_date, title
- `sort_order` (optional): asc, desc
- `per_page` (optional): 15

### 7. Get My Tracks (Chair only)
**Endpoint:** `GET /my-tracks`
**Auth Required:** Yes

---

## 📝 CONFERENCE REQUEST APIs

### 1. List Conference Requests
**Endpoint:** `GET /conference-requests`
**Auth Required:** Yes

**Query Parameters:**
- `status` (optional): PENDING, APPROVED, REJECTED

**Note:** Admin xem tất cả, User chỉ xem của mình

### 2. Create Conference Request (Chair only)
**Endpoint:** `POST /conference-requests`
**Auth Required:** Yes

**Request Body:**
```json
{
  "title": "Hội thảo Khoa học Công nghệ 2026",
  "year": 2026,
  "start_date": "2026-12-01",
  "end_date": "2026-12-03",
  "deadline_submission": "2026-10-15",
  "deadline_review": "2026-11-01",
  "deadline_camera_ready": "2026-11-20",
  "level_code": "NATIONAL",
  "notes": "Lý do tổ chức hội thảo..."
}
```

**Response Success (201):**
```json
{
  "success": true,
  "message": "Conference request submitted successfully",
  "data": {
    "request_id": 1,
    "conference_id": 3,
    "requester_id": 2,
    "status": "PENDING",
    "request_date": "2025-10-04 10:00:00"
  }
}
```

### 3. Get Request Details
**Endpoint:** `GET /conference-requests/{id}`
**Auth Required:** Yes

### 4. Approve Request (Admin only)
**Endpoint:** `POST /conference-requests/{id}/approve`
**Auth Required:** Yes

**Request Body:**
```json
{
  "notes": "Approved. Good proposal."
}
```

### 5. Reject Request (Admin only)
**Endpoint:** `POST /conference-requests/{id}/reject`
**Auth Required:** Yes

**Request Body:**
```json
{
  "notes": "Rejected because..."
}
```

### 6. Cancel Request (Requester only)
**Endpoint:** `POST /conference-requests/{id}/cancel`
**Auth Required:** Yes

### 7. Request Statistics (Admin only)
**Endpoint:** `GET /conference-requests/statistics`
**Auth Required:** Yes

---

## 🔜 COMING SOON

### Paper APIs (Phase 4)
- POST /conferences/{id}/papers - Nộp bài
- GET /papers - Bài của tôi
- GET /papers/{id} - Chi tiết bài
- PUT /papers/{id} - Cập nhật bài

### Review APIs (Phase 5)
- GET /my-reviews - Assignments của tôi
- POST /papers/{id}/bidding - Bidding
- POST /assignments/{id}/review - Nộp review

---

**© 2025 HUIT Conferences - API Documentation v2.0**
**Last Updated: 04/10/2025 - Phase 3 Complete**

