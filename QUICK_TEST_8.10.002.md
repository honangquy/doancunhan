# ⚡ QUICK TEST - Bug Fix 8.10.002

**Testing:** Schema mismatch fixes in COI Management UI  
**Time:** 5-10 minutes  
**Browser:** Chrome/Edge

---

## 🎯 CRITICAL TESTS (Must Pass)

### Test 1: Chair COI List (2 minutes)
1. Login: `chair@test.com` / `password`
2. Navigate: http://localhost/qly_hthao/qlyhoithao/public/chair/coi
3. **Expected:** 
   - ✅ Page loads WITHOUT database errors
   - ✅ Statistics displayed (Total, Unresolved, Resolved, etc.)
   - ✅ COI list shows (even if empty)
4. **If Error:** Screenshot and report

---

### Test 2: Chair COI Detail (1 minute)
1. Click any COI case from list (if exists)
2. **Expected:**
   - ✅ Details page loads WITHOUT errors
   - ✅ Paper title, reviewer name visible
   - ✅ COI type name displayed (no "coi_description" field)
   - ✅ Resolution status shown (if resolved)
3. **If No COI:** Skip to Test 4

---

### Test 3: Chair COI Resolution Form (2 minutes)
1. From COI detail, click "Giải quyết" button (if unresolved)
2. **Expected:**
   - ✅ Form loads WITHOUT errors
   - ✅ Dropdown shows 2 options:
     - "Xác nhận COI"
     - "Từ chối COI"
3. Select "Xác nhận COI", add note, submit
4. **Expected:**
   - ✅ Success message
   - ✅ COI marked as resolved
   - ✅ Assignment deleted (if CONFIRMED)

---

### Test 4: Reviewer COI List (2 minutes)
1. Logout, login: `reviewer@test.com` / `password`
2. Navigate: http://localhost/qly_hthao/qlyhoithao/public/reviewer/coi
3. **Expected:**
   - ✅ Page loads WITHOUT database errors
   - ✅ Statistics displayed
   - ✅ Declared COI list shows (even if empty)
4. **If Error:** Screenshot and report

---

### Test 5: Reviewer COI Detail (1 minute)
1. Click any declared COI (if exists)
2. **Expected:**
   - ✅ Details page loads WITHOUT errors
   - ✅ Paper info, COI type visible
   - ✅ Resolution status shown (if resolved)
   - ✅ No "coi_description" or "resolution_description" errors

---

## ✅ SUCCESS CRITERIA

**ALL tests pass if:**
- ✅ No database errors about missing columns
- ✅ Pages load successfully
- ✅ Data displays correctly
- ✅ Forms submit without errors

**FAIL if:**
- ❌ `Unknown column 'lc.description'` error
- ❌ `Unknown column 'xc.resolution_id'` error
- ❌ `Unknown column 'xc.resolved_by'` error
- ❌ Table 'LoaiXuLyCOI' doesn't exist error

---

## 🐛 IF ERRORS OCCUR

1. **Take screenshot of error**
2. **Note which test failed**
3. **Check error message for column name**
4. **Report:** "Test X failed with column Y error"

---

## 📝 QUICK REPORT

```
✅ Test 1: Chair COI List - PASS
✅ Test 2: Chair COI Detail - PASS / SKIP (no data)
✅ Test 3: Chair Resolution Form - PASS / SKIP (no data)
✅ Test 4: Reviewer COI List - PASS
✅ Test 5: Reviewer COI Detail - PASS / SKIP (no data)

Overall: PASS ✅ / FAIL ❌
```

---

## 🚀 AFTER PASSING

If all tests pass → Proceed to full **PHASE_8_10_TESTING_GUIDE.md** (22 scenarios)

---

*Quick test created: January 5, 2025*
