# 🔧 HƯỚNG DẪN SETUP XAMPP CHO HUIT CONFERENCES

## 📋 Yêu cầu
- XAMPP 8.1+ (với PHP 8.1+, Apache, MySQL)
- Windows 10/11

---

## 🚀 BƯỚC 1: CÀI ĐẶT XAMPP

1. Download XAMPP từ: https://www.apachefriends.org/
2. Cài đặt vào `C:\xampp`
3. Chọn components: Apache, MySQL, PHP, phpMyAdmin

---

## 🔧 BƯỚC 2: CẤU HÌNH VIRTUAL HOST (Khuyến nghị)

### 2.1. Cấu hình Apache Virtual Hosts

**Mở file:** `C:\xampp\apache\conf\extra\httpd-vhosts.conf`

**Thêm vào cuối file:**
```apache
# HUIT Conferences Virtual Host
<VirtualHost *:80>
    ServerName huit-conferences.local
    ServerAlias www.huit-conferences.local
    DocumentRoot "C:/xampp/htdocs/qly_hthao/qlyhoithao/public"
    
    <Directory "C:/xampp/htdocs/qly_hthao/qlyhoithao/public">
        Options Indexes FollowSymLinks MultiViews
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog "logs/huit-conferences-error.log"
    CustomLog "logs/huit-conferences-access.log" combined
</VirtualHost>
```

### 2.2. Cấu hình Windows Hosts File

**Mở Notepad as Administrator**

**Mở file:** `C:\Windows\System32\drivers\etc\hosts`

**Thêm dòng:**
```
127.0.0.1       huit-conferences.local
127.0.0.1       www.huit-conferences.local
```

**Save file**

### 2.3. Enable mod_rewrite trong Apache

**Mở file:** `C:\xampp\apache\conf\httpd.conf`

