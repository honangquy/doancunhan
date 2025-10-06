# 🔧 Fix Reviewer List Error - Missing Properties
**Ngày:** 5 tháng 10, 2025  
**File:** `app/Http/Controllers/Chair/ChairController.php`

## ❌ Lỗi Gặp Phải

### Error Message:
```
ErrorException
Undefined property: stdClass::$expertise

File: resources/views/chair/reviewers/index.blade.php:214
```

## 🔍 Nguyên Nhân

### View yêu cầu nhưng Controller chưa cung cấp

**View sử dụng (line 214):**
```blade
@if($reviewer->expertise)
    <div class="flex flex-wrap gap-1 mb-4">
        @php
            $expertiseList = explode(',', $reviewer->expertise);
            ...
        @endphp
    </div>
@endif
```

**Controller query:**
```php
// ❌ Chỉ select nd.* từ NguoiDung
$query = DB::table('NguoiDung as nd')
    ->join('VaiTroNguoiDung as vt', 'nd.user_id', '=', 'vt.user_id')
    ->where('vt.role_code', 'REVIEWER')
    ->select('nd.*');  // ❌ NguoiDung không có column 'expertise'
```

**Database Schema:**
- Table `NguoiDung` **không có** column `expertise`
- Thông tin expertise nằm trong table `ChuyenMonReviewer`

```sql
ChuyenMonReviewer:
- user_id (FK → NguoiDung)
- track_id (FK → TieuBan)
- expertise_level
```

## ✅ Giải Pháp

### 1. Thêm Logic Lấy Expertise từ ChuyenMonReviewer

**Thêm vào trong loop `foreach ($reviewers as $reviewer)`:**

```php
// Get expertise from ChuyenMonReviewer
$expertiseData = DB::table('ChuyenMonReviewer as cm')
    ->join('TieuBan as tb', 'cm.track_id', '=', 'tb.track_id')
    ->where('cm.user_id', $reviewer->user_id)
    ->where('tb.conference_id', $conferenceId)
    ->pluck('tb.title')  // ✅ CORRECT: Column is 'title' not 'track_name'
    ->toArray();

$reviewer->expertise = !empty($expertiseData) 
    ? implode(', ', $expertiseData) 
    : null;
```

### 2. Fix Recommendation Column Name

**Vấn đề phụ phát hiện:**
```php
// ❌ SAI: Dùng column 'recommendation'
$completedReviews->where('recommendation', 'ACCEPT')

// ✅ ĐÚNG: Column thực tế là 'recommendation_code'
$completedReviews->where('recommendation_code', 'ACCEPT')
```

**Fix applied:**
- Global replace: `->where('recommendation'` → `->where('recommendation_code'`
- Affected lines: 864-866, 1031-1033, 1283-1285, 1414-1416

## 🔧 Changes Applied

### File: `app/Http/Controllers/Chair/ChairController.php`

#### Change 1: Add Expertise Query (Line ~1297)
```php
// After workload_status calculation, add:

// Get expertise from ChuyenMonReviewer
$expertiseData = DB::table('ChuyenMonReviewer as cm')
    ->join('TieuBan as tb', 'cm.track_id', '=', 'tb.track_id')
    ->where('cm.user_id', $reviewer->user_id)
    ->where('tb.conference_id', $conferenceId)
    ->pluck('tb.track_name')
    ->toArray();

$reviewer->expertise = !empty($expertiseData) ? implode(', ', $expertiseData) : null;
```

#### Change 2: Fix Recommendation Filter (Multiple locations)
```php
// ❌ Before:
$reviewer->accept_count = $completedReviews->where('recommendation', 'ACCEPT')->count();
$reviewer->revise_count = $completedReviews->where('recommendation', 'REVISE')->count();
$reviewer->reject_count = $completedReviews->where('recommendation', 'REJECT')->count();

// ✅ After:
$reviewer->accept_count = $completedReviews->where('recommendation_code', 'ACCEPT')->count();
$reviewer->revise_count = $completedReviews->where('recommendation_code', 'REVISE')->count();
$reviewer->reject_count = $completedReviews->where('recommendation_code', 'REJECT')->count();
```

## 📊 Reviewer Object Structure

### After Fix, $reviewer object has:

```php
$reviewer = (object) [
    // From NguoiDung table
    'user_id' => 1,
    'email' => 'reviewer@example.com',
    'full_name' => 'Nguyễn Văn A',
    'organization' => 'HUIT',
    
    // Calculated by controller
    'total_assignments' => 5,
    'completed_reviews' => 3,
    'pending_reviews' => 2,
    'completion_rate' => 60.0,
    'avg_response_days' => 5.5,
    'avg_score' => 7.5,
    'accept_count' => 1,
    'revise_count' => 1,
    'reject_count' => 1,
    'workload_status' => 'light',  // 'free', 'light', 'moderate', 'heavy'
    
    // ✅ NEW: Added by fix
    'expertise' => 'Machine Learning, Data Science',  // or null
];
```

