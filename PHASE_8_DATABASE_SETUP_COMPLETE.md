# Phase 8: Database Setup - COMPLETE ✅

**Date:** October 24, 2025  
**Status:** Database Foundation Complete  
**Progress:** Phase 8.1 Complete - Ready for 8.2 (Controller Integration)

---

## 📊 Database Summary

### Current State
```
Users:           248 ✅
Conferences:     6   ✅
Papers:          45  ✅
Assignments:     114 ✅
Reviews:         74  ✅
Roles Assigned:  248 ✅
```

### User Distribution
- **Admins:**     6 users
- **Chairs:**     18 users
- **Reviewers:**  68 users
- **Authors:**    156 users
- **Total:**      248 users (all with roles assigned)

---

## 🎯 Conferences Created

### 1. HUIT International Conference on ICT 2025 (ID: 4)
- **Level:** TRUONG (University-wide)
- **Papers:** 28
- **Status Distribution:**
  - ✅ Accepted: 20
  - 🔄 Under Review: 5
  - 🔧 Revision: 2
  - ❌ Rejected: 1
- **Topics:** Deep Learning, Blockchain, IoT, NLP, Cloud Computing, Big Data, etc.

### 2. HUIT Security Summit 2025 (ID: 5)
- **Level:** TRUONG (University-wide)
- **Papers:** 12
- **Status Distribution:**
  - ✅ Accepted: 2
  - 🔄 Under Review: 8
  - 📝 Submitted: 2
- **Topics:** Intrusion Detection, Cryptography, Zero Trust, Malware, Penetration Testing

### 3. HUIT AI & Data Science Forum 2025 (ID: 6)
- **Level:** KHOA (Faculty-level)
- **Papers:** 5
- **Status Distribution:**
  - 🔄 Under Review: 3
  - 📝 Submitted: 2
- **Topics:** Reinforcement Learning, Generative AI, Explainable AI, Data Science, Predictive Analytics

---

## 👥 Reviewer Assignments

### Assignment Summary
- **Total Assignments:** 114
- **Papers Reviewed:** 38 (papers in ACCEPTED/UNDER_REVIEW/REVISION status)
- **Reviewers per Paper:** 3
- **Active Reviewers:** 68

### Assignment Status Distribution
- **COMPLETED:** 74 (reviews submitted)
- **ACCEPTED:** 10 (reviewer accepted the assignment)
- **INVITED:** 30 (awaiting reviewer response)

### Review Quality
- **Reviews Completed:** 74
- **Review Scores:** 1-5 scale
- **Recommendations:**
  - ACCEPT (score 4-5)
  - MINOR (score 3 - minor revisions)
  - MAJOR (score 2 - major revisions)
  - REJECT (score 1)

---

## 🗄️ Database Schema Understanding

### Key Tables & Actual Structure

#### 1. **HoiThao** (Conferences)
```sql
- conference_id (PK)
- parent_id (nullable) - for hierarchy
- level_code (KHOA/TRUONG) - faculty or university level
- faculty_id - owning faculty
- title - conference name (NOT conference_name)
- year - single year field (NOT start_year/end_year)
- start_date, end_date
- deadline_submission, deadline_review, deadline_camera_ready
- status
```

#### 2. **BaiBao** (Papers)
```sql
- paper_id (PK)
- conference_id (FK)
- submitter_id (FK to NguoiDung)
- title
- status_code (FK to TrangThaiBaiBao)
- created_at - submission timestamp (NOT submitted_at)
- current_version_id - link to latest version
```

#### 3. **PhanCongPhanBien** (Reviewer Assignments)
```sql
- assignment_id (PK)
- paper_id (FK)
- reviewer_id (FK to NguoiDung)
- chair_id (nullable FK to NguoiDung)
- status_code (FK to TrangThaiPhanCong)
- token (UUID) - unique token for assignment
- assigned_at - timestamp (NOT created_at)
- deadline - review due date
```

#### 4. **PhanBien** (Reviews)
```sql
- review_id (PK)
- assignment_id (FK)
- recommendation_code (FK to LoaiKhuyenNghi)
- score (1-5)
- comment_author - feedback for author
- comment_chair - feedback for chair
- submitted_at - submission timestamp
```

---

## 📝 Seeder Files Created

