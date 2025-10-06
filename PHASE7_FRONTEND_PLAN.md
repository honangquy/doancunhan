# 🎨 PHASE 7 - FRONTEND DEVELOPMENT PLAN

**Technology**: Laravel Blade + Tailwind CSS + Alpine.js  
**Duration**: ~60 hours (12 sub-phases)  
**Target**: Complete responsive web interface

---

## 🎯 OVERVIEW

### Technology Stack:
- **Framework**: Laravel 10.x (Blade Templates)
- **CSS**: Tailwind CSS 3.x
- **JavaScript**: Alpine.js (lightweight reactivity)
- **Icons**: Heroicons (SVG icons)
- **HTTP Client**: Axios (API calls)
- **Charts**: Chart.js (statistics)

### Why This Stack?
✅ **Laravel Blade**: Already integrated, no need separate frontend  
✅ **Tailwind CSS**: Utility-first, fast development, responsive  
✅ **Alpine.js**: Lightweight (15KB), Vue-like syntax, perfect for Blade  
✅ **Native to Laravel**: No build complexity like React/Vue  

---

## 📊 DEVELOPMENT PHASES (12 Sub-Phases)

```
Phase 7.1: Setup & Base Layout        ████░░░░░░░░░░░░░░░░   5h
Phase 7.2: Auth Pages                 ████░░░░░░░░░░░░░░░░   4h
Phase 7.3: Public Pages               ███░░░░░░░░░░░░░░░░░   3h
Phase 7.4: Author Dashboard           ████████░░░░░░░░░░░░   8h
Phase 7.5: Reviewer Dashboard         ████████░░░░░░░░░░░░   8h
Phase 7.6: Chair Dashboard            ████████████░░░░░░░░  12h
Phase 7.7: Admin Dashboard            ████████░░░░░░░░░░░░   8h
Phase 7.8: Paper Submission           ████░░░░░░░░░░░░░░░░   4h
Phase 7.9: Review System              ████░░░░░░░░░░░░░░░░   4h
Phase 7.10: Bidding Interface         ███░░░░░░░░░░░░░░░░░   3h
Phase 7.11: Reports & Analytics       ████░░░░░░░░░░░░░░░░   4h
Phase 7.12: Polish & Testing          ███░░░░░░░░░░░░░░░░░   3h
─────────────────────────────────────────────────────────
TOTAL:                                ████████████████████  60h
```

---

## 🎨 DESIGN SYSTEM (Based on UI/UX Instructions)

### Color Palette:
```css
/* Primary Colors */
--color-primary: #1e40af;      /* Blue 700 - Navigation, Headers */
--color-primary-dark: #1e3a8a; /* Blue 800 - Hover states */
--color-accent: #f97316;        /* Orange 500 - CTA buttons */

/* Neutral Colors */
--color-bg: #f9fafb;           /* Gray 50 - Background */
--color-surface: #ffffff;       /* White - Cards, surfaces */
--color-text: #111827;         /* Gray 900 - Primary text */
--color-text-light: #6b7280;   /* Gray 500 - Secondary text */

/* Semantic Colors */
--color-success: #10b981;      /* Green 500 - Success messages */
--color-warning: #f59e0b;      /* Amber 500 - Warnings */
--color-danger: #ef4444;       /* Red 500 - Errors, delete */
--color-info: #3b82f6;         /* Blue 500 - Info messages */
```

### Typography:
```css
/* Font Family */
font-family: 'Inter', sans-serif;

/* Font Sizes */
--text-xs: 12px;
--text-sm: 14px;    /* Base size */
--text-base: 16px;
--text-lg: 18px;
--text-xl: 20px;
--text-2xl: 24px;
--text-3xl: 30px;
```

### Button Styles:
```html
<!-- Primary CTA -->
<button class="btn-primary">
  bg-orange-500 hover:bg-orange-600 text-white px-4 py-2 rounded-lg
</button>

<!-- Secondary -->
<button class="btn-secondary">
  bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 px-4 py-2 rounded-lg
</button>

<!-- Danger -->
<button class="btn-danger">
  bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg
</button>
```

---

## 📁 FOLDER STRUCTURE

