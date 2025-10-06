# 🚀 POSTMAN - Hướng dẫn Test APIs

## 📋 Mục lục
1. [Cài đặt và Import Collection](#1-cài-đặt-và-import-collection)
2. [Cấu hình Environment Variables](#2-cấu-hình-environment-variables)
3. [Test từng nhóm APIs](#3-test-từng-nhóm-apis)
4. [Sử dụng Auto Token](#4-sử-dụng-auto-token)
5. [Troubleshooting](#5-troubleshooting)

---

## 1. Cài đặt và Import Collection

### Bước 1: Tải Postman
- **Desktop**: Tải tại [postman.com/downloads](https://www.postman.com/downloads)
- **Web**: Truy cập [web.postman.co](https://web.postman.co)

### Bước 2: Import Collection

#### Cách 1: Import từ File
1. Mở Postman
2. Click **Import** (góc trên bên trái)
3. Chọn **Upload Files**
4. Chọn file `HUIT-Conference-APIs.postman_collection.json`
5. Click **Import**

#### Cách 2: Import từ URL (nếu có)
1. Click **Import**
2. Chọn **Link**
3. Paste URL của collection JSON
4. Click **Continue** → **Import**

### Bước 3: Kiểm tra Import thành công
✅ Bạn sẽ thấy collection **"HUIT Conference APIs - Phase 3"** trong sidebar với:
- 📁 **1. Authentication** (9 requests)
- 📁 **2. Conferences** (8 requests)
- 📁 **3. Tracks** (7 requests)
- 📁 **4. Conference Requests** (7 requests)

**Tổng: 31 API requests**

---

## 2. Cấu hình Environment Variables

### Option 1: Sử dụng Collection Variables (Đơn giản - Khuyên dùng)

Collection đã có sẵn variables:
- `base_url`: `http://localhost/qly_hthao/qlyhoithao/public/api`
- `token`: Để trống (sẽ tự động cập nhật khi login)
- `admin_token`: Để trống
- `chair_token`: Để trống

**Không cần làm gì thêm!** Token sẽ tự động lưu khi bạn login.

### Option 2: Tạo Environment mới (Nâng cao)

Nếu muốn quản lý nhiều môi trường (dev, staging, production):

1. Click **Environments** (sidebar trái)
2. Click **+ Create Environment**
3. Đặt tên: `HUIT Conference - Local`
4. Thêm variables:

| Variable | Type | Initial Value | Current Value |
|----------|------|---------------|---------------|
| base_url | default | http://localhost/qly_hthao/qlyhoithao/public/api | (same) |
| token | secret | | |
| admin_token | secret | | |
| chair_token | secret | | |

5. Click **Save**
6. Chọn environment này từ dropdown (góc trên bên phải)

---

## 3. Test từng nhóm APIs

### 📌 Nhóm 1: Authentication

#### Test 1.1: Health Check
```
GET {{base_url}}/health
```

**Kết quả mong đợi:**
```json
{
    "status": "success",
    "message": "HUIT Conference API is running"
}
```

#### Test 1.2: Login Admin ⭐ (QUAN TRỌNG)
```
POST {{base_url}}/auth/login
Body:
{
    "email": "admin@huit.edu.vn",
    "password": "admin123"
}
```

**Kết quả mong đợi:**
```json
{
    "status": "success",
    "message": "Đăng nhập thành công",
    "data": {
        "token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
        "token_type": "bearer",
        "expires_in": 3600,
        "user": { ... }
    }
}
```

**🎯 Quan trọng:** Collection có **Test Script tự động lưu token**!
- `admin_token` sẽ được lưu tự động
- `token` sẽ được set thành admin token

#### Test 1.3: Login Chair
Tương tự, login với tài khoản Chair:
```json
{
    "email": "chair1@huit.edu.vn",
    "password": "password123"
}
```
`chair_token` sẽ được lưu tự động.

#### Test 1.5: Get Profile
```
GET {{base_url}}/auth/profile
Authorization: Bearer {{token}}
```

**Kết quả:** Thông tin user đang đăng nhập

---

### 📌 Nhóm 2: Conferences

#### Test 2.1: List Conferences (Public)
```
GET {{base_url}}/conferences
```
**Không cần token** - API công khai

#### Test 2.2: List Conferences (Filtered)
```
GET {{base_url}}/conferences?status=OPEN&year=2025
```

**Kết quả mong đợi:**
```json
{
    "status": "success",
    "data": {
        "current_page": 1,
        "data": [
            {
                "id": 1,
                "title": "HUIT Conference 2025",
                "status": "OPEN",
                "year": 2025,
                ...
            }
        ],
        "total": 2
    }
}
```

#### Test 2.5: Create Conference (Admin only)
```
POST {{base_url}}/conferences
Authorization: Bearer {{admin_token}}
Body:
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
```

**Lưu ý:** Đổi `{{token}}` thành `{{admin_token}}` trong Authorization tab

---

### 📌 Nhóm 3: Tracks

#### Test 3.1: List Tracks
```
GET {{base_url}}/conferences/1/tracks
Authorization: Bearer {{token}}
```

#### Test 3.2: Create Track (Admin only)
```
POST {{base_url}}/conferences/1/tracks
Authorization: Bearer {{admin_token}}
Body:
{
    "track_name": "Blockchain & Web3",
    "description": "Track về công nghệ Blockchain",
    "chair_id": 2
}
```

#### Test 3.6: Get Track Papers
```
GET {{base_url}}/tracks/1/papers?status=SUBMITTED
Authorization: Bearer {{token}}
```

#### Test 3.7: Get My Tracks (Chair only)
```
GET {{base_url}}/my-tracks
Authorization: Bearer {{chair_token}}
```

**Lưu ý:** Đổi token thành `{{chair_token}}`

---

### 📌 Nhóm 4: Conference Requests

#### Test 4.1: List Conference Requests (Admin)
```
GET {{base_url}}/conference-requests?status=PENDING
Authorization: Bearer {{admin_token}}
```

#### Test 4.2: Submit Conference Request (Chair)
```
POST {{base_url}}/conference-requests
Authorization: Bearer {{chair_token}}
Body:
{
    "title": "Hội thảo AI 2026",
    "year": 2026,
    "start_date": "2026-12-10",
    "end_date": "2026-12-12",
    "deadline_submission": "2026-10-20",
    "deadline_review": "2026-11-05",
    "deadline_camera_ready": "2026-11-25",
    "level_code": "INTERNATIONAL",
    "notes": "Đề xuất tổ chức hội thảo quốc tế về AI"
}
```

**Workflow test:**
1. Chair login → lấy `chair_token`
2. Chair submit request → lưu `request_id` từ response
3. Admin login → lấy `admin_token`
4. Admin approve/reject request

#### Test 4.4: Approve Request (Admin only)
```
POST {{base_url}}/conference-requests/1/approve
Authorization: Bearer {{admin_token}}
Body:
{
    "notes": "Chấp thuận. Đề xuất tốt."
}
```

#### Test 4.7: Get Request Statistics (Admin)
```
GET {{base_url}}/conference-requests/statistics
Authorization: Bearer {{admin_token}}
```

---

## 4. Sử dụng Auto Token

### Test Scripts đã được tích hợp

Collection này có **Test Scripts tự động**:

#### Script trong "1.2 Login Admin":
```javascript
if (pm.response.code === 200) {
    var jsonData = pm.response.json();
    pm.environment.set("admin_token", jsonData.data.token);
    pm.environment.set("token", jsonData.data.token);
    console.log("Admin token saved");
}
```

#### Script trong "1.3 Login Chair":
```javascript
if (pm.response.code === 200) {
    var jsonData = pm.response.json();
    pm.environment.set("chair_token", jsonData.data.token);
    console.log("Chair token saved");
}
```

### Cách sử dụng:

1. **Send** request "1.2 Login Admin"
2. Kiểm tra **Console** (View → Show Postman Console):
   ```
   Admin token saved: eyJ0eXAiOiJKV1QiLCJh...
   ```
3. Token đã được lưu! Giờ bạn có thể gọi các API khác

### Xem Token đã lưu:

- **Collection Variables**: Click collection → **Variables** tab
- **Environment**: Click Environment → Xem Current Value

---

## 5. Troubleshooting

### ❌ Lỗi: "Unauthenticated" hoặc 401

**Nguyên nhân:**
- Token chưa được set
- Token hết hạn (sau 60 phút)
- Token không đúng

**Giải pháp:**
1. Login lại để lấy token mới
2. Kiểm tra Authorization header:
   ```
   Authorization: Bearer {{token}}
   ```
3. Đảm bảo không có space thừa

### ❌ Lỗi: "Unauthorized" hoặc 403

**Nguyên nhân:**
- Không có quyền truy cập
- Ví dụ: Author gọi API admin

**Giải pháp:**
- Dùng đúng token cho từng role:
  - Admin APIs: `{{admin_token}}`
  - Chair APIs: `{{chair_token}}`

### ❌ Lỗi: "404 Not Found"

**Nguyên nhân:**
- URL sai
- ID không tồn tại

**Giải pháp:**
1. Kiểm tra `base_url` trong Variables
2. Kiểm tra ID có tồn tại không:
   ```
   GET {{base_url}}/conferences
   ```
3. Sửa ID trong URL path

### ❌ Lỗi: "422 Validation Error"

**Nguyên nhân:**
- Dữ liệu input không hợp lệ

**Giải pháp:**
- Xem chi tiết lỗi trong response:
  ```json
  {
      "errors": {
          "email": ["Email không đúng định dạng"],
          "password": ["Mật khẩu tối thiểu 8 ký tự"]
      }
  }
  ```
- Sửa dữ liệu input theo yêu cầu

### ❌ Lỗi: "500 Internal Server Error"

**Nguyên nhân:**
- Lỗi server
- Lỗi database

**Giải pháp:**
1. Kiểm tra Laravel logs:
   ```
   C:\xampp\htdocs\qly_hthao\qlyhoithao\storage\logs\laravel.log
   ```
2. Kiểm tra XAMPP (Apache + MySQL) đang chạy
3. Kiểm tra database connection trong `.env`

---

## 📊 Test Scenarios

### Scenario 1: Admin quản lý hội thảo

```
1. POST /auth/login (admin)
   → Lưu admin_token

2. GET /conferences
   → Xem danh sách hội thảo

3. POST /conferences
   → Tạo hội thảo mới

4. GET /conferences/{id}/statistics
   → Xem thống kê hội thảo

5. POST /conferences/{id}/tracks
   → Tạo track cho hội thảo
```

### Scenario 2: Chair submit và quản lý request

```
1. POST /auth/login (chair)
   → Lưu chair_token

2. POST /conference-requests
   → Chair submit đề xuất hội thảo

3. GET /my-tracks
   → Xem các track mà Chair quản lý

4. GET /tracks/{id}/papers
   → Xem papers trong track
```

### Scenario 3: Admin duyệt request

```
1. POST /auth/login (admin)
   → Lưu admin_token

2. GET /conference-requests?status=PENDING
   → Xem các request đang chờ

3. POST /conference-requests/{id}/approve
   → Duyệt request

4. GET /conference-requests/statistics
   → Xem thống kê requests
```

---

## 🎯 Tips & Tricks

### 1. Sử dụng Collection Runner
- Click **Run collection**
- Chọn folder muốn test
- Click **Run HUIT Conference APIs**
- Tự động test tất cả APIs trong folder

### 2. Sử dụng Pre-request Scripts
Thêm vào Collection Pre-request:
```javascript
// Tự động thêm timestamp
pm.collectionVariables.set("timestamp", Date.now());
```

### 3. Organize Requests
- Tạo folder mới: Right-click collection → **Add Folder**
- Di chuyển requests: Drag & drop
- Rename: Right-click → **Rename**

### 4. Save Response Examples
- Send request thành công
- Click **Save Response** → **Save as example**
- Example sẽ hiện khi hover vào request

### 5. Export Collection
- Right-click collection
- **Export**
- Chọn Collection v2.1
- Chia sẻ với team

---

## 📞 Test Accounts

| Role | Email | Password |
|------|-------|----------|
| Admin | admin@huit.edu.vn | admin123 |
| Chair | chair1@huit.edu.vn | password123 |
| Author | author2@huit.edu.vn | password123 |
| Reviewer | reviewer6@huit.edu.vn | password123 |

---

## ✅ Checklist Test

### Authentication (9 APIs)
- [ ] 1.1 Health Check
- [ ] 1.2 Login Admin
- [ ] 1.3 Login Chair
- [ ] 1.4 Register
- [ ] 1.5 Get Profile
- [ ] 1.6 Update Profile
- [ ] 1.7 Change Password
- [ ] 1.8 Refresh Token
- [ ] 1.9 Logout

### Conferences (8 APIs)
- [ ] 2.1 List Conferences
- [ ] 2.2 List Conferences (Filtered)
- [ ] 2.3 Get Conference Details
- [ ] 2.4 Get Conference Statistics
- [ ] 2.5 Create Conference
- [ ] 2.6 Update Conference
- [ ] 2.7 Delete Conference
- [ ] 2.8 Get My Conferences

### Tracks (7 APIs)
- [ ] 3.1 List Tracks
- [ ] 3.2 Create Track
- [ ] 3.3 Get Track Details
- [ ] 3.4 Update Track
- [ ] 3.5 Delete Track
- [ ] 3.6 Get Track Papers
- [ ] 3.7 Get My Tracks

### Conference Requests (7 APIs)
- [ ] 4.1 List Conference Requests
- [ ] 4.2 Submit Conference Request
- [ ] 4.3 Get Request Details
- [ ] 4.4 Approve Request
- [ ] 4.5 Reject Request
- [ ] 4.6 Cancel Request
- [ ] 4.7 Get Request Statistics

**Tổng: 31 APIs**

---

## 🚀 Next Steps

Sau khi test xong Phase 3, bạn có thể:

1. **Phase 4: Paper Management** (~15 APIs)
   - Submit papers
   - Upload files
   - Version control
   - Revision requests

2. **Phase 5: Review System** (~12 APIs)
   - Assign reviewers
   - Submit reviews
   - View review status
   - Decision making

3. **Phase 6: Frontend** (React/Vue)
   - Admin dashboard
   - Chair management
   - Author submission
   - Reviewer interface

---

**Happy Testing! 🎉**
