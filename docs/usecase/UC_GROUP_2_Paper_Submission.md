# NHÓM 2: PAPER SUBMISSION & MANAGEMENT
## 10 Use Cases - Quản lý Bài báo

---

## 📊 Sơ đồ Use Case - Nhóm 2

```plantuml
@startuml UC_Group2_Paper_Submission
!theme plain
skinparam actorStyle awesome

actor "Guest" as Guest #LightGray
actor "Author" as Author #Purple
actor "Chair" as Chair #Orange
actor "System" as System #Green

package "Paper Browsing" #LightGreen {
  usecase (UC-11: Xem chi tiết hội thảo) as UC11
  usecase (UC-12: Xem CFP - Call for Papers) as UC12
  usecase (UC-13: Download template) as UC13
}

package "Paper Submission" #LightCoral {
  usecase (UC-14: Nộp bài báo mới) as UC14
  usecase (UC-15: Chỉnh sửa bài báo) as UC15
  usecase (UC-16: Rút bài báo) as UC16
  usecase (UC-17: Xem trạng thái bài báo) as UC17
}

package "Paper Management" #Wheat {
  usecase (UC-18: Quản lý bài báo của tôi) as UC18
  usecase (UC-19: Upload Camera-Ready) as UC19
  usecase (UC-20: Xem danh sách bài báo (Chair)) as UC20
}

Guest --> UC11
Guest --> UC12
Guest --> UC13

Author --> UC14
Author --> UC15
Author --> UC16
Author --> UC17
Author --> UC18
Author --> UC19

Chair --> UC20

UC11 ..> UC12 : <<include>>
UC14 ..> System : auto-assign AUTHOR role
UC15 ..> UC14 : <<extend>>
UC19 ..> UC17 : require ACCEPTED status

@enduml
```

---

## 📋 ĐẶC TẢ CHI TIẾT CÁC USE CASE

### UC-11: Xem chi tiết hội thảo

**Mô tả**: Guest/User xem thông tin chi tiết về một hội thảo cụ thể

**Actor**: Guest, User

**Tiền điều kiện**: 
- Conference có status = 'ACTIVE', 'ONGOING', hoặc 'COMPLETED'

**Hậu điều kiện**: 
- Hiển thị đầy đủ thông tin hội thảo
- User có thể truy cập CFP và submit paper

**Luồng chính**:
1. Guest/User click vào một hội thảo từ danh sách
2. Hệ thống truy vấn chi tiết:
   ```sql
   SELECT h.*, 
          u.full_name as organizer_name,
          COUNT(DISTINCT b.paper_id) as total_papers,
          COUNT(DISTINCT v.user_id) as total_reviewers,
          GROUP_CONCAT(DISTINCT tb.title) as tracks
   FROM hoithao h
   JOIN nguoidung u ON h.organizer_id = u.user_id
   LEFT JOIN baibao b ON h.conference_id = b.conference_id
   LEFT JOIN vaitronguoidung v ON h.conference_id = v.conference_id 
        AND v.role_code = 'REVIEWER'
   LEFT JOIN tieuban tb ON h.conference_id = tb.conference_id
   WHERE h.conference_id = ?
   GROUP BY h.conference_id
   ```
3. Hệ thống hiển thị thông tin:
   - **Header**: Logo, Title, Acronym, Dates, Venue
   - **Overview**: Description, Objective, Level
   - **Important Dates**:
     - Paper Submission Deadline (highlight nếu còn mở)
     - Notification Date
     - Camera-Ready Deadline
     - Conference Dates
   - **Tracks/Topics**: Danh sách các chuyên đề
   - **Statistics**: 
     - Số bài đã nộp
     - Số reviewers
     - Acceptance rate (nếu có)
   - **For Authors**:
     - Author Guidelines
     - Template download link
     - Submit Paper button (nếu còn hạn)
   - **Organization**: 
     - Organizer
     - Chair information
     - Contact

4. Nếu User đã đăng nhập:
   - Hiển thị nút "Submit Paper" nổi bật
   - Hiển thị "My Submissions" nếu đã nộp bài

5. Nếu deadline đã qua:
   - Hiển thị "Submission closed"
   - Không hiển thị submit button

**Luồng thay thế**:

*2a. Conference không tồn tại hoặc chưa active*:
1. Hệ thống hiển thị lỗi 404: "Hội thảo không tồn tại hoặc chưa được công bố"
2. Use case kết thúc

*4a. User click "Submit Paper"*:
1. Chuyển sang UC-14 (Nộp bài báo)

*4b. User click "My Submissions"*:
1. Chuyển sang UC-18 (Quản lý bài báo của tôi)

**Route**: `GET /conferences/{conferenceId}`

**Controller**: `ConferenceController@show`

**Database**:
- Tables: `hoithao`, `nguoidung`, `baibao`, `vaitronguoidung`, `tieuban`

**Business Rules**:
- Chỉ ACTIVE/ONGOING/COMPLETED conferences mới hiển thị public
- Submit button chỉ hiển thị khi chưa quá paper_submission_deadline
- Statistics chỉ hiển thị con số tổng quát, không chi tiết

---

### UC-12: Xem CFP - Call for Papers

**Mô tả**: Guest/User xem thông tin chi tiết về Call for Papers của hội thảo

**Actor**: Guest, User

**Tiền điều kiện**: 
- Conference đang mở nhận bài (chưa quá paper_submission_deadline)

**Hậu điều kiện**: 
- Hiển thị đầy đủ thông tin CFP
- Guest có thể download template

