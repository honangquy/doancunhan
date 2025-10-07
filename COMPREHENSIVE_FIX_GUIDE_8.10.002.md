# 🛠️ COMPREHENSIVE FIX GUIDE - Phase 8.10 COI Controllers Schema Errors

**Date:** January 5, 2025  
**Bug ID:** BUG-8.10-002  
**Severity:** CRITICAL  
**Files:** Chair/COIController.php, Reviewer/COIController.php

---

## 🎯 EXECUTIVE SUMMARY

**Problem:** Code references database columns that don't exist  
**Root Cause:** Code written based on assumed schema, not actual migration  
**Solution:** Update all queries to match actual database schema  
**Estimated Time:** 30-45 minutes  

---

## 📋 SCHEMA CORRECTIONS NEEDED

### 1. LoaiCOI Table
**Remove:** `lc.description` (column doesn't exist)  
**Use instead:** `lc.coi_name` only

### 2. XuLyCOI Table
**Column Mapping:**
| Code Uses (WRONG) | Actual Schema (CORRECT) |
|-------------------|-------------------------|
| `resolution_id` | `decision_id` |
| `resolution_code` | `decision` |
| `resolved_by` | `chair_id` |
| `resolved_at` | `decided_at` |

### 3. LoaiXuLyCOI Table
**Problem:** Table DOES NOT EXIST  
**Solution:** Remove all joins to this table

### 4. HoiThao Table  
**Fix:** `ht.code` → `ht.title` (already fixed in Bug 001)

---

## 🔧 DETAILED FIXES

## FILE 1: app/Http/Controllers/Chair/COIController.php

### Fix 1: index() method (Lines 38-62)

**FIND (Line 45-62):**
```php
            ->select(
                'c.coi_id',
                'c.paper_id',
                'c.reviewer_id',
                'c.coi_code',
                'c.source_type',
                'c.evidence',
                'c.created_at',
                'bb.title as paper_title',
                'reviewer.full_name as reviewer_name',
                'reviewer.email as reviewer_email',
                'author.full_name as author_name',
                'lc.coi_name',
                'lc.description as coi_description',
                'xc.resolution_id',
                'xc.resolution_code',
                'xc.resolved_at'
            )
```

**REPLACE WITH:**
```php
            ->select(
                'c.coi_id',
                'c.paper_id',
                'c.reviewer_id',
                'c.coi_code',
                'c.source_type',
                'c.evidence',
                'c.created_at',
                'bb.title as paper_title',
                'reviewer.full_name as reviewer_name',
                'reviewer.email as reviewer_email',
                'author.full_name as author_name',
                'lc.coi_name',
                'xc.decision_id',
                'xc.decision',
                'xc.decided_at'
            )
```

**Changes:**
- ❌ Removed: `'lc.description as coi_description'`
- ✅ Changed: `'xc.resolution_id'` → `'xc.decision_id'`
- ✅ Changed: `'xc.resolution_code'` → `'xc.decision'`
- ✅ Changed: `'xc.resolved_at'` → `'xc.decided_at'`

---

### Fix 2: index() statistics (Lines 66-71)

**FIND:**
```php
        $stats = [
            'total' => $coiCases->count(),
            'unresolved' => $coiCases->whereNull('resolution_id')->count(),
            'resolved' => $coiCases->whereNotNull('resolution_id')->count(),
            'declared' => $coiCases->where('source_type', 'DECLARED')->count(),
            'detected' => $coiCases->where('source_type', 'DETECTED')->count(),
        ];
```

**REPLACE WITH:**
```php
        $stats = [
            'total' => $coiCases->count(),
            'unresolved' => $coiCases->whereNull('decision_id')->count(),
            'resolved' => $coiCases->whereNotNull('decision_id')->count(),
            'declared' => $coiCases->where('source_type', 'DECLARED')->count(),
            'detected' => $coiCases->where('source_type', 'DETECTED')->count(),
        ];
```

**Changes:**
- ✅ Changed: `'resolution_id'` → `'decision_id'` (2 places)

---

### Fix 3: show() method (Lines 86-127)

**FIND (Line 92-97):**
```php
            ->leftJoin('XuLyCOI as xc', 'c.coi_id', '=', 'xc.coi_id')
            ->leftJoin('LoaiXuLyCOI as lxc', 'xc.resolution_code', '=', 'lxc.resolution_code')
            ->leftJoin('NguoiDung as resolver', 'xc.resolved_by', '=', 'resolver.user_id')
```

**REPLACE WITH:**
```php
            ->leftJoin('XuLyCOI as xc', 'c.coi_id', '=', 'xc.coi_id')
            ->leftJoin('NguoiDung as resolver', 'xc.chair_id', '=', 'resolver.user_id')
```

**Changes:**
- ❌ Removed entire line: `->leftJoin('LoaiXuLyCOI as lxc', ...)`
- ✅ Changed: `'xc.resolved_by'` → `'xc.chair_id'`

---

**FIND (Line 98-122):**
```php
            ->select(
                'c.*',
                'bb.title as paper_title',
                'bb.abstract',
                'bb.keywords',
                'bb.status_code as paper_status',
                'ht.conference_id',
                'ht.code as conference_code',
                'ht.title as conference_name',
                'reviewer.full_name as reviewer_name',
                'reviewer.email as reviewer_email',
                'reviewer.organization as reviewer_org',
                'author.full_name as author_name',
                'author.email as author_email',
                'lc.coi_name',
                'lc.description as coi_description',
                'xc.resolution_id',
                'xc.resolution_code',
                'xc.resolved_at',
                'xc.note as resolution_note',
                'lxc.resolution_name',
                'lxc.description as resolution_description',
                'resolver.full_name as resolved_by_name'
            )
```

**REPLACE WITH:**
```php
            ->select(
                'c.*',
                'bb.title as paper_title',
                'bb.abstract',
                'bb.keywords',
                'bb.status_code as paper_status',
                'ht.conference_id',
                'ht.title as conference_code',
                'ht.title as conference_name',
                'reviewer.full_name as reviewer_name',
                'reviewer.email as reviewer_email',
                'reviewer.organization as reviewer_org',
                'author.full_name as author_name',
                'author.email as author_email',
                'lc.coi_name',
                'xc.decision_id',
                'xc.decision',
                'xc.decided_at',
                'xc.note as resolution_note',
                'resolver.full_name as resolved_by_name'
            )
```

**Changes:**
- ✅ Changed: `'ht.code as conference_code'` → `'ht.title as conference_code'`
- ❌ Removed: `'lc.description as coi_description'`
- ✅ Changed: `'xc.resolution_id'` → `'xc.decision_id'`
- ✅ Changed: `'xc.resolution_code'` → `'xc.decision'`
- ✅ Changed: `'xc.resolved_at'` → `'xc.decided_at'`
- ❌ Removed: `'lxc.resolution_name'`
- ❌ Removed: `'lxc.description as resolution_description'`

---

### Fix 4: resolveForm() method (Lines 172-196)

**FIND (Line 189-196):**
```php
            ->select(
                'c.*',
                'bb.title as paper_title',
                'bb.conference_id',
                'ht.title as conference_name',
                'reviewer.full_name as reviewer_name',
                'lc.coi_name',
                'xc.resolution_id'
            )
```

**REPLACE WITH:**
```php
            ->select(
                'c.*',
                'bb.title as paper_title',
                'bb.conference_id',
                'ht.title as conference_name',
                'reviewer.full_name as reviewer_name',
                'lc.coi_name',
                'xc.decision_id'
            )
```

**Changes:**
- ✅ Changed: `'xc.resolution_id'` → `'xc.decision_id'`

---

**FIND (Line 204):**
```php
        if ($coi->resolution_id) {
```

**REPLACE WITH:**
```php
        if ($coi->decision_id) {
```

---

**FIND (Line 209-212):**
```php
        // Get resolution types
        $resolutionTypes = DB::table('LoaiXuLyCOI')
            ->orderBy('resolution_name')
            ->get();
```

**REPLACE WITH:**
```php
        // Get resolution types (hardcoded since no LoaiXuLyCOI table)
        $resolutionTypes = collect([
            (object)['decision' => 'CONFIRMED', 'decision_name' => 'Xác nhận COI', 'description' => 'Xác nhận xung đột lợi ích và xóa phân công reviewer'],
            (object)['decision' => 'REJECTED', 'decision_name' => 'Từ chối COI', 'description' => 'Từ chối khai báo COI, cho phép reviewer tiếp tục review']
        ]);
```

**Changes:**
- ❌ Removed: Query to non-existent `LoaiXuLyCOI` table
- ✅ Added: Hardcoded resolution options based on ENUM

---

### Fix 5: resolve() method (Lines 218-280)

**FIND (Line 222):**
```php
        $validated = $request->validate([
            'resolution_code' => 'required|exists:LoaiXuLyCOI,resolution_code',
            'note' => 'nullable|string|max:500'
        ]);
```

**REPLACE WITH:**
```php
        $validated = $request->validate([
            'decision' => 'required|in:CONFIRMED,REJECTED',
            'note' => 'nullable|string|max:500'
        ]);
```

---

**FIND (Line 228-233):**
```php
        $coi = DB::table('COI')
            ->leftJoin('XuLyCOI', 'COI.coi_id', '=', 'XuLyCOI.coi_id')
            ->where('COI.coi_id', $coiId)
            ->select('COI.*', 'XuLyCOI.resolution_id')
            ->first();
```

**REPLACE WITH:**
```php
        $coi = DB::table('COI')
            ->leftJoin('XuLyCOI', 'COI.coi_id', '=', 'XuLyCOI.coi_id')
            ->where('COI.coi_id', $coiId)
            ->select('COI.*', 'XuLyCOI.decision_id')
            ->first();
```

---

**FIND (Line 238):**
```php
        if ($coi->resolution_id) {
```

**REPLACE WITH:**
```php
        if ($coi->decision_id) {
```

---

**FIND (Line 253-259):**
```php
            DB::table('XuLyCOI')->insert([
                'coi_id' => $coiId,
                'resolution_code' => $validated['resolution_code'],
                'resolved_by' => $userId,
                'note' => $validated['note'],
                'resolved_at' => Carbon::now()
            ]);
```

**REPLACE WITH:**
```php
            DB::table('XuLyCOI')->insert([
                'coi_id' => $coiId,
                'chair_id' => $userId,
                'decision' => $validated['decision'],
                'note' => $validated['note'],
                'decided_at' => Carbon::now()
            ]);
```

---

**FIND (Line 261-266):**
```php
            // If resolution is REMOVE_ASSIGNMENT, delete the assignment
            if ($validated['resolution_code'] === 'REMOVE_ASSIGNMENT') {
                DB::table('PhanCongPhanBien')
                    ->where('paper_id', $coi->paper_id)
                    ->where('reviewer_id', $coi->reviewer_id)
                    ->delete();
            }
```

**REPLACE WITH:**
```php
            // If decision is CONFIRMED, delete the assignment
            if ($validated['decision'] === 'CONFIRMED') {
                DB::table('PhanCongPhanBien')
                    ->where('paper_id', $coi->paper_id)
                    ->where('reviewer_id', $coi->reviewer_id)
                    ->delete();
            }
```

---

### Fix 6: statistics() method (Lines 286-353)

**ALL instances of:**
- `resolution_id` → `decision_id`
- `resolution_code` → `decision`
- `resolved_at` → `decided_at`
- `resolved_by` → `chair_id`

**Find and replace in this method:**
```php
// Line 291
'unresolved' => $cois->whereNull('resolution_id')->count(),
→ 'unresolved' => $cois->whereNull('decision_id')->count(),

// Line 292
'resolved' => $cois->whereNotNull('resolution_id')->count(),
→ 'resolved' => $cois->whereNotNull('decision_id')->count(),

// Line 303-312 (by_resolution query)
->join('XuLyCOI', 'COI.coi_id', '=', 'XuLyCOI.coi_id')
->join('LoaiXuLyCOI', 'XuLyCOI.resolution_code', '=', 'LoaiXuLyCOI.resolution_code')
->select('LoaiXuLyCOI.resolution_name', DB::raw('count(*) as count'))
->groupBy('LoaiXuLyCOI.resolution_name')

// REPLACE WITH:
->join('XuLyCOI', 'COI.coi_id', '=', 'XuLyCOI.coi_id')
->select('XuLyCOI.decision', DB::raw('count(*) as count'))
->groupBy('XuLyCOI.decision')

// Line 328
'unresolved_cois' => $unresolvedCois
→ Keep this, just ensure query uses decision_id
```

---

## FILE 2: app/Http/Controllers/Reviewer/COIController.php

### Fix 1: index() method (Line 44-53)

**FIND:**
```php
                'HoiThao.title as conference_code',
                'HoiThao.title as conference_title',
                'LoaiCOI.coi_code',
                'LoaiCOI.coi_name',
                'COI.evidence',
                'COI.note',
                'COI.detected_at',
                'COI.created_at',
                'XuLyCOI.resolution_id',
                'LoaiXuLyCOI.resolution_name',
                'XuLyCOI.resolved_at'
```

**REPLACE WITH:**
```php
                'HoiThao.title as conference_code',
                'HoiThao.title as conference_title',
                'LoaiCOI.coi_code',
                'LoaiCOI.coi_name',
                'COI.evidence',
                'COI.note',
                'COI.detected_at',
                'COI.created_at',
                'XuLyCOI.decision_id',
                'XuLyCOI.decision',
                'XuLyCOI.decided_at'
```

---

**FIND (Line 35-36):**
```php
            ->leftJoin('XuLyCOI', 'COI.coi_id', '=', 'XuLyCOI.coi_id')
            ->leftJoin('LoaiXuLyCOI', 'XuLyCOI.resolution_code', '=', 'LoaiXuLyCOI.resolution_code')
```

**REPLACE WITH:**
```php
            ->leftJoin('XuLyCOI', 'COI.coi_id', '=', 'XuLyCOI.coi_id')
```

---

### Fix 2: show() method (Line 198-221)

**FIND (Line 199-201):**
```php
            ->leftJoin('XuLyCOI', 'COI.coi_id', '=', 'XuLyCOI.coi_id')
            ->leftJoin('LoaiXuLyCOI', 'XuLyCOI.resolution_code', '=', 'LoaiXuLyCOI.resolution_code')
            ->leftJoin('NguoiDung', 'XuLyCOI.resolved_by', '=', 'NguoiDung.user_id')
```

**REPLACE WITH:**
```php
            ->leftJoin('XuLyCOI', 'COI.coi_id', '=', 'XuLyCOI.coi_id')
            ->leftJoin('NguoiDung', 'XuLyCOI.chair_id', '=', 'NguoiDung.user_id')
```

---

**FIND (Line 212-221):**
```php
                'HoiThao.title as conference_code',
                'HoiThao.title as conference_title',
                'LoaiCOI.coi_name',
                'LoaiCOI.description as coi_description',
                'XuLyCOI.resolution_id',
                'LoaiXuLyCOI.resolution_name',
                'LoaiXuLyCOI.description as resolution_description',
                'XuLyCOI.note as resolution_note',
                'XuLyCOI.resolved_at',
                'NguoiDung.full_name as resolved_by_name'
```

**REPLACE WITH:**
```php
                'HoiThao.title as conference_code',
                'HoiThao.title as conference_title',
                'LoaiCOI.coi_name',
                'XuLyCOI.decision_id',
                'XuLyCOI.decision',
                'XuLyCOI.note as resolution_note',
                'XuLyCOI.decided_at',
                'NguoiDung.full_name as resolved_by_name'
```

---

### Fix 3: retract() method (Line 254-260)

**FIND:**
```php
        $coi = DB::table('COI')
            ->leftJoin('XuLyCOI', 'COI.coi_id', '=', 'XuLyCOI.coi_id')
            ->where('COI.coi_id', $coiId)
            ->where('COI.reviewer_id', $userId)
            ->where('COI.source_type', 'DECLARED')
            ->select('COI.*', 'XuLyCOI.resolution_id')
            ->first();
```

**REPLACE WITH:**
```php
        $coi = DB::table('COI')
            ->leftJoin('XuLyCOI', 'COI.coi_id', '=', 'XuLyCOI.coi_id')
            ->where('COI.coi_id', $coiId)
            ->where('COI.reviewer_id', $userId)
            ->where('COI.source_type', 'DECLARED')
            ->select('COI.*', 'XuLyCOI.decision_id')
            ->first();
```

---

**FIND (Line 267):**
```php
        if ($coi->resolution_id) {
```

**REPLACE WITH:**
```php
        if ($coi->decision_id) {
```

---

## 📝 VIEWS TO UPDATE

Views also need updates to match new variable names:

### 1. chair/coi/index.blade.php
- Change: `$coi->resolution_id` → `$coi->decision_id`

### 2. chair/coi/show.blade.php
- Change: `$coi->resolution_id` → `$coi->decision_id`
- Change: `$coi->resolution_name` → Display based on `$coi->decision`
- Remove: `$coi->coi_description` references

### 3. chair/coi/resolve.blade.php
- Change form: `name="resolution_code"` → `name="decision"`
- Change: Loop through `$resolutionTypes` using `decision` instead of `resolution_code`

### 4. reviewer/coi/index.blade.php
- Change: `$coi->resolution_id` → `$coi->decision_id`
- Change: `$coi->resolution_name` → Display based on `$coi->decision`

### 5. reviewer/coi/show.blade.php
- Change: `$coi->resolution_id` → `$coi->decision_id`
- Change: `$coi->resolution_name` → Display based on `$coi->decision`
- Remove: `$coi->coi_description` references
- Remove: `$coi->resolution_description` references

---

## ⚠️ BREAKING CHANGES

**Database Schema Change:**
- `XuLyCOI.decision` is ENUM('CONFIRMED', 'REJECTED')
- No longer uses separate `LoaiXuLyCOI` lookup table
- Simpler but less flexible

**Semantic Change:**
- Old: "REMOVE_ASSIGNMENT", "ALLOW_WITH_DISCLOSURE", "REASSIGN", "OTHER"
- New: "CONFIRMED" (COI exists, remove assignment), "REJECTED" (COI invalid, keep assignment)

---

## 🧪 TESTING AFTER FIX

1. Clear cache: `php artisan cache:clear`
2. Test Chair COI list
3. Test Chair COI detail
4. Test Chair COI resolution
5. Test Reviewer COI list
6. Test Reviewer COI declaration
7. Test Reviewer COI retraction

---

## 💡 PREVENTION FOR FUTURE

**Checklist before writing queries:**
1. Read migration file FIRST
2. Note exact column names
3. Check if lookup tables exist
4. Verify foreign key relationships
5. Test query in Tinker before controller
6. Run one method at a time

**Better: Use Eloquent Models**
```php
// Instead of DB::table()
$coi = COI::with('paper', 'reviewer', 'coiType', 'resolution')->find($id);
```

---

*Fix Guide created: January 5, 2025*  
*Estimated time to apply all fixes: 30-45 minutes*
