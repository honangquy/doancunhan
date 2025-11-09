# 📱 Chair API Documentation - Flutter Integration Guide

## 🎯 Overview
API endpoints cho Chair (Chủ tịch hội thảo) để quản lý quy trình phản biện trên app Flutter.

**Base URL**: `http://127.0.0.1:8000/api`  
**Authentication**: Bearer Token (JWT)  
**Role Required**: `CHAIR`

---

## 📊 1. Dashboard Statistics

### **GET** `/api/chair/dashboard`

Lấy thống kê tổng quan cho dashboard Chair.

**Headers**:
```
Authorization: Bearer YOUR_JWT_TOKEN
Accept: application/json
```

**Response** (200 OK):
```json
{
  "status": "success",
  "message": "Dashboard statistics",
  "data": {
    "conferences": [
      {
        "conference_id": 1,
        "title": "Hội thảo CNTT 2025",
        "year": 2025,
        "deadline_submission": "2025-12-31"
      }
    ],
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
    "recent_papers": [
      {
        "paper_id": 123,
        "title": "AI in Healthcare",
        "conference_name": "Hội thảo CNTT 2025",
        "author_name": "Nguyễn Văn A",
        "status_code": "UNDER_REVIEW",
        "status_name": "Đang phản biện",
        "created_at": "2025-11-01 10:30:00",
        "reviews_total": 3,
        "reviews_completed": 1
      }
    ],
    "pending_actions": [
      {
        "type": "assign_reviewers",
        "paper_id": 125,
        "title": "Machine Learning Paper",
        "message": "Cần phân công phản biện viên",
        "priority": "high"
      },
      {
        "type": "make_decision",
        "paper_id": 120,
        "title": "Deep Learning Paper",
        "message": "Sẵn sàng để ra quyết định",
        "priority": "medium"
      }
    ]
  }
}
```

**Flutter Usage**:
```dart
Future<Map<String, dynamic>> getChairDashboard() async {
  final response = await dio.get(
    '/chair/dashboard',
    options: Options(
      headers: {'Authorization': 'Bearer $token'},
    ),
  );
  return response.data['data'];
}
```

**UI Implementation**:
```dart
// Statistics Cards
Row(
  children: [
    StatCard(
      title: 'Tổng bài báo',
      value: stats['total_submissions'].toString(),
      icon: Icons.article,
      color: Colors.blue,
    ),
    StatCard(
      title: 'Đang phản biện',
      value: stats['papers_under_review'].toString(),
      icon: Icons.rate_review,
      color: Colors.orange,
    ),
    StatCard(
      title: 'Chờ quyết định',
      value: stats['pending_decisions'].toString(),
      icon: Icons.pending_actions,
      color: Colors.red,
    ),
  ],
)

// Pending Actions List
ListView.builder(
  itemCount: pendingActions.length,
  itemBuilder: (context, index) {
    final action = pendingActions[index];
    return PriorityCard(
      type: action['type'],
      title: action['title'],
      message: action['message'],
      priority: action['priority'],
      onTap: () => navigateToPaper(action['paper_id']),
    );
  },
)
```

---

## 📄 2. Paper Management

### **GET** `/api/chair/papers`

Lấy danh sách bài báo với filters và pagination.

**Query Parameters**:
- `conference_id` (optional): Filter by conference
- `status` (optional): Filter by status (SUBMITTED, UNDER_REVIEW, REVIEWED, ACCEPTED, REJECTED)
- `search` (optional): Search in title, author, keywords
- `page` (optional, default: 1): Page number
- `per_page` (optional, default: 20): Items per page

**Example Request**:
```
GET /api/chair/papers?conference_id=1&status=UNDER_REVIEW&page=1&per_page=20
```

