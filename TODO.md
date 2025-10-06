# TODO - HỆ THỐNG QUẢN LÝ HỘI THẢO HUIT

## 🎯 GIAI ĐOẠN 1: SETUP & CƠ SỞ HẠ TẦNG ✅

### Database
- [x] Thiết kế schema database (database.md)
- [x] Tạo migration files cho tất cả các bảng
- [x] Tạo seeder cho lookup tables
- [x] Tạo seeder dữ liệu mẫu

### Laravel Setup
- [x] Cấu hình .env
- [x] Tạo Models với relationships
- [x] Cài đặt JWT authentication
- [ ] Cấu hình mail service
- [ ] Cấu hình file storage

---

## 🎯 GIAI ĐOẠN 2: AUTHENTICATION & USER MANAGEMENT ✅

### Module Auth
- [x] Model: User (NguoiDung)
- [x] Model: UserRole (VaiTroNguoiDung)
- [x] Controller: AuthController
  - [x] POST /api/auth/register - Đăng ký tài khoản
  - [x] POST /api/auth/login - Đăng nhập
  - [x] POST /api/auth/logout - Đăng xuất
  - [x] GET /api/auth/profile - Lấy thông tin profile
  - [x] PUT /api/auth/profile - Cập nhật profile
  - [x] POST /api/auth/change-password - Đổi mật khẩu
  - [x] POST /api/auth/refresh - Refresh token

### Module User Management (Admin)
- [ ] Controller: UserController
  - [ ] GET /api/admin/users - Danh sách users
  - [ ] GET /api/admin/users/{id} - Chi tiết user
  - [ ] PUT /api/admin/users/{id}/lock - Khóa/mở user
  - [ ] POST /api/admin/users/{id}/roles - Gán vai trò
  - [ ] DELETE /api/admin/users/{id}/roles/{roleId} - Xóa vai trò

### Middleware
- [x] JWT Authentication Middleware
- [ ] Role-based Authorization Middleware
- [ ] Check Conference Access Middleware

---

## 🎯 GIAI ĐOẠN 3: QUẢN LÝ HỘI THẢO ✅

### Module Conference Request
- [x] Model: ConferenceRequest (YeuCauHoiThao)
- [x] Controller: ConferenceRequestController
  - [x] POST /api/conference-requests - Gửi yêu cầu tạo hội thảo (Chair)
  - [x] GET /api/conference-requests - Danh sách yêu cầu
  - [x] GET /api/conference-requests/{id} - Chi tiết yêu cầu
  - [x] POST /api/conference-requests/{id}/approve - Duyệt yêu cầu (Admin)
  - [x] POST /api/conference-requests/{id}/reject - Từ chối yêu cầu (Admin)
  - [x] POST /api/conference-requests/{id}/cancel - Hủy yêu cầu (Requester)
  - [x] GET /api/conference-requests/statistics - Thống kê yêu cầu (Admin)

### Module Conference
- [x] Model: Conference (HoiThao)
- [x] Model: Track (TieuBan)
- [x] Controller: ConferenceController
  - [x] GET /api/conferences - Danh sách hội thảo công khai
  - [x] GET /api/conferences/{id} - Chi tiết hội thảo
  - [x] POST /api/conferences - Tạo hội thảo (Admin/Chair)
  - [x] PUT /api/conferences/{id} - Cập nhật hội thảo
  - [x] DELETE /api/conferences/{id} - Xóa hội thảo
  - [x] GET /api/conferences/{id}/statistics - Thống kê hội thảo
  - [x] GET /api/my-conferences - Danh sách hội thảo của user

### Module Track
- [x] Controller: TrackController
  - [x] GET /api/conferences/{conference_id}/tracks - Danh sách tiểu ban
  - [x] POST /api/conferences/{conference_id}/tracks - Tạo tiểu ban
  - [x] GET /api/tracks/{id} - Chi tiết tiểu ban
  - [x] PUT /api/tracks/{id} - Cập nhật tiểu ban (gồm cả gán chair)
  - [x] DELETE /api/tracks/{id} - Xóa tiểu ban
  - [x] GET /api/tracks/{id}/papers - Danh sách bài báo của tiểu ban
  - [x] GET /api/my-tracks - Danh sách tiểu ban do user quản lý

---

## 🎯 GIAI ĐOẠN 4: QUẢN LÝ BÀI BÁO ✅

### Module Paper Submission
- [x] Model: Paper (BaiBao)
- [x] Model: PaperVersion (PhienBanBaiBao)
- [x] Model: PaperAuthor (TacGiaBaiBao)
- [x] Controller: PaperController
  - [x] GET /api/papers - Danh sách bài báo (với filters)
  - [x] POST /api/papers - Nộp bài mới (multi-author + file upload)
  - [x] GET /api/papers/{id} - Chi tiết bài
  - [x] PUT /api/papers/{id} - Cập nhật metadata bài
  - [x] DELETE /api/papers/{id} - Rút bài (withdraw)
  - [x] GET /api/my-papers - Danh sách bài của tôi
  - [x] GET /api/papers/statistics - Thống kê bài báo
  - [x] GET /api/papers/{id}/download - Tải file bài báo

