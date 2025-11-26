# Copilot Instructions for qly_hthao Laravel Project

## Project Overview
This is a **Laravel 9 application** running on XAMPP (Windows development environment). The project name suggests it's a Vietnamese management system ("qly" = quản lý = management, "hthao" likely refers to the domain/organization).

## Architecture & Key Components

### Development Environment
- **XAMPP Setup**: Project runs at `c:\xampp\htdocs\qly_hthao\qlyhoithao\`
- **Database**: MySQL via XAMPP (`DB_HOST=127.0.0.1`, `DB_DATABASE=quanly_hoithao`)
- **Frontend**: Vite + Laravel Mix for asset compilation
- **Authentication**: Laravel Sanctum for API tokens

### Essential Commands (Windows PowerShell)
```powershell
# Start development server
php artisan serve

# Asset compilation
npm run dev          # Development with hot reload
npm run build        # Production build

# Database operations
php artisan migrate
php artisan db:seed
php artisan migrate:fresh --seed

# Clear caches (common debug step)
php artisan config:clear; php artisan cache:clear; php artisan view:clear
```

## Project Structure Patterns

### Controllers
- Base controller: `app/Http/Controllers/Controller.php` uses standard Laravel traits
- Follow Laravel resource controller conventions: `index`, `create`, `store`, `show`, `edit`, `update`, `destroy`
- API routes go in `routes/api.php` with `auth:sanctum` middleware for protected endpoints

### Models & Database
- Models in `app/Models/` namespace with Eloquent conventions
- User model uses `HasApiTokens`, `HasFactory`, `Notifiable` traits
- Migrations follow Laravel timestamp naming: `YYYY_MM_DD_HHMMSS_description`
- Use mass assignment protection (`$fillable`) and hidden attributes (`$hidden`)

### Frontend Assets
- Entry points: `resources/css/app.css`, `resources/js/app.js`  
- Blade templates in `resources/views/`
- Vite configuration supports hot reload and asset versioning
- Use `@vite(['resources/css/app.css', 'resources/js/app.js'])` in Blade templates

### Testing
- Feature tests in `tests/Feature/` for HTTP endpoints
- Unit tests in `tests/Unit/` for isolated logic
- Use `RefreshDatabase` trait for database tests
- Run tests: `php artisan test` or `vendor/bin/phpunit`

## Configuration Specifics

### Environment Variables
- Default MySQL config for XAMPP: host `127.0.0.1`, port `3306`, no password
- Mail configured for Mailpit (`MAIL_HOST=mailpit`, `MAIL_PORT=1025`)
- File-based sessions and cache (suitable for single-server XAMPP setup)
- Debug mode enabled (`APP_DEBUG=true`) for development

### Middleware & Security
- CSRF protection enabled for web routes
- Sanctum for API authentication
- Rate limiting configured in `app/Http/Kernel.php`
- CORS configured in `config/cors.php`

## Vietnamese Context Considerations
Given the project name pattern, expect:
- Vietnamese language content in views and validation messages
- Date/time formatting for Vietnamese locale
- Potential integration with Vietnamese business processes
- Consider UTF-8 encoding for Vietnamese characters in database and responses

## Development Workflow
1. **Database First**: Create migrations before models
2. **API Routes**: Use `auth:sanctum` middleware for protected endpoints
3. **Validation**: Use Form Requests for complex validation logic
4. **Resources**: Use API Resources for consistent JSON responses
5. **Testing**: Write feature tests for all controller actions

## Common Issues & Solutions
- **XAMPP MySQL**: Ensure MySQL service is running in XAMPP Control Panel
- **Permissions**: Check `storage/` and `bootstrap/cache/` are writable
- **Composer**: Use `composer install --no-dev` for production
- **Assets**: Run `npm install` then `npm run build` for deployment
- **Environment**: Copy `.env.example` to `.env` and generate `APP_KEY` with `php artisan key:generate`

## ⚙️ Nguyên tắc chung (Cấp cao)

### 🚫 QUY TẮC VÀNG: KHÔNG BAO GIỜ ĐOÁN DATABASE SCHEMA

**LUÔN LUÔN** kiểm tra database thực tế trước khi viết bất kỳ SQL/Query nào:

```bash
# BẮT BUỘC chạy trước khi code
php artisan db:table [table_name]    # Kiểm tra cấu trúc bảng
php artisan db:show                  # Xem tất cả bảng trong DB
```

### 📋 QUY TRÌNH KIỂM TRA DATABASE BẮT BUỘC

1. **TRƯỚC KHI VIẾT Query/SQL:**
   ```bash
   # Bước 1: Kiểm tra bảng tồn tại
   php artisan db:show
   
   # Bước 2: Kiểm tra cấu trúc cột thực tế  
   php artisan db:table baibao
   php artisan db:table nguoidung
   php artisan db:table [table_name]
   
   # Bước 3: Kiểm tra data mẫu (nếu cần)
   php artisan tinker --execute="DB::table('table_name')->limit(3)->get()"
   ```

2. **CÁC LỖI PHỔ BIẾN CẦN TRÁNH:**
   - ❌ `tb.track_name` (thực tế: `tb.title`)
   - ❌ `phancongphanbien` (bảng rỗng) → ✅ `reviewer_assignments` (có data)
   - ❌ `assignment_status` → ✅ `status` 
   - ❌ `pc.status_code` → ✅ `ra.status`
   - ❌ Đoán tên foreign key, join condition

3. **WORKFLOW BẮT BUỘC:**
   ```
   Yêu cầu → Kiểm tra DB → Viết Query → Test → Deploy
            ↑ KHÔNG ĐƯỢC BỎ QUA BƯỚC NÀY
   ```

### 🔍 CHECKLIST TRƯỚC KHI CODE

**Database Verification:**
- [ ] Đã chạy `php artisan db:table [table]` cho TẤT CẢ bảng liên quan
- [ ] Đã xác minh tên cột CHÍNH XÁC (không đoán)
- [ ] Đã kiểm tra foreign key relationships
- [ ] Đã xác minh bảng nào có data, bảng nào rỗng

**Query Construction:**  
- [ ] Sử dụng đúng tên bảng từ database thực tế
- [ ] Sử dụng đúng tên cột từ schema check
- [ ] Join đúng bảng có data (tránh join bảng rỗng)
- [ ] Test query trước khi integrate vào controller

**Error Prevention:**
- [ ] Không assume tên cột dựa trên convention
- [ ] Không copy-paste query từ bảng khác mà không verify
- [ ] Luôn check schema khi gặp "Column not found" error

### 📊 DATABASE MAPPING (Cập nhật định kỳ)

**Bảng có Data thực tế:**
- `reviewer_assignments` ✅ (10+ records) - USE THIS for reviewer queries
- `baibao` ✅ (5 papers)
- `nguoidung` ✅ (27 users) 
- `vaitronguoidung` ✅ (role assignments)
- `hoithao` ✅ (conferences)

**Bảng rỗng/Deprecated:**
- `phancongphanbien` ❌ (0 records) - DON'T USE
- `assignment_notifications` ⚠️ (limited data)

**Tên cột thường gặp:**
- `tieuban.title` (NOT track_name)
- `reviewer_assignments.status` (NOT status_code)  
- `reviewer_assignments.user_id` (NOT reviewer_id)

### 🛠️ DEBUGGING COMMANDS

```bash
# Khi gặp lỗi SQL
php artisan db:table [table_name]  # Check column names
php tinker                         # Test query interactively
DB::table('table')->get()          # Check data existence

# Verify relationships
php artisan schema:dump            # Export full schema
```

### 🎯 NGUYÊN TẮC KHÁC

4. **Ưu tiên thứ tự kiểm tra:**  
   **Database Schema thực tế > Artisan commands > Migrations > Code assumptions**

5. Nếu code tham chiếu một cột không tồn tại:  
   → **BƯỚC 1:** Chạy `php artisan db:table [table]` để xem cột thật
   → **BƯỚC 2:** Sửa code dùng đúng tên cột  
   → **BƯỚC 3:** (Tùy chọn) Tạo migration nếu thực sự cần cột mới

6. Khi sửa code:  
   - Luôn hiển thị **diff/patch cụ thể** (chỉ sửa phần cần thiết)  
   - Giải thích ngắn gọn **lý do và tác động**
   - **Test query trước khi commit**

7. Giao diện UI/UX:  
   - Bắt buộc dùng svg icons hiện đại, không được phép dùng emoji