**Response** (200 OK):
```json
{
  "status": "success",
  "message": "Danh sách bài báo",
  "data": {
    "papers": [
      {
        "paper_id": 123,
        "title": "AI in Healthcare Systems",
        "keywords": "AI, Healthcare, Machine Learning",
        "conference_name": "Hội thảo CNTT 2025",
        "conference_id": 1,
        "author_name": "Nguyễn Văn A",
        "status_code": "UNDER_REVIEW",
        "status_name": "Đang phản biện",
        "created_at": "2025-11-01 10:30:00",
        "reviewers": {
          "assigned": 3,
          "accepted": 2,
          "declined": 0,
          "pending": 1,
          "completed": 1,
          "avg_score": 7.5,
          "list": [
            {
              "full_name": "TS. Trần Văn B",
              "status": "ACCEPTED"
            },
            {
              "full_name": "PGS. Lê Thị C",
              "status": "PENDING"
            }
          ]
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
}
```

**Flutter Usage**:
```dart
Future<PaginatedPapers> getChairPapers({
  int? conferenceId,
  String? status,
  String? search,
  int page = 1,
  int perPage = 20,
}) async {
  final queryParams = <String, dynamic>{
    'page': page,
    'per_page': perPage,
  };
  
  if (conferenceId != null) queryParams['conference_id'] = conferenceId;
  if (status != null) queryParams['status'] = status;
  if (search != null && search.isNotEmpty) queryParams['search'] = search;
  
  final response = await dio.get(
    '/chair/papers',
    queryParameters: queryParams,
    options: Options(headers: {'Authorization': 'Bearer $token'}),
  );
  
  return PaginatedPapers.fromJson(response.data['data']);
}
```

**UI Implementation**:
```dart
// Filters
Row(
  children: [
    DropdownButton<int>(
      hint: Text('Hội thảo'),
      value: selectedConferenceId,
      items: conferences.map((conf) => 
        DropdownMenuItem(value: conf.id, child: Text(conf.title))
      ).toList(),
      onChanged: (val) => setState(() => selectedConferenceId = val),
    ),
    DropdownButton<String>(
      hint: Text('Trạng thái'),
      value: selectedStatus,
      items: ['SUBMITTED', 'UNDER_REVIEW', 'REVIEWED', 'ACCEPTED', 'REJECTED']
        .map((s) => DropdownMenuItem(value: s, child: Text(s)))
        .toList(),
      onChanged: (val) => setState(() => selectedStatus = val),
    ),
  ],
)

// Paper List with Reviewer Info
ListView.builder(
  itemCount: papers.length,
  itemBuilder: (context, index) {
    final paper = papers[index];
    return PaperCard(
      title: paper.title,
      author: paper.authorName,
      status: paper.statusName,
      reviewersAssigned: paper.reviewers.assigned,
      reviewersCompleted: paper.reviewers.completed,
      avgScore: paper.reviewers.avgScore,
      onTap: () => navigateToPaperDetail(paper.paperId),
    );
  },
)
```

---

### **GET** `/api/chair/papers/{id}`

Lấy chi tiết bài báo với reviews đầy đủ.