### Module Paper Version
- [x] Controller: PaperVersionController
  - [x] GET /api/papers/{paper_id}/versions - Danh sách phiên bản
  - [x] POST /api/papers/{paper_id}/versions - Upload phiên bản mới
  - [x] GET /api/papers/{paper_id}/versions/{version_no} - Chi tiết phiên bản
  - [x] GET /api/papers/{paper_id}/versions/{version_no}/download - Tải phiên bản
  - [x] GET /api/papers/{paper_id}/versions/compare - So sánh phiên bản

### Module Paper Revision
- [ ] Model: RevisionRequest (YeuCauChinhSua)
- [ ] Controller: RevisionController
  - [ ] POST /api/papers/{id}/revision-request - Yêu cầu chỉnh sửa (Chair)
  - [ ] POST /api/papers/{id}/revisions - Nộp bản sửa (Author)
  - [ ] GET /api/papers/{id}/revision-requests - Danh sách yêu cầu sửa

### Module Paper Withdrawal
- [ ] Model: Withdrawal (RutBaiBao)
- [ ] Controller: WithdrawalController
  - [ ] POST /api/papers/{id}/withdraw - Yêu cầu rút bài
  - [ ] GET /api/withdrawals - Danh sách yêu cầu rút bài
  - [ ] PUT /api/withdrawals/{id}/approve - Duyệt rút bài (Chair)
  - [ ] PUT /api/withdrawals/{id}/reject - Từ chối rút bài

---

## 🎯 GIAI ĐOẠN 5: PHẢN BIỆN - PHẦN 1 (BIDDING & COI)

### Module Reviewer Expertise
- [ ] Model: ReviewerExpertise (ChuyenMonReviewer)
- [ ] Controller: ExpertiseController
  - [ ] POST /api/reviewers/expertise - Khai báo chuyên môn
  - [ ] GET /api/reviewers/expertise - Lấy chuyên môn của reviewer
  - [ ] PUT /api/reviewers/expertise/{id} - Cập nhật chuyên môn

### Module Bidding
- [ ] Model: Bidding
- [ ] Controller: BiddingController
  - [ ] GET /api/conferences/{id}/papers/bidding - Danh sách bài để bid
  - [ ] POST /api/papers/{id}/bidding - Submit bidding
  - [ ] GET /api/conferences/{id}/my-bidding - Danh sách bidding của reviewer
  - [ ] PUT /api/bidding/{id} - Cập nhật bidding

### Module COI (Conflict of Interest)
- [ ] Model: COI
- [ ] Model: COIDecision (XuLyCOI)
- [ ] Controller: COIController
  - [ ] POST /api/papers/{id}/coi - Khai báo COI
  - [ ] GET /api/conferences/{id}/coi - Danh sách COI (Chair)
  - [ ] PUT /api/coi/{id}/decision - Xử lý COI (Chair)
  - [ ] GET /api/coi/pending - COI chờ xử lý

---

## 🎯 GIAI ĐOẠN 6: PHẢN BIỆN - PHẦN 2 (ASSIGNMENT & REVIEW)

### Module Auto Assignment
- [ ] Service: AssignmentService (thuật toán phân công)
- [ ] Controller: AssignmentController
  - [ ] POST /api/conferences/{id}/auto-assign - Chạy phân công tự động
  - [ ] GET /api/conferences/{id}/assignments - Danh sách phân công
  - [ ] POST /api/papers/{id}/assign - Phân công thủ công
  - [ ] DELETE /api/assignments/{id} - Hủy phân công
  - [ ] POST /api/assignments/{id}/replace - Thay thế reviewer

### Module Assignment Response
- [ ] Model: Assignment (PhanCongPhanBien)
- [ ] Controller: AssignmentResponseController
  - [ ] GET /api/assignments/token/{token} - Xem chi tiết lời mời
  - [ ] POST /api/assignments/{token}/accept - Chấp nhận lời mời
  - [ ] POST /api/assignments/{token}/decline - Từ chối lời mời
  - [ ] POST /api/assignments/{token}/report-coi - Báo COI

### Module Review
- [ ] Model: Review (PhanBien)
- [ ] Controller: ReviewController
  - [ ] GET /api/my-reviews - Danh sách bài được gán
  - [ ] GET /api/assignments/{id} - Chi tiết assignment
  - [ ] POST /api/assignments/{id}/review - Nộp review
  - [ ] PUT /api/reviews/{id} - Cập nhật review (nếu chưa quá hạn)
  - [ ] GET /api/papers/{id}/reviews - Xem reviews của bài (Chair)