### 1. **ConferencesPapersSeeder.php** ✅
- **Purpose:** Seed conferences and papers for existing users
- **Created:** 3 conferences, 45 papers
- **Logic:** Assigns papers to random existing authors
- **Status:** Successfully run

### 2. **ReviewerAssignmentsSeeder.php** ✅
- **Purpose:** Create reviewer assignments and sample reviews
- **Created:** 114 assignments, 74 reviews
- **Logic:** 
  - Assigns 3 reviewers per paper
  - Avoids assigning paper authors as reviewers
  - Creates completed reviews with scores and recommendations
- **Status:** Successfully run

---

## ⚠️ Schema Differences Discovered

During Phase 8.1, we discovered several differences between expected and actual schema:

### Column Name Differences
| Expected | Actual | Table |
|----------|--------|-------|
| `submitted_at` | `created_at` | BaiBao |
| `created_at` | `assigned_at` | PhanCongPhanBien |
| `conference_name` | `title` | HoiThao |

### Missing Columns
- `HoiThao.chair_id` - Does NOT exist (chairs assigned through roles)
- `HoiThao.conference_code` - Does NOT exist (use conference_id)

### Additional Required Fields
- `PhanCongPhanBien.token` - UUID required for each assignment

### Lookup Table Differences
The actual `LoaiKhuyenNghi` (Recommendation Types) contains:
- ACCEPT
- MAJOR (major revision)
- MINOR (minor revision)
- REJECT

NOT the expected STRONG_ACCEPT, WEAK_ACCEPT, BORDERLINE, etc.

---

## 🔍 Data Verification Commands

### Check Overall Counts
```bash
php artisan tinker --execute="
echo '========== DATABASE SUMMARY ==========' . PHP_EOL;
echo 'Users: ' . DB::table('NguoiDung')->count() . PHP_EOL;
echo 'Conferences: ' . DB::table('HoiThao')->count() . PHP_EOL;
echo 'Papers: ' . DB::table('BaiBao')->count() . PHP_EOL;
echo 'Assignments: ' . DB::table('PhanCongPhanBien')->count() . PHP_EOL;
echo 'Reviews: ' . DB::table('PhanBien')->count() . PHP_EOL;
"
```

### Check User Roles
```bash
php artisan tinker --execute="
DB::table('VaiTroNguoiDung')
    ->join('LoaiVaiTro', 'VaiTroNguoiDung.role_code', '=', 'LoaiVaiTro.role_code')
    ->select('LoaiVaiTro.role_name', DB::raw('count(*) as count'))
    ->groupBy('LoaiVaiTro.role_name')
    ->get();
"
```

### Check Conference Papers
```bash
php artisan tinker --execute="
DB::table('BaiBao')
    ->join('HoiThao', 'BaiBao.conference_id', '=', 'HoiThao.conference_id')
    ->select('HoiThao.title', DB::raw('count(*) as papers'))
    ->groupBy('HoiThao.title')
    ->get();
"
```

### Check Assignment Status
```bash
php artisan tinker --execute="
DB::table('PhanCongPhanBien')
    ->select('status_code', DB::raw('count(*) as count'))
    ->groupBy('status_code')
    ->get();
"
```

---

## 🚀 Next Steps: Phase 8.2 - Controller Integration

### Task Overview
Update controllers to fetch and display real database data instead of hardcoded values.

### Files to Modify

#### 1. **DashboardController.php**
Update all dashboard methods to query real data:

##### Author Dashboard
```php
public function authorDashboard()
{
    $userId = Auth::id();
    
    // Get user's papers
    $papers = DB::table('BaiBao')
        ->where('submitter_id', $userId)
        ->join('TrangThaiBaiBao', 'BaiBao.status_code', '=', 'TrangThaiBaiBao.status_code')
        ->join('HoiThao', 'BaiBao.conference_id', '=', 'HoiThao.conference_id')
        ->select(
            'BaiBao.*',
            'TrangThaiBaiBao.status_name',
            'HoiThao.title as conference_name'
        )
        ->get();
    
    // Calculate stats
    $stats = [
        'total' => $papers->count(),
        'under_review' => $papers->where('status_code', 'UNDER_REVIEW')->count(),
        'accepted' => $papers->where('status_code', 'ACCEPTED')->count(),
        'rejected' => $papers->where('status_code', 'REJECTED')->count(),
    ];
    
    return view('author.dashboard', compact('papers', 'stats'));
}
```