```
resources/
├── views/
│   ├── layouts/
│   │   ├── app.blade.php              # Main layout
│   │   ├── guest.blade.php            # Guest layout (login/register)
│   │   └── partials/
│   │       ├── header.blade.php       # Navigation bar
│   │       ├── sidebar.blade.php      # Sidebar menu
│   │       ├── footer.blade.php       # Footer
│   │       └── alerts.blade.php       # Toast notifications
│   │
│   ├── auth/
│   │   ├── login.blade.php            # Login page
│   │   ├── register.blade.php         # Registration page
│   │   └── profile.blade.php          # User profile
│   │
│   ├── public/
│   │   ├── home.blade.php             # Homepage (landing)
│   │   ├── conferences.blade.php      # Conference listing
│   │   └── conference-detail.blade.php # Conference details
│   │
│   ├── author/
│   │   ├── dashboard.blade.php        # Author dashboard
│   │   ├── papers.blade.php           # My papers list
│   │   ├── paper-submit.blade.php     # Submit new paper
│   │   └── paper-detail.blade.php     # Paper details
│   │
│   ├── reviewer/
│   │   ├── dashboard.blade.php        # Reviewer dashboard
│   │   ├── bidding.blade.php          # Bidding interface
│   │   ├── assignments.blade.php      # My assignments
│   │   └── review-form.blade.php      # Review submission
│   │
│   ├── chair/
│   │   ├── dashboard.blade.php        # Chair dashboard
│   │   ├── papers.blade.php           # Manage papers
│   │   ├── reviewers.blade.php        # Manage reviewers
│   │   ├── assignments.blade.php      # Assign reviewers
│   │   └── coi.blade.php              # COI management
│   │
│   ├── admin/
│   │   ├── dashboard.blade.php        # Admin dashboard
│   │   ├── users.blade.php            # User management
│   │   ├── conferences.blade.php      # Conference management
│   │   └── reports.blade.php          # System reports
│   │
│   └── components/
│       ├── card.blade.php             # Card component
│       ├── table.blade.php            # Table component
│       ├── modal.blade.php            # Modal component
│       ├── badge.blade.php            # Status badge
│       ├── pagination.blade.php       # Pagination
│       └── chart.blade.php            # Chart component
│
├── css/
│   └── app.css                        # Tailwind CSS + custom styles
│
└── js/
    ├── app.js                         # Main JS file
    ├── api.js                         # API service (Axios)
    ├── auth.js                        # Authentication logic
    └── components/
        ├── notifications.js           # Toast notifications
        ├── file-upload.js             # File upload handler
        └── charts.js                  # Chart initialization
```

---

## 🚀 PHASE 7.1: SETUP & BASE LAYOUT (5 hours)

### Goals:
- ✅ Install Tailwind CSS & Alpine.js
- ✅ Setup main layout structure
- ✅ Create responsive navigation
- ✅ Setup routing for web pages

### Tasks:

#### 1. Install Dependencies
```bash
# Install Tailwind CSS
npm install -D tailwindcss postcss autoprefixer
npx tailwindcss init -p

# Install Alpine.js
npm install alpinejs

# Install Axios
npm install axios

# Install Chart.js (for analytics)
npm install chart.js
```

#### 2. Configure Tailwind
```js
// tailwind.config.js
module.exports = {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
  ],
  theme: {
    extend: {
      colors: {
        primary: '#1e40af',
        accent: '#f97316',
      },
      fontFamily: {
        sans: ['Inter', 'sans-serif'],
      },
    },
  },
}
```

#### 3. Create Base Layout
```blade
{{-- layouts/app.blade.php --}}
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - HUIT Conferences</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 font-sans">
    @include('layouts.partials.header')
    
    <div class="flex">
        @include('layouts.partials.sidebar')
        
        <main class="flex-1 p-6">
            @include('layouts.partials.alerts')
            @yield('content')
        </main>
    </div>
    
    @include('layouts.partials.footer')
</body>
</html>
```

#### 4. Create Navigation
```blade
{{-- layouts/partials/header.blade.php --}}
<nav class="bg-blue-700 text-white shadow-lg">
    <div class="container mx-auto px-4">
        <div class="flex items-center justify-between h-16">
            {{-- Logo --}}
            <a href="/" class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center">
                    <span class="text-blue-700 font-bold text-xl">H</span>
                </div>
                <div>
                    <div class="font-bold">HUIT Conferences</div>
                    <div class="text-xs text-blue-200">Hệ thống quản lý hội thảo</div>
                </div>
            </a>
            
            {{-- Desktop Menu --}}
            <div class="hidden md:flex items-center space-x-6">
                <a href="/conferences" class="hover:text-blue-200">Hội thảo</a>
                <a href="/about" class="hover:text-blue-200">Tin tức</a>
                <a href="/help" class="hover:text-blue-200">Hỗ trợ</a>
                <a href="/calendar" class="hover:text-blue-200">Lịch</a>
                
                @auth
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="flex items-center space-x-2">
                            <span>{{ auth()->user()->full_name }}</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        
                        <div x-show="open" @click.away="open = false" 
                             class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg">
                            <a href="/profile" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Profile</a>
                            <a href="/dashboard" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Dashboard</a>
                            <hr>
                            <form method="POST" action="/logout">
                                @csrf
                                <button type="submit" class="w-full text-left px-4 py-2 text-red-600 hover:bg-gray-100">
                                    Đăng xuất
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <a href="/login" class="hover:text-blue-200">Đăng nhập</a>
                    <a href="/register" class="bg-orange-500 hover:bg-orange-600 px-4 py-2 rounded-lg">
                        Đăng ký
                    </a>
                @endauth
            </div>
            
            {{-- Mobile Menu Button --}}
            <button class="md:hidden" @click="mobileMenuOpen = !mobileMenuOpen">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
            </button>
        </div>
    </div>
</nav>
```

