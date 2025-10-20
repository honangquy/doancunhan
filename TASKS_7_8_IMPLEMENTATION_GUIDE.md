# 📋 REMAINING TASKS - IMPLEMENTATION GUIDE

**Status**: Tasks 7 & 8 (25% remaining)  
**Complexity**: Medium to High

---

## 📌 TASK 7: Conference Configuration Form for CHAIRs

### Overview
After admin approves a conference request, the CHAIR user must complete a configuration form with additional conference details before the conference becomes visible on the website.

### Database Schema
Already exists in migrations - fields are already added to `hoithao` table:
```php
$table->text('description');              // Short description
$table->string('cfp_url', 500);          // Call for Papers PDF URL
$table->text('submission_guidelines');    // Detailed submission instructions
$table->string('location', 255);          // Conference location
$table->string('contact_email', 255);     // Contact email
$table->string('contact_phone', 20);      // Contact phone
$table->string('chair_name', 255);        // Chair full name
$table->string('chair_email', 255);       // Chair email
$table->text('keywords');                 // Keywords/topics (comma-separated)
```

### Implementation Steps

#### Step 1: Create Configuration Form Modal
**File**: `resources/views/chair/conference-config.blade.php`

Features:
- Show only for CHAIR users
- Show after approval + CHAIR role assignment
- Pre-populate fields if already configured
- Modal or separate page

Fields:
1. Conference Title (read-only, from request)
2. Description (textarea, 500+ chars)
3. CFP URL (file upload or URL input for PDF)
4. Submission Guidelines (rich text or textarea)
5. Location (text input)
6. Contact Email (email field)
7. Contact Phone (tel input)
8. Chair Name (text, pre-filled from request)
9. Chair Email (email, pre-filled from request)
10. Keywords (textarea, comma-separated)

#### Step 2: Create Configuration Endpoint
**Endpoint**: `PUT /api/conferences/{id}/configure`

**Location**: `app/Http/Controllers/Api/ConferenceController.php`

**Input Validation**:
```php
[
    'description' => 'required|string|max:2000',
    'cfp_url' => 'nullable|url|max:500',
    'submission_guidelines' => 'nullable|string|max:5000',
    'location' => 'required|string|max:255',
    'contact_email' => 'required|email|max:255',
    'contact_phone' => 'nullable|string|max:20',
    'chair_name' => 'required|string|max:255',
    'chair_email' => 'required|email|max:255',
    'keywords' => 'nullable|string|max:1000',
]
```

**Processing**:
```php
1. Verify user is CHAIR of this conference
2. Validate all fields
3. Update conference record
4. Set status to 'OPEN' (make public)
5. Create notification: "Configuration complete!"
6. Return success response
```

#### Step 3: Add Menu Item for CHAIR
**File**: `resources/views/layouts/app.blade.php` or similar

Add menu item visible for CHAIR role:
```blade
@if(auth()->user()->hasRole('CHAIR'))
    <a href="{{ route('chair.my-conferences') }}">
        My Conferences
    </a>
@endif
```

#### Step 4: Create CHAIR Dashboard Page
**File**: `resources/views/chair/conferences.blade.php`

Shows:
- List of CHAIRs conferences
- Status indicators (Pending Config / Configured / Open)
- "Configure" button for pending ones
- Edit button for configured ones

---

## 📌 TASK 8: Notification System

### Overview
Implement email + in-app notifications for:
1. When conference request is approved
2. When conference request is rejected

### Database
**Model**: `app/Models/Notification.php` (already exists)

**Table Structure** (if needed):
```sql
CREATE TABLE notifications (
    id BIGINT PRIMARY KEY,
    user_id BIGINT,
    type VARCHAR(50),
    title VARCHAR(255),
    message TEXT,
    read_at TIMESTAMP NULL,
    created_at TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES nguoidung(user_id)
);
```

### Implementation Steps

#### Step 1: Update Approve Endpoint
**File**: `app/Http/Controllers/Api/ConferenceRequestController.php`

In `approve()` method, add after approval:

```php
// Send in-app notification
Notification::create([
    'user_id' => $conferenceRequest->user_id,
    'type' => 'conference_request_approved',
    'title' => 'Yêu cầu tạo hội thảo được duyệt',
    'message' => "Yêu cầu tạo hội thảo '{$conferenceRequest->title}' đã được duyệt. " .
                 "Vui lòng hoàn thành cấu hình conference để công khai.",
    'data' => ['request_id' => $conferenceRequest->request_id]
]);

// Send email notification
Mail::send('emails.conference-request-approved', [
    'user' => $conferenceRequest->requester,
    'request' => $conferenceRequest,
    'configUrl' => route('chair.conference.configure', $conferenceRequest->id)
], function($mail) use ($conferenceRequest) {
    $mail->to($conferenceRequest->requester->email)
         ->subject('Yêu cầu tạo hội thảo được duyệt');
});
```

