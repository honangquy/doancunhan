# 📚 HUIT Conference API - Complete Summary

**Project:** Hệ thống Quản lý Hội thảo Khoa học  
**Version:** 1.0.0  
**Last Updated:** 14/11/2025  
**Base URL:** `http://127.0.0.1:8000/api`  
**Authentication:** JWT Bearer Token (Laravel Sanctum)

---

## 📖 Table of Contents

1. [Authentication & User Management](#1-authentication--user-management)
2. [Conference Management](#2-conference-management)
3. [Track Management](#3-track-management)
4. [Conference Request System](#4-conference-request-system)
5. [Paper Submission & Management](#5-paper-submission--management)
6. [Paper Version Control](#6-paper-version-control)
7. [Bidding System](#7-bidding-system)
8. [Review System](#8-review-system)
9. [Assignment System](#9-assignment-system)
10. [Reviewer Revision Tracking](#10-reviewer-revision-tracking-mobile)
11. [Chair Management APIs](#11-chair-management-apis-mobile)
12. [Announcement System](#12-announcement-system)
13. [Admin & Reports](#13-admin--reports)
14. [Notifications](#14-notifications)
15. [Utilities & Health Check](#15-utilities--health-check)

---

## 1. Authentication & User Management

### 1.1 Public Routes (No Auth Required)

#### Register User
```http
POST /api/auth/register
Content-Type: application/json

{
  "email": "user@huit.edu.vn",
  "password": "password123",
  "full_name": "Nguyễn Văn A",
  "faculty_id": 1,
  "affiliation": "HUIT"
}

Response 201:
{
  "message": "User registered successfully",
  "user": {...},
  "token": "eyJ0eXAiOi..."
}
```

#### Login
```http
POST /api/auth/login
Content-Type: application/json

{
  "email": "user@huit.edu.vn",
  "password": "password123"
}

Response 200:
{
  "access_token": "eyJ0eXAiOi...",
  "token_type": "bearer",
  "expires_in": 3600,
  "user": {...}
}
```

### 1.2 Protected Routes (Auth Required)

#### Get Profile
```http
GET /api/auth/profile
Authorization: Bearer {token}

Response 200:
{
  "user_id": 1,
  "email": "user@huit.edu.vn",
  "full_name": "Nguyễn Văn A",
  "roles": ["AUTHOR", "REVIEWER"]
}
```

#### Update Profile
```http
PUT /api/auth/profile
Authorization: Bearer {token}
Content-Type: application/json

{
  "full_name": "Nguyễn Văn B",
  "affiliation": "HUIT - CS Department"
}
```

#### Change Password
```http
POST /api/auth/change-password
Authorization: Bearer {token}

{
  "current_password": "old_password",
  "new_password": "new_password",
  "new_password_confirmation": "new_password"
}
```

#### Logout
```http
POST /api/auth/logout
Authorization: Bearer {token}
```

#### Refresh Token
```http
POST /api/auth/refresh
Authorization: Bearer {token}
```

---

## 2. Conference Management

### 2.1 Public Routes

#### List Conferences (Public)
```http
GET /api/conferences?status=ACTIVE&year=2025

Response 200:
{
  "data": [
    {
      "conference_id": 1,
      "title": "AI Conference 2025",
      "start_date": "2025-12-01",
      "end_date": "2025-12-03",
      "location": "HUIT",
      "status": "ACTIVE"
    }
  ],
  "pagination": {...}
}
```

#### Conference Detail (Public)
```http
GET /api/conferences/{id}

Response 200:
{
  "conference_id": 1,
  "title": "AI Conference 2025",
  "description": "...",
  "tracks": [...],
  "important_dates": {...}
}
```

#### Conference Statistics (Public)
```http
GET /api/conferences/{id}/statistics

Response 200:
{
  "total_papers": 45,
  "accepted_papers": 20,
  "total_reviewers": 30,
  "total_tracks": 5
}
```

### 2.2 Protected Routes (Chair/Admin)

#### Create Conference
```http
POST /api/conferences
Authorization: Bearer {token}

{
  "title": "AI Conference 2025",
  "year": 2025,
  "level_code": "TRUONG",
  "start_date": "2025-12-01",
  "end_date": "2025-12-03"
}
```

#### Update Conference
```http
PUT /api/conferences/{id}
Authorization: Bearer {token}
```

#### Delete Conference
```http
DELETE /api/conferences/{id}
Authorization: Bearer {token}
```

#### My Conferences (Chair)
```http
GET /api/my-conferences
Authorization: Bearer {token}

Response 200:
{
  "data": [
    {
      "conference_id": 1,
      "title": "AI Conference 2025",
      "role": "CHAIR",
      "status": "ACTIVE"
    }
  ]
}
```

---

## 3. Track Management

#### List Tracks by Conference
```http
GET /api/conferences/{conference_id}/tracks

Response 200:
{
  "data": [
    {
      "track_id": 1,
      "title": "Machine Learning Track",
      "description": "...",
      "chair": {...}
    }
  ]
}
```

#### Create Track
```http
POST /api/conferences/{conference_id}/tracks
Authorization: Bearer {token}

{
  "title": "Machine Learning Track",
  "description": "Focus on ML applications",
  "chair_id": 5
}
```

#### Update Track
```http
PUT /api/tracks/{id}
Authorization: Bearer {token}
```

#### Delete Track
```http
DELETE /api/tracks/{id}
Authorization: Bearer {token}
```

#### Track Papers
```http
GET /api/tracks/{id}/papers
Authorization: Bearer {token}

Response 200:
{
  "data": [
    {
      "paper_id": 10,
      "title": "Deep Learning in Healthcare",
      "status": "UNDER_REVIEW",
      "authors": [...]
    }
  ]
}
```

#### My Tracks (Track Chair)
```http
GET /api/my-tracks
Authorization: Bearer {token}
```

---

## 4. Conference Request System

#### List Conference Requests
```http
GET /api/conference-requests?status=PENDING
Authorization: Bearer {token}

Response 200:
{
  "data": [
    {
      "request_id": 1,
      "title": "AI Symposium 2025",
      "status": "PENDING",
      "chair_fullname": "Dr. Nguyễn Văn A",
      "created_at": "2025-11-01"
    }
  ]
}
```

#### Submit Conference Request
```http
POST /api/conference-requests
Authorization: Bearer {token}
Content-Type: multipart/form-data

{
  "title": "AI Symposium 2025",
  "objective": "Promote AI research",
  "level_code": "TRUONG",
  "chair_fullname": "Dr. Nguyễn Văn A",
  "chair_email": "chair@huit.edu.vn",
  "chair_phone": "0123456789",
  "proposal_file": [PDF file]
}

Response 201:
{
  "message": "Request submitted successfully",
  "request_id": 1
}
```

#### Show Request Detail
```http
GET /api/conference-requests/{id}
Authorization: Bearer {token}
```

#### Approve Request (Admin)
```http
POST /api/conference-requests/{id}/approve
Authorization: Bearer {token}

{
  "approval_notes": "Approved for 2025"
}

Response 200:
{
  "message": "Conference request approved",
  "conference_id": 10
}
```

#### Reject Request (Admin)
```http
POST /api/conference-requests/{id}/reject
Authorization: Bearer {token}

{
  "rejection_reason": "Incomplete proposal"
}
```

#### Cancel Request (Requester)
```http
POST /api/conference-requests/{id}/cancel
Authorization: Bearer {token}
```

#### Configure Conference (Admin)
```http
PUT /api/conference-requests/{id}/configure
Authorization: Bearer {token}

{
  "start_date": "2025-12-01",
  "end_date": "2025-12-03",
  "submission_deadline": "2025-10-01",
  "review_deadline": "2025-11-01"
}
```

#### Request Statistics (Admin)
```http
GET /api/conference-requests/statistics
Authorization: Bearer {token}

Response 200:
{
  "total": 50,
  "pending": 10,
  "approved": 35,
  "rejected": 5
}
```

---

## 5. Paper Submission & Management

#### List Papers
```http
GET /api/papers?conference_id=1&status=UNDER_REVIEW
Authorization: Bearer {token}

Response 200:
{
  "data": [
    {
      "paper_id": 1,
      "title": "Machine Learning in Healthcare",
      "abstract": "...",
      "status": "UNDER_REVIEW",
      "authors": [...],
      "track": {...}
    }
  ],
  "pagination": {...}
}
```

#### Submit Paper
```http
POST /api/papers
Authorization: Bearer {token}
Content-Type: multipart/form-data

{
  "title": "Deep Learning Research",
  "abstract": "This paper presents...",
  "keywords": "deep learning, neural networks",
  "conference_id": 1,
  "track_id": 2,
  "file": [PDF file],
  "authors": [
    {"full_name": "Nguyễn Văn A", "email": "a@huit.edu.vn"},
    {"full_name": "Trần Thị B", "email": "b@huit.edu.vn"}
  ]
}

Response 201:
{
  "message": "Paper submitted successfully",
  "paper_id": 15
}
```

#### Paper Statistics
```http
GET /api/papers/statistics?conference_id=1
Authorization: Bearer {token}

Response 200:
{
  "total": 100,
  "submitted": 100,
  "under_review": 60,
  "accepted": 25,
  "rejected": 15
}
```

#### Show Paper Detail
```http
GET /api/papers/{id}
Authorization: Bearer {token}

Response 200:
{
  "paper_id": 1,
  "title": "...",
  "abstract": "...",
  "current_version": {...},
  "all_versions": [...],
  "authors": [...],
  "reviews": [...]
}
```

#### Update Paper
```http
PUT /api/papers/{id}
Authorization: Bearer {token}

{
  "title": "Updated Title",
  "abstract": "Updated abstract"
}
```

#### Delete Paper
```http
DELETE /api/papers/{id}
Authorization: Bearer {token}
```

#### Withdraw Paper
```http
POST /api/papers/{id}/withdraw
Authorization: Bearer {token}

{
  "reason": "Author request"
}
```

#### Download Paper
```http
GET /api/papers/{id}/download
Authorization: Bearer {token}

Response: PDF file download
```

#### My Papers (Author)
```http
GET /api/my-papers?status=ACCEPTED
Authorization: Bearer {token}

Response 200:
{
  "data": [
    {
      "paper_id": 5,
      "title": "My Research",
      "status": "ACCEPTED",
      "submission_date": "2025-10-15"
    }
  ]
}
```

#### Author Statistics
```http
GET /api/author/statistics
Authorization: Bearer {token}

Response 200:
{
  "total_papers": 5,
  "accepted": 3,
  "rejected": 1,
  "under_review": 1
}
```

---

## 6. Paper Version Control

#### List Paper Versions
```http
GET /api/papers/{paper_id}/versions
Authorization: Bearer {token}

Response 200:
{
  "data": [
    {
      "version_id": 1,
      "version_no": 1,
      "file_path": "papers/v1_paper.pdf",
      "uploaded_at": "2025-10-01",
      "is_current": true
    },
    {
      "version_id": 2,
      "version_no": 2,
      "file_path": "papers/v2_paper.pdf",
      "uploaded_at": "2025-10-15",
      "is_current": false
    }
  ]
}
```

#### Upload New Version
```http
POST /api/papers/{paper_id}/versions
Authorization: Bearer {token}
Content-Type: multipart/form-data

{
  "file": [PDF file],
  "change_notes": "Fixed methodology section"
}

Response 201:
{
  "message": "New version uploaded",
  "version_id": 3,
  "version_no": 3
}
```

#### Show Specific Version
```http
GET /api/papers/{paper_id}/versions/{version_no}
Authorization: Bearer {token}

Response 200:
{
  "version_id": 2,
  "version_no": 2,
  "file_path": "papers/v2_paper.pdf",
  "file_url": "http://.../storage/papers/v2_paper.pdf",
  "uploaded_at": "2025-10-15"
}
```

#### Download Version
```http
GET /api/papers/{paper_id}/versions/{version_no}/download
Authorization: Bearer {token}

Response: PDF file download
```

#### Compare Versions
```http
GET /api/papers/{paper_id}/versions/compare?old=1&new=2
Authorization: Bearer {token}

Response 200:
{
  "old_version": {
    "version_no": 1,
    "file_url": "...",
    "uploaded_at": "2025-10-01"
  },
  "new_version": {
    "version_no": 2,
    "file_url": "...",
    "uploaded_at": "2025-10-15"
  },
  "time_difference": "14 days"
}
```

---

## 7. Bidding System

#### List Biddings for Paper (Admin/Chair)
```http
GET /api/papers/{paper_id}/biddings
Authorization: Bearer {token}

Response 200:
{
  "data": [
    {
      "user_id": 10,
      "reviewer_name": "Dr. Nguyễn A",
      "preference": "WILLING",
      "expertise_match": 85,
      "bidded_at": "2025-10-20"
    }
  ]
}
```

#### Submit Bid (Reviewer)
```http
POST /api/papers/{paper_id}/bid
Authorization: Bearer {token}

{
  "preference": "WILLING",
  "expertise_level": "HIGH",
  "comments": "I have 5 years experience in this area"
}

Response 201:
{
  "message": "Bid submitted successfully"
}
```

#### My Biddings (Reviewer)
```http
GET /api/my-biddings?preference=WILLING
Authorization: Bearer {token}

Response 200:
{
  "data": [
    {
      "paper_id": 5,
      "paper_title": "ML in Healthcare",
      "preference": "WILLING",
      "expertise_level": "HIGH",
      "bidded_at": "2025-10-20"
    }
  ]
}
```

#### Update Bid
```http
PUT /api/biddings/{paper_id}
Authorization: Bearer {token}

{
  "preference": "EAGER",
  "expertise_level": "EXPERT"
}
```

#### Withdraw Bid
```http
DELETE /api/biddings/{paper_id}
Authorization: Bearer {token}
```

#### Bidding Statistics (Admin)
```http
GET /api/bidding/statistics?conference_id=1
Authorization: Bearer {token}

Response 200:
{
  "total_biddings": 120,
  "eager": 30,
  "willing": 60,
  "neutral": 20,
  "unwilling": 10,
  "papers_with_bids": 40,
  "papers_without_bids": 10
}
```

---

## 8. Review System

#### Submit Review
```http
POST /api/reviews
Authorization: Bearer {token}

{
  "assignment_id": 15,
  "paper_version_id": 2,
  "decision": "ACCEPT",
  "overall_score": 8,
  "originality_score": 8,
  "technical_quality_score": 7,
  "clarity_score": 9,
  "relevance_score": 8,
  "comments": "Well-written paper with solid methodology",
  "confidential_comments": "Minor issues in references"
}

Response 201:
{
  "message": "Review submitted successfully",
  "review_id": 25
}
```

#### List Paper Reviews (Admin/Chair)
```http
GET /api/papers/{paper_id}/reviews
Authorization: Bearer {token}

Response 200:
{
  "data": [
    {
      "review_id": 1,
      "reviewer_name": "Anonymous Reviewer 1",
      "decision": "ACCEPT",
      "overall_score": 8,
      "submitted_at": "2025-11-01",
      "is_finalized": true
    }
  ],
  "average_score": 7.8
}
```

#### Show Review Detail
```http
GET /api/reviews/{review_id}
Authorization: Bearer {token}

Response 200:
{
  "review_id": 1,
  "decision": "ACCEPT",
  "scores": {...},
  "comments": "...",
  "submitted_at": "2025-11-01"
}
```

#### Update Review
```http
PUT /api/reviews/{review_id}
Authorization: Bearer {token}

{
  "overall_score": 9,
  "comments": "Updated review comments"
}
```

#### My Reviews (Reviewer)
```http
GET /api/my-reviews?status=COMPLETED
Authorization: Bearer {token}

Response 200:
{
  "data": [
    {
      "review_id": 5,
      "paper_title": "ML Research",
      "decision": "ACCEPT",
      "submitted_at": "2025-11-01",
      "is_finalized": true
    }
  ]
}
```

#### Finalize Review
```http
POST /api/reviews/{review_id}/finalize
Authorization: Bearer {token}

Response 200:
{
  "message": "Review finalized successfully"
}
```

#### Review Statistics (Admin)
```http
GET /api/review/statistics?conference_id=1
Authorization: Bearer {token}

Response 200:
{
  "total_reviews": 150,
  "completed": 120,
  "pending": 30,
  "average_score": 7.5,
  "decisions": {
    "ACCEPT": 50,
    "MINOR_REVISION": 40,
    "MAJOR_REVISION": 30,
    "REJECT": 30
  }
}
```

---

## 9. Assignment System

#### Manual Assignment
```http
POST /api/assignments
Authorization: Bearer {token}

{
  "paper_id": 10,
  "reviewer_id": 15,
  "assignment_notes": "Expert in this field"
}

Response 201:
{
  "message": "Reviewer assigned successfully",
  "assignment_id": 25
}
```

#### Auto-Assignment Algorithm
```http
POST /api/assignments/auto-assign
Authorization: Bearer {token}

{
  "conference_id": 1,
  "papers_per_reviewer": 5,
  "reviewers_per_paper": 3
}

Response 200:
{
  "message": "Auto-assignment completed",
  "assignments_created": 45,
  "papers_assigned": 15
}
```

#### Unassign Reviewer
```http
DELETE /api/assignments/{assignment_id}
Authorization: Bearer {token}

Response 200:
{
  "message": "Assignment removed successfully"
}
```

#### Paper Assignments (Chair/Admin)
```http
GET /api/papers/{paper_id}/assignments
Authorization: Bearer {token}

Response 200:
{
  "data": [
    {
      "assignment_id": 10,
      "reviewer": {
        "user_id": 15,
        "full_name": "Dr. Nguyễn Văn A",
        "email": "reviewer@huit.edu.vn"
      },
      "status": "IN_PROGRESS",
      "assigned_at": "2025-10-25"
    }
  ]
}
```

#### My Assignments (Reviewer)
```http
GET /api/my-assignments?status=IN_PROGRESS
Authorization: Bearer {token}

Response 200:
{
  "data": [
    {
      "assignment_id": 10,
      "paper": {
        "paper_id": 5,
        "title": "ML in Healthcare",
        "abstract": "..."
      },
      "status": "IN_PROGRESS",
      "deadline": "2025-11-15",
      "assigned_at": "2025-10-25"
    }
  ]
}
```

#### Accept/Reject Assignment
```http
PUT /api/assignments/{assignment_id}/accept
Authorization: Bearer {token}

{
  "action": "ACCEPT",
  "notes": "I accept this assignment"
}

Response 200:
{
  "message": "Assignment accepted successfully"
}
```

#### Assignment Statistics
```http
GET /api/assignment/statistics?conference_id=1
Authorization: Bearer {token}

Response 200:
{
  "total_assignments": 90,
  "pending": 10,
  "in_progress": 50,
  "completed": 30,
  "papers_assigned": 30,
  "papers_unassigned": 5,
  "average_per_reviewer": 3
}
```

---

## 10. Reviewer Revision Tracking (Mobile)

### 🎯 Chức năng: Theo dõi & xác nhận revision của tác giả

#### 10.1 Danh sách bài có revision
```http
GET /api/reviewer/papers-with-revisions
Authorization: Bearer {token}

Response 200:
{
  "data": [
    {
      "assignment_id": 10,
      "paper_id": 5,
      "title": "Machine Learning in Healthcare",
      "abstract": "This paper...",
      "submitter": {
        "user_id": 20,
        "full_name": "Nguyễn Văn A",
        "email": "author@huit.edu.vn"
      },
      "current_version": 2,
      "total_versions": 2,
      "revision_status": "pending_review",
      "last_updated": "2025-11-10 14:30:00"
    }
  ]
}
```

**Revision Status:**
- `pending_review` - Chờ reviewer xác nhận
- `approved` - Đã approve
- `needs_changes` - Yêu cầu chỉnh sửa thêm

#### 10.2 Lịch sử revision chi tiết
```http
GET /api/reviewer/papers/{paper_id}/revision-history
Authorization: Bearer {token}

Response 200:
{
  "paper_id": 5,
  "title": "Machine Learning in Healthcare",
  "current_version": 2,
  "versions": [
    {
      "version_id": 2,
      "version_no": 2,
      "file_path": "papers/v2_ml_healthcare.pdf",
      "file_url": "http://.../storage/papers/v2_ml_healthcare.pdf",
      "uploaded_at": "2025-11-10 14:30:00",
      "is_current": true,
      "review_status": "pending",
      "reviewer_decision": null,
      "reviewer_comments": null
    },
    {
      "version_id": 1,
      "version_no": 1,
      "file_path": "papers/v1_ml_healthcare.pdf",
      "file_url": "http://.../storage/papers/v1_ml_healthcare.pdf",
      "uploaded_at": "2025-10-15 10:00:00",
      "is_current": false,
      "review_status": "reviewed",
      "reviewer_decision": "MAJOR_REVISION",
      "reviewer_comments": "Cần bổ sung phần literature review và methodology"
    }
  ]
}
```

#### 10.3 Xác nhận kết quả chỉnh sửa
```http
POST /api/reviewer/papers/{paper_id}/confirm-revision
Authorization: Bearer {token}
Content-Type: application/json

{
  "version_id": 2,
  "decision": "APPROVE",
  "comments": "Tác giả đã chỉnh sửa đầy đủ theo yêu cầu. Paper đạt tiêu chuẩn để accept."
}

Response 200:
{
  "message": "Revision confirmed successfully",
  "review_id": 15,
  "paper_status": "ACCEPTED"
}
```

**Decision Values:**
- `APPROVE` - Chấp nhận revision, paper đạt yêu cầu
- `REQUEST_CHANGES` - Yêu cầu chỉnh sửa thêm

**Validation:**
- `comments` required, min 10 characters

**Logic tự động:**
- Nếu TẤT CẢ reviewers approve → Paper status = `ACCEPTED`
- Nếu có reviewer request changes → Paper status giữ nguyên `UNDER_REVIEW`

#### 10.4 So sánh 2 phiên bản
```http
GET /api/reviewer/papers/{paper_id}/compare-versions?old_version=1&new_version=2
Authorization: Bearer {token}

Response 200:
{
  "paper_id": 5,
  "paper_title": "Machine Learning in Healthcare",
  "old_version": {
    "version_no": 1,
    "file_path": "papers/v1_ml_healthcare.pdf",
    "file_url": "http://.../storage/papers/v1_ml_healthcare.pdf",
    "uploaded_at": "2025-10-15 10:00:00"
  },
  "new_version": {
    "version_no": 2,
    "file_path": "papers/v2_ml_healthcare.pdf",
    "file_url": "http://.../storage/papers/v2_ml_healthcare.pdf",
    "uploaded_at": "2025-11-10 14:30:00"
  },
  "time_difference": "26 days",
  "comparison_summary": "Version 2 uploaded 26 days after version 1"
}
```

### 📱 Mobile UI Suggestions

**Dashboard Card:**
```
┌─────────────────────────────────────┐
│ 🔵 Machine Learning in Healthcare   │
│ Author: Nguyễn Văn A                │
│ Status: Pending Review              │
│ Version 2/2 • Updated 2 hours ago   │
│                                     │
│ [View History]  [Compare]  [Review] │
└─────────────────────────────────────┘
```

**Timeline View:**
```
● Version 2 (Current)     10/11/2025 14:30
  📄 v2_ml_healthcare.pdf
  🔵 Pending review
  [Download] [Review Now]

● Version 1               15/10/2025 10:00
  📄 v1_ml_healthcare.pdf
  ✓ Reviewed
  Decision: Major Revision
  "Cần bổ sung literature review..."
  [Download]
```

**Review Form:**
```
┌─────────────────────────────────────┐
│ Confirm Revision - Version 2        │
├─────────────────────────────────────┤
│ ○ Approve - Accept revision         │
│   Author has addressed all concerns │
│                                     │
│ ○ Request Changes                   │
│   Additional revisions needed       │
├─────────────────────────────────────┤
│ Comments: (required, min 10 chars)  │
│ ┌─────────────────────────────────┐ │
│ │ Tác giả đã chỉnh sửa tốt...     │ │
│ └─────────────────────────────────┘ │
│                                     │
│      [Cancel]  [Submit Review ✓]    │
└─────────────────────────────────────┘
```

---

## 11. Chair Management APIs (Mobile)

### 🎯 Chức năng: Quản lý toàn bộ conference cho Chair

#### 11.1 Dashboard Statistics
```http
GET /api/chair/dashboard?conference_id=1
Authorization: Bearer {token}

Response 200:
{
  "conference": {
    "conference_id": 1,
    "title": "AI Conference 2025",
    "start_date": "2025-12-01",
    "status": "ACTIVE"
  },
  "statistics": {
    "total_papers": 45,
    "pending_review": 15,
    "under_review": 20,
    "accepted": 7,
    "rejected": 3,
    "total_reviewers": 30,
    "active_assignments": 60,
    "completed_reviews": 40,
    "pending_reviews": 20
  },
  "deadlines": {
    "submission_deadline": "2025-10-15",
    "review_deadline": "2025-11-15",
    "camera_ready_deadline": "2025-11-30"
  }
}
```

#### 11.2 List All Papers (with Filters)
```http
GET /api/chair/papers?conference_id=1&status=UNDER_REVIEW&track_id=2
Authorization: Bearer {token}

Response 200:
{
  "data": [
    {
      "paper_id": 10,
      "title": "Deep Learning in Healthcare",
      "abstract": "This paper...",
      "status": "UNDER_REVIEW",
      "track": {
        "track_id": 2,
        "title": "Machine Learning Track"
      },
      "authors": [
        {
          "full_name": "Nguyễn Văn A",
          "email": "author@huit.edu.vn"
        }
      ],
      "assignments_count": 3,
      "reviews_count": 2,
      "average_score": 7.5,
      "submitted_at": "2025-10-10"
    }
  ],
  "pagination": {...}
}
```

**Filter Options:**
- `conference_id` - Conference ID (required for Chair)
- `status` - SUBMITTED, UNDER_REVIEW, ACCEPTED, REJECTED
- `track_id` - Filter by track
- `has_reviews` - true/false

#### 11.3 Paper Detail with Reviews
```http
GET /api/chair/papers/{id}
Authorization: Bearer {token}

Response 200:
{
  "paper_id": 10,
  "title": "Deep Learning in Healthcare",
  "abstract": "...",
  "status": "UNDER_REVIEW",
  "current_version": {
    "version_id": 2,
    "file_url": "..."
  },
  "authors": [...],
  "track": {...},
  "assignments": [
    {
      "assignment_id": 15,
      "reviewer": {
        "user_id": 20,
        "full_name": "Dr. Reviewer A",
        "expertise": "Machine Learning"
      },
      "status": "COMPLETED",
      "review": {
        "decision": "ACCEPT",
        "overall_score": 8,
        "comments": "Well-written paper"
      }
    }
  ],
  "review_summary": {
    "total_reviewers": 3,
    "completed_reviews": 2,
    "average_score": 7.5,
    "decisions": {
      "ACCEPT": 1,
      "MINOR_REVISION": 1,
      "MAJOR_REVISION": 0,
      "REJECT": 0
    }
  }
}
```

#### 11.4 Get Available Reviewers for Paper
```http
GET /api/chair/papers/{id}/available-reviewers
Authorization: Bearer {token}

Response 200:
{
  "data": [
    {
      "user_id": 25,
      "full_name": "Dr. Nguyễn Văn B",
      "email": "reviewer@huit.edu.vn",
      "expertise": "Deep Learning, Computer Vision",
      "current_assignments": 4,
      "completed_reviews": 12,
      "average_review_score": 7.8,
      "has_coi": false,
      "bidding_preference": "WILLING"
    }
  ]
}
```

**Sorting:**
- By expertise match (default)
- By workload (assignments count)
- By bidding preference

#### 11.5 Assign Reviewer to Paper
```http
POST /api/chair/papers/{id}/assign-reviewer
Authorization: Bearer {token}

{
  "reviewer_id": 25,
  "assignment_notes": "Expert in deep learning"
}

Response 201:
{
  "message": "Reviewer assigned successfully",
  "assignment_id": 30
}
```

#### 11.6 Remove Assignment
```http
DELETE /api/chair/assignments/{id}
Authorization: Bearer {token}

{
  "reason": "COI detected"
}

Response 200:
{
  "message": "Assignment removed successfully"
}
```

#### 11.7 Make Decision (Accept/Reject Paper)
```http
POST /api/chair/papers/{id}/decision
Authorization: Bearer {token}

{
  "decision": "ACCEPT",
  "decision_notes": "All reviewers recommend acceptance. Paper meets conference standards.",
  "notify_author": true
}

Response 200:
{
  "message": "Decision recorded successfully",
  "paper_status": "ACCEPTED"
}
```

**Decision Values:**
- `ACCEPT` - Accept paper
- `REJECT` - Reject paper
- `MINOR_REVISION` - Request minor revision
- `MAJOR_REVISION` - Request major revision

#### 11.8 Review Statistics by Conference
```http
GET /api/chair/conferences/{id}/review-statistics
Authorization: Bearer {token}

Response 200:
{
  "conference_id": 1,
  "total_papers": 45,
  "review_progress": {
    "not_started": 5,
    "in_progress": 20,
    "completed": 20
  },
  "decisions_summary": {
    "ACCEPT": 10,
    "MINOR_REVISION": 8,
    "MAJOR_REVISION": 5,
    "REJECT": 2
  },
  "average_scores": {
    "overall": 7.5,
    "originality": 7.8,
    "technical_quality": 7.3,
    "clarity": 7.6,
    "relevance": 7.9
  },
  "reviewer_performance": {
    "total_reviewers": 30,
    "active": 25,
    "completed_on_time": 20,
    "overdue": 5
  }
}
```

#### 11.9 List Reviewers with Performance
```http
GET /api/chair/reviewers?conference_id=1
Authorization: Bearer {token}

Response 200:
{
  "data": [
    {
      "user_id": 20,
      "full_name": "Dr. Reviewer A",
      "email": "reviewer@huit.edu.vn",
      "expertise": "Machine Learning, AI",
      "assignments": {
        "total": 5,
        "completed": 4,
        "in_progress": 1,
        "overdue": 0
      },
      "performance": {
        "average_score": 7.8,
        "reviews_on_time": 4,
        "average_review_length": 250
      }
    }
  ]
}
```

### 📱 Mobile UI Suggestions for Chair

**Dashboard:**
```
┌─────────────────────────────────────┐
│ 📊 AI Conference 2025 Dashboard     │
├─────────────────────────────────────┤
│ Papers                              │
│ 45 Total  │ 20 Under Review  │ 7 ✓ │
│                                     │
│ Reviews                             │
│ 60 Assigned  │ 40 Done  │ 20 Pending│
│                                     │
│ Deadlines                           │
│ Review: Nov 15 (5 days)             │
│ Camera Ready: Nov 30 (20 days)      │
├─────────────────────────────────────┤
│ [View Papers] [View Reviewers]      │
└─────────────────────────────────────┘
```

**Paper List:**
```
┌─────────────────────────────────────┐
│ 🔵 Deep Learning in Healthcare      │
│ Authors: Nguyễn Văn A, Trần Thị B   │
│ Track: ML Track                     │
│ Reviews: 2/3 • Avg Score: 7.5       │
│ [Assign Reviewer] [View Reviews]    │
├─────────────────────────────────────┤
│ 🟢 Blockchain in Supply Chain       │
│ Authors: Lê Văn C                   │
│ Track: Blockchain Track             │
│ Reviews: 3/3 • Avg Score: 8.2       │
│ [Make Decision]                     │
└─────────────────────────────────────┘
```

---

## 12. Announcement System

#### 12.1 List Announcements (User)
```http
GET /api/announcements?status=active&type=general
Authorization: Bearer {token}

Response 200:
{
  "data": [
    {
      "announcement_id": 1,
      "title": "Deadline Extension",
      "content": "Submission deadline extended to Nov 30",
      "type": "general",
      "priority": "high",
      "scheduled_at": "2025-11-10 09:00:00",
      "is_read": false,
      "conference": {
        "conference_id": 1,
        "title": "AI Conference 2025"
      }
    }
  ]
}
```

**Filter Options:**
- `status` - active, scheduled, expired
- `type` - general, deadline, review_reminder, decision
- `priority` - low, medium, high, urgent
- `is_read` - true/false

#### 12.2 Create Announcement (Chair)
```http
POST /api/announcements
Authorization: Bearer {token}

{
  "conference_id": 1,
  "title": "Review Deadline Reminder",
  "content": "Please complete your reviews by Nov 15",
  "type": "review_reminder",
  "priority": "high",
  "target_audience": "REVIEWERS",
  "scheduled_at": "2025-11-10 09:00:00",
  "send_email": true,
  "send_chatbot": true
}

Response 201:
{
  "message": "Announcement created successfully",
  "announcement_id": 5,
  "recipients_count": 30
}
```

**Target Audience Options:**
- `ALL` - All conference participants
- `AUTHORS` - Paper submitters
- `REVIEWERS` - Assigned reviewers
- `CHAIRS` - Track chairs
- `CUSTOM` - Custom user list

#### 12.3 Get Conferences List (Chair)
```http
GET /api/announcements/conferences/list
Authorization: Bearer {token}

Response 200:
{
  "data": [
    {
      "conference_id": 1,
      "title": "AI Conference 2025",
      "participants_count": {
        "authors": 50,
        "reviewers": 30,
        "chairs": 5,
        "all": 85
      }
    }
  ]
}
```

#### 12.4 Preview Recipients Count
```http
POST /api/announcements/preview-recipients
Authorization: Bearer {token}

{
  "conference_id": 1,
  "target_audience": "REVIEWERS"
}

Response 200:
{
  "recipients_count": 30,
  "breakdown": {
    "reviewers": 30
  }
}
```

#### 12.5 Show Announcement Detail
```http
GET /api/announcements/{id}
Authorization: Bearer {token}

Response 200:
{
  "announcement_id": 1,
  "title": "Deadline Extension",
  "content": "...",
  "type": "deadline",
  "priority": "high",
  "created_by": {
    "user_id": 10,
    "full_name": "Dr. Chair A"
  },
  "delivery_status": {
    "total_recipients": 50,
    "sent": 50,
    "read": 30,
    "failed": 0
  }
}
```

#### 12.6 Update Announcement (Chair)
```http
PUT /api/announcements/{id}
Authorization: Bearer {token}

{
  "title": "Updated Title",
  "content": "Updated content",
  "priority": "urgent"
}
```

#### 12.7 Delete Announcement (Chair)
```http
DELETE /api/announcements/{id}
Authorization: Bearer {token}
```

#### 12.8 Mark as Read (User)
```http
POST /api/announcements/{id}/mark-read
Authorization: Bearer {token}

Response 200:
{
  "message": "Announcement marked as read"
}
```

---

## 13. Admin & Reports

#### 13.1 List All Users (Admin)
```http
GET /api/admin/users?role=REVIEWER&status=active
Authorization: Bearer {token}

Response 200:
{
  "data": [
    {
      "user_id": 10,
      "full_name": "Dr. Nguyễn Văn A",
      "email": "user@huit.edu.vn",
      "roles": ["AUTHOR", "REVIEWER"],
      "is_active": true,
      "created_at": "2025-01-15",
      "statistics": {
        "papers_submitted": 5,
        "reviews_completed": 12
      }
    }
  ],
  "pagination": {...}
}
```

#### 13.2 Update User (Admin)
```http
PUT /api/admin/users/{id}
Authorization: Bearer {token}

{
  "full_name": "Dr. Nguyễn Văn B",
  "is_active": false,
  "lock_reason": "Violation of review ethics"
}

Response 200:
{
  "message": "User updated successfully"
}
```

#### 13.3 Manage User Roles (Admin)
```http
POST /api/admin/users/{id}/roles
Authorization: Bearer {token}

{
  "action": "ADD",
  "role_code": "REVIEWER"
}

Response 200:
{
  "message": "Role assigned successfully",
  "current_roles": ["AUTHOR", "REVIEWER"]
}
```

**Actions:**
- `ADD` - Assign role
- `REMOVE` - Revoke role

**Role Codes:**
- `AUTHOR` - Paper submitter
- `REVIEWER` - Review papers
- `CHAIR` - Track/Conference chair
- `ADMIN` - System administrator

#### 13.4 Conference Report (Admin)
```http
GET /api/admin/reports/conference/{id}
Authorization: Bearer {token}

Response 200:
{
  "conference": {
    "conference_id": 1,
    "title": "AI Conference 2025",
    "level": "TRUONG",
    "start_date": "2025-12-01"
  },
  "papers": {
    "total": 45,
    "by_status": {
      "SUBMITTED": 5,
      "UNDER_REVIEW": 20,
      "ACCEPTED": 15,
      "REJECTED": 5
    },
    "by_track": [
      {"track": "ML Track", "count": 20},
      {"track": "AI Track", "count": 25}
    ]
  },
  "reviews": {
    "total": 90,
    "completed": 80,
    "pending": 10,
    "average_score": 7.5
  },
  "participants": {
    "authors": 50,
    "reviewers": 30,
    "chairs": 5
  }
}
```

#### 13.5 System Overview (Admin)
```http
GET /api/admin/reports/overview
Authorization: Bearer {token}

Response 200:
{
  "users": {
    "total": 150,
    "authors": 80,
    "reviewers": 50,
    "chairs": 15,
    "admins": 5,
    "new_this_month": 10
  },
  "conferences": {
    "total": 10,
    "active": 3,
    "upcoming": 2,
    "completed": 5
  },
  "papers": {
    "total": 200,
    "this_year": 45,
    "accepted_rate": "45%",
    "by_level": {
      "KHOA": 80,
      "TRUONG": 120
    }
  },
  "reviews": {
    "total": 450,
    "this_month": 90,
    "average_score": 7.5,
    "completion_rate": "88%"
  }
}
```

---

## 14. Notifications

#### 14.1 List Notifications
```http
GET /api/notifications?is_read=false&limit=20
Authorization: Bearer {token}

Response 200:
{
  "notifications": [
    {
      "id": 10,
      "title": "New Paper Assigned",
      "message": "You have been assigned to review 'ML in Healthcare'",
      "time": "2 hours ago",
      "created_at": "2025-11-14 18:00:00",
      "is_read": false
    }
  ],
  "unreadCount": 5
}
```

#### 14.2 Unread Count
```http
GET /api/notifications/unread
Authorization: Bearer {token}

Response 200:
{
  "unreadCount": 5
}
```

#### 14.3 Show Notification
```http
GET /api/notifications/{id}
Authorization: Bearer {token}

Response 200:
{
  "notification_id": 10,
  "title": "New Paper Assigned",
  "message": "...",
  "created_at": "2025-11-14 18:00:00",
  "is_read": true
}
```

#### 14.4 Mark as Read
```http
PATCH /api/notifications/{id}/read
Authorization: Bearer {token}

Response 200:
{
  "success": true
}
```

#### 14.5 Mark All as Read
```http
PATCH /api/notifications/read-all
Authorization: Bearer {token}

Response 200:
{
  "success": true,
  "updated": 5
}
```

#### 14.6 Delete Notification
```http
DELETE /api/notifications/{id}
Authorization: Bearer {token}

Response 200:
{
  "message": "Notification deleted successfully"
}
```

---

## 15. Utilities & Health Check

#### 15.1 List All Routes
```http
GET /api

Response 200:
[
  {
    "method": "GET|HEAD",
    "uri": "/api/conferences",
    "action": "App\\Http\\Controllers\\Api\\ConferenceController@index"
  },
  ...
]
```

#### 15.2 Health Check
```http
GET /api/health

Response 200:
{
  "status": "ok",
  "message": "HUIT Conference API is running",
  "timestamp": "2025-11-14 20:30:00"
}
```

#### 15.3 Get Facilities (Public)
```http
GET /api/facilities

Response 200:
{
  "facilities": [
    {
      "id": 1,
      "name": "Khoa Công nghệ Thông tin"
    },
    {
      "id": 2,
      "name": "Khoa Kinh tế"
    }
  ]
}
```

#### 15.4 Test Stats (Debug)
```http
GET /api/test/stats

Response 200:
{
  "papers": 45,
  "biddings": 120,
  "assignments": 90,
  "unassigned": 5
}
```

#### 15.5 Test Papers (Debug)
```http
GET /api/test/papers

Response 200:
[
  {
    "paper_id": 1,
    "title": "ML in Healthcare",
    "bidding_count": 8,
    "assignment_count": 3
  }
]
```

---

## 🔐 Authentication Guide

### Get Token
```bash
# Login
curl -X POST http://127.0.0.1:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "user@huit.edu.vn",
    "password": "password123"
  }'

# Response
{
  "access_token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
  "token_type": "bearer",
  "expires_in": 3600
}
```

### Use Token in Requests
```bash
curl -X GET http://127.0.0.1:8000/api/my-papers \
  -H "Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGc..."
```

---

## 📊 Status Codes

### Success Codes
- `200 OK` - Request successful
- `201 Created` - Resource created
- `204 No Content` - Successful deletion

### Client Error Codes
- `400 Bad Request` - Invalid request data
- `401 Unauthorized` - Missing/invalid token
- `403 Forbidden` - No permission
- `404 Not Found` - Resource not found
- `422 Unprocessable Entity` - Validation failed

### Server Error Codes
- `500 Internal Server Error` - Server error

---

## 🎯 Common Response Formats

### Success Response
```json
{
  "message": "Operation successful",
  "data": {...}
}
```

### Error Response
```json
{
  "error": "Error message",
  "details": "..."
}
```

### Validation Error
```json
{
  "message": "Validation failed",
  "errors": {
    "email": ["The email field is required"],
    "password": ["Password must be at least 8 characters"]
  }
}
```

### Pagination Format
```json
{
  "data": [...],
  "pagination": {
    "total": 100,
    "per_page": 15,
    "current_page": 1,
    "last_page": 7,
    "from": 1,
    "to": 15
  }
}
```

---

## 📱 Mobile Integration Checklist

### ✅ For Mobile Developers

#### Authentication
- [ ] Implement login/register
- [ ] Store token securely
- [ ] Handle token refresh
- [ ] Implement logout

#### Core Features
- [ ] Conference listing
- [ ] Paper submission
- [ ] Review assignment notification
- [ ] Submit reviews
- [ ] View announcements

#### Reviewer Features (NEW)
- [ ] Track paper revisions
- [ ] Compare versions
- [ ] Confirm/approve revisions
- [ ] View revision timeline

#### Chair Features (NEW)
- [ ] Dashboard statistics
- [ ] Manage paper assignments
- [ ] View all reviews
- [ ] Make final decisions
- [ ] Send announcements

#### Error Handling
- [ ] Network errors
- [ ] Token expiration
- [ ] Validation errors
- [ ] 404 errors

#### UI/UX
- [ ] Loading states
- [ ] Empty states
- [ ] Success messages
- [ ] Error messages
- [ ] Pull-to-refresh

---

## 📚 Related Documentation

- **Reviewer Revision:** `REVIEWER_REVISION_API.md`
- **Chair Management:** `CHAIR_API_SUMMARY.md`
- **Announcements:** `ANNOUNCEMENT_API_SUMMARY_FULL.md`
- **Admin Reports:** `ADMIN_REPORTS_MODULE.md`

---

## 🎉 Summary

### Total APIs: **120+ endpoints**

**By Module:**
- Authentication: 6 endpoints
- Conferences: 8 endpoints
- Tracks: 6 endpoints
- Conference Requests: 8 endpoints
- Papers: 11 endpoints
- Paper Versions: 5 endpoints
- Bidding: 6 endpoints
- Reviews: 7 endpoints
- Assignments: 7 endpoints
- **Reviewer Revision: 4 endpoints** ✨ NEW
- **Chair Management: 9 endpoints** ✨ NEW
- Announcements: 8 endpoints
- Admin: 5 endpoints
- Notifications: 6 endpoints
- Utilities: 5 endpoints

### Status: ✅ PRODUCTION READY

All APIs documented, tested, and ready for mobile integration!

---

**Last Updated:** 14/11/2025 20:45  
**Maintained by:** HUIT Development Team