**Luồng chính**:
1. Guest/User truy cập trang CFP (GET /conferences/{id}/cfp)
2. Hệ thống query thông tin CFP:
   ```sql
   SELECT h.title, h.acronym, h.description,
          h.paper_submission_deadline,
          h.notification_deadline,
          h.camera_ready_deadline,
          h.author_guidelines,
          h.template_file,
          h.start_date, h.end_date, h.venue,
          GROUP_CONCAT(DISTINCT tb.title ORDER BY tb.title) as tracks,
          GROUP_CONCAT(DISTINCT tb.keywords) as topics
   FROM hoithao h
   LEFT JOIN tieuban tb ON h.conference_id = tb.conference_id
   WHERE h.conference_id = ?
   AND h.status = 'ACTIVE'
   AND h.paper_submission_deadline >= NOW()
   GROUP BY h.conference_id
   ```
3. Hệ thống hiển thị trang CFP với format professional:
   
   **CALL FOR PAPERS**
   
   **Conference Title & Dates**
   - Full name, Acronym
   - Dates & Venue
   
   **Scope & Topics**
   - Description
   - Research topics (từ tracks)
   - Keywords
   
   **Important Dates**
   - Paper Submission: [date] (countdown timer)
   - Notification: [date]
   - Camera-Ready: [date]
   - Conference: [date]
   
   **Submission Guidelines**
   - Author guidelines (rich text)
   - Format requirements
   - Page limit
   - Language requirements
   
   **Submission Process**
   - Template download link
   - Registration requirement
   - Submission platform link
   
   **Review Process**
   - Peer review type (double-blind, single-blind)
   - Number of reviewers per paper
   - Evaluation criteria
   
   **Contact Information**
   - Chair email
   - Conference website

4. User có thể:
   - Download template file
   - Copy CFP text (share button)
   - Print CFP
   - Submit paper (nếu đã login)

**Luồng thay thế**:

*2a. Deadline đã qua*:
1. Hệ thống vẫn hiển thị CFP nhưng với banner "SUBMISSION CLOSED"
2. Không hiển thị submit button
3. Hiển thị "Please check back for future conferences"

*4a. Template file không có*:
1. Hiển thị: "Please contact conference chair for submission template"
2. Hiển thị email chair

**Route**: `GET /conferences/{conferenceId}/cfp`

**Controller**: `ConferenceController@showCFP`

**Database**:
- Tables: `hoithao`, `tieuban`

**Business Rules**:
- CFP phải public và accessible cho guest
- Template download không yêu cầu authentication
- CFP có thể được crawled bởi search engines (SEO optimized)

---

### UC-13: Download template

**Mô tả**: Guest/User tải file template bài báo của hội thảo

**Actor**: Guest, User

**Tiền điều kiện**: 
- Conference đã upload template file

**Hậu điều kiện**: 
- File template được download
- Log download được ghi nhận (analytics)

**Luồng chính**:
1. Guest/User click "Download Template" trên trang CFP hoặc conference detail
2. Hệ thống kiểm tra template tồn tại:
   ```sql
   SELECT template_file, title 
   FROM hoithao 
   WHERE conference_id = ? 
   AND template_file IS NOT NULL
   ```
3. Hệ thống verify file tồn tại trên storage:
   - Path: `public/conferences/templates/{conference_id}/{filename}`
4. Hệ thống ghi log download (analytics):
   ```sql
   INSERT INTO download_logs (
     conference_id, 
     user_id, -- NULL nếu guest
     file_type, 
     ip_address, 
     user_agent,
     downloaded_at
   ) VALUES (?, ?, 'TEMPLATE', ?, ?, NOW())
   ```
5. Hệ thống trả về file:
   - Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document (DOCX) hoặc application/pdf
   - Content-Disposition: attachment; filename="{conference_acronym}_Template.docx"
6. Browser tự động download file

**Luồng thay thế**:

*2a. Template không tồn tại*:
1. Hệ thống hiển thị lỗi: "Template chưa được upload. Vui lòng liên hệ chair"
2. Hiển thị email contact của chair
3. Use case kết thúc

*3a. File bị mất trên storage*:
1. Hệ thống ghi log error
2. Hệ thống hiển thị: "File tạm thời không khả dụng. Vui lòng thử lại sau"
3. Gửi alert đến admin
4. Use case kết thúc

**Route**: `GET /conferences/{conferenceId}/template/download`

**Controller**: `ConferenceController@downloadTemplate`

**Database**:
- Tables: `hoithao`, `download_logs`
- Storage: `public/conferences/templates/`

**Business Rules**:
- Template download không yêu cầu authentication
- Support nhiều format: DOCX, PDF, LaTeX (ZIP)
- File size limit: 50MB
- Template có version control (nếu update)

---

### UC-14: Nộp bài báo mới

**Mô tả**: Author đăng ký và nộp bài báo cho hội thảo

**Actor**: User (will become Author)

**Tiền điều kiện**: 
- User đã đăng nhập và xác thực email
- Conference đang mở nhận bài (chưa quá deadline)
- User chưa nộp bài trùng title

**Hậu điều kiện**:
- Bài báo được tạo với status = 'SUBMITTED'
- User được gán role AUTHOR tự động
- Chair nhận thông báo
- File bài báo được lưu trữ

**Luồng chính**:
1. Author truy cập form submit (GET /conferences/{id}/submit)
2. Hệ thống kiểm tra điều kiện:
   ```sql
   SELECT h.conference_id, h.title, h.paper_submission_deadline,
          h.min_reviewers_per_paper, h.max_reviewers_per_paper
   FROM hoithao h
   WHERE h.conference_id = ?
   AND h.status = 'ACTIVE'
   AND h.paper_submission_deadline >= NOW()
   ```
