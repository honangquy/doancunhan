# 🧪 CHAIR REQUEST FEATURE - TESTING GUIDE

**Version**: 1.0  
**Date**: October 20, 2025  
**Status**: Ready for Testing

---

## 📋 Pre-Testing Setup

### 1. Database Migrations
```bash
# Run all migrations
php artisan migrate

# Verify tables exist
php artisan tinker
>>> DB::table('yeucauhoithao')->count()
>>> DB::table('themvienbosungng')->count()
```

### 2. Mail Configuration
Update `.env`:
```
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io          # Or your email service
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_FROM_ADDRESS=noreply@huit-conference.local
MAIL_FROM_NAME="HUIT Conference System"
```

### 3. Storage Setup
```bash
# Create storage symlink for public file access
php artisan storage:link

# Verify write permissions
chmod -R 755 storage/app/public
chmod -R 755 storage/conference-requests
```

### 4. Clear Caches
```bash
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear
```

---

## 🧪 STEP 1: Test User Form Submission

### Prerequisites
- User account with verified email
- Admin account for review

### Test Cases

#### Test 1.1: Form Appears After Email Verification
```
1. Login as user with verified email
2. Go to homepage
3. Open user menu dropdown
4. Verify "Tạo hội thảo" button appears
5. Click button
6. Verify modal form appears
```

#### Test 1.2: Form Validation (Client-side)
```
1. Open conference request form
2. Leave all required fields empty
3. Click "Cấu hình Hội thảo" button
4. Verify error messages appear for required fields
   - "Tiêu đề hội thảo không được để trống"
   - "Lĩnh vực không được để trống"
   - etc.
5. Fill one field at a time and verify error clears
```

#### Test 1.3: Co-chair Management
```
1. In form, click "Thêm co-chair"
2. Fill: Name, Email, Affiliation
3. Verify co-chair row appears
4. Click "Xóa" on that row
5. Verify co-chair removed
6. Add multiple co-chairs (test with 3-5)
7. Verify all appear in list
```

#### Test 1.4: File Upload
```
1. In form, click file upload area
2. Try uploading:
   a) Valid PDF file → Should show filename ✅
   b) Non-PDF file (DOC, DOCX) → Should show error ❌
   c) Large file (>10MB) → Should show error ❌
3. Test drag-and-drop:
   a) Drag PDF onto upload area
   b) Verify file accepted
   c) Verify filename displayed
```

#### Test 1.5: Form Submission
```
1. Fill all required fields:
   - Title: "AI Conference 2025"
   - Field: "Artificial Intelligence"
   - Level: "TRUONG"
   - Expected Date: 2025-12-01
   - Objective: "Discuss latest AI trends and applications"
   - Facility: Select from dropdown
   - Chair Name: "John Doe"
   - Chair Email: "john@example.com"
   - File: Upload PDF
2. Click "Cấu hình Hội thảo"
3. Verify success message appears:
   "Yêu cầu tạo hội thảo đã được gửi thành công!"
4. Verify form closes
5. Verify notification appears with request_id
```

#### Test 1.6: Database Check After Submission
```bash
# Check request created
php artisan tinker
>>> $req = App\Models\YeuCauHoiThao::latest()->first()
>>> $req->title                    # Should be "AI Conference 2025"
>>> $req->status                   # Should be "PENDING"
>>> $req->user_id                  # Should be logged-in user's ID
>>> $req->proposal_file            # Should have file path
>>> $req->coChairs()->count()       # Should match co-chairs added
```

---

## 🧪 STEP 2: Test Admin Review Panel

### Prerequisites
- At least one pending conference request
- Admin account

### Test Cases

#### Test 2.1: Admin Panel Access
```
1. Login as admin user
2. Navigate to /admin/conference-requests
3. Verify page loads
4. Verify table displays with columns:
   - ID ✅
   - Title ✅
   - Field ✅
   - Level ✅
   - Requester ✅
   - Expected Date ✅
   - Status (badge) ✅
   - Actions ✅
```

#### Test 2.2: Table Display
```
1. Verify requests display in table
2. Check each row shows:
   - Request ID
   - Conference title
   - Field of research
   - Level (KHOA/TRUONG)
   - Requester name
   - Expected date
   - Status badge (PENDING = yellow, APPROVED = green, REJECTED = red)
3. Verify pagination works (if >15 requests)
```

