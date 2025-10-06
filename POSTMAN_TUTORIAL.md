# 📮 POSTMAN COLLECTION - HƯỚNG DẪN SỬ DỤNG

## 🎯 TỔNG QUAN

Collection này chứa **42 APIs hoàn chỉnh** cho HUIT Conference Management System:

| Module | APIs | Status |
|--------|------|--------|
| 1. Authentication | 7 | ✅ Complete |
| 2. Conferences | 7 | ✅ Complete |
| 3. Tracks | 9 | ✅ Complete |
| 4. Conference Requests | 6 | ✅ Complete |
| 5. Papers | 8 | ✅ NEW Phase 4 |
| 6. Paper Versions | 5 | ✅ NEW Phase 4 |
| **TOTAL** | **42** | **✅ Ready** |

---

## 📥 BƯỚC 1: IMPORT COLLECTION VÀO POSTMAN

### Option A: Import File
1. Mở **Postman Desktop** hoặc **Postman Web**
2. Click **Import** (góc trái trên)
3. Chọn file: `HUIT-Conference-APIs-Complete.postman_collection.json`
4. Click **Import** → Collection sẽ xuất hiện bên trái

### Option B: Import URL (nếu có)
```
https://raw.githubusercontent.com/your-repo/HUIT-Conference-APIs-Complete.postman_collection.json
```

---

## ⚙️ BƯỚC 2: SETUP ENVIRONMENT

### Tạo Environment Mới
1. Click **Environments** tab (bên trái)
2. Click **+** để tạo environment mới
3. Đặt tên: `HUIT Conference - Local`
4. Thêm các variables:

| Variable | Initial Value | Current Value | Type |
|----------|--------------|---------------|------|
| baseUrl | `http://localhost/qly_hthao/qlyhoithao/public/api` | - | default |
| token | | | default |
| adminToken | | | default |
| chairToken | | | default |
| authorToken | | | default |
| reviewerToken | | | default |

5. Click **Save**
6. Chọn environment vừa tạo từ dropdown (góc phải trên)

### Auto-Save Token
Collection đã được config để **tự động lưu token** sau khi login:
- Login Admin → Lưu vào `{{adminToken}}`
- Login Chair → Lưu vào `{{chairToken}}`
- Login Author → Lưu vào `{{authorToken}}`
- Login Reviewer → Lưu vào `{{reviewerToken}}`

---

## 🚀 BƯỚC 3: TEST APIs

### Test Flow Đầy Đủ (10 phút)

#### 1️⃣ Authentication (2 phút)
```
✅ 1.1 Health Check
   → Verify API is working
   
✅ 1.2 Login Admin
   → Email: admin@huit.edu.vn
   → Password: admin123
   → Token tự động lưu vào {{adminToken}} và {{token}}
   
✅ 1.3 Login Chair
   → Email: chair1@huit.edu.vn
   → Password: password123
   → Token tự động lưu vào {{chairToken}}
   
✅ 1.4 Login Author
   → Email: author2@huit.edu.vn
   → Password: password123
   → Token tự động lưu vào {{authorToken}}
   
✅ 1.6 Get Profile
   → Verify current user info
```

#### 2️⃣ Conference Management (2 phút)
```
✅ 2.1 List Conferences (Public)
   → No auth required
   → Try filters: ?status=OPEN&year=2025
   
✅ 2.2 Get Conference Details
   → Replace {id} with 1
   
✅ 2.3 Get Conference Statistics
   → See total tracks, papers, users
   
✅ 2.4 Create Conference (Admin)
   → Switch to Admin token
   → Create conference for 2026
   
✅ 2.7 My Conferences (Chair)
   → Switch to Chair token
   → See conferences where you're chair
```

#### 3️⃣ Track Management (2 phút)
```
✅ 3.1 List Tracks
   → See all tracks in conference
   
✅ 3.2 Create Track (Admin)
   → Create "Blockchain & Web3" track
   → Assign chair_id
   
✅ 3.3 Get Track Details
   → View track with papers count
   
✅ 3.6 Get Track Papers
   → See all papers in track
   
✅ 3.9 My Tracks (Chair)
   → Switch to Chair token
```

#### 4️⃣ Paper Submission (2 phút) **NEW!**
```
✅ 5.1 List Papers
   → Filter by conference_id, track_id, status
   → Search by title/abstract
   
✅ 5.2 Submit Paper (Author) ⭐ IMPORTANT
   → Switch to Author token ({{authorToken}})
   → Go to Body tab → form-data
   → Fill in:
      - conference_id: 1
      - track_id: 1
      - title: "Your paper title"
      - abstract: "Your abstract..."
      - authors[0][user_id]: 3 (existing user)
      - authors[0][is_contact]: true
      - file: [Click Select Files → Upload PDF]
   → Send → Should return 201 Created
   
✅ 5.3 Get Paper Details
   → Replace {id} with paper ID from previous response
   → See authors, versions, status
   
✅ 5.6 My Papers (Author)
   → See all papers where you're author/submitter
   
✅ 5.8 Download Paper
   → Downloads PDF file
```