#### Step 2: Update Reject Endpoint
**File**: `app/Http/Controllers/Api/ConferenceRequestController.php`

In `reject()` method, add after rejection:

```php
// Send in-app notification
Notification::create([
    'user_id' => $conferenceRequest->user_id,
    'type' => 'conference_request_rejected',
    'title' => 'Yêu cầu tạo hội thảo bị từ chối',
    'message' => "Yêu cầu tạo hội thảo '{$conferenceRequest->title}' đã bị từ chối. " .
                 "Lý do: {$request->reason}",
    'data' => ['request_id' => $conferenceRequest->request_id]
]);

// Send email notification
Mail::send('emails.conference-request-rejected', [
    'user' => $conferenceRequest->requester,
    'request' => $conferenceRequest,
    'reason' => $request->reason
], function($mail) use ($conferenceRequest) {
    $mail->to($conferenceRequest->requester->email)
         ->subject('Yêu cầu tạo hội thảo bị từ chối');
});
```

#### Step 3: Create Email Templates
**Files**:
- `resources/views/emails/conference-request-approved.blade.php`
- `resources/views/emails/conference-request-rejected.blade.php`

**Approved Email Template**:
```blade
<h2>Yêu cầu tạo hội thảo được duyệt!</h2>

<p>Chào {{ $user->full_name }},</p>

<p>Yêu cầu tạo hội thảo "<strong>{{ $request->title }}</strong>" của bạn đã được duyệt bởi quản trị viên.</p>

<p>Vui lòng hoàn thành cấu hình chi tiết hội thảo để công khai trên website:</p>

<p style="text-align: center; margin: 30px 0;">
    <a href="{{ $configUrl }}" style="background-color: #3B82F6; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px;">
        Cấu hình hội thảo
    </a>
</p>

<p>Nếu bạn cần hỗ trợ, vui lòng liên hệ với chúng tôi.</p>

<p>Trân trọng,<br>Quản trị viên Hệ thống</p>
```

**Rejected Email Template**:
```blade
<h2>Yêu cầu tạo hội thảo bị từ chối</h2>

<p>Chào {{ $user->full_name }},</p>

<p>Xin thông báo rằng yêu cầu tạo hội thảo "<strong>{{ $request->title }}</strong>" của bạn đã bị từ chối.</p>

<p><strong>Lý do:</strong> {{ $reason }}</p>

<p>Nếu bạn cần thêm thông tin hoặc muốn gửi lại yêu cầu, vui lòng liên hệ với quản trị viên.</p>

<p>Trân trọng,<br>Quản trị viên Hệ thống</p>
```

#### Step 4: Create Notification API Endpoints
**File**: `routes/api.php`

Add routes (if not exists):
```php
Route::middleware('auth:api')->group(function () {
    Route::get('notifications', [NotificationController::class, 'index']);
    Route::get('notifications/{id}', [NotificationController::class, 'show']);
    Route::patch('notifications/{id}/read', [NotificationController::class, 'markAsRead']);
    Route::patch('notifications/read-all', [NotificationController::class, 'markAllAsRead']);
});
```

#### Step 5: Update Home Page Notification Display
**File**: `resources/views/home.blade.php`

Already has notification dropdown - just ensure it loads from API:
```javascript
async loadNotifications() {
    const response = await fetch('/api/notifications');
    const data = await response.json();
    this.notifications = data.notifications;
    this.unreadCount = data.unreadCount;
}
```

---

## 🎯 IMPLEMENTATION ORDER

1. **Create Email Templates** (5 min)
2. **Add Notification Creation Logic** (10 min)
3. **Create Conference Config Endpoint** (20 min)
4. **Create Configuration Form Modal** (25 min)
5. **Create CHAIR Dashboard** (20 min)
6. **Testing** (30 min)

**Total Estimated Time**: ~1.5 hours

---

## ✅ COMPLETION CHECKLIST

### Task 7
- [ ] Add route for configure form
- [ ] Create modal/page form
- [ ] Implement PUT /api/conferences/{id}/configure
- [ ] Add validation
- [ ] Update conference status to OPEN
- [ ] Test configuration workflow

### Task 8
- [ ] Create email templates
- [ ] Add notification creation in approve()
- [ ] Add notification creation in reject()
- [ ] Create notification API endpoints
- [ ] Send test emails
- [ ] Test in-app notifications

---

## 📞 SUPPORT NEEDED

Let me know when you're ready to implement tasks 7 & 8, and I can:
1. Create all the controller methods
2. Design the configuration form
3. Set up email templates
4. Implement notification endpoints
5. Test the complete workflow

---

**Status**: Ready for Phase 2 Implementation  
**Blockers**: None  
**Tests**: Can begin as soon as Phase 1 is deployed
