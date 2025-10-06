# Phase 8.3: Authentication & Authorization

**Date:** October 5, 2025  
**Status:** In Progress 🚀  
**Estimated Time:** 4-6 hours

---

## 📋 Overview

Implement complete authentication system with role-based access control using Laravel's built-in authentication features, integrated with our existing database structure.

---

## 🎯 Goals

1. **User Authentication**
   - Login/Logout functionality
   - Session management
   - Remember me feature

2. **Custom User Model**
   - Use `NguoiDung` table instead of default `users`
   - Map fields correctly

3. **Role-Based Access Control**
   - Middleware for role checking
   - Redirect users to correct dashboard
   - Protect routes by role

4. **Replace Sample Data**
   - Remove hardcoded user IDs from controllers
   - Use `Auth::id()` for current user
   - Get user roles dynamically

---

## 📝 Tasks Checklist

### Part 1: Custom User Model (30-45 mins)
- [ ] Create User model for NguoiDung table
- [ ] Configure authentication fields mapping
- [ ] Update config/auth.php
- [ ] Add relationships to roles

### Part 2: Authentication Routes & Controller (30-45 mins)
- [ ] Create AuthController
- [ ] Setup login routes
- [ ] Setup logout routes
- [ ] Implement login logic
- [ ] Session management

### Part 3: Login View (30-45 mins)
- [ ] Create login page UI
- [ ] Form validation
- [ ] Error messages
- [ ] Remember me checkbox

### Part 4: Role-Based Middleware (45-60 mins)
- [ ] Create CheckRole middleware
- [ ] Register middleware
- [ ] Apply to dashboard routes
- [ ] Implement role checking logic

### Part 5: Update Controllers (45-60 mins)
- [ ] Replace sample user IDs with Auth::id()
- [ ] Add role-based data filtering
- [ ] Update all 4 dashboard controllers
- [ ] Add authorization checks

### Part 6: Role-Based Redirects (30 mins)
- [ ] Redirect after login based on role
- [ ] Handle unauthorized access
- [ ] Create 403 error page

### Part 7: Testing (30 mins)
- [ ] Test login with different roles
- [ ] Test logout
- [ ] Test unauthorized access
- [ ] Test session persistence

---

## 🗂️ Files to Create/Modify

### New Files:
1. `app/Models/NguoiDung.php` - Custom User model
2. `app/Http/Controllers/Auth/AuthController.php` - Authentication controller
3. `app/Http/Middleware/CheckRole.php` - Role checking middleware
4. `resources/views/auth/login.blade.php` - Login page
5. `resources/views/errors/403.blade.php` - Unauthorized page

### Modified Files:
1. `config/auth.php` - Authentication configuration
2. `app/Http/Kernel.php` - Register middleware
3. `routes/web.php` - Add auth routes and protect dashboards
4. `app/Http/Controllers/DashboardController.php` - Use Auth::id()

---

## 🔧 Implementation Details

### 1. Custom User Model Structure

```php
// app/Models/NguoiDung.php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class NguoiDung extends Authenticatable
{
    protected $table = 'NguoiDung';
    protected $primaryKey = 'user_id';
    
    protected $fillable = [
        'full_name',
        'email',
        'password',
        'affiliation',
        'country',
    ];
    
    protected $hidden = [
        'password',
    ];
    
    public function roles()
    {
        return $this->hasMany(VaiTroNguoiDung::class, 'user_id', 'user_id');
    }
    
    public function hasRole($roleCode)
    {
        return $this->roles()->where('role_code', $roleCode)->exists();
    }
}
```

### 2. Login Controller Logic

```php
public function login(Request $request)
{
    $credentials = $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);
    
    if (Auth::attempt($credentials, $request->remember)) {
        $request->session()->regenerate();
        
        // Redirect based on role
        $user = Auth::user();
        if ($user->hasRole('ADMIN')) {
            return redirect('/admin/dashboard');
        } elseif ($user->hasRole('CHAIR')) {
            return redirect('/chair/dashboard');
        } elseif ($user->hasRole('REVIEWER')) {
            return redirect('/reviewer/dashboard');
        } else {
            return redirect('/author/dashboard');
        }
    }
    
    return back()->withErrors([
        'email' => 'Thông tin đăng nhập không chính xác.',
    ]);
}
```

### 3. Middleware Structure

```php
// app/Http/Middleware/CheckRole.php
public function handle($request, Closure $next, ...$roles)
{
    if (!Auth::check()) {
        return redirect('/login');
    }
    
    $user = Auth::user();
    
    foreach ($roles as $role) {
        if ($user->hasRole($role)) {
            return $next($request);
        }
    }
    
    abort(403, 'Unauthorized access');
}
```

### 4. Protected Routes

```php
// routes/web.php
Route::middleware(['auth', 'role:AUTHOR'])->group(function () {
    Route::get('/author/dashboard', [DashboardController::class, 'authorDashboard']);
});

Route::middleware(['auth', 'role:REVIEWER'])->group(function () {
    Route::get('/reviewer/dashboard', [DashboardController::class, 'reviewerDashboard']);
});

Route::middleware(['auth', 'role:CHAIR'])->group(function () {
    Route::get('/chair/dashboard', [DashboardController::class, 'chairDashboard']);
});

Route::middleware(['auth', 'role:ADMIN'])->group(function () {
    Route::get('/admin/dashboard', [DashboardController::class, 'adminDashboard']);
});
```

### 5. Updated Controller Methods

```php
public function authorDashboard()
{
    $userId = Auth::id(); // Instead of hardcoded user ID
    
    $papers = DB::table('BaiBao')
        ->where('submitter_id', $userId)
        // ... rest of query
}
```

---

## 🎨 Login Page Features

- Clean, professional design matching dashboard style
- Email and password fields
- Remember me checkbox
- Error message display
- Responsive layout
- CSRF protection
- Form validation feedback

---

## 🧪 Test Accounts

From our seeded database, we can use these test accounts:

1. **Author Account:**
   - Email: From first user with AUTHOR role
   - Password: password (hashed)

2. **Reviewer Account:**
   - Email: From first user with REVIEWER role
   - Password: password

3. **Chair Account:**
   - Email: From first user with CHAIR role
   - Password: password

4. **Admin Account:**
   - Email: From first user with ADMIN role
   - Password: password

---

## 📊 Progress Tracking

- [ ] Part 1: Custom User Model (0%)
- [ ] Part 2: Authentication Routes (0%)
- [ ] Part 3: Login View (0%)
- [ ] Part 4: Role Middleware (0%)
- [ ] Part 5: Update Controllers (0%)
- [ ] Part 6: Role Redirects (0%)
- [ ] Part 7: Testing (0%)

**Overall: 0%**

---

## 🚀 Ready to Start!

Tôi sẽ bắt đầu với việc tạo Custom User Model và cấu hình authentication.

**Bạn có muốn tôi tiếp tục không?** (yes/no)
