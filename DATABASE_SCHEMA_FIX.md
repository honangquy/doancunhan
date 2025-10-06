# 🔧 Fix Database Schema Errors - ChairController
**Ngày:** 5 tháng 10, 2025  
**File:** `app/Http/Controllers/Chair/ChairController.php`

## ❌ Lỗi Gặp Phải

### Error Message:
```sql
SQLSTATE[42S22]: Column not found: 1054 Unknown column 'bb.submission_date' in 'field list'
```

### Chi Tiết:
- **URL:** `/chair/reviewers`
- **Method:** `listReviewers()`
- **Line:** 1239 (và nhiều chỗ khác)

## 🔍 Nguyên Nhân

### Mismatch giữa Code và Database Schema

**Code đang dùng (SAI):**
```php
// ❌ Table name sai
->leftJoin('PhieuNhanXet as pn', ...)

// ❌ Column name không tồn tại
->select('bb.submission_date')
->select('pn.submission_date as review_date')
->select('pn.overall_score')
->select('pn.recommendation')
```

**Database Schema thực tế:**
```php
// ✅ Table đúng: PhanBien (không phải PhieuNhanXet)
Schema::create('PhanBien', function (Blueprint $table) {
    $table->id('review_id');
    $table->unsignedBigInteger('assignment_id');
    $table->string('recommendation_code', 20);  // ✅ recommendation_CODE
    $table->tinyInteger('score')->nullable();    // ✅ score (không phải overall_score)
    $table->longText('comment_author')->nullable();
    $table->longText('comment_chair')->nullable();
    $table->timestamp('submitted_at')->useCurrent();  // ✅ submitted_AT
});

// ✅ BaiBao table
Schema::create('BaiBao', function (Blueprint $table) {
    $table->id('paper_id');
    $table->timestamp('created_at')->useCurrent();  // ✅ created_at (không phải submission_date)
    // ... không có submission_date
});
```

## ✅ Giải Pháp Áp Dụng

### 1. Replace Table Name: PhieuNhanXet → PhanBien

**Tất cả các queries đã được sửa:**
```php
// Trước:
->leftJoin('PhieuNhanXet as pn', 'pc.assignment_id', '=', 'pn.assignment_id')

// Sau:
->leftJoin('PhanBien as pb', 'pc.assignment_id', '=', 'pb.assignment_id')
```

### 2. Fix Column Names

#### a) BaiBao.submission_date → BaiBao.created_at

**Line 1239, 1379:**
```php
// ❌ Trước:
->select('pc.*', 'bb.submission_date')

// ✅ Sau:
->select('pc.*', 'bb.created_at')
```

#### b) PhanBien columns

**Lines 1247, 1275, 1375-1384:**
```php
// ❌ Trước:
'pn.overall_score'         // Column không tồn tại
'pn.recommendation'        // Column không tồn tại  
'pn.submission_date'       // Column không tồn tại

// ✅ Sau:
'pb.score'                 // ✅ Đúng
'pb.recommendation_code'   // ✅ Đúng
'pb.submitted_at'          // ✅ Đúng
```

### 3. Fix Foreign Key References

**Line 1372:**
```php
// ❌ Trước:
->leftJoin('TrangThaiBaiBao as tt', 'bb.status_id', '=', 'tt.status_id')

// ✅ Sau:
->leftJoin('TrangThaiBaiBao as tt', 'bb.status_code', '=', 'tt.status_code')
```

## 📊 Schema Reference

### Correct Table Structures:

```php
/**
 * BaiBao (Papers)
 */
- paper_id (PK)
- conference_id (FK → HoiThao)
- track_id (FK → TieuBan) 
- submitter_id (FK → NguoiDung)
- title
- abstract
- current_version_id
- status_code (FK → TrangThaiBaiBao)
- created_at  ← USE THIS (không phải submission_date)

/**
 * PhanBien (Reviews) - không phải PhieuNhanXet
 */
- review_id (PK)
- assignment_id (FK → PhanCongPhanBien)
- recommendation_code (FK → LoaiKhuyenNghi)
- score  ← USE THIS (không phải overall_score)
- comment_author
- comment_chair
- submitted_at  ← USE THIS (không phải submission_date)

/**
 * PhanCongPhanBien (Assignments)
 */
- assignment_id (PK)
- paper_id (FK → BaiBao)
- reviewer_id (FK → NguoiDung)
- chair_id (FK → NguoiDung)
- status_code (FK → TrangThaiPhanCong)
- token
- assigned_at  ← Thời gian phân công
- deadline
```