3. Nếu không đủ điều kiện → redirect với message
4. Hệ thống hiển thị form multi-step:

**Step 1: Basic Information**
- Title * (max 200 chars)
- Abstract * (max 500 words, rich text)
- Keywords * (comma-separated, 3-7 keywords)
- Track/Topic * (select từ tieuban)
- Type (Full Paper / Short Paper / Poster)

**Step 2: Authors Information** (dynamic, ít nhất 1 author)
- Full Name *
- Email *
- Affiliation *
- Country
- ORCID (optional)
- Is Corresponding Author (checkbox)
- Order (drag to reorder)

**Step 3: File Upload**
- Paper File * (PDF, max 10MB)
- Supplementary Materials (ZIP, max 50MB) - optional
- Cover Letter (optional)

**Step 4: Review & Submit**
- Preview all information
- Checkbox: "I confirm this is original work"
- Checkbox: "I agree to copyright terms"

5. Author điền đầy đủ thông tin và nhấn "Submit"
6. Hệ thống validate:
   - Required fields
   - Email format cho tất cả authors
   - File types và sizes
   - Duplicate title check
7. Hệ thống bắt đầu transaction:

a. Upload files:
```
storage/papers/{conference_id}/{timestamp}_{paper_id}/
  - paper.pdf
  - supplementary.zip (nếu có)
  - cover_letter.pdf (nếu có)
```

b. Tạo paper record:
```sql
INSERT INTO baibao (
  conference_id, track_id, title, abstract,
  keywords, type, submission_file, supplementary_file,
  status, submitted_by, submitted_at, created_at
) VALUES (
  ?, ?, ?, ?, ?, ?, ?, ?, 
  'SUBMITTED', ?, NOW(), NOW()
)
```

c. Lưu authors:
```sql
INSERT INTO paper_authors (
  paper_id, full_name, email, affiliation,
  country, orcid, is_corresponding, author_order
) VALUES (?, ?, ?, ?, ?, ?, ?, ?)
```

d. Auto-assign AUTHOR role (nếu chưa có):
```sql
INSERT INTO vaitronguoidung (
  user_id, role_code, conference_id, assigned_at
) VALUES (?, 'AUTHOR', ?, NOW())
ON DUPLICATE KEY UPDATE assigned_at = NOW()
```

e. Tạo thông báo cho Chair:
```sql
INSERT INTO user_notifications (
  user_id, title, message, type, data
)
SELECT user_id,
  'Bài báo mới được nộp',
  'Bài báo "{title}" vừa được nộp bởi {author_name}',
  'NEW_PAPER',
  JSON_OBJECT('paper_id', ?, 'conference_id', ?)
FROM vaitronguoidung
WHERE conference_id = ? AND role_code = 'CHAIR'
```

f. Gửi email confirmation đến author

g. Ghi activity log

8. Hệ thống commit transaction
9. Hệ thống hiển thị success page:
   - Paper ID
   - Submission confirmation
   - Next steps
   - Track submission link
10. Hệ thống gửi email confirmation với PDF receipt

**Luồng thay thế**:

*2a. Deadline đã qua*:
1. Hệ thống hiển thị: "Hạn nộp bài đã kết thúc"
2. Hiển thị notification deadline
3. Use case kết thúc

*6a. Title trùng*:
1. Hệ thống check:
   ```sql
   SELECT paper_id FROM baibao 
   WHERE conference_id = ? 
   AND title = ? 
   AND status != 'WITHDRAWN'
   ```
2. Hiển thị: "Tiêu đề bài báo đã tồn tại. Vui lòng sử dụng tiêu đề khác"
3. Quay lại Step 1

*7a. File upload failed*:
1. Hệ thống rollback transaction
2. Hiển thị lỗi cụ thể
3. Cho phép retry

*7b. Storage full*:
1. Hệ thống ghi log critical
2. Alert admin
3. Hiển thị: "Hệ thống tạm thời không thể nhận file. Vui lòng thử lại sau"

**Route**: 
- `GET /conferences/{id}/submit` (form)
- `POST /conferences/{id}/papers` (submit)

**Controller**: `Author\PaperController@create`, `@store`

**Database**:
- Tables: `baibao`, `paper_authors`, `vaitronguoidung`, `user_notifications`, `activity_logs`
- Storage: `storage/papers/{conference_id}/`

**Business Rules**:
- Ít nhất 1 corresponding author
- PDF only cho main paper
- Abstract không quá 500 từ
- Keywords: 3-7 keywords
- Auto-assign AUTHOR role khi submit thành công
- Paper ID format: `CONF{year}{number}` (e.g., CONF2025001)

---

### UC-15: Chỉnh sửa bài báo

**Mô tả**: Author chỉnh sửa bài báo đã nộp trước deadline

**Actor**: Author

**Tiền điều kiện**: 
- Author đã nộp bài
- Paper có status = 'SUBMITTED' hoặc 'REVISION_REQUIRED'
- Chưa quá paper_submission_deadline (cho SUBMITTED)
- Chưa quá revision_deadline (cho REVISION_REQUIRED)

**Hậu điều kiện**:
- Thông tin paper được cập nhật
- Version mới của file được lưu
- Chair nhận thông báo về update

**Luồng chính**:
1. Author truy cập "My Papers" (UC-18)
2. Author click "Edit" trên paper của mình
3. Hệ thống kiểm tra quyền edit:
   ```sql
   SELECT b.*, h.paper_submission_deadline,
          h.conference_id
   FROM baibao b
   JOIN hoithao h ON b.conference_id = h.conference_id
   WHERE b.paper_id = ?
   AND b.submitted_by = ?
   AND (
     (b.status = 'SUBMITTED' AND NOW() <= h.paper_submission_deadline)
     OR
     (b.status = 'REVISION_REQUIRED' AND NOW() <= b.revision_deadline)
   )
   ```