**Response** (200 OK):
```json
{
  "status": "success",
  "message": "Chi tiết bài báo",
  "data": {
    "paper": {
      "paper_id": 123,
      "title": "AI in Healthcare Systems",
      "abstract": "This paper discusses...",
      "keywords": "AI, Healthcare, ML",
      "status_code": "REVIEWED",
      "status_name": "Đã phản biện",
      "conference_id": 1,
      "conference_name": "Hội thảo CNTT 2025",
      "track_name": "AI Track",
      "file_path": "papers/123_paper.pdf",
      "created_at": "2025-11-01 10:30:00"
    },
    "authors": [
      {
        "author_name": "Nguyễn Văn A",
        "author_email": "nguyenvana@gmail.com",
        "author_organization": "HUIT",
        "is_contact": 1,
        "author_order": 1
      }
    ],
    "assignments": [
      {
        "assignment_id": 45,
        "user_id": 20,
        "reviewer_name": "TS. Trần Văn B",
        "reviewer_email": "tranvanb@gmail.com",
        "status": "ACCEPTED",
        "assigned_at": "2025-11-02 09:00:00",
        "responded_at": "2025-11-02 14:30:00",
        "review_submitted_at": "2025-11-05 16:45:00",
        "review_id": 78,
        "total_score": 8,
        "recommendation_code": "ACCEPT"
      }
    ],
    "reviews": [
      {
        "review_id": 78,
        "reviewer_name": "TS. Trần Văn B",
        "total_score": 8,
        "recommendation_code": "ACCEPT",
        "detailed_comments": "The paper presents a novel approach...",
        "comments_to_author": "Please expand section 3 with more examples",
        "comments_to_chair": "Recommend acceptance with minor revisions",
        "submitted_at": "2025-11-05 16:45:00",
        "score_novelty": 8,
        "score_relevance": 9,
        "score_technical_quality": 8,
        "score_presentation": 8,
        "score_references": 7
      }
    ]
  }
}
```

**Flutter Usage**:
```dart
Future<PaperDetail> getPaperDetail(int paperId) async {
  final response = await dio.get(
    '/chair/papers/$paperId',
    options: Options(headers: {'Authorization': 'Bearer $token'}),
  );
  return PaperDetail.fromJson(response.data['data']);
}
```

**UI Implementation**:
```dart
// Paper Detail Screen
Scaffold(
  appBar: AppBar(title: Text(paper.title)),
  body: SingleChildScrollView(
    child: Column(
      children: [
        // Paper Info Card
        PaperInfoCard(paper: paper, authors: authors),
        
        // Review Progress
        ReviewProgressCard(
          assigned: assignments.length,
          completed: reviews.length,
          avgScore: calculateAvgScore(reviews),
        ),
        
        // Reviews List
        ...reviews.map((review) => ReviewCard(
          reviewerName: review.reviewerName,
          score: review.overallScore,
          recommendation: review.recommendation,
          strengths: review.strengths,
          weaknesses: review.weaknesses,
          comments: review.commentsToAuthors,
          onExpand: () => showFullReview(review),
        )),
        
        // Action Buttons
        if (paper.statusCode == 'REVIEWED')
          Row(
            children: [
              ElevatedButton.icon(
                icon: Icon(Icons.check_circle),
                label: Text('Chấp nhận'),
                style: ElevatedButton.styleFrom(backgroundColor: Colors.green),
                onPressed: () => makeDecision('ACCEPTED'),
              ),
              ElevatedButton.icon(
                icon: Icon(Icons.cancel),
                label: Text('Từ chối'),
                style: ElevatedButton.styleFrom(backgroundColor: Colors.red),
                onPressed: () => makeDecision('REJECTED'),
              ),
            ],
          ),
      ],
    ),
  ),
)
```

---

## 👥 3. Reviewer Management

### **GET** `/api/chair/papers/{id}/available-reviewers`

Lấy danh sách phản biện viên có thể phân công.

**Response** (200 OK):
```json
{
  "status": "success",
  "message": "Danh sách phản biện viên khả dụng",
  "data": {
    "reviewers": [
      {
        "user_id": 20,
        "full_name": "TS. Trần Văn B",
        "email": "tranvanb@gmail.com",
        "organization": "HUIT",
        "current_assignments": 3
      }
    ]
  }
}
```

**Flutter Usage**:
```dart
Future<List<Reviewer>> getAvailableReviewers(int paperId) async {
  final response = await dio.get(
    '/chair/papers/$paperId/available-reviewers',
    options: Options(headers: {'Authorization': 'Bearer $token'}),
  );
  
  final List<dynamic> data = response.data['data']['reviewers'];
  return data.map((json) => Reviewer.fromJson(json)).toList();
}
```

---

### **POST** `/api/chair/papers/{id}/assign-reviewer`