**Tìm và uncomment (bỏ dấu #) dòng:**
```apache
LoadModule rewrite_module modules/mod_rewrite.so
```

---

## 🗄️ BƯỚC 3: TẠO DATABASE

### Option 1: Qua phpMyAdmin
1. Mở: `http://localhost/phpmyadmin`
2. Click "New" ở sidebar trái
3. Database name: `quanly_hoithao`
4. Collation: `utf8mb4_unicode_ci`
5. Click "Create"

### Option 2: Qua Command Line
```sql
CREATE DATABASE IF NOT EXISTS quanly_hoithao
  DEFAULT CHARACTER SET utf8mb4
  DEFAULT COLLATE utf8mb4_unicode_ci;
```

---

## 📂 BƯỚC 4: SETUP PROJECT

### 4.1. Verify project location
```
C:\xampp\htdocs\qly_hthao\qlyhoithao\
```

### 4.2. Check .htaccess file
File `C:\xampp\htdocs\qly_hthao\qlyhoithao\public\.htaccess` phải tồn tại với nội dung:

```apache
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    # Handle Authorization Header
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    # Redirect Trailing Slashes If Not A Folder...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]

    # Send Requests To Front Controller...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>
```

### 4.3. Set folder permissions
Đảm bảo các folder sau có quyền write:
- `storage/`
- `bootstrap/cache/`

---

## ▶️ BƯỚC 5: KHỞI ĐỘNG

### 5.1. Start Services
1. Mở XAMPP Control Panel
2. Click "Start" cho **Apache**
3. Click "Start" cho **MySQL**

### 5.2. Verify Services
- Apache: Port 80 (màu xanh)
- MySQL: Port 3306 (màu xanh)

### 5.3. Test Access
Mở trình duyệt và truy cập:

**Với Virtual Host:**
```
http://huit-conferences.local
```

**Không có Virtual Host:**
```
http://localhost/qly_hthao/qlyhoithao/public
```

---

## ✅ BƯỚC 6: VERIFY SETUP

### 6.1. Test Laravel
Truy cập: `http://huit-conferences.local`

Nếu thấy trang Laravel hoặc trang chủ → ✅ Success!

### 6.2. Test API
```
GET http://huit-conferences.local/api/health
```

Response expected:
```json
{
    "status": "ok",
    "message": "HUIT Conference API is running",
    "timestamp": "2025-10-04 14:30:00"
}
```

### 6.3. Test Database Connection
Chạy trong terminal:
```bash
cd C:\xampp\htdocs\qly_hthao\qlyhoithao
php artisan migrate:status
```

---

## 🐛 TROUBLESHOOTING

### Problem 1: Port 80 đã bị sử dụng
**Giải pháp:**
1. Tắt Skype hoặc apps khác đang dùng port 80
2. Hoặc đổi Apache port:
   - Edit `C:\xampp\apache\conf\httpd.conf`
   - Tìm: `Listen 80` → Đổi thành `Listen 8080`
   - Truy cập: `http://huit-conferences.local:8080`

### Problem 2: MySQL không start
**Giải pháp:**
1. Check port 3306 có bị chiếm không
2. Restart XAMPP as Administrator

### Problem 3: 404 Not Found
**Giải pháp:**
1. Check mod_rewrite đã enable chưa
2. Check `.htaccess` file tồn tại trong `public/`
3. Check `AllowOverride All` trong vhosts config

### Problem 4: Virtual Host không hoạt động
**Giải pháp:**
1. Restart Apache
2. Clear browser cache
3. Try: `http://localhost/qly_hthao/qlyhoithao/public`

### Problem 5: API trả về 500 Error
**Giải pháp:**
1. Check `storage/` và `bootstrap/cache/` có quyền write
2. Run: `php artisan config:clear`
3. Run: `php artisan cache:clear`
4. Check Laravel logs: `storage/logs/laravel.log`

### Problem 6: Database connection error
**Giải pháp:**
1. Verify MySQL đang chạy
2. Check `.env` file:
   ```
   DB_DATABASE=quanly_hoithao
   DB_USERNAME=root
   DB_PASSWORD=
   ```
3. Test connection: `php artisan migrate:status`

---

## 🎯 BEST PRACTICES

### 1. Development vs Production
**Development (current):**
```
APP_ENV=local
APP_DEBUG=true
```

**Production (future):**
```
APP_ENV=production
APP_DEBUG=false
```

### 2. Regular Maintenance
```bash
# Clear cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear

# Optimize
php artisan config:cache
php artisan route:cache
```

### 3. Backup Database
```bash
# Export
C:\xampp\mysql\bin\mysqldump -u root quanly_hoithao > backup.sql

# Import
C:\xampp\mysql\bin\mysql -u root quanly_hoithao < backup.sql
```

---

## 📝 CHECKLIST HOÀN THÀNH

- [ ] XAMPP installed
- [ ] Apache started (port 80 màu xanh)
- [ ] MySQL started (port 3306 màu xanh)
- [ ] Virtual host configured
- [ ] Hosts file updated
- [ ] mod_rewrite enabled
- [ ] Database created: `quanly_hoithao`
- [ ] .htaccess exists in public/
- [ ] Permissions set for storage/ and bootstrap/cache/
- [ ] Can access: `http://huit-conferences.local`
- [ ] API health check working
- [ ] Database migrations successful

---

## 🎓 USEFUL COMMANDS

### Apache Commands
```bash
# Start Apache
C:\xampp\apache_start.bat

# Stop Apache
C:\xampp\apache_stop.bat

# Check config
C:\xampp\apache\bin\httpd.exe -t
```

### MySQL Commands
```bash
# Access MySQL CLI
C:\xampp\mysql\bin\mysql.exe -u root

# Show databases
SHOW DATABASES;

# Use database
USE quanly_hoithao;

# Show tables
SHOW TABLES;
```

### Laravel Commands
```bash
# Check routes
php artisan route:list

# Check config
php artisan config:show

# Check database
php artisan migrate:status

# Seed data
php artisan db:seed
```

---

## 📞 SUPPORT

### Logs Location
- Apache Error Log: `C:\xampp\apache\logs\error.log`
- Laravel Log: `C:\xampp\htdocs\qly_hthao\qlyhoithao\storage\logs\laravel.log`
- MySQL Log: `C:\xampp\mysql\data\mysql_error.log`

### Configuration Files
- Apache Config: `C:\xampp\apache\conf\httpd.conf`
- Virtual Hosts: `C:\xampp\apache\conf\extra\httpd-vhosts.conf`
- PHP Config: `C:\xampp\php\php.ini`
- MySQL Config: `C:\xampp\mysql\bin\my.ini`

---

**✅ Setup Complete! You're ready to develop!** 🚀
