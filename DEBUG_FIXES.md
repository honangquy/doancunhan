# 🔧 Debug Report - Fix 2 Issues

## ❌ Vấn đề 1: Dropdown Conference không load đúng
**Nguyên nhân:** Query trong ReviewerInvitationController có thể có vấn đề với JOIN syntax

**✅ Giải pháp đã áp dụng:**
1. Sửa lại query từ closure-based join thành simple join
2. Thêm filter `status = 'ACTIVE'` 
3. Thêm logging để debug
4. Thêm error handling tốt hơn

**📝 Code đã sửa:**
```php
// Before (có thể có vấn đề)
->join('vaitronguoidung as vt', function($join) use ($userId) {
    $join->on('ht.conference_id', '=', 'vt.conference_id')
         ->where('vt.user_id', '=', $userId)
         ->where('vt.role_code', '=', 'CHAIR');
})

// After (đơn giản và ổn định hơn)  
->join('vaitronguoidung as vt', 'ht.conference_id', '=', 'vt.conference_id')
->where('vt.user_id', $userId)
->where('vt.role_code', 'CHAIR')
->where('ht.status', 'ACTIVE')
```

---

## ❌ Vấn đề 2: Auto Approval thay vì Manual Approval 
**Nguyên nhân:** Code tự động tạo role `REVIEWER` thay vì tạo request để admin duyệt

**✅ Giải pháp đã áp dụng:**
1. Sử dụng bảng `join_requests` có sẵn (không tạo bảng mới)
2. Tạo request với status `PENDING` thay vì tự động assign role
3. Tích hợp với hệ thống admin approval có sẵn
4. Admin sẽ thấy request trong mục "Yêu cầu vai trò" và duyệt thủ công

**📝 Code đã sửa:**
```php
// Before: Tự động assign role
DB::table('vaitronguoidung')->insert([
    'user_id' => $user->user_id,
    'conference_id' => $invitation->conference_id,
    'role_code' => 'REVIEWER', // ❌ Tự động cấp role
    'assigned_at' => now()
]);

// After: Tạo request để admin duyệt
DB::table('join_requests')->insert([
    'user_id' => $user->user_id,
    'conference_id' => $invitation->conference_id,
    'role' => 'REVIEWER',
    'status' => 'PENDING', // ✅ Chờ admin duyệt
    'invitation_token' => $token,
    // ... other fields
]);
```

---

## 🔄 Luồng hoạt động sau khi sửa:

### Vấn đề 1: Dropdown Conference
1. Chair login với `honangquy1@gmail.com` 
2. Vào `/chair/reviewers/invite`
3. Dropdown sẽ hiển thị: "Hội thảo Khoa học CNTT HUIT 2025"
4. Log sẽ ghi lại việc load conferences

### Vấn đề 2: Manual Approval  
1. User nhận email mời → Click link
2. Đăng nhập/Đăng ký → Điền form thông tin
3. Submit form → Tạo **join request** (PENDING)
4. Admin nhận thông báo → Vào "Yêu cầu vai trò"  
5. Admin xem và **duyệt thủ công**
6. Sau khi duyệt → User được cấp role REVIEWER

---

## 🎯 Cách test:

### Test Issue 1:
```bash
# 1. Login as chair: honangquy1@gmail.com
# 2. Go to: /chair/reviewers/invite  
# 3. Check dropdown có conferences không
# 4. Check Laravel log để thấy conferences được load
```

### Test Issue 2:  
```bash
# 1. Tạo reviewer invitation từ chair
# 2. User click link → điền form → submit
# 3. Check table join_requests có record PENDING
# 4. Admin login → check "Yêu cầu vai trò" có request mới
# 5. Admin duyệt → check user được cấp role REVIEWER
```

---

## 📊 Tables involved:
- `join_requests`: Chứa tất cả yêu cầu tham gia (Author + Reviewer)
- `reviewer_invitations`: Lời mời từ chair  
- `vaitronguoidung`: Roles được assign sau khi admin duyệt
- `notifications`: Thông báo cho admin

**✅ Status: Ready for testing!**