# 🚀 PHASE 8.12 - COI VIEWS CONVERSION TO DASHBOARD STRUCTURE

**Start Date:** October 6, 2025  
**Status:** 🔄 IN PROGRESS  
**Priority:** ⭐⭐⭐⭐ URGENT  
**Progress:** 1/6 views complete (17%)

---

## 🎯 OBJECTIVE

Convert all COI views from layout-based structure to standalone HTML matching `chair/dashboard.blade.php` pattern.

**Why?** User wants all COI pages integrated into main dashboard with top navbar + sidebar visible.

---

## ✅ COMPLETED (1/6)

### 1. `chair/coi/index.blade.php` ✅
- **Status:** DONE
- **Structure:** Standalone HTML with dashboard layout
- **Features:** Orange top navbar, white sidebar, COI menu highlighted
- **Size:** 350+ lines
- **Cache:** Cleared

---

## ⏳ PENDING (5/6)

### 2. `chair/coi/show.blade.php` ⏳ NEXT
**Current:** Standalone but missing navbar + sidebar  
**Need:** Add dashboard structure  
**Est. Time:** 30 minutes  

**Steps:**
1. Read current file structure
2. Add HTML boilerplate (DOCTYPE, head, body)
3. Add top navbar (orange gradient, HUIT branding)
4. Add sidebar menu (mark "Kiểm tra COI" active)
5. Wrap content in `<main>` tag
6. Add proper closing tags
7. Clear cache

---

### 3. `chair/coi/resolve.blade.php` ⏳
**Current:** Standalone but missing navbar + sidebar  
**Need:** Add dashboard structure  
**Est. Time:** 30 minutes  

**Steps:** Same as show.blade.php

---

### 4. `reviewer/coi/index.blade.php` ⏳
**Current:** Standalone but missing navbar + sidebar  
**Need:** Add reviewer dashboard structure (PURPLE theme!)  
**Est. Time:** 35 minutes  

**Steps:**
1. Read `resources/views/reviewer/dashboard.blade.php` structure
2. Copy purple theme navbar
3. Add reviewer sidebar menu
4. Mark "Kiểm tra COI" active
5. Wrap content
6. Clear cache

**Note:** Reviewer theme is PURPLE (purple-800), not orange!

---

### 5. `reviewer/coi/create.blade.php` ⏳
**Current:** Standalone but missing navbar + sidebar  
**Need:** Add reviewer dashboard structure  
**Est. Time:** 35 minutes  

**Steps:** Same as reviewer index, purple theme

---

### 6. `reviewer/coi/show.blade.php` ⏳
**Current:** Standalone but missing navbar + sidebar  
**Need:** Add reviewer dashboard structure  
**Est. Time:** 30 minutes  

**Steps:** Same as reviewer index, purple theme

---

## 📊 TIME ESTIMATE

| Task | Time |
|------|------|
| chair/coi/show | 30 min |
| chair/coi/resolve | 30 min |
| reviewer/coi/index | 35 min |
| reviewer/coi/create | 35 min |
| reviewer/coi/show | 30 min |
| Testing all views | 20 min |
| Documentation | 10 min |
| **TOTAL** | **3h 10min** |

---

## 🎨 TEMPLATE STRUCTURE

### Chair Views Template

```blade
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $pageTitle }} - HUIT Conferences</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Top Navbar: Orange gradient -->
    <nav class="bg-gradient-to-r from-orange-800 via-orange-700 to-orange-600 text-white shadow-lg">
        <div class="px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo -->
                <div class="flex items-center space-x-3">
                    <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2L2 7v10c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V7l-10-5z"/>
                    </svg>
                    <span class="text-xl font-bold">HUIT Conferences</span>
                </div>
                
                <!-- User menu -->
                <div class="flex items-center space-x-4">
                    <span class="text-sm">{{ Auth::user()->full_name }}</span>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="text-sm hover:text-orange-200">Đăng xuất</button>
                    </form>
                </div>
            </div>
        </div>
    </nav>
    
    <div class="flex">
        <!-- Sidebar: White -->
        <aside class="w-64 bg-white shadow-lg min-h-screen sticky top-16">
            <nav class="p-4 space-y-2">
                <a href="{{ route('chair.dashboard') }}" 
                   class="w-full flex items-center space-x-3 px-4 py-3 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                    <span>Dashboard</span>
                </a>
                
                <!-- More menu items... -->
                
                <a href="{{ route('chair.coi.index') }}" 
                   class="w-full flex items-center space-x-3 px-4 py-3 rounded-lg bg-orange-50 text-orange-700 font-medium transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                    <span>Kiểm tra COI</span>
                </a>
            </nav>
        </aside>
        
        <!-- Main Content -->
        <main class="flex-1 p-6 lg:p-8">
            <!-- Page content here -->
        </main>
    </div>
</body>
</html>
```

### Reviewer Views Template (Purple theme)

Same structure but:
- Navbar: `bg-gradient-to-r from-purple-800 via-purple-700 to-purple-600`
- Active menu: `bg-purple-50 text-purple-700`
- Routes: `reviewer.coi.index`, etc.

---

## 🧪 TESTING CHECKLIST

After each conversion:
- [ ] Top navbar appears (correct color: orange/purple)
- [ ] Sidebar appears with all menus
- [ ] "Kiểm tra COI" menu highlighted
- [ ] User name shows in navbar
- [ ] Logout button works
- [ ] Content displays correctly
- [ ] No layout issues
- [ ] No console errors
- [ ] Links work properly
- [ ] Forms submit correctly

---

## 🚀 IMPLEMENTATION ORDER

1. ✅ chair/coi/index.blade.php - DONE
2. ⏳ chair/coi/show.blade.php - NEXT (30 min)
3. ⏳ chair/coi/resolve.blade.php (30 min)
4. ⏳ reviewer/coi/index.blade.php (35 min)
5. ⏳ reviewer/coi/create.blade.php (35 min)
6. ⏳ reviewer/coi/show.blade.php (30 min)
7. ⏳ Clear all caches (2 min)
8. ⏳ Test all 6 views (20 min)
9. ⏳ Document completion (10 min)

---

**Ready to continue? Reply "ok" to convert next view (chair/coi/show.blade.php)** 🚀
