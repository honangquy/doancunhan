# 📋 KỊCH BẢN KIỂM THỬ HỆ THỐNG QUẢN LÝ HỘI THẢO KHOA HỌC

**Dự án:** HUIT Conference Management System  
**Phiên bản:** 2.0  
**Ngày:** 28/11/2025  
**Người kiểm thử:** QA Team  
**Ghi chú:** Test scenarios dựa 100% vào routes thực tế trong web.php và views thực tế

---

## 📑 MỤC LỤC

1. [Authentication & Authorization](#1-authentication--authorization)
2. [Conference Request](#2-conference-request)
3. [Admin - Conference Management](#3-admin---conference-management)
4. [Chair - Conference Setup](#4-chair---conference-setup)
5. [Chair - Paper Management](#5-chair---paper-management)
6. [Chair - Reviewer Management](#6-chair---reviewer-management)
7. [Chair - Announcements (Broadcast)](#7-chair---announcements-broadcast)
8. [Chair - Proceedings](#8-chair---proceedings)
9. [Author - Paper Submission](#9-author---paper-submission)
10. [Reviewer - Bidding & COI](#10-reviewer---bidding--coi)
11. [Reviewer - Assignments & Reviews](#11-reviewer---assignments--reviews)
12. [Mobile API](#12-mobile-api)

---

## 1. AUTHENTICATION & AUTHORIZATION

### TC-AUTH-001: Đăng nhập thành công
**Route:** `GET /login`, `POST /login`  
**Controller:** `AuthController::showLoginForm`, `AuthController::login`  
**View:** `resources/views/auth/login.blade.php`

**Các bước:**
1. Truy cập `http://127.0.0.1:8000/login`
2. Nhập email: `dangtrucquynh04@gmail.com`
3. Nhập password: `123456789`
4. Click "Đăng nhập"

**Kết quả mong đợi:**
- ✅ Redirect đến `/dashboard`
- ✅ Session được tạo
- ✅ Cookie `laravel_session` có giá trị

---

### TC-AUTH-002: Đăng nhập thất bại - Sai password
**Route:** `POST /login`

**Các bước:**
1. Nhập email đúng
2. Nhập password sai
3. Submit

**Kết quả mong đợi:**
- ❌ Không đăng nhập được
- ⚠️ Hiển thị validation error
- 🔄 Form được giữ lại

---

### TC-AUTH-003: Đăng ký tài khoản mới
**Route:** `GET /register`, `POST /register`  
**View:** `resources/views/auth/register.blade.php`  
**Controller:** `AuthController::showRegistrationForm`, `AuthController::register`

**Các bước:**
1. Truy cập `/register`
2. Nhập:
   - Full Name: `Test User`
   - Email: `testuser@huit.edu.vn`
   - Password: `Test@123456`
   - Confirm Password: `Test@123456`
3. Click "Đăng ký"

**Kết quả mong đợi:**
- ✅ Tài khoản được tạo trong `nguoidung`
- ✅ Redirect đến `/email/verify`
- ✅ Email verification được gửi

---

### TC-AUTH-004: Email Verification
**Route:** `GET /email/verify`, `GET /email/verify/{id}/{hash}`  
**View:** `resources/views/auth/verify-email.blade.php`  
**Controller:** `AuthController::verifyEmailNotice`, `AuthController::verifyEmail`

**Các bước:**
1. Click link trong email verification
2. Kiểm tra status

**Kết quả mong đợi:**
- ✅ `email_verified_at` được set
- ✅ Redirect đến dashboard
- ✅ Có thể truy cập các trang yêu cầu verified

---

### TC-AUTH-005: Forgot Password
**Route:** `GET /forgot-password`, `POST /forgot-password`  
**View:** `resources/views/auth/forgot-password.blade.php`  
**Controller:** `AuthController::showForgotPasswordForm`, `AuthController::sendResetLinkEmail`

**Các bước:**
1. Truy cập `/forgot-password`
2. Nhập email đã đăng ký
3. Click "Gửi link reset"

**Kết quả mong đợi:**
- ✅ Email reset được gửi
- ✅ Token được tạo trong `password_resets`

---

### TC-AUTH-006: Reset Password
**Route:** `GET /reset-password/{token}`, `POST /reset-password`  
**View:** `resources/views/auth/reset-password.blade.php`  
**Controller:** `AuthController::showResetPasswordForm`, `AuthController::resetPassword`

**Các bước:**
1. Click link trong email
2. Nhập password mới
3. Confirm password
4. Submit

**Kết quả mong đợi:**
- ✅ Password được cập nhật
- ✅ Token bị xóa
- ✅ Redirect đến login

---

### TC-AUTH-007: Logout
**Route:** `POST /logout`  
**Controller:** `AuthController::logout`

**Các bước:**
1. Click "Đăng xuất"
2. Confirm

**Kết quả mong đợi:**
- ✅ Session bị xóa
- ✅ Redirect về `/login`
- ❌ Không truy cập được trang authenticated

---

### TC-AUTH-008: Profile Management
**Route:** `GET /profile`, `PUT /profile`  
**View:** `resources/views/auth/profile.blade.php`  
**Controller:** `AuthController::profile`, `AuthController::updateProfile`

**Các bước:**
1. Truy cập `/profile`
2. Cập nhật thông tin
3. Click "Cập nhật"

**Kết quả mong đợi:**
- ✅ Thông tin được cập nhật
- ✅ Hiển thị thông báo thành công

---

### TC-AUTH-009: Avatar Upload
**Route:** `POST /profile/avatar`  
**Controller:** `AuthController::updateAvatar`

**Các bước:**
1. Tại profile
2. Upload ảnh (JPG/PNG, < 2MB)
3. Submit

**Kết quả mong đợi:**
- ✅ Ảnh lưu vào `public/avatars/`
- ✅ Avatar path update trong DB
- ✅ Avatar hiển thị trên header

---

## 2. CONFERENCE REQUEST

### TC-CONF-REQ-001: Tạo yêu cầu tổ chức hội thảo
**Route:** `GET /create-conference`, `POST /conference-requests`  
**View:** `resources/views/conference-request/create.blade.php`  
**Controller:** `ConferenceRequestController::create`, `ConferenceRequestController::store`

**Điều kiện:** User đã verified email

**Các bước:**
1. Truy cập `/create-conference`
2. Nhập thông tin:
   - Tên hội thảo: `HUIT Tech Conference 2025`
   - Mục tiêu: `Hội thảo về công nghệ thông tin`
   - Cấp: `TRUONG`
   - Họ tên Chair, Email, Phone
   - Upload file đề xuất (PDF)
3. Thêm Co-chairs (optional)
4. Click "Gửi yêu cầu"

**Kết quả mong đợi:**
- ✅ Record tạo trong `yeucauhoithao`
- ✅ Status: `PENDING`
- ✅ File lưu vào storage
- ✅ Thông báo thành công

---

### TC-CONF-REQ-002: Xem danh sách requests
**Route:** `GET /conference-management/requests`  
**Controller:** `ConferenceManagementController::requests`

**Các bước:**
1. Truy cập `/conference-management/requests`

**Kết quả mong đợi:**
- ✅ Hiển thị requests của user
- ✅ Hiển thị status
- ✅ Có thể xem chi tiết

---

## 3. ADMIN - CONFERENCE MANAGEMENT

### TC-ADMIN-001: Xem danh sách Conference Requests
**Route:** `GET /admin/conference-requests`  
**View:** `resources/views/admin/conference-requests/index.blade.php`  
**Middleware:** `role:ADMIN`

**Các bước:**
1. Đăng nhập ADMIN
2. Truy cập `/admin/conference-requests`

**Kết quả mong đợi:**
- ✅ Hiển thị tất cả requests
- ✅ Filter theo status
- ✅ Nút Approve/Reject

---

### TC-ADMIN-002: Duyệt yêu cầu (Bước 1)
**Route:** `POST /admin/conference-requests/{id}/approve`  
**Controller:** `AdminConferenceRequestController::approve`

**Các bước:**
1. Tại request detail
2. Click "Phê duyệt"
3. Confirm

**Kết quả mong đợi:**
- ✅ Status → `APPROVED`
- ✅ Email gửi Chair
- ✅ Chair configure conference

---

### TC-ADMIN-003: Từ chối yêu cầu
**Route:** `POST /admin/conference-requests/{id}/reject`  
**Controller:** `AdminConferenceRequestController::reject`

**Các bước:**
1. Click "Từ chối"
2. Nhập lý do
3. Submit

**Kết quả mong đợi:**
- ✅ Status → `REJECTED`
- ✅ Lý do được lưu
- ✅ Email gửi người tạo

---

### TC-ADMIN-004: Xem Configured Conferences (Bước 2)
**Route:** `GET /admin/configured-conferences`  
**View:** `resources/views/admin/configured-conferences/index.blade.php`  
**Controller:** `AdminConferenceRequestController::configuredConferences`

**Các bước:**
1. Truy cập `/admin/configured-conferences`

**Kết quả mong đợi:**
- ✅ Hiển thị conferences đã configure bởi Chair
- ✅ Chờ phê duyệt final

---

### TC-ADMIN-005: Duyệt Configuration (Bước 2 - Final Approval)
**Route:** `POST /admin/conference-requests/{id}/approve-conference`  
**Controller:** `AdminConferenceRequestController::approveConference`

**Điều kiện:** Request APPROVED, Chair đã configure

**Các bước:**
1. Tại `/admin/configured-conferences/{id}`
2. Xem chi tiết (tracks, deadlines)
3. Click "Phê duyệt cấu hình"

**Kết quả mong đợi:**
- ✅ Conference status → `ACTIVE`
- ✅ Xuất hiện ở homepage
- ✅ Authors có thể nộp bài

---

### TC-ADMIN-006: Quản lý Users
**Route:** `GET /admin/users`  
**View:** `resources/views/admin/users.blade.php`  
**Controller:** `AdminUserController::index`

**Các bước:**
1. Truy cập `/admin/users`
2. Search, filter

**Kết quả mong đợi:**
- ✅ Hiển thị tất cả users
- ✅ Có thể edit, lock/unlock
- ✅ Verify email manually

---

### TC-ADMIN-007: Lock User Account
**Route:** `POST /admin/users/{id}/lock`  
**Controller:** `AdminUserController::lock`

**Các bước:**
1. Tại user list
2. Click "Lock"
3. Confirm

**Kết quả mong đợi:**
- ✅ `is_locked = 1`
- ✅ User không login được
- ✅ Badge "Locked" hiển thị

---

### TC-ADMIN-008: Xem System Logs
**Route:** `GET /admin/logs`  
**View:** `resources/views/admin/logs.blade.php`  
**Controller:** `ActivityLogController::index`

**Các bước:**
1. Truy cập `/admin/logs`
2. Filter theo level, date

**Kết quả mong đợi:**
- ✅ Hiển thị activity logs
- ✅ Filter hoạt động
- ✅ Export được

---

### TC-ADMIN-009: Backup Database
**Route:** `GET /admin/settings`  
**View:** `resources/views/admin/settings.blade.php`  
**Controller:** `BackupController`

**Các bước:**
1. Truy cập `/admin/settings`
2. Click "Tạo Backup"

**Kết quả mong đợi:**
- ✅ Backup file được tạo
- ✅ Có thể download
- ✅ Có thể restore

---

## 4. CHAIR - CONFERENCE SETUP

### TC-CHAIR-001: Configure Conference (sau APPROVED)
**Route:** `GET /chair/conferences/configure/{requestId}`, `POST /chair/conferences/configure/{requestId}`  
**View:** `resources/views/chair/conferences/configure.blade.php`  
**Controller:** `ConferenceSetupController::configure`, `ConferenceSetupController::store`

**Điều kiện:** Request đã APPROVED bởi Admin

**Các bước:**
1. Truy cập `/chair/conferences/configure/{requestId}`
2. Nhập:
   - Submission Deadline
   - Review Deadline
   - Camera-ready Deadline
   - Ngày bắt đầu/kết thúc
3. Tạo Tracks:
   - Track 1: `AI`
   - Track 2: `Software Engineering`
4. Click "Lưu cấu hình"

**Kết quả mong đợi:**
- ✅ Conference tạo trong `hoithao`
- ✅ Tracks tạo trong `tieuban`
- ✅ Chair role được gán
- ✅ Redirect đến conference detail

---

### TC-CHAIR-002: Xem danh sách Conferences
**Route:** `GET /chair/conferences`  
**View:** `resources/views/chair/conferences/index.blade.php`  
**Controller:** `ConferenceSetupController::index`

**Các bước:**
1. Truy cập `/chair/conferences`

**Kết quả mong đợi:**
- ✅ Hiển thị conferences mà user là Chair
- ✅ Không hiển thị conferences khác

---

### TC-CHAIR-003: Xem chi tiết Conference
**Route:** `GET /chair/conferences/{conferenceId}`  
**View:** `resources/views/chair/conferences/show.blade.php`  
**Controller:** `ConferenceSetupController::show`

**Các bước:**
1. Click vào conference

**Kết quả mong đợi:**
- ✅ Hiển thị thông tin conference
- ✅ Hiển thị tracks
- ✅ Statistics (papers, reviewers)
- ✅ Nút Edit

---

### TC-CHAIR-004: Edit Conference
**Route:** `GET /chair/conferences/{conferenceId}/edit`, `PUT /chair/conferences/{conferenceId}`  
**View:** `resources/views/chair/conferences/edit.blade.php`  
**Controller:** `ConferenceSetupController::edit`, `ConferenceSetupController::update`

**Các bước:**
1. Click "Edit"
2. Cập nhật deadlines, tracks
3. Submit

**Kết quả mong đợi:**
- ✅ Thông tin được update
- ✅ Tracks thêm/sửa/xóa được
- ✅ Thông báo thành công

---

## 5. CHAIR - PAPER MANAGEMENT

### TC-CHAIR-005: Xem danh sách Papers
**Route:** `GET /chair/papers`  
**View:** `resources/views/chair/papers/index.blade.php`  
**Controller:** `ChairController::papers`

**Các bước:**
1. Truy cập `/chair/papers`
2. Filter theo conference

**Kết quả mong đợi:**
- ✅ Hiển thị papers của conferences mình là Chair
- ✅ Filter theo status
- ✅ Search theo title

---

### TC-CHAIR-006: Xem chi tiết Paper
**Route:** `GET /chair/papers/{id}`  
**View:** `resources/views/chair/papers/show.blade.php`  
**Controller:** `ChairController::showPaper`

**Các bước:**
1. Click vào paper

**Kết quả mong đợi:**
- ✅ Hiển thị thông tin paper
- ✅ Hiển thị authors
- ✅ Nút Download PDF
- ✅ Hiển thị assignments
- ✅ Hiển thị reviews (nếu có)

---

### TC-CHAIR-007: Download Paper PDF
**Route:** `GET /chair/papers/{id}/download`  
**Controller:** `ChairController::downloadPaper`

**Các bước:**
1. Tại paper detail
2. Click "Download PDF"

**Kết quả mong đợi:**
- ✅ PDF được download
- ✅ Filename đúng format

---

### TC-CHAIR-008: Xem Reviews
**Route:** `GET /chair/papers/{id}/reviews`  
**Controller:** `ChairController::reviews`

**Điều kiện:** Paper đã có reviews

**Các bước:**
1. Tab "Reviews"

**Kết quả mong đợi:**
- ✅ Hiển thị tất cả reviews
- ✅ Reviewer name, scores, comments
- ✅ Recommendation (ACCEPT/REJECT)

---

### TC-CHAIR-009: Make Final Decision
**Route:** `GET /chair/papers/{id}/decision`, `POST /chair/papers/{id}/decision`  
**Controller:** `ChairController::makeDecision`, `ChairController::storeDecision`

**Điều kiện:** Đã có đủ reviews

**Các bước:**
1. Click "Quyết định"
2. Chọn ACCEPT/REJECT
3. Nhập lý do
4. Submit

**Kết quả mong đợi:**
- ✅ Decision lưu trong `baibao`
- ✅ Status thay đổi
- ✅ Email gửi authors
- ✅ Không thay đổi được sau khi quyết định

---

## 6. CHAIR - REVIEWER MANAGEMENT

### TC-CHAIR-010: Invite Reviewer
**Route:** `POST /chair/reviewers/invite/send`  
**View:** `resources/views/chair/reviewers/invite.blade.php`  
**Controller:** `ReviewerInvitationController::sendInvitation`

**Các bước:**
1. Truy cập `/chair/reviewers/invite`
2. Chọn conference
3. Nhập email, họ tên reviewer
4. Click "Gửi lời mời"

**Kết quả mong đợi:**
- ✅ Invitation tạo trong `reviewer_invitations`
- ✅ Token unique được tạo
- ✅ Email gửi đến reviewer
- ✅ Link: `/reviewer/invitation/{token}`

---

### TC-CHAIR-011: Xem Invitations
**Route:** `GET /chair/reviewers/invitations`  
**Controller:** `ReviewerInvitationController::sentInvitations`

**Các bước:**
1. Truy cập `/chair/reviewers/invitations`

**Kết quả mong đợi:**
- ✅ Hiển thị invitations đã gửi
- ✅ Status: PENDING/ACCEPTED/DECLINED/EXPIRED
- ✅ Nút Resend, Revoke

---

### TC-CHAIR-012: Cấu hình Bidding
**Route:** `GET /chair/bidding-settings`, `PUT /chair/conferences/{conferenceId}/bidding-settings`  
**View:** `resources/views/chair/bidding-settings/index.blade.php`  
**Controller:** `BiddingSettingsController`

**Các bước:**
1. Truy cập `/chair/bidding-settings`
2. Chọn conference
3. Bật bidding
4. Set start_date, end_date
5. Lưu

**Kết quả mong đợi:**
- ✅ Settings lưu trong `conference_bidding_settings`
- ✅ Reviewers thấy bidding form
- ✅ Tự động đóng sau end_date

---

### TC-CHAIR-013: Phân công Reviewer thủ công
**Route:** `POST /chair/assignments/papers/{id}/assign`  
**Controller:** `AssignmentController::assignReviewers`

**Các bước:**
1. Tại paper detail
2. Click "Phân công reviewer"
3. Chọn reviewers
4. Submit

**Kết quả mong đợi:**
- ✅ Assignment tạo trong `reviewer_assignments`
- ✅ Status: PENDING
- ✅ Email gửi reviewers
- ✅ Reviewers thấy trong dashboard

---

### TC-CHAIR-014: Phân công tự động (Auto-assign)
**Route:** `POST /chair/assignments/conferences/{id}/auto-assign`  
**Controller:** `AssignmentController::autoAssignConference`

**Điều kiện:** Papers đã có bidding

**Các bước:**
1. Truy cập `/chair/assignments`
2. Chọn conference
3. Click "Phân công tự động"
4. Chọn số reviewers/paper (3)
5. Chạy thuật toán

**Kết quả mong đợi:**
- ✅ Thuật toán chạy thành công
- ✅ Mỗi paper có đủ reviewers
- ✅ Tránh COI
- ✅ Cân bằng workload theo bidding
- ✅ Email gửi tất cả reviewers

---

### TC-CHAIR-015: Xóa Assignment
**Route:** `DELETE /chair/assignments/papers/{paperId}/reviewers/{reviewerId}`  
**Controller:** `AssignmentController::unassignReviewer`

**Các bước:**
1. Tại paper assignments
2. Click "Xóa"
3. Confirm

**Kết quả mong đợi:**
- ✅ Assignment bị xóa
- ✅ Email thông báo reviewer

---

### TC-CHAIR-016: Xem Assignment Statistics
**Route:** `GET /chair/assignments/conferences/{id}/stats`  
**Controller:** `AssignmentController::getAssignmentStats`

**Các bước:**
1. Truy cập `/chair/assignments`
2. Chọn conference

**Kết quả mong đợi:**
- ✅ Thống kê:
  - Tổng papers
  - Papers đã phân công đủ
  - Papers chưa đủ reviewers
  - Reviewer workload distribution

---

## 7. CHAIR - ANNOUNCEMENTS (BROADCAST)

### TC-CHAIR-017: Tạo Broadcast Announcement
**Route:** `GET /chair/announcements/create`, `POST /chair/announcements/store`  
**View:** `resources/views/chair/announcements/create.blade.php`  
**Controller:** `AnnouncementController::create`, `AnnouncementController::store`

**Các bước:**
1. Truy cập `/chair/announcements/create`
2. Nhập:
   - Tiêu đề: `Thông báo deadline`
   - Nội dung: `Kính gửi...`
   - Channels: ☑ Email, ☑ In-App
3. Chọn "Gửi ngay" hoặc "Lên lịch"
4. Submit

**Kết quả mong đợi:**
- ✅ Announcement tạo trong `thongbao`
- ✅ `conference_id = NULL` (broadcast)
- ✅ Status: SENT/SCHEDULED
- ✅ Job `SendAnnouncementJob` dispatch
- ✅ Notifications tạo trong `user_notifications`
- ✅ Email gửi tất cả active users

---

### TC-CHAIR-018: Xem danh sách Announcements
**Route:** `GET /chair/announcements`, `GET /chair/announcements/data/list`  
**View:** `resources/views/chair/announcements/index.blade.php`  
**Controller:** `AnnouncementController::index`, `AnnouncementController::getAnnouncementsList`

**Các bước:**
1. Truy cập `/chair/announcements`

**Kết quả mong đợi:**
- ✅ Hiển thị broadcast (conference_id = NULL)
- ✅ Hiển thị announcements của conferences mình là Chair
- ✅ Statistics: Tổng, Đã gửi, Lên lịch, Thất bại
- ✅ Filter theo status
- ✅ Search

---

### TC-CHAIR-019: Xem Statistics
**Route:** `GET /chair/announcements/{id}/statistics`  
**View:** `resources/views/chair/announcements/statistics.blade.php`  
**Controller:** `AnnouncementController::statistics`

**Các bước:**
1. Click vào announcement
2. Xem statistics

**Kết quả mong đợi:**
- ✅ Số người nhận
- ✅ Số đã đọc
- ✅ Delivery status per user

---

## 8. CHAIR - PROCEEDINGS

### TC-CHAIR-020: Upload Proceedings PDF
**Route:** `GET /chair/conferences/{conferenceId}/proceedings-upload`, `POST /chair/conferences/{conferenceId}/proceedings-upload`  
**View:** `resources/views/chair/proceedings/upload.blade.php`  
**Controller:** `ProceedingsController::showUploadForm`, `ProceedingsController::uploadProceedings`

**Điều kiện:** Conference đã kết thúc

**Các bước:**
1. Truy cập `/chair/conferences/{conferenceId}/proceedings-upload`
2. Upload file PDF (< 50MB)
3. Submit

**Kết quả mong đợi:**
- ✅ File lưu vào `storage/proceedings/{conferenceId}/`
- ✅ `proceedings_file_path` update trong `hoithao`
- ✅ `proceedings_published_at` được set
- ✅ Authors có thể download

---

### TC-CHAIR-021: Cấu hình Pagination
**Route:** `POST /chair/proceedings/{conferenceId}/update-pagination`  
**Controller:** `ProceedingsController::updatePagination`

**Các bước:**
1. Truy cập `/chair/proceedings/{conferenceId}`
2. Nhập start_page, end_page cho từng paper
3. Lưu

**Kết quả mong đợi:**
- ✅ `start_page`, `end_page` update trong `baibao`
- ✅ Hiển thị trong proceedings

---

### TC-CHAIR-022: Publish Proceedings
**Route:** `POST /chair/proceedings/{conferenceId}/publish`  
**Controller:** `ProceedingsController::publish`

**Các bước:**
1. Sau upload và configure pagination
2. Click "Publish"

**Kết quả mong đợi:**
- ✅ `proceedings_published_at` được set
- ✅ Authors xem được trên `/author/proceedings`

---

### TC-CHAIR-023: Xem Reminder Logs
**Route:** `GET /chair/reminders`, `GET /chair/reminders/{conferenceId}/logs`  
**View:** `resources/views/chair/reminders/index.blade.php`  
**Controller:** `ConferenceReminderController`

**Các bước:**
1. Truy cập `/chair/reminders`
2. Chọn conference
3. Xem logs

**Kết quả mong đợi:**
- ✅ Hiển thị reminder schedule
- ✅ Logs đã gửi
- ✅ Types: SUBMISSION_REMINDER, REVIEW_REMINDER

---

## 9. AUTHOR - PAPER SUBMISSION

### TC-AUTHOR-001: Nộp bài báo mới
**Route:** `GET /author/papers/create`, `POST /author/papers`  
**View:** `resources/views/author/papers/create.blade.php`  
**Controller:** `AuthorPaperController::create`, `AuthorPaperController::store`

**Điều kiện:** Conference đang mở submission

**Các bước:**
1. Truy cập `/author/papers/create`
2. Chọn conference
3. Chọn track
4. Nhập:
   - Tiêu đề
   - Abstract
   - Keywords
5. Upload file PDF
6. Submit

**Kết quả mong đợi:**
- ✅ Paper tạo trong `baibao`
- ✅ Status: SUBMITTED
- ✅ File lưu vào storage
- ✅ Version tạo trong `paper_versions`
- ✅ Email xác nhận gửi author

---

### TC-AUTHOR-002: Xem danh sách Papers
**Route:** `GET /author/papers`  
**View:** `resources/views/author/papers/index.blade.php`  
**Controller:** `AuthorPaperController::index`

**Các bước:**
1. Truy cập `/author/papers`

**Kết quả mong đợi:**
- ✅ Hiển thị papers mà user là submitter
- ✅ Hiển thị status
- ✅ Nút View, Edit (nếu chưa quá deadline)

---

### TC-AUTHOR-003: Xem chi tiết Paper
**Route:** `GET /author/papers/{id}`  
**View:** `resources/views/author/papers/show.blade.php`  
**Controller:** `AuthorPaperController::show`

**Các bước:**
1. Click vào paper

**Kết quả mong đợi:**
- ✅ Hiển thị thông tin đầy đủ
- ✅ Hiển thị status
- ✅ Nút Download
- ✅ Nếu có reviews → hiển thị (ẩn tên reviewer)

---

### TC-AUTHOR-004: Edit Paper (trước deadline)
**Route:** `GET /author/papers/{id}/edit`, `PUT /author/papers/{id}`  
**View:** `resources/views/author/papers/edit.blade.php`  
**Controller:** `AuthorPaperController::edit`, `AuthorPaperController::update`

**Điều kiện:** Chưa quá submission deadline

**Các bước:**
1. Click "Edit"
2. Sửa title, abstract
3. Upload file mới (optional)
4. Submit

**Kết quả mong đợi:**
- ✅ Thông tin được update
- ✅ Nếu upload file mới → tạo version mới
- ✅ File cũ vẫn được giữ

---

### TC-AUTHOR-005: Withdraw Paper
**Route:** `POST /author/papers/{id}/withdraw`  
**Controller:** `AuthorPaperController::withdraw`

**Các bước:**
1. Tại paper detail
2. Click "Rút bài"
3. Nhập lý do
4. Confirm

**Kết quả mong đợi:**
- ✅ Status → WITHDRAWN
- ✅ Lý do được lưu
- ✅ Email thông báo Chair
- ✅ Reviewers không thấy paper

---

### TC-AUTHOR-006: Download Paper
**Route:** `GET /author/papers/{id}/download`  
**Controller:** `AuthorPaperController::download`

**Các bước:**
1. Click "Download PDF"

**Kết quả mong đợi:**
- ✅ PDF được download
- ✅ Filename đúng

---

### TC-AUTHOR-007: Xem Proceedings List
**Route:** `GET /author/proceedings`  
**View:** `resources/views/author/proceedings/index.blade.php`  
**Controller:** `Author\ProceedingsController::index`

**Các bước:**
1. Truy cập `/author/proceedings`

**Kết quả mong đợi:**
- ✅ Hiển thị conferences đã publish proceedings
- ✅ Chỉ conferences mà user có paper ACCEPTED

---

### TC-AUTHOR-008: Xem Proceedings Detail
**Route:** `GET /author/proceedings/{conference}`  
**View:** `resources/views/author/proceedings/show.blade.php`  
**Controller:** `Author\ProceedingsController::show`

**Các bước:**
1. Click vào conference

**Kết quả mong đợi:**
- ✅ Hiển thị thông tin proceedings
- ✅ Hiển thị papers accepted
- ✅ Hiển thị pagination (start_page - end_page)
- ✅ Nút download từng paper

---

### TC-AUTHOR-009: Download Proceedings Paper
**Route:** `GET /author/proceedings/{conference}/papers/{paper}/download`  
**Controller:** `Author\ProceedingsController::downloadPaper`

**Các bước:**
1. Click "Download" paper

**Kết quả mong đợi:**
- ✅ PDF được download
- ✅ Hoặc download toàn bộ proceedings

---

## 10. REVIEWER - BIDDING & COI

### TC-REVIEWER-001: Accept Reviewer Invitation
**Route:** `GET /reviewer/invitation/{token}`  
**Controller:** `InvitationController::acceptInvitation`

**Điều kiện:** Đã nhận email invitation

**Các bước:**
1. Click link trong email
2. Nếu chưa có account → redirect join form
3. Nếu đã có account → auto accept

**Kết quả mong đợi:**
- ✅ Invitation status → ACCEPTED
- ✅ Role REVIEWER gán trong `vaitronguoidung`
- ✅ Redirect đến `/reviewer/dashboard`

---

### TC-REVIEWER-002: Join Form (chưa có account)
**Route:** `GET /reviewer/join`, `POST /reviewer/join`  
**View:** `resources/views/reviewer/join-form.blade.php`  
**Controller:** `InvitationController::showJoinForm`, `InvitationController::submitJoinForm`

**Các bước:**
1. Từ invitation link
2. Nhập:
   - Full Name
   - Password
   - Confirm Password
   - Organization
3. Submit

**Kết quả mong đợi:**
- ✅ User tạo trong `nguoidung`
- ✅ Email verified tự động
- ✅ Role REVIEWER gán
- ✅ Redirect dashboard

---

### TC-REVIEWER-003: Xem Papers để Bidding
**Route:** `GET /reviewer/bidding`  
**View:** `resources/views/reviewer/bidding.blade.php`  
**Controller:** `BiddingController::index`

**Điều kiện:** Bidding đang mở

**Các bước:**
1. Truy cập `/reviewer/bidding`
2. Chọn conference

**Kết quả mong đợi:**
- ✅ Hiển thị papers của conference
- ✅ Title, Abstract, Keywords
- ✅ KHÔNG hiển thị authors (blind)
- ✅ Nút Bid: YES/MAYBE/NO

---

### TC-REVIEWER-004: Submit Bidding - YES
**Route:** `POST /reviewer/bidding`  
**Controller:** `BiddingController::submitBidding`

**Các bước:**
1. Click "Interested" (YES)
2. Submit

**Kết quả mong đợi:**
- ✅ Bidding lưu trong `reviewer_bidding`
- ✅ bid_value = `YES`
- ✅ Icon hiển thị đã bid
- ✅ Có thể thay đổi sau

---

### TC-REVIEWER-005: Submit Bidding - NO với COI
**Route:** `POST /reviewer/bidding`

**Các bước:**
1. Click "Not Interested" (NO)
2. Chọn reason: "Conflict of Interest"
3. Nhập mô tả COI
4. Submit

**Kết quả mong đợi:**
- ✅ Bidding lưu với NO
- ✅ COI được ghi nhận
- ✅ Chair được notify
- ❌ Không được phân công paper này

---

### TC-REVIEWER-006: Bulk Bidding
**Route:** `POST /reviewer/bidding/bulk`  
**Controller:** `BiddingController::submitBulkBidding`

**Các bước:**
1. Select nhiều papers
2. Chọn bid value
3. Click "Submit All"

**Kết quả mong đợi:**
- ✅ Tất cả biddings được lưu
- ✅ Hiển thị thông báo số lượng

---

### TC-REVIEWER-007: Khai báo COI thủ công
**Route:** `GET /reviewer/coi`, `POST /reviewer/coi`  
**View:** `resources/views/reviewer/coi/index.blade.php`  
**Controller:** `Reviewer\COIController::index`, `COIController::store`

**Các bước:**
1. Truy cập `/reviewer/coi`
2. Click "Khai báo COI"
3. Chọn paper
4. Chọn loại COI
5. Nhập mô tả
6. Submit

**Kết quả mong đợi:**
- ✅ COI lưu trong `xulycoi`
- ✅ Chair nhận notification
- ✅ Không được assign paper đó

---

### TC-REVIEWER-008: Xem Bidding Statistics
**Route:** `GET /reviewer/bidding/statistics/{conferenceId}`  
**Controller:** `BiddingController::getBiddingStatistics`

**Các bước:**
1. Tại bidding page
2. Xem statistics

**Kết quả mong đợi:**
- ✅ Tổng papers
- ✅ Số đã bid
- ✅ Breakdown: YES/MAYBE/NO

---

## 11. REVIEWER - ASSIGNMENTS & REVIEWS

### TC-REVIEWER-009: Xem Assignments
**Route:** `GET /reviewer/assignments`  
**View:** `resources/views/reviewer/assignments/index.blade.php`  
**Controller:** `Reviewer\AssignmentController::index`

**Các bước:**
1. Truy cập `/reviewer/assignments`

**Kết quả mong đợi:**
- ✅ Hiển thị papers được phân công
- ✅ Status: PENDING/ACCEPTED/DECLINED/COMPLETED
- ✅ Hiển thị deadline
- ✅ Nút Accept/Decline (nếu PENDING)

---

### TC-REVIEWER-010: Accept Assignment
**Route:** `POST /reviewer/assignments/{id}/accept`  
**Controller:** `Reviewer\AssignmentController::accept`

**Điều kiện:** Assignment PENDING

**Các bước:**
1. Click "Chấp nhận"
2. Confirm

**Kết quả mong đợi:**
- ✅ Status → ACCEPTED
- ✅ Email gửi Chair
- ✅ Có thể bắt đầu review

---

### TC-REVIEWER-011: Decline Assignment
**Route:** `POST /reviewer/assignments/{id}/decline`  
**Controller:** `Reviewer\AssignmentController::decline`

**Các bước:**
1. Click "Từ chối"
2. Nhập lý do
3. Submit

**Kết quả mong đợi:**
- ✅ Status → DECLINED
- ✅ Lý do được lưu
- ✅ Email gửi Chair

---

### TC-REVIEWER-012: View Assignment Detail
**Route:** `GET /reviewer/assignments/{id}`  
**View:** `resources/views/reviewer/assignments/show.blade.php`  
**Controller:** `Reviewer\AssignmentController::show`

**Các bước:**
1. Click vào assignment

**Kết quả mong đợi:**
- ✅ Hiển thị paper info
- ✅ Nút Download PDF
- ✅ Nút Write Review (nếu ACCEPTED)

---

### TC-REVIEWER-013: Download Paper
**Route:** `GET /reviewer/papers/{paperId}/download`  
**Controller:** `ReviewerController::downloadPaper`

**Các bước:**
1. Tại assignment detail
2. Click "Download PDF"

**Kết quả mong đợi:**
- ✅ PDF được download
- ✅ Blind review (không thấy tên authors)

---

### TC-REVIEWER-014: Write Review - Create
**Route:** `GET /reviewer/reviews/create/{assignmentId}`, `POST /reviewer/reviews/{assignmentId}/store`  
**View:** `resources/views/reviewer/reviews/create.blade.php`  
**Controller:** `ReviewerController::createReview`, `ReviewerController::storeReview`

**Điều kiện:** Assignment ACCEPTED

**Các bước:**
1. Click "Write Review"
2. Nhập:
   - Overall Score: 7/10
   - Originality: 8/10
   - Technical Quality: 7/10
   - Clarity: 6/10
   - Comments for Authors
   - Comments for Chair
   - Recommendation: ACCEPT/REJECT
3. Click "Save Draft" hoặc "Submit Review"

**Kết quả mong đợi:**
- ✅ Review lưu trong `phanbien`
- ✅ Nếu Save Draft: `is_draft = true`
- ✅ Nếu Submit: `is_draft = false`

---

### TC-REVIEWER-015: Edit Review (Draft)
**Route:** `GET /reviewer/reviews/{id}/edit`, `PUT /reviewer/reviews/{id}`  
**View:** `resources/views/reviewer/reviews/edit.blade.php`  
**Controller:** `ReviewerController::editReview`, `ReviewerController::updateReview`

**Điều kiện:** Review là draft

**Các bước:**
1. Click "Edit"
2. Sửa scores, comments
3. Update

**Kết quả mong đợi:**
- ✅ Review được update
- ✅ Vẫn là draft nếu chọn Save

---

### TC-REVIEWER-016: Submit Review Final
**Route:** `PUT /reviewer/reviews/{id}` (với action = submit)

**Các bước:**
1. Từ draft review
2. Click "Submit Final Review"
3. Confirm

**Kết quả mong đợi:**
- ✅ `is_draft = false`
- ✅ `submitted_at` được set
- ✅ Assignment status → COMPLETED
- ✅ Email gửi Chair
- ✅ Chair thấy review
- ❌ Không thể edit nữa

---

### TC-REVIEWER-017: Xem lịch sử Reviews
**Route:** `GET /reviewer/reviews`  
**View:** `resources/views/reviewer/reviews/index.blade.php`  
**Controller:** `ReviewerController::reviews`

**Các bước:**
1. Truy cập `/reviewer/reviews`

**Kết quả mong đợi:**
- ✅ Hiển thị tất cả reviews đã làm
- ✅ Filter theo conference
- ✅ Filter theo status

---

## 12. MOBILE API

### TC-API-001: Login API
**Route:** `POST /api/auth/login`  
**Controller:** `Api\AuthController::login`

**Request:**
```json
{
  "email": "test@example.com",
  "password": "123456789"
}
```

**Kết quả mong đợi:**
- ✅ HTTP 200 OK
- ✅ Response:
```json
{
  "access_token": "...",
  "token_type": "bearer",
  "expires_in": 3600
}
```

---

### TC-API-002: Get Proceedings List
**Route:** `GET /api/proceedings`  
**Headers:** `Authorization: Bearer {token}`  
**Controller:** `Api\ProceedingsController::index`

**Kết quả mong đợi:**
- ✅ HTTP 200 OK
- ✅ Response:
```json
{
  "success": true,
  "data": [
    {
      "conference_id": 26,
      "title": "...",
      "has_proceedings": true,
      "paper_count": 2
    }
  ]
}
```

---

### TC-API-003: Get Proceedings Detail
**Route:** `GET /api/proceedings/{id}`  
**Headers:** `Authorization: Bearer {token}`  
**Controller:** `Api\ProceedingsController::show`

**Kết quả mong đợi:**
- ✅ HTTP 200 OK
- ✅ Response có `proceedings_url`, `file_size`

---

### TC-API-004: Download Proceedings PDF
**Route:** `GET /api/proceedings/{id}/download`  
**Headers:** `Authorization: Bearer {token}`  
**Controller:** `Api\ProceedingsController::download`

**Kết quả mong đợi:**
- ✅ HTTP 200 OK
- ✅ Content-Type: application/pdf
- ✅ File download thành công

---

### TC-API-005: Unauthorized Access
**Route:** `GET /api/proceedings`  
**Headers:** Không có Authorization

**Kết quả mong đợi:**
- ❌ HTTP 401 Unauthorized
- ⚠️ Message: "Unauthenticated."

---

### TC-API-006: Forbidden - Không phải AUTHOR
**Route:** `GET /api/proceedings/{id}`  
**Headers:** Token của user không có role AUTHOR

**Kết quả mong đợi:**
- ❌ HTTP 403 Forbidden
- ⚠️ Message: "Bạn không có quyền xem kỷ yếu của hội thảo này"

---

## 📊 TEST EXECUTION TRACKING

**Test Execution Date:** __/__/____  
**Tester:** _______________  
**Environment:** ☐ Development ☐ Staging ☐ Production

### Summary

| Module | Total | Passed | Failed | Blocked | Pass Rate |
|--------|-------|--------|--------|---------|-----------|
| Authentication | 9 | | | | |
| Conference Request | 2 | | | | |
| Admin | 9 | | | | |
| Chair - Setup | 4 | | | | |
| Chair - Papers | 5 | | | | |
| Chair - Reviewers | 7 | | | | |
| Chair - Announcements | 3 | | | | |
| Chair - Proceedings | 4 | | | | |
| Author | 9 | | | | |
| Reviewer - Bidding | 8 | | | | |
| Reviewer - Reviews | 9 | | | | |
| Mobile API | 6 | | | | |
| **TOTAL** | **75** | | | | |

---

## 🐛 BUG REPORT TEMPLATE

**Bug ID:** BUG-XXX  
**Severity:** ☐ Critical ☐ High ☐ Medium ☐ Low  
**Priority:** ☐ P0 ☐ P1 ☐ P2 ☐ P3  
**Status:** ☐ Open ☐ In Progress ☐ Resolved ☐ Closed

**Title:** _____________________

**Test Case:** TC-XXX-XXX

**Description:** _____________________

**Steps to Reproduce:**
1. _____________________
2. _____________________
3. _____________________

**Expected Result:** _____________________

**Actual Result:** _____________________

**Screenshots/Logs:** _____________________

**Environment:**
- Browser: _____________________
- OS: _____________________
- Laravel Version: _____________________

**Assigned To:** _____________________  
**Fixed In Version:** _____________________

---

## 📝 NOTES

### Test Data Requirements
- Cần ít nhất 1 user mỗi role: ADMIN, CHAIR, AUTHOR, REVIEWER
- Cần ít nhất 1 conference ở mỗi status: PENDING, APPROVED, ACTIVE
- Cần ít nhất 5 papers với status khác nhau
- Cần ít nhất 3 reviewers đã accept invitation

### Pre-test Setup
1. Reset database về clean state
2. Run seeders nếu có
3. Clear cache: `php artisan cache:clear`
4. Clear views: `php artisan view:clear`
5. Clear config: `php artisan config:clear`

### Post-test Cleanup
1. Document tất cả bugs tìm được
2. Export test results
3. Update test cases nếu có thay đổi requirements

### Important Routes Verified
Tất cả test cases dựa trên routes thực tế trong `routes/web.php`:
- Authentication: ✅ Verified
- Conference Request: ✅ Verified (2-step approval process)
- Admin: ✅ Verified (conference-requests, configured-conferences, users, logs)
- Chair: ✅ Verified (setup, papers, reviewers, bidding-settings, announcements, proceedings, reminders)
- Author: ✅ Verified (papers CRUD, proceedings view)
- Reviewer: ✅ Verified (invitation/{token}, bidding, coi, assignments, reviews)
- API: ✅ Verified (auth/login, proceedings endpoints)

---

**Prepared by:** QA Team  
**Last Updated:** 28/11/2025  
**Version:** 2.0 (Based on actual routes & views)  
**Status:** ✅ 100% dựa trên hệ thống thực tế
