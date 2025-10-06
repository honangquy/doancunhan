# ⚡ PHASE 6 QUICK TEST GUIDE - 5 Minutes

**Goal**: Test all 5 Phase 6 APIs (Admin & Reports)  
**Time**: ~5 minutes  
**Prerequisites**: You have admin token from previous tests

---

## 🚀 QUICK START (3 Steps)

### Step 1: Get Admin Token (if needed)
```http
POST http://localhost/qly_hthao/qlyhoithao/public/api/auth/login
Content-Type: application/json

{
  "email": "admin@huit.edu.vn",
  "password": "123456"
}
```

**Save the token** from response: `access_token`

---

### Step 2: Test All 5 APIs

#### ✅ API 1: List All Users
```http
GET http://localhost/qly_hthao/qlyhoithao/public/api/admin/users
Authorization: Bearer YOUR_ADMIN_TOKEN
```

**Expected**: 200 OK, list of users with roles  
**Check**: See user details, roles array

---

#### ✅ API 2: Update User (Lock Account)
```http
PUT http://localhost/qly_hthao/qlyhoithao/public/api/admin/users/2
Authorization: Bearer YOUR_ADMIN_TOKEN
Content-Type: application/json

{
  "locked": true,
  "full_name": "Updated User Name"
}
```

**Expected**: 200 OK, user updated  
**Check**: `locked: true` in response

---

#### ✅ API 3: Assign Role to User
```http
POST http://localhost/qly_hthao/qlyhoithao/public/api/admin/users/2/roles
Authorization: Bearer YOUR_ADMIN_TOKEN
Content-Type: application/json

{
  "action": "assign",
  "role_code": "REVIEWER",
  "conference_id": 1
}
```

**Expected**: 200 OK, role assigned  
**Check**: New role appears in user's roles array

---

#### ✅ API 4: Conference Report
```http
GET http://localhost/qly_hthao/qlyhoithao/public/api/admin/reports/conference/1
Authorization: Bearer YOUR_ADMIN_TOKEN
```

**Expected**: 200 OK, detailed conference statistics  
**Check**: See papers, assignments, reviews, top reviewers

---

#### ✅ API 5: System Overview
```http
GET http://localhost/qly_hthao/qlyhoithao/public/api/admin/reports/overview
Authorization: Bearer YOUR_ADMIN_TOKEN
```

**Expected**: 200 OK, system-wide statistics  
**Check**: Total users, conferences, papers, reviews

---

### Step 3: Test Permission Denied

#### 🚫 Non-Admin Cannot Access
```http
GET http://localhost/qly_hthao/qlyhoithao/public/api/admin/users
Authorization: Bearer REVIEWER_OR_AUTHOR_TOKEN
```

**Expected**: 403 Forbidden  
**Message**: "Unauthorized. Admin access required."

---

## 📋 CHECKLIST

### User Management Tests:
- [ ] ✅ List all users (200 OK)
- [ ] ✅ Search users by email/name
- [ ] ✅ Filter users by role
- [ ] ✅ Update user details (200 OK)
- [ ] ✅ Lock user account (200 OK)
- [ ] ✅ Assign REVIEWER role (200 OK)
- [ ] ✅ Revoke role (200 OK)
- [ ] 🚫 Non-admin access denied (403)

### Reports Tests:
- [ ] ✅ Conference report as Admin (200 OK)
- [ ] ✅ Conference report as Chair (200 OK)
- [ ] ✅ System overview as Admin (200 OK)
- [ ] 🚫 Non-admin overview denied (403)

---

## 🎯 POSTMAN COLLECTION (COPY-PASTE)

```json
{
  "info": {
    "name": "Phase 6 - Admin & Reports",
    "schema": "https://schema.getpostman.com/json/collection/v2.1.0/collection.json"
  },
  "item": [
    {
      "name": "1. List Users",
      "request": {
        "method": "GET",
        "header": [
          {
            "key": "Authorization",
            "value": "Bearer {{admin_token}}"
          }
        ],
        "url": {
          "raw": "{{base_url}}/admin/users?per_page=20",
          "host": ["{{base_url}}"],
          "path": ["admin", "users"],
          "query": [
            {"key": "per_page", "value": "20"},
            {"key": "search", "value": "nguyen", "disabled": true},
            {"key": "role", "value": "REVIEWER", "disabled": true},
            {"key": "locked", "value": "false", "disabled": true}
          ]
        }
      }
    },
    {
      "name": "2. Update User",
      "request": {
        "method": "PUT",
        "header": [
          {
            "key": "Authorization",
            "value": "Bearer {{admin_token}}"
          },
          {
            "key": "Content-Type",
            "value": "application/json"
          }
        ],
        "body": {
          "mode": "raw",
          "raw": "{\n  \"full_name\": \"Updated Name\",\n  \"organization\": \"HUIT\",\n  \"locked\": false\n}"
        },
        "url": {
          "raw": "{{base_url}}/admin/users/{{user_id}}",
          "host": ["{{base_url}}"],
          "path": ["admin", "users", "{{user_id}}"]
        }
      }
    },
    {
      "name": "3. Assign Role",
      "request": {
        "method": "POST",
        "header": [
          {
            "key": "Authorization",
            "value": "Bearer {{admin_token}}"
          },
          {
            "key": "Content-Type",
            "value": "application/json"
          }
        ],
        "body": {
          "mode": "raw",
          "raw": "{\n  \"action\": \"assign\",\n  \"role_code\": \"REVIEWER\",\n  \"conference_id\": 1\n}"
        },
        "url": {
          "raw": "{{base_url}}/admin/users/{{user_id}}/roles",
          "host": ["{{base_url}}"],
          "path": ["admin", "users", "{{user_id}}", "roles"]
        }
      }
    },
    {
      "name": "4. Conference Report",
      "request": {
        "method": "GET",
        "header": [
          {
            "key": "Authorization",
            "value": "Bearer {{admin_token}}"
          }
        ],
        "url": {
          "raw": "{{base_url}}/admin/reports/conference/{{conference_id}}",
          "host": ["{{base_url}}"],
          "path": ["admin", "reports", "conference", "{{conference_id}}"]
        }
      }
    },
    {
      "name": "5. System Overview",
      "request": {
        "method": "GET",
        "header": [
          {
            "key": "Authorization",
            "value": "Bearer {{admin_token}}"
          }
        ],
        "url": {
          "raw": "{{base_url}}/admin/reports/overview",
          "host": ["{{base_url}}"],
          "path": ["admin", "reports", "overview"]
        }
      }
    }
  ],
  "variable": [
    {
      "key": "base_url",
      "value": "http://localhost/qly_hthao/qlyhoithao/public/api"
    },
    {
      "key": "admin_token",
      "value": "YOUR_ADMIN_TOKEN_HERE"
    },
    {
      "key": "user_id",
      "value": "2"
    },
    {
      "key": "conference_id",
      "value": "1"
    }
  ]
}
```