#### 5️⃣ Version Control (2 phút) **NEW!**
```
✅ 6.1 List Versions
   → See all versions of paper
   
✅ 6.2 Upload New Version ⭐ IMPORTANT
   → Switch to Author token
   → Go to Body tab → form-data
   → Fill in:
      - file: [Click Select Files → Upload PDF]
      - version_notes: "Fixed typos"
   → Send → Version auto-increments
   
✅ 6.3 Get Version Details
   → View specific version info
   
✅ 6.4 Download Version
   → Download specific version file
   
✅ 6.5 Compare Versions
   → Add query params: ?version1=1&version2=2
   → See time diff and size diff
```

---

## 🔑 TÀI KHOẢN TEST

| Role | Email | Password | Token Variable |
|------|-------|----------|----------------|
| **Admin** | admin@huit.edu.vn | admin123 | {{adminToken}} |
| **Chair** | chair1@huit.edu.vn | password123 | {{chairToken}} |
| **Author** | author2@huit.edu.vn | password123 | {{authorToken}} |
| **Reviewer** | reviewer6@huit.edu.vn | password123 | {{reviewerToken}} |

---

## 📋 CÁCH SỬ DỤNG TOKEN

### Auto-Save (Recommended)
Collection đã setup auto-save token. Chỉ cần:
1. Run "1.2 Login Admin" → Token tự động lưu
2. Run các APIs khác → Token tự động dùng từ `{{token}}`

### Manual Switch Token
Nếu muốn switch giữa các roles:
1. Open request cần test
2. Go to Authorization tab
3. Change từ `{{token}}` sang `{{adminToken}}` / `{{chairToken}}` / `{{authorToken}}`

### Check Token
```bash
# In Postman Console (View → Show Postman Console)
console.log('Current token:', pm.environment.get('token'));
console.log('Admin token:', pm.environment.get('adminToken'));
```

---

## 📤 UPLOAD FILE TRONG POSTMAN

### Bước Upload Paper (Request 5.2)
1. **Select request:** "5.2 Submit Paper (Author)"
2. **Authorization tab:** Bearer Token = `{{authorToken}}`
3. **Body tab:** 
   - Select **form-data** (NOT raw JSON!)
   - Add fields như trong collection
4. **Upload file:**
   - Key: `file`
   - Type: **File** (dropdown bên phải)
   - Value: Click **Select Files** → Chọn PDF
5. **Send request**

### Supported File Types
- ✅ PDF (.pdf)
- ✅ DOC (.doc)
- ✅ DOCX (.docx)
- ❌ Max size: 10MB

### Test Files
Tạo file test:
```bash
# Create dummy PDF (Windows PowerShell)
"Test Paper Content" | Out-File -FilePath test-paper.pdf -Encoding utf8
```

---

## 🔍 DEBUG & TROUBLESHOOTING

### Enable Console Log
1. **View** menu → **Show Postman Console** (Alt+Ctrl+C)
2. Xem request/response details
3. Xem auto-save token logs

### Common Errors

#### ❌ Error 401: Unauthorized
```json
{
  "message": "Unauthenticated."
}
```
**Fix:**
- Login lại để lấy token mới
- Check token đã lưu vào environment chưa
- Check Authorization header: `Bearer {{token}}`

#### ❌ Error 403: Forbidden
```json
{
  "message": "You do not have permission to perform this action."
}
```
**Fix:**
- Check role: Admin-only APIs cần admin token
- Switch token: Dùng `{{adminToken}}` thay vì `{{authorToken}}`

#### ❌ Error 422: Validation Error
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "file": ["The file field is required."]
  }
}
```
**Fix:**
- Check required fields
- Check file upload: Phải dùng form-data, not raw JSON
- Check file type: Chỉ PDF/DOC/DOCX
- Check file size: Max 10MB

#### ❌ Error 404: Not Found
```json
{
  "message": "Resource not found."
}
```
**Fix:**
- Check ID trong URL: /papers/{id}
- Check resource có tồn tại không (list trước)

#### ❌ Error 500: Server Error
```json
{
  "message": "Server Error"
}
```
**Fix:**
- Check Laravel logs: `storage/logs/laravel.log`
- Check database connection
- Check file permissions: `storage/` folder writable

### Debug Checklist
```
☐ Base URL correct? http://localhost/qly_hthao/qlyhoithao/public/api
☐ Environment selected? (góc phải trên)
☐ Token saved? Check in environment variables
☐ Authorization header? Bearer {{token}}
☐ Content-Type? application/json (for JSON) or multipart/form-data (for files)
☐ Body format? form-data for file upload, raw JSON for others
☐ File size? Max 10MB
☐ File type? PDF/DOC/DOCX only
```

---

## 📊 TEST SCENARIOS

### Scenario 1: Author Submit Paper (Full Flow)
```
1. Login Author (1.4) → Save {{authorToken}}
2. List Conferences (2.1) → Note conference_id
3. List Tracks (3.1) → Note track_id
4. Submit Paper (5.2):
   - Use {{authorToken}}
   - Upload PDF file
   - Add authors (existing + external)
