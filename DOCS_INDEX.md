# 📚 TÀI LIỆU DỰ ÁN - INDEX

## 🚀 Bắt đầu

### Cho người mới
1. **[README.md](README.md)** - Tổng quan dự án
2. **[QUICKSTART.md](QUICKSTART.md)** ⚡ - Bắt đầu nhanh trong 5 phút
3. **[XAMPP_SETUP.md](XAMPP_SETUP.md)** 🔧 - Setup XAMPP chi tiết

### Cho Developer
1. **[API_DOCS.md](API_DOCS.md)** 📚 - API documentation đầy đủ
2. **[TESTING_GUIDE.md](TESTING_GUIDE.md)** 🧪 - Hướng dẫn test APIs
3. **[TODO.md](TODO.md)** ✅ - Task list (~150 tasks)

---

## 📖 Danh sách tài liệu đầy đủ

### 🎯 Setup & Installation
| File | Mô tả | Dành cho |
|------|-------|----------|
| **[QUICKSTART.md](QUICKSTART.md)** | Quick start trong 5 phút | 🟢 Beginner |
| **[XAMPP_SETUP.md](XAMPP_SETUP.md)** | Hướng dẫn setup XAMPP chi tiết | 🟡 Intermediate |
| **[XAMPP_MIGRATION.md](XAMPP_MIGRATION.md)** | Migration từ artisan serve sang XAMPP | 🔵 Info |

### 📚 API & Development
| File | Mô tả | Dành cho |
|------|-------|----------|
| **[API_DOCS.md](API_DOCS.md)** | API documentation đầy đủ | 🟢 All |
| **[TESTING_GUIDE.md](TESTING_GUIDE.md)** | Hướng dẫn test APIs với Postman | 🟢 All |
| **[database.md](database.md)** | Database schema SQL | 🟡 Database |

### 📊 Project Management
| File | Mô tả | Dành cho |
|------|-------|----------|
| **[TODO.md](TODO.md)** | Task list chi tiết | 🟡 Developer |
| **[PROGRESS.md](PROGRESS.md)** | Theo dõi tiến độ | 🔵 Manager |
| **[SUMMARY.md](SUMMARY.md)** | Báo cáo tổng kết | 🔵 Manager |

### 📖 Documentation
| File | Mô tả | Dành cho |
|------|-------|----------|
| **[README.md](README.md)** | README chính của dự án | 🟢 All |
| **[PROJECT_README.md](PROJECT_README.md)** | Tài liệu dự án chi tiết | 🟡 All |
| **[DOCS_INDEX.md](DOCS_INDEX.md)** | Index này | 🟢 All |

---

## 🎯 Workflow đọc tài liệu

### Scenario 1: Setup lần đầu
```
1. README.md          → Overview
2. QUICKSTART.md      → Setup nhanh
3. XAMPP_SETUP.md     → Chi tiết nếu cần
4. TESTING_GUIDE.md   → Test APIs
```

### Scenario 2: Development
```
1. API_DOCS.md        → Xem APIs available
2. TODO.md            → Chọn task
3. database.md        → Check schema
4. TESTING_GUIDE.md   → Test sau khi code
```

### Scenario 3: Troubleshooting
```
1. XAMPP_SETUP.md     → Troubleshooting section
2. API_DOCS.md        → Check API format
3. TESTING_GUIDE.md   → Test cases
```

### Scenario 4: Project Review
```
1. PROGRESS.md        → Xem tiến độ
2. SUMMARY.md         → Xem achievements
3. TODO.md            → Xem còn gì chưa làm
```

---

## 📝 Tài liệu theo Phase

### Phase 1: Database & Setup ✅
- ✅ database.md
- ✅ XAMPP_SETUP.md
- ✅ QUICKSTART.md

### Phase 2: Authentication ✅
- ✅ API_DOCS.md (Auth section)
- ✅ TESTING_GUIDE.md (Auth tests)
- ✅ Models documentation (in code)

### Phase 3: Conference Management 🚧
- ⬜ Conference APIs docs
- ⬜ Admin guide
- ⬜ User guide

### Phase 4: Paper Submission 🚧
- ⬜ Paper submission guide
- ⬜ File upload guide
- ⬜ Version control docs

### Phase 5: Review System 🚧
- ⬜ Bidding guide
- ⬜ Assignment algorithm docs
- ⬜ Review guide

### Phase 6: Frontend 🚧
- ⬜ UI/UX documentation
- ⬜ Component library
- ⬜ User manual

---

## 🔍 Tìm kiếm nhanh

### Tôi muốn...

**...setup dự án**
→ [QUICKSTART.md](QUICKSTART.md) hoặc [XAMPP_SETUP.md](XAMPP_SETUP.md)

**...test APIs**
→ [TESTING_GUIDE.md](TESTING_GUIDE.md)

**...xem APIs có gì**
→ [API_DOCS.md](API_DOCS.md)

**...biết làm gì tiếp theo**
→ [TODO.md](TODO.md)

**...xem database schema**
→ [database.md](database.md)

**...xem tiến độ dự án**
→ [PROGRESS.md](PROGRESS.md)

**...hiểu tổng quan dự án**
→ [PROJECT_README.md](PROJECT_README.md)

**...fix lỗi XAMPP**
→ [XAMPP_SETUP.md#troubleshooting](XAMPP_SETUP.md#troubleshooting)

**...biết accounts test**
→ Tất cả docs đều có, nhưng chính xác nhất: [TESTING_GUIDE.md](TESTING_GUIDE.md)

---

## 📞 Quick Reference

### Test Accounts
```
Admin:    admin@huit.edu.vn / admin123
Author:   author2@huit.edu.vn / password123
Reviewer: reviewer6@huit.edu.vn / password123
```

### URLs
```
Web:      http://huit-conferences.local
API:      http://huit-conferences.local/api
Health:   http://huit-conferences.local/api/health
```

### Important Commands
```bash
# Start XAMPP
Open XAMPP Control Panel → Start Apache + MySQL

# Check migrations
php artisan migrate:status

# Clear cache
php artisan config:clear
php artisan cache:clear

# Seed database
php artisan db:seed
```

---

## 📊 Documentation Stats

- **Total Files:** 12 files
- **Total Pages:** ~100+ pages
- **Last Updated:** 04/10/2025
- **Coverage:** 100% Phase 1 & 2
- **Language:** Vietnamese + English

---

## 🎯 Documentation Quality

| Aspect | Status |
|--------|--------|
| Setup Instructions | ✅ Complete |
| API Documentation | ✅ Complete (Phase 2) |
| Testing Guide | ✅ Complete |
| Troubleshooting | ✅ Complete |
| Code Examples | ✅ Complete |
| Screenshots | ⬜ Todo |
| Video Tutorials | ⬜ Todo |

---

## 🔄 Update History

| Date | File | Changes |
|------|------|---------|
| 04/10/2025 | All docs | Initial creation |
| 04/10/2025 | XAMPP docs | Migration to XAMPP |
| 04/10/2025 | API_DOCS | Phase 2 Auth APIs |
| 04/10/2025 | TESTING_GUIDE | Complete test scenarios |

---

## 💡 Tips

1. **Bookmark**: Bookmark file này để truy cập nhanh
2. **Search**: Dùng Ctrl+F để tìm trong docs
3. **Update**: Docs được cập nhật liên tục
4. **Contribute**: Nếu thấy thiếu gì, báo hoặc thêm vào

---

**Happy Reading! 📚**

[← Back to README](README.md)