Phân công phản biện viên cho bài báo.

**Request Body**:
```json
{
  "reviewer_id": 20,
  "deadline": "2025-11-30"
}
```

**Response** (200 OK):
```json
{
  "status": "success",
  "message": "Phân công phản biện viên thành công",
  "data": {
    "assignment_id": 45
  }
}
```

**Flutter Usage**:
```dart
Future<void> assignReviewer({
  required int paperId,
  required int reviewerId,
  DateTime? deadline,
}) async {
  final response = await dio.post(
    '/chair/papers/$paperId/assign-reviewer',
    data: {
      'reviewer_id': reviewerId,
      if (deadline != null) 'deadline': deadline.toIso8601String(),
    },
    options: Options(headers: {'Authorization': 'Bearer $token'}),
  );
  
  if (response.data['status'] == 'success') {
    showSuccessSnackbar('Phân công thành công');
  }
}
```

**UI Implementation**:
```dart
// Assign Reviewer Dialog
showDialog(
  context: context,
  builder: (context) => AlertDialog(
    title: Text('Phân công phản biện viên'),
    content: Column(
      mainAxisSize: MainAxisSize.min,
      children: [
        DropdownButton<int>(
          hint: Text('Chọn phản biện viên'),
          value: selectedReviewerId,
          items: availableReviewers.map((reviewer) => 
            DropdownMenuItem(
              value: reviewer.userId,
              child: ListTile(
                title: Text(reviewer.fullName),
                subtitle: Text('${reviewer.currentAssignments} bài đang phụ trách'),
              ),
            )
          ).toList(),
          onChanged: (val) => setState(() => selectedReviewerId = val),
        ),
        DatePicker(
          label: 'Deadline phản biện',
          onDateSelected: (date) => reviewDeadline = date,
        ),
      ],
    ),
    actions: [
      TextButton(
        child: Text('Hủy'),
        onPressed: () => Navigator.pop(context),
      ),
      ElevatedButton(
        child: Text('Phân công'),
        onPressed: () async {
          await assignReviewer(
            paperId: paperId,
            reviewerId: selectedReviewerId!,
            deadline: reviewDeadline,
          );
          Navigator.pop(context);
          refreshPaperDetail();
        },
      ),
    ],
  ),
);
```

---

### **DELETE** `/api/chair/assignments/{id}`

Xóa phân công phản biện viên.

**Response** (200 OK):
```json
{
  "status": "success",
  "message": "Xóa phân công thành công"
}
```

**Error** (422):
```json
{
  "status": "error",
  "message": "Không thể xóa phân công đã có phản biện hoàn thành"
}
```

**Flutter Usage**:
```dart
Future<void> removeAssignment(int assignmentId) async {
  try {
    await dio.delete(
      '/chair/assignments/$assignmentId',
      options: Options(headers: {'Authorization': 'Bearer $token'}),
    );
    showSuccessSnackbar('Xóa phân công thành công');
  } on DioException catch (e) {
    if (e.response?.statusCode == 422) {
      showErrorSnackbar(e.response?.data['message']);
    }
  }
}
```

---

## ✅ 4. Decision Making

### **POST** `/api/chair/papers/{id}/decision`

Ra quyết định chấp nhận/từ chối bài báo.

**Request Body**:
```json
{
  "decision": "ACCEPTED",
  "comments": "Good paper with minor revisions needed"
}
```

**Validation**:
- `decision`: Required, must be `ACCEPTED` or `REJECTED`
- `comments`: Optional string
- Paper must have status `REVIEWED`

**Response** (200 OK):
```json
{
  "status": "success",
  "message": "Ra quyết định thành công",
  "data": {
    "decision": "ACCEPTED",
    "paper_id": 123
  }
}
```