4. Nếu không đủ điều kiện:
   - Hiển thị lỗi: "Không thể chỉnh sửa bài báo này"
   - Use case kết thúc
5. Hệ thống load form với data hiện tại
6. Author chỉnh sửa:
   - Title
   - Abstract
   - Keywords
   - Track
   - Authors (add/remove/reorder)
   - Upload file mới (optional)
7. Author nhấn "Update"
8. Hệ thống validate changes
9. Hệ thống bắt đầu transaction:

a. Nếu có upload file mới:
```
- Backup old file: paper_v1.pdf, paper_v2.pdf...
- Upload new file: paper.pdf (latest version)
```

b. Cập nhật paper:
```sql
UPDATE baibao SET
  title = ?, abstract = ?, keywords = ?,
  track_id = ?, type = ?,
  submission_file = ?, -- nếu upload mới
  version = version + 1, -- increment version
  updated_at = NOW()
WHERE paper_id = ?
```

c. Xóa và tạo lại authors:
```sql
DELETE FROM paper_authors WHERE paper_id = ?;
INSERT INTO paper_authors (...) VALUES (...);
```

d. Ghi version history:
```sql
INSERT INTO paper_versions (
  paper_id, version, title, abstract,
  file_path, changed_by, changed_at
) VALUES (?, ?, ?, ?, ?, ?, NOW())
```

e. Tạo thông báo cho Chair:
```sql
INSERT INTO user_notifications (
  user_id, title, message, type, data
)
SELECT user_id,
  'Bài báo được cập nhật',
  'Bài báo "{title}" đã được tác giả cập nhật (v{version})',
  'PAPER_UPDATED',
  JSON_OBJECT('paper_id', ?, 'version', ?)
FROM vaitronguoidung
WHERE conference_id = ? AND role_code = 'CHAIR'
```

f. Ghi activity log

10. Hệ thống commit transaction
11. Hệ thống hiển thị: "Bài báo đã được cập nhật thành công (Version {version})"
12. Gửi email confirmation

**Luồng thay thế**:

*3a. Deadline đã qua*:
1. Hệ thống hiển thị: "Không thể chỉnh sửa sau deadline"
2. Use case kết thúc

*3b. Paper đang được review*:
1. Nếu status = 'UNDER_REVIEW':
   - Hiển thị: "Bài báo đang được phản biện, không thể chỉnh sửa"
2. Use case kết thúc

*8a. New title conflicts*:
1. Check duplicate title
2. Hiển thị lỗi
3. Quay lại bước 6

**Route**: 
- `GET /author/papers/{id}/edit` (form)
- `PUT /author/papers/{id}` (update)

**Controller**: `Author\PaperController@edit`, `@update`

**Database**:
- Tables: `baibao`, `paper_authors`, `paper_versions`, `user_notifications`, `activity_logs`
- Storage: `storage/papers/{conference_id}/{paper_id}/versions/`

**Business Rules**:
- Mỗi lần edit tăng version number
- Old files được backup, không xóa
- Chỉ author (submitted_by) mới edit được
- Không edit được khi status = 'UNDER_REVIEW', 'ACCEPTED', 'REJECTED'
- Limit số lần edit: 10 times (spam prevention)

---

### UC-16: Rút bài báo

**Mô tả**: Author rút bài báo đã nộp

**Actor**: Author

**Tiền điều kiện**: 
- Author đã nộp bài
- Paper có status = 'SUBMITTED' hoặc 'UNDER_REVIEW' hoặc 'REVISION_REQUIRED'
- Chưa bị ACCEPTED hoặc REJECTED

**Hậu điều kiện**:
- Paper status = 'WITHDRAWN'
- Không thể undo (permanent action)
- Chair và reviewers nhận thông báo

**Luồng chính**:
1. Author truy cập "My Papers"
2. Author click "Withdraw" trên paper của mình
3. Hệ thống hiển thị confirmation modal:
   - Warning: "This action cannot be undone"
   - Yêu cầu nhập lý do (required)
   - Checkbox: "I understand this is permanent"
4. Author nhập lý do và confirm
5. Hệ thống kiểm tra điều kiện:
   ```sql
   SELECT b.*, b.status
   FROM baibao b
   WHERE b.paper_id = ?
   AND b.submitted_by = ?
   AND b.status IN ('SUBMITTED', 'UNDER_REVIEW', 'REVISION_REQUIRED')
   ```
6. Hệ thống bắt đầu transaction:

a. Cập nhật status:
```sql
UPDATE baibao SET
  status = 'WITHDRAWN',
  withdrawal_reason = ?,
  withdrawn_at = NOW(),
  withdrawn_by = ?
WHERE paper_id = ?
```

b. Hủy review assignments nếu có:
```sql
UPDATE reviewer_assignments SET
  status = 'CANCELLED',
  cancelled_reason = 'Paper withdrawn by author'
WHERE paper_id = ?
AND status IN ('PENDING', 'ACCEPTED')
```

c. Thông báo cho Chair:
```sql
INSERT INTO user_notifications (
  user_id, title, message, type, data
)
SELECT user_id,
  'Bài báo bị rút',
  'Bài báo "{title}" đã bị tác giả rút. Lý do: {reason}',
  'PAPER_WITHDRAWN',
  JSON_OBJECT('paper_id', ?, 'reason', ?)
FROM vaitronguoidung
WHERE conference_id = ? AND role_code = 'CHAIR'
```

