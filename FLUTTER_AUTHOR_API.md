# Flutter Author API Documentation

## Overview
API endpoints cho tác giả (Author) để quản lý bài báo trong ứng dụng Flutter.

**Base URL:** `http://your-domain.com/api`

**Authentication:** Tất cả endpoints yêu cầu Bearer token trong header:
```
Authorization: Bearer {your_token}
```

---

## 📊 Statistics Endpoints

### 1. Get Author Statistics
Lấy thống kê tổng quan về bài báo của tác giả.

**Endpoint:** `GET /api/author/statistics`

**Response:**
```json
{
  "status": "success",
  "message": "Thống kê bài báo của tác giả",
  "data": {
    "total": 10,
    "draft": 2,
    "submitted": 3,
    "under_review": 2,
    "accepted": 2,
    "rejected": 1,
    "withdrawn": 0
  }
}
```

**Flutter Usage:**
```dart
Future<Map<String, dynamic>> getAuthorStatistics() async {
  final response = await http.get(
    Uri.parse('$baseUrl/api/author/statistics'),
    headers: {
      'Authorization': 'Bearer $token',
      'Accept': 'application/json',
    },
  );
  
  if (response.statusCode == 200) {
    return json.decode(response.body)['data'];
  }
  throw Exception('Failed to load statistics');
}
```

---

## 📄 My Papers Endpoints

### 2. Get My Papers (Recent Papers)
Lấy danh sách bài báo của tác giả với phân trang và permissions.

**Endpoint:** `GET /api/my-papers`

**Query Parameters:**
- `status` (optional): Filter by status code (SUBMITTED, UNDER_REVIEW, ACCEPTED, REJECTED, WITHDRAWN)
- `conference_id` (optional): Filter by conference ID
- `per_page` (optional): Items per page (default: 15)
- `page` (optional): Page number (default: 1)
- `sort_by` (optional): Sort field (default: baibao.created_at)
- `sort_order` (optional): asc/desc (default: desc)

**Response:**
```json
{
  "status": "success",
  "message": "Danh sách bài báo của tôi",
  "data": {
    "current_page": 1,
    "data": [
      {
        "paper_id": 11,
        "title": "Hội thảo về vấn đề biến đổi khí hậu toàn cầu 2025",
        "abstract": "Abstract text...",
        "keywords": "climate, AI, machine learning",
        "created_at": "2025-11-07 15:30:00",
        "status_code": "SUBMITTED",
        "file_path": "papers/1/11_1730975400.pdf",
        "conference_title": "Hội thảo cung cấp nguồn nhân lực",
        "conference_id": 1,
        "deadline_submission": "2025-12-31 23:59:59",
        "deadline_camera_ready": null,
        "status_name": "Đã nộp",
        "can_edit": true,
        "edit_reason": "",
        "can_withdraw": true,
        "withdraw_reason": "",
        "formatted_created_at": "07/11/2025 15:30",
        "formatted_deadline": "31/12/2025"
      }
    ],
    "first_page_url": "http://localhost/api/my-papers?page=1",
    "from": 1,
    "last_page": 1,
    "last_page_url": "http://localhost/api/my-papers?page=1",
    "links": [...],
    "next_page_url": null,
    "path": "http://localhost/api/my-papers",
    "per_page": 15,
    "prev_page_url": null,
    "to": 7,
    "total": 7
  }
}
```

**Important Fields:**
- `can_edit`: Boolean - Có thể chỉnh sửa bài báo không
- `edit_reason`: String - Lý do không thể chỉnh sửa (nếu có)
- `can_withdraw`: Boolean - Có thể rút bài không
- `withdraw_reason`: String - Lý do không thể rút bài (nếu có)
- `formatted_created_at`: String - Ngày nộp đã format (dd/mm/yyyy HH:mm)
- `formatted_deadline`: String - Deadline đã format (dd/mm/yyyy)

