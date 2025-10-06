# 📮 POSTMAN QUICK REFERENCE CARD

## 🚀 5-MINUTE SETUP

### Step 1: Import Collection (30 seconds)
```
1. Open Postman
2. Click "Import"
3. Select: HUIT-Conference-APIs-Complete.postman_collection.json
4. Done! ✅
```

### Step 2: Setup Environment (1 minute)
```
1. Click "Environments" → "+" (Create new)
2. Name: "HUIT Local"
3. Add variable:
   - baseUrl = http://localhost/qly_hthao/qlyhoithao/public/api
4. Save
5. Select environment from dropdown (top-right)
```

### Step 3: Login & Get Token (1 minute)
```
1. Run: "1.2 Login Admin"
   Body: {"email": "admin@huit.edu.vn", "password": "admin123"}
2. Token auto-saved to {{token}} ✅
3. All other APIs now authenticated!
```

### Step 4: Test APIs (2 minutes)
```
✅ Run "1.1 Health Check" → Verify server running
✅ Run "2.1 List Conferences" → See conferences
✅ Run "5.2 Submit Paper" → Upload paper (need PDF file)
✅ Run "6.1 List Versions" → See paper versions
```

**DONE! All 42 APIs ready to test! 🎉**

---

## 📋 ALL 42 APIs CHECKLIST

### 1. Authentication (7)
- [ ] 1.1 Health Check `GET /health`
- [ ] 1.2 Login Admin `POST /auth/login`
- [ ] 1.3 Login Chair `POST /auth/login`
- [ ] 1.4 Login Author `POST /auth/login`
- [ ] 1.5 Login Reviewer `POST /auth/login`
- [ ] 1.6 Get Profile `GET /auth/profile`
- [ ] 1.7 Update Profile `PUT /auth/profile`

### 2. Conferences (7)
- [ ] 2.1 List Conferences `GET /conferences`
- [ ] 2.2 Get Conference Details `GET /conferences/{id}`
- [ ] 2.3 Get Conference Statistics `GET /conferences/{id}/statistics`
- [ ] 2.4 Create Conference `POST /conferences` *Admin*
- [ ] 2.5 Update Conference `PUT /conferences/{id}` *Admin*
- [ ] 2.6 Delete Conference `DELETE /conferences/{id}` *Admin*
- [ ] 2.7 My Conferences `GET /my-conferences` *Chair*

### 3. Tracks (9)
- [ ] 3.1 List Tracks `GET /conferences/{id}/tracks`
- [ ] 3.2 Create Track `POST /conferences/{id}/tracks` *Admin*
- [ ] 3.3 Get Track Details `GET /tracks/{id}`
- [ ] 3.4 Update Track `PUT /tracks/{id}` *Admin/Chair*
- [ ] 3.5 Delete Track `DELETE /tracks/{id}` *Admin*
- [ ] 3.6 Get Track Papers `GET /tracks/{id}/papers`
- [ ] 3.7 Get Track Statistics `GET /tracks/{id}/statistics`
- [ ] 3.8 Assign Chair `POST /tracks/{id}/assign-chair` *Admin*
- [ ] 3.9 My Tracks `GET /my-tracks` *Chair*

### 4. Conference Requests (6)
- [ ] 4.1 List Requests `GET /conference-requests` *Admin*
- [ ] 4.2 Submit Request `POST /conference-requests` *Chair*
- [ ] 4.3 Get Request Details `GET /conference-requests/{id}`
- [ ] 4.4 Approve Request `POST /conference-requests/{id}/approve` *Admin*
- [ ] 4.5 Reject Request `POST /conference-requests/{id}/reject` *Admin*
- [ ] 4.6 Get Statistics `GET /conference-requests/statistics` *Admin*

### 5. Papers (8) ⭐ NEW
- [ ] 5.1 List Papers `GET /papers`
- [ ] 5.2 Submit Paper `POST /papers` *Author* **FILE UPLOAD**
- [ ] 5.3 Get Paper Details `GET /papers/{id}`
- [ ] 5.4 Update Paper `PUT /papers/{id}` *Submitter*
- [ ] 5.5 Withdraw Paper `DELETE /papers/{id}` *Submitter*
- [ ] 5.6 My Papers `GET /my-papers` *Author*
- [ ] 5.7 Paper Statistics `GET /papers/statistics`
- [ ] 5.8 Download Paper `GET /papers/{id}/download` **FILE DOWNLOAD**

### 6. Paper Versions (5) ⭐ NEW
- [ ] 6.1 List Versions `GET /papers/{paper_id}/versions`
- [ ] 6.2 Upload New Version `POST /papers/{paper_id}/versions` *Submitter* **FILE UPLOAD**
- [ ] 6.3 Get Version Details `GET /papers/{paper_id}/versions/{version_no}`
- [ ] 6.4 Download Version `GET /papers/{paper_id}/versions/{version_no}/download` **FILE DOWNLOAD**
- [ ] 6.5 Compare Versions `GET /papers/{paper_id}/versions/compare?version1=1&version2=2`

---

## 🔑 TEST ACCOUNTS

| Email | Password | Role | Token |
|-------|----------|------|-------|
| admin@huit.edu.vn | admin123 | Admin | {{adminToken}} |
| chair1@huit.edu.vn | password123 | Chair | {{chairToken}} |
| author2@huit.edu.vn | password123 | Author | {{authorToken}} |
| reviewer6@huit.edu.vn | password123 | Reviewer | {{reviewerToken}} |

---

## 📤 FILE UPLOAD CHEAT SHEET

