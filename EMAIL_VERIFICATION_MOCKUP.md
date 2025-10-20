# Visual Mockup - Email Verification in Edit User Modal

## BEFORE vs AFTER Comparison

### BEFORE (Original Edit Modal):
```
┌─────────────────────────────────────────────────────────────┐
│                    Chỉnh sửa người dùng                    │
├─────────────────────────────────────────────────────────────┤
│                                                             │
│  👤 Họ tên      [Nguyen Van A.....................]        │
│                                                             │
│  ✉️ Email       [user@example.com.................]        │
│                                                             │
│  🔒 Mật khẩu mới [.................................]        │
│                 Để trống nếu không muốn thay đổi mật khẩu   │
│                                                             │
│  ✓ Vai trò      [REVIEWER ▼]                               │
│                                                             │
│                                     [Hủy]  [Cập nhật]     │
└─────────────────────────────────────────────────────────────┘
```

### AFTER (With Email Verification Feature):
```
┌─────────────────────────────────────────────────────────────┐
│                    Chỉnh sửa người dùng                    │
├─────────────────────────────────────────────────────────────┤
│                                                             │
│  👤 Họ tên      [Nguyen Van A.....................]        │
│                                                             │
│  ✉️ Email       [user@example.com.................]        │
│  ┌───────────────────────────────────────────────────────┐  │
│  │ ✅ Email đã được xác thực        [Hủy xác thực]    │  │
│  │ Xác thực lúc: 20/10/2025 14:30:25                  │  │
│  └───────────────────────────────────────────────────────┘  │
│                                                             │
│  🔒 Mật khẩu mới [.................................]        │
│                 Để trống nếu không muốn thay đổi mật khẩu   │
│                                                             │
│  ✓ Vai trò      [REVIEWER ▼]                               │
│                                                             │
│                                     [Hủy]  [Cập nhật]     │
└─────────────────────────────────────────────────────────────┘
```

---

## State Variations

### 1. VERIFIED STATE:
```
┌───────────────────────────────────────────────────────────┐
│ ✅ Email đã được xác thực             [Hủy xác thực]    │ <- Green theme
│ Xác thực lúc: 20/10/2025 14:30:25                       │
└───────────────────────────────────────────────────────────┘
```