##### Reviewer Dashboard
```php
public function reviewerDashboard()
{
    $userId = Auth::id();
    
    // Get assignments
    $assignments = DB::table('PhanCongPhanBien')
        ->where('reviewer_id', $userId)
        ->join('BaiBao', 'PhanCongPhanBien.paper_id', '=', 'BaiBao.paper_id')
        ->join('HoiThao', 'BaiBao.conference_id', '=', 'HoiThao.conference_id')
        ->leftJoin('PhanBien', 'PhanCongPhanBien.assignment_id', '=', 'PhanBien.assignment_id')
        ->select(
            'PhanCongPhanBien.*',
            'BaiBao.title as paper_title',
            'HoiThao.title as conference_name',
            'PhanBien.review_id',
            'PhanBien.recommendation_code'
        )
        ->get();
    
    // Calculate stats
    $stats = [
        'total' => $assignments->count(),
        'pending' => $assignments->where('status_code', 'INVITED')->count(),
        'in_progress' => $assignments->where('status_code', 'ACCEPTED')->count(),
        'completed' => $assignments->where('status_code', 'COMPLETED')->count(),
    ];
    
    return view('reviewer.dashboard', compact('assignments', 'stats'));
}
```

##### Chair Dashboard
```php
public function chairDashboard()
{
    $userId = Auth::id();
    
    // Get conferences where user is chair
    // Note: Need to determine chair assignment logic (via roles or conference table)
    $conferences = DB::table('HoiThao')
        ->join('VaiTroNguoiDung', function($join) use ($userId) {
            $join->where('VaiTroNguoiDung.user_id', '=', $userId)
                 ->where('VaiTroNguoiDung.role_code', '=', 'CHAIR');
        })
        ->select('HoiThao.*')
        ->get();
    
    // Get papers for these conferences
    $papers = DB::table('BaiBao')
        ->whereIn('conference_id', $conferences->pluck('conference_id'))
        ->join('TrangThaiBaiBao', 'BaiBao.status_code', '=', 'TrangThaiBaiBao.status_code')
        ->select('BaiBao.*', 'TrangThaiBaiBao.status_name')
        ->get();
    
    // Calculate stats
    $stats = [
        'total_papers' => $papers->count(),
        'accepted' => $papers->where('status_code', 'ACCEPTED')->count(),
        'under_review' => $papers->where('status_code', 'UNDER_REVIEW')->count(),
        'rejected' => $papers->where('status_code', 'REJECTED')->count(),
    ];
    
    return view('chair.dashboard', compact('conferences', 'papers', 'stats'));
}
```

##### Admin Dashboard
```php
public function adminDashboard()
{
    // System-wide statistics
    $stats = [
        'total_users' => DB::table('NguoiDung')->count(),
        'pending_users' => DB::table('NguoiDung')->where('status', 'PENDING')->count(),
        'total_conferences' => DB::table('HoiThao')->count(),
        'active_conferences' => DB::table('HoiThao')->where('status', 'ACTIVE')->count(),
        'total_papers' => DB::table('BaiBao')->count(),
        'total_reviews' => DB::table('PhanBien')->count(),
    ];
    
    // Recent activity
    $recentPapers = DB::table('BaiBao')
        ->join('NguoiDung', 'BaiBao.submitter_id', '=', 'NguoiDung.user_id')
        ->join('HoiThao', 'BaiBao.conference_id', '=', 'HoiThao.conference_id')
        ->select(
            'BaiBao.title',
            'NguoiDung.full_name as author',
            'HoiThao.title as conference',
            'BaiBao.created_at'
        )
        ->orderBy('BaiBao.created_at', 'desc')
        ->limit(10)
        ->get();
    
    return view('admin.dashboard', compact('stats', 'recentPapers'));
}
```

#### 2. **Update Blade Templates**
Replace hardcoded data with dynamic Blade loops:

**Author Dashboard (author/dashboard.blade.php)**
```blade
<!-- Stats Cards -->
<div class="stat-card">
    <h3>{{ $stats['total'] }}</h3>
    <p>Total Papers</p>
</div>

<!-- Papers Table -->
@foreach($papers as $paper)
<tr>
    <td>{{ $paper->title }}</td>
    <td>{{ $paper->conference_name }}</td>
    <td>
        <span class="badge badge-{{ $paper->status_code }}">
            {{ $paper->status_name }}
        </span>
    </td>
    <td>{{ $paper->created_at }}</td>
</tr>
@endforeach
```