5. My Papers (5.6) → Verify paper created
6. Get Paper Details (5.3) → Check version 1
7. Upload New Version (6.2) → Upload v2
8. Compare Versions (6.5) → Compare v1 vs v2
9. Download Paper (5.8) → Download file
```

### Scenario 2: Admin Manage Conference
```
1. Login Admin (1.2) → Save {{adminToken}}
2. Create Conference (2.4) → New conference 2026
3. Create Track (3.2) → Add tracks
4. Get Statistics (2.3) → View stats
5. List Papers (5.1) → Filter by conference
6. Paper Statistics (5.7) → View paper counts
```

### Scenario 3: Chair Manage Track
```
1. Login Chair (1.3) → Save {{chairToken}}
2. My Tracks (3.9) → View assigned tracks
3. Get Track Papers (3.6) → See submitted papers
4. Get Track Statistics (3.7) → View track stats
```

### Scenario 4: Multi-Author Paper
```
1. Login Author (1.4)
2. Submit Paper (5.2) with:
   authors[0][user_id]: 3 (existing user author2)
   authors[0][is_contact]: true
   authors[1][full_name]: "Nguyễn Văn B"
   authors[1][email]: "nguyenvanb@example.com"
   authors[1][organization]: "VNU-HCM"
3. Check paper details → See 2 authors
4. External author auto-created in database
```

---

## 🎓 TIPS & BEST PRACTICES

### 1. Organize Your Workspace
- Tạo separate environments: Local, Staging, Production
- Duplicate collection để test riêng
- Use folders để group related tests

### 2. Use Variables
```javascript
// In Tests tab (sau khi send request):
// Save paper_id for next requests
pm.environment.set('paper_id', pm.response.json().data.id);

// Use in next request:
GET {{baseUrl}}/papers/{{paper_id}}
```

### 3. Chain Requests
Run Collection với Postman Runner:
1. Click **Runner** button
2. Select collection
3. Select environment
4. Click **Run** → Tất cả APIs chạy tuần tự

### 4. Export Test Results
1. Run collection
2. Click **Export Results**
3. Share with team

### 5. API Documentation
Generate API docs từ Postman:
1. Click **...** trên collection
2. Select **View Documentation**
3. Click **Publish** để share public

---

## 📚 TÀI LIỆU THAM KHẢO

### Documentation Files
- `PHASE4_API_DOCS.md` - Chi tiết 13 APIs Phase 4
- `PHASE4_QUICK.md` - Quick reference guide
- `PHASE4_COMPLETE.md` - Phase 4 summary
- `README.md` - Project overview

### API Status Codes
| Code | Meaning | Action |
|------|---------|--------|
| 200 | OK | Success |
| 201 | Created | Resource created |
| 400 | Bad Request | Check request format |
| 401 | Unauthorized | Login required |
| 403 | Forbidden | Permission denied |
| 404 | Not Found | Resource not exists |
| 422 | Validation Error | Check input data |
| 500 | Server Error | Check logs |

### Paper Status Workflow
```
SUBMITTED → UNDER_REVIEW → REVISION_REQUIRED → REVISED → ACCEPTED/REJECTED
                                ↓
                           CAMERA_READY
                                ↓
                            WITHDRAWN
```

---

## 🎯 NEXT STEPS

### After Testing Phase 4
1. ✅ Verify all 42 APIs working
2. ✅ Test file upload/download
3. ✅ Test multi-author papers
4. ✅ Test version control
5. ⏳ Ready for **Phase 5: Review System** (~15 APIs)

### Phase 5 Preview
- Bidding system
- COI management
- Reviewer assignment
- Review submission
- Decision making

---

## 💬 SUPPORT

### Issues?
1. Check `storage/logs/laravel.log`
2. Check Postman Console (Alt+Ctrl+C)
3. Read error message carefully
4. Check documentation

### Questions?
- Read `PHASE4_API_DOCS.md` for API details
- Read `PHASE4_QUICK.md` for quick reference
- Check this guide's troubleshooting section

---

## 🎉 CONGRATULATIONS!

Bạn đã có **42 APIs hoàn chỉnh** với:
- ✅ Authentication (JWT)
- ✅ Conference Management
- ✅ Track Management
- ✅ Paper Submission
- ✅ Version Control
- ✅ File Upload/Download
- ✅ Multi-Author Support
- ✅ Permission System

**Ready to test!** 🚀

---

*Generated: 04/10/2025*  
*Collection Version: 2.0*  
*Total APIs: 42*  
*Status: Production Ready ✅*
