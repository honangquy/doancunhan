# 📄 Phase 4: Paper Management - API Documentation

## Overview
Phase 4 cung cấp các APIs để quản lý bài báo (papers) và các phiên bản của chúng.

**Base URL:** `http://localhost/qly_hthao/qlyhoithao/public/api`

**Total APIs Phase 4:** 13 APIs
- Paper Management: 8 APIs
- Version Management: 5 APIs

---

## 🔐 Authentication
Tất cả APIs yêu cầu JWT token (trừ public endpoints)

```
Authorization: Bearer {token}
```

---

## 📋 Paper Management APIs

### 1. List Papers
**GET** `/api/papers`

Lấy danh sách bài báo với filter và pagination.

**Headers:**
```
Authorization: Bearer {token}
```

**Query Parameters:**
| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| conference_id | integer | No | Filter by conference |
| track_id | integer | No | Filter by track |
| status | string | No | Filter by status (SUBMITTED, UNDER_REVIEW, etc.) |
| submitter_id | integer | No | Filter by submitter |
| search | string | No | Search in title/abstract |
| my_papers | boolean | No | Only my papers (as author or submitter) |
| sort_by | string | No | Field to sort (default: created_at) |
| sort_order | string | No | asc or desc (default: desc) |
| per_page | integer | No | Items per page (default: 15) |

**Response 200:**
```json
{
    "status": "success",
    "message": "Danh sách bài báo",
    "data": {
        "current_page": 1,
        "data": [
            {
                "paper_id": 1,
                "conference_id": 1,
                "track_id": 1,
                "submitter_id": 3,
                "title": "AI in Healthcare",
                "abstract": "This paper discusses...",
                "status_code": "SUBMITTED",
                "created_at": "2025-10-01T10:00:00",
                "hoiThao": {
                    "conference_id": 1,
                    "title": "HUIT Conference 2025",
                    "year": 2025
                },
                "tieuBan": {
                    "track_id": 1,
                    "title": "AI & Machine Learning"
                },
                "submitter": {
                    "user_id": 3,
                    "full_name": "Nguyễn Văn A",
                    "email": "author1@huit.edu.vn"
                },
                "tacGias": [
                    {
                        "user_id": 3,
                        "full_name": "Nguyễn Văn A",
                        "pivot": {
                            "author_order": 1,
                            "is_contact": 1
                        }
                    }
                ],
                "currentVersion": {
                    "version_id": 1,
                    "version_no": 1,
                    "submitted_at": "2025-10-01T10:00:00"
                }
            }
        ],
        "total": 50,
        "per_page": 15,
        "last_page": 4
    }
}
```

---

### 2. Submit Paper
**POST** `/api/papers`

Nộp bài báo mới.

**Headers:**
```
Authorization: Bearer {token}
Content-Type: multipart/form-data
```

**Body (multipart/form-data):**
| Field | Type | Required | Description |
|-------|------|----------|-------------|
| conference_id | integer | Yes | Conference ID |
| track_id | integer | No | Track ID |
| title | string | Yes | Paper title (max 500 chars) |
| abstract | text | Yes | Paper abstract |
| file | file | Yes | PDF/DOC/DOCX (max 10MB) |
| authors | array | Yes | List of authors (min 1) |
| authors[].user_id | integer | No* | Existing user ID |
| authors[].full_name | string | No* | Author name (if new user) |
| authors[].email | string | No* | Author email (if new user) |
| authors[].organization | string | No | Author organization |
| authors[].is_contact | boolean | No | Is contact author (default: false) |

*Note: Mỗi author phải có hoặc `user_id` (existing user) hoặc `full_name` + `email` (new user)

**Example Body:**
```json
{
    "conference_id": 1,
    "track_id": 1,
    "title": "AI in Healthcare: A Comprehensive Review",
    "abstract": "This paper provides a comprehensive review of AI applications in healthcare...",
    "authors": [
        {
            "user_id": 3,
            "is_contact": true
        },
        {
            "full_name": "External Author",
            "email": "external@university.edu",
            "organization": "External University",
            "is_contact": false
        }
    ]
}
// + file: paper.pdf
```