### Deliverables:
- ✅ Tailwind CSS configured
- ✅ Alpine.js integrated
- ✅ Base layout created
- ✅ Responsive navigation
- ✅ Guest & authenticated layouts

---

## 🔐 PHASE 7.2: AUTH PAGES (4 hours)

### Pages to Create:
1. **Login Page** - `/login`
2. **Register Page** - `/register`
3. **Profile Page** - `/profile`

### Features:
- ✅ Beautiful login/register forms
- ✅ Client-side validation
- ✅ Loading states
- ✅ Error handling
- ✅ Remember me checkbox
- ✅ Forgot password link

---

## 🌐 PHASE 7.3: PUBLIC PAGES (3 hours)

### Pages to Create:
1. **Homepage** - `/`
2. **Conference Listing** - `/conferences`
3. **Conference Detail** - `/conferences/{id}`

### Features:
- ✅ Hero section with search
- ✅ Conference cards with stats
- ✅ Filters (status, date, topic)
- ✅ Conference timeline
- ✅ Responsive grid layout

---

## 📝 PHASE 7.4: AUTHOR DASHBOARD (8 hours)

### Pages:
1. **Dashboard** - Overview of my papers
2. **My Papers** - List with filters
3. **Submit Paper** - Multi-step form
4. **Paper Detail** - View status, reviews

### Features:
- ✅ Paper status tracking
- ✅ Multi-step submission form
- ✅ File upload (PDF, DOCX)
- ✅ Co-author management
- ✅ Version history
- ✅ Review feedback display

---

## 📊 PHASE 7.5: REVIEWER DASHBOARD (8 hours)

### Pages:
1. **Dashboard** - Overview of assignments
2. **Bidding** - Bid on papers
3. **Assignments** - My assigned papers
4. **Review Form** - Submit review

### Features:
- ✅ Bidding interface (EAGER/WILLING/NEUTRAL/UNWILLING/CONFLICT)
- ✅ Paper list with filters
- ✅ Deadline countdown
- ✅ Review form with ratings
- ✅ Progress tracking

---

## 👔 PHASE 7.6: CHAIR DASHBOARD (12 hours)

### Pages:
1. **Dashboard** - Conference overview
2. **Manage Papers** - All papers in conference
3. **Manage Reviewers** - Reviewer list
4. **Assignments** - Assign reviewers to papers
5. **COI Management** - Resolve conflicts

### Features:
- ✅ Paper-reviewer matrix
- ✅ Drag-drop assignment
- ✅ COI visualization
- ✅ Deadline management
- ✅ Bulk operations
- ✅ Conference statistics

---

## 🔧 PHASE 7.7: ADMIN DASHBOARD (8 hours)

### Pages:
1. **Dashboard** - System overview
2. **User Management** - List, edit, lock users
3. **Conference Management** - Approve/reject
4. **Reports** - System analytics

### Features:
- ✅ User CRUD operations
- ✅ Role assignment
- ✅ System statistics
- ✅ Charts & graphs
- ✅ Export reports

---

## 📤 PHASE 7.8: PAPER SUBMISSION (4 hours)

### Features:
- ✅ Multi-step wizard
- ✅ File upload with drag-drop
- ✅ Metadata form
- ✅ Author management
- ✅ Preview before submit
- ✅ Progress saving

---

## ⭐ PHASE 7.9: REVIEW SYSTEM (4 hours)

### Features:
- ✅ Review form with criteria
- ✅ Rating sliders (1-10)
- ✅ Confidence level
- ✅ Public/private comments
- ✅ Recommendation dropdown
- ✅ Save draft functionality

---

## 🎯 PHASE 7.10: BIDDING INTERFACE (3 hours)

### Features:
- ✅ Paper cards with quick actions
- ✅ One-click bidding buttons
- ✅ Bulk bidding
- ✅ Filter by topic
- ✅ COI declaration
- ✅ Statistics display

---

## 📈 PHASE 7.11: REPORTS & ANALYTICS (4 hours)

### Features:
- ✅ Conference report dashboard
- ✅ Charts (papers by status, review progress)
- ✅ Top reviewers leaderboard
- ✅ Deadline calendar
- ✅ Export to PDF/Excel
- ✅ System health indicators

---

## ✨ PHASE 7.12: POLISH & TESTING (3 hours)

