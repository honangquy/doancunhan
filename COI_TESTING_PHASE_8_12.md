# 🧪 PHASE 8.12 - COI COMPREHENSIVE TESTING

**Testing Date:** October 6, 2025, 11:50 PM  
**Status:** 🔄 IN PROGRESS  
**Estimated Time:** 30 minutes  
**Scope:** All COI functionality after dashboard conversion

---

## 🎯 TESTING OBJECTIVES

1. **Layout Integration** - Verify all views show navbar + sidebar
2. **Functionality** - Ensure all COI features still work
3. **Navigation** - Test menu links and buttons
4. **Data Flow** - Verify database operations
5. **User Experience** - Check responsive design & interactions
6. **Error Handling** - Test validation and error messages

---

## 📋 TESTING CHECKLIST

### Phase 1: Chair COI Testing (15 minutes)

#### 1.1 Chair COI Index (`/chair/coi`)
- [ ] **Layout Check:**
  - [ ] Orange top navbar visible
  - [ ] HUIT Conferences logo displays
  - [ ] User name shows in top right
  - [ ] Logout button works
  - [ ] White sidebar on left
  - [ ] "Kiểm tra COI" menu highlighted (orange)
  
- [ ] **Content Check:**
  - [ ] Statistics cards display (Total, Unresolved, etc.)
  - [ ] COI cases table shows data
  - [ ] Conference filter dropdown works
  - [ ] Status filter works
  - [ ] Search box functions
  - [ ] "Xem chi tiết" buttons work
  - [ ] "Xử lý" buttons work (if pending COI exists)

- [ ] **Navigation Check:**
  - [ ] Dashboard link works (goes to chair dashboard)
  - [ ] All sidebar links work or show alerts
  - [ ] No console errors
  - [ ] Responsive on mobile

#### 1.2 Chair COI Detail (`/chair/coi/{id}`)
- [ ] **Layout Check:**
  - [ ] Same navbar + sidebar structure
  - [ ] "Kiểm tra COI" still highlighted
  
- [ ] **Content Check:**
  - [ ] COI information section loads
  - [ ] Paper information displays
  - [ ] Reviewer information shows
  - [ ] Status card shows correct state
  - [ ] Resolution details (if resolved)
  
- [ ] **Actions Check:**
  - [ ] "Giải quyết COI" button (if pending)
  - [ ] "Xem bài báo" link works
  - [ ] "Quay lại danh sách" works

#### 1.3 Chair COI Resolve (`/chair/coi/{id}/resolve`)
- [ ] **Layout Check:**
  - [ ] Navbar + sidebar present
  - [ ] Form displays properly
  
- [ ] **Functionality Check:**
  - [ ] COI summary shows correct info
  - [ ] Resolution options load (from ENUM)
  - [ ] Notes textarea works
  - [ ] Form validation works
  - [ ] Confirmation modal appears
  - [ ] Submit button processes correctly
  - [ ] Success redirect to COI list
  - [ ] Database updates correctly

### Phase 2: Reviewer COI Testing (15 minutes)

#### 2.1 Reviewer COI Index (`/reviewer/coi`)
- [ ] **Layout Check:**
  - [ ] Purple top navbar visible
  - [ ] HUIT Conferences logo displays
  - [ ] User name shows
  - [ ] Logout button works
  - [ ] White sidebar with reviewer menus
  - [ ] "Khai báo COI" menu highlighted (purple)
  
- [ ] **Content Check:**
  - [ ] Statistics cards show data
  - [ ] COI declarations table loads
  - [ ] "Khai báo COI mới" button (purple)
  - [ ] Filter dropdowns work
  - [ ] Search functionality
  - [ ] "Xem chi tiết" buttons work
  - [ ] "Thu hồi" buttons (for pending only)

#### 2.2 Reviewer COI Create (`/reviewer/coi/create`)
- [ ] **Layout Check:**
  - [ ] Purple theme consistent
  - [ ] Form displays properly
  
- [ ] **Functionality Check:**
  - [ ] Paper search box works (AJAX)
  - [ ] Search results appear
  - [ ] Paper selection works
  - [ ] Selected paper preview shows
  - [ ] Reason textarea required validation
  - [ ] Character count works
  - [ ] Alpine.js validation works
  - [ ] Form submission works
  - [ ] Success redirect to COI list
  - [ ] Database insert correct

#### 2.3 Reviewer COI Detail (`/reviewer/coi/{id}`)
- [ ] **Layout Check:**
  - [ ] Purple navbar + sidebar
  - [ ] Content displays properly
  