**Flutter Usage**:
```dart
Future<void> makeDecision({
  required int paperId,
  required String decision, // 'ACCEPTED' or 'REJECTED'
  String? comments,
}) async {
  final response = await dio.post(
    '/chair/papers/$paperId/decision',
    data: {
      'decision': decision,
      if (comments != null) 'comments': comments,
    },
    options: Options(headers: {'Authorization': 'Bearer $token'}),
  );
  
  if (response.data['status'] == 'success') {
    showSuccessSnackbar('Quyết định đã được lưu');
    navigateBackToPaperList();
  }
}
```

**UI Implementation**:
```dart
// Decision Dialog
showDialog(
  context: context,
  builder: (context) => AlertDialog(
    title: Text('Quyết định bài báo'),
    content: Column(
      mainAxisSize: MainAxisSize.min,
      children: [
        Text('Bạn muốn chấp nhận hay từ chối bài báo này?'),
        SizedBox(height: 16),
        TextField(
          controller: commentsController,
          decoration: InputDecoration(
            labelText: 'Nhận xét (tùy chọn)',
            border: OutlineInputBorder(),
          ),
          maxLines: 3,
        ),
      ],
    ),
    actions: [
      TextButton(
        child: Text('Hủy'),
        onPressed: () => Navigator.pop(context),
      ),
      ElevatedButton(
        child: Text('Từ chối'),
        style: ElevatedButton.styleFrom(backgroundColor: Colors.red),
        onPressed: () async {
          await makeDecision(
            paperId: paperId,
            decision: 'REJECTED',
            comments: commentsController.text,
          );
          Navigator.pop(context);
        },
      ),
      ElevatedButton(
        child: Text('Chấp nhận'),
        style: ElevatedButton.styleFrom(backgroundColor: Colors.green),
        onPressed: () async {
          await makeDecision(
            paperId: paperId,
            decision: 'ACCEPTED',
            comments: commentsController.text,
          );
          Navigator.pop(context);
        },
      ),
    ],
  ),
);
```

---

## 📊 5. Statistics & Reports

### **GET** `/api/chair/conferences/{id}/review-statistics`

Lấy thống kê phản biện cho hội thảo.

**Response** (200 OK):
```json
{
  "status": "success",
  "message": "Thống kê phản biện",
  "data": {
    "papers_by_status": [
      {
        "status_code": "SUBMITTED",
        "status_name": "Đã nộp",
        "count": 10
      },
      {
        "status_code": "UNDER_REVIEW",
        "status_name": "Đang phản biện",
        "count": 12
      }
    ],
    "reviewer_performance": [
      {
        "user_id": 20,
        "full_name": "TS. Trần Văn B",
        "total_assigned": 5,
        "accepted": 4,
        "declined": 0,
        "pending": 1,
        "completed": 3
      }
    ],
    "scores_by_recommendation": [
      {
        "recommendation_code": "ACCEPT",
        "count": 15,
        "avg_score": 8.2
      },
      {
        "recommendation_code": "REJECT",
        "count": 3,
        "avg_score": 4.5
      }
    ]
  }
}
```

**Flutter Usage**:
```dart
Future<ReviewStatistics> getReviewStatistics(int conferenceId) async {
  final response = await dio.get(
    '/chair/conferences/$conferenceId/review-statistics',
    options: Options(headers: {'Authorization': 'Bearer $token'}),
  );
  return ReviewStatistics.fromJson(response.data['data']);
}
```