**Response 201:**
```json
{
    "status": "success",
    "message": "Nộp bài báo thành công",
    "data": {
        "paper_id": 10,
        "conference_id": 1,
        "track_id": 1,
        "submitter_id": 3,
        "title": "AI in Healthcare: A Comprehensive Review",
        "abstract": "This paper provides...",
        "status_code": "SUBMITTED",
        "current_version_id": 15,
        "created_at": "2025-10-04T14:30:00",
        "hoiThao": {...},
        "submitter": {...},
        "tacGias": [...],
        "currentVersion": {
            "version_id": 15,
            "version_no": 1,
            "file_path": "papers/1/paper_10_v1_1728045000.pdf",
            "submitted_at": "2025-10-04T14:30:00"
        }
    }
}
```

**Response 400:**
```json
{
    "status": "error",
    "message": "Hội thảo không đang mở nhận bài"
}
```

**Response 422:**
```json
{
    "status": "error",
    "message": "Dữ liệu không hợp lệ",
    "errors": {
        "title": ["Title là bắt buộc"],
        "file": ["File phải là PDF, DOC hoặc DOCX"]
    }
}
```

---

### 3. Get Paper Details
**GET** `/api/papers/{id}`

Xem chi tiết bài báo.

**Headers:**
```
Authorization: Bearer {token}
```

**Response 200:**
```json
{
    "status": "success",
    "message": "Chi tiết bài báo",
    "data": {
        "paper_id": 1,
        "conference_id": 1,
        "track_id": 1,
        "submitter_id": 3,
        "title": "AI in Healthcare",
        "abstract": "This paper discusses...",
        "status_code": "SUBMITTED",
        "created_at": "2025-10-01T10:00:00",
        "hoiThao": {...},
        "tieuBan": {...},
        "submitter": {...},
        "tacGias": [...],
        "phienBans": [
            {
                "version_id": 1,
                "version_no": 1,
                "file_path": "papers/1/paper_1_v1_1727776800.pdf",
                "submitted_at": "2025-10-01T10:00:00",
                "note": "Phiên bản nộp lần đầu"
            }
        ],
        "currentVersion": {...},
        "lichSuTrangThais": [
            {
                "history_id": 1,
                "status_code": "SUBMITTED",
                "changed_at": "2025-10-01T10:00:00",
                "changedBy": {
                    "user_id": 3,
                    "full_name": "Nguyễn Văn A"
                }
            }
        ]
    }
}
```

**Response 403:**
```json
{
    "status": "error",
    "message": "Không có quyền xem bài báo này"
}
```

**Response 404:**
```json
{
    "status": "error",
    "message": "Không tìm thấy bài báo"
}
```

---

### 4. Update Paper
**PUT** `/api/papers/{id}`

Cập nhật thông tin bài báo (chỉ người nộp bài, status: SUBMITTED hoặc REVISION_REQUIRED).

**Headers:**
```
Authorization: Bearer {token}
Content-Type: application/json
```

**Body:**
```json
{
    "title": "Updated Paper Title",
    "abstract": "Updated abstract...",
    "track_id": 2
}
```

**Response 200:**
```json
{
    "status": "success",
    "message": "Cập nhật bài báo thành công",
    "data": {
        "paper_id": 1,
        "title": "Updated Paper Title",
        "abstract": "Updated abstract...",
        ...
    }
}
```

**Response 403:**
```json
{
    "status": "error",
    "message": "Chỉ người nộp bài mới có thể chỉnh sửa"
}
```

**Response 400:**
```json
{
    "status": "error",
    "message": "Không thể chỉnh sửa bài báo ở trạng thái hiện tại"
}
```

---

### 5. Withdraw Paper
**DELETE** `/api/papers/{id}`

Rút bài báo (chỉ người nộp bài, không thể rút bài đã ACCEPTED).

**Headers:**
```
Authorization: Bearer {token}
```

**Response 200:**
```json
{
    "status": "success",
    "message": "Rút bài báo thành công"
}
```

**Response 403:**
```json
{
    "status": "error",
    "message": "Chỉ người nộp bài mới có thể rút bài"
}
```

**Response 400:**
```json
{
    "status": "error",
    "message": "Không thể rút bài ở trạng thái hiện tại"
}
```

---

### 6. Get My Papers
**GET** `/api/my-papers`

Lấy danh sách bài báo của tôi (as author hoặc submitter).

**Headers:**
```
Authorization: Bearer {token}
```

**Query Parameters:**
| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| status | string | No | Filter by status |
| conference_id | integer | No | Filter by conference |
| sort_by | string | No | Field to sort (default: created_at) |
| sort_order | string | No | asc or desc (default: desc) |
| per_page | integer | No | Items per page (default: 15) |

**Response 200:**
```json
{
    "status": "success",
    "message": "Danh sách bài báo của tôi",
    "data": {
        "current_page": 1,
        "data": [...],
        "total": 5
    }
}
```

