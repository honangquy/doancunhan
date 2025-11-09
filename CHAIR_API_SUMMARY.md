# ✅ Chair API Implementation Summary

## 📋 Đã Hoàn Thành

### 🎯 API Controller: `App\Http\Controllers\Api\ChairController`

**Location**: `app/Http/Controllers/Api/ChairController.php`

**Chức năng chính**:
1. ✅ Dashboard với thống kê tổng quan
2. ✅ Quản lý bài báo (filters, pagination, reviewer info)
3. ✅ Chi tiết bài báo với reviews đầy đủ
4. ✅ Phân công phản biện viên
5. ✅ Xóa phân công
6. ✅ Ra quyết định ACCEPT/REJECT
7. ✅ Thống kê phản biện
8. ✅ Danh sách phản biện viên với performance metrics

---

## 🔗 API Endpoints (9 endpoints)

### 1. Dashboard & Overview
```
GET /api/chair/dashboard
```
- Statistics: total conferences, submissions, under review, reviewed, etc.
- Recent papers (10 mới nhất)
- Pending actions (needs reviewers, needs decision)

### 2. Paper Management  
```
GET /api/chair/papers
```
Query params: `conference_id`, `status`, `search`, `page`, `per_page`
- Pagination support
- Reviewer statistics per paper
- Filters by conference, status, search

```
GET /api/chair/papers/{id}
```
- Paper details
- Authors list
- Reviewer assignments
- Full reviews with scores

### 3. Reviewer Management
```
GET /api/chair/papers/{id}/available-reviewers
```
- List reviewers not assigned to paper
- Exclude paper authors
- Show current workload

```
POST /api/chair/papers/{id}/assign-reviewer
```
Body: `{ "reviewer_id": 20, "deadline": "2025-11-30" }`
- Assign reviewer to paper
- Auto update paper status to UNDER_REVIEW

```
DELETE /api/chair/assignments/{id}
```
- Remove reviewer assignment
- Prevent deletion if review completed

### 4. Decision Making
```
POST /api/chair/papers/{id}/decision
```
Body: `{ "decision": "ACCEPTED|REJECTED", "comments": "..." }`
- Accept or reject paper
- Requires paper status = REVIEWED

### 5. Statistics & Reports
```
GET /api/chair/conferences/{id}/review-statistics
```
- Papers by status
- Reviewer performance metrics
- Average scores by recommendation

```
GET /api/chair/reviewers
```
- All reviewers with statistics
- Total assigned, completed, avg score
- Performance tracking

---

## 📊 Dữ Liệu Trả Về

### Dashboard Response Structure
```json
{
  "conferences": [...],
  "statistics": {
    "total_conferences": 2,
    "total_submissions": 45,
    "papers_under_review": 12,
    "papers_reviewed": 8,
    "accepted_assignments": 30,
    "needs_reviewers": 5,
    "pending_decisions": 8,
    "decisions_made": 20
  },
  "recent_papers": [...],
  "pending_actions": [...]
}
```

### Paper List Response Structure
```json
{
  "papers": [
    {
      "paper_id": 123,
      "title": "...",
      "reviewers": {
        "assigned": 3,
        "accepted": 2,
        "declined": 0,
        "pending": 1,
        "completed": 1,
        "avg_score": 7.5,
        "list": [...]
      }
    }
  ],
  "pagination": {
    "current_page": 1,
    "total": 45,
    "per_page": 20,
    "last_page": 3
  }
}
```

### Paper Detail Response Structure
```json
{
  "paper": {...},
  "authors": [...],
  "assignments": [
    {
      "assignment_id": 45,
      "reviewer_name": "...",
      "status": "ACCEPTED",
      "review_id": 78,
      "total_score": 8,
      "recommendation_code": "ACCEPT"
    }
  ],
  "reviews": [
    {
      "review_id": 78,
      "reviewer_name": "...",
      "total_score": 8,
      "recommendation_code": "ACCEPT",
      "detailed_comments": "...",
      "comments_to_author": "...",
      "comments_to_chair": "...",
      "score_novelty": 8,
      "score_relevance": 9,
      "score_technical_quality": 8,
      "score_presentation": 8,
      "score_references": 7
    }
  ]
}
```

---

## 🔐 Authentication & Authorization

**Middleware**: `auth:api` (JWT Token)  
**Role Check**: Chair role verified via `vaitronguoidung` table

**Verification Logic**:
```php
// Verify chair has access to conference
$paper = DB::table('baibao as bb')
    ->join('vaitronguoidung as vt', function($join) use ($userId) {
        $join->on('bb.conference_id', '=', 'vt.conference_id')
             ->where('vt.user_id', '=', $userId)
             ->where('vt.role_code', '=', 'CHAIR');
    })
    ->where('bb.paper_id', $paperId)
    ->first();
```

---

## 🧪 Testing

### Test với cURL

**1. Login**:
```bash
curl -X POST http://127.0.0.1:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"chair@example.com","password":"password"}'
```

**2. Get Dashboard**:
```bash
curl http://127.0.0.1:8000/api/chair/dashboard \
  -H "Authorization: Bearer YOUR_TOKEN"
```

