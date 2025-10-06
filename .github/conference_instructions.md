# Instructions – Conference Management System

## 1. Đăng ký tài khoản (Signup)
Mục tiêu
- Tạo tài khoản mới (Author/Reviewer mặc định).
- Đảm bảo email duy nhất, mật khẩu băm (hash).
- Chair gửi mail mời ->  reviewer điền profile -> chair duyệt -> cấp quyền

## 2. Đăng nhập (Login)
Mục tiêu
- Cho phép người dùng đăng nhập bằng email + mật khẩu.
- Sinh JWT/Session Token.

## 3. Yêu cầu quyền Chair + upload PDF đề xuất hội thảo
Mục tiêu
- Cho phép Author gửi đề xuất hội thảo (form + PDF).
- Chờ duyệt để trở thành Chair và tạo Hội Thảo.

Luồng xử lý
1. Author nhập metadata + upload PDF.
2. Lưu yêu cầu (status = PENDING).
3. Admin/PC duyệt → APPROVE/REJECT.
   - Nếu APPROVE → Tạo Conference, gán role CHAIR.
   - Nếu REJECT → cập nhật trạng thái + ghi chú.

## 4. Bidding (Reviewer tự chọn mức độ phù hợp)
Mục tiêu
- Reviewer chọn WANT / CAN / NO / CONFLICT cho từng bài.

Ràng buộc
- Lưu đầy đủ dữ liệu bidding để phục vụ phân công.

## 5. Chạy phân công tạm thời (Invitation)
Mục tiêu
- Gán k reviewer/bài dựa trên score tổng hợp.

Luồng xử lý
- Input: conf_id, k, max_load, due_days.
- Lấy danh sách bài → loại bỏ COI, quá tải.
- Tính điểm: 3*bid + 2*exp - load (+bonus).
- Chọn top k reviewer.
- Sinh assignment status = INVITED (hạn = due_days).
- Gửi email mời.

Ràng buộc & quy tắc
- Không gán reviewer có COI.
- Tôn trọng max_load, unique assignment.

## 6. Reviewer phản hồi lời mời
Mục tiêu
- Cho reviewer Accept / Decline / Báo COI.

Luồng xử lý
- Accept → status = ACCEPTED.
- Decline → status = DECLINED.
- COI → lưu COI pending.
- Quá hạn → INVITED → DECLINED (tự động).

## 7. Xử lý COI (Chair duyệt/bác)
Mục tiêu
- Chair xác nhận hoặc bác bỏ COI.

Luồng xử lý
- Nếu CONFIRMED → assignment CANCELLED, mời reviewer khác.
- Nếu REJECTED → giữ nguyên assignment.

## 8. Nộp bài báo (Submission)
Mục tiêu
- Author nộp metadata + PDF.

Luồng xử lý
1. Upload PDF.
2. Lưu BaiBao (status = SUBMITTED).
3. Thêm danh sách tác giả (1 contact author).

Ràng buộc
- PDF hợp lệ, trong hạn submission.
- Contact author duy nhất.

## 9. Chỉnh sửa theo góp ý (Revision)
Mục tiêu
- Tác giả nộp bản sửa sau khi reviewer yêu cầu.

Luồng xử lý
- Upload bản sửa → tạo version mới.
- Cập nhật trạng thái.

Ràng buộc
- Chỉ khi status = REVISION_REQUIRED.
- Version duy nhất theo paper.

## 10. Thực hiện phản biện (Submit Review)
Mục tiêu
- Reviewer nộp nhận xét, điểm, khuyến nghị.

Ràng buộc
- Mỗi assignment tối đa 1 review cuối.
- Lưu timestamp khi submit.

## 11. Rút bài (Withdraw)
Mục tiêu
- Author xin rút bài.
- Chair duyệt REJECT/APPROVE.

## 12. Đăng thông báo (Announcements)
Mục tiêu
- Chair/Admin gửi thông báo trong hệ thống.

## 13. Xuất bản kỷ yếu (Proceedings)
Mục tiêu
- Sinh file kỷ yếu từ các bài ACCEPTED.

Luồng xử lý
- Lọc bài accepted.
- Lấy metadata → sinh PDF kỷ yếu.
- Lưu file + công bố.

## 14. Nhắc lịch tự động (Scheduler)
Mục tiêu
- Nhắc deadline submission / review / camera-ready.

## 15. Quản trị người dùng & vai trò
Mục tiêu
- Quản lý account, role, khóa/mở user.
