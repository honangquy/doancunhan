# ⚡ THUNDER CLIENT - QUICK REFERENCE

## 🚀 Quick Start (3 phút)

### 1️⃣ Install
```
VS Code → Extensions (Ctrl+Shift+X) → Search "Thunder Client" → Install
```

### 2️⃣ Import Collection
```
Click ⚡ icon → Collections tab → Menu (3 dots) → Import → 
Select: thunder-client-collection.json
```

### 3️⃣ Test
```
Open "1.2 Login Admin" → Click Send → Copy token → 
Set token in other requests → Test!
```

---

## 📋 24 APIs Ready to Test

### 🔐 Authentication (5)
- ✅ Health Check
- ✅ Login Admin
- ✅ Login Chair
- ✅ Get Profile
- ✅ Update Profile

### 🏛️ Conferences (7)
- ✅ List Conferences
- ✅ List Filtered
- ✅ Get Details
- ✅ Get Statistics
- ✅ Create Conference
- ✅ Update Conference
- ✅ My Conferences

### 🎯 Tracks (6)
- ✅ List Tracks
- ✅ Create Track
- ✅ Get Track Details
- ✅ Update Track
- ✅ Get Track Papers
- ✅ My Tracks

### 📝 Conference Requests (6)
- ✅ List Requests
- ✅ Submit Request
- ✅ Get Request Details
- ✅ Approve Request
- ✅ Reject Request
- ✅ Request Statistics

---

## 🔑 Test Accounts

```
Admin:    admin@huit.edu.vn / admin123
Chair:    chair1@huit.edu.vn / password123
Author:   author2@huit.edu.vn / password123
Reviewer: reviewer6@huit.edu.vn / password123
```

---

## 🎯 Testing Workflow

### Step 1: Login
```
Request: 1.2 Login Admin
Action: Click Send
Result: Copy token from response
```

### Step 2: Set Token
```
Method 1 (Quick): 
  Each request → Headers tab → 
  Replace {{token}} with actual token

Method 2 (Environment):
  Env tab → New Environment → 
  Add variable: token = your_token_here
  Select environment in dropdown
```

### Step 3: Test APIs
```
Try each request in order:
✅ 1.4 Get Profile → Should return user info
✅ 2.1 List Conferences → Should return 2 conferences
✅ 2.3 Get Conference Details → Should return full info
✅ 3.1 List Tracks → Should return 3 tracks
```

---

## 🐛 Common Errors & Fixes

| Error | Cause | Fix |
|-------|-------|-----|
| ECONNREFUSED | XAMPP not running | Start Apache in XAMPP |
| 401 Unauthorized | No/wrong token | Login again, update token |
| 403 Forbidden | Wrong role | Use Admin/Chair account |
| 422 Validation | Invalid data | Check JSON body format |
| 404 Not Found | Wrong URL/ID | Verify endpoint & ID |
| 500 Server Error | Code error | Check Laravel logs |

---

## 💡 Pro Tips

### 1. Environment Variables
```
Create variables:
- baseUrl = http://localhost/qly_hthao/qlyhoithao/public/api
- token = your_admin_token
- chairToken = your_chair_token

Use in requests:
- URL: {{baseUrl}}/conferences
- Header: Authorization: Bearer {{token}}
```

### 2. Copy as cURL
```
Right-click request → Copy as cURL
Paste in terminal to run
```

### 3. Save Responses
```
Click Save icon in response panel
Compare different responses
```

### 4. Test Scripts
```javascript
// Tab: Tests
tc.test("Status is 200", () => {
  tc.assert(tc.response.status == 200);
});

tc.test("Has success field", () => {
  tc.assert(tc.response.json.success === true);
});
```

---

## 📊 Test Progress Checklist

### Authentication ✅
- [ ] Health Check works
- [ ] Admin login gets token
- [ ] Chair login gets token
- [ ] Get Profile with token works
- [ ] Update Profile works

### Conferences ✅
- [ ] List conferences (public)
- [ ] Get conference details
- [ ] Get conference statistics
- [ ] Create conference (admin)
- [ ] Update conference (admin)
- [ ] Get my conferences

### Tracks ✅
- [ ] List tracks
- [ ] Create track (admin)
- [ ] Get track details
- [ ] Update track
- [ ] Get track papers
- [ ] Get my tracks (chair)

### Conference Requests ✅
- [ ] Submit request (chair)
- [ ] List requests
- [ ] Get request details
- [ ] Approve request (admin)
- [ ] Reject request (admin)
- [ ] Get statistics (admin)

---

## 🎯 Testing Scenarios

### Scenario A: Admin Flow
```
1. Login as admin
2. List all conferences
3. Create new conference
4. List tracks of conference
5. Create new track
6. View statistics
```

### Scenario B: Chair Flow
```
1. Login as chair
2. Submit conference request
3. View my requests
4. View my managed tracks
5. View my conferences
```

### Scenario C: Request Approval Flow
```
1. Login as chair → Submit request
2. Logout → Login as admin
3. View pending requests
4. Approve/Reject request
5. Verify conference status changed
```

---

## 📚 Full Documentation

- **Complete Guide:** [THUNDER_CLIENT_GUIDE.md](THUNDER_CLIENT_GUIDE.md)
- **API Docs:** [API_DOCS.md](API_DOCS.md)
- **Test Guide:** [TEST_GUIDE.md](TEST_GUIDE.md)

---

## 🆘 Need Help?

1. **Thunder Client not showing?**
   - Restart VS Code
   - Check if extension installed
   - Look for ⚡ icon in sidebar

2. **Import failed?**
   - Check file path: `thunder-client-collection.json`
   - Try manual creation: Copy requests from guide

3. **All requests failing?**
   - Check XAMPP running (Apache + MySQL)
   - Verify URL: http://localhost/qly_hthao/qlyhoithao/public/api/health

4. **Token not working?**
   - Token expires after 60 minutes
   - Login again to get new token
   - Update in Environment or Headers

---

**⚡ Thunder Client Ready!**

**Files Created:**
- ✅ `thunder-client-collection.json` - 24 requests
- ✅ `THUNDER_CLIENT_GUIDE.md` - Full guide
- ✅ `THUNDER_CLIENT_QUICK.md` - This quick reference

**Next:** Open Thunder Client, Import collection, Start testing! 🚀
