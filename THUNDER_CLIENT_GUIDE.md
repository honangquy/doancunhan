# ⚡ HƯỚNG DẪN SỬ DỤNG THUNDER CLIENT

## 📥 Bước 1: Cài đặt Thunder Client

1. **Mở Extensions trong VS Code:**
   - Nhấn `Ctrl + Shift + X` (Windows/Linux)
   - Hoặc `Cmd + Shift + X` (Mac)
   - Hoặc click icon Extensions ở sidebar bên trái

2. **Tìm và cài đặt:**
   - Gõ vào ô search: `Thunder Client`
   - Tìm extension "Thunder Client" by Ranga Vadhineni
   - Click nút **Install**

3. **Kiểm tra:**
   - Sau khi cài xong, bạn sẽ thấy icon ⚡ (sấm sét) ở sidebar bên trái
   - Click vào icon ⚡ để mở Thunder Client

---

## 📋 Bước 2: Import Collection

### Cách 1: Import từ file JSON (Khuyến nghị)

1. **Click vào icon ⚡ Thunder Client** ở sidebar
2. Chọn tab **"Collections"** (thứ 2 từ trên xuống)
3. Click nút **"Menu"** (3 chấm) ở góc trên bên phải
4. Chọn **"Import"**
5. Chọn **"Import from File"**
6. Browse và chọn file: `thunder-client-collection.json`
7. Click **Open**

✅ **Xong!** Bạn sẽ thấy collection "HUIT Conference APIs - Phase 3" với 4 folders:
- 1. Authentication (5 requests)
- 2. Conferences (7 requests)
- 3. Tracks (6 requests)
- 4. Conference Requests (6 requests)

### Cách 2: Tạo requests thủ công

Nếu import không được, bạn có thể tạo từng request theo hướng dẫn bên dưới.

---

## 🧪 Bước 3: Test APIs

### Test 1: Health Check ✅

1. Mở folder **"1. Authentication"**
2. Click vào request **"1.1 Health Check"**
3. Click nút **"Send"** (màu xanh)
4. Bạn sẽ thấy Response:
   ```json
   {
     "status": "ok",
     "message": "HUIT Conference API is running",
     "timestamp": "2025-10-04 ..."
   }
   ```

✅ **Success!** API đang chạy!

---

### Test 2: Login Admin 🔑

1. Click vào request **"1.2 Login Admin"**
2. Kiểm tra tab **Body**:
   ```json
   {
     "email": "admin@huit.edu.vn",
     "password": "admin123"
   }
   ```
3. Click **"Send"**
4. Bạn sẽ nhận được Response với **token**:
   ```json
   {
     "success": true,
     "message": "Đăng nhập thành công",
     "data": {
       "user": { ... },
       "token": "eyJ0eXAiOiJKV1..."
     }
   }
   ```

5. **QUAN TRỌNG:** Copy giá trị `token` (dòng dài bắt đầu bằng `eyJ0...`)

---

### Test 3: Set Environment Variable (Token)

**Cách 1: Set biến token thủ công (Dễ nhất)**

Sau khi login, với mỗi request cần token:
1. Click vào request (ví dụ: "1.4 Get Profile")
2. Chọn tab **"Headers"**
3. Tìm dòng `Authorization: Bearer {{token}}`
4. Thay `{{token}}` bằng token thật của bạn:
   ```
   Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGc...
   ```

**Cách 2: Set Environment Variable (Chuyên nghiệp hơn)**

1. Click vào tab **"Env"** ở thanh trên cùng của Thunder Client
2. Click **"New Environment"**
3. Đặt tên: `HUIT Conference Local`
4. Thêm biến:
   - Name: `token`
   - Value: (paste token bạn copy được)
5. Click **"Save"**
6. Select environment "HUIT Conference Local" ở dropdown trên cùng

Bây giờ tất cả requests dùng `{{token}}` sẽ tự động thay bằng token của bạn!

---

### Test 4: Get Profile (Protected) 🔒

1. Click vào request **"1.4 Get Profile"**
2. Đảm bảo đã set token (theo cách 1 hoặc 2 ở trên)
3. Click **"Send"**
4. Bạn sẽ nhận được thông tin profile:
   ```json
   {
     "success": true,
     "data": {
       "user_id": 1,
       "full_name": "Quản trị viên",
       "email": "admin@huit.edu.vn",
       "roles": ["ADMIN"],
       ...
     }
   }
   ```

✅ **Success!** Token hoạt động!

---

### Test 5: List Conferences (Public) 📋

1. Click vào request **"2.1 List Conferences"**
2. Request này không cần token (public)
3. Click **"Send"**
4. Bạn sẽ thấy danh sách conferences:
   ```json
   {
     "success": true,
     "data": {
       "current_page": 1,
       "data": [
         {
           "conference_id": 1,
           "title": "Hội thảo Khoa học CNTT HUIT 2025",
           "status": "OPEN",
           ...
         }
       ],
       "total": 2
     }
   }
   ```