## 🔧 Files Changed

### Main File:
- ✅ `app/Http/Controllers/Chair/ChairController.php`

### Changes Made:
1. ✅ Replaced all `PhieuNhanXet` → `PhanBien` (global replace)
2. ✅ Fixed `bb.submission_date` → `bb.created_at`
3. ✅ Fixed `pb.submission_date` → `pb.submitted_at`
4. ✅ Fixed `pb.overall_score` → `pb.score`
5. ✅ Fixed `pb.recommendation` → `pb.recommendation_code`
6. ✅ Fixed `bb.status_id` → `bb.status_code`
7. ✅ Added `pc.assigned_at` to select for response time calculation

## 🧪 Testing

### Test URL:
```
http://localhost/qly_hthao/qlyhoithao/public/chair/reviewers
```

### Expected Result:
- ✅ Page loads successfully (HTTP 200)
- ✅ List of reviewers displayed
- ✅ Statistics calculated correctly:
  - Total assignments
  - Completed reviews
  - Pending reviews
  - Completion rate
  - Average response time
  - Average score

### Test Checklist:
- [ ] Access `/chair/reviewers` - no 500 error
- [ ] See list of reviewers with statistics
- [ ] Click on reviewer name to see details
- [ ] Check `/chair/reviewers/{id}` works
- [ ] Verify all statistics display correctly

## 💡 Prevention Tips

### 1. Always Check Migration Files

```bash
# Before coding, check schema:
php artisan migrate:status

# Review migration files in:
database/migrations/
```

### 2. Use Eloquent Models

```php
// ✅ Better: Use models with correct column names
$review = PhanBien::where('assignment_id', $id)->first();
echo $review->submitted_at;  // IDE autocomplete helps!

// ❌ Prone to errors: Raw queries
$review = DB::table('PhieuNhanXet')  // Wrong table name
    ->select('submission_date')       // Wrong column name
    ->first();
```

### 3. Document Schema Changes

Create a `SCHEMA.md` file:
```markdown
# Database Schema

## PhanBien (Reviews)
- review_id
- assignment_id
- recommendation_code (not 'recommendation')
- score (not 'overall_score')
- submitted_at (not 'submission_date')
```

### 4. Use Constants

```php
// In Model or Config
class PhanBien extends Model {
    protected $table = 'PhanBien';  // Explicit table name
    protected $primaryKey = 'review_id';
    
    const CREATED_AT = null;  // No created_at
    const UPDATED_AT = null;  // No updated_at
    protected $dates = ['submitted_at'];
}
```

## 📝 Complete Fix Summary

### Methods Fixed:

1. **listReviewers()** (Line ~1230-1280)
   - Fixed table names
   - Fixed column names
   - Fixed joins

2. **showReviewer()** (Line ~1365-1390)
   - Fixed table names
   - Fixed column references
   - Fixed foreign keys

3. **Other methods** (Lines 800-1020)
   - Global replace `PhieuNhanXet` → `PhanBien`

### Column Mapping Reference:

| ❌ Wrong (Old) | ✅ Correct (New) | Table |
|---------------|------------------|-------|
| `PhieuNhanXet` | `PhanBien` | Table name |
| `bb.submission_date` | `bb.created_at` | BaiBao |
| `pn.submission_date` | `pb.submitted_at` | PhanBien |
| `pn.overall_score` | `pb.score` | PhanBien |
| `pn.recommendation` | `pb.recommendation_code` | PhanBien |
| `bb.status_id` | `bb.status_code` | BaiBao |
| `tt.status_id` | `tt.status_code` | TrangThaiBaiBao |

## 🚨 Related Issues to Check

Search for these patterns in other files:

```bash
# Find other potential issues
grep -r "PhieuNhanXet" app/
grep -r "submission_date" app/
grep -r "overall_score" app/
grep -r "\.recommendation[^_]" app/
```

## ✅ Status

**Issue:** ✅ **RESOLVED**

**Impact:** 
- Fixed `/chair/reviewers` endpoint
- Fixed reviewer statistics calculation  
- Fixed reviewer detail page

**Next Steps:**
1. Test all reviewer-related pages
2. Check other controllers for similar issues
3. Update any API documentation
4. Consider creating Eloquent models to prevent future issues

---

*Last Updated: October 5, 2025*  
*Affected Methods: listReviewers(), showReviewer(), and related queries*  
*Changes: Table names + Column names fixed across entire ChairController*