d. Thông báo cho reviewers đã được assigned:
```sql
INSERT INTO user_notifications (
  user_id, title, message, type, data
)
SELECT DISTINCT ra.user_id,
  'Assignment cancelled',
  'Bài báo bạn được phân công phản biện đã bị rút bởi tác giả',
  'ASSIGNMENT_CANCELLED',
  JSON_OBJECT('paper_id', ?)
FROM reviewer_assignments ra
WHERE ra.paper_id = ?
AND ra.status IN ('PENDING', 'ACCEPTED')
```

e. Ghi activity log

7. Hệ thống commit transaction
8. Hệ thống hiển thị: "Bài báo đã được rút thành công"
9. Gửi email confirmation
10. Redirect về "My Papers"

**Luồng thay thế**:

*5a. Paper đã ACCEPTED*:
1. Hệ thống hiển thị lỗi: "Không thể rút bài báo đã được chấp nhận. Vui lòng liên hệ Chair"
2. Hiển thị email Chair
3. Use case kết thúc

*5b. Paper đã REJECTED*:
1. Hệ thống hiển thị: "Bài báo đã bị từ chối, không cần rút"
2. Use case kết thúc

*4a. Author không nhập lý do*:
1. Hệ thống yêu cầu: "Vui lòng nhập lý do rút bài"
2. Quay lại bước 3

**Route**: 
- `POST /author/papers/{id}/withdraw` (withdraw)

**Controller**: `Author\PaperController@withdraw`

**Database**:
- Tables: `baibao`, `reviewer_assignments`, `user_notifications`, `activity_logs`

**Business Rules**:
- Withdrawal là permanent, không thể undo
- Lý do rút bài là bắt buộc
- Không thể rút bài đã ACCEPTED/REJECTED
- Sau khi rút, author có thể nộp bài mới với title khác
- Files vẫn được giữ trong storage (archival)

---

### UC-17: Xem trạng thái bài báo

**Mô tả**: Author theo dõi trạng thái và timeline của bài báo

**Actor**: Author

**Tiền điều kiện**: 
- Author đã nộp ít nhất 1 bài báo

**Hậu điều kiện**: 
- Hiển thị chi tiết trạng thái và history

**Luồng chính**:
1. Author truy cập chi tiết bài báo (GET /author/papers/{id})
2. Hệ thống kiểm tra quyền truy cập:
   ```sql
   SELECT b.*, h.title as conference_title,
          tb.title as track_title,
          h.notification_deadline,
          h.camera_ready_deadline
   FROM baibao b
   JOIN hoithao h ON b.conference_id = h.conference_id
   LEFT JOIN tieuban tb ON b.track_id = tb.track_id
   WHERE b.paper_id = ?
   AND b.submitted_by = ?
   ```
3. Hệ thống query thêm thông tin:
   
a. Authors:
```sql
SELECT * FROM paper_authors 
WHERE paper_id = ? 
ORDER BY author_order
```

b. Review status:
```sql
SELECT COUNT(*) as total_assigned,
       SUM(CASE WHEN status = 'COMPLETED' THEN 1 ELSE 0 END) as completed
FROM reviewer_assignments
WHERE paper_id = ?
```

c. Timeline/Activity history:
```sql
SELECT action, description, created_at, created_by
FROM paper_activities
WHERE paper_id = ?
ORDER BY created_at DESC
```

d. Reviews (nếu có và status cho phép):
```sql
SELECT r.overall_score, r.recommendation,
       r.strengths, r.weaknesses, r.comments,
       r.submitted_at,
       u.full_name as reviewer_name -- ẩn nếu double-blind
FROM phanbien r
JOIN nguoidung u ON r.reviewer_id = u.user_id
WHERE r.paper_id = ?
AND r.status = 'COMPLETED'
```

4. Hệ thống hiển thị trang chi tiết với các sections:

**Paper Information**
- Title, Abstract, Keywords
- Track, Type, Version
- Authors list
- Files: Download links cho paper, supplementary

**Status Timeline** (visual timeline)
```
✓ SUBMITTED - [date]
✓ UNDER_REVIEW - [date] 
⏳ WAITING_DECISION - Current
○ NOTIFICATION - Expected [notification_deadline]
```

**Review Progress** (nếu đang review)
- Progress bar: "2/3 reviews completed"
- Estimated completion date

**Decision** (nếu có)
- Status: ACCEPTED / REJECTED / REVISION_REQUIRED
- Decision date
- Chair's comments (nếu có)

**Reviews** (nếu notification deadline đã qua)
- Reviewer 1:
  - Score: 8/10
  - Recommendation: Accept
  - Strengths: [...]
  - Weaknesses: [...]
  - Comments: [...]
- Reviewer 2: [...]

**Actions** (buttons based on status)
- Edit (nếu SUBMITTED và chưa quá deadline)
- Withdraw (nếu có thể)
- Upload Camera-Ready (nếu ACCEPTED)
- Submit Revision (nếu REVISION_REQUIRED)

**Activity Log**
- Submission: [date] by [author]
- Updated to v2: [date] by [author]
- Assigned to reviewers: [date]
- Review 1 completed: [date]
- Review 2 completed: [date]
- Decision: ACCEPTED [date]

**Luồng thay thế**:

*2a. Không có quyền truy cập*:
1. Hệ thống hiển thị lỗi 403: "Access denied"
2. Use case kết thúc

*4a. Status = SUBMITTED*:
1. Hiển thị: "Your paper is waiting for review assignment"
2. Không hiển thị review section