#### 3. **Create Eloquent Models**
Create models for easier data manipulation:

```php
// app/Models/Conference.php
class Conference extends Model
{
    protected $table = 'HoiThao';
    protected $primaryKey = 'conference_id';
    
    public function papers() {
        return $this->hasMany(Paper::class, 'conference_id', 'conference_id');
    }
}

// app/Models/Paper.php
class Paper extends Model
{
    protected $table = 'BaiBao';
    protected $primaryKey = 'paper_id';
    public $timestamps = false;
    
    public function conference() {
        return $this->belongsTo(Conference::class, 'conference_id', 'conference_id');
    }
    
    public function submitter() {
        return $this->belongsTo(User::class, 'submitter_id', 'user_id');
    }
    
    public function assignments() {
        return $this->hasMany(ReviewerAssignment::class, 'paper_id', 'paper_id');
    }
}

// app/Models/ReviewerAssignment.php
class ReviewerAssignment extends Model
{
    protected $table = 'PhanCongPhanBien';
    protected $primaryKey = 'assignment_id';
    
    public function paper() {
        return $this->belongsTo(Paper::class, 'paper_id', 'paper_id');
    }
    
    public function reviewer() {
        return $this->belongsTo(User::class, 'reviewer_id', 'user_id');
    }
    
    public function review() {
        return $this->hasOne(Review::class, 'assignment_id', 'assignment_id');
    }
}
```

---

## 📋 Phase 8 Completion Checklist

### Phase 8.1: Database Setup ✅ COMPLETE
- [x] MySQL running and accessible
- [x] All 10 migrations executed
- [x] Lookup tables seeded
- [x] 248 users created with roles
- [x] 6 conferences created (3 from seeder + 3 existing)
- [x] 45 papers created and assigned to authors
- [x] 114 reviewer assignments created
- [x] 74 sample reviews created
- [x] Schema differences documented

### Phase 8.2: Controller Integration ⏳ NEXT
- [ ] Update DashboardController methods
- [ ] Create Eloquent models
- [ ] Update Blade templates to use dynamic data
- [ ] Test all dashboards with real data
- [ ] Verify stats calculations are correct

### Phase 8.3: Authentication ⏳ PENDING
- [ ] Configure auth guards
- [ ] Update login to use NguoiDung table
- [ ] Implement role-based redirects
- [ ] Add auth middleware to routes

### Phase 8.4-8.8: Feature Implementation ⏳ PENDING
- [ ] Author: Paper submission
- [ ] Reviewer: Bidding & Reviews
- [ ] Chair: Assignment & Decisions
- [ ] Admin: User & Conference Management

---

## 🎓 Lessons Learned

### 1. Always Verify Actual Schema
- Don't assume column names match expectations
- Read migration files to understand actual structure
- Check lookup table values before using them

### 2. Handle Existing Data
- Check for existing data before seeding
- Use `insertOrIgnore()` for idempotent seeding
- Clear related data before reseeding

### 3. Foreign Key Constraints
- Lookup tables must be seeded first
- Check valid values in lookup tables
- Use correct status codes from database

### 4. Timestamp Columns
- Laravel's default `created_at`/`updated_at` may not match custom schemas
- Explicitly set timestamp values in raw queries
- Use `useCurrent()` in migrations for default timestamps

---

## 🔗 References

- **Database Schema:** See migrations in `database/migrations/`
- **Lookup Tables:** See `database/seeders/LookupTablesSeeder.php`
- **Phase 7 Summary:** See `PHASE_7_SUMMARY.md`
- **Current Seeders:**
  - `ConferencesPapersSeeder.php`
  - `ReviewerAssignmentsSeeder.php`

---

## 👏 Phase 8.1 Complete!

**Database foundation is ready!** 🎉

You now have:
- ✅ 248 users with proper roles
- ✅ 6 conferences with realistic data
- ✅ 45 papers across multiple topics
- ✅ 114 reviewer assignments
- ✅ 74 completed reviews
- ✅ Complete understanding of actual schema

**Ready to move to Phase 8.2:** Update controllers to display this real data in your dashboards! 🚀

---

*Last Updated: October 24, 2025*
