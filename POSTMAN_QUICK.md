# ⚡ POSTMAN Quick Reference

## 🚀 Quick Start (3 bước)

### 1. Import Collection
```
Postman → Import → Upload Files → Chọn HUIT-Conference-APIs.postman_collection.json
```

### 2. Login để lấy Token
```
POST {{base_url}}/auth/login
Body: {"email": "admin@huit.edu.vn", "password": "admin123"}
```
✅ Token tự động lưu vào `{{admin_token}}` và `{{token}}`

### 3. Test API
```
GET {{base_url}}/conferences
Authorization: Bearer {{token}}
```

---

## 📋 31 APIs Checklist

### 🔐 Authentication (9 APIs)
| # | Method | Endpoint | Auth | Mô tả |
|---|--------|----------|------|-------|
| 1.1 | GET | `/health` | ❌ | Kiểm tra API hoạt động |
| 1.2 | POST | `/auth/login` | ❌ | Đăng nhập (auto save token) |
| 1.3 | POST | `/auth/login` | ❌ | Đăng nhập Chair (auto save) |
| 1.4 | POST | `/auth/register` | ❌ | Đăng ký tài khoản mới |
| 1.5 | GET | `/auth/profile` | ✅ | Xem thông tin cá nhân |
| 1.6 | PUT | `/auth/profile` | ✅ | Cập nhật thông tin |
| 1.7 | POST | `/auth/change-password` | ✅ | Đổi mật khẩu |
| 1.8 | POST | `/auth/refresh` | ✅ | Làm mới token |
| 1.9 | POST | `/auth/logout` | ✅ | Đăng xuất |

### 🏛️ Conferences (8 APIs)
| # | Method | Endpoint | Auth | Mô tả |
|---|--------|----------|------|-------|
| 2.1 | GET | `/conferences` | ❌ | Danh sách hội thảo |
| 2.2 | GET | `/conferences?status=OPEN` | ❌ | Lọc theo điều kiện |
| 2.3 | GET | `/conferences/{id}` | ❌ | Chi tiết hội thảo |
| 2.4 | GET | `/conferences/{id}/statistics` | ❌ | Thống kê hội thảo |
| 2.5 | POST | `/conferences` | 👤 Admin | Tạo hội thảo |
| 2.6 | PUT | `/conferences/{id}` | 👤 Admin | Cập nhật hội thảo |
| 2.7 | DELETE | `/conferences/{id}` | 👤 Admin | Xóa hội thảo |
| 2.8 | GET | `/my-conferences` | ✅ | Hội thảo của tôi |

### 📊 Tracks (7 APIs)
| # | Method | Endpoint | Auth | Mô tả |
|---|--------|----------|------|-------|
| 3.1 | GET | `/conferences/{id}/tracks` | ✅ | Danh sách tracks |
| 3.2 | POST | `/conferences/{id}/tracks` | 👤 Admin | Tạo track |
| 3.3 | GET | `/tracks/{id}` | ✅ | Chi tiết track |
| 3.4 | PUT | `/tracks/{id}` | 👤 Admin/Chair | Cập nhật track |
| 3.5 | DELETE | `/tracks/{id}` | 👤 Admin | Xóa track |
| 3.6 | GET | `/tracks/{id}/papers` | ✅ | Papers trong track |
| 3.7 | GET | `/my-tracks` | 👨‍💼 Chair | Tracks tôi quản lý |

### 📝 Conference Requests (7 APIs)
| # | Method | Endpoint | Auth | Mô tả |
|---|--------|----------|------|-------|
| 4.1 | GET | `/conference-requests` | ✅ | Danh sách requests |
| 4.2 | POST | `/conference-requests` | 👨‍💼 Chair | Submit đề xuất |
| 4.3 | GET | `/conference-requests/{id}` | ✅ | Chi tiết request |
| 4.4 | POST | `/conference-requests/{id}/approve` | 👤 Admin | Duyệt request |
| 4.5 | POST | `/conference-requests/{id}/reject` | 👤 Admin | Từ chối request |
| 4.6 | POST | `/conference-requests/{id}/cancel` | 👨‍💼 Chair | Hủy request |
| 4.7 | GET | `/conference-requests/statistics` | 👤 Admin | Thống kê requests |

**Legend:**
- ❌ = Public (không cần token)
- ✅ = Protected (cần token)
- 👤 Admin = Chỉ Admin
- 👨‍💼 Chair = Chỉ Chair

---

## 🔑 Test Accounts

