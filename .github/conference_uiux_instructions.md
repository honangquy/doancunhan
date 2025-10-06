# Instructions – UI/UX Requirements (Conference Management System)

## 🎯 Mục tiêu
- Giao diện website quản lý hội thảo phải **trực quan, hiện đại và dễ sử dụng**.
- Đảm bảo trải nghiệm mượt mà trên **desktop, tablet, mobile**.
- Hỗ trợ từng vai trò (Author, Reviewer, Chair, Admin) thao tác nhanh chóng, hạn chế lỗi.

## 🖼️ Nguyên tắc thiết kế UI
1. **Tính nhất quán (Consistency)**
   - Màu sắc chủ đạo: xanh dương đậm (background/navigation) + cam (CTA).
   - Không sử dụng gradient
   - Font chữ thống nhất Inter.
   - Button có 3 loại: Primary (CTA), Secondary (phụ), Danger (xóa, hủy). Các button phải nhỏ gọn, không được lớn
   - Các thông báo hiện lên (ví dụ: thành công, thất bại phải đẹp, sử dụng animation mượt mà)

2. **Đơn giản & Trực quan**
   - Thanh menu rõ ràng
   - Có icon svg + tooltip cho các nút quan trọng.

3. **Khả năng truy cập (Accessibility)**
   - Đảm bảo contrast cao (WCAG).
   - Font nhỏ 14px, có thể zoom.
   - Hỗ trợ screen reader và điều hướng bằng bàn phím.

4. **Phản hồi tức thì (Feedback)**
   - Submit form → loading spinner.
   - Lỗi nhập liệu → hiển thị ngay dưới field.
   - Thành công → banner xanh hoặc toast notification.

## 📱 Nguyên tắc UX
1. **Website-first**
   - Responsive layout.
   - Menu thu gọn (hamburger menu) trên mobile.

2. **User Flow rõ ràng**
   - Signup/Login → Nộp bài → Bidding → Review → Proceedings.
   - Chỉ hiển thị chức năng phù hợp với vai trò.

3. **Tối ưu cho Reviewer**
   - Danh sách bài: có filter (theo chủ đề, deadline, trạng thái).
   - Nút Bidding (WANT/CAN/NO/COI) hiển thị trực tiếp trên mỗi bài.

4. **Tối ưu cho Author**
   - Form nộp bài: metadata + PDF + danh sách tác giả.
   - Tracking trạng thái bài: Submitted → Under Review → Revision → Accepted/Rejected.

5. **Tối ưu cho Chair/Admin**
   - Dashboard: hiển thị số bài, số reviewer, COI pending, deadline gần.
   - Bảng phân công review trực quan (matrix paper vs reviewer).