**UI Implementation with Charts**:
```dart
// Statistics Screen
Scaffold(
  appBar: AppBar(title: Text('Thống kê phản biện')),
  body: ListView(
    children: [
      // Paper Status Pie Chart
      PieChart(
        data: papersByStatus.map((s) => 
          PieChartData(label: s.statusName, value: s.count)
        ).toList(),
      ),
      
      // Reviewer Performance Table
      DataTable(
        columns: [
          DataColumn(label: Text('Phản biện viên')),
          DataColumn(label: Text('Phân công')),
          DataColumn(label: Text('Hoàn thành')),
          DataColumn(label: Text('Tỷ lệ')),
        ],
        rows: reviewerPerformance.map((r) => DataRow(
          cells: [
            DataCell(Text(r.fullName)),
            DataCell(Text(r.totalAssigned.toString())),
            DataCell(Text(r.completed.toString())),
            DataCell(Text('${(r.completed / r.totalAssigned * 100).toStringAsFixed(1)}%')),
          ],
        )).toList(),
      ),
      
      // Score Distribution Bar Chart
      BarChart(
        data: scoresByRecommendation.map((s) => 
          BarChartData(
            label: s.recommendation,
            value: s.avgScore,
            count: s.count,
          )
        ).toList(),
      ),
    ],
  ),
)
```

---

### **GET** `/api/chair/reviewers`

Lấy danh sách tất cả phản biện viên với performance metrics.

**Response** (200 OK):
```json
{
  "status": "success",
  "message": "Danh sách phản biện viên",
  "data": {
    "reviewers": [
      {
        "user_id": 20,
        "full_name": "TS. Trần Văn B",
        "email": "tranvanb@gmail.com",
        "organization": "HUIT",
        "statistics": {
          "total_assigned": 8,
          "accepted": 7,
          "declined": 1,
          "pending": 2,
          "completed": 5,
          "avg_score": 7.8
        }
      }
    ]
  }
}
```

**Flutter Usage**:
```dart
Future<List<ReviewerWithStats>> getReviewers() async {
  final response = await dio.get(
    '/chair/reviewers',
    options: Options(headers: {'Authorization': 'Bearer $token'}),
  );
  
  final List<dynamic> data = response.data['data']['reviewers'];
  return data.map((json) => ReviewerWithStats.fromJson(json)).toList();
}
```

**UI Implementation**:
```dart
// Reviewers Screen
ListView.builder(
  itemCount: reviewers.length,
  itemBuilder: (context, index) {
    final reviewer = reviewers[index];
    final stats = reviewer.statistics;
    
    return Card(
      child: ListTile(
        leading: CircleAvatar(
          child: Text(reviewer.fullName[0]),
        ),
        title: Text(reviewer.fullName),
        subtitle: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text(reviewer.email),
            Text('${stats.completed}/${stats.totalAssigned} hoàn thành'),
          ],
        ),
        trailing: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Text('Điểm TB', style: TextStyle(fontSize: 12)),
            Text(
              stats.avgScore?.toStringAsFixed(1) ?? 'N/A',
              style: TextStyle(
                fontSize: 18,
                fontWeight: FontWeight.bold,
                color: getScoreColor(stats.avgScore),
              ),
            ),
          ],
        ),
        onTap: () => showReviewerDetail(reviewer),
      ),
    );
  },
)
```

---

## 🔐 Error Handling

**Common Error Responses**:

**401 Unauthorized**:
```json
{
  "status": "error",
  "message": "Unauthenticated"
}
```

**403 Forbidden**:
```json
{
  "status": "error",
  "message": "Không có quyền truy cập"
}
```

**404 Not Found**:
```json
{
  "status": "error",
  "message": "Không tìm thấy bài báo hoặc bạn không có quyền truy cập"
}
```

**422 Validation Error**:
```json
{
  "status": "error",
  "message": "Dữ liệu không hợp lệ",
  "errors": {
    "reviewer_id": ["The reviewer_id field is required."]
  }
}
```

**500 Server Error**:
```json
{
  "status": "error",
  "message": "Lỗi khi lấy thống kê: Database connection failed"
}
```

**Flutter Error Handling**:
```dart
try {
  final result = await getChairDashboard();
  // Success
} on DioException catch (e) {
  if (e.response?.statusCode == 401) {
    // Token expired, redirect to login
    navigateToLogin();
  } else if (e.response?.statusCode == 403) {
    // No permission
    showErrorSnackbar('Bạn không có quyền Chair');
  } else if (e.response?.statusCode == 422) {
    // Validation error
    final errors = e.response?.data['errors'];
    showValidationErrors(errors);
  } else {
    // Generic error
    showErrorSnackbar('Đã xảy ra lỗi: ${e.message}');
  }
}
```

