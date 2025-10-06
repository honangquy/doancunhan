# Instructions – Chức năng Bidding và Gán Reviewer

## 1. Mục tiêu
- Bạn có thể điều chỉnh nếu thấy sai hoặc không phù hợp (nếu thay đổi hãy ghi ra lý do và mục đích)
- Cho phép Reviewer bày tỏ mức độ mong muốn/khả năng phản biện các bài báo (Bidding).
- Hỗ trợ Chair/hệ thống phân công reviewer phù hợp dựa trên Bidding + COI + tải công việc.
- Cho phép Reviewer xác nhận hoặc từ chối khi được mời phản biện.
- Quản lý và xử lý xung đột lợi ích (COI).
- Tự động theo dõi hạn phản hồi, chuyển trạng thái nếu quá hạn.

---

## 2. Các tác nhân & hành vi

### Reviewer
- Đăng nhập hệ thống, xem danh sách bài báo (tiêu đề, abstract, từ khóa).
- Thực hiện Bidding với 4 mức:
  - WANT = rất muốn phản biện
  - CAN = có khả năng phản biện
  - NO = không muốn phản biện
  - CONFLICT = xung đột lợi ích
- Nhận lời mời phản biện (INVITED).
- Chọn Accept / Decline hoặc khai báo COI mới.
- Nhận thông báo qua email/hệ thống khi có thay đổi.

### Chair
- Chạy phân công tự động (dựa trên Bidding + COI + tải công việc).
- Chạy phân công thủ công (chọn Reviewer cụ thể).
- Xem danh sách COI do Reviewer khai báo hoặc hệ thống phát hiện.
- Xác nhận hoặc bác bỏ COI.
- Thay thế Reviewer khi cần (Reassign).

### Hệ thống (Admin)
- Lưu trữ dữ liệu Bidding.
- Tạo bản ghi phân công phản biện ở trạng thái INVITED.
- Quản lý vai trò (Chair, Reviewer).
- Theo dõi hạn phản hồi (2–3 ngày).
- Gửi thông báo nhắc nhở deadline.
- Tự động chuyển INVITED → DECLINED nếu quá hạn.

---

## 3. Trạng thái & vòng đời phân công
- **INVITED**: Reviewer được mời phản biện.
- **ACCEPTED / ASSIGNED**: Reviewer đồng ý, phân công chính thức.
- **DECLINED**: Reviewer từ chối.
- **REASSIGNED**: Reviewer khác được phân công thay thế.
- **CONFLICT**: Reviewer có COI, không được mời.

---

## 4. Ràng buộc & Quy tắc
- Một bài báo cần ít nhất 2–3 reviewer.
- Một reviewer không được nhận quá số lượng tối đa (ví dụ: 5 bài).
- COI được ưu tiên cao nhất: hệ thống không gán Reviewer có COI.
- Reviewer phải phản hồi trong 2–3 ngày, nếu không hệ thống tự động DECLINED.

---

## 5. Yêu cầu UI/UX
- Giao diện Bidding: danh sách bài báo + dropdown chọn mức độ (WANT/CAN/NO/CONFLICT).
- Giao diện Reviewer: danh sách lời mời + nút Accept/Decline + báo cáo COI.
- Giao diện Chair: bảng điều khiển hiển thị trạng thái bài báo, Reviewer, COI, phân công.
- Thông báo hiển thị nổi bật và có email nhắc nhở.

---

