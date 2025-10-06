# 🧪 HƯỚNG DẪN TEST API

## Chuẩn bị

### 1. Khởi động XAMPP server

**Bước 1:** Mở XAMPP Control Panel

**Bước 2:** Start Apache và MySQL

**Bước 3:** Truy cập dự án tại:
```
http://localhost/qly_hthao/qlyhoithao/public
```

**Hoặc tạo Virtual Host** (khuyến nghị):

1. Mở file `C:\xampp\apache\conf\extra\httpd-vhosts.conf`

2. Thêm vào cuối file:
```apache
<VirtualHost *:80>
    ServerName huit-conferences.local
    DocumentRoot "C:/xampp/htdocs/qly_hthao/qlyhoithao/public"
    <Directory "C:/xampp/htdocs/qly_hthao/qlyhoithao/public">
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

3. Mở file `C:\Windows\System32\drivers\etc\hosts` (as Administrator)

4. Thêm dòng:
```
127.0.0.1  huit-conferences.local
```

5. Restart Apache trong XAMPP

6. Truy cập: `http://huit-conferences.local`

**API Base URL:**
- Với public folder: `http://localhost/qly_hthao/qlyhoithao/public/api`
- Với virtual host: `http://huit-conferences.local/api`

### 2. Test tools
Sử dụng một trong các công cụ sau:
- **Postman** (Recommended)
- **Insomnia**
- **Thunder Client** (VS Code Extension)
- **cURL** (Command line)

---

## 🔥 TEST SCENARIOS

### Scenario 1: Đăng ký tài khoản mới

**Request:**
```http
POST http://huit-conferences.local/api/auth/register
Content-Type: application/json

{
  "email": "testuser@huit.edu.vn",
  "password": "password123",
  "password_confirmation": "password123",
  "full_name": "Nguyễn Test User",
  "is_student": false,
  "faculty_id": 1,
  "organization": "Trường Đại học Công nghệ TP.HCM"
}
```

**Expected Response:**
- Status: 201 Created
- Body chứa: user info + JWT token

**Save token** từ response để dùng cho các requests tiếp theo!

---

### Scenario 2: Đăng nhập với tài khoản có sẵn

**Request:**
```http
POST http://huit-conferences.local/api/auth/login
Content-Type: application/json

{
  "email": "admin@huit.edu.vn",
  "password": "admin123"
}
```

**Expected Response:**
- Status: 200 OK
- Body chứa: user info (với roles) + JWT token

---

### Scenario 3: Lấy thông tin profile

**Request:**
```http
GET http://huit-conferences.local/api/auth/profile
Authorization: Bearer YOUR_TOKEN_HERE
```

**Expected Response:**
- Status: 200 OK
- Body chứa: full user info + roles + khoa

---

### Scenario 4: Cập nhật profile

**Request:**
```http
PUT http://huit-conferences.local/api/auth/profile
Authorization: Bearer YOUR_TOKEN_HERE
Content-Type: application/json

{
  "full_name": "Nguyễn Test User (Updated)",
  "organization": "HUIT - Research Lab"
}
```

**Expected Response:**
- Status: 200 OK
- Body chứa: updated user info

---

### Scenario 5: Đổi mật khẩu

**Request:**
```http
POST http://huit-conferences.local/api/auth/change-password
Authorization: Bearer YOUR_TOKEN_HERE
Content-Type: application/json

{
  "current_password": "password123",
  "new_password": "newpassword456",
  "new_password_confirmation": "newpassword456"
}
```

**Expected Response:**
- Status: 200 OK
- Message: "Đổi mật khẩu thành công"

---

### Scenario 6: Đăng xuất

**Request:**
```http
POST http://huit-conferences.local/api/auth/logout
Authorization: Bearer YOUR_TOKEN_HERE
```

**Expected Response:**
- Status: 200 OK
- Message: "Đăng xuất thành công"

---

## 🐛 TEST ERROR CASES

### 1. Email đã tồn tại
```http
POST http://localhost:8000/api/auth/register
Content-Type: application/json

{
  "email": "admin@huit.edu.vn",
  "password": "password123",
  "password_confirmation": "password123",
  "full_name": "Test"
}
```
**Expected:** 422 Unprocessable Entity, "The email has already been taken"

---