*4b. Status = UNDER_REVIEW*:
1. Hiển thị review progress
2. Không hiển thị review content (blind review)

*4c. Status = ACCEPTED*:
1. Hiển thị congratulation message
2. Hiển thị "Upload Camera-Ready" button
3. Hiển thị registration instructions

*4d. Status = REJECTED*:
1. Hiển thị reviews (để author học hỏi)
2. Hiển thị "Submit to another conference" suggestion

**Route**: `GET /author/papers/{id}`

**Controller**: `Author\PaperController@show`

**Database**:
- Tables: `baibao`, `paper_authors`, `reviewer_assignments`, `phanbien`, `paper_activities`, `hoithao`, `tieuban`

**Business Rules**:
- Reviews chỉ hiển thị sau notification_deadline
- Double-blind: Ẩn reviewer names
- Author chỉ thấy papers của mình
- Activity log không hiển thị sensitive information (reviewer names)

---

### UC-18: Quản lý bài báo của tôi

**Mô tả**: Author xem danh sách tất cả bài báo đã nộp

**Actor**: Author

**Tiền điều kiện**: 
- Author đã đăng nhập

**Hậu điều kiện**: 
- Hiển thị danh sách papers với filters

**Luồng chính**:
1. Author truy cập "My Papers" (GET /author/papers)
2. Hệ thống query papers của author:
   ```sql
   SELECT b.paper_id, b.title, b.status,
          b.submitted_at, b.version,
          h.title as conference_title,
          h.acronym as conference_acronym,
          h.paper_submission_deadline,
          h.notification_deadline,
          tb.title as track_title,
          COUNT(DISTINCT ra.assignment_id) as reviewers_assigned,
          COUNT(DISTINCT r.review_id) as reviews_completed
   FROM baibao b
   JOIN hoithao h ON b.conference_id = h.conference_id
   LEFT JOIN tieuban tb ON b.track_id = tb.track_id
   LEFT JOIN reviewer_assignments ra ON b.paper_id = ra.paper_id
   LEFT JOIN phanbien r ON b.paper_id = r.paper_id 
        AND r.status = 'COMPLETED'
   WHERE b.submitted_by = ?
   GROUP BY b.paper_id
   ORDER BY b.submitted_at DESC
   ```
3. Hệ thống hiển thị table/grid với columns:
   - **Paper Title** (clickable → UC-17)
   - **Conference** (acronym)
   - **Track**
   - **Status** (badge với màu)
     - SUBMITTED: blue
     - UNDER_REVIEW: yellow
     - ACCEPTED: green
     - REJECTED: red
     - WITHDRAWN: gray
     - REVISION_REQUIRED: orange
   - **Submitted** (date)
   - **Reviews** (e.g., "2/3")
   - **Actions** (dropdown)
     - View Details
     - Edit (nếu có thể)
     - Withdraw (nếu có thể)
     - Upload Camera-Ready (nếu ACCEPTED)

4. Author có thể:
   - Filter by:
     - Conference
     - Status
     - Date range
   - Sort by:
     - Submission date
     - Status
     - Conference
   - Search by title

5. Hiển thị statistics summary:
   - Total Papers: X
   - Accepted: Y
   - Under Review: Z
   - Pending: W

**Luồng thay thế**:

*2a. Chưa có paper nào*:
1. Hệ thống hiển thị empty state:
   - "You haven't submitted any papers yet"
   - Button: "Browse Conferences"
   - Button: "Submit Your First Paper"

*4a. Click "Edit" trên paper*:
1. Chuyển sang UC-15 (Chỉnh sửa bài báo)

*4b. Click "Withdraw"*:
1. Chuyển sang UC-16 (Rút bài báo)

*4c. Click "Upload Camera-Ready"*:
1. Chuyển sang UC-19 (Upload Camera-Ready)

**Route**: `GET /author/papers`

**Controller**: `Author\PaperController@index`

**Database**:
- Tables: `baibao`, `hoithao`, `tieuban`, `reviewer_assignments`, `phanbien`

**Business Rules**:
- Chỉ hiển thị papers mà user là submitted_by
- Papers được sort theo submitted_at DESC mặc định
- Pagination: 20 papers/page
- Cache danh sách 5 phút

---

### UC-19: Upload Camera-Ready

**Mô tả**: Author upload bản chính thức sau khi bài báo được chấp nhận

**Actor**: Author

**Tiền điều kiện**: 
- Paper có status = 'ACCEPTED'
- Chưa quá camera_ready_deadline
- Author đã xử lý các comments từ reviewers

**Hậu điều kiện**:
- Camera-ready file được lưu
- Paper status = 'CAMERA_READY_SUBMITTED'
- Chair nhận thông báo

**Luồng chính**:
1. Author truy cập paper detail (UC-17)
2. System hiển thị "Upload Camera-Ready" button nổi bật
3. Author click button
4. Hệ thống kiểm tra điều kiện:
   ```sql
   SELECT b.*, h.camera_ready_deadline,
          h.conference_id
   FROM baibao b
   JOIN hoithao h ON b.conference_id = h.conference_id
   WHERE b.paper_id = ?
   AND b.submitted_by = ?
   AND b.status = 'ACCEPTED'
   AND NOW() <= h.camera_ready_deadline
   ```
5. Hệ thống hiển thị form upload:

**Instructions**
- Review all comments from reviewers
- Follow formatting guidelines
- Check paper template compliance
- File format: PDF only
- File size: Max 10MB
- Include all author information
- Update copyright notice

**Upload Camera-Ready File**
- File upload field *
- Drag & drop support
- Preview after upload