---

## 🧪 Testing with cURL

**1. Login first**:
```bash
curl -X POST http://127.0.0.1:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"chair@example.com","password":"password123"}'
```

**2. Get Dashboard**:
```bash
curl -X GET http://127.0.0.1:8000/api/chair/dashboard \
  -H "Authorization: Bearer YOUR_TOKEN"
```

**3. List Papers**:
```bash
curl -X GET "http://127.0.0.1:8000/api/chair/papers?status=UNDER_REVIEW&page=1" \
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

---

## 🎯 Complete Flutter Example

```dart
class ChairService {
  final Dio dio;
  final String baseUrl = 'http://127.0.0.1:8000/api';
  String? token;
  
  ChairService(this.dio);
  
  // Dashboard
  Future<DashboardData> getDashboard() async {
    final response = await dio.get(
      '$baseUrl/chair/dashboard',
      options: Options(headers: {'Authorization': 'Bearer $token'}),
    );
    return DashboardData.fromJson(response.data['data']);
  }
  
  // Papers
  Future<PaginatedPapers> getPapers({
    int? conferenceId,
    String? status,
    String? search,
    int page = 1,
  }) async {
    final response = await dio.get(
      '$baseUrl/chair/papers',
      queryParameters: {
        if (conferenceId != null) 'conference_id': conferenceId,
        if (status != null) 'status': status,
        if (search != null) 'search': search,
        'page': page,
      },
      options: Options(headers: {'Authorization': 'Bearer $token'}),
    );
    return PaginatedPapers.fromJson(response.data['data']);
  }
  
  // Assign Reviewer
  Future<void> assignReviewer(int paperId, int reviewerId, {DateTime? deadline}) async {
    await dio.post(
      '$baseUrl/chair/papers/$paperId/assign-reviewer',
      data: {
        'reviewer_id': reviewerId,
        if (deadline != null) 'deadline': deadline.toIso8601String(),
      },
      options: Options(headers: {'Authorization': 'Bearer $token'}),
    );
  }
  
  // Make Decision
  Future<void> makeDecision(int paperId, String decision, {String? comments}) async {
    await dio.post(
      '$baseUrl/chair/papers/$paperId/decision',
      data: {
        'decision': decision,
        if (comments != null) 'comments': comments,
      },
      options: Options(headers: {'Authorization': 'Bearer $token'}),
    );
  }
}
```

---

## 📝 Summary

**Endpoints Available**:
1. ✅ `GET /api/chair/dashboard` - Dashboard statistics
2. ✅ `GET /api/chair/papers` - List papers with filters
3. ✅ `GET /api/chair/papers/{id}` - Paper detail with reviews
4. ✅ `GET /api/chair/papers/{id}/available-reviewers` - Available reviewers
5. ✅ `POST /api/chair/papers/{id}/assign-reviewer` - Assign reviewer
6. ✅ `DELETE /api/chair/assignments/{id}` - Remove assignment
7. ✅ `POST /api/chair/papers/{id}/decision` - Make decision
8. ✅ `GET /api/chair/conferences/{id}/review-statistics` - Review statistics
9. ✅ `GET /api/chair/reviewers` - List reviewers with performance

**Key Features**:
- ✅ Dashboard với statistics và pending actions
- ✅ Quản lý bài báo với filters và pagination
- ✅ Phân công phản biện viên
- ✅ Xem chi tiết reviews
- ✅ Ra quyết định ACCEPT/REJECT
- ✅ Thống kê và báo cáo chi tiết
- ✅ Performance metrics cho reviewers

**Ready for Flutter Development!** 🚀
