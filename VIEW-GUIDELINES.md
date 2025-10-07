# 📘 VIEW DEVELOPMENT GUIDELINES - Blade Templates

**Document:** VIEW-GUIDELINES.md  
**Created:** January 5, 2025  
**Purpose:** Standard practices for creating Blade views in HUIT Conference System

---

## ⚠️ CRITICAL RULE #1: ALWAYS USE LAYOUTS

**❌ NEVER DO THIS:**
```blade
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <title>My Page</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>
    <!-- Custom navbar -->
    <nav>...</nav>
    
    <!-- Page content -->
    <div>...</div>
</body>
</html>
```

**✅ ALWAYS DO THIS:**
```blade
@extends('layouts.chair')

@section('title', 'My Page Title')

@section('content')
    <!-- Page content only -->
    <div>...</div>
@endsection
```

---

## 🎯 WHY USE LAYOUTS?

### Problems with Standalone HTML:
1. ❌ **Duplicate navbar** - Each page has own navigation
2. ❌ **Inconsistent UI** - Different styles across pages
3. ❌ **Hard to maintain** - Changes require editing all files
4. ❌ **No sidebar integration** - Standalone pages don't fit dashboard
5. ❌ **Route conflicts** - Custom navbars may reference wrong routes
6. ❌ **Styling issues** - Mixing Tailwind configs causes conflicts

### Benefits of Layouts:
1. ✅ **Single source of truth** - One navbar for entire app
2. ✅ **Consistent experience** - Same look & feel everywhere
3. ✅ **Easy updates** - Edit layout once, affects all pages
4. ✅ **Dashboard integration** - Sidebar, header, footer included
5. ✅ **Correct routing** - Layout knows all available routes
6. ✅ **Clean code** - Views focus on content, not structure

---

## 📂 AVAILABLE LAYOUTS

### 1. Chair Layout
**File:** `resources/views/layouts/chair.blade.php`  
**Use for:** All Chair/Admin pages

**Features:**
- Orange theme (`from-orange-600 to-orange-700`)
- Sidebar with Chair menu items
- Dashboard, Papers, Reviewers, COI, Settings
- Top header with user info
- Responsive design

**Example:**
```blade
@extends('layouts.chair')

@section('title', 'COI Management')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-2xl font-bold">Manage COI Cases</h1>
        <!-- Your content here -->
    </div>
@endsection
```

---

### 2. Reviewer Layout
**File:** `resources/views/layouts/reviewer.blade.php`  
**Use for:** All Reviewer pages

**Features:**
- Purple theme (`from-purple-800 to-purple-600`)
- Top navigation bar
- Dashboard, Assignments, Reviews, COI, Profile
- Notification bell
- Responsive design

**Example:**
```blade
@extends('layouts.reviewer')

@section('title', 'My Reviews')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-2xl font-bold">Assigned Papers</h1>
        <!-- Your content here -->
    </div>
@endsection
```

---

## 🛠️ LAYOUT STRUCTURE

### Required Sections:

```blade
@extends('layouts.chair')  <!-- or 'layouts.reviewer' -->

@section('title', 'Page Title')  <!-- Browser tab title -->

@section('content')
    <!-- ALL your page content goes here -->
    <!-- No need for <html>, <body>, <nav> tags -->
@endsection

@push('styles')  <!-- Optional: Additional CSS -->
    <style>
        .custom-class { color: red; }
    </style>
@endpush

@push('scripts')  <!-- Optional: Additional JS -->
    <script>
        console.log('Page loaded');
    </script>
@endpush
```

---

## 📋 STEP-BY-STEP: Creating a New View

### Step 1: Choose Correct Layout
- Chair pages → `@extends('layouts.chair')`
- Reviewer pages → `@extends('layouts.reviewer')`

### Step 2: Define Page Title
```blade
@section('title', 'Conflict of Interest Management')
```

### Step 3: Write Content Section
```blade
@section('content')
    <!-- Page Header -->
    <div class="bg-white border-b shadow-sm">
        <div class="container mx-auto px-4 py-6">
            <h1 class="text-2xl font-bold text-gray-800">Page Title</h1>
            <p class="text-gray-600 mt-1">Page description</p>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container mx-auto px-4 py-8">
        <!-- Your content here -->
    </div>
@endsection
```