#### Test 2.3: Search Functionality
```
1. In search box, type part of conference title
2. Verify table filters to matching requests only
3. Clear search
4. Type requester name
5. Verify table filters to that requester's requests
6. Test with non-existent text
7. Verify no results shown
```

#### Test 2.4: Filter by Status
```
1. Click "Status" filter dropdown
2. Verify options: All, Pending, Approved, Rejected
3. Select "Pending"
4. Verify only PENDING requests shown
5. Select "Approved"
6. Verify only APPROVED requests shown
7. Select "Rejected"
8. Verify only REJECTED requests shown
```

#### Test 2.5: Filter by Level
```
1. Click "Level" filter dropdown
2. Select "KHOA"
3. Verify only KHOA-level requests shown
4. Select "TRUONG"
5. Verify only TRUONG-level requests shown
```

#### Test 2.6: Detail Modal
```
1. Click "Chi tiết" button on a request
2. Verify modal opens showing:
   - Title
   - Field
   - Level
   - Expected Date
   - Objective
   - Chair info (name, email, phone)
   - Co-chairs list
   - Proposal file download link
   - Status badge
3. Click file download link
4. Verify PDF downloads
5. Close modal
```

#### Test 2.7: Database Check
```bash
php artisan tinker
>>> $requests = App\Models\YeuCauHoiThao::where('status', 'PENDING')->get()
>>> count($requests)               # Should match pending in table
```

---

## 🧪 STEP 3: Test Approval Endpoint

### Prerequisites
- Pending conference request
- Admin account
- Mail service configured

### Test Cases

#### Test 3.1: Approve Request via Admin Panel
```
1. In admin panel, find a PENDING request
2. Click "Duyệt" (Approve) button
3. Verify confirmation dialog appears
4. Click "Xác nhận"
5. Verify success message: "Yêu cầu đã được duyệt thành công!"
6. Verify request status changes to "APPROVED" in table
7. Verify "Duyệt" button disappears
```

#### Test 3.2: Direct API Call (Postman)
```
Endpoint: POST /api/conference-requests/{id}/approve
Headers: 
  - Authorization: Bearer {admin_token}
  - Content-Type: application/json

Response should be:
{
    "success": true,
    "message": "Yêu cầu đã được duyệt thành công!",
    "data": {
        "request_id": 123,
        "status": "APPROVED",
        "approver_id": {admin_id},
        "approved_at": "2025-10-20T10:30:00",
        ...
    }
}
```

#### Test 3.3: Email Sent
```
1. Check mail service (Mailtrap/email logs)
2. Verify email sent to requester email
3. Verify email subject: "Yêu cầu Tạo Hội thảo Được Duyệt"
4. Verify email body contains:
   - "Yêu cầu ... đã được duyệt thành công"
   - Conference title
   - "Cấu hình Hội thảo" button/link
   - Link URL should be: /chair/configure-conference/{id}
```

#### Test 3.4: Notification Created
```bash
php artisan tinker
>>> $notif = App\Models\Notification::where('type', 'conference_request_approved')->latest()->first()
>>> $notif->user_id                # Should be requester's ID
>>> $notif->title                  # Should be "Yêu cầu Tạo Hội thảo Được Duyệt"
>>> $notif->read_at                # Should be null (unread)
>>> json_decode($notif->data)      # Should have request_id
```

#### Test 3.5: CHAIR Role Assigned
```bash
php artisan tinker
>>> $user = App\Models\NguoiDung::find({requester_id})
>>> $user->roles()->where('role_code', 'CHAIR')->exists()  # Should be true
>>> $role = $user->roles()->where('role_code', 'CHAIR')->first()
>>> $role->role_code               # Should be "CHAIR"
```

#### Test 3.6: Requester Sees Notification
```
1. Login as requester
2. Check notification dropdown (bell icon)
3. Verify unread notification badge appears
4. Click to open notification
5. Verify message shows approval message
6. Verify "Cấu hình" button/link appears
7. Click link
8. Verify redirects to /chair/configure-conference/{id}
```

---

## 🧪 STEP 4: Test Rejection Endpoint

### Prerequisites
- Pending conference request
- Admin account
- Mail service configured

### Test Cases

#### Test 4.1: Reject Request via Admin Panel
```
1. In admin panel, find a PENDING request
2. Click "Từ chối" (Reject) button
3. Verify rejection reason modal appears
4. Enter reason: "Proposal content does not meet requirements"
5. Click "Xác nhận"
6. Verify success message
7. Verify status changes to "REJECTED" in table
8. Verify "Từ chối" button disappears
```

