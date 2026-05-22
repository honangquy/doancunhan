# HUIT Conferences (Hệ thống Quản lý Hội thảo)

![Laravel](https://img.shields.io/badge/Laravel-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-4479A1?style=for-the-badge&logo=mysql&logoColor=white)
![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white)
![Alpine.js](https://img.shields.io/badge/Alpine.js-8BC0D0?style=for-the-badge&logo=alpine.js&logoColor=white)

> Hệ thống quản lý hội thảo toàn diện dành cho Đại học Công thương TP.HCM (HUIT), hỗ trợ tổ chức, nộp bài, đánh giá (review) và quản lý người dùng theo các vai trò khác nhau (Admin, Chair, Reviewer, Author).

## Table of Contents

- [Features](#features)
- [Tech Stack](#tech-stack)
- [Prerequisites](#prerequisites)
- [Getting Started](#getting-started)
- [Environment Variables](#environment-variables)
- [Project Structure](#project-structure)
- [Screenshots](#screenshots)

---

## Features

- **Quản lý phân quyền đa vai trò:** Hỗ trợ các vai trò riêng biệt bao gồm Admin, Chủ tọa (Chair), Người phản biện (Reviewer), và Tác giả (Author).
- **Quy trình nộp & duyệt bài:** Quản lý vòng đời của bài báo khoa học từ lúc tác giả nộp bài, phân công phản biện, cho đến lúc duyệt đăng.
- **Quản lý Hội thảo (Conferences):** Tạo, chỉnh sửa và quản lý các hội thảo, yêu cầu mở hội thảo.
- **Hệ thống Tin tức (News):** Cập nhật, đăng tin tức liên quan đến các hội thảo khoa học.
- **Dashboard & Báo cáo:** Cung cấp biểu đồ thống kê trực quan với Chart.js về số lượng bài báo, trạng thái, người dùng.
- **API & Xác thực:** Cung cấp hệ thống RESTful API với xác thực bảo mật thông qua JWT Auth và Laravel Sanctum. Document API với L5-Swagger.

## Tech Stack

- **Frontend:** Laravel Blade, Tailwind CSS, Alpine.js, Chart.js, Vite
- **Backend:** PHP 8.0+, Laravel Framework 9.x
- **Database:** MySQL
- **Authentication:** Laravel Sanctum, JWT Auth (tymon/jwt-auth)
- **API Documentation:** L5-Swagger

## Prerequisites

Trước khi bắt đầu, hãy đảm bảo bạn đã cài đặt sẵn các công cụ sau trên máy:

- PHP >= 8.0.2
- Composer
- Node.js & npm (hoặc yarn)
- MySQL Server (XAMPP/MAMP/LAMP hoặc Docker)
- Git

## Getting Started

Làm theo các bước dưới đây để thiết lập và khởi chạy dự án trên môi trường local.

1. **Clone repository:**
   ```bash
   git clone https://github.com/your-username/qlyhoithao.git
   cd qlyhoithao
   ```

2. **Cài đặt thư viện Backend (PHP/Laravel):**
   ```bash
   composer install
   ```

3. **Cài đặt thư viện Frontend (Node.js):**
   ```bash
   npm install
   ```

4. **Thiết lập biến môi trường:**
   Tạo file `.env` từ file `.env.example`.
   ```bash
   cp .env.example .env
   ```
   *Lưu ý: Mở file `.env` và cấu hình kết nối database `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` tương ứng với máy của bạn.*

5. **Tạo Application Key và JWT Secret:**
   ```bash
   php artisan key:generate
   php artisan jwt:secret
   ```

6. **Chạy Migration & Seeder (Tạo cấu trúc CSDL và dữ liệu mẫu):**
   ```bash
   php artisan migrate --seed
   ```

7. **Khởi chạy Server:**
   Mở 2 terminal để chạy backend và build frontend:
   
   *Terminal 1 (Backend):*
   ```bash
   php artisan serve
   ```
   
   *Terminal 2 (Frontend/Vite):*
   ```bash
   npm run dev
   ```
   
   Dự án sẽ có thể truy cập tại: `http://localhost:8000` (hoặc domain ảo nếu dùng XAMPP/Valet).

## Environment Variables

Dưới đây là một số biến môi trường quan trọng cần chú ý trong file `.env`:

| Biến môi trường | Mô tả | Yêu cầu |
| :--- | :--- | :---: |
| `APP_NAME` | Tên của ứng dụng (VD: HUIT Conferences) | Có |
| `APP_URL` | URL gốc của dự án (VD: http://localhost/qly_hthao/qlyhoithao/public) | Có |
| `DB_CONNECTION` | Loại cơ sở dữ liệu (VD: mysql) | Có |
| `DB_DATABASE` | Tên database tạo trong MySQL (VD: quanly_hoithao) | Có |
| `DB_USERNAME` | Username đăng nhập MySQL | Có |
| `DB_PASSWORD` | Mật khẩu đăng nhập MySQL | Có |
| `JWT_SECRET` | Khóa bí mật dùng cho xác thực JWT API | Có |
| `MAIL_*` | Thông tin SMTP để gửi email (VD: tài khoản Gmail) | Cần khi gửi mail |

## Project Structure

```text
qlyhoithao/
├── app/                  # Mã nguồn chính của ứng dụng (Models, Controllers, Middleware)
│   ├── Http/Controllers/ # Chứa logic xử lý (Admin, Author, Chair, Reviewer, Api...)
│   └── Models/           # Các model tương tác với CSDL
├── bootstrap/            # Files khởi động framework
├── config/               # Tất cả các file cấu hình
├── database/             # Migrations, Seeders và Factories cho CSDL
├── public/               # Thư mục gốc chứa index.php, assets tĩnh (images, css, js)
├── resources/            # Các view Blade, CSS (Tailwind), JS chưa biên dịch
├── routes/               # Định nghĩa các endpoints (web.php, api.php)
├── storage/              # File uploads, logs, compiled views
├── tests/                # Automated tests (PHPUnit)
├── .env                  # Cấu hình biến môi trường
├── composer.json         # Quản lý dependencies của PHP
├── package.json          # Quản lý dependencies của Node.js
└── README.md             # Tài liệu dự án
```

## Screenshots

![Trang chủ/Dashboard Placeholder](https://via.placeholder.com/800x450?text=Dashboard+Screenshot)
*Giao diện Dashboard thống kê Hội thảo*

![Quản lý bài báo Placeholder](https://via.placeholder.com/800x450?text=Paper+Management+Screenshot)
*Giao diện quản lý quá trình nộp và duyệt bài báo*
