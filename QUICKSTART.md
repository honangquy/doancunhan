# ⚡ QUICK START GUIDE - HUIT CONFERENCES

## 🎯 Bắt đầu nhanh trong 5 phút!

### Bước 1: Start XAMPP (30 giây)
1. Mở **XAMPP Control Panel**
2. Click **Start** cho **Apache**
3. Click **Start** cho **MySQL**
4. Đợi đến khi cả 2 đều màu xanh ✅

### Bước 2: Tạo Virtual Host (2 phút)

**Thêm vào:** `C:\xampp\apache\conf\extra\httpd-vhosts.conf`
```apache
<VirtualHost *:80>
    ServerName huit-conferences.local
    DocumentRoot "C:/xampp/htdocs/qly_hthao/qlyhoithao/public"
    <Directory "C:/xampp/htdocs/qly_hthao/qlyhoithao/public">
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

**Thêm vào:** `C:\Windows\System32\drivers\etc\hosts` (as Admin)
```
127.0.0.1  huit-conferences.local
```

**Restart Apache** trong XAMPP

### Bước 3: Test (30 giây)

**Mở browser:**
```
http://huit-conferences.local
```

**Test API:**
```
http://huit-conferences.local/api/health
```

Nếu thấy response JSON → ✅ **THÀNH CÔNG!**

---

## 🧪 Test Login ngay

### 1. Mở Postman

### 2. Tạo request
```
POST http://huit-conferences.local/api/auth/login
Content-Type: application/json
```

Body:
```json
{
  "email": "admin@huit.edu.vn",
  "password": "admin123"
}
```

### 3. Send → Nhận token ✅

---

## 📱 Các tài khoản test sẵn có

| Role | Email | Password |
|------|-------|----------|
| **Admin** | admin@huit.edu.vn | admin123 |
| **Author** | author2@huit.edu.vn | password123 |
| **Author** | author3@huit.edu.vn | password123 |
| **Reviewer** | reviewer6@huit.edu.vn | password123 |
| **Reviewer** | reviewer7@huit.edu.vn | password123 |

---

## 🚀 Các APIs sẵn sàng

✅ `POST /api/auth/register` - Đăng ký  
✅ `POST /api/auth/login` - Đăng nhập  
✅ `GET /api/auth/profile` - Xem profile  
✅ `PUT /api/auth/profile` - Cập nhật  
✅ `POST /api/auth/change-password` - Đổi pass  
✅ `POST /api/auth/logout` - Đăng xuất  
✅ `POST /api/auth/refresh` - Refresh token  

**Chi tiết:** [API_DOCS.md](API_DOCS.md)

---

## 📚 Tài liệu đầy đủ

| File | Nội dung |
|------|----------|
| **XAMPP_SETUP.md** | Hướng dẫn setup chi tiết |
| **TESTING_GUIDE.md** | Hướng dẫn test APIs |
| **API_DOCS.md** | API documentation |
| **TODO.md** | Task list |
| **PROGRESS.md** | Tiến độ dự án |

---

## ❓ Gặp vấn đề?

### Apache không start?
→ Check port 80 bị chiếm bởi app khác (Skype, IIS...)

### 404 Not Found?
→ Check `.htaccess` trong `public/` folder

### Database connection error?
→ Check MySQL đã start và `.env` đã đúng

### API 500 Error?
→ Run: `php artisan config:clear`

**Chi tiết troubleshooting:** [XAMPP_SETUP.md#troubleshooting](XAMPP_SETUP.md#troubleshooting)

---

## 🎉 Xong! Bắt đầu code thôi!

**Next steps:**
1. Test tất cả 7 APIs
2. Đọc [TODO.md](TODO.md) để biết task tiếp theo
3. Check [PROGRESS.md](PROGRESS.md) để track tiến độ

**Happy Coding! 💻**
