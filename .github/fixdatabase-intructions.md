# 🧠 Copilot Instructions — Tránh lỗi Database Schema (Toàn diện)

## 🎯 Mục đích  
Tài liệu này là **bộ hướng dẫn chi tiết** dành cho GitHub Copilot, ChatGPT Code Assistant hoặc các developer khi **sửa code liên quan đến database trong project Laravel**, nhằm **tránh các lỗi kiểu:**
> ❌ Database Schema Error! Cột `X` không tồn tại.

---

## ⚙️ Nguyên tắc chung (Cấp cao)

1. **Không đoán tên cột hoặc bảng.**  
   Trước khi sinh SQL/Query hoặc thay đổi controller, **luôn kiểm tra schema/migration thực tế**.

2. **Ưu tiên thứ tự kiểm tra:**  
   **Schema thực tế > Migrations > Code logic.**

3. Nếu code tham chiếu một cột không tồn tại:  
   → Xác định xem cột nằm **ở bảng khác** hoặc **có tên khác** (ví dụ: `submission_date` → `submitted_at`).

4. Nếu không có quyền truy cập DB runtime:  
   → Báo rõ và đề xuất **2 phương án**:
   - (A) Sửa code để dùng đúng cột.  
   - (B) Tạo migration để thêm cột còn thiếu.

5. Khi sửa code:  
   - Luôn hiển thị **diff/patch cụ thể** (chỉ sửa phần cần thiết).  
   - Giải thích ngắn gọn **lý do và tác động**.

