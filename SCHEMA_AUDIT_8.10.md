# 🔍 DATABASE SCHEMA AUDIT - Phase 8.10 COI Controllers

**Date:** January 5, 2025  
**Status:** CRITICAL SCHEMA MISMATCHES FOUND

---

## ❌ SCHEMA MISMATCHES DISCOVERED

### 1. **LoaiCOI Table**

**Migration Schema:**
```php
Schema::create('LoaiCOI', function (Blueprint $table) {
    $table->string('coi_code', 30)->primary();
    $table->string('coi_name', 100);
    // ❌ NO 'description' column
});
```

**Code References (WRONG):**
- `'lc.description as coi_description'` - Line 57, 109 in Chair/COIController.php
- Used in: `index()`, `show()` methods

**Fix:** Remove `description` references, use only `coi_name`

---

### 2. **XuLyCOI Table**

**Migration Schema:**
```php
Schema::create('XuLyCOI', function (Blueprint $table) {
    $table->id('decision_id');              // ✅ PK
    $table->unsignedBigInteger('coi_id');
    $table->unsignedBigInteger('chair_id'); // ✅ NOT 'resolved_by'
    $table->enum('decision', ['CONFIRMED', 'REJECTED']); // ✅ NOT 'resolution_code'
    $table->string('note', 255)->nullable();
    $table->timestamp('decided_at')->useCurrent(); // ✅ NOT 'resolved_at'
});
```

**Code References (ALL WRONG):**
- `'xc.resolution_id'` → should be `'xc.decision_id'`
- `'xc.resolution_code'` → should be `'xc.decision'`
- `'xc.resolved_by'` → should be `'xc.chair_id'`
- `'xc.resolved_at'` → should be `'xc.decided_at'`

**Join Error:**
- `->leftJoin('LoaiXuLyCOI as lxc', ...)` → Table `LoaiXuLyCOI` **DOES NOT EXIST**

---

### 3. **HoiThao Table**

**Migration Schema:**
```php
Schema::create('HoiThao', function (Blueprint $table) {
    $table->id('conference_id');
    $table->string('title', 255);
    $table->smallInteger('year');
    // ❌ NO 'code' column
    // ❌ NO 'conference_code' column
});
```

**Code References (WRONG):**
- `'ht.code as conference_code'` - Line 103 in Chair/COIController.php

**Fix:** Use `'ht.title as conference_code'` (already fixed in Bug 001)

---

### 4. **PhanCongPhanBien vs PhanCong**

**Migration:**
- Table name: `PhanCongPhanBien`
- PK: `assignment_id`

**Code References (MIGHT BE WRONG):**
- Uses `PhanCongPhanBien` in Chair controller
- BUT Reviewer controller uses `PhanCong`

**Need to verify:** Which table name is correct?

---

## 🔧 REQUIRED FIXES

### Files Affected:
1. `app/Http/Controllers/Chair/COIController.php`
2. `app/Http/Controllers/Reviewer/COIController.php`

### Fix Summary:

1. **Remove all `lc.description` references** (column doesn't exist)
2. **Fix all XuLyCOI column names:**
   - `resolution_id` → `decision_id`
   - `resolution_code` → `decision`
   - `resolved_by` → `chair_id`
   - `resolved_at` → `decided_at`
3. **Remove `LoaiXuLyCOI` join** (table doesn't exist)
4. **Fix `ht.code`** → `ht.title` (already done)
5. **Verify table name:** `PhanCongPhanBien` vs `PhanCong`

---

## 📊 COMPLEXITY ASSESSMENT

**Severity:** HIGH - Multiple schema mismatches  
**Impact:** Complete feature broken  
**Estimated Fix Time:** 30-45 minutes  
**Files to Modify:** 2 controllers  
**Lines to Change:** ~40-50 lines

---

## 💡 ROOT CAUSE

**Why this happened:**
1. Developer designed ideal schema in code
2. But migration was created differently
3. No verification between migration and code
4. Migration ran first (creating actual schema)
5. Code written assuming different schema
6. Result: Total mismatch

**Should have done:**
1. Read migration files FIRST
2. Write code based on ACTUAL schema
3. Or: Update migration to match design
4. Run: `php artisan migrate:fresh` to test

---

*Schema Audit completed: January 5, 2025*