### Step 4: Add Optional Sections (if needed)
```blade
@push('styles')
    <!-- Custom CSS for this page only -->
@endpush

@push('scripts')
    <!-- Custom JS for this page only -->
@endpush
```

---

## ✅ CHECKLIST: Before Creating a View

- [ ] Identified correct layout (chair or reviewer)
- [ ] Used `@extends()` instead of full HTML
- [ ] Set page title with `@section('title')`
- [ ] Wrapped content in `@section('content')`
- [ ] No `<html>`, `<body>`, `<nav>` tags in view
- [ ] No duplicate navbar code
- [ ] Tested in browser with layout navigation

---

## 🚫 COMMON MISTAKES TO AVOID

### Mistake 1: Creating Standalone HTML
```blade
<!-- ❌ WRONG -->
<!DOCTYPE html>
<html>
<head>...</head>
<body>
    <nav>Custom navbar</nav>
    <div>Content</div>
</body>
</html>
```

**Fix:** Use layout instead!

---

### Mistake 2: Mixing Layout with HTML
```blade
<!-- ❌ WRONG -->
<!DOCTYPE html>
<html>
@extends('layouts.chair')  <!-- Can't mix! -->
<body>
    @section('content')...
```

**Fix:** Choose one - either full HTML OR layout, never both!

---

### Mistake 3: Custom Navbar in Layout View
```blade
<!-- ❌ WRONG -->
@extends('layouts.chair')

@section('content')
    <nav class="bg-orange-600">  <!-- Don't add navbar! -->
        <a href="/dashboard">Dashboard</a>
    </nav>
    <div>Content</div>
@endsection
```

**Fix:** Layout already has navbar - just add content!

---

### Mistake 4: Wrong Layout for User Type
```blade
<!-- ❌ WRONG - Reviewer page using Chair layout -->
@extends('layouts.chair')  <!-- Reviewer should use 'layouts.reviewer' -->

@section('content')
    <h1>Reviewer Dashboard</h1>
@endsection
```

**Fix:** Match layout to user role!

---

## 🔍 DEBUGGING: How to Check if View Uses Layout

### Good Signs (Using Layout):
✅ No `<!DOCTYPE html>` at start of file  
✅ First line is `@extends('layouts.something')`  
✅ Has `@section('content')` wrapping main content  
✅ No `<nav>` tag in view file  
✅ File is small (~50-100 lines for complex pages)

### Bad Signs (Standalone HTML):
❌ Starts with `<!DOCTYPE html>`  
❌ Has `<head>`, `<body>` tags  
❌ Contains `<nav>` with dashboard links  
❌ File is large (200+ lines)  
❌ Duplicate Tailwind CDN links

---

## 📊 EXAMPLE: Converting Standalone to Layout

### Before (Standalone HTML - 200+ lines):
```blade
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <title>COI Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        * { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-gradient-to-r from-orange-600 to-orange-500 text-white shadow-lg">
        <div class="container mx-auto px-4">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center">
                        <span class="text-orange-600 font-bold text-xl">C</span>
                    </div>
                    <div>
                        <div class="font-bold text-lg">Chair Dashboard</div>
                        <div class="text-xs text-orange-100">COI Management</div>
                    </div>
                </div>
                <div class="flex items-center space-x-6">
                    <a href="{{ route('chair.dashboard') }}">Dashboard</a>
                    <a href="{{ route('chair.coi.index') }}">COI</a>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit">Logout</button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <!-- Page Header -->
    <div class="bg-white border-b shadow-sm">
        <div class="container mx-auto px-4 py-6">
            <h1 class="text-2xl font-bold text-gray-800">COI Management</h1>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container mx-auto px-4 py-8">
        <p>Content here...</p>
    </div>
</body>
</html>
```

### After (Using Layout - 20 lines):
```blade
@extends('layouts.chair')

@section('title', 'COI Management')

@section('content')
    <!-- Page Header -->
    <div class="bg-white border-b shadow-sm">
        <div class="container mx-auto px-4 py-6">
            <h1 class="text-2xl font-bold text-gray-800">COI Management</h1>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container mx-auto px-4 py-8">
        <p>Content here...</p>
    </div>
@endsection
```