| Role | Email | Password | Token Variable |
|------|-------|----------|----------------|
| Admin | admin@huit.edu.vn | admin123 | `{{admin_token}}` |
| Chair | chair1@huit.edu.vn | password123 | `{{chair_token}}` |
| Author | author2@huit.edu.vn | password123 | - |
| Reviewer | reviewer6@huit.edu.vn | password123 | - |

---

## 🎯 Test Workflows

### Workflow 1: Admin tạo hội thảo
```
1. Login Admin → POST /auth/login
2. Tạo hội thảo → POST /conferences
3. Tạo track → POST /conferences/1/tracks
4. Xem thống kê → GET /conferences/1/statistics
```

### Workflow 2: Chair submit request
```
1. Login Chair → POST /auth/login
2. Submit request → POST /conference-requests
3. Admin xem request → GET /conference-requests?status=PENDING
4. Admin duyệt → POST /conference-requests/1/approve
```

### Workflow 3: Chair quản lý track
```
1. Login Chair → POST /auth/login
2. Xem my tracks → GET /my-tracks
3. Xem papers → GET /tracks/1/papers
4. Update track → PUT /tracks/1
```

---

## 🔧 Variables

### Collection Variables
```
base_url = http://localhost/qly_hthao/qlyhoithao/public/api
token = (auto-saved when login)
admin_token = (auto-saved when admin login)
chair_token = (auto-saved when chair login)
```

### Sử dụng trong request
```
URL: {{base_url}}/conferences
Authorization: Bearer {{token}}
Authorization: Bearer {{admin_token}}
Authorization: Bearer {{chair_token}}
```

---

## 🐛 Common Errors

| Error | Status | Nguyên nhân | Giải pháp |
|-------|--------|-------------|-----------|
| Unauthenticated | 401 | Token chưa set hoặc hết hạn | Login lại để lấy token mới |
| Unauthorized | 403 | Không có quyền | Dùng đúng token role (admin/chair) |
| Not Found | 404 | ID không tồn tại | Kiểm tra ID trong URL |
| Validation Error | 422 | Dữ liệu input sai | Xem chi tiết lỗi trong response |
| Server Error | 500 | Lỗi server | Kiểm tra logs Laravel |

---

## 💡 Pro Tips

### 1. Auto Token Management
Collection có **Test Scripts** tự động lưu token:
- Login Admin → auto save vào `admin_token` + `token`
- Login Chair → auto save vào `chair_token`

### 2. Xem Token đã lưu
```
Click Collection → Variables tab
Hoặc: Hover vào {{token}} trong request
```

### 3. Console Logging
```
View → Show Postman Console (Alt+Ctrl+C)
Xem log: "Admin token saved: eyJ0eX..."
```

### 4. Collection Runner
```
Click "Run" → Chọn folder → Run
Tự động test tất cả APIs trong folder
```

### 5. Save Response Examples
```
Send request → Click "Save Response" → "Save as example"
Example sẽ hiện khi hover vào request name
```

### 6. Query Parameters
```
GET /conferences?status=OPEN&year=2025&per_page=10
Trong Postman: Params tab → Add key-value pairs
```

### 7. Request Body Templates
Collection đã có sẵn body templates cho POST/PUT:
- Chỉ cần sửa giá trị
- Không cần viết lại JSON structure

---

## 📊 Test Progress

Track tiến độ test của bạn:

```
✅ Authentication: ___ / 9
✅ Conferences: ___ / 8
✅ Tracks: ___ / 7
✅ Conference Requests: ___ / 7

Total: ___ / 31 APIs
```

---

## 🔗 Quick Links

- **Base URL**: http://localhost/qly_hthao/qlyhoithao/public/api
- **Logs**: `C:\xampp\htdocs\qly_hthao\qlyhoithao\storage\logs\laravel.log`
- **Collection JSON**: `HUIT-Conference-APIs.postman_collection.json`
- **Detailed Guide**: `POSTMAN_GUIDE.md`

---

## 📞 Need Help?

### Kiểm tra XAMPP
```powershell
# Apache đang chạy?
curl http://localhost

# MySQL đang chạy?
mysql -u root -p
```

### Kiểm tra API
```powershell
curl http://localhost/qly_hthao/qlyhoithao/public/api/health
```

### Xem Laravel Logs
```powershell
Get-Content C:\xampp\htdocs\qly_hthao\qlyhoithao\storage\logs\laravel.log -Tail 50
```

---

**Ready to test! 🚀**
