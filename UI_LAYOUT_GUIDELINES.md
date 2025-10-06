# UI/UX Layout Guidelines - HUIT Conference System

## 📋 **LAYOUT REQUIREMENT - CỰC KỲ QUAN TRỌNG**

### ⚠️ **QUY TẮC BẮT BUỘC CHO TẤT CẢ TRANG**

**YÊU CẦU**: Tất cả các trang trong hệ thống PHẢI sử dụng layout **2 cột** (Sidebar + Content Area) như trang Dashboard.

### 📐 **Cấu trúc layout chuẩn:**

```
┌─────────────────────────────────────────────────────┐
│ [Sidebar]         │   [Top Bar - Header]            │
│                   ├─────────────────────────────────┤
│ - Logo            │                                  │
│ - Navigation      │   [Main Content Area]           │
│ - User Info       │   (Scrollable)                  │
│                   │                                  │
│                   │                                  │
└───────────────────┴──────────────────────────────────┘
```

## 🎨 **Implementation cho từng Role**

### 1️⃣ **CHAIR** (Đã hoàn thành ✅)

**Layout file**: `resources/views/layouts/chair.blade.php`

**Đặc điểm:**
- **Màu chủ đạo**: Orange (#ea580c, #f97316)
- **Sidebar**: Fixed width 256px, gradient orange
- **Navigation menu**: Dashboard, Papers, Reviewers, Assignments, COI, Reports
- **Active state**: bg-orange-500, font-semibold

**Cách sử dụng:**
```blade
@extends('layouts.chair')

@section('title', 'Page Title')
@section('page-title', 'Page Header')
@section('page-subtitle', 'Optional subtitle')

@section('content')
    <!-- Your content here -->
@endsection
```

**Các trang đã áp dụng:**
- ✅ `chair/dashboard.blade.php`
- ✅ `chair/papers/index.blade.php`
- ⏸️ `chair/papers/show.blade.php` (cần tạo)
- ⏸️ `chair/reviewers/index.blade.php` (cần tạo)

---

### 2️⃣ **REVIEWER** (Chưa triển khai ⏸️)

**Layout file cần tạo**: `resources/views/layouts/reviewer.blade.php`

**Đặc điểm:**
- **Màu chủ đạo**: Purple/Violet (#7c3aed, #8b5cf6)
- **Sidebar**: Fixed width 256px, gradient purple
- **Navigation menu**: 
  - Dashboard
  - My Assignments (Phân công của tôi)
  - Papers to Review (Bài cần phản biện)
  - Completed Reviews (Đã hoàn thành)
  - Profile & Expertise (Hồ sơ & chuyên môn)

**Route prefix**: `/reviewer/*`

---

### 3️⃣ **AUTHOR** (Chưa triển khai ⏸️)

**Layout file cần tạo**: `resources/views/layouts/author.blade.php`

**Đặc điểm:**
- **Màu chủ đạo**: Blue (#2563eb, #3b82f6)
- **Sidebar**: Fixed width 256px, gradient blue
- **Navigation menu**: 
  - Dashboard
  - My Papers (Bài báo của tôi)
  - Submit New Paper (Nộp bài mới)
  - Reviews & Feedback (Phản biện & phản hồi)
  - Conferences (Hội thảo)

**Route prefix**: `/author/*`

---

### 4️⃣ **ADMIN** (Chưa triển khai ⏸️)

**Layout file cần tạo**: `resources/views/layouts/admin.blade.php`

**Đặc điểm:**
- **Màu chủ đạo**: Gray/Slate (#334155, #475569)
- **Sidebar**: Fixed width 256px, gradient gray/dark
- **Navigation menu**: 
  - Dashboard
  - Conferences Management (Quản lý hội thảo)
  - Users Management (Quản lý người dùng)
  - Roles & Permissions (Vai trò & phân quyền)
  - System Settings (Cài đặt hệ thống)
  - Reports & Analytics (Báo cáo & thống kê)

**Route prefix**: `/admin/*`

---

## 🔧 **Các Component chung trong Layout**

### Sidebar Components:

1. **Logo Section** (64px height, border-bottom)
   - Logo icon + app name
   - Role label

2. **Navigation Section** (flex-1, overflow-y-auto)
   - Menu items với icon + text
   - Active state highlighting
   - Hover effects

3. **User Info Section** (border-top)
   - Avatar circle với initial
   - Username + email
   - Logout button

### Top Bar Components:

1. **Page Header**
   - Page title (text-2xl, font-bold)
   - Subtitle/breadcrumb (text-sm, text-gray-600)

2. **Right Section**
   - Notification bell (với red dot indicator)
   - User avatar + name (duplicate for UX)

---

## 📝 **Checklist khi tạo trang mới**

### Bước 1: Xác định Role
- [ ] Chair / Reviewer / Author / Admin?

### Bước 2: Check Layout đã có chưa
- [ ] File `layouts/{role}.blade.php` đã tồn tại?
- [ ] Nếu chưa → tạo dựa trên `layouts/chair.blade.php` làm template

### Bước 3: Tạo View với @extends
```blade
@extends('layouts.{role}')

@section('title', 'Page Title')
@section('page-title', 'Display Title')
@section('page-subtitle', 'Optional subtitle')

@push('styles')
    <!-- Custom CSS if needed -->
@endpush

@section('content')
    <!-- Main content here -->
    <!-- NO sidebar, NO top bar needed -->
@endsection

@push('scripts')
    <!-- Custom JS if needed -->
@endpush
```

### Bước 4: Verify Navigation
- [ ] Active menu item được highlight đúng?
- [ ] Route name trong layout khớp với web.php?

---

## ⚡ **Best Practices**

### 1. **Consistency (Nhất quán)**
- Tất cả trang cùng role phải dùng CÙNG MỘT layout
- KHÔNG tạo duplicate sidebar/topbar trong view

### 2. **Responsive**
- Sidebar ẩn trên mobile (hidden md:flex)
- Content area full-width khi sidebar collapse

### 3. **Performance**
- CDN cho Tailwind CSS
- Alpine.js cho interactive elements
- Lazy load large tables

### 4. **Accessibility**
- Semantic HTML
- Proper ARIA labels
- Keyboard navigation support

---

## 🚨 **NHỮNG LỖI THƯỜNG GẶP - TRÁNH**

### ❌ **SAI:**
```blade
<!-- File: chair/papers/index.blade.php -->
<!DOCTYPE html>
<html>
<head>...</head>
<body>
    <!-- Duplicate sidebar here ❌ -->
    <aside>...</aside>
    <!-- Duplicate topbar here ❌ -->
    <nav>...</nav>
    <main>Content</main>
</body>
</html>
```

### ✅ **ĐÚNG:**
```blade
<!-- File: chair/papers/index.blade.php -->
@extends('layouts.chair')

@section('content')
    <!-- Only content here ✅ -->
    <div>Content</div>
@endsection
```

---

## 📚 **Files liên quan**

### Layout Templates:
- `resources/views/layouts/chair.blade.php` ✅
- `resources/views/layouts/reviewer.blade.php` ⏸️
- `resources/views/layouts/author.blade.php` ⏸️
- `resources/views/layouts/admin.blade.php` ⏸️

### Routes:
- `routes/web.php` - Route definitions
- Sử dụng `Route::name()` để hỗ trợ active state trong menu

### CSS Framework:
- Tailwind CSS 3.x (CDN)
- Custom classes defined in layout `<script>` section

---

## 🎯 **Next Steps (Tiếp theo cần làm)**

1. **Tạo Reviewer Layout** 
   - Copy `layouts/chair.blade.php`
   - Đổi màu orange → purple
   - Update menu items cho reviewer
   - Update route names

2. **Tạo Author Layout**
   - Copy `layouts/chair.blade.php`
   - Đổi màu orange → blue
   - Update menu items cho author
   - Update route names

3. **Tạo Admin Layout**
   - Copy `layouts/chair.blade.php`
   - Đổi màu orange → gray/slate
   - Update menu items cho admin
   - Update route names

4. **Update tất cả views hiện tại**
   - Scan folder `resources/views/`
   - Convert HTML standalone → @extends layout
   - Test từng trang

---

## 📞 **Support**

Nếu gặp vấn đề khi implement layout:

1. Check layout file đã tồn tại chưa
2. Verify route names trong `web.php`
3. Inspect với DevTools xem có conflict CSS không
4. Test responsive trên mobile/tablet

---

**Ghi chú**: Document này được tạo ngày **05/10/2025** sau khi hoàn thành Chair Dashboard + Papers List. Cập nhật mỗi khi có thay đổi layout.