**Colors:**
- ✅ Icon: Green (#22C55E)
- Text: Dark green (#15803D)
- Button: Red background (#FEE2E2) with red text (#B91C1C)

### 2. UNVERIFIED STATE:
```
┌───────────────────────────────────────────────────────────┐
│ ⚠️ Email chưa được xác thực          [Xác thực email]   │ <- Orange theme
│ Chưa được xác thực                                       │
└───────────────────────────────────────────────────────────┘
```

**Colors:**
- ⚠️ Icon: Orange (#F97316)
- Text: Dark orange (#C2410C)
- Button: Green background (#DCFCE7) with green text (#15803D)

---

## Interactive Behavior

### Workflow 1: Verify Email
```
STEP 1: User sees unverified state
┌─────────────────────────────────────────────────────┐
│ ⚠️ Email chưa được xác thực    [Xác thực email]   │
└─────────────────────────────────────────────────────┘

STEP 2: User clicks "Xác thực email"
       ↓
   [Confirm Dialog]
   "Bạn có chắc muốn xác thực email của người dùng này?"
   [Hủy]  [OK]

STEP 3: User clicks OK
       ↓
   [Loading/API Call]

STEP 4: Success - Realtime update (no page reload needed)
┌─────────────────────────────────────────────────────┐
│ ✅ Email đã được xác thực      [Hủy xác thực]     │
│ Xác thực lúc: 20/10/2025 14:32:15                 │
└─────────────────────────────────────────────────────┘
   +
   [Success Notification]
   "✅ Email đã được xác thực thành công!"

STEP 5: Page auto-reload after 1.5s (to sync table data)
```

### Workflow 2: Unverify Email
```
STEP 1: User sees verified state
┌─────────────────────────────────────────────────────┐
│ ✅ Email đã được xác thực      [Hủy xác thực]     │
│ Xác thực lúc: 19/10/2025 10:15:30                 │
└─────────────────────────────────────────────────────┘

STEP 2: User clicks "Hủy xác thực"
       ↓
   [Confirm Dialog]
   "Bạn có chắc muốn hủy xác thực email của người dùng này?"
   [Hủy]  [OK]

STEP 3: Success - Realtime update
┌─────────────────────────────────────────────────────┐
│ ⚠️ Email chưa được xác thực    [Xác thực email]   │
│ Chưa được xác thực                                 │
└─────────────────────────────────────────────────────┘
   +
   [Success Notification]
   "✅ Đã hủy xác thực email!"
```

---

## CSS Classes Used

### Container:
```css
.mt-2          /* margin-top: 0.5rem */
.p-3           /* padding: 0.75rem */
.border        /* border-width: 1px */
.border-gray-200 /* border-color: #E5E7EB */
.rounded-lg    /* border-radius: 0.5rem */
.bg-gray-50    /* background-color: #F9FAFB */
```

### Layout:
```css
.flex                /* display: flex */
.items-center        /* align-items: center */
.justify-between     /* justify-content: space-between */
.space-x-2           /* gap between children: 0.5rem */
```

### Icon States:
```css
/* Verified */
.w-4 .h-4 .text-green-500    /* 16px, green */

/* Unverified */
.w-4 .h-4 .text-orange-500   /* 16px, orange */
```

### Text States:
```css
/* Verified */
.text-sm .font-medium .text-green-700

/* Unverified */
.text-sm .font-medium .text-orange-700
```

### Button States:
```css
/* Verify Button */
.px-3 .py-1.5 .text-xs .font-medium 
.text-green-700 .bg-green-100 .border-green-200
.rounded-md .hover:bg-green-200

/* Unverify Button */
.px-3 .py-1.5 .text-xs .font-medium
.text-red-700 .bg-red-100 .border-red-200  
.rounded-md .hover:bg-red-200
```

---

## JavaScript Functions Overview

```javascript
// 1. Main update function
updateEmailVerificationStatus(emailVerifiedAt, userId)
├── Updates icon SVG and color
├── Updates status text and color  
├── Updates date/time display
├── Updates button text and styling
└── Stores user ID for actions

// 2. Action handler
handleEmailVerification()
├── Gets user ID from button data
├── Gets action type (verify/unverify)
└── Calls appropriate function

// 3. Enhanced existing functions
verifyEmail(userId)
├── Shows confirmation dialog
├── Makes API call
├── Updates modal realtime (NEW)
└── Shows success message

unverifyEmail(userId)  
├── Shows confirmation dialog
├── Makes API call  
├── Updates modal realtime (NEW)
└── Shows success message

// 4. Integration
editUser(userId)
├── Fetches user data
├── Populates form fields
├── Calls updateEmailVerificationStatus() (NEW)
└── Opens modal
```

---

## Real-world Usage Example

**Scenario**: Admin managing conference reviewers

1. Admin opens "Quản lý người dùng"
2. Sees list of users, some with ❌ (unverified) in email column
3. Clicks "Chỉnh sửa" on user "Dr. Nguyen Van A"
4. Modal opens, shows:
   - Name: Dr. Nguyen Van A
   - Email: nguyenvana@university.edu.vn
   - ⚠️ **Email chưa được xác thực** [Xác thực email]
   - Role: REVIEWER
5. Admin clicks "Xác thực email"
6. Confirms action
7. Status immediately changes to:
   - ✅ **Email đã được xác thực** [Hủy xác thực]
   - Xác thực lúc: 20/10/2025 14:35:42
8. Success message appears
9. Page reloads → user list now shows ✅ for this user

**Result**: Reviewer can now receive email notifications for paper assignments.

---

**Implementation Status**: ✅ COMPLETE  
**Testing Status**: Ready for QA  
**Documentation**: Complete