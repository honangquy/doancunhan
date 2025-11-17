# 📱 API Quản lý Thông báo - Hướng dẫn tích hợp Mobile

## 📋 Tổng quan

API này cho phép **Chair** quản lý thông báo hội thảo và **User** xem thông báo đã nhận qua Mobile App.

**Base URL:** `http://localhost:8000/api`  
**Authentication:** JWT Bearer Token (header `Authorization: Bearer {token}`)

---

## 🔐 Authentication

### 1. Login để lấy Token

```bash
POST /api/auth/login
Content-Type: application/json

{
  "email": "honangquy1@gmail.com",
  "password": "your_password"
}
```

**Response:**
```json
{
  "success": true,
  "access_token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
  "token_type": "bearer",
  "expires_in": 3600
}
```

**Sử dụng Token:**
```
Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGc...
```

---

## 📨 API Endpoints

### 1. Danh sách thông báo

**Endpoint:** `GET /api/announcements`

**Mô tả:**
- **Chair:** Lấy danh sách thông báo của các hội thảo mình quản lý
- **User:** Lấy danh sách thông báo đã nhận

**Query Parameters:**
- `conference_id` (optional): Lọc theo hội thảo
- `status` (optional): Lọc theo trạng thái (`SENT`, `SCHEDULED`, `FAILED`)

**Request Example:**
```bash
curl -X GET "http://localhost:8000/api/announcements?status=SENT" \
  -H "Authorization: Bearer {token}"
```

**Response (Chair):**
```json
{
  "success": true,
  "data": {
    "announcements": [
      {
        "announcement_id": 13,
        "title": "test 13:42",
        "content": "dshajkdhsakhdka",
        "audience": "ALL",
        "channels": ["SYSTEM"],
        "status": "SENT",
        "scheduled_at": "2025-11-13 13:42:00",
        "sent_at": "2025-11-13 13:42:13",
        "created_at": "2025-11-13 13:40:54",
        "conference_id": 8,
        "conference_name": "Hội thảo cung cấp nguồn nhân lực",
        "recipient_count": 11
      }
    ],
    "statistics": {
      "total": 11,
      "sent": 10,
      "scheduled": 1,
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
        "announcement_id": 13,
        "title": "test 13:42",
        "content": "dshajkdhsakhdka",
        "sent_at": "2025-11-13 13:42:13",
        "conference_id": 8,
        "conference_name": "Hội thảo cung cấp nguồn nhân lực",
        "is_read": false,
        "read_at": null,
        "received_at": "2025-11-13 13:42:13"
      }
    ],
    "unread_count": 5
  }
}
```

---

### 2. Tạo thông báo mới (Chair only)

**Endpoint:** `POST /api/announcements`

**Request Body:**
```json
{
  "conference_id": 8,
  "title": "Thông báo quan trọng",
  "content": "Nội dung thông báo chi tiết...",
  "audience": "ALL",
  "channels": ["SYSTEM", "EMAIL"],
  "scheduled_at": "2025-11-13 14:30:00"
}
```

**Field Descriptions:**
- `conference_id` (required): ID hội thảo
- `title` (required): Tiêu đề thông báo (max 255 ký tự)
- `content` (required): Nội dung thông báo
- `audience` (required): Đối tượng nhận
  - `ALL`: Tất cả thành viên
  - `AUTHORS`: Tác giả
  - `REVIEWERS`: Phản biện
  - `CHAIRS`: Chủ tịch
- `channels` (required): Kênh gửi (array)
  - `SYSTEM`: Thông báo trong hệ thống
  - `EMAIL`: Gửi email
  - `CHATBOT`: Gửi qua chatbot
- `scheduled_at` (required): Thời gian gửi (phải sau hiện tại)

**Response:**
```json
{
  "success": true,
  "message": "Thông báo đã được tạo và lên lịch thành công",
  "data": {
    "announcement_id": 14,
    "scheduled_at": "2025-11-13 14:30:00"
  }
}
```

**Error Responses:**
```json
// Validation error
{
  "success": false,
  "message": "Dữ liệu không hợp lệ",
  "errors": {
    "scheduled_at": ["Thời gian gửi phải sau hiện tại"]
  }
}

// Permission denied
{
  "success": false,
  "message": "Bạn không có quyền tạo thông báo cho hội thảo này"
}
```

---

### 3. Chi tiết thông báo

**Endpoint:** `GET /api/announcements/{id}`

**Response:**
```json
{
  "success": true,
  "data": {
    "announcement_id": 13,
    "title": "test 13:42",
    "content": "dshajkdhsakhdka",
    "audience": "ALL",
    "channels": ["SYSTEM"],
    "status": "SENT",
    "scheduled_at": "2025-11-13 13:42:00",
    "sent_at": "2025-11-13 13:42:13",
    "conference_id": 8,
    "conference_name": "Hội thảo cung cấp nguồn nhân lực",
    "statistics": {
      "total_recipients": 11,
      "read_count": 3
    }
  }
}
```

---

### 4. Cập nhật thông báo (Chair only)

**Endpoint:** `PUT /api/announcements/{id}`

**Note:** Chỉ cập nhật được thông báo có `status = SCHEDULED`

**Request Body:**
```json
{
  "title": "Tiêu đề mới",
  "content": "Nội dung mới",
  "scheduled_at": "2025-11-13 15:00:00"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Đã cập nhật thông báo"
}
```

---

### 5. Xóa thông báo (Chair only)

**Endpoint:** `DELETE /api/announcements/{id}`

**Note:** Chỉ xóa được thông báo có `status = SCHEDULED`

**Response:**
```json
{
  "success": true,
  "message": "Đã xóa thông báo"
}
```