---

### 7. Get Paper Statistics
**GET** `/api/papers/statistics`

Thống kê bài báo.

**Headers:**
```
Authorization: Bearer {token}
```

**Query Parameters:**
| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| conference_id | integer | No | Filter by conference |

**Response 200:**
```json
{
    "status": "success",
    "message": "Thống kê bài báo",
    "data": {
        "total": 50,
        "by_status": {
            "SUBMITTED": 20,
            "UNDER_REVIEW": 15,
            "REVISION_REQUIRED": 5,
            "REVISED": 3,
            "ACCEPTED": 5,
            "REJECTED": 2
        },
        "by_track": [
            {
                "track_id": 1,
                "track_name": "AI & Machine Learning",
                "count": 25
            },
            {
                "track_id": 2,
                "track_name": "Data Science",
                "count": 15
            }
        ]
    }
}
```

---

### 8. Download Paper
**GET** `/api/papers/{id}/download`

Tải file bài báo (phiên bản hiện tại).

**Headers:**
```
Authorization: Bearer {token}
```

**Response 200:**
File download (PDF/DOC/DOCX)

**Response 403:**
```json
{
    "status": "error",
    "message": "Không có quyền tải bài báo này"
}
```

**Response 404:**
```json
{
    "status": "error",
    "message": "Không tìm thấy file bài báo"
}
```

---

## 📚 Paper Version Management APIs

### 9. List Versions
**GET** `/api/papers/{paper_id}/versions`

Lấy danh sách các phiên bản của bài báo.

**Headers:**
```
Authorization: Bearer {token}
```

**Response 200:**
```json
{
    "status": "success",
    "message": "Danh sách phiên bản",
    "data": [
        {
            "version_id": 3,
            "paper_id": 1,
            "version_no": 3,
            "file_path": "papers/1/paper_1_v3_1728045000.pdf",
            "submitted_at": "2025-10-04T14:30:00",
            "note": "Phiên bản sau khi sửa theo yêu cầu reviewer"
        },
        {
            "version_id": 2,
            "paper_id": 1,
            "version_no": 2,
            "file_path": "papers/1/paper_1_v2_1727949600.pdf",
            "submitted_at": "2025-10-03T12:00:00",
            "note": "Phiên bản 2"
        },
        {
            "version_id": 1,
            "paper_id": 1,
            "version_no": 1,
            "file_path": "papers/1/paper_1_v1_1727776800.pdf",
            "submitted_at": "2025-10-01T10:00:00",
            "note": "Phiên bản nộp lần đầu"
        }
    ]
}
```

---

### 10. Upload New Version
**POST** `/api/papers/{paper_id}/versions`

Upload phiên bản mới (chỉ người nộp bài).

**Headers:**
```
Authorization: Bearer {token}
Content-Type: multipart/form-data
```

**Body:**
| Field | Type | Required | Description |
|-------|------|----------|-------------|
| file | file | Yes | PDF/DOC/DOCX (max 10MB) |
| note | string | No | Note about this version |

**Example:**
```
file: paper_revised.pdf
note: "Đã sửa theo yêu cầu của reviewer"
```

**Response 201:**
```json
{
    "status": "success",
    "message": "Upload phiên bản mới thành công",
    "data": {
        "version_id": 4,
        "paper_id": 1,
        "version_no": 4,
        "file_path": "papers/1/paper_1_v4_1728131400.pdf",
        "submitted_at": "2025-10-05T14:30:00",
        "note": "Đã sửa theo yêu cầu của reviewer"
    }
}
```

**Response 403:**
```json
{
    "status": "error",
    "message": "Chỉ người nộp bài mới có thể upload phiên bản mới"
}
```

**Response 400:**
```json
{
    "status": "error",
    "message": "Không thể upload phiên bản mới ở trạng thái hiện tại"
}
```

---

### 11. Get Version Details
**GET** `/api/papers/{paper_id}/versions/{version_no}`

Xem chi tiết một phiên bản cụ thể.

**Headers:**
```
Authorization: Bearer {token}
```

**Response 200:**
```json
{
    "status": "success",
    "message": "Chi tiết phiên bản",
    "data": {
        "version_id": 2,
        "paper_id": 1,
        "version_no": 2,
        "file_path": "papers/1/paper_1_v2_1727949600.pdf",
        "submitted_at": "2025-10-03T12:00:00",
        "note": "Phiên bản 2"
    }
}
```

---