- [ ] **Content Check:**
  - [ ] COI details section
  - [ ] Paper information
  - [ ] Resolution status
  - [ ] Decision details (if resolved)
  - [ ] Action buttons appropriate
  
- [ ] **Actions Check:**
  - [ ] "Thu hồi khai báo" (if pending)
  - [ ] "Quay lại danh sách" works
  - [ ] Retraction confirmation modal

---

## 🗄️ TEST DATA REQUIREMENTS

### Chair Account
- **Email:** chair@test.com
- **Password:** password123
- **Role:** CHAIR
- **Conference:** Must have conference assigned

### Reviewer Account  
- **Email:** reviewer@test.com
- **Password:** password123
- **Role:** REVIEWER
- **Conference:** Same conference as Chair

### Test COI Cases
Need at least:
- [ ] 1 pending COI case (for resolution testing)
- [ ] 1 resolved COI case (for display testing)
- [ ] 1 reviewer's own COI (for retraction testing)

---

## 🐛 ISSUES TO WATCH FOR

### Common Problems
1. **Layout Issues:**
   - Navbar not showing
   - Sidebar missing or broken
   - Content overlapping
   - Mobile responsiveness

2. **Navigation Issues:**
   - Links not working
   - Wrong redirects
   - Menu highlighting incorrect
   - Logout not functioning

3. **Functionality Issues:**
   - Forms not submitting
   - AJAX not working
   - Database errors
   - Validation failing

4. **Data Issues:**
   - Empty tables
   - Wrong statistics
   - Missing information
   - Incorrect relationships

---

## 📊 SUCCESS CRITERIA

### Must Pass (Critical)
- [ ] All 6 views load without errors
- [ ] Navbar + sidebar visible on all pages
- [ ] Theme colors correct (orange/purple)
- [ ] Basic navigation works
- [ ] Core CRUD operations function
- [ ] No console errors
- [ ] Database operations successful

### Should Pass (Important)
- [ ] All buttons and links work
- [ ] Form validation works
- [ ] AJAX search functions
- [ ] Mobile responsive
- [ ] Statistics accurate
- [ ] Error messages display

### Nice to Have (Optional)
- [ ] Smooth animations
- [ ] Fast loading times
- [ ] Perfect alignment
- [ ] Advanced features work

---

## 🚀 TESTING EXECUTION PLAN

### Step 1: Environment Check (2 minutes)
```bash
# Verify server running
curl http://localhost/qly_hthao/qlyhoithao/public

# Check database connection
php artisan db:show

# Verify test accounts exist
mysql -u root huit_conference -e "SELECT * FROM NguoiDung WHERE email IN ('chair@test.com', 'reviewer@test.com');"
```

### Step 2: Chair Testing (13 minutes)
1. Login as Chair (1 min)
2. Test COI Index (5 min)
3. Test COI Detail (3 min)
4. Test COI Resolve (4 min)

### Step 3: Reviewer Testing (13 minutes)
1. Login as Reviewer (1 min)
2. Test COI Index (4 min)
3. Test COI Create (4 min)
4. Test COI Detail (4 min)

### Step 4: Cross-Testing (2 minutes)
1. Verify Chair can see Reviewer's COI
2. Test resolution flow end-to-end
3. Verify database consistency

---

## 📝 RESULTS DOCUMENTATION

### Test Results Template
```markdown
## Test Results - [View Name]

**Status:** ✅ PASS / ❌ FAIL / ⚠️ PARTIAL

### Layout
- Navbar: ✅/❌
- Sidebar: ✅/❌  
- Theme: ✅/❌
- Responsive: ✅/❌

### Functionality  
- Core features: ✅/❌
- Forms: ✅/❌
- Navigation: ✅/❌
- AJAX: ✅/❌

### Issues Found
- Issue 1: Description
- Issue 2: Description

### Screenshots
- Desktop: [link]
- Mobile: [link]
```

---

## 🎯 NEXT ACTIONS

**If ALL TESTS PASS:**
✅ Phase 8.12 Complete → Proceed to Phase 9 (Final Deployment)

**If CRITICAL ISSUES FOUND:**
❌ Fix issues → Re-test → Document fixes → Proceed

**If MINOR ISSUES FOUND:**
⚠️ Document issues → Mark for future improvement → Proceed with caution

---

**Ready to start testing? Let's begin with Chair COI testing!** 🚀

**First step:** Verify test accounts and login as Chair