### Tasks:
- ✅ Animation & transitions
- ✅ Loading states
- ✅ Error handling
- ✅ Toast notifications
- ✅ Responsive testing (mobile, tablet, desktop)
- ✅ Browser testing (Chrome, Firefox, Safari, Edge)
- ✅ Accessibility audit
- ✅ Performance optimization

---

## 🎨 COMPONENTS TO BUILD

### Reusable Components:

#### 1. Card Component
```blade
{{-- components/card.blade.php --}}
<div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition">
    @if(isset($header))
        <div class="border-b pb-4 mb-4">
            {{ $header }}
        </div>
    @endif
    
    {{ $slot }}
    
    @if(isset($footer))
        <div class="border-t pt-4 mt-4">
            {{ $footer }}
        </div>
    @endif
</div>
```

#### 2. Badge Component
```blade
{{-- components/badge.blade.php --}}
@php
    $colors = [
        'success' => 'bg-green-100 text-green-800',
        'warning' => 'bg-yellow-100 text-yellow-800',
        'danger' => 'bg-red-100 text-red-800',
        'info' => 'bg-blue-100 text-blue-800',
    ];
    $class = $colors[$type ?? 'info'];
@endphp

<span class="px-2 py-1 text-xs font-semibold rounded {{ $class }}">
    {{ $slot }}
</span>
```

#### 3. Modal Component
```blade
{{-- components/modal.blade.php --}}
<div x-data="{ open: false }" @open-modal.window="open = true">
    <div x-show="open" 
         class="fixed inset-0 z-50 overflow-y-auto" 
         @click.away="open = false">
        <div class="flex items-center justify-center min-h-screen px-4">
            {{-- Backdrop --}}
            <div class="fixed inset-0 bg-black opacity-50"></div>
            
            {{-- Modal --}}
            <div class="bg-white rounded-lg shadow-xl max-w-md w-full relative z-10">
                <div class="p-6">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </div>
</div>
```

#### 4. Table Component
```blade
{{-- components/table.blade.php --}}
<div class="overflow-x-auto">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            {{ $header }}
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            {{ $slot }}
        </tbody>
    </table>
</div>
```

---

## 🔄 API INTEGRATION

### JavaScript API Service:
```javascript
// resources/js/api.js
import axios from 'axios';

const API_BASE = '/api';

// Setup axios defaults
axios.defaults.baseURL = API_BASE;
axios.defaults.headers.common['Accept'] = 'application/json';
axios.defaults.headers.common['Content-Type'] = 'application/json';

// Add token to requests
const token = localStorage.getItem('auth_token');
if (token) {
    axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
}

// API methods
export const api = {
    // Auth
    login: (credentials) => axios.post('/auth/login', credentials),
    register: (data) => axios.post('/auth/register', data),
    logout: () => axios.post('/auth/logout'),
    
    // Conferences
    getConferences: (params) => axios.get('/conferences', { params }),
    getConference: (id) => axios.get(`/conferences/${id}`),
    
    // Papers
    getPapers: (params) => axios.get('/papers', { params }),
    submitPaper: (data) => axios.post('/papers', data),
    
    // Reviews
    submitReview: (data) => axios.post('/reviews', data),
    getMyReviews: () => axios.get('/my-reviews'),
    
    // Bidding
    submitBid: (paperId, data) => axios.post(`/papers/${paperId}/bid`, data),
    getMyBiddings: () => axios.get('/my-biddings'),
    
    // Admin
    getUsers: (params) => axios.get('/admin/users', { params }),
    updateUser: (id, data) => axios.put(`/admin/users/${id}`, data),
};
```

---

## 📱 RESPONSIVE BREAKPOINTS

```css
/* Tailwind Breakpoints */
sm:  640px   /* Mobile landscape */
md:  768px   /* Tablet */
lg:  1024px  /* Desktop */
xl:  1280px  /* Large desktop */
2xl: 1536px  /* Extra large */
```

---

## ✅ ACCEPTANCE CRITERIA

### Phase 7.1 Complete When:
- [ ] Tailwind CSS working
- [ ] Alpine.js integrated
- [ ] Navigation responsive
- [ ] Layouts created
- [ ] Components folder setup

### Overall Phase 7 Complete When:
- [ ] All 12 sub-phases done
- [ ] All role-specific pages working
- [ ] Responsive on all devices
- [ ] API integration complete
- [ ] Toast notifications working
- [ ] File uploads working
- [ ] Forms validated
- [ ] Charts displaying
- [ ] No console errors
- [ ] Accessibility tested

---

## 🚀 NEXT ACTIONS

**Start with Phase 7.1 - Setup & Base Layout**

Ready to begin? I'll:
1. Install Tailwind CSS & Alpine.js
2. Create base layouts
3. Build responsive navigation
4. Setup component structure

**Shall we start Phase 7.1?** 🎨
