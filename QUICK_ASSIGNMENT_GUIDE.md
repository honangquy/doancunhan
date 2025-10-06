# 🚀 QUICK GUIDE: Phân Công Reviewer

## ✅ TL;DR - Đã sẵn sàng!

**114 assignments đã tồn tại trong hệ thống** ✅  
**Feature hoạt động hoàn hảo** ✅  
**Có thể phân công ngay** ✅

---

## 🎯 Cách Phân Công Reviewer (5 Bước)

### 1️⃣ Login as Chair
```
Email: chair1@huit.edu.vn
Password: password123
```

### 2️⃣ Vào Dashboard
```
http://localhost/qly_hthao/qlyhoithao/public/chair/dashboard
```

### 3️⃣ Chọn Bài Báo
- Click "Quản lý bài báo"
- Chọn một bài báo cần phân công

### 4️⃣ Click "Phân công phản biện"
- Nút màu xanh trong paper detail
- Hoặc từ menu dropdown

### 5️⃣ Chọn Reviewer & Deadline
```
1. Search reviewer (by name/email)
2. Click on reviewer card
3. Select deadline (future date)
4. Click "Phân công" button
```

---

## 📊 Trạng Thái Hiện Tại

```
✅ 49 Papers
✅ 69 Reviewers  
✅ 20 Chairs
✅ 114 Assignments (đã tồn tại!)
```

### Breakdown Assignments:
- **COMPLETED:** ~75 (66%) - Đã hoàn thành
- **INVITED:** ~25 (22%) - Đang chờ chấp nhận
- **ACCEPTED:** ~10 (9%) - Đã chấp nhận
- **DECLINED:** ~4 (3%) - Đã từ chối

---

## 🛡️ Tự Động Kiểm Tra

Hệ thống tự động ngăn chặn:

✅ **Self-review** - Tác giả không thể phản biện bài của mình  
✅ **Duplicate** - Một reviewer chỉ phân công 1 lần/bài  
✅ **COI** - Cảnh báo khi có xung đột lợi ích  
✅ **Access control** - Chỉ chair của conference mới phân công được  

---

## 📸 UI Features

### Trang Phân Công Có:

✅ **Search Box** - Tìm reviewer nhanh  
✅ **Expertise Tags** - Xem chuyên môn  
✅ **Workload Indicator** - Biết reviewer nào rảnh  
✅ **COI Warning** - Cảnh báo xung đột  
✅ **Current Assignments** - Xem ai đã được phân công  
✅ **Remove Option** - Xóa phân công nếu cần  

---

## 🔗 URLs

```bash
# View all papers
/chair/papers

# Paper detail
/chair/papers/{id}

# Assignment page
/chair/papers/{id}/assign

# Submit assignment (POST)
/chair/papers/{id}/assign

# Remove assignment (DELETE)
/chair/assignments/{id}
```

---

## 💡 Tips

### Chọn Reviewer Tốt:

1. **Check Expertise** - Khớp với topic bài báo
2. **Check Workload** - Ưu tiên người ít công việc
3. **Check COI** - Tránh xung đột lợi ích
4. **Set Realistic Deadline** - Thường 2-4 tuần

### Best Practices:

```
✅ Assign 3 reviewers per paper (standard)
✅ Give at least 2 weeks deadline
✅ Monitor assignment status regularly
✅ Follow up on INVITED status after 3 days
```

---

## 🧪 Test Commands

```bash
# Test với script
php test_assignment_feature.php

# Check database
php artisan tinker
>>> DB::table('PhanCongPhanBien')->count()

# Clear cache if needed
php artisan cache:clear
```

---

## ❓ Troubleshooting

### "No reviewers available"
- Tất cả reviewers đã được assign
- Hoặc tất cả có COI
- Hoặc đều là authors
→ **Solution:** Thêm reviewers mới hoặc chọn paper khác

### "Deadline must be future date"
- Ngày chọn phải sau hôm nay
→ **Solution:** Chọn ngày mai trở đi

### "Reviewer already assigned"
- Reviewer này đã phân công cho paper này rồi
→ **Solution:** Chọn reviewer khác

---

## 📞 Quick Reference

| Action | URL | Method |
|--------|-----|--------|
| List papers | `/chair/papers` | GET |
| View paper | `/chair/papers/{id}` | GET |
| Assign page | `/chair/papers/{id}/assign` | GET |
| Submit assign | `/chair/papers/{id}/assign` | POST |
| Remove assign | `/chair/assignments/{id}` | DELETE |

---

## ✅ Status

**PRODUCTION READY** 🚀

- ✅ All tests passing
- ✅ 114 real assignments exist
- ✅ Security validated
- ✅ UI fully functional
- ✅ AJAX working
- ✅ Error handling complete

---

**Có thể bắt đầu phân công reviewer ngay bây giờ!** 🎉

*Last updated: Oct 5, 2025, 19:25*