**Savings:** 180 lines removed, 90% smaller file!

---

## 🎨 STYLING GUIDELINES

### Use Layout's Tailwind Config
Layout already includes Tailwind CDN - **don't add it again!**

### Consistent Color Themes:
- **Chair pages:** Orange (`orange-600`, `orange-700`)
- **Reviewer pages:** Purple (`purple-600`, `purple-700`)
- **Shared elements:** Gray (`gray-50`, `gray-100` backgrounds)

### Typography:
- **Headings:** `text-2xl font-bold text-gray-800`
- **Subheadings:** `text-lg font-semibold text-gray-700`
- **Body text:** `text-gray-600`
- **Buttons:** Layout provides button classes

---

## 🧪 TESTING CHECKLIST

After creating a view, verify:

- [ ] Page loads without errors
- [ ] Sidebar/navbar appears (from layout)
- [ ] Active menu item highlights correctly
- [ ] Logout button works
- [ ] No duplicate navigation bars
- [ ] Tailwind styles apply correctly
- [ ] Responsive on mobile devices
- [ ] Breadcrumbs work (if applicable)
- [ ] User name displays in header
- [ ] Theme colors match role (orange/purple)

---

## 📚 REFERENCE: All Layout Views

### Chair Layout Views:
```
resources/views/
  chair/
    dashboard.blade.php           ✅ Uses layout
    dashboard_old.blade.php       ✅ Uses layout
    dashboard_fixed.blade.php     ✅ Uses layout
    coi/
      index.blade.php             ❌ Needs conversion
      show.blade.php              ❌ Needs conversion
      resolve.blade.php           ❌ Needs conversion
```

### Reviewer Layout Views:
```
resources/views/
  reviewer/
    dashboard.blade.php           ✅ Uses layout
    assignments.blade.php         ✅ Uses layout
    reviews/
      index.blade.php             ✅ Uses layout
      show.blade.php              ✅ Uses layout
    coi/
      index.blade.php             ❌ Needs conversion
      create.blade.php            ❌ Needs conversion
      show.blade.php              ❌ Needs conversion
```

---

## 🚀 QUICK REFERENCE TEMPLATE

**Copy-paste this as starting point:**

```blade
@extends('layouts.chair')  {{-- or layouts.reviewer --}}

@section('title', 'Your Page Title')

@section('content')
    {{-- Page Header --}}
    <div class="bg-white border-b shadow-sm">
        <div class="container mx-auto px-4 py-6">
            <h1 class="text-2xl font-bold text-gray-800">Page Heading</h1>
            <p class="text-gray-600 mt-1">Page description</p>
        </div>
    </div>

    {{-- Main Content --}}
    <div class="container mx-auto px-4 py-8">
        <div class="bg-white rounded-lg shadow p-6">
            {{-- Your content here --}}
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Optional: Page-specific JavaScript
    </script>
@endpush
```

---

## ⚡ MIGRATION GUIDE: Converting Existing Views

### Step 1: Backup Original
```bash
copy view.blade.php view.blade.php.backup
```

### Step 2: Remove Top Section
Delete everything from start until `<body>` tag (inclusive):
- `<!DOCTYPE html>`
- `<html>`
- `<head>` and all contents
- `<body>` opening tag

### Step 3: Remove Navigation
Delete entire `<nav>` block (usually 20-30 lines)

### Step 4: Remove Bottom Section
Delete closing tags at end:
- `</body>`
- `</html>`

### Step 5: Add Layout Extend
Add at very top:
```blade
@extends('layouts.chair')  {{-- or reviewer --}}

@section('title', 'Page Title')
```

### Step 6: Wrap Content
Wrap remaining content:
```blade
@section('content')
    {{-- All existing content goes here --}}
@endsection
```

### Step 7: Test
- Clear cache: `php artisan cache:clear`
- Load page in browser
- Verify layout appears correctly

---

## 📞 SUPPORT

**If you encounter issues:**

1. Check this document first
2. Look at working examples (reviewer/assignments.blade.php)
3. Verify layout file exists
4. Clear cache and refresh browser
5. Check console for JavaScript errors

**Remember:** When in doubt, use layouts! 🎯

---

*Document created: January 5, 2025*  
*Last updated: January 5, 2025*  
*Version: 1.0*