**Additional Materials** (optional)
- Source files (LaTeX, Word) - ZIP
- Presentation slides (for conference)
- Poster (PDF)

**Copyright Form**
- Checkbox: "I have completed the copyright form" *
- Upload copyright form (PDF) *

**Checklist** (must check all)
- [ ] Addressed all reviewer comments *
- [ ] Followed formatting guidelines *
- [ ] Checked for typos and errors *
- [ ] Updated acknowledgments *
- [ ] Included all required sections *

6. Author upload file và complete checklist
7. Author nhấn "Submit Camera-Ready"
8. Hệ thống validate:
   - File type = PDF
   - File size <= 10MB
   - Copyright form uploaded
   - All checklist items checked
9. Hệ thống bắt đầu transaction:

a. Upload files:
```
storage/papers/{conference_id}/{paper_id}/camera-ready/
  - camera_ready.pdf
  - source_files.zip (nếu có)
  - presentation.pptx (nếu có)
  - copyright_form.pdf
```

b. Cập nhật paper:
```sql
UPDATE baibao SET
  camera_ready_file = ?,
  camera_ready_submitted_at = NOW(),
  status = 'CAMERA_READY_SUBMITTED',
  source_files = ?, -- nếu có
  copyright_form = ?
WHERE paper_id = ?
```

c. Tạo version record:
```sql
INSERT INTO paper_versions (
  paper_id, version, version_type, file_path,
  changed_by, changed_at
) VALUES (?, 'camera-ready', 'FINAL', ?, ?, NOW())
```

d. Thông báo Chair:
```sql
INSERT INTO user_notifications (
  user_id, title, message, type, data
)
SELECT user_id,
  'Camera-ready uploaded',
  'Bài báo "{title}" đã upload camera-ready version',
  'CAMERA_READY_UPLOADED',
  JSON_OBJECT('paper_id', ?)
FROM vaitronguoidung
WHERE conference_id = ? AND role_code = 'CHAIR'
```

e. Ghi activity log

10. Hệ thống commit transaction
11. Hệ thống hiển thị success message:
    - "Camera-ready version uploaded successfully!"
    - Next steps: Registration, Presentation preparation
12. Gửi email confirmation với:
    - Submission receipt
    - Conference registration link
    - Presentation guidelines

**Luồng thay thế**:

*4a. Deadline đã qua*:
1. Hệ thống hiển thị: "Camera-ready deadline has passed"
2. Hiển thị: "Please contact the chair for extension"
3. Hiển thị email chair
4. Use case kết thúc

*4b. Status không phải ACCEPTED*:
1. Hệ thống hiển thị: "Only accepted papers can upload camera-ready"
2. Use case kết thúc

*8a. File validation failed*:
1. Hiển thị lỗi cụ thể (format, size)
2. Quay lại bước 6

*8b. Checklist chưa đủ*:
1. Highlight các items chưa check
2. Yêu cầu complete trước khi submit

**Luồng bổ sung - Reupload**:
1. Author có thể reupload trước deadline
2. Hệ thống backup old version
3. Version number tăng: camera-ready-v1, camera-ready-v2

**Route**: 
- `GET /author/papers/{id}/camera-ready` (form)
- `POST /author/papers/{id}/camera-ready` (upload)

**Controller**: `Author\PaperController@showCameraReadyForm`, `@uploadCameraReady`

**Database**:
- Tables: `baibao`, `paper_versions`, `user_notifications`, `activity_logs`
- Storage: `storage/papers/{conference_id}/{paper_id}/camera-ready/`

**Business Rules**:
- Chỉ ACCEPTED papers mới upload được
- Copyright form là bắt buộc
- Cho phép reupload trước deadline
- Old versions được backup
- File size limit: 10MB cho PDF, 50MB cho source files

---

### UC-20: Xem danh sách bài báo (Chair)

**Mô tả**: Chair xem và quản lý tất cả bài báo của hội thảo

**Actor**: Chair

**Tiền điều kiện**: 
- User có role CHAIR cho conference

**Hậu điều kiện**: 
- Hiển thị danh sách papers với filters và actions

**Luồng chính**:
1. Chair truy cập paper management (GET /chair/conferences/{id}/papers)
2. Hệ thống kiểm tra quyền:
   ```sql
   SELECT * FROM vaitronguoidung
   WHERE user_id = ?
   AND conference_id = ?
   AND role_code = 'CHAIR'
   ```
3. Hệ thống query papers:
   ```sql
   SELECT b.paper_id, b.title, b.abstract, b.status,
          b.type, b.submitted_at, b.version,
          tb.title as track_title,
          u.full_name as author_name,
          u.email as author_email,
          COUNT(DISTINCT ra.assignment_id) as reviewers_count,
          COUNT(DISTINCT r.review_id) as reviews_count,
          AVG(r.overall_score) as avg_score
   FROM baibao b
   JOIN nguoidung u ON b.submitted_by = u.user_id
   LEFT JOIN tieuban tb ON b.track_id = tb.track_id
   LEFT JOIN reviewer_assignments ra ON b.paper_id = ra.paper_id
        AND ra.status IN ('ACCEPTED', 'COMPLETED')
   LEFT JOIN phanbien r ON b.paper_id = r.paper_id
        AND r.status = 'COMPLETED'
   WHERE b.conference_id = ?
   GROUP BY b.paper_id
   ORDER BY b.submitted_at DESC
   ```
4. Hệ thống hiển thị dashboard với sections:

**Statistics Summary**
- Total Submissions: X
- Pending Review: Y
- Under Review: Z
- Decisions Made: W
- Acceptance Rate: XX%