#### Test 4.2: Direct API Call (Postman)
```
Endpoint: POST /api/conference-requests/{id}/reject
Headers:
  - Authorization: Bearer {admin_token}
  - Content-Type: application/json

Body:
{
    "reason": "Proposal content does not meet requirements"
}

Response should be:
{
    "success": true,
    "message": "Yêu cầu đã bị từ chối",
    "data": {
        "request_id": 123,
        "status": "REJECTED",
        "approval_note": "Proposal content does not meet requirements",
        ...
    }
}
```

#### Test 4.3: Email Sent with Reason
```
1. Check mail service logs
2. Verify email sent to requester
3. Verify email subject: "Yêu cầu Tạo Hội thảo Bị Từ chối"
4. Verify email body contains:
   - Rejection notice
   - Reason displayed
   - Conference title
   - Encouragement to resubmit
```

#### Test 4.4: Notification Created
```bash
php artisan tinker
>>> $notif = App\Models\Notification::where('type', 'conference_request_rejected')->latest()->first()
>>> $notif->title                  # Should be "Yêu cầu Tạo Hội thảo Bị Từ chối"
>>> json_decode($notif->data)      # Should include reason
```

---

## 🧪 STEP 5: Test Conference Configuration

### Prerequisites
- Approved conference request
- CHAIR role assigned
- Same user logged in as requester

### Test Cases

#### Test 5.1: Configuration Form Access
```
1. Login as CHAIR (requester who got approval)
2. Go to /chair/configure-conference/{id}
3. Verify form loads
4. Verify title field is pre-filled with conference title
5. Verify read-only (cannot edit title)
```

#### Test 5.2: Form Validation
```
1. Leave all required fields empty
2. Click "Cấu hình Hội thảo"
3. Verify errors for:
   - Description (required)
   - Location (required)
   - Contact Email (required)
   - Chair Name (required)
   - Chair Email (required)
4. Verify optional fields (CFP URL, etc.) can be empty
```

#### Test 5.3: Fill and Submit Form
```
1. Fill all required fields:
   - Description: "A comprehensive conference on modern AI techniques"
   - Location: "Hanoi University of Science"
   - Contact Email: john@example.com
   - Contact Phone: +84912345678
   - Chair Name: John Doe
   - Chair Email: john@example.com
   - Keywords: "AI, Machine Learning, NLP"
2. Optionally fill:
   - CFP URL: https://example.com/cfp.pdf
   - Submission Guidelines: "Papers should be..."
3. Click "Cấu hình Hội thảo"
4. Verify success message: "Cấu hình hội thảo thành công!"
5. Verify redirect to /chair/my-conferences
```

#### Test 5.4: Database Verification
```bash
php artisan tinker
>>> $conf = App\Models\HoiThao::latest()->first()
>>> $conf->description             # Should be filled
>>> $conf->location                # Should be filled
>>> $conf->contact_email           # Should be filled
>>> $conf->status                  # Should be "OPEN"

>>> $req = App\Models\YeuCauHoiThao::find({id})
>>> $req->status                   # Should be "CONFIGURED"
>>> $req->conference_id            # Should match $conf->conference_id
```

#### Test 5.5: Conference Now Visible
```
1. Go to /conferences page (public)
2. Search/filter for the conference
3. Verify conference appears in list
4. Click to view conference details
5. Verify all configured information displays:
   - Description ✅
   - Location ✅
   - Contact info ✅
   - Keywords ✅
   - CFP link (if provided) ✅
   - Submission guidelines (if provided) ✅
```

---

## 🧪 STEP 6: Test Notification API Endpoints

### Prerequisites
- At least one notification created
- User account with notifications

### Test Cases

#### Test 6.1: Get Notifications
```
Endpoint: GET /api/notifications?per_page=10&filter=all
Headers: Authorization: Bearer {token}

Response:
{
    "success": true,
    "data": {
        "data": [
            {
                "id": 1,
                "user_id": 123,
                "type": "conference_request_approved",
                "title": "Yêu cầu Tạo Hội thảo Được Duyệt",
                "message": "...",
                "read_at": null,
                "created_at": "2025-10-20T10:30:00"
            }
        ],
        "current_page": 1,
        "total": 5
    },
    "unread_count": 3
}
```

#### Test 6.2: Get Unread Count
```
Endpoint: GET /api/notifications/unread
Headers: Authorization: Bearer {token}

Response:
{
    "success": true,
    "unread_count": 3
}
```

