# 🔧 Column Name Fix - TieuBan.title
**Date:** Oct 5, 2025  
**Time:** 18:35

## ❌ Error Found

```sql
SQLSTATE[42S22]: Column not found: 1054 
Unknown column 'tb.track_name' in 'field list'
```

**Location:** `ChairController::listReviewers()` line 1303

## 🔍 Root Cause

**Code assumed wrong column name:**
```php
// ❌ WRONG
->pluck('tb.track_name')
```

**Actual database schema:**
```php
// Table: TieuBan
Schema::create('TieuBan', function (Blueprint $table) {
    $table->id('track_id');
    $table->unsignedBigInteger('conference_id');
    $table->string('title', 200);  // ✅ Column is 'title'
    $table->unsignedBigInteger('chair_id')->nullable();
});
```

## ✅ Fix Applied

```php
// ✅ CORRECT
->pluck('tb.title')
```

**Changed in:**
- `app/Http/Controllers/Chair/ChairController.php` line 1303

## 📝 Lesson

**Always check migration files before coding!**

```bash
# Check table structure
php artisan migrate:status

# Review migration file
cat database/migrations/2025_10_04_113039_create_hoi_thao_tables.php
```

## ✅ Status

**Fixed:** ✅  
**Tested:** Pending (needs refresh)

---

*Quick fix applied - Oct 5, 2025, 18:35*
