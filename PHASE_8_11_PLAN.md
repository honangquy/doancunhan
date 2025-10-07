# 🚀 PHASE 8.11 - REVIEWERS MANAGEMENT UI

**Start Date:** October 6, 2025  
**Status:** 🔄 IN PROGRESS  
**Priority:** ⭐⭐⭐ HIGH  
**Progress:** Phase 8.11 = 0%, Overall Project = 95%

---

## 🎯 PHASE OBJECTIVES

Complete the "Quản lý reviewer" menu feature to allow Chair to:
- ✅ View list of all reviewers in conference
- ✅ View detailed reviewer profile
- ✅ Monitor reviewer workload
- ✅ Track reviewer performance metrics
- ✅ View assignment history

---

## 📋 TASKS BREAKDOWN

### Task 1: Backend - Reviewer List & Profile (1 hour)

**File:** `app/Http/Controllers/Chair/ChairController.php`

Add methods:
```php
/**
 * GET /chair/reviewers
 * List all reviewers for current conference
 */
public function listReviewers(Request $request)
{
    $chairId = auth()->id();
    
    // Get chair's conference
    $conference = DB::table('HoiThao as ht')
        ->join('NguoiDung as nd', 'ht.chair_id', '=', 'nd.user_id')
        ->where('ht.chair_id', $chairId)
        ->select('ht.*')
        ->first();
    
    if (!$conference) {
        return redirect()->route('chair.dashboard')
            ->with('error', 'Không tìm thấy hội thảo');
    }
    
    // Get all reviewers with statistics
    $reviewers = DB::table('NguoiDung as nd')
        ->join('VaiTro as vt', 'nd.user_id', '=', 'vt.user_id')
        ->leftJoin('PhanCongPhanBien as pc', function($join) use ($conference) {
            $join->on('nd.user_id', '=', 'pc.reviewer_id')
                 ->join('BaiBao as bb', 'pc.paper_id', '=', 'bb.paper_id')
                 ->where('bb.conference_id', '=', $conference->conference_id);
        })
        ->leftJoin('PhanBien as pb', 'pc.assignment_id', '=', 'pb.assignment_id')
        ->where('vt.role_name', 'REVIEWER')
        ->select(
            'nd.user_id',
            'nd.full_name',
            'nd.email',
            'nd.affiliation',
            'nd.expertise',
            DB::raw('COUNT(DISTINCT pc.assignment_id) as total_assignments'),
            DB::raw('COUNT(DISTINCT pb.review_id) as completed_reviews'),
            DB::raw('COUNT(DISTINCT CASE WHEN pc.status = "PENDING" THEN pc.assignment_id END) as pending_reviews'),
            DB::raw('AVG(pb.overall_score) as avg_score'),
            DB::raw('AVG(DATEDIFF(pb.submitted_at, pc.assigned_at)) as avg_response_days')
        )
        ->groupBy('nd.user_id', 'nd.full_name', 'nd.email', 'nd.affiliation', 'nd.expertise')
        ->get();
    
    return view('chair.reviewers.index', [
        'conference' => $conference,
        'reviewers' => $reviewers,
    ]);
}

/**
 * GET /chair/reviewers/{id}
 * Show detailed reviewer profile
 */
public function showReviewer($id)
{
    $chairId = auth()->id();
    
    // Get reviewer info
    $reviewer = DB::table('NguoiDung')
        ->where('user_id', $id)
        ->first();
    
    if (!$reviewer) {
        return redirect()->route('chair.reviewers.index')
            ->with('error', 'Không tìm thấy reviewer');
    }
    
    // Get chair's conference
    $conference = DB::table('HoiThao')
        ->where('chair_id', $chairId)
        ->first();
    
    // Get assignments for this conference
    $assignments = DB::table('PhanCongPhanBien as pc')
        ->join('BaiBao as bb', 'pc.paper_id', '=', 'bb.paper_id')
        ->leftJoin('PhanBien as pb', 'pc.assignment_id', '=', 'pb.assignment_id')
        ->where('bb.conference_id', $conference->conference_id)
        ->where('pc.reviewer_id', $id)
        ->select(
            'pc.*',
            'bb.title as paper_title',
            'bb.status as paper_status',
            'pb.review_id',
            'pb.overall_score',
            'pb.submitted_at',
            'pb.recommendation'
        )
        ->orderBy('pc.assigned_at', 'desc')
        ->get();
    
    // Get statistics
    $stats = [
        'total_assignments' => $assignments->count(),
        'completed' => $assignments->where('review_id', '!=', null)->count(),
        'pending' => $assignments->where('status', 'PENDING')->count(),
        'avg_score' => $assignments->where('overall_score', '!=', null)->avg('overall_score'),
        'avg_days' => $assignments->where('submitted_at', '!=', null)
            ->map(function($a) {
                return \Carbon\Carbon::parse($a->assigned_at)
                    ->diffInDays(\Carbon\Carbon::parse($a->submitted_at));
            })->avg(),
    ];
    
    return view('chair.reviewers.show', [
        'reviewer' => $reviewer,
        'conference' => $conference,
        'assignments' => $assignments,
        'stats' => $stats,
    ]);
}
```

