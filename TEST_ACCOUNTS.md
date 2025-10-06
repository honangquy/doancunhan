# 🔐 TEST ACCOUNTS - Quick Reference

**Server:** XAMPP Apache  
**Login URL:** http://localhost/qly_hthao/qlyhoithao/public/login

---

## Test Credentials

| Role | Email | Password | Dashboard URL |
|------|-------|----------|---------------|
| 👤 **Author** | `author@test.com` | `password123` | http://localhost/qly_hthao/qlyhoithao/public/author/dashboard |
| 📝 **Reviewer** | `reviewer@test.com` | `password123` | http://localhost/qly_hthao/qlyhoithao/public/reviewer/dashboard |
| 👑 **Chair** | `chair@test.com` | `password123` | http://localhost/qly_hthao/qlyhoithao/public/chair/dashboard |
| 🔧 **Admin** | `admin@test.com` | `password123` | http://localhost/qly_hthao/qlyhoithao/public/admin/dashboard |

---

## Quick Test Steps

### 1️⃣ **Test Author Dashboard**
```
1. Go to: http://localhost/qly_hthao/qlyhoithao/public/login
2. Login: author@test.com / password123
3. Should see: Author dashboard with papers list
4. Stats should show: 0 papers (new account)
```

### 2️⃣ **Test Reviewer Dashboard**
```
1. Logout (if logged in)
2. Login: reviewer@test.com / password123
3. Should see: Reviewer dashboard with assignments
4. Stats should show: 0 assignments (new account)
```

### 3️⃣ **Test Chair Dashboard**
```
1. Logout (if logged in)
2. Login: chair@test.com / password123
3. Should see: Chair dashboard with conference papers
4. Stats should show: Conference data
```

### 4️⃣ **Test Admin Dashboard**
```
1. Logout (if logged in)
2. Login: admin@test.com / password123
3. Should see: Admin dashboard with system stats
4. Should show:
   - Total users: 253+
   - Total conferences: 6
   - Total papers: 45
   - Total reviews: 74
```

---

## 🔧 Troubleshooting

### **Problem: "Page not found" or 404 error**
**Solution:** Make sure Apache mod_rewrite is enabled
```
1. Open: C:\xampp\apache\conf\httpd.conf
2. Find: #LoadModule rewrite_module modules/mod_rewrite.so
3. Remove the # to uncomment it
4. Restart Apache in XAMPP Control Panel
```

### **Problem: "Server error" or blank page**
**Solution:** Check Laravel logs
```
Location: storage/logs/laravel.log
Look for recent error messages
```

### **Problem: Login doesn't work**
**Solution:** Check database connection
```
1. Open phpMyAdmin: http://localhost/phpmyadmin
2. Check database: hoi_thao_db
3. Check table: NguoiDung
4. Verify test accounts exist (user_id 250-253)
```

### **Problem: "Access denied" after login**
**Solution:** Check role assignments
```
1. Open phpMyAdmin
2. Go to table: VaiTroNguoiDung
3. Verify roles are assigned:
   - user_id 250 → AUTHOR
   - user_id 251 → REVIEWER
   - user_id 252 → CHAIR
   - user_id 253 → ADMIN
```

### **Problem: Sessions not working**
**Solution:** Check session storage permissions
```
Directory: storage/framework/sessions/
Make sure it's writable (on Windows usually automatic)
```

---

## 📊 Expected Data in Dashboards

### **Author Dashboard (author@test.com)**
- **Papers:** 0 (new account, no papers submitted yet)
- **Stats:** All zeros
- **Empty state:** "Chưa có bài báo nào"

### **Reviewer Dashboard (reviewer@test.com)**
- **Assignments:** 0 (new account, not assigned yet)
- **Stats:** All zeros
- **Empty state:** "Chưa có phân công nào"

### **Chair Dashboard (chair@test.com)**
- **Papers:** Shows all papers from first conference
- **Stats:** 
  - Total papers: ~15
  - Accepted: ~3-5
  - Under review: ~8-10
  - Needs reviewers: ~2-3

### **Admin Dashboard (admin@test.com)**
- **Users:** 253+ (all users including test accounts)
- **Conferences:** 6
- **Papers:** 45
- **Reviews:** 74
- **Role Distribution:**
  - Authors: ~63%
  - Reviewers: ~27%
  - Chairs: ~7%
  - Admins: ~2%

---

## 🎯 What to Test

### ✅ **Authentication**
- [ ] Can login with valid credentials
- [ ] Cannot login with invalid credentials
- [ ] Error message shows for wrong password
- [ ] Remember me checkbox works
- [ ] Can logout successfully

### ✅ **Role-Based Access**
- [ ] Author can access author dashboard
- [ ] Author CANNOT access reviewer/chair/admin dashboards
- [ ] Reviewer can access reviewer dashboard
- [ ] Reviewer CANNOT access author/chair/admin dashboards
- [ ] Chair can access chair dashboard
- [ ] Admin can access admin dashboard

### ✅ **Dashboard Data**
- [ ] Author dashboard shows correct user's papers
- [ ] Reviewer dashboard shows correct user's assignments
- [ ] Chair dashboard shows conference papers
- [ ] Admin dashboard shows system statistics
- [ ] Stats cards display real numbers from database

### ✅ **Security**
- [ ] Cannot access dashboards without login
- [ ] Redirects to login page when not authenticated
- [ ] Shows 403 Forbidden when accessing unauthorized pages
- [ ] Session persists across page refreshes
- [ ] Logout clears session properly

---

## 🚀 Ready to Test!

**Start here:** http://localhost/qly_hthao/qlyhoithao/public/login

**Have fun testing!** 🎉
