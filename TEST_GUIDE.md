# 🧪 TEST APIs - Hướng dẫn đơn giản

## 🎯 Cách test nhanh nhất: Dùng Postman hoặc Thunder Client (VS Code Extension)

---

## ✅ OPTION 1: Thunder Client (Khuyến nghị - trong VS Code)

### Bước 1: Cài Thunder Client Extension
1. Mở VS Code
2. Extensions (Ctrl+Shift+X)
3. Tìm "Thunder Client"
4. Install

### Bước 2: Import Collection
Tạo requests như sau:

---

## 📋 TEST CASES

### Base URL
```
http://localhost/qly_hthao/qlyhoithao/public/api
```

---

### ✅ Test 1: Health Check
```
Method: GET
URL: http://localhost/qly_hthao/qlyhoithao/public/api/health
Headers: (none)

Expected: 200 OK
{
  "status": "ok",
  "message": "HUIT Conference API is running",
  "timestamp": "..."
}
```

---

### ✅ Test 2: List Conferences (Public)
```
Method: GET
URL: http://localhost/qly_hthao/qlyhoithao/public/api/conferences
Headers: (none)

Expected: 200 OK
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

### ✅ Test 3: Login Admin
```
Method: POST
URL: http://localhost/qly_hthao/qlyhoithao/public/api/auth/login
Headers: 
  Content-Type: application/json
Body (JSON):
{
  "email": "admin@huit.edu.vn",
  "password": "admin123"
}

Expected: 200 OK
{
  "success": true,
  "data": {
    "user": { ... },
    "token": "eyJ0eXAiOiJKV1Q..."
  }
}

⚠️ SAVE THE TOKEN! Copy giá trị token để dùng cho tests tiếp theo
```

---

### ✅ Test 4: Get Profile (Protected)
```
Method: GET
URL: http://localhost/qly_hthao/qlyhoithao/public/api/auth/profile
Headers: 
  Authorization: Bearer {YOUR_TOKEN_HERE}

Expected: 200 OK
{
  "success": true,
  "data": {
    "user_id": 1,
    "full_name": "Quản trị viên",
    "email": "admin@huit.edu.vn",
    ...
  }
}
```

---

### ✅ Test 5: Get Conference Details
```
Method: GET
URL: http://localhost/qly_hthao/qlyhoithao/public/api/conferences/1
Headers: (none)

Expected: 200 OK
{
  "success": true,
  "data": {
    "conference_id": 1,
    "title": "Hội thảo Khoa học CNTT HUIT 2025",
    ...
  },
  "statistics": { ... }
}
```

---

### ✅ Test 6: List Tracks (Protected)
```
Method: GET
URL: http://localhost/qly_hthao/qlyhoithao/public/api/conferences/1/tracks
Headers: 
  Authorization: Bearer {YOUR_TOKEN_HERE}

Expected: 200 OK
{
  "success": true,
  "data": [
    {
      "track_id": 1,
      "track_name": "...",
      "chair": { ... },
      "bai_baos_count": 0
    }
  ]
}
```

---

### ✅ Test 7: Create Conference (Admin)
```
Method: POST
URL: http://localhost/qly_hthao/qlyhoithao/public/api/conferences
Headers: 
  Authorization: Bearer {YOUR_TOKEN_HERE}
  Content-Type: application/json
Body (JSON):
{
  "title": "Test Conference 2026",
  "year": 2026,
  "start_date": "2026-12-01",
  "end_date": "2026-12-03",
  "deadline_submission": "2026-10-15",
  "deadline_review": "2026-11-01",
  "deadline_camera_ready": "2026-11-20",
  "level_code": "NATIONAL",
  "faculty_id": 1,
  "status": "OPEN"
}

Expected: 201 Created
{
  "success": true,
  "message": "Conference created successfully",
  "data": { ... }
}
```

---

### ✅ Test 8: Create Track (Admin)
```
Method: POST
URL: http://localhost/qly_hthao/qlyhoithao/public/api/conferences/1/tracks
Headers: 
  Authorization: Bearer {YOUR_TOKEN_HERE}
  Content-Type: application/json
Body (JSON):
{
  "track_name": "Blockchain & Web3",
  "description": "Track về công nghệ Blockchain",
  "chair_id": 2
}

Expected: 201 Created
{
  "success": true,
  "message": "Track created successfully",
  "data": { ... }
}
```

---

### ✅ Test 9: Login as Chair
```
Method: POST
URL: http://localhost/qly_hthao/qlyhoithao/public/api/auth/login
Headers: 
  Content-Type: application/json
Body (JSON):
{
  "email": "chair1@huit.edu.vn",
  "password": "password123"
}

Expected: 200 OK
Save the chair token!
```

---

### ✅ Test 10: Submit Conference Request (Chair)
```
Method: POST
URL: http://localhost/qly_hthao/qlyhoithao/public/api/conference-requests
Headers: 
  Authorization: Bearer {CHAIR_TOKEN_HERE}
  Content-Type: application/json
Body (JSON):
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

Expected: 201 Created
{
  "success": true,
  "message": "Conference request submitted successfully",
  "data": {
    "request_id": 1,
    "status": "PENDING",
    ...
  }
}
```

---

### ✅ Test 11: List Conference Requests (Admin)
```
Method: GET
URL: http://localhost/qly_hthao/qlyhoithao/public/api/conference-requests
Headers: 
  Authorization: Bearer {ADMIN_TOKEN_HERE}

Expected: 200 OK
{
  "success": true,
  "data": {
    "data": [
      {
        "request_id": 1,
        "status": "PENDING",
        ...
      }
    ]
  }
}
```

---

### ✅ Test 12: Approve Conference Request (Admin)
```
Method: POST
URL: http://localhost/qly_hthao/qlyhoithao/public/api/conference-requests/1/approve
Headers: 
  Authorization: Bearer {ADMIN_TOKEN_HERE}
  Content-Type: application/json
Body (JSON):
{
  "notes": "Approved. Good proposal."
}

Expected: 200 OK
{
  "success": true,
  "message": "Conference request approved successfully",
  "data": {
    "status": "APPROVED",
    ...
  }
}
```

---

## 🔑 Test Accounts

```
Admin:
  Email: admin@huit.edu.vn
  Password: admin123

Chair:
  Email: chair1@huit.edu.vn
  Password: password123

Author:
  Email: author2@huit.edu.vn
  Password: password123

Reviewer:
  Email: reviewer6@huit.edu.vn
  Password: password123
```

---

## ✅ OPTION 2: Dùng PowerShell (Command Line)

### Test Health Check
```powershell
Invoke-RestMethod -Uri "http://localhost/qly_hthao/qlyhoithao/public/api/health" -Method GET
```

### Test Login và lưu token
```powershell
$loginData = @{
    email = "admin@huit.edu.vn"
    password = "admin123"
} | ConvertTo-Json

$response = Invoke-RestMethod -Uri "http://localhost/qly_hthao/qlyhoithao/public/api/auth/login" -Method POST -Body $loginData -ContentType "application/json"
$token = $response.data.token
Write-Host "Token: $token"
```

### Test API với token
```powershell
$headers = @{
    "Authorization" = "Bearer $token"
}

Invoke-RestMethod -Uri "http://localhost/qly_hthao/qlyhoithao/public/api/auth/profile" -Method GET -Headers $headers
```

---

## ✅ OPTION 3: Dùng curl (Bash/Git Bash)

### Test Health Check
```bash
curl http://localhost/qly_hthao/qlyhoithao/public/api/health
```

### Test Login
```bash
curl -X POST http://localhost/qly_hthao/qlyhoithao/public/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@huit.edu.vn","password":"admin123"}'
```

### Test với token
```bash
TOKEN="your_token_here"

curl http://localhost/qly_hthao/qlyhoithao/public/api/auth/profile \
  -H "Authorization: Bearer $TOKEN"
```

---

## 🐛 Troubleshooting

### 401 Unauthorized
- Token hết hạn → Login lại
- Token sai format → Kiểm tra "Bearer " prefix
- Chưa có token → Login trước

### 403 Forbidden
- Không đủ quyền → Dùng tài khoản Admin/Chair
- Sai role → Kiểm tra user roles

### 422 Validation Error
- Thiếu field bắt buộc
- Format dữ liệu sai
- Date validation fail (check deadline order)

### 404 Not Found
- ID không tồn tại
- Path sai → Kiểm tra URL
- Chưa có dữ liệu → Chạy seeder

### 500 Server Error
- Kiểm tra logs: `storage/logs/laravel.log`
- Database connection issue
- Code error → Check terminal output

---

## 📚 Tài liệu tham khảo

- **API Documentation:** [API_DOCS.md](API_DOCS.md)
- **Quick Tests:** [QUICK_API_TESTS.md](QUICK_API_TESTS.md)
- **Phase 3 Summary:** [PHASE3_SUMMARY.md](PHASE3_SUMMARY.md)

---

**💡 TIP:** Dùng Thunder Client trong VS Code để test dễ nhất!

1. Install Thunder Client Extension
2. Create New Request
3. Copy URL + Body từ guide này
4. Click Send!

**Happy Testing! 🚀**