**Flutter Usage:**
```dart
Future<PaginatedPapers> getMyPapers({
  String? status,
  int? conferenceId,
  int page = 1,
  int perPage = 15,
}) async {
  final queryParams = {
    'page': page.toString(),
    'per_page': perPage.toString(),
    if (status != null) 'status': status,
    if (conferenceId != null) 'conference_id': conferenceId.toString(),
  };
  
  final uri = Uri.parse('$baseUrl/api/my-papers')
      .replace(queryParameters: queryParams);
  
  final response = await http.get(
    uri,
    headers: {
      'Authorization': 'Bearer $token',
      'Accept': 'application/json',
    },
  );
  
  if (response.statusCode == 200) {
    final data = json.decode(response.body)['data'];
    return PaginatedPapers.fromJson(data);
  }
  throw Exception('Failed to load papers');
}
```

---

## 📝 Paper Detail Endpoint

### 3. Get Paper Details
Lấy chi tiết bài báo với authors, assignments, reviews và permissions.

**Endpoint:** `GET /api/papers/{id}`

**Response:**
```json
{
  "status": "success",
  "message": "Chi tiết bài báo",
  "data": {
    "paper": {
      "paper_id": 11,
      "conference_id": 1,
      "track_id": null,
      "submitter_id": 19,
      "status_code": "SUBMITTED",
      "title": "Hội thảo về vấn đề biến đổi khí hậu toàn cầu 2025",
      "abstract": "Abstract...",
      "keywords": "climate, AI",
      "file_path": "papers/1/11_1730975400.pdf",
      "withdrawal_reason": null,
      "current_version_id": null,
      "created_at": "2025-11-07 15:30:00",
      "conference_title": "Hội thảo cung cấp nguồn nhân lực",
      "deadline_submission": "2025-12-31 23:59:59",
      "deadline_camera_ready": null,
      "status_name": "Đã nộp"
    },
    "authors": [
      {
        "user_id": 19,
        "full_name": "Võ Nguyễn Phúc",
        "email": "vonp@gmail.com",
        "organization": null,
        "author_order": 1,
        "is_contact": 1
      }
    ],
    "assignments": [
      {
        "assignment_id": 22,
        "user_id": 14,
        "status": "ACCEPTED",
        "assigned_at": "2025-11-08 01:45:40",
        "review_submitted_at": null,
        "reviewer_name": "Nguyễn Văn Hùng"
      }
    ],
    "reviews": [],
    "permissions": {
      "can_edit": true,
      "edit_reason": "",
      "can_withdraw": true,
      "withdraw_reason": ""
    },
    "formatted_dates": {
      "created_at": "07/11/2025 15:30",
      "deadline_submission": "31/12/2025",
      "deadline_camera_ready": null
    }
  }
}
```

**Flutter Usage:**
```dart
Future<PaperDetail> getPaperDetail(int paperId) async {
  final response = await http.get(
    Uri.parse('$baseUrl/api/papers/$paperId'),
    headers: {
      'Authorization': 'Bearer $token',
      'Accept': 'application/json',
    },
  );
  
  if (response.statusCode == 200) {
    return PaperDetail.fromJson(json.decode(response.body)['data']);
  } else if (response.statusCode == 404) {
    throw Exception('Paper not found');
  }
  throw Exception('Failed to load paper detail');
}
```

---

## ✏️ Edit Paper Endpoint

### 4. Update Paper
Chỉnh sửa thông tin bài báo (có kiểm tra permissions).

**Endpoint:** `PUT /api/papers/{id}`

**Request Body:**
```json
{
  "title": "Updated Title",
  "abstract": "Updated abstract",
  "keywords": "updated, keywords",
  "conference_id": 1,
  "track_id": null
}
```

**Response (Success):**
```json
{
  "status": "success",
  "message": "Cập nhật bài báo thành công",
  "data": {
    "paper_id": 11,
    "title": "Updated Title",
    "conference_title": "Conference Name",
    "status_name": "Đã nộp",
    ...
  }
}
```

**Response (Permission Denied):**
```json
{
  "status": "error",
  "message": "Đã quá hạn nộp bài hoặc bài đang được phản biện."
}
```

**Flutter Usage:**
```dart
Future<void> updatePaper(int paperId, Map<String, dynamic> data) async {
  final response = await http.put(
    Uri.parse('$baseUrl/api/papers/$paperId'),
    headers: {
      'Authorization': 'Bearer $token',
      'Content-Type': 'application/json',
      'Accept': 'application/json',
    },
    body: json.encode(data),
  );
  
  if (response.statusCode == 200) {
    return;
  } else if (response.statusCode == 403) {
    final error = json.decode(response.body)['message'];
    throw Exception(error);
  }
  throw Exception('Failed to update paper');
}
```