#### Test 6.3: View Notification (Auto-read)
```
Endpoint: GET /api/notifications/{id}
Headers: Authorization: Bearer {token}

1. Before: GET /api/notifications/unread → count should be X
2. GET /api/notifications/{id}
3. After: GET /api/notifications/unread → count should be X-1
4. Verify read_at is now set in response
```

#### Test 6.4: Mark as Read
```
Endpoint: PATCH /api/notifications/{id}/read
Headers: Authorization: Bearer {token}

Response:
{
    "success": true,
    "message": "Thông báo đã được đánh dấu là đã đọc",
    "data": {
        "read_at": "2025-10-20T10:35:00"
    }
}
```

#### Test 6.5: Mark All as Read
```
Endpoint: PATCH /api/notifications/read-all
Headers: Authorization: Bearer {token}

1. Before: GET /api/notifications/unread → count = 5
2. PATCH /api/notifications/read-all
3. After: GET /api/notifications/unread → count = 0
```

#### Test 6.6: Delete Notification
```
Endpoint: DELETE /api/notifications/{id}
Headers: Authorization: Bearer {token}

1. Before: GET /api/notifications → count = 10
2. DELETE /api/notifications/{id}
3. After: GET /api/notifications → count = 9
4. Verify notification completely removed
```

---

## 🧪 STEP 7: Error Handling

### Test Cases

#### Test 7.1: Unauthorized Access
```
1. Submit request without auth token
2. Verify 401 response
3. Try approve as non-admin user
4. Verify 403 response with message
5. Try configure another user's request
6. Verify 403 response
```

#### Test 7.2: Invalid Data
```
1. Try submit with invalid email
2. Verify validation error
3. Try submit non-PDF file
4. Verify file type error
5. Try submit with future-dated expected_date
6. Verify date validation error
```

#### Test 7.3: Not Found Errors
```
1. Try GET /api/conference-requests/99999
2. Verify 404 response
3. Try approve non-existent request
4. Verify 404 response
5. Try configure non-existent request
6. Verify 404 response
```

#### Test 7.4: State Validation
```
1. Try approve already-approved request
2. Verify error: "Chỉ có thể duyệt yêu cầu ở trạng thái chờ duyệt"
3. Try configure non-approved request
4. Verify error: "Chỉ có thể cấu hình yêu cầu đã được duyệt"
5. Try reject already-rejected request
6. Verify error: "Chỉ có thể từ chối yêu cầu ở trạng thái chờ duyệt"
```

---

## 📊 Test Results Template

```markdown
### Test Session Report
**Date**: [Date]
**Tester**: [Name]
**Environment**: [Dev/Staging/Prod]

#### Results
- [ ] Step 1: User Form Submission - PASS/FAIL
- [ ] Step 2: Admin Review Panel - PASS/FAIL
- [ ] Step 3: Approval Endpoint - PASS/FAIL
- [ ] Step 4: Rejection Endpoint - PASS/FAIL
- [ ] Step 5: Configuration - PASS/FAIL
- [ ] Step 6: Notification API - PASS/FAIL
- [ ] Step 7: Error Handling - PASS/FAIL

#### Issues Found
1. [Issue description]
   - Severity: High/Medium/Low
   - Reproduction steps: ...
   - Expected: ...
   - Actual: ...

#### Notes
- [Any additional notes]
```

---

## 📞 Troubleshooting

### Issue: Form doesn't appear on homepage
**Solution**: 
- Verify user has `email_verified_at` set
- Clear browser cache
- Check browser console for JavaScript errors

### Issue: Email not sending
**Solution**:
- Verify `.env` mail settings
- Check mail logs in `storage/logs/`
- Test with `php artisan mail:send`
- Verify Mailtrap/email service is working

### Issue: File upload fails
**Solution**:
- Verify storage permissions: `chmod -R 755 storage/`
- Verify storage symlink created: `php artisan storage:link`
- Check file size (max 10MB)
- Verify file is PDF format

### Issue: Notification doesn't appear
**Solution**:
- Verify database migration ran: `php artisan migrate`
- Check database: `App\Models\Notification` records exist
- Verify user_id is correct
- Check API endpoint: `GET /api/notifications`

### Issue: CHAIR role not assigned
**Solution**:
- Verify `VaiTroNguoiDung` table exists
- Check approve endpoint execution
- Verify database transaction committed
- Run: `php artisan tinker` and check manually

---

**All tests completed!** ✅
Report any failures to the development team.