### Request 5.2: Submit Paper
```
Method: POST
URL: {{baseUrl}}/papers
Authorization: Bearer {{authorToken}}
Body Type: form-data (NOT raw JSON!)

Fields:
✅ conference_id: 1
✅ track_id: 1
✅ title: "Your Paper Title"
✅ abstract: "Your abstract..."
✅ authors[0][user_id]: 3
✅ authors[0][is_contact]: true
✅ file: [Select File] → Upload PDF
```

### Request 6.2: Upload New Version
```
Method: POST
URL: {{baseUrl}}/papers/1/versions
Authorization: Bearer {{authorToken}}
Body Type: form-data

Fields:
✅ file: [Select File] → Upload PDF
✅ version_notes: "Fixed typos"
```

**File Requirements:**
- Type: PDF, DOC, DOCX only
- Size: Max 10MB
- Body: **form-data** (không dùng raw!)

---

## 🐛 QUICK TROUBLESHOOTING

| Error | Fix |
|-------|-----|
| 401 Unauthorized | Login again → Get new token |
| 403 Forbidden | Wrong role → Use admin token |
| 422 Validation | Check required fields → Read error message |
| 404 Not Found | Wrong ID → Check resource exists |
| 500 Server Error | Check logs: `storage/logs/laravel.log` |

**Debug Steps:**
1. ✅ Environment selected? (top-right dropdown)
2. ✅ Token saved? Check environment variables
3. ✅ Authorization header? Bearer {{token}}
4. ✅ Body format? form-data for files, raw JSON for others
5. ✅ Console log? View → Show Postman Console (Alt+Ctrl+C)

---

## 📊 COMMON TEST FLOWS

### Flow 1: Submit Paper (3 minutes)
```
1. Login Author (1.4) → Get {{authorToken}}
2. List Conferences (2.1) → Note ID
3. List Tracks (3.1) → Note ID
4. Submit Paper (5.2) → Upload PDF
5. My Papers (5.6) → Verify created
6. Download (5.8) → Get file back
```

### Flow 2: Version Control (2 minutes)
```
1. List Versions (6.1) → See v1
2. Upload Version (6.2) → Upload v2
3. List Versions (6.1) → See v1, v2
4. Compare (6.5) → ?version1=1&version2=2
5. Download v1 (6.4) → Get old version
```

### Flow 3: Admin Management (2 minutes)
```
1. Login Admin (1.2) → Get {{adminToken}}
2. Create Conference (2.4) → New conference
3. Create Track (3.2) → Add track
4. List Papers (5.1) → Filter by conference
5. Statistics (5.7) → View counts
```

---

## 🎯 PERMISSION MATRIX

| API | Public | Author | Chair | Admin |
|-----|--------|--------|-------|-------|
| List Conferences | ✅ | ✅ | ✅ | ✅ |
| Create Conference | ❌ | ❌ | ❌ | ✅ |
| Submit Paper | ❌ | ✅ | ✅ | ✅ |
| Update Paper | ❌ | ✅* | ❌ | ✅ |
| Withdraw Paper | ❌ | ✅* | ❌ | ✅ |
| Upload Version | ❌ | ✅* | ❌ | ✅ |
| Approve Request | ❌ | ❌ | ❌ | ✅ |

*Submitter only (paper owner)

---

## 🔄 PAPER STATUS WORKFLOW

```
SUBMITTED 
    ↓
UNDER_REVIEW
    ↓
REVISION_REQUIRED ←→ REVISED
    ↓
ACCEPTED / REJECTED
    ↓
CAMERA_READY
    ↓
WITHDRAWN
```

**Status Rules:**
- Update paper: SUBMITTED, REVISION_REQUIRED only
- Upload version: SUBMITTED, REVISION_REQUIRED, REVISED
- Withdraw: NOT ACCEPTED, CAMERA_READY, WITHDRAWN

---

## 💡 POSTMAN TIPS

### Auto-Save Token
```javascript
// Already configured! Just login:
Run "1.2 Login Admin" → Token auto-saved ✅
```

### Use Variables
```javascript
// In Tests tab:
pm.environment.set('paper_id', pm.response.json().data.id);

// In next request URL:
{{baseUrl}}/papers/{{paper_id}}
```

### Run Collection
```
1. Click "Runner"
2. Select collection
3. Select environment
4. Run → All 42 APIs tested!
```

### Console Debug
```
View → Show Postman Console (Alt+Ctrl+C)
See all requests/responses/logs
```

---

## 📚 DOCUMENTATION

| File | Description |
|------|-------------|
| POSTMAN_TUTORIAL.md | Full tutorial (this file) |
| PHASE4_API_DOCS.md | API details with examples |
| PHASE4_QUICK.md | Quick reference guide |
| README.md | Project overview |

---

## ✅ SUCCESS CHECKLIST

Before saying "Done":
- [ ] Imported collection successfully
- [ ] Created environment with baseUrl
- [ ] Logged in and got token
- [ ] Tested health check (200 OK)
- [ ] Listed conferences (got data)
- [ ] Submitted paper with file (201 Created)
- [ ] Downloaded paper (got file)
- [ ] Uploaded new version (version_no = 2)
- [ ] Compared versions (time + size diff)
- [ ] All 42 APIs showing in collection

**All checked? CONGRATULATIONS! 🎉**

---

## 🚀 READY TO GO!

**Base URL:** `http://localhost/qly_hthao/qlyhoithao/public/api`  
**Collection:** 42 APIs ready  
**Status:** ✅ Production Ready  
**Next:** Start testing or continue to Phase 5!

---

*Quick Reference Card v2.0*  
*Updated: 04/10/2025*  
*Total APIs: 42 (7+7+9+6+8+5)*