**Import Steps**:
1. Copy JSON above
2. Open Postman → Import → Raw text
3. Paste JSON → Import
4. Update `admin_token` variable

---

## 🎯 COMMON TEST SCENARIOS

### Scenario 1: Find & Lock Suspicious User
```bash
# Step 1: Search for user
GET /admin/users?search=suspicious@email.com

# Step 2: Lock their account
PUT /admin/users/15
{
  "locked": true
}
```

### Scenario 2: Promote User to Chair
```bash
# Step 1: Find user
GET /admin/users?search=newchair@huit.edu.vn

# Step 2: Assign CHAIR role
POST /admin/users/20/roles
{
  "action": "assign",
  "role_code": "CHAIR",
  "conference_id": 3
}
```

### Scenario 3: Monitor Conference Progress
```bash
# Get detailed conference report
GET /admin/reports/conference/1

# Check:
# - Papers by status
# - Review completion rate
# - Top reviewers
# - Papers needing attention
```

### Scenario 4: System Health Check
```bash
# Get system overview
GET /admin/reports/overview

# Check:
# - Total users, conferences, papers
# - Active conferences
# - Recent activity (last 30 days)
# - System health indicators
```

---

## 🐛 TROUBLESHOOTING

### Issue 1: "Unauthorized. Admin access required."
**Cause**: User is not ADMIN  
**Solution**: 
1. Make sure you're logged in as admin@huit.edu.vn
2. Or assign ADMIN role to your user:
   ```sql
   INSERT INTO VaiTroNguoiDung (user_id, role_code, conference_id)
   VALUES (YOUR_USER_ID, 'ADMIN', NULL);
   ```

### Issue 2: "User not found"
**Cause**: User ID doesn't exist  
**Solution**: 
1. List all users first: `GET /admin/users`
2. Use valid user_id from response

### Issue 3: "Conference not found"
**Cause**: Conference ID doesn't exist  
**Solution**: 
1. List conferences: `GET /conferences`
2. Use valid conference_id

### Issue 4: "Cannot lock your own account"
**Cause**: Trying to lock the admin account you're logged in with  
**Solution**: Lock a different user, not yourself

### Issue 5: "Cannot revoke your own admin role"
**Cause**: Trying to revoke ADMIN role from yourself  
**Solution**: Revoke role from another user, not yourself

---

## ✅ SUCCESS CRITERIA

After testing, you should see:
- ✅ All 5 APIs return 200 OK
- ✅ User list shows correct data
- ✅ User update works (locked status changes)
- ✅ Role assignment works (role appears in user)
- ✅ Conference report shows statistics
- ✅ System overview shows totals
- ✅ Non-admin gets 403 error

---

## 🎉 PHASE 6 COMPLETE!

Congratulations! You've tested all Phase 6 APIs:
- ✅ User Management (3 APIs)
- ✅ Reports & Analytics (2 APIs)

**BACKEND IS NOW 100% COMPLETE!** 🎊

---

## 📊 FINAL API COUNT

```
Phase 1: Database        ✅ 23 tables
Phase 2: Auth            ✅ 7 APIs
Phase 3: Conferences     ✅ 22 APIs
Phase 4: Papers          ✅ 13 APIs
Phase 5: Review System   ✅ 25 APIs
Phase 6: Admin & Reports ✅ 5 APIs
─────────────────────────────────
TOTAL:                   ✅ 73 APIs (100%)
```

---

## 🚀 NEXT STEPS

1. **Test More Scenarios**: Try edge cases, invalid inputs
2. **Check Logs**: Look at `storage/logs/laravel.log` for any errors
3. **Performance Test**: Try with large datasets
4. **Frontend Development**: Start building UI (Phase 7)
5. **Deployment**: Prepare for production

---

**Built with ❤️ using Laravel 10.x**  
**100% Backend Complete!** 🎉
