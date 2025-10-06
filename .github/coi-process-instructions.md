# Instructions – Quy trình xử lý COI (Conflict of Interest)

## 1. Mục tiêu
Đảm bảo tính công bằng, minh bạch trong quá trình phản biện, tránh việc reviewer có lợi ích hoặc mối quan hệ cá nhân với tác giả/đề tài được phân công.Bạn có thể điều chỉnh nếu thấy sai hoặc chưa hợp lý(nêu rõ sai ở đâu, mục đích chỉnh sửa)

---

## 2. Các tác nhân liên quan
- **Reviewer**: Khai báo COI thủ công khi phát hiện xung đột.
- **Hệ thống**: Phát hiện COI tự động dựa trên dữ liệu (cơ quan, GVHD, đồng tác giả gần đây).
- **Chair**: Kiểm tra, xác minh và đưa ra quyết định cuối cùng về COI.

---

## 3. Luồng xử lý COI

### Bước 1. Phát hiện COI
- **Reviewer khai báo thủ công**
  - Khi được phân công phản biện hoặc xem danh sách bài báo trong hệ thống.
  - Reviewer có thể chọn “COI” → khai báo loại COI (cùng GVHD, cùng cơ quan, đồng tác giả…).
  - Có thể kèm ghi chú hoặc file minh chứng.

- **Hệ thống phát hiện tự động**
  - So khớp `NguoiDung.faculty_id` với `TacGiaBaiBao.organization` → cùng cơ quan.
  - Kiểm tra quan hệ đồng tác giả trong các bài báo trước.
  - Kiểm tra quan hệ GVHD.
  - Nếu phát hiện → sinh bản ghi COI (`source_type = DETECTED`).

---

### Bước 2. Xác minh COI
- **Chair** đăng nhập → vào màn hình quản lý COI.
- Xem danh sách COI:
  - Khai báo bởi reviewer (`source_type = DECLARED`).
  - Phát hiện bởi hệ thống (`source_type = DETECTED`).
- Chair xử lý:
  - Nếu COI rõ ràng (GVHD, đồng tác giả gần đây) → **CONFIRMED**.
  - Nếu mơ hồ (cùng trường nhưng khác khoa) → **REJECTED**.

---

### Bước 3. Quyết định COI
- Khi Chair quyết định:
  - **CONFIRMED** → reviewer bị loại khỏi phân công (assignment.status = CANCELLED nếu đã gán).
  - **REJECTED** → COI không ảnh hưởng đến thuật toán gán.
- Lưu bản ghi vào bảng `XuLyCOI` gồm: `decision`, `chair_id`, `note`, `decided_at`.

---

### Bước 4. Ảnh hưởng đến phân công reviewer
- Sau khi xử lý COI, thuật toán gán reviewer (`sp_assign_reviewers_for_paper`) chỉ chọn reviewer hợp lệ.
- Nếu nhiều reviewer bị loại → Chair cần mời thêm reviewer hoặc phân công thủ công.

---

## 4. Dữ liệu liên quan
- **COI**: lưu thông tin COI (declared/detected).
- **XuLyCOI**: lưu quyết định cuối cùng của Chair.
- **PhanCongPhanBien**: cập nhật trạng thái “CANCELLED” khi reviewer bị loại.

---

## 5. Ràng buộc & Quy tắc
1. Reviewer phải xác nhận COI trong vòng 2–3 ngày kể từ khi được gán tạm thời.
2. Nếu reviewer không phản hồi → mặc định coi là không có COI.
3. Chair có quyền quyết định cuối cùng, reviewer không thể hủy COI đã bị bác bỏ.
4. Một bài báo chỉ được gán chính thức sau khi tất cả COI đã được xử lý.

---