**3. List Papers with Filters**:
```bash
curl "http://127.0.0.1:8000/api/chair/papers?status=UNDER_REVIEW&page=1" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

**4. Assign Reviewer**:
```bash
curl -X POST http://127.0.0.1:8000/api/chair/papers/123/assign-reviewer \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"reviewer_id":20,"deadline":"2025-11-30"}'
```

**5. Make Decision**:
```bash
curl -X POST http://127.0.0.1:8000/api/chair/papers/123/decision \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"decision":"ACCEPTED","comments":"Good paper"}'
```

### Verify Routes
```bash
php artisan route:list --path=api/chair
```

Output:
```
DELETE     api/chair/assignments/{id}
GET|HEAD   api/chair/conferences/{id}/review-statistics
GET|HEAD   api/chair/dashboard
GET|HEAD   api/chair/papers
GET|HEAD   api/chair/papers/{id}
POST       api/chair/papers/{id}/assign-reviewer
GET|HEAD   api/chair/papers/{id}/available-reviewers
POST       api/chair/papers/{id}/decision
GET|HEAD   api/chair/reviewers
```

---

## 📱 Flutter Integration

### Service Class Example
```dart
class ChairService {
  final Dio dio;
  String? token;
  
  Future<DashboardData> getDashboard() async {
    final response = await dio.get('/chair/dashboard',
      options: Options(headers: {'Authorization': 'Bearer $token'}));
    return DashboardData.fromJson(response.data['data']);
  }
  
  Future<PaginatedPapers> getPapers({int? conferenceId, String? status}) async {
    final response = await dio.get('/chair/papers',
      queryParameters: {'conference_id': conferenceId, 'status': status},
      options: Options(headers: {'Authorization': 'Bearer $token'}));
    return PaginatedPapers.fromJson(response.data['data']);
  }
}
```

### Model Classes
```dart
class DashboardData {
  final List<Conference> conferences;
  final Statistics statistics;
  final List<Paper> recentPapers;
  final List<PendingAction> pendingActions;
  
  factory DashboardData.fromJson(Map<String, dynamic> json) {...}
}

class Paper {
  final int paperId;
  final String title;
  final ReviewerStats reviewers;
  
  factory Paper.fromJson(Map<String, dynamic> json) {...}
}
```

---

## 🎨 UI Screens Suggestion

### 1. Dashboard Screen
- Statistics cards (4 columns)
- Recent papers list
- Pending actions cards (high priority highlighted)

### 2. Papers List Screen  
- Filters: Conference dropdown, Status dropdown, Search bar
- Paper cards with reviewer progress bars
- Pull-to-refresh support
- Infinite scroll pagination

### 3. Paper Detail Screen
- Paper info card
- Authors list
- Reviewer assignments with status badges
- Reviews expandable cards
- Action buttons (Assign Reviewer, Make Decision)

### 4. Assign Reviewer Dialog
- Reviewer dropdown with workload info
- Deadline date picker
- Conflict of interest warning

### 5. Decision Dialog
- Accept/Reject buttons
- Comments text field
- Confirmation prompt

### 6. Statistics Screen
- Pie chart: Papers by status
- Bar chart: Average scores by recommendation
- Table: Reviewer performance

### 7. Reviewers Screen
- List with avatar, name, stats
- Sort by: completion rate, avg score
- Filter by performance level

---

## 📖 Documentation

**Full API documentation**: `FLUTTER_CHAIR_API.md`

Bao gồm:
- ✅ Detailed endpoint descriptions
- ✅ Request/response examples
- ✅ Flutter code samples
- ✅ UI implementation examples
- ✅ Error handling guide
- ✅ cURL test commands

---

## 🚀 Ready for Flutter Development!

**Tất cả API endpoints đã được implement và test**:
- ✅ 9 endpoints hoạt động
- ✅ Authentication & authorization
- ✅ Error handling
- ✅ Data validation
- ✅ Pagination support
- ✅ Performance metrics
- ✅ Complete documentation

**Next Steps cho Flutter Team**:
1. Tạo model classes từ JSON responses
2. Implement ChairService với Dio
3. Build UI screens theo design
4. Add state management (Provider/Riverpod/Bloc)
5. Test với real data từ API

---

## 📝 Notes

**Database Tables Used**:
- `baibao` - Papers
- `hoithao` - Conferences  
- `nguoidung` - Users
- `vaitronguoidung` - User roles
- `reviewer_assignments` - Reviewer assignments (14 columns)
- `phanbien` - Reviews (16 columns)
- `trangthaibaibao` - Paper statuses
- `tieuban` - Tracks
- `tacgiabaibao` - Paper authors (5 columns: paper_id, user_id, author_order, is_contact, organization)

**⚠️ Important Database Schema Notes**:

**Table: `phanbien` (Reviews)**
- ✅ `total_score` (NOT `overall_score`)
- ✅ `recommendation_code` (NOT `recommendation`)
- ✅ `comment_author` (NOT `comments_to_authors`)
- ✅ `comment_chair` (NOT `comments_to_chair`)
- ✅ `detailed_comments` (general comments)
- ✅ Individual scores: `score_novelty`, `score_relevance`, `score_technical_quality`, `score_presentation`, `score_references`
- ❌ NO `confidence_level`, `strengths`, `weaknesses` columns

**Table: `reviewer_assignments`**
- ✅ `assigned_at`, `responded_at`, `review_submitted_at`
- ❌ NO `review_deadline` column (removed from insert)

**Table: `tacgiabaibao` (Paper Authors)**
- ✅ Join with `nguoidung` to get: `full_name` → `author_name`, `email` → `author_email`
- ✅ Direct columns: `organization` → `author_organization`, `is_contact`, `author_order`
- ❌ NO direct `author_name`, `author_email` columns in this table

**Key Features**:
- Realtime statistics
- Multi-conference support
- Advanced filtering
- Workload balancing
- Performance tracking
- Decision workflow
- Audit trail ready

**Performance Considerations**:
- Pagination for large datasets
- Efficient joins with indexes
- Cached statistics (can be added)
- Lazy loading for reviews

---

**Created**: November 9, 2025  
**Status**: ✅ Production Ready  
**API Version**: 1.0