### 2. Mật khẩu không khớp
```http
POST http://localhost:8000/api/auth/register
Content-Type: application/json

{
  "email": "new@huit.edu.vn",
  "password": "password123",
  "password_confirmation": "different",
  "full_name": "Test"
}
```
**Expected:** 422 Unprocessable Entity, validation errors

---

### 3. Email không đúng định dạng
```http
POST http://localhost:8000/api/auth/login
Content-Type: application/json

{
  "email": "invalidemail",
  "password": "password123"
}
```
**Expected:** 422 Unprocessable Entity

---

### 4. Sai mật khẩu
```http
POST http://localhost:8000/api/auth/login
Content-Type: application/json

{
  "email": "admin@huit.edu.vn",
  "password": "wrongpassword"
}
```
**Expected:** 401 Unauthorized, "Mật khẩu không đúng"

---

### 5. Token không hợp lệ
```http
GET http://localhost:8000/api/auth/profile
Authorization: Bearer invalid_token_here
```
**Expected:** 401 Unauthorized

---

### 6. Không có token
```http
GET http://localhost:8000/api/auth/profile
```
**Expected:** 401 Unauthorized

---

## 📦 POSTMAN COLLECTION

### Import vào Postman

1. Tạo New Collection tên "HUIT Conferences"
2. Tạo Environment với variables:
   - `base_url`: `http://huit-conferences.local/api`
   - `token`: (sẽ tự động set sau khi login)

3. Tạo các requests:

#### Folder: Authentication

**1. Register**
- Method: POST
- URL: `{{base_url}}/auth/register`
- Body: raw JSON (xem ở trên)

**2. Login**
- Method: POST
- URL: `{{base_url}}/auth/login`
- Body: raw JSON
- Tests (để tự động save token):
```javascript
if (pm.response.code === 200) {
    var jsonData = pm.response.json();
    pm.environment.set("token", jsonData.data.token);
}
```

**3. Get Profile**
- Method: GET
- URL: `{{base_url}}/auth/profile`
- Authorization: Bearer Token
- Token: `{{token}}`

**4. Update Profile**
- Method: PUT
- URL: `{{base_url}}/auth/profile`
- Authorization: Bearer Token
- Body: raw JSON

**5. Change Password**
- Method: POST
- URL: `{{base_url}}/auth/change-password`
- Authorization: Bearer Token
- Body: raw JSON

**6. Logout**
- Method: POST
- URL: `{{base_url}}/auth/logout`
- Authorization: Bearer Token

---

## ✅ CHECKLIST TEST

- [ ] Register thành công với email mới
- [ ] Register thất bại với email đã tồn tại
- [ ] Login thành công với credentials đúng
- [ ] Login thất bại với sai password
- [ ] Login thất bại với email không tồn tại
- [ ] Get profile thành công với valid token
- [ ] Get profile thất bại khi không có token
- [ ] Update profile thành công
- [ ] Change password thành công
- [ ] Change password thất bại khi current password sai
- [ ] Logout thành công
- [ ] Token không còn valid sau khi logout

---

## 🎓 TIPS

1. **Save token:** Sau khi login thành công, copy token và lưu để dùng cho các requests khác

2. **Postman Environment:** Sử dụng environment variables để quản lý token tự động

3. **Check Response:** Luôn kiểm tra:
   - HTTP Status Code
   - Response structure
   - Error messages

4. **Database Check:** Sau mỗi test, có thể check database để verify:
```sql
SELECT * FROM NguoiDung ORDER BY created_at DESC LIMIT 1;
SELECT * FROM VaiTroNguoiDung WHERE user_id = X;
```

---

## 📝 TEST RESULTS TEMPLATE

```
Date: __________
Tester: __________

| Test Case | Status | Notes |
|-----------|--------|-------|
| Register new user | ⬜ Pass ⬜ Fail | |
| Login admin | ⬜ Pass ⬜ Fail | |
| Login author | ⬜ Pass ⬜ Fail | |
| Get profile | ⬜ Pass ⬜ Fail | |
| Update profile | ⬜ Pass ⬜ Fail | |
| Change password | ⬜ Pass ⬜ Fail | |
| Logout | ⬜ Pass ⬜ Fail | |
| Invalid email error | ⬜ Pass ⬜ Fail | |
| Wrong password error | ⬜ Pass ⬜ Fail | |
| Invalid token error | ⬜ Pass ⬜ Fail | |
```

---

**Happy Testing! 🚀**