---

### Test 6: Create Conference (Admin) ➕

1. Click vào request **"2.5 Create Conference"**
2. Kiểm tra token đã set chưa
3. Tab **Body** đã có sẵn data:
   ```json
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
4. Click **"Send"**
5. Response:
   ```json
   {
     "success": true,
     "message": "Conference created successfully",
     "data": {
       "conference_id": 3,
       "title": "Test Conference 2026",
       ...
     }
   }
   ```

✅ **Success!** Conference đã được tạo!

---

### Test 7: Login as Chair (Để test Conference Request)

1. Click vào request **"1.3 Login Chair"**
2. Click **"Send"**
3. Copy token từ response
4. **Set chairToken:**
   - Nếu dùng Environment: Thêm biến `chairToken` với giá trị vừa copy
   - Nếu manual: Thay `{{chairToken}}` trong các request bằng token này

---

### Test 8: Submit Conference Request (Chair) 📝

1. Click vào request **"4.2 Submit Conference Request (Chair)"**
2. Đảm bảo đã set `chairToken`
3. Body đã có sẵn:
   ```json
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
4. Click **"Send"**
5. Response:
   ```json
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

### Test 9: Approve Request (Admin) ✔️

1. Click vào request **"4.4 Approve Request (Admin)"**
2. Đảm bảo dùng admin token (không phải chairToken)
3. Sửa URL nếu cần: Thay `/1/` bằng request_id vừa tạo
4. Click **"Send"**
5. Response:
   ```json
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

## 🎯 Workflow Test Đầy Đủ

### Scenario 1: Admin creates conference
```
1. Login Admin → Get token
2. Create Conference → Conference created
3. List Conferences → See new conference
4. Get Conference Details → See full info
```

### Scenario 2: Chair requests conference
```
1. Login Chair → Get chairToken
2. Submit Conference Request → Request pending
3. Login Admin → Get admin token
4. Approve Request → Request approved, Conference OPEN
5. List Conferences → See approved conference
```

### Scenario 3: Admin manages tracks
```
1. Login Admin → Get token
2. List Tracks → See existing tracks
3. Create Track → New track created
4. Get Track Details → See track info
5. Update Track → Track updated
```

---

## 🔍 Features của Thunder Client

### 1. Collections
- Organize requests into folders
- Import/Export collections

### 2. Environment Variables
- Set variables: `{{token}}`, `{{baseUrl}}`
- Switch between environments (local, dev, prod)

### 3. Tests
- Write test scripts
- Validate responses

### 4. History
- See all previous requests
- Re-run old requests

### 5. Copy as cURL
- Right-click request → Copy as cURL
- Use in terminal

---

## 🐛 Troubleshooting

### ❌ Request failed: ECONNREFUSED
**Problem:** XAMPP chưa chạy hoặc URL sai

**Solution:**
1. Mở XAMPP Control Panel
2. Start Apache và MySQL
3. Kiểm tra URL: `http://localhost/qly_hthao/qlyhoithao/public/api`

### ❌ 401 Unauthorized
**Problem:** Token hết hạn hoặc sai

**Solution:**
1. Login lại để lấy token mới
2. Update token trong Environment hoặc Header

### ❌ 403 Forbidden
**Problem:** Không đủ quyền

**Solution:**
1. Kiểm tra user role (Admin/Chair/Author)
2. Dùng đúng account cho từng API

### ❌ 422 Validation Error
**Problem:** Dữ liệu gửi lên không hợp lệ

**Solution:**
1. Kiểm tra required fields
2. Kiểm tra format date (YYYY-MM-DD)
3. Đọc error message trong response

---

## 💡 Tips & Tricks

### 1. Save Responses
- Click **Save** ở response panel
- Dùng để so sánh responses

### 2. Copy Response
- Click **Copy** icon ở response
- Paste vào text editor để analyze

### 3. Format JSON
- Response tự động format đẹp
- Click **Raw** để xem dạng gốc

### 4. Test Scripts
- Tab **Tests** để viết validation
- Example:
  ```javascript
  tc.test("Status is 200", () => {
    tc.assert(tc.response.status == 200);
  });
  ```

### 5. Pre-Request Scripts
- Tab **Pre-Req** để chạy code trước request
- Useful để generate timestamps, signatures

---

## 📚 Tài liệu tham khảo

- **Thunder Client Docs:** https://www.thunderclient.com/
- **API Documentation:** [API_DOCS.md](API_DOCS.md)
- **Test Guide:** [TEST_GUIDE.md](TEST_GUIDE.md)

---

## 🎉 Chúc mừng!

Bạn đã setup xong Thunder Client và sẵn sàng test APIs!

**Next Steps:**
1. ✅ Test tất cả 24 requests trong collection
2. ✅ Thử modify data và test edge cases
3. ✅ Ready cho Phase 4 - Paper Management

**Happy Testing! 🚀**
