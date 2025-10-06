@auth
<aside class="w-64 bg-white border-r border-gray-200 min-h-screen" x-show="!sidebarOpen" @click.away="sidebarOpen = false">
    <nav class="p-4 space-y-1">
        @php
            $role = Auth::user()->role ?? 'author';
        @endphp
        
        <!-- Dashboard -->
        <a href="{{ route('dashboard') }}" 
           class="nav-link {{ request()->routeIs('dashboard') ? 'nav-link-active' : 'nav-link-inactive' }}">
            <div class="flex items-center space-x-3">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                </svg>
                <span>Dashboard</span>
            </div>
        </a>
        
        @if($role === 'author')
        <!-- Author Menu -->
        <a href="{{ route('author.papers.index') }}" 
           class="nav-link {{ request()->routeIs('author.papers.*') ? 'nav-link-active' : 'nav-link-inactive' }}">
            <div class="flex items-center space-x-3">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <span>Bài báo của tôi</span>
            </div>
        </a>
        
        <a href="{{ route('author.papers.create') }}" 
           class="nav-link {{ request()->routeIs('author.papers.create') ? 'nav-link-active' : 'nav-link-inactive' }}">
            <div class="flex items-center space-x-3">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                <span>Nộp bài mới</span>
            </div>
        </a>
        @endif
        
        @if($role === 'reviewer')
        <!-- Reviewer Menu -->
        <a href="{{ route('reviewer.bidding.index') }}" 
           class="nav-link {{ request()->routeIs('reviewer.bidding.*') ? 'nav-link-active' : 'nav-link-inactive' }}">
            <div class="flex items-center space-x-3">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
                </svg>
                <span>Bidding</span>
            </div>
        </a>
        
        <a href="{{ route('reviewer.assignments.index') }}" 
           class="nav-link {{ request()->routeIs('reviewer.assignments.*') ? 'nav-link-active' : 'nav-link-inactive' }}">
            <div class="flex items-center space-x-3">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
                <span>Bài được phân công</span>
            </div>
        </a>
        @endif
        
        @if($role === 'chair')
        <!-- Chair Menu -->
        <a href="{{ route('chair.conferences.index') }}" 
           class="nav-link {{ request()->routeIs('chair.conferences.*') ? 'nav-link-active' : 'nav-link-inactive' }}">
            <div class="flex items-center space-x-3">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
                <span>Quản lý hội thảo</span>
            </div>
        </a>
        
        <a href="{{ route('chair.papers.index') }}" 
           class="nav-link {{ request()->routeIs('chair.papers.*') ? 'nav-link-active' : 'nav-link-inactive' }}">
            <div class="flex items-center space-x-3">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <span>Quản lý bài báo</span>
            </div>
        </a>
        
        <a href="{{ route('chair.reviewers.index') }}" 
           class="nav-link {{ request()->routeIs('chair.reviewers.*') ? 'nav-link-active' : 'nav-link-inactive' }}">
            <div class="flex items-center space-x-3">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
                <span>Quản lý Reviewer</span>
            </div>
        </a>
        @endif
        
        @if($role === 'admin')
        <!-- Admin Menu -->
        <a href="{{ route('admin.users.index') }}" 
           class="nav-link {{ request()->routeIs('admin.users.*') ? 'nav-link-active' : 'nav-link-inactive' }}">
            <div class="flex items-center space-x-3">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                </svg>
                <span>Quản lý User</span>
            </div>
        </a>
        
        <a href="{{ route('admin.conferences.index') }}" 
           class="nav-link {{ request()->routeIs('admin.conferences.*') ? 'nav-link-active' : 'nav-link-inactive' }}">
            <div class="flex items-center space-x-3">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
                <span>Quản lý Hội thảo</span>
            </div>
        </a>
        
        <a href="{{ route('admin.reports.index') }}" 
           class="nav-link {{ request()->routeIs('admin.reports.*') ? 'nav-link-active' : 'nav-link-inactive' }}">
            <div class="flex items-center space-x-3">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
                <span>Báo cáo & Thống kê</span>
            </div>
        </a>
        @endif
        
        <!-- Common Links -->
        <hr class="my-4">
        
        <a href="{{ route('conferences.index') }}" 
           class="nav-link nav-link-inactive">
            <div class="flex items-center space-x-3">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                <span>Tìm hội thảo</span>
            </div>
        </a>
        
        <a href="{{ route('support') }}" 
           class="nav-link nav-link-inactive">
            <div class="flex items-center space-x-3">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"></path>
                </svg>
                <span>Trợ giúp</span>
            </div>
        </a>
    </nav>
</aside>
@endauth