---

## 🗑️ Withdraw Paper Endpoint

### 5. Withdraw Paper
Rút bài báo (có kiểm tra permissions).

**Endpoint:** `POST /api/papers/{id}/withdraw`

**Request Body:**
```json
{
  "reason": "Personal reason for withdrawal (optional)"
}
```

**Response (Success):**
```json
{
  "status": "success",
  "message": "Rút bài báo thành công"
}
```

**Response (Permission Denied):**
```json
{
  "status": "error",
  "message": "Không thể rút bài sau khi có kết quả phản biện."
}
```

**Flutter Usage:**
```dart
Future<void> withdrawPaper(int paperId, {String? reason}) async {
  final response = await http.post(
    Uri.parse('$baseUrl/api/papers/$paperId/withdraw'),
    headers: {
      'Authorization': 'Bearer $token',
      'Content-Type': 'application/json',
      'Accept': 'application/json',
    },
    body: json.encode({
      if (reason != null) 'reason': reason,
    }),
  );
  
  if (response.statusCode == 200) {
    return;
  } else if (response.statusCode == 403) {
    final error = json.decode(response.body)['message'];
    throw Exception(error);
  }
  throw Exception('Failed to withdraw paper');
}
```

---

## 📥 Download Paper Endpoint

### 6. Download Paper File
Tải file PDF của bài báo.

**Endpoint:** `GET /api/papers/{id}/download`

**Response:** Binary PDF file

**Flutter Usage:**
```dart
Future<void> downloadPaper(int paperId, String savePath) async {
  final response = await http.get(
    Uri.parse('$baseUrl/api/papers/$paperId/download'),
    headers: {
      'Authorization': 'Bearer $token',
    },
  );
  
  if (response.statusCode == 200) {
    final file = File(savePath);
    await file.writeAsBytes(response.bodyBytes);
    return;
  }
  throw Exception('Failed to download paper');
}
```

---

## 🔐 Permission Rules

### Edit Permission Logic:
1. **TRƯỚC deadline nộp bài** + status (DRAFT/SUBMITTED) → ✅ Cho phép
2. **SAU deadline** hoặc đang review → ❌ Không cho phép
3. **Đã có reviewer hoàn thành review** → ❌ Không cho phép
4. **Status = REJECTED** → ❌ Không cho phép
5. **Status = ACCEPTED** + trong thời hạn camera-ready → ✅ Cho phép
6. **Status = ACCEPTED** + quá hạn camera-ready → ❌ Không cho phép

### Withdraw Permission Logic:
1. **TRƯỚC deadline nộp bài** + status (DRAFT/SUBMITTED) → ✅ Cho phép
2. **SAU deadline** hoặc đang review → ❌ Không cho phép
3. **Đã có reviewer hoàn thành review** → ❌ Không cho phép
4. **Có kết quả review** (ACCEPTED/REJECTED) → ❌ Không cho phép

---

## 🎨 Flutter UI Implementation Example

### Statistics Dashboard Card:
```dart
class StatisticsCard extends StatelessWidget {
  final Map<String, dynamic> stats;
  
  @override
  Widget build(BuildContext context) {
    return GridView.count(
      crossAxisCount: 2,
      children: [
        _buildStatCard('Tổng số', stats['total'], Colors.purple),
        _buildStatCard('Đang duyệt', stats['under_review'], Colors.yellow),
        _buildStatCard('Chấp nhận', stats['accepted'], Colors.green),
        _buildStatCard('Từ chối', stats['rejected'], Colors.red),
      ],
    );
  }
  
  Widget _buildStatCard(String label, int value, Color color) {
    return Card(
      color: color.withOpacity(0.2),
      child: Column(
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          Text(
            value.toString(),
            style: TextStyle(fontSize: 32, fontWeight: FontWeight.bold, color: color),
          ),
          Text(label, style: TextStyle(fontSize: 14)),
        ],
      ),
    );
  }
}
```

