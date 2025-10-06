# 🎓 HỆ THỐNG QUẢN LÝ HỘI THẢO KHOA HỌC - HUIT CONFERENCES

<img src="https://img.shields.io/badge/Laravel-10.x-red" /> <img src="https://img.shields.io/badge/PHP-8.1+-blue" /> <img src="https://img.shields.io/badge/MySQL-8.0-orange" /> <img src="https://img.shields.io/badge/Status-In%20Development-yellow" />

Hệ thống quản lý hội thảo khoa học toàn diện cho Trường Đại học Công nghệ TP.HCM (HUIT), hỗ trợ quy trình từ đăng ký, nộp bài, phản biện, đến xuất bản kỷ yếu.

## 🌟 Tính năng chính

### 👥 Quản lý Người dùng & Vai trò
- ✅ Đăng ký và đăng nhập (JWT Authentication)
- ✅ Phân quyền: Admin, Author, Reviewer, Chair, PC
- ✅ Quản lý profile và chuyên môn

### 🎪 Quản lý Hội thảo
- ✅ Yêu cầu tổ chức hội thảo (upload proposal PDF)
- ✅ Phê duyệt và tạo hội thảo (cấp Khoa/Trường)
- ✅ Quản lý tiểu ban (tracks) và deadlines
- ✅ Thống kê và báo cáo

### 📝 Nộp và Quản lý Bài báo
- ✅ Nộp bài với metadata và PDF
- ✅ Quản lý tác giả và contact author
- ✅ Theo dõi phiên bản bài báo
- ✅ Lịch sử trạng thái
- ✅ Yêu cầu chỉnh sửa (revision)
- ✅ Rút bài (withdrawal)

### 🔍 Hệ thống Phản biện
- ✅ **Bidding:** Reviewer tự chọn bài muốn phản biện
- ✅ **Auto-Assignment:** Thuật toán phân công thông minh
  - Tính điểm: 3×bidding + 2×expertise - load
  - Tự động loại bỏ COI
  - Tôn trọng max load/reviewer
- ✅ **COI Management:** Khai báo và xử lý xung đột lợi ích
- ✅ **Review Process:** Nộp đánh giá, điểm số, khuyến nghị
- ✅ **Decision:** Chair tổng hợp và quyết định cuối

### 📢 Tính năng bổ sung
- ✅ Thông báo hệ thống
- ✅ Nhắc deadline tự động
- ✅ Xuất kỷ yếu (proceedings)
- ✅ Email notifications

## 🗄️ Cơ sở dữ liệu

**23 bảng** được tổ chức thành các nhóm:

### Lookup Tables (7)
- TrangThaiBaiBao, GiaTriBidding, LoaiCOI, LoaiVaiTro
- CapHoiThao, TrangThaiPhanCong, LoaiKhuyenNghi

### Core Tables (16)
- **Users:** Khoa, NguoiDung, VaiTroNguoiDung
- **Conference:** YeuCauHoiThao, HoiThao, TieuBan, ThongBao
- **Papers:** BaiBao, PhienBanBaiBao, TacGiaBaiBao, LichSuTrangThai, YeuCauChinhSua, RutBaiBao
- **Review:** ChuyenMonReviewer, Bidding, COI, XuLyCOI, PhanCongPhanBien, PhanBien

Xem chi tiết: [database.md](database.md)

## 📁 Cấu trúc Project

```
qlyhoithao/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── AuthController.php
│   │   │   ├── ConferenceController.php
│   │   │   ├── PaperController.php
│   │   │   ├── ReviewController.php
│   │   │   └── ...
│   │   └── Middleware/
│   │       ├── JWTAuth.php
│   │       └── RoleMiddleware.php
│   ├── Models/
│   │   ├── NguoiDung.php
│   │   ├── HoiThao.php
│   │   ├── BaiBao.php
│   │   └── ...
│   └── Services/
│       ├── AssignmentService.php
│       └── EmailService.php
├── database/
│   ├── migrations/
│   └── seeders/
├── resources/
│   ├── views/
│   ├── js/
│   └── css/
├── routes/
│   ├── web.php
│   └── api.php
├── storage/
│   └── app/
│       ├── papers/
│       └── proposals/
├── database.md           # Database schema
├── TODO.md              # Task list
└── PROGRESS.md          # Progress tracking
```

