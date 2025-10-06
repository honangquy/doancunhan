# 🔍 DEBUG STEPS - Không thấy list reviewer

**Status:** Debug info shows "Backend count: 69" ✅ BUT list not showing ❌

---

## ✅ Confirmed Working:
- Backend has 69 reviewers
- Data passed to view
- Debug box displays
- Cache cleared

## ❌ Problem:
- Reviewer grid not showing
- Cannot search/select reviewers

---

## 🧪 TEST STEPS

### Step 1: Test Alpine.js x-for
```
Open: http://localhost/qly_hthao/qlyhoithao/public/test-xfor.php
```

**Expected:**
- Should see "Total reviewers: 69"
- Should see 5 reviewer cards
- Each card has name, email, workload

**Result:**
- [ ] Works - Alpine.js is fine
- [ ] Doesn't work - Alpine.js issue

---

### Step 2: Check Browser Console

**Press F12 → Console tab**

Look for:
- ❌ Red errors
- ⚠️ Yellow warnings about Alpine.js
- Any messages about "reviewers" or "filteredReviewers"

**Take screenshot of console**

---

### Step 3: Check Debug Boxes

After refreshing assignment page, you should see **2 debug boxes**:

#### Yellow Box (top):
```
🔍 Debug Info:
Backend count: 69
Alpine reviewers.length: ??  ← WHAT NUMBER?
Filtered count: ??
Search query: empty
```

#### Gray Box (before grid):
```
Alpine.js Debug:
reviewers array exists: ??
reviewers.length: ??
filteredReviewers.length: ??
First reviewer: ??
```

**Screenshot both boxes!**

---

### Step 4: View Page Source

**Right-click → View Page Source** (or Ctrl+U)

Search for: `x-data=`

**Should find:**
```javascript
x-data="{
    searchQuery: '',
    selectedReviewer: null,
    deadline: '',
    loading: false,
    message: '',
    messageType: '',
    reviewers: [{"user_id":25,"full_name":"Reviewer User 25",...}],  ← LONG JSON ARRAY
```

**Check:**
- [ ] Found `x-data=`
- [ ] Found `reviewers: [...]` with actual data
- [ ] JSON array has 69 items

**If NO data:** Backend not passing correctly
**If HAS data:** Alpine.js not rendering

---

## 🔧 Possible Fixes

### Fix 1: If Alpine.js test page (test-xfor.php) works

**Problem:** Specific to assign.blade.php  
**Solution:** Issue with Blade/Alpine.js conflict

Try this in browser console:
```javascript
// Check if Alpine is loaded
window.Alpine

// Check data
Alpine.store
```

### Fix 2: If console shows Alpine errors

**Copy exact error message** and report

Common errors:
- "Cannot read property 'length' of undefined"
- "filteredReviewers is not defined"
- "x-for requires :key"

### Fix 3: If reviewers array is empty in Alpine

**Problem:** JSON encoding issue

Check in browser console:
```javascript
// In page, find the x-data element
document.querySelector('[x-data]').__x.$data.reviewers
// Should show array of 69 items
```

---

## 📊 Expected vs Actual

### EXPECTED (after fixes):
```
Yellow Box:
✅ Backend count: 69
✅ Alpine reviewers.length: 69
✅ Filtered count: 69

Gray Box:
✅ reviewers array exists: YES
✅ reviewers.length: 69
✅ filteredReviewers.length: 69
✅ First reviewer: Reviewer User 25

Below:
✅ Grid of 69 reviewer cards showing
```

### ACTUAL (now):
```
Yellow Box:
✅ Backend count: 69
❓ Alpine reviewers.length: ??
❓ Filtered count: ??

Gray Box:
❓ Not showing OR showing zeros

Below:
❌ No reviewer cards
```

---

## 🎯 ACTION ITEMS

1. [ ] Open test-xfor.php - Does it work?
2. [ ] F12 Console - Any errors? (screenshot)
3. [ ] Yellow box numbers - What shows?
4. [ ] Gray box numbers - What shows?
5. [ ] View source - Is JSON data there?
6. [ ] Test in console: `Alpine.version` - Shows version?

---

## 📞 Report Back

**Please provide:**

1. **test-xfor.php result:**
   - Works / Doesn't work
   - Screenshot

2. **Console errors:**
   - Screenshot of F12 Console
   - Copy any red error text

3. **Debug box values:**
   - Alpine reviewers.length: ??
   - filteredReviewers.length: ??
   - First reviewer: ??

4. **Page source check:**
   - Found x-data? Yes/No
   - Found reviewers array? Yes/No
   - How many items in array? ??

---

## 💡 Quick Diagnostic

```
If test-xfor.php works:
  → Alpine.js is fine
  → Problem in assign.blade.php
  → Check Blade syntax conflicts

If test-xfor.php fails:
  → Alpine.js not loading
  → CDN blocked?
  → Browser compatibility?

If console has errors:
  → Specific syntax issue
  → Need exact error to fix

If page source has no JSON:
  → Backend not passing data
  → Check controller
```

---

*Debug guide created: Oct 5, 2025, 20:15*
*Awaiting test results from user*
