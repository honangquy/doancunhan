# 🧪 QUICK API TESTS - Phase 3

## Base URL
```
http://huit-conferences.local/api
```

---

## 1️⃣ Health Check (Public)

```bash
GET /health
```

**Expected Response:**
```json
{
  "status": "ok",
  "message": "HUIT Conference API is running",
  "timestamp": "2025-10-04 16:30:00"
}
```

---

## 2️⃣ Login (Get Token)

```bash
POST /auth/login
Content-Type: application/json

{
  "email": "admin@huit.edu.vn",
  "password": "admin123"
}
```

**Expected Response:**
```json
{
  "success": true,
  "data": {
    "user": { ... },
    "token": "eyJ0eXAiOiJKV1QiLCJhbGc..."
  }
}
```

**💡 Copy the token for next requests!**

---

## 3️⃣ List Conferences (Public)

```bash
GET /conferences
```

**With filters:**
```bash
GET /conferences?status=OPEN&year=2025&per_page=5
```

**Expected Response:**
```json
{
  "success": true,
  "data": {
    "current_page": 1,
    "data": [ ... ],
    "total": 2
  }
}
```

---

## 4️⃣ Get Conference Details (Public)

```bash
GET /conferences/1
```

**Expected Response:**
```json
{
  "success": true,
  "data": {
    "conference_id": 1,
    "title": "Hội thảo Khoa học Công nghệ 2025",
    "khoa": { ... },
    "tieuBans": [ ... ]
  },
  "statistics": {
    "total_tracks": 3,
    "total_papers": 0
  }
}
```

---

## 5️⃣ Get Conference Statistics (Public)

```bash
GET /conferences/1/statistics
```

**Expected Response:**
```json
{
  "success": true,
  "data": {
    "conference_info": { ... },
    "tracks": { ... },
    "papers": { ... },
    "deadlines": { ... }
  }
}
```

---

## 6️⃣ Create Conference (Admin/Chair) 🔒

```bash
POST /conferences
Authorization: Bearer {your_token}
Content-Type: application/json

{
  "title": "Test Conference 2026",
  "year": 2026,
  "start_date": "2026-12-01",
  "end_date": "2026-12-03",
  "deadline_submission": "2026-10-15",
  "deadline_review": "2026-11-01",
  "deadline_camera_ready": "2026-11-20",
  "level_code": "NATIONAL",
  "faculty_id": 1
}
```

**Expected Response:**
```json
{
  "success": true,
  "message": "Conference created successfully",
  "data": { ... }
}
```

---

## 7️⃣ Get My Conferences 🔒

```bash
GET /my-conferences
Authorization: Bearer {your_token}
```

**With role filter:**
```bash
GET /my-conferences?role=chair
```

---

## 8️⃣ List Tracks 🔒

```bash
GET /conferences/1/tracks
Authorization: Bearer {your_token}
```

**Expected Response:**
```json
{
  "success": true,
  "data": [
    {
      "track_id": 1,
      "track_name": "AI & Machine Learning",
      "chair": { ... },
      "bai_baos_count": 0
    }
  ]
}
```

---

## 9️⃣ Create Track (Admin/Chair) 🔒

```bash
POST /conferences/1/tracks
Authorization: Bearer {your_token}
Content-Type: application/json

{
  "track_name": "Blockchain & Web3",
  "description": "Track về công nghệ Blockchain",
  "chair_id": 2
}
```

**Expected Response:**
```json
{
  "success": true,
  "message": "Track created successfully",
  "data": { ... }
}
```

---

## 🔟 Get My Tracks (Chair) 🔒

```bash
GET /my-tracks
Authorization: Bearer {chair_token}
```

---

## 1️⃣1️⃣ Submit Conference Request (Chair) 🔒

```bash
POST /conference-requests
Authorization: Bearer {chair_token}
Content-Type: application/json

{
  "title": "Hội thảo AI 2026",
  "year": 2026,
  "start_date": "2026-12-10",
  "end_date": "2026-12-12",
  "deadline_submission": "2026-10-20",
  "deadline_review": "2026-11-05",
  "deadline_camera_ready": "2026-11-25",
  "level_code": "INTERNATIONAL",
  "notes": "Proposal for AI conference"
}
```

**Expected Response:**
```json
{
  "success": true,
  "message": "Conference request submitted successfully",
  "data": {
    "request_id": 1,
    "status": "PENDING",
    "request_date": "2025-10-04 16:30:00"
  }
}
```

---

## 1️⃣2️⃣ List Conference Requests 🔒

```bash
GET /conference-requests
Authorization: Bearer {your_token}
```

**With status filter:**
```bash
GET /conference-requests?status=PENDING
```

**Expected Response:**
```json
{
  "success": true,
  "data": {
    "data": [
      {
        "request_id": 1,
        "status": "PENDING",
        "hoiThao": { ... },
        "requester": { ... }
      }
    ]
  }
}
```

---

## 1️⃣3️⃣ Approve Request (Admin) 🔒

```bash
POST /conference-requests/1/approve
Authorization: Bearer {admin_token}
Content-Type: application/json

{
  "notes": "Approved. Good proposal."
}
```

**Expected Response:**
```json
{
  "success": true,
  "message": "Conference request approved successfully",
  "data": {
    "status": "APPROVED",
    "admin_id": 1
  }
}
```

---

## 1️⃣4️⃣ Reject Request (Admin) 🔒

```bash
POST /conference-requests/1/reject
Authorization: Bearer {admin_token}
Content-Type: application/json

{
  "notes": "Rejected: Not enough budget"
}
```

---

## 1️⃣5️⃣ Get Request Statistics (Admin) 🔒

```bash
GET /conference-requests/statistics
Authorization: Bearer {admin_token}
```

**Expected Response:**
```json
{
  "success": true,
  "data": {
    "total": 5,
    "pending": 2,
    "approved": 2,
    "rejected": 1,
    "by_status": [ ... ],
    "recent_requests": [ ... ]
  }
}
```

---

## 🔑 Test Accounts

### For Admin APIs (approve/reject)
```
Email: admin@huit.edu.vn
Password: admin123
```

### For Chair APIs (submit request, create track)
```
Email: chair1@huit.edu.vn
Password: password123
```

### For Author/Reviewer APIs
```
Email: author2@huit.edu.vn
Password: password123
```

---

## ⚡ Quick Test Flow

1. **Health Check** → Verify API is running
2. **Login** → Get token for admin
3. **List Conferences** → See existing conferences
4. **Get Conference Details** → Check conference #1
5. **Create Conference** → Test admin permission
6. **Login as Chair** → Get chair token
7. **Submit Conference Request** → Test request flow
8. **Login as Admin** → Approve/Reject request
9. **List Tracks** → See tracks in conference
10. **Create Track** → Test track creation

---

## 🐛 Common Issues

### 401 Unauthorized
- Token expired → Login again
- Token missing → Add Authorization header
- Invalid token → Check token format

### 403 Forbidden
- Wrong role → Use correct account (Admin/Chair)
- Not authorized → Check permissions

### 422 Validation Error
- Check request body format
- Verify required fields
- Check date validations

### 404 Not Found
- Conference/Track ID doesn't exist
- Check seeded data with `php artisan db:seed`

---

## 📝 Notes

- All protected endpoints require `Authorization: Bearer {token}`
- Tokens expire after 60 minutes (default JWT TTL)
- Use `POST /auth/refresh` to refresh token
- Public endpoints: health, conferences list/show/statistics

---

**Happy Testing! 🚀**

For more details: [API_DOCS.md](API_DOCS.md)