---

### Task 2: Frontend - Reviewers List View (1 hour)

**File:** `resources/views/chair/reviewers/index.blade.php`

Create standalone HTML matching dashboard structure with:
- Statistics cards (total reviewers, avg workload, etc.)
- Reviewers table with:
  - Name, Email, Affiliation
  - Total assignments, Completed, Pending
  - Average score, Response time
  - Action buttons (View profile)
- Search & filter functionality
- Sidebar with "Quản lý reviewer" menu highlighted

---

### Task 3: Frontend - Reviewer Profile View (1 hour)

**File:** `resources/views/chair/reviewers/show.blade.php`

Create profile page with:
- **Profile Card:**
  - Avatar/initials
  - Full name, Email
  - Affiliation, Expertise
- **Statistics Dashboard:**
  - Total assignments (card)
  - Completed reviews (card)
  - Pending reviews (card)
  - Average score (card)
  - Average response time (card)
- **Assignment History Table:**
  - Paper title, Status
  - Assigned date, Submitted date
  - Score, Recommendation
  - Link to paper detail
- **Workload Chart** (optional - can use simple progress bars)

---

### Task 4: Routes Registration (5 minutes)

**File:** `routes/web.php`

Routes already exist:
```php
// Line 129-130
Route::get('/reviewers', [\App\Http\Controllers\Chair\ChairController::class, 'listReviewers'])->name('reviewers.index');
Route::get('/reviewers/{id}', [\App\Http\Controllers\Chair\ChairController::class, 'showReviewer'])->name('reviewers.show');
```

✅ No changes needed!

---

### Task 5: Update Dashboard Menu (5 minutes)

**File:** `resources/views/chair/dashboard.blade.php`

Change line ~395 from:
```html
<button onclick="alert('Chức năng đang phát triển')"
```

To:
```html
<a href="{{ route('chair.reviewers.index') }}"
```

---

### Task 6: Testing (30 minutes)

Test scenarios:
1. Click "Quản lý reviewer" → List appears
2. View list → Check statistics accuracy
3. Click reviewer → Profile page loads
4. Check assignment history → Data correct
5. Navigate back → No errors
6. Search functionality works
7. Responsive on mobile

---

## 📊 ESTIMATED TIME

| Task | Time |
|------|------|
| Backend methods | 1 hour |
| List view | 1 hour |
| Profile view | 1 hour |
| Routes & menu | 10 min |
| Testing | 30 min |
| **TOTAL** | **3h 40min** |

---

## 🎯 SUCCESS CRITERIA

- ✅ Reviewers list displays correctly
- ✅ Statistics are accurate
- ✅ Profile page shows complete info
- ✅ Assignment history is correct
- ✅ Menu navigation works
- ✅ No console errors
- ✅ Responsive design
- ✅ Cache cleared

---

## 📝 IMPLEMENTATION ORDER

1. ✅ Create this plan document
2. ⏳ Add backend methods to ChairController
3. ⏳ Create reviewers/index.blade.php
4. ⏳ Create reviewers/show.blade.php
5. ⏳ Update dashboard menu link
6. ⏳ Clear cache & test
7. ⏳ Document completion

---

**Ready to start? Reply "1" to begin implementation!** 🚀