### 12. Download Specific Version
**GET** `/api/papers/{paper_id}/versions/{version_no}/download`

Tải file của một phiên bản cụ thể.

**Headers:**
```
Authorization: Bearer {token}
```

**Response 200:**
File download

---

### 13. Compare Versions
**GET** `/api/papers/{paper_id}/versions/compare`

So sánh 2 phiên bản.

**Headers:**
```
Authorization: Bearer {token}
```

**Query Parameters:**
| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| version1 | integer | Yes | Version number 1 |
| version2 | integer | Yes | Version number 2 |

**Example:**
```
GET /api/papers/1/versions/compare?version1=1&version2=3
```

**Response 200:**
```json
{
    "status": "success",
    "message": "So sánh phiên bản",
    "data": {
        "version1": {
            "version_id": 1,
            "version_no": 1,
            "file_path": "papers/1/paper_1_v1_1727776800.pdf",
            "submitted_at": "2025-10-01T10:00:00"
        },
        "version2": {
            "version_id": 3,
            "version_no": 3,
            "file_path": "papers/1/paper_1_v3_1728045000.pdf",
            "submitted_at": "2025-10-04T14:30:00"
        },
        "time_diff": "3 days later",
        "size_diff": "50.5 KB (+)"
    }
}
```

---

## 📊 Paper Status Codes

| Status Code | Tên tiếng Việt | Mô tả |
|-------------|----------------|-------|
| SUBMITTED | Đã nộp | Bài báo vừa được nộp |
| UNDER_REVIEW | Đang phản biện | Đang trong quá trình phản biện |
| REVISION_REQUIRED | Yêu cầu chỉnh sửa | Reviewer yêu cầu sửa |
| REVISED | Đã chỉnh sửa | Author đã nộp bản sửa |
| ACCEPTED | Chấp nhận | Bài báo được chấp nhận |
| REJECTED | Từ chối | Bài báo bị từ chối |
| CAMERA_READY | Bản in | Bản in cuối cùng |
| WITHDRAWN | Đã rút | Author đã rút bài |

---

## 🔒 Permission Rules

### View Paper:
- ✅ Admin
- ✅ Submitter
- ✅ Co-authors
- ✅ Track chair (papers in their track)
- ✅ Assigned reviewers

### Edit Paper:
- ✅ Submitter only
- ✅ Only when status is SUBMITTED or REVISION_REQUIRED

### Withdraw Paper:
- ✅ Submitter only
- ❌ Cannot withdraw ACCEPTED, CAMERA_READY, or already WITHDRAWN papers

### Upload New Version:
- ✅ Submitter only
- ✅ Only when status is SUBMITTED, REVISION_REQUIRED, or REVISED

---

## 🔗 API Summary

**Total Phase 4 APIs: 13**

### Paper Management (8 APIs)
1. GET `/api/papers` - List papers
2. POST `/api/papers` - Submit paper
3. GET `/api/papers/{id}` - Get paper details
4. PUT `/api/papers/{id}` - Update paper
5. DELETE `/api/papers/{id}` - Withdraw paper
6. GET `/api/my-papers` - My papers
7. GET `/api/papers/statistics` - Statistics
8. GET `/api/papers/{id}/download` - Download paper

### Version Management (5 APIs)
9. GET `/api/papers/{paper_id}/versions` - List versions
10. POST `/api/papers/{paper_id}/versions` - Upload new version
11. GET `/api/papers/{paper_id}/versions/{version_no}` - Get version details
12. GET `/api/papers/{paper_id}/versions/{version_no}/download` - Download version
13. GET `/api/papers/{paper_id}/versions/compare` - Compare versions

---

## 💡 Testing Tips

### 1. Submit Paper Flow
```
1. Login as Author
2. POST /api/papers (với file PDF)
3. GET /api/my-papers (xem bài vừa nộp)
4. GET /api/papers/{id} (xem chi tiết)
5. GET /api/papers/{id}/download (tải file)
```

### 2. Upload New Version Flow
```
1. Paper status = REVISION_REQUIRED
2. POST /api/papers/{id}/versions (upload bản sửa)
3. GET /api/papers/{id}/versions (xem các phiên bản)
4. GET /api/papers/{id}/versions/compare?version1=1&version2=2
```

### 3. Withdraw Paper Flow
```
1. DELETE /api/papers/{id}
2. Paper status → WITHDRAWN
3. Cannot edit or upload new version
```

---

**Phase 4 Complete! 🎉**

Next: Phase 5 - Review System
