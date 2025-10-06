# 🔧 Database Column Name Corrections
**Date:** Oct 5, 2025  
**File:** `app/Http/Controllers/Chair/ChairController.php`

## 🎯 Overview

Fixed multiple column name mismatches between code and database schema.

## ❌ Issues Found

### 1. TieuBan.track_name → title
```sql
-- ❌ Code used: tb.track_name
-- ✅ Actual column: tb.title
```

### 2. BaiBao.status_id → status_code
```sql
-- ❌ Code used: bb.status_id
-- ✅ Actual column: bb.status_code
```

### 3. TrangThaiBaiBao.status_id → status_code
```sql
-- ❌ Code used: tt.status_id (in join)
-- ✅ Actual column: tt.status_code
```

### 4. BaiBao.submitted_by → submitter_id
```sql
-- ❌ Code used: bb.submitted_by
-- ✅ Actual column: bb.submitter_id
```

## ✅ Fixes Applied

### Fix 1: TieuBan Column
**Line:** ~1303  
**Change:**
```php
// Before:
->pluck('tb.track_name')

// After:
->pluck('tb.title')
```

### Fix 2: BaiBao Status Column
**Lines:** 790, 987, and others  
**Change:**
```php
// Before:
->leftJoin('TrangThaiBaiBao as tt', 'bb.status_id', '=', 'tt.status_id')

// After:
->leftJoin('TrangThaiBaiBao as tt', 'bb.status_code', '=', 'tt.status_code')
```

### Fix 3: BaiBao Submitter Column
**Lines:** 789, 926  
**Change:**
```php
// Before:
->join('NguoiDung as nd', 'bb.submitted_by', '=', 'nd.user_id')

// After:
->join('NguoiDung as nd', 'bb.submitter_id', '=', 'nd.user_id')
```

## 📊 Correct Schema Reference

### BaiBao Table
```sql
CREATE TABLE BaiBao (
    paper_id BIGINT PRIMARY KEY,
    conference_id BIGINT,
    track_id BIGINT,
    submitter_id BIGINT,        -- ✅ Not 'submitted_by'
    title VARCHAR(500),
    abstract LONGTEXT,
    current_version_id BIGINT,
    status_code VARCHAR(30),    -- ✅ Not 'status_id'
    created_at TIMESTAMP
);
```

### TieuBan Table
```sql
CREATE TABLE TieuBan (
    track_id BIGINT PRIMARY KEY,
    conference_id BIGINT,
    title VARCHAR(200),         -- ✅ Not 'track_name'
    chair_id BIGINT
);
```

### TrangThaiBaiBao Table
```sql
CREATE TABLE TrangThaiBaiBao (
    status_code VARCHAR(30) PRIMARY KEY,  -- ✅ Not 'status_id'
    status_name VARCHAR(100),
    description VARCHAR(255)
);
```

## 🔍 How These Were Found

### Method 1: Error Messages
```
SQLSTATE[42S22]: Column not found: 1054 
Unknown column 'tb.track_name' in 'field list'
```

### Method 2: Checking Migration Files
```bash
# Check actual schema
cat database/migrations/2025_10_04_*.php
```

### Method 3: Searching Code
```bash
# Find potential issues
Select-String -Pattern "track_name|status_id|submitted_by" -Path "app\Http\Controllers\*.php"
```

## 💡 Prevention Tips

### 1. Always Check Migrations First
```bash
# Before coding, review schema
php artisan migrate:status
cat database/migrations/*.php
```

### 2. Use Eloquent Models
```php
// ✅ Models know column names
$paper = BaiBao::find($id);
echo $paper->submitter_id;  // IDE autocomplete helps!

// ❌ Raw queries prone to typos
DB::table('BaiBao')->where('submitted_by', $id);  // Typo!
```

### 3. Create Schema Documentation
```markdown
# SCHEMA.md

## BaiBao
- submitter_id (not submitted_by)
- status_code (not status_id)

## TieuBan
- title (not track_name)
```

### 4. Use Database Inspection Tools
```php
// Laravel Tinker
php artisan tinker
Schema::getColumnListing('BaiBao');
```

## 📝 Complete Column Mapping

| Table | ❌ Wrong Name | ✅ Correct Name | Type |
|-------|--------------|----------------|------|
| BaiBao | submitted_by | submitter_id | FK to NguoiDung |
| BaiBao | status_id | status_code | FK to TrangThaiBaiBao |
| BaiBao | submission_date | created_at | Timestamp |
| TieuBan | track_name | title | String |
| TrangThaiBaiBao | status_id (PK) | status_code | String PK |
| PhanBien | overall_score | score | TinyInt |
| PhanBien | recommendation | recommendation_code | FK |
| PhanBien | submission_date | submitted_at | Timestamp |

## 🧪 Testing

### After Fixes, Test:

```bash
# 1. Clear Laravel cache
php artisan cache:clear
php artisan config:clear

# 2. Refresh browser
Ctrl + Shift + F5

# 3. Test URLs:
http://localhost/.../chair/reviewers
http://localhost/.../chair/papers
http://localhost/.../chair/papers/{id}
```

### Expected Results:
- ✅ No SQL column errors
- ✅ All pages load successfully
- ✅ Data displays correctly
- ✅ Statistics calculate properly

## ✅ Status

**Total Column Fixes:** 4 column name corrections  
**Files Modified:** 1 file (ChairController.php)  
**Global Replaces:** 4 search-and-replace operations  
**Status:** ✅ **COMPLETE**

## 📚 Related Docs

- `DATABASE_SCHEMA_FIX.md` - Previous schema fixes
- `REVIEWER_LIST_FIX.md` - Reviewer properties fix
- Migration files in `database/migrations/`

---

*All column names corrected - Oct 5, 2025, 18:40*

## 🔗 Quick Reference

When in doubt, check these tables:

```sql
-- BaiBao
submitter_id, status_code, created_at

-- TieuBan  
title (not track_name)

-- PhanBien
score, recommendation_code, submitted_at

-- TrangThaiBaiBao
status_code (PK)
```