---

## 🎯 GIAI ĐOẠN 7: QUYẾT ĐỊNH & CÔNG BỐ

### Module Decision
- [ ] Controller: DecisionController
  - [ ] GET /api/papers/{id}/review-summary - Tổng hợp reviews
  - [ ] PUT /api/papers/{id}/decision - Quyết định cuối cùng (Chair)
  - [ ] POST /api/papers/{id}/notify-authors - Thông báo tác giả

### Module Announcement
- [ ] Model: Announcement (ThongBao)
- [ ] Controller: AnnouncementController
  - [ ] POST /api/conferences/{id}/announcements - Tạo thông báo
  - [ ] GET /api/conferences/{id}/announcements - Danh sách thông báo
  - [ ] PUT /api/announcements/{id} - Cập nhật thông báo
  - [ ] DELETE /api/announcements/{id} - Xóa thông báo
  - [ ] GET /api/my-announcements - Thông báo của user

### Module Proceedings
- [ ] Service: ProceedingsService
- [ ] Controller: ProceedingsController
  - [ ] POST /api/conferences/{id}/proceedings/generate - Tạo kỷ yếu
  - [ ] GET /api/conferences/{id}/proceedings - Tải kỷ yếu

---

## 🎯 GIAI ĐOẠN 8: TỰ ĐỘNG HÓA & SCHEDULER

### Jobs & Schedulers
- [ ] Job: SendDeadlineReminderJob
- [ ] Job: AutoDeclineExpiredInvitationsJob
- [ ] Job: SendBulkEmailJob
- [ ] Command: CheckDeadlinesCommand
- [ ] Command: UpdatePaperStatusCommand

### Notifications
- [ ] Notification: WelcomeNotification
- [ ] Notification: ConferenceRequestApprovedNotification
- [ ] Notification: PaperSubmittedNotification
- [ ] Notification: AssignmentInvitationNotification
- [ ] Notification: ReviewSubmittedNotification
- [ ] Notification: DecisionNotification
- [ ] Notification: DeadlineReminderNotification

---

## 🎯 GIAI ĐOẠN 9: FRONTEND

### Layout & Components
- [ ] Header với menu (Hội thảo, Tin tức, Hỗ trợ, Ký yếu)
- [ ] Hero section với search
- [ ] Statistics cards (8 hội thảo, 326 bài báo, 142 reviewers, 987 tác giả)
- [ ] Conference list với filters
- [ ] Footer với thông tin liên hệ

### Pages - Public
- [ ] Landing Page (như thiết kế)
- [ ] Conference List Page
- [ ] Conference Detail Page
- [ ] Login Page
- [ ] Register Page
- [ ] About Page
- [ ] Contact Page

### Pages - Author
- [ ] Author Dashboard
- [ ] Submit Paper Page
- [ ] My Papers Page
- [ ] Paper Detail Page
- [ ] Submit Revision Page

### Pages - Reviewer
- [ ] Reviewer Dashboard
- [ ] Bidding Page
- [ ] My Reviews Page
- [ ] Submit Review Page
- [ ] Expertise Management Page

### Pages - Chair
- [ ] Chair Dashboard
- [ ] Conference Management
- [ ] Track Management
- [ ] Paper Management
- [ ] Assignment Management
- [ ] Review Management
- [ ] Decision Management
- [ ] Announcements Management

### Pages - Admin
- [ ] Admin Dashboard
- [ ] User Management
- [ ] Conference Requests
- [ ] System Settings
- [ ] Role Management

---

## 🎯 GIAI ĐOẠN 10: TESTING & DEPLOYMENT

### Testing
- [ ] Unit Tests cho Models
- [ ] Feature Tests cho APIs
- [ ] Integration Tests
- [ ] Browser Tests (Dusk)

### Deployment
- [ ] Setup production environment
- [ ] Configure CI/CD
- [ ] Deploy to server
- [ ] Setup SSL
- [ ] Configure backup system

---

## 📊 PROGRESS TRACKING

**Tổng số task:** ~150+
**Hoàn thành:** 1
**Đang thực hiện:** 0
**Chưa bắt đầu:** 149+

**Ước tính thời gian:** 3-4 tháng (1 người)

---

## 🚀 PRIORITY ORDER

1. ⭐⭐⭐⭐⭐ Database + Models + Auth
2. ⭐⭐⭐⭐⭐ Paper Submission
3. ⭐⭐⭐⭐⭐ Assignment & Review
4. ⭐⭐⭐⭐ Conference Management
5. ⭐⭐⭐⭐ Frontend Landing Page
6. ⭐⭐⭐ Bidding & COI
7. ⭐⭐⭐ Decision & Announcements
8. ⭐⭐ Automation & Scheduler
9. ⭐⭐ Proceedings
10. ⭐ Testing & Polish
