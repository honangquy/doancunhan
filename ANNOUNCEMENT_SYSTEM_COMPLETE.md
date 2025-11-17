# 📢 Hệ thống Quản lý Thông báo Hội thảo - Tài liệu Hoàn chỉnh

**Ngày hoàn thành:** 13/11/2025  
**Phiên bản:** 1.0  
**Tác giả:** Backend Team

---

## 📋 Mục lục

1. [Tổng quan hệ thống](#tổng-quan-hệ-thống)
2. [Kiến trúc hệ thống](#kiến-trúc-hệ-thống)
3. [Database Schema](#database-schema)
4. [Backend Implementation](#backend-implementation)
5. [API Documentation](#api-documentation)
6. [Scheduler & Jobs](#scheduler--jobs)
7. [Testing Results](#testing-results)
8. [Mobile Integration Guide](#mobile-integration-guide)
9. [Deployment Guide](#deployment-guide)
10. [Troubleshooting](#troubleshooting)

---

## 1. Tổng quan hệ thống

### 1.1. Mục đích

Hệ thống cho phép **Chair hội thảo** gửi thông báo đến các thành viên tham gia hội thảo qua nhiều kênh khác nhau (System, Email, Chatbot) với khả năng:
- Lên lịch gửi tự động
- Phân loại đối tượng nhận (ALL, AUTHORS, REVIEWERS, CHAIRS)
- Theo dõi trạng thái gửi và đọc
- Quản lý qua Web và Mobile App

### 1.2. Tính năng chính

#### 👨‍💼 Chair (Chủ tịch hội thảo)
- ✅ Tạo thông báo mới với lịch gửi
- ✅ Chọn đối tượng nhận: Tất cả / Tác giả / Phản biện / Chair
- ✅ Chọn kênh gửi: System / Email / Chatbot
- ✅ Xem trước số lượng người nhận
- ✅ Sửa/xóa thông báo đang lên lịch
- ✅ Xem thống kê gửi thành công/thất bại
- ✅ Xem số người đã đọc

#### 👥 User (Thành viên hội thảo)
- ✅ Nhận thông báo tự động
- ✅ Xem danh sách thông báo đã nhận
- ✅ Đánh dấu đã đọc
- ✅ Xem số thông báo chưa đọc

### 1.3. Flow hoạt động

```
┌─────────────┐
│   Chair     │
│  Tạo TB     │
└──────┬──────┘
       │
       ▼
┌─────────────────┐
│   thongbao      │
│ status=SCHEDULED│
└──────┬──────────┘
       │
       ▼
┌──────────────────────────┐
│ ProcessScheduledJob      │
│ (Chạy mỗi phút)          │
│ scheduled_at <= now()    │
└──────┬───────────────────┘
       │
       ▼
┌──────────────────────────┐
│ SendAnnouncementJob      │
│ - Get recipients         │
│ - Create notifications   │
│ - Update status=SENT     │
└──────┬───────────────────┘
       │
       ▼
┌──────────────────────────┐
│ user_notifications       │
│ (11 users notified)      │
└──────────────────────────┘
       │
       ▼
┌──────────────────────────┐
│ User nhận & đọc TB       │
│ is_read = true           │
└──────────────────────────┘
```

---

## 2. Kiến trúc hệ thống

### 2.1. Technology Stack

**Backend:**
- Laravel 9
- PHP 8.1
- MySQL 8.0
- JWT Authentication
- Laravel Scheduler
- Queue Jobs

**Frontend Web:**
- Blade Templates
- Alpine.js
- TailwindCSS
- SVG Icons

**Mobile:**
- Flutter/Dart (recommended)
- Dio HTTP Client
- JWT Bearer Token

### 2.2. Architecture Pattern

```
┌─────────────────────────────────────────────┐
│              Client Layer                    │
│  ┌──────────────┐      ┌──────────────┐    │
│  │   Web UI     │      │  Mobile App  │    │
│  │  (Blade)     │      │  (Flutter)   │    │
│  └──────┬───────┘      └──────┬───────┘    │
│         │                     │             │
└─────────┼─────────────────────┼─────────────┘
          │                     │
          │ HTTP/Session        │ HTTP/JWT
          ▼                     ▼
┌─────────────────────────────────────────────┐
│              API Layer                       │
│  ┌──────────────────────────────────────┐  │
│  │  AnnouncementController.php          │  │
│  │  - index()    - store()              │  │
│  │  - show()     - update()             │  │
│  │  - destroy()  - markAsRead()         │  │
│  └──────────────────────────────────────┘  │
└─────────────────────────────────────────────┘
          │
          ▼
┌─────────────────────────────────────────────┐
│           Business Logic Layer               │
│  ┌──────────────────────────────────────┐  │
│  │  Jobs:                               │  │
│  │  - ProcessScheduledAnnouncementsJob  │  │
│  │  - SendAnnouncementJob               │  │
│  │  - SendEmailNotificationJob          │  │
│  └──────────────────────────────────────┘  │
└─────────────────────────────────────────────┘
          │
          ▼
┌─────────────────────────────────────────────┐
│              Data Layer                      │
│  ┌──────────────────────────────────────┐  │
│  │  Database:                           │  │
│  │  - thongbao                          │  │
│  │  - user_notifications                │  │
│  │  - nguoidung                         │  │
│  │  - hoithao                           │  │
│  │  - join_requests                     │  │
│  └──────────────────────────────────────┘  │
└─────────────────────────────────────────────┘
```

---

## 3. Database Schema

### 3.1. Bảng `thongbao` (Announcements)

```sql
CREATE TABLE thongbao (
    announcement_id INT PRIMARY KEY AUTO_INCREMENT,
    conference_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    audience ENUM('ALL','AUTHORS','REVIEWERS','CHAIRS') NOT NULL,
    channels JSON NOT NULL,  -- ["SYSTEM", "EMAIL", "CHATBOT"]
    status ENUM('SCHEDULED','SENT','FAILED') DEFAULT 'SCHEDULED',
    scheduled_at DATETIME NOT NULL,
    sent_at DATETIME NULL,
    created_at DATETIME NOT NULL,
    created_by INT NOT NULL,
    
    FOREIGN KEY (conference_id) REFERENCES hoithao(conference_id),
    FOREIGN KEY (created_by) REFERENCES nguoidung(user_id),
    INDEX idx_status_scheduled (status, scheduled_at),
    INDEX idx_conference (conference_id)
);
```

**Ví dụ dữ liệu:**
```json
{
  "announcement_id": 14,
  "conference_id": 8,
  "title": "Test API từ Mobile",
  "content": "Đây là thông báo được tạo từ API mobile app",
  "audience": "ALL",
  "channels": ["SYSTEM", "EMAIL"],
  "status": "SENT",
  "scheduled_at": "2025-11-13 14:21:56",
  "sent_at": "2025-11-13 14:22:34",
  "created_by": 19
}
```

### 3.2. Bảng `user_notifications` (User Notifications)

```sql
CREATE TABLE user_notifications (
    notification_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    announcement_id INT NOT NULL,
    is_read BOOLEAN DEFAULT FALSE,
    read_at DATETIME NULL,
    created_at DATETIME NOT NULL,
    
    FOREIGN KEY (user_id) REFERENCES nguoidung(user_id),
    FOREIGN KEY (announcement_id) REFERENCES thongbao(announcement_id) ON DELETE CASCADE,
    INDEX idx_user_read (user_id, is_read),
    INDEX idx_announcement (announcement_id),
    UNIQUE KEY unique_user_announcement (user_id, announcement_id)
);
```

### 3.3. Mối quan hệ với bảng khác

```sql
-- Bảng hội thảo
hoithao (
    conference_id,
    title,          -- Tên hội thảo
    chair_id,       -- Chair quản lý
    status          -- APPROVED, ACTIVE
)

-- Bảng người dùng
nguoidung (
    user_id,
    email,
    full_name,
    password_hash
)

-- Bảng tham gia hội thảo
join_requests (
    user_id,
    conference_id,
    role,           -- AUTHOR, REVIEWER
    status          -- APPROVED
)

-- Bảng bài báo
baibao (
    paper_id,
    conference_id,
    submitter_id    -- Tác giả
)
```

### 3.4. Indexes cho Performance

```sql
-- Index cho scheduler query
CREATE INDEX idx_scheduled_processing 
ON thongbao(status, scheduled_at) 
WHERE status = 'SCHEDULED';

-- Index cho recipient query
CREATE INDEX idx_join_requests_approved 
ON join_requests(conference_id, status, role) 
WHERE status = 'APPROVED';

-- Index cho unread count
CREATE INDEX idx_user_unread 
ON user_notifications(user_id, is_read) 
WHERE is_read = FALSE;
```

---

## 4. Backend Implementation

### 4.1. File Structure

```
app/
├── Http/
│   └── Controllers/
│       ├── Api/
│       │   └── AnnouncementController.php  (700+ lines)
│       └── Chair/
│           └── AnnouncementController.php  (web UI - empty, sử dụng closure)
├── Jobs/
│   ├── ProcessScheduledAnnouncementsJob.php
│   └── SendAnnouncementJob.php
├── Mail/
│   └── AnnouncementMail.php
└── Console/
    └── Kernel.php (Scheduler config)

routes/
├── api.php    (+10 routes mới)
└── web.php    (announcement routes với closure)

database/
└── migrations/
    └── 2025_11_13_create_announcements_tables.php
```

### 4.2. API Controller - AnnouncementController.php

**Location:** `app/Http/Controllers/Api/AnnouncementController.php`

**Methods:**

| Method | Endpoint | Description |
|--------|----------|-------------|
| `index()` | GET /api/announcements | Danh sách (Chair: manage, User: received) |
| `store()` | POST /api/announcements | Tạo mới (Chair only) |
| `show()` | GET /api/announcements/{id} | Chi tiết |
| `update()` | PUT /api/announcements/{id} | Cập nhật SCHEDULED (Chair) |
| `destroy()` | DELETE /api/announcements/{id} | Xóa SCHEDULED (Chair) |
| `markAsRead()` | POST /api/announcements/{id}/mark-read | Đánh dấu đọc (User) |
| `getConferences()` | GET /api/announcements/conferences/list | Danh sách hội thảo (Chair) |
| `previewRecipients()` | POST /api/announcements/preview-recipients | Xem trước người nhận |

**Key Logic - Role Detection:**

```php
public function index(Request $request)
{
    $user = $request->user();
    
    // Phát hiện role tự động
    $isChair = DB::table('hoithao')
        ->where('chair_id', $user->user_id)
        ->exists();
    
    if ($isChair) {
        return $this->getChairAnnouncements($request);
    } else {
        return $this->getUserAnnouncements($request);
    }
}
```

**Key Logic - Permission Check:**

```php
public function store(Request $request)
{
    // Kiểm tra Chair của hội thảo
    $isChair = DB::table('hoithao')
        ->where('conference_id', $request->conference_id)
        ->where('chair_id', $user->user_id)
        ->exists();
    
    if (!$isChair) {
        return response()->json([
            'success' => false,
            'message' => 'Bạn không có quyền tạo thông báo cho hội thảo này'
        ], 403);
    }
    
    // Tạo thông báo...
}
```

### 4.3. Jobs - ProcessScheduledAnnouncementsJob.php

**Location:** `app/Jobs/ProcessScheduledAnnouncementsJob.php`

**Chức năng:** Quét và xử lý thông báo đã đến lịch gửi

```php
public function handle()
{
    $announcements = DB::table('thongbao')
        ->where('status', 'SCHEDULED')
        ->where('scheduled_at', '<=', now())
        ->get();
    
    foreach ($announcements as $announcement) {
        // Dispatch job gửi thông báo
        SendAnnouncementJob::dispatch($announcement);
        
        // Update status
        DB::table('thongbao')
            ->where('announcement_id', $announcement->announcement_id)
            ->update([
                'status' => 'SENT',
                'sent_at' => now()
            ]);
    }
}
```

**Scheduled:** Chạy mỗi phút (cron: `* * * * *`)

### 4.4. Jobs - SendAnnouncementJob.php

**Location:** `app/Jobs/SendAnnouncementJob.php`

**Chức năng:** Gửi thông báo đến từng user

**Key Logic - Get Recipients:**

```php
private function getRecipients($audience, $conferenceId)
{
    switch ($audience) {
        case 'ALL':
            // ✅ FIXED: Dùng join_requests thay vì vaitronguoidung
            return DB::table('nguoidung as u')
                ->join('join_requests as jr', 'jr.user_id', '=', 'u.user_id')
                ->where('jr.conference_id', $conferenceId)
                ->where('jr.status', 'APPROVED')
                ->select('u.user_id', 'u.full_name', 'u.email')
                ->distinct()
                ->get();
                
        case 'AUTHORS':
            return DB::table('nguoidung as u')
                ->join('baibao as bb', 'bb.submitter_id', '=', 'u.user_id')
                ->where('bb.conference_id', $conferenceId)
                ->select('u.user_id', 'u.full_name', 'u.email')
                ->distinct()
                ->get();
                
        case 'REVIEWERS':
            return DB::table('nguoidung as u')
                ->join('join_requests as jr', 'jr.user_id', '=', 'u.user_id')
                ->where('jr.conference_id', $conferenceId)
                ->where('jr.status', 'APPROVED')
                ->where('jr.role', 'REVIEWER')
                ->select('u.user_id', 'u.full_name', 'u.email')
                ->distinct()
                ->get();
                
        case 'CHAIRS':
            return DB::table('nguoidung as u')
                ->join('hoithao as ht', 'ht.chair_id', '=', 'u.user_id')
                ->where('ht.conference_id', $conferenceId)
                ->select('u.user_id', 'u.full_name', 'u.email')
                ->distinct()
                ->get();
    }
}
```

**Create Notifications:**

```php
foreach ($recipients as $recipient) {
    DB::table('user_notifications')->insert([
        'user_id' => $recipient->user_id,
        'announcement_id' => $this->announcement->announcement_id,
        'is_read' => false,
        'created_at' => now()
    ]);
}
```

### 4.5. Scheduler Configuration

**Location:** `app/Console/Kernel.php`

```php
protected function schedule(Schedule $schedule)
{
    // Chạy job xử lý thông báo đã lên lịch mỗi phút
    $schedule->job(new \App\Jobs\ProcessScheduledAnnouncementsJob)
        ->everyMinute();
    
    // Xử lý email nhắc lịch hội thảo mỗi ngày lúc 7:00 sáng
    $schedule->command('reminders:process-conference')
        ->dailyAt('07:00');
}
```

**Cron Setup (Production):**

```bash
* * * * * cd /path/to/project && php artisan schedule:run >> /dev/null 2>&1
```

**Development (macOS):**

```bash
# Option 1: Cron job
crontab -e
# Add: * * * * * cd /Applications/XAMPP/xamppfiles/htdocs/doancunhan && /usr/bin/php artisan schedule:run

# Option 2: Background process
nohup bash -c 'while true; do php artisan schedule:run; sleep 60; done' > storage/logs/scheduler.log 2>&1 &
```

---

## 5. API Documentation

### 5.1. Authentication

**Login:**
```http
POST /api/auth/login
Content-Type: application/json

{
  "email": "honangquy1@gmail.com",
  "password": "Concac123!@#"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Đăng nhập thành công",
  "data": {
    "user": {
      "user_id": 19,
      "email": "honangquy1@gmail.com",
      "full_name": "Hồ Năng Quý",
      "roles": [
        {
          "role_code": "CHAIR",
          "conference_id": 8
        }
      ]
    },
    "token": "eyJ0eXAiOiJKV1QiLCJhbGc..."
  }
}
```

**Sử dụng Token:**
```http
Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGc...
```

### 5.2. API Endpoints

#### 📋 GET /api/announcements - Danh sách thông báo

**Request:**
```http
GET /api/announcements?status=SENT&conference_id=8
Authorization: Bearer {token}
```

**Response (Chair):**
```json
{
  "success": true,
  "data": {
    "announcements": [
      {
        "announcement_id": 14,
        "title": "Test API từ Mobile",
        "content": "Đây là thông báo được tạo từ API mobile app",
        "audience": "ALL",
        "channels": ["SYSTEM"],
        "status": "SENT",
        "scheduled_at": "2025-11-13 14:21:56",
        "sent_at": "2025-11-13 14:22:34",
        "created_at": "2025-11-13 14:19:56",
        "conference_id": 8,
        "conference_name": "Hội thảo cung cấp nguồn nhân lực",
        "recipient_count": 22
      }
    ],
    "statistics": {
      "total": 13,
      "sent": 13,
      "scheduled": 0,
      "failed": 0
    }
  }
}
```

**Response (User):**
```json
{
  "success": true,
  "data": {
    "announcements": [
      {
        "announcement_id": 14,
        "title": "Test API từ Mobile",
        "content": "Đây là thông báo...",
        "sent_at": "2025-11-13 14:22:34",
        "conference_id": 8,
        "conference_name": "Hội thảo cung cấp nguồn nhân lực",
        "is_read": false,
        "read_at": null,
        "received_at": "2025-11-13 14:22:34"
      }
    ],
    "unread_count": 5
  }
}
```

#### ➕ POST /api/announcements - Tạo thông báo mới

**Request:**
```http
POST /api/announcements
Authorization: Bearer {token}
Content-Type: application/json

{
  "conference_id": 8,
  "title": "Thông báo quan trọng",
  "content": "Nội dung thông báo chi tiết...",
  "audience": "ALL",
  "channels": ["SYSTEM", "EMAIL"],
  "scheduled_at": "2025-11-13 15:00:00"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Thông báo đã được tạo và lên lịch thành công",
  "data": {
    "announcement_id": 15,
    "scheduled_at": "2025-11-13 15:00:00"
  }
}
```

**Validation Rules:**
- `conference_id`: required, exists in hoithao
- `title`: required, max 255 chars
- `content`: required
- `audience`: required, in [ALL, AUTHORS, REVIEWERS, CHAIRS]
- `channels`: required array, items in [SYSTEM, EMAIL, CHATBOT]
- `scheduled_at`: required, date, after now

#### 🔍 GET /api/announcements/{id} - Chi tiết thông báo

**Request:**
```http
GET /api/announcements/14
Authorization: Bearer {token}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "announcement_id": 14,
    "conference_id": 8,
    "title": "Test API từ Mobile",
    "content": "Đây là thông báo được tạo từ API mobile app",
    "audience": "ALL",
    "scheduled_at": "2025-11-13 14:21:56",
    "sent_at": "2025-11-13 14:22:34",
    "status": "SENT",
    "channels": ["SYSTEM"],
    "created_by": 19,
    "created_at": "2025-11-13 14:19:56",
    "conference_name": "Hội thảo cung cấp nguồn nhân lực",
    "statistics": {
      "total_recipients": 22,
      "read_count": 3
    }
  }
}
```

#### ✏️ PUT /api/announcements/{id} - Cập nhật thông báo

**Chỉ cho phép cập nhật thông báo có `status = SCHEDULED`**

**Request:**
```http
PUT /api/announcements/15
Authorization: Bearer {token}
Content-Type: application/json

{
  "title": "Thông báo test API - ĐÃ CẬP NHẬT",
  "content": "Nội dung mới",
  "scheduled_at": "2025-11-13 16:00:00"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Đã cập nhật thông báo"
}
```

#### ❌ DELETE /api/announcements/{id} - Xóa thông báo

**Chỉ cho phép xóa thông báo có `status = SCHEDULED`**

**Request:**
```http
DELETE /api/announcements/15
Authorization: Bearer {token}
```

**Response:**
```json
{
  "success": true,
  "message": "Đã xóa thông báo"
}
```

#### ✅ POST /api/announcements/{id}/mark-read - Đánh dấu đã đọc

**Request:**
```http
POST /api/announcements/14/mark-read
Authorization: Bearer {token}
```

**Response:**
```json
{
  "success": true,
  "message": "Đã đánh dấu đã đọc"
}
```

#### 📚 GET /api/announcements/conferences/list - Danh sách hội thảo

**Request:**
```http
GET /api/announcements/conferences/list
Authorization: Bearer {token}
```

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "conference_id": 9,
      "conference_name": "Hội thảo chuyển đổi kinh tế số",
      "start_date": "2026-05-08",
      "end_date": "2026-05-29"
    },
    {
      "conference_id": 8,
      "conference_name": "Hội thảo cung cấp nguồn nhân lực",
      "start_date": "2026-03-31",
      "end_date": "2026-06-25"
    }
  ]
}
```

#### 👥 POST /api/announcements/preview-recipients - Xem trước người nhận

**Request:**
```http
POST /api/announcements/preview-recipients
Authorization: Bearer {token}
Content-Type: application/json

{
  "conference_id": 8,
  "audience": "ALL"
}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "count": 11,
    "audience": "ALL"
  }
}
```

### 5.3. Error Responses

**400 Bad Request:**
```json
{
  "success": false,
  "message": "Bad request"
}
```

**401 Unauthorized:**
```json
{
  "success": false,
  "message": "Unauthenticated"
}
```

**403 Forbidden:**
```json
{
  "success": false,
  "message": "Bạn không có quyền tạo thông báo cho hội thảo này"
}
```

**404 Not Found:**
```json
{
  "success": false,
  "message": "Không tìm thấy thông báo"
}
```

**422 Validation Error:**
```json
{
  "success": false,
  "message": "Dữ liệu không hợp lệ",
  "errors": {
    "title": ["Vui lòng nhập tiêu đề"],
    "content": ["Vui lòng nhập nội dung"],
    "scheduled_at": ["Thời gian gửi phải sau hiện tại"]
  }
}
```

---

## 6. Scheduler & Jobs

### 6.1. Scheduler Flow

```
Cron Job (Every minute)
        ↓
php artisan schedule:run
        ↓
ProcessScheduledAnnouncementsJob::handle()
        ↓
Query: status=SCHEDULED AND scheduled_at <= NOW()
        ↓
For each announcement:
    ├── SendAnnouncementJob::dispatch()
    └── Update status=SENT, sent_at=NOW()
        ↓
SendAnnouncementJob::handle()
    ├── getRecipients(audience, conference_id)
    ├── Create user_notifications
    └── Send Email (if EMAIL in channels)
```

### 6.2. Query Performance

**Scheduler Query (Optimized):**
```sql
SELECT * FROM thongbao 
WHERE status = 'SCHEDULED' 
  AND scheduled_at <= NOW()
ORDER BY scheduled_at ASC
LIMIT 100;
```

**Index:** `idx_scheduled_processing (status, scheduled_at)`

**Recipient Query (ALL audience):**
```sql
SELECT DISTINCT u.user_id, u.full_name, u.email
FROM nguoidung u
INNER JOIN join_requests jr ON jr.user_id = u.user_id
WHERE jr.conference_id = 8
  AND jr.status = 'APPROVED';
```

**Index:** `idx_join_requests_approved (conference_id, status, role)`

### 6.3. Job Queue Configuration

**Queue Driver:** Sync (Development) / Redis (Production)

**`.env` configuration:**
```env
QUEUE_CONNECTION=sync  # Development
# QUEUE_CONNECTION=redis  # Production
```

**Start Queue Worker (Production):**
```bash
php artisan queue:work --tries=3 --timeout=90
```

**Supervisor Configuration:**
```ini
[program:laravel-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /path/to/artisan queue:work --sleep=3 --tries=3
autostart=true
autorestart=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/path/to/worker.log
```

---

## 7. Testing Results

### 7.1. Unit Testing

**Test Account:**
- Email: `honangquy1@gmail.com`
- Password: `Concac123!@#`
- Role: Chair of conferences 1, 7, 8

### 7.2. API Test Results

| # | Test Case | Method | Endpoint | Expected | Actual | Status |
|---|-----------|--------|----------|----------|--------|--------|
| 1 | Login Chair | POST | `/api/auth/login` | Token + User info | ✅ Token received | ✅ PASS |
| 2 | Get announcements | GET | `/api/announcements` | 13 announcements | ✅ 13 items + stats | ✅ PASS |
| 3 | Get conferences | GET | `/api/announcements/conferences/list` | 3 conferences | ✅ 3 items | ✅ PASS |
| 4 | Preview ALL | POST | `/api/announcements/preview-recipients` | count=11 | ✅ count=11 | ✅ PASS |
| 5 | Preview REVIEWERS | POST | `/api/announcements/preview-recipients` | count=6 | ✅ count=6 | ✅ PASS |
| 6 | Create announcement | POST | `/api/announcements` | announcement_id=15 | ✅ ID=15 | ✅ PASS |
| 7 | Get detail | GET | `/api/announcements/15` | Full detail + stats | ✅ Complete data | ✅ PASS |
| 8 | Update title | PUT | `/api/announcements/15` | Success message | ✅ Updated | ✅ PASS |
| 9 | Filter SCHEDULED | GET | `/api/announcements?status=SCHEDULED` | 1 item | ✅ 1 item | ✅ PASS |
| 10 | Delete SCHEDULED | DELETE | `/api/announcements/15` | Success | ✅ Deleted | ✅ PASS |
| 11 | Validation error | POST | `/api/announcements` | 422 + errors | ✅ 422 response | ✅ PASS |
| 12 | Delete SENT (fail) | DELETE | `/api/announcements/14` | 403 error | ✅ 403 forbidden | ✅ PASS |

**Overall: 12/12 PASS (100%)**

### 7.3. Scheduler Testing

**Test Case:** Tạo announcement scheduled +2 phút

**Steps:**
1. Tạo announcement lúc 13:36:41, scheduled 13:38:11
2. Scheduler chạy tự động mỗi phút
3. Lúc 13:39:12 (sau scheduled_at) → Job xử lý
4. Status chuyển SCHEDULED → SENT
5. 11 user_notifications được tạo

**Result:** ✅ PASS

**Proof:**
```
Status: SENT
Sent at: 2025-11-13 13:39:12
User notifications: 11
Người nhận: Võ Nguyên Phúc, Một Con Chó, Hồ Năng Quý, Quả Lọ, Quả Lọ
```

### 7.4. Bug Fixes

#### Bug #1: Column 'conference_name' not found
**Cause:** Bảng `hoithao` có cột `title` không phải `conference_name`  
**Fix:** Sửa query từ `ht.conference_name` → `ht.title as conference_name`  
**Status:** ✅ FIXED

#### Bug #2: ALL audience returns 0 recipients
**Cause:** Query dùng `vaitronguoidung` (có conference_id=NULL)  
**Fix:** Chuyển sang dùng `join_requests` (có conference_id)  
**Result:** 0 recipients → 11 recipients  
**Status:** ✅ FIXED

#### Bug #3: Scheduler không chạy tự động
**Cause:** Chưa setup cron job trên macOS  
**Fix:** Thêm cron job hoặc chạy background process  
**Status:** ✅ FIXED

---

## 8. Mobile Integration Guide

### 8.1. Setup

**1. Install Dependencies (Flutter):**
```yaml
# pubspec.yaml
dependencies:
  dio: ^5.4.0
  shared_preferences: ^2.2.2
  flutter_secure_storage: ^9.0.0
  intl: ^0.18.1
```

**2. Create API Service:**
```dart
// lib/services/api_service.dart
import 'package:dio/dio.dart';

class ApiService {
  final Dio dio = Dio(BaseOptions(
    baseUrl: 'http://192.168.1.100:8000/api',
    connectTimeout: Duration(seconds: 5),
    receiveTimeout: Duration(seconds: 3),
  ));
  
  String? accessToken;
  
  Future<bool> login(String email, String password) async {
    try {
      final response = await dio.post('/auth/login', data: {
        'email': email,
        'password': password,
      });
      
      if (response.data['success']) {
        accessToken = response.data['data']['token'];
        await _saveToken(accessToken!);
        return true;
      }
      return false;
    } catch (e) {
      return false;
    }
  }
  
  void _addAuthHeader() {
    if (accessToken != null) {
      dio.options.headers['Authorization'] = 'Bearer $accessToken';
    }
  }
  
  Future<List<Announcement>> getAnnouncements() async {
    _addAuthHeader();
    final response = await dio.get('/announcements');
    
    if (response.data['success']) {
      final data = response.data['data'];
      return (data['announcements'] as List)
          .map((json) => Announcement.fromJson(json))
          .toList();
    }
    throw Exception('Failed to load announcements');
  }
}
```

**3. Create Models:**
```dart
// lib/models/announcement.dart
class Announcement {
  final int announcementId;
  final String title;
  final String content;
  final String audience;
  final List<String> channels;
  final String status;
  final DateTime scheduledAt;
  final DateTime? sentAt;
  final int conferenceId;
  final String conferenceName;
  final int? recipientCount;
  final bool? isRead;

  Announcement({
    required this.announcementId,
    required this.title,
    required this.content,
    required this.audience,
    required this.channels,
    required this.status,
    required this.scheduledAt,
    this.sentAt,
    required this.conferenceId,
    required this.conferenceName,
    this.recipientCount,
    this.isRead,
  });

  factory Announcement.fromJson(Map<String, dynamic> json) {
    return Announcement(
      announcementId: json['announcement_id'],
      title: json['title'],
      content: json['content'],
      audience: json['audience'],
      channels: List<String>.from(json['channels']),
      status: json['status'],
      scheduledAt: DateTime.parse(json['scheduled_at']),
      sentAt: json['sent_at'] != null ? DateTime.parse(json['sent_at']) : null,
      conferenceId: json['conference_id'],
      conferenceName: json['conference_name'],
      recipientCount: json['recipient_count'],
      isRead: json['is_read'],
    );
  }
}
```

### 8.2. Backend Setup

**1. Lấy IP máy Mac:**
```bash
ipconfig getifaddr en0
# Output: 192.168.1.100
```

**2. Chạy Laravel server:**
```bash
php artisan serve --host=0.0.0.0 --port=8000
```

**3. Update API Base URL:**
```dart
// lib/config/api_config.dart
class ApiConfig {
  static const String baseUrl = 'http://192.168.1.100:8000/api';
}
```

**4. Test connection từ mobile:**
```dart
void testConnection() async {
  try {
    final dio = Dio(BaseOptions(baseUrl: ApiConfig.baseUrl));
    final response = await dio.get('/');
    print('API connected: ${response.data}');
  } catch (e) {
    print('Connection failed: $e');
  }
}
```

### 8.3. Example UI Screens

**List Screen:**
```dart
class AnnouncementListScreen extends StatefulWidget {
  @override
  _AnnouncementListScreenState createState() => _AnnouncementListScreenState();
}

class _AnnouncementListScreenState extends State<AnnouncementListScreen> {
  final ApiService _api = ApiService();
  List<Announcement> announcements = [];
  int unreadCount = 0;

  @override
  void initState() {
    super.initState();
    _loadAnnouncements();
  }

  Future<void> _loadAnnouncements() async {
    announcements = await _api.getAnnouncements();
    setState(() {});
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text('Thông báo'),
        actions: [
          Badge(
            label: Text('$unreadCount'),
            child: Icon(Icons.notifications),
          ),
        ],
      ),
      body: ListView.builder(
        itemCount: announcements.length,
        itemBuilder: (context, index) {
          final announcement = announcements[index];
          return ListTile(
            leading: Icon(
              announcement.isRead == true 
                  ? Icons.mark_email_read 
                  : Icons.mark_email_unread,
            ),
            title: Text(announcement.title),
            subtitle: Text(announcement.conferenceName),
            onTap: () => _openDetail(announcement),
          );
        },
      ),
    );
  }
}
```

---

## 9. Deployment Guide

### 9.1. Server Requirements

- PHP 8.1+
- MySQL 8.0+
- Nginx / Apache
- Composer
- Node.js & NPM
- Redis (recommended for queue)

### 9.2. Deployment Steps

**1. Clone & Install:**
```bash
git clone https://github.com/yourusername/project.git
cd project
composer install --no-dev
npm install && npm run build
```

**2. Environment Setup:**
```bash
cp .env.example .env
php artisan key:generate
```

**Edit `.env`:**
```env
APP_NAME="Conference Management"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://api.yourdomain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=quanly_hoithao
DB_USERNAME=your_username
DB_PASSWORD=your_password

QUEUE_CONNECTION=redis
CACHE_DRIVER=redis
SESSION_DRIVER=redis

MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
```

**3. Database Migration:**
```bash
php artisan migrate --force
php artisan db:seed --force
```

**4. Permissions:**
```bash
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

**5. Cron Job:**
```bash
crontab -e
# Add:
* * * * * cd /var/www/html/project && php artisan schedule:run >> /dev/null 2>&1
```

**6. Queue Worker:**
```bash
# Start queue worker
php artisan queue:work redis --tries=3 --daemon

# Or use Supervisor (recommended)
sudo apt-get install supervisor
```

**Supervisor config:**
```ini
[program:laravel-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/html/project/artisan queue:work redis --sleep=3 --tries=3
autostart=true
autorestart=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/html/project/storage/logs/worker.log
```

**7. Nginx Configuration:**
```nginx
server {
    listen 80;
    server_name api.yourdomain.com;
    root /var/www/html/project/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

**8. SSL Certificate:**
```bash
sudo certbot --nginx -d api.yourdomain.com
```

**9. Optimize:**
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
composer dump-autoload --optimize
```

---

## 10. Troubleshooting

### 10.1. Common Issues

#### Issue: "Connection refused" từ mobile
**Cause:** Server không chạy hoặc sai IP  
**Solution:**
```bash
# Kiểm tra server
php artisan serve --host=0.0.0.0 --port=8000

# Kiểm tra IP
ipconfig getifaddr en0

# Test từ browser mobile
http://192.168.1.100:8000/api
```

#### Issue: "401 Unauthorized"
**Cause:** Token hết hạn hoặc không hợp lệ  
**Solution:**
```dart
// Login lại để lấy token mới
await apiService.login(email, password);
```

#### Issue: Scheduler không chạy
**Cause:** Cron job chưa setup  
**Solution:**
```bash
# Kiểm tra cron
crontab -l

# Test manual
php artisan schedule:run

# Kiểm tra log
tail -f storage/logs/laravel.log
```

#### Issue: "SQLSTATE[42S22]: Column not found"
**Cause:** Schema mismatch  
**Solution:**
```bash
# Kiểm tra migration
php artisan migrate:status

# Re-run migration
php artisan migrate:fresh --seed
```

#### Issue: ALL audience returns 0 recipients
**Cause:** Bảng `join_requests` rỗng  
**Solution:**
```sql
-- Kiểm tra data
SELECT COUNT(*) FROM join_requests 
WHERE conference_id = 8 AND status = 'APPROVED';

-- Nếu rỗng, cần seed data hoặc import từ backup
```

### 10.2. Debugging Tools

**Enable Query Log:**
```php
// Trong AppServiceProvider.php boot()
DB::listen(function($query) {
    Log::info($query->sql, $query->bindings);
});
```

**Check Scheduler:**
```bash
php artisan schedule:list
```

**Test Queue:**
```bash
php artisan queue:failed
php artisan queue:retry all
```

**Monitor Logs:**
```bash
tail -f storage/logs/laravel.log
tail -f storage/logs/scheduler.log
```

---

## 📚 Tài liệu tham khảo

- **API Documentation:** `API_ANNOUNCEMENT_GUIDE.md`
- **API Summary:** `ANNOUNCEMENT_API_SUMMARY.md`
- **Mobile Integration:** `MOBILE_INTEGRATION.md`
- **Source Code:**
  - Controller: `app/Http/Controllers/Api/AnnouncementController.php`
  - Jobs: `app/Jobs/SendAnnouncementJob.php`
  - Routes: `routes/api.php` (line ~140)

---

## 👥 Credits

**Development Team:**
- Backend API: ✅ Complete
- Scheduler & Jobs: ✅ Complete
- Database Schema: ✅ Complete
- Testing: ✅ 12/12 PASS
- Documentation: ✅ Complete

**Version History:**
- v1.0 (13/11/2025): Initial release with full API & scheduler

---

**📞 Support:**  
Nếu gặp vấn đề khi tích hợp, vui lòng liên hệ backend team hoặc tham khảo các file tài liệu kèm theo.

**🎉 Hệ thống đã sẵn sàng cho Production!**
