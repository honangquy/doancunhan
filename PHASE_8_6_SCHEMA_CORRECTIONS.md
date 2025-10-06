# 🔍 PHASE 8.6: DATABASE SCHEMA CORRECTIONS

## ⚠️ Schema Differences Found During Implementation

### **Critical Finding: HoiThao Table**
**Chair Assignment Method:**
- ❌ **Expected:** `HoiThao.chair_id` column
- ✅ **Actual:** Chairs assigned through `VaiTroNguoiDung` table
  - Table: `VaiTroNguoiDung`
  - Columns: `user_id`, `role_code = 'CHAIR'`, `conference_id`
  - Relationship: Many chairs can be assigned to many conferences

**Query Pattern:**
```php
// ❌ WRONG:
$conferences = DB::table('HoiThao')
    ->where('chair_id', $userId)
    ->get();

// ✅ CORRECT:
$conferences = DB::table('HoiThao as ht')
    ->join('VaiTroNguoiDung as vt', function($join) use ($userId) {
        $join->on('ht.conference_id', '=', 'vt.conference_id')
             ->where('vt.user_id', '=', $userId)
             ->where('vt.role_code', '=', 'CHAIR');
    })
    ->select('ht.*')
    ->get();
```

---

### **Column Name Corrections**

| Table | ❌ Expected Column | ✅ Actual Column | Notes |
|-------|-------------------|------------------|-------|
| **BaiBao** | `submission_date` | `created_at` | Paper submission timestamp |
| **PhanBien** | `overall_score` | `score` | Review score (tinyint 1-10) |
| **PhanBien** | `recommendation` | `recommendation_code` | FK to LoaiKhuyenNghi |
| **HoiThao** | `conference_name` | `title` | Conference title |
| **HoiThao** | `chair_id` | ❌ DOES NOT EXIST | Use VaiTroNguoiDung instead |
| **PhanCongPhanBien** | `assignment_date` | `assigned_at` | When reviewer was assigned |
| **PhanCongPhanBien** | `due_date` | `deadline` | Review deadline |
| **PhanCongPhanBien** | `response_status` | `status_code` | INVITED, ACCEPTED, DECLINED, COMPLETED |

---

### **BaiBao (Papers) Table - Actual Structure**
```sql
- paper_id (PK)
- conference_id (FK)
- track_id (FK, nullable)
- submitter_id (FK to NguoiDung)
- title
- abstract (longtext, nullable)
- current_version_id (FK to PhienBanBaiBao, nullable)
- status_code (FK to TrangThaiBaiBao)
- created_at (timestamp) ← USE THIS instead of submission_date
```

---

### **PhanBien (Reviews) Table - Actual Structure**
```sql
- review_id (PK)
- assignment_id (FK to PhanCongPhanBien)
- recommendation_code (FK to LoaiKhuyenNghi) ← NOT 'recommendation'
- score (tinyint 1-10) ← NOT 'overall_score'
- comment_author (longtext)
- comment_chair (longtext)
- submitted_at (timestamp)
```

---

### **PhanCongPhanBien (Assignments) Table - Actual Structure**
```sql
- assignment_id (PK)
- paper_id (FK)
- reviewer_id (FK to NguoiDung)
- chair_id (FK to NguoiDung, nullable)
- status_code (FK to TrangThaiPhanCong)
- token (char(36), unique)
- assigned_at (timestamp) ← NOT 'assignment_date'
- deadline (date) ← NOT 'due_date'
```

---

### **VaiTroNguoiDung (User Roles) Table - Complete Structure**
```sql
- user_role_id (PK)
- user_id (FK to NguoiDung)
- role_code (FK to LoaiVaiTro: AUTHOR, REVIEWER, CHAIR, ADMIN)
- conference_id (FK to HoiThao, nullable)
```

**Important Notes:**
- `conference_id` is **nullable** - allows global roles (e.g., ADMIN)
- For CHAIR and REVIEWER roles, `conference_id` **MUST be set**
- One user can have multiple role assignments (different conferences)

---

### **HoiThao (Conferences) Table - Actual Structure**
```sql
- conference_id (PK)
- parent_id (FK to HoiThao, nullable)
- level_code (FK to CapHoiThao: KHOA, TRUONG)
- faculty_id (FK to Khoa, nullable)
- title (varchar 255) ← NOT 'conference_name'
- year (smallint)
- start_date (date, nullable)
- end_date (date, nullable)
- deadline_submission (date, nullable)
- deadline_review (date, nullable)
- deadline_camera_ready (date, nullable)
- status (varchar 50, nullable)
```

**NO chair_id column!** - Chairs are assigned via `VaiTroNguoiDung`

---

## 🔧 Required Changes

### **ChairController.php**
1. ✅ Update `dashboard()` - Use VaiTroNguoiDung join
2. ✅ Update `papers()` - Use VaiTroNguoiDung join
3. ✅ Update `showPaper()` - Check authorization via VaiTroNguoiDung
4. ✅ Change all `submission_date` → `created_at`
5. ✅ Change all `overall_score` → `score`
6. ✅ Change all `recommendation` → `recommendation_code`
7. ✅ Change all `assignment_date` → `assigned_at`
8. ✅ Change all `due_date` → `deadline`
9. ✅ Change all `conference_name` → `title`

### **test_chair_backend.php**
1. ✅ Update conference query
2. ✅ Change `submission_date` → `created_at`
3. ✅ Change `overall_score` → `score`

### **Future Controllers**
All chair-related queries must use the VaiTroNguoiDung join pattern.

---

## 🛠️ Database Seeder Fix
**Issue:** Original seeder created CHAIR roles but didn't set `conference_id`
**Solution:** Created `ChairConferenceSeeder.php` to assign chairs to conferences

```php
php artisan db:seed --class=ChairConferenceSeeder
```

**Results:**
- User 7 (chair7@huit.edu.vn) → Conference 1
- User 8 (chair8@huit.edu.vn) → Conference 2
- User 9 (chair9@huit.edu.vn) → Conference 3
- etc.

---

## ✅ Verification Checklist

- [x] Chair-conference relationship via VaiTroNguoiDung working
- [x] Chair can query their assigned conferences
- [ ] All column names corrected in ChairController
- [ ] Test script runs without errors
- [ ] Authorization checks working correctly

---

## 📝 Documentation Updates Needed

Update these files with corrected schema:
1. `PHASE_8_DATABASE_SETUP_COMPLETE.md` - Add VaiTroNguoiDung details
2. `PHASE_8_6_PLAN.md` - Update query examples
3. `database.md` - Already correct (reference source)

---

*Last Updated: 2025-01-05*
*Phase: 8.6 - Chair Features Implementation*