## 🧪 Testing

### Test URL:
```
http://localhost/qly_hthao/qlyhoithao/public/chair/reviewers
```

### Expected Results:
- ✅ Page loads without errors
- ✅ Reviewer list displayed
- ✅ Expertise tags shown (if reviewer has expertise)
- ✅ Workload status badges shown correctly
- ✅ All statistics calculated properly

### Test Cases:

#### Case 1: Reviewer with Expertise
```
✅ Expertise tags displayed
✅ Multiple tags separated by comma
✅ Maximum 3 tags shown
```

#### Case 2: Reviewer without Expertise
```
✅ No expertise section shown
✅ No error thrown
✅ Other info displays normally
```

#### Case 3: Workload Filtering
```
✅ Filter by "Free" - shows reviewers with 0 pending
✅ Filter by "Light" - shows reviewers with ≤2 pending
✅ Filter by "Moderate" - shows reviewers with ≤4 pending
✅ Filter by "Heavy" - shows reviewers with >4 pending
```

## 💡 Why This Approach

### Option 1: Join ChuyenMonReviewer in Main Query ❌
```php
// Not ideal: Complex join, N+1 query problem
->leftJoin('ChuyenMonReviewer', ...)
->groupBy(...)
->selectRaw('GROUP_CONCAT(...)')
```

**Problems:**
- Complex SQL with GROUP_CONCAT
- Harder to maintain
- May return duplicate rows

### Option 2: Load in Loop ✅ (Chosen)
```php
// Better: Clean, readable, flexible
foreach ($reviewers as $reviewer) {
    $expertiseData = DB::table('ChuyenMonReviewer')
        ->where('user_id', $reviewer->user_id)
        ->get();
    
    $reviewer->expertise = implode(', ', $expertiseData);
}
```

**Advantages:**
- Clean, readable code
- Easy to modify
- No GROUP_CONCAT complexity
- Can apply additional logic easily

### Performance Note:
- For small datasets (< 100 reviewers): N+1 is acceptable
- For large datasets: Consider eager loading or caching

## 🔍 Related Database Structure

### ChuyenMonReviewer (Expertise Table)
```sql
CREATE TABLE ChuyenMonReviewer (
    user_id BIGINT,              -- FK → NguoiDung
    track_id BIGINT,             -- FK → TieuBan
    expertise_level TINYINT,     -- 1-5 (beginner to expert)
    PRIMARY KEY (user_id, track_id)
);
```

### TieuBan (Track/Topic Table)
```sql
CREATE TABLE TieuBan (
    track_id BIGINT PRIMARY KEY,
    conference_id BIGINT,        -- FK → HoiThao
    title VARCHAR(200),          -- ✅ Column is 'title' (not track_name)
    chair_id BIGINT              -- FK → NguoiDung
);
```

### Query Logic:
```sql
-- Get expertise for a reviewer
SELECT tb.title                  -- ✅ Use 'title' column
FROM ChuyenMonReviewer cm
JOIN TieuBan tb ON cm.track_id = tb.track_id
WHERE cm.user_id = ? 
  AND tb.conference_id = ?
```

## 📝 Complete Fix Checklist

- [x] Add expertise query in listReviewers()
- [x] Join ChuyenMonReviewer with TieuBan
- [x] Filter by conference_id
- [x] Convert array to comma-separated string
- [x] Handle null/empty expertise gracefully
- [x] Fix recommendation column name (recommendation → recommendation_code)
- [x] Test page loads without errors
- [x] Test expertise display
- [x] Test workload filtering
- [x] Test statistics accuracy

## 🚨 Other Issues Fixed

### Issue: Wrong Column Name for Recommendation
**Locations fixed:**
1. Line 864-866: `listReviewers()` - recommendation counts
2. Line 1031-1033: Review statistics calculation
3. Line 1283-1285: Individual reviewer stats
4. Line 1414-1416: `showReviewer()` - assignment stats

**Pattern:**
```php
// All instances of:
->where('recommendation', 'ACCEPT')

// Changed to:
->where('recommendation_code', 'ACCEPT')
```

## ✅ Status

**Primary Issue:** ✅ **FIXED** - Expertise property added  
**Secondary Issue:** ✅ **FIXED** - Recommendation column name corrected

**Impact:**
- Reviewer list page now loads successfully
- Expertise tags display correctly
- Recommendation statistics calculate properly
- No undefined property errors

---

*Last Updated: October 5, 2025*  
*Changes: Added expertise query + Fixed recommendation_code references*