### Papers List with Permissions:
```dart
class PaperListItem extends StatelessWidget {
  final Paper paper;
  
  @override
  Widget build(BuildContext context) {
    return ListTile(
      title: Text(paper.title),
      subtitle: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text('Hội thảo: ${paper.conferenceTitle}'),
          Text('Ngày nộp: ${paper.formattedCreatedAt}'),
          _buildStatusChip(paper.statusCode),
        ],
      ),
      trailing: Row(
        mainAxisSize: MainAxisSize.min,
        children: [
          IconButton(
            icon: Icon(Icons.visibility),
            onPressed: () => _viewPaper(paper.paperId),
          ),
          if (paper.canEdit)
            IconButton(
              icon: Icon(Icons.edit),
              onPressed: () => _editPaper(paper.paperId),
            ),
          if (paper.canWithdraw)
            IconButton(
              icon: Icon(Icons.delete),
              color: Colors.red,
              onPressed: () => _withdrawPaper(paper.paperId),
            ),
        ],
      ),
    );
  }
}
```

---

## ❌ Error Handling

**Common Error Responses:**

```json
// 401 Unauthorized
{
  "message": "Unauthenticated."
}

// 403 Forbidden (Permission denied)
{
  "status": "error",
  "message": "Đã quá hạn nộp bài hoặc bài đang được phản biện."
}

// 404 Not Found
{
  "status": "error",
  "message": "Không tìm thấy bài báo hoặc bạn không có quyền truy cập."
}

// 422 Validation Error
{
  "status": "error",
  "message": "Dữ liệu không hợp lệ",
  "errors": {
    "title": ["The title field is required."],
    "abstract": ["The abstract field is required."]
  }
}

// 500 Server Error
{
  "status": "error",
  "message": "Lỗi khi lấy danh sách bài báo: [error details]"
}
```

---

## 📋 Status Codes Reference

| Status Code | Status Name | Meaning |
|------------|-------------|---------|
| DRAFT | Nháp | Draft paper (not submitted yet) |
| SUBMITTED | Đã nộp | Submitted for review |
| UNDER_REVIEW | Đang phản biện | Under peer review |
| ACCEPTED | Chấp nhận | Accepted for publication |
| REJECTED | Từ chối | Rejected |
| WITHDRAWN | Đã rút | Withdrawn by author |

---

## 🔧 Testing with cURL

```bash
# 1. Get statistics
curl -X GET "http://localhost:8000/api/author/statistics" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Accept: application/json"

# 2. Get my papers (page 1, 10 items)
curl -X GET "http://localhost:8000/api/my-papers?per_page=10&page=1" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Accept: application/json"

# 3. Get paper detail
curl -X GET "http://localhost:8000/api/papers/11" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Accept: application/json"

# 4. Update paper
curl -X PUT "http://localhost:8000/api/papers/11" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"title":"Updated Title","abstract":"Updated abstract"}'

# 5. Withdraw paper
curl -X POST "http://localhost:8000/api/papers/11/withdraw" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"reason":"Personal reason"}'

# 6. Download paper
curl -X GET "http://localhost:8000/api/papers/11/download" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  --output paper.pdf
```

---

## ✅ Summary

**Đã cập nhật `Api\PaperController` với logic từ `Author\PaperController` (web):**

✅ Thêm helper methods: `canEditPaper()`, `canWithdrawPaper()`, `hasCompletedReviews()`  
✅ Fix import typo: `use App\Models\PhienBanBaiBao;` (removed duplicate Models\)  
✅ Fix validation: `exists:tieuban,track_id` (table name instead of Model name)  
✅ Add `authorStatistics()` endpoint: GET /api/author/statistics  
✅ Update `myPapers()`: Thêm permissions (can_edit, can_withdraw) và formatted dates  
✅ Update `show()`: Thêm permissions, authors, assignments, reviews  
✅ Update `update()`: Check permissions dựa trên deadline và review status  
✅ Update `withdraw()`: Check permissions và lưu reason  
✅ Add route: POST /api/papers/{id}/withdraw  
✅ Add route: GET /api/author/statistics  

**Tất cả chức năng giống với Author\PaperController (web) và ready cho Flutter app!** 🎉