**Filters & Search**
- Status filter (all/submitted/under-review/accepted/rejected)
- Track filter
- Date range
- Search by title/author

**Papers Table** (responsive, sortable)
Columns:
- ID (paper_id)
- Title (clickable)
- Author (submitted_by)
- Track
- Type (Full/Short/Poster)
- Status (badge)
- Reviews (e.g., "3/3" với progress bar)
- Avg Score (nếu có reviews)
- Submitted (date)
- Actions (dropdown):
  - View Details
  - Assign Reviewers
  - View Reviews
  - Make Decision
  - Download PDF
  - Send Message to Author

**Bulk Actions**
- Select multiple papers
- Bulk assign to reviewer
- Bulk status update
- Export selected (CSV/Excel)

5. Chair có thể:
   - Click vào paper → View details
   - Click "Assign Reviewers" → UC-23 (Assign reviewers)
   - Click "Make Decision" → UC-30 (Make decision)
   - Sort by any column
   - Filter by multiple criteria
   - Export data

**View Modes**
- Table view (default)
- Card view (visual)
- Kanban board (by status)

6. Hệ thống cung cấp quick stats cho mỗi track:
   ```sql
   SELECT tb.title as track_name,
          COUNT(b.paper_id) as total_papers,
          SUM(CASE WHEN b.status = 'ACCEPTED' THEN 1 ELSE 0 END) as accepted,
          AVG(r.overall_score) as avg_score
   FROM tieuban tb
   LEFT JOIN baibao b ON tb.track_id = b.track_id
   LEFT JOIN phanbien r ON b.paper_id = r.paper_id
        AND r.status = 'COMPLETED'
   WHERE tb.conference_id = ?
   GROUP BY tb.track_id
   ```

**Luồng thay thế**:

*2a. Không có quyền CHAIR*:
1. Hệ thống hiển thị lỗi 403: "Access denied"
2. Use case kết thúc

*3a. Chưa có paper nào*:
1. Hiển thị empty state:
   - "No papers submitted yet"
   - "Submissions will appear here after deadline"
   - Link to "View CFP"

**Luồng bổ sung - Export**:
1. Chair click "Export"
2. Chọn format: CSV / Excel / PDF
3. Chọn columns to export
4. Hệ thống generate file
5. Download file

**Route**: `GET /chair/conferences/{conferenceId}/papers`

**Controller**: `Chair\PaperController@index`

**Database**:
- Tables: `baibao`, `nguoidung`, `tieuban`, `reviewer_assignments`, `phanbien`, `vaitronguoidung`

**Business Rules**:
- Chair thấy tất cả papers của conference
- Real-time stats update
- Pagination: 50 papers/page
- Cache 2 phút
- Export limit: 1000 papers

---

## 📊 TỔNG KẾT NHÓM 2

### Thống kê:
- **Tổng số UC**: 10
- **Actors**: Guest (3 UC), Author (6 UC), Chair (1 UC), System (auto-triggers)
- **Database tables**: baibao, paper_authors, paper_versions, paper_activities, hoithao, tieuban, vaitronguoidung, reviewer_assignments, phanbien, user_notifications, activity_logs, download_logs

### Workflow chính:
```
Guest → UC-11 (View conference) → UC-12 (View CFP) → UC-13 (Download template)
User → UC-14 (Submit paper) → System auto-assign AUTHOR role
Author → UC-15 (Edit paper) / UC-16 (Withdraw) / UC-17 (Track status)
Author → UC-18 (My papers dashboard)
Paper ACCEPTED → Author UC-19 (Upload camera-ready)
Chair → UC-20 (Manage all papers)
```

### Mối quan hệ giữa các UC:
- UC-11 → UC-12 (include): Conference detail bao gồm CFP
- UC-11 → UC-14 (extend): From conference detail có thể submit
- UC-14 → System (trigger): Auto-assign AUTHOR role
- UC-15 → UC-14 (extend): Edit extends submit functionality
- UC-17 → UC-19 (conditional): Chỉ ACCEPTED papers mới upload camera-ready
- UC-18 → UC-17, UC-15, UC-16 (navigate): Dashboard dẫn đến các actions

### Key Business Rules:
1. **Submission Rules**:
   - Must login và verify email để submit
   - Paper submission chỉ trong deadline
   - Auto-assign AUTHOR role khi submit thành công
   - Title phải unique trong conference

2. **Editing Rules**:
   - Chỉ edit được SUBMITTED papers trước deadline
   - Mỗi edit tăng version number
   - Old versions được backup
   - Limit 10 edits (spam prevention)

3. **Withdrawal Rules**:
   - Permanent action, cannot undo
   - Lý do rút bài là required
   - Không withdraw được ACCEPTED/REJECTED papers
   - Cancel tất cả review assignments

4. **Camera-Ready Rules**:
   - Chỉ ACCEPTED papers
   - Copyright form bắt buộc
   - Must complete checklist
   - Allow reupload before deadline

5. **File Management**:
   - PDF only cho papers
   - Max 10MB cho paper file
   - Max 50MB cho supplementary files
   - Version control cho tất cả uploads

### Storage Structure:
```
storage/papers/
  {conference_id}/
    {paper_id}/
      - paper.pdf (current version)
      - supplementary.zip
      versions/
        - paper_v1.pdf
        - paper_v2.pdf
      camera-ready/
        - camera_ready.pdf
        - source_files.zip
        - copyright_form.pdf
```

---

**File này là phần 2 trong series đặc tả Use Case. Tiếp theo: Nhóm 3 - Reviewer Management...**