## 🚀 Installation

### Requirements
- PHP >= 8.1
- MySQL >= 8.0
- Composer
- XAMPP/WAMP (hoặc web server khác)

### Setup

1. **Clone repository**
```bash
cd c:\xampp\htdocs
git clone <repository-url> qly_hthao
cd qly_hthao\qlyhoithao
```

2. **Install dependencies**
```bash
composer install
npm install
```

3. **Configure environment**
```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env`:
```
DB_DATABASE=quanly_hoithao
DB_USERNAME=root
DB_PASSWORD=
```

4. **Create database & run migrations**
```bash
php artisan migrate:fresh --seed
```

5. **Setup Virtual Host (khuyến nghị)**

Thêm vào `C:\xampp\apache\conf\extra\httpd-vhosts.conf`:
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

Thêm vào `C:\Windows\System32\drivers\etc\hosts`:
```
127.0.0.1  huit-conferences.local
```

6. **Restart Apache trong XAMPP**

Visit: `http://huit-conferences.local`

**Hoặc truy cập trực tiếp:**
`http://localhost/qly_hthao/qlyhoithao/public`

## 👤 Tài khoản Test

| Role | Email | Password |
|------|-------|----------|
| Admin | admin@huit.edu.vn | admin123 |
| Author | author2@huit.edu.vn | password123 |
| Reviewer | reviewer6@huit.edu.vn | password123 |

## 📖 API Documentation

### Authentication
```
POST   /api/register       - Đăng ký tài khoản
POST   /api/login          - Đăng nhập
GET    /api/profile        - Lấy profile
PUT    /api/profile        - Cập nhật profile
```

### Conferences
```
GET    /api/conferences              - Danh sách hội thảo
GET    /api/conferences/{id}         - Chi tiết hội thảo
POST   /api/conference-requests      - Yêu cầu tổ chức
```

### Papers
```
POST   /api/conferences/{id}/papers  - Nộp bài
GET    /api/papers                   - Bài của tôi
GET    /api/papers/{id}              - Chi tiết bài
```

### Reviews
```
GET    /api/my-reviews               - Assignments của tôi
POST   /api/assignments/{id}/review  - Nộp review
POST   /api/papers/{id}/bidding      - Bidding
```

[Full API Documentation →](API_DOCS.md)

## 🎨 Frontend

### Landing Page
- Hero section với search
- Statistics cards (8 hội thảo, 326 bài, 142 reviewers, 987 tác giả)
- Conference list
- Responsive design

### Dashboards
- **Author Dashboard:** My papers, statistics, deadlines
- **Reviewer Dashboard:** Assignments, bidding, expertise
- **Chair Dashboard:** Paper management, reviews, decisions
- **Admin Panel:** Users, conferences, system settings

## 🧪 Testing

```bash
# Run all tests
php artisan test

# Run specific test
php artisan test --filter=AuthTest
```

## 📚 Development Roadmap

- [x] **Phase 1:** Database & Setup (100%)
- [ ] **Phase 2:** Authentication (0%)
- [ ] **Phase 3:** Conference Management (0%)
- [ ] **Phase 4:** Paper Submission (0%)
- [ ] **Phase 5:** Review System (0%)
- [ ] **Phase 6:** Frontend (0%)
- [ ] **Phase 7:** Testing & Deployment (0%)

Xem chi tiết: [TODO.md](TODO.md) | [PROGRESS.md](PROGRESS.md)

## 🤝 Contributing

Dự án đang trong giai đoạn phát triển. Contributions are welcome!

## 📄 License

[MIT License](LICENSE)

## 📞 Contact

**Trường Đại học Công nghệ TP.HCM (HUIT)**  
- 📧 Email: kvnc@huit.edu.vn
- 📞 Điện thoại: (028) 38xx xxxx
- 📍 Địa chỉ: 140 Lê Trọng Tấn, TP.HCM

---

**© 2025 HUIT - All rights reserved**