---

### 6. Đánh dấu đã đọc (User)

**Endpoint:** `POST /api/announcements/{id}/mark-read`

**Response:**
```json
{
  "success": true,
  "message": "Đã đánh dấu đã đọc"
}
```

---

### 7. Danh sách hội thảo (Chair only)

**Endpoint:** `GET /api/announcements/conferences/list`

**Mô tả:** Lấy danh sách hội thảo mà Chair quản lý để tạo thông báo

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

---

### 8. Xem trước số lượng người nhận

**Endpoint:** `POST /api/announcements/preview-recipients`

**Request Body:**
```json
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

---

## 📊 Data Models

### Announcement Object (Thông báo)

```typescript
interface Announcement {
  announcement_id: number;
  title: string;
  content: string;
  audience: "ALL" | "AUTHORS" | "REVIEWERS" | "CHAIRS";
  channels: Array<"SYSTEM" | "EMAIL" | "CHATBOT">;
  status: "SENT" | "SCHEDULED" | "FAILED";
  scheduled_at: string; // ISO datetime
  sent_at: string | null; // ISO datetime
  created_at: string; // ISO datetime
  conference_id: number;
  conference_name: string;
  recipient_count?: number; // Chỉ có với Chair
  is_read?: boolean; // Chỉ có với User
  read_at?: string | null; // Chỉ có với User
  received_at?: string; // Chỉ có với User
}
```

### Statistics Object

```typescript
interface AnnouncementStatistics {
  total: number;
  sent: number;
  scheduled: number;
  failed: number;
}
```

---

## 🎯 Use Cases (Mobile App)

### 1. Màn hình danh sách thông báo (User)

```dart
// Flutter example
Future<void> loadAnnouncements() async {
  final response = await dio.get(
    '/api/announcements',
    options: Options(
      headers: {'Authorization': 'Bearer $token'}
    )
  );
  
  if (response.data['success']) {
    final data = response.data['data'];
    final announcements = (data['announcements'] as List)
        .map((json) => Announcement.fromJson(json))
        .toList();
    final unreadCount = data['unread_count'];
    
    setState(() {
      this.announcements = announcements;
      this.unreadCount = unreadCount;
    });
  }
}
```

### 2. Màn hình tạo thông báo (Chair)

```dart
// Step 1: Load conference list
final response = await dio.get('/api/announcements/conferences/list');
final conferences = response.data['data'];

// Step 2: Preview recipient count
final previewResponse = await dio.post(
  '/api/announcements/preview-recipients',
  data: {
    'conference_id': selectedConferenceId,
    'audience': selectedAudience,
  }
);
final recipientCount = previewResponse.data['data']['count'];

// Step 3: Create announcement
final createResponse = await dio.post(
  '/api/announcements',
  data: {
    'conference_id': selectedConferenceId,
    'title': titleController.text,
    'content': contentController.text,
    'audience': selectedAudience,
    'channels': selectedChannels,
    'scheduled_at': scheduledDateTime.toIso8601String(),
  }
);
```

### 3. Đánh dấu đã đọc khi mở thông báo

```dart
Future<void> markAsRead(int announcementId) async {
  await dio.post(
    '/api/announcements/$announcementId/mark-read',
    options: Options(
      headers: {'Authorization': 'Bearer $token'}
    )
  );
}

// Gọi khi user mở chi tiết thông báo
void openAnnouncementDetail(Announcement announcement) {
  if (!announcement.isRead) {
    markAsRead(announcement.announcementId);
  }
  // Navigate to detail page...
}
```

---

## 🔔 Real-time Updates (Optional)

Nếu muốn nhận thông báo real-time, có thể:

1. **Polling:** Gọi API mỗi 30-60 giây
2. **Push Notification:** Tích hợp Firebase Cloud Messaging
3. **WebSocket:** Sử dụng Laravel Broadcasting (cần setup thêm)

---

## ⚠️ Error Handling

Tất cả API đều trả về format nhất quán:

**Success:**
```json
{
  "success": true,
  "data": { ... }
}
```

**Error:**
```json
{
  "success": false,
  "message": "Mô tả lỗi",
  "errors": { ... } // Chi tiết lỗi validation (optional)
}
```

**HTTP Status Codes:**
- `200`: Success
- `201`: Created
- `400`: Bad Request
- `401`: Unauthorized (token không hợp lệ)
- `403`: Forbidden (không có quyền)
- `404`: Not Found
- `422`: Validation Error
- `500`: Server Error

---

## 🧪 Testing với cURL

```bash
# 1. Login
TOKEN=$(curl -s -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"honangquy1@gmail.com","password":"123456"}' \
  | jq -r '.access_token')

# 2. Get announcements
curl -X GET "http://localhost:8000/api/announcements" \
  -H "Authorization: Bearer $TOKEN"

# 3. Create announcement
curl -X POST "http://localhost:8000/api/announcements" \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "conference_id": 8,
    "title": "Test từ API",
    "content": "Nội dung test",
    "audience": "ALL",
    "channels": ["SYSTEM"],
    "scheduled_at": "2025-11-13 15:00:00"
  }'

# 4. Mark as read
curl -X POST "http://localhost:8000/api/announcements/13/mark-read" \
  -H "Authorization: Bearer $TOKEN"
```

---

## 📞 Support

Nếu gặp vấn đề khi tích hợp, vui lòng liên hệ backend team hoặc tham khảo:
- Swagger Documentation: `http://localhost:8000/api/documentation`
- Source code: `app/Http/Controllers/Api/AnnouncementController.php`
- Routes: `routes/api.php` (line ~140)
