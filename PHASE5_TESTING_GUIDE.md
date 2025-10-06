# 🧪 PHASE 5 - TESTING GUIDE

**Purpose**: Complete guide to test all 25 Phase 5 APIs with Postman  
**Time Required**: 30-45 minutes  
**Collection**: `PHASE5_POSTMAN_COLLECTION.json`  

---

## 📋 PREREQUISITES

### 1. Import Postman Collection
```
File → Import → Select PHASE5_POSTMAN_COLLECTION.json
```

### 2. Setup Environment Variables
The collection includes these variables (auto-configured):

| Variable | Default Value | Description |
|----------|---------------|-------------|
| `base_url` | http://localhost/qly_hthao/qlyhoithao/public/api | API base URL |
| `jwt_token` | (auto-set on login) | JWT authentication token |
| `conference_id` | 1 | Conference ID for testing |
| `paper_id` | 1 | Paper ID for testing |
| `reviewer_id` | 5 | Reviewer user ID |
| `assignment_id` | (auto-set) | Created assignment ID |
| `review_id` | (auto-set) | Created review ID |
| `coi_id` | (auto-set) | Created COI ID |

### 3. Database Setup
Make sure you have test data:

```sql
-- Check you have users with roles
SELECT u.user_id, u.ho_ten, u.email, vt.role_code 
FROM NguoiDung u
JOIN VaiTro vt ON u.user_id = vt.user_id;

-- Check you have a conference
SELECT * FROM HoiThao LIMIT 1;

-- Check you have papers
SELECT paper_id, title, submitter_id FROM BaiBao LIMIT 5;
```

---

## 🎯 TESTING WORKFLOW

### PHASE 1: AUTHENTICATION (5 min)

Run these first to get JWT tokens:

**Folder**: `0. Authentication (Setup)`

1. ✅ **Login as Reviewer**
   - Use: `reviewer@huit.edu.vn` / `password123`
   - Auto-saves JWT token to `{{jwt_token}}`
   - Console shows: "✅ Reviewer logged in successfully"

2. ✅ **Login as Chair**
   - Use: `chair@huit.edu.vn` / `password123`
   - Needed for admin operations

3. ✅ **Login as Admin**
   - Use: `admin@huit.edu.vn` / `password123`
   - Needed for system-wide operations

**✅ Success Criteria**: You see JWT token in response and console log.

---

### PHASE 2: BIDDING SYSTEM (10 min)

**Folder**: `1. Bidding System (6 APIs)`

**Login as**: Reviewer

#### Test 1.1: View Available Papers
```
GET /api/bidding/available-papers?conference_id=1
```

**Expected Response**:
```json
{
  "success": true,
  "data": {
    "current_page": 1,
    "data": [
      {
        "paper_id": 1,
        "title": "Paper Title",
        "abstract": "...",
        "keywords": ["AI", "ML"],
        "submission_date": "2025-01-15",
        "my_bidding": null
      }
    ]
  }
}
```

**✅ Pass**: You see list of papers you haven't authored

#### Test 1.2: Submit Bidding - EAGER
```
POST /api/papers/1/bid
Body: {
  "paper_id": 1,
  "bidding_code": "EAGER",
  "note": "Perfect match for my expertise!"
}
```

**Expected Response**:
```json
{
  "success": true,
  "message": "Bidding submitted successfully",
  "data": {
    "user_id": 5,
    "paper_id": 1,
    "bidding_code": "EAGER"
  }
}
```

**✅ Pass**: Bidding created, status 201

**Test Variations**:
- Submit WILLING bidding on paper 2
- Submit CONFLICT bidding on paper 3 (should auto-create COI!)

#### Test 1.3: View My Biddings
```
GET /api/my-biddings?conference_id=1
```

**✅ Pass**: You see all your biddings with details

#### Test 1.4: Update Bidding
```
PUT /api/biddings/1
Body: {
  "bidding_code": "WILLING",
  "note": "Changed my mind"
}
```

**✅ Pass**: Bidding updated successfully

#### Test 1.5: Withdraw Bidding
```
DELETE /api/biddings/1
```

**✅ Pass**: Bidding deleted (only if not assigned yet!)

#### Test 1.6: Bidding Statistics (Admin/Chair)
**Login as**: Chair

```
GET /api/bidding/statistics?conference_id=1
```

**Expected**:
```json
{
  "success": true,
  "data": {
    "total_biddings": 25,
    "by_bidding_code": {
      "EAGER": 10,
      "WILLING": 12,
      "NEUTRAL": 3
    },
    "participation_rate": 78.5,
    "papers_with_biddings": 8
  }
}
```

**✅ Pass**: Statistics show bidding breakdown

---

### PHASE 3: ASSIGNMENT SYSTEM (15 min)

**Folder**: `2. Assignment System (7 APIs)`

**Login as**: Chair

#### Test 2.1: Manual Assignment
```
POST /api/assignments
Body: {
  "paper_id": 1,
  "reviewer_id": 5,
  "deadline": "2025-02-28 23:59:59"
}
```

**Expected Response**:
```json
{
  "success": true,
  "message": "Reviewer assigned successfully",
  "data": {
    "assignment_id": 123,
    "paper_id": 1,
    "reviewer_id": 5,
    "status_code": "INVITED"
  }
}
```

**✅ Pass**: Assignment created, `assignment_id` auto-saved to variables

**Test Error Cases**:
- Try assigning paper author as reviewer → Should fail!
- Try assigning reviewer with COI → Should fail!
- Try duplicate assignment → Should fail!

#### Test 2.2: Auto-Assignment Algorithm ⭐ KEY TEST!
```
POST /api/assignments/auto-assign
Body: {
  "conference_id": 1,
  "reviewers_per_paper": 3
}
```

**Expected Response**:
```json
{
  "success": true,
  "data": {
    "total_assignments": 15,
    "assignments": [
      {
        "paper_id": 1,
        "assigned_reviewers": [
          {
            "reviewer_id": 5,
            "reviewer_name": "Dr. Smith",
            "score": 3.5,
            "bidding": "WILLING",
            "workload": 3
          }
        ]
      }
    ],
    "errors": []
  }
}
```

**✅ Pass Criteria**:
1. EAGER bidders assigned first (score=4)
2. Workload balanced (adjusted_score considers workload)
3. Authors excluded from their own papers
4. COI reviewers excluded
5. No duplicate assignments

**Verification Steps**:
1. Check paper 1 assignments → Should have 3 reviewers
2. Check EAGER bidder → Should be assigned (high score)
3. Check overloaded reviewer → Should have lower priority

#### Test 2.3: View Paper Assignments
```
GET /api/papers/1/assignments
```

**✅ Pass**: See all 3 reviewers assigned to paper 1

#### Test 2.4: View My Assignments
**Login as**: Reviewer

```
GET /api/my-assignments?conference_id=1
```

**✅ Pass**: See your assignments with deadline tracking

#### Test 2.5: Accept Assignment
```
PUT /api/assignments/123/accept
Body: {
  "accept": true,
  "note": "Happy to review!"
}
```

**Expected**: Status changes INVITED → ACCEPTED

**✅ Pass**: Assignment accepted successfully

**Also Test**: Decline assignment (accept=false)

#### Test 2.6: Unassign Reviewer
**Login as**: Chair

```
DELETE /api/assignments/123
```

**✅ Pass**: Assignment removed (only if no review submitted!)

#### Test 2.7: Assignment Statistics
```
GET /api/assignment/statistics?conference_id=1
```

**Expected**:
```json
{
  "data": {
    "total_assignments": 15,
    "by_status": {
      "INVITED": 5,
      "ACCEPTED": 8,
      "DECLINED": 2
    },
    "completion_rate": 60.5,
    "acceptance_rate": 80.0,
    "reviewers_overloaded": []
  }
}
```

**✅ Pass**: Statistics accurate

---

### PHASE 4: REVIEW SYSTEM (10 min)

**Folder**: `3. Review System (7 APIs)`

**Login as**: Reviewer (who has ACCEPTED assignment)

#### Test 3.1: Submit Review
```
POST /api/reviews
Body: {
  "assignment_id": 123,
  "overall_rating": 4,
  "recommendation_code": "MINOR_REVISION",
  "confidence_level": 4,
  "strengths": "Novel approach, good results",
  "weaknesses": "Missing ablation studies",
  "suggestions": "Add more experiments",
  "is_draft": false
}
```

**Expected**: Review created, assignment status → REVIEWED

**✅ Pass**: Review submitted, review_id auto-saved

**Test Variations**:
- Save as draft (is_draft=true)
- Try different recommendations: ACCEPT, MAJOR_REVISION, REJECT

#### Test 3.2: List All Reviews (Admin)
**Login as**: Admin

```
GET /api/reviews?conference_id=1
```

**✅ Pass**: See all reviews in conference

#### Test 3.3: View Review Details
```
GET /api/reviews/123
```

**✅ Pass**: See full review with all fields

#### Test 3.4: Update Review
**Login as**: Reviewer

```
PUT /api/reviews/123
Body: {
  "overall_rating": 5,
  "recommendation_code": "ACCEPT"
}
```

**✅ Pass**: Review updated (only if not finalized!)

#### Test 3.5: View My Reviews
```
GET /api/my-reviews?conference_id=1
```

**✅ Pass**: See all your submitted reviews

#### Test 3.6: Finalize Review
**Login as**: Chair

```
PUT /api/reviews/123/finalize
```

**Expected**: is_finalized → true, locked for editing

**✅ Pass**: Review finalized

**Test**: Try to update finalized review → Should fail!

#### Test 3.7: Review Statistics
```
GET /api/review/statistics?conference_id=1
```

**Expected**:
```json
{
  "data": {
    "total_reviews": 12,
    "by_recommendation": {
      "ACCEPT": 3,
      "MINOR_REVISION": 5,
      "MAJOR_REVISION": 3,
      "REJECT": 1
    },
    "avg_overall_rating": 3.8,
    "completion_rate": 80.0
  }
}
```

**✅ Pass**: Statistics accurate

---

### PHASE 5: COI MANAGEMENT (10 min)

**Folder**: `4. COI Management (6 APIs)`

**Login as**: Reviewer

#### Test 4.1: Declare COI Manually
```
POST /api/coi/declare
Body: {
  "paper_id": 1,
  "coi_code": "ADVISOR_STUDENT",
  "description": "Author was my PhD advisor"
}
```

**Expected**: COI created, status PENDING

**✅ Pass**: COI declared, coi_id auto-saved

**Test All COI Types**:
- AUTHOR_REVIEWER
- SAME_INSTITUTION
- COLLABORATION
- ADVISOR_STUDENT
- FAMILY_RELATION
- FINANCIAL_INTEREST

#### Test 4.2: List Paper COIs
```
GET /api/papers/1/cois
```

**✅ Pass**: See all COIs for paper 1

#### Test 4.3: List All COIs
**Login as**: Admin

```
GET /api/cois?conference_id=1
```

**✅ Pass**: See all COIs system-wide

#### Test 4.4: Auto-Detect COI
**Login as**: Chair

```
POST /api/coi/detect
Body: {
  "conference_id": 1
}
```

**Expected**: System detects if reviewer is also author

**✅ Pass**: Auto-detected COIs created

**Verification**: Check if system found AUTHOR_REVIEWER conflicts

#### Test 4.5: Resolve COI - CONFIRM
```
PUT /api/coi/123/resolve
Body: {
  "decision": "CONFIRMED",
  "note": "COI is valid, removing assignment"
}
```

**Expected Actions**:
1. Assignment removed
2. Bidding updated to CONFLICT
3. Future assignments blocked

**✅ Pass**: COI confirmed, assignment removed

#### Test 4.5: Resolve COI - REJECT
```
PUT /api/coi/124/resolve
Body: {
  "decision": "REJECTED",
  "note": "COI is not valid"
}
```

**Expected**: Assignment restored (if removed)

**✅ Pass**: COI rejected

#### Test 4.6: COI Statistics
```
GET /api/coi/statistics?conference_id=1
```

**Expected**:
```json
{
  "data": {
    "total_cois": 8,
    "by_coi_type": {
      "ADVISOR_STUDENT": 3,
      "COLLABORATION": 2,
      "AUTHOR_REVIEWER": 3
    },
    "by_resolution": {
      "PENDING": 2,
      "CONFIRMED": 5,
      "REJECTED": 1
    }
  }
}
```

**✅ Pass**: Statistics accurate

---

### PHASE 6: COMPLETE WORKFLOW TEST (10 min)

**Folder**: `5. Complete Workflow Test`

This tests the entire review workflow end-to-end:

**Step 1**: Reviewer bids EAGER on paper
**Step 2**: Chair runs auto-assignment (EAGER bidder should be assigned!)
**Step 3**: Reviewer accepts assignment
**Step 4**: Reviewer submits review
**Step 5**: Chair finalizes review

**Run all 5 requests in sequence.**

**✅ Pass**: Complete workflow works without errors

---

## 🎯 SUCCESS METRICS

### Bidding System (6/6)
- ✅ Can view available papers
- ✅ Can submit biddings (all codes)
- ✅ CONFLICT bidding auto-creates COI
- ✅ Can view my biddings
- ✅ Can update/withdraw biddings
- ✅ Statistics accurate

### Assignment System (7/7)
- ✅ Manual assignment works
- ✅ **Auto-assignment algorithm works** (KEY!)
- ✅ EAGER bidders prioritized
- ✅ Workload balanced
- ✅ COI/author exclusions work
- ✅ Accept/decline workflow works
- ✅ Statistics accurate

### Review System (7/7)
- ✅ Can submit reviews
- ✅ Draft system works
- ✅ Can update draft reviews
- ✅ Cannot update finalized reviews
- ✅ Assignment status updated to REVIEWED
- ✅ Finalization locks editing
- ✅ Statistics accurate

### COI Management (6/6)
- ✅ Can declare COI manually
- ✅ All 6 COI types work
- ✅ Auto-detection finds conflicts
- ✅ CONFIRM removes assignments
- ✅ REJECT restores assignments
- ✅ Statistics accurate

---

## 🐛 COMMON ISSUES & SOLUTIONS

### Issue 1: "Unauthenticated" Error
**Solution**: Run login request first, JWT token auto-saves

### Issue 2: "Cannot assign paper author as reviewer"
**Expected**: This is correct! Test passed.

### Issue 3: "Cannot assign reviewer with confirmed COI"
**Expected**: This is correct! COI blocking works.

### Issue 4: "Assignment already accepted/declined"
**Solution**: Can only accept/decline INVITED assignments once

### Issue 5: "Cannot update finalized review"
**Expected**: This is correct! Finalization locks editing.

### Issue 6: Auto-assignment returns errors
**Check**:
- Do you have enough reviewers?
- Are all reviewers also authors?
- Do all reviewers have COI?
**Solution**: Add more reviewers to database

---

## 📊 TESTING CHECKLIST

Print this and check off as you test:

### Bidding System
- [ ] View available papers (filter by keywords, track)
- [ ] Submit EAGER bidding
- [ ] Submit WILLING bidding
- [ ] Submit CONFLICT bidding (check COI created!)
- [ ] View my biddings
- [ ] Update bidding
- [ ] Withdraw bidding
- [ ] View statistics (Admin/Chair)

### Assignment System
- [ ] Manual assignment
- [ ] Test error: assign author as reviewer
- [ ] Test error: assign reviewer with COI
- [ ] Test error: duplicate assignment
- [ ] **Auto-assignment algorithm** ⭐
- [ ] Verify EAGER bidders assigned first
- [ ] Verify workload balancing
- [ ] View paper assignments
- [ ] View my assignments
- [ ] Accept assignment
- [ ] Decline assignment
- [ ] Unassign reviewer (before review)
- [ ] Test error: unassign after review
- [ ] View statistics

### Review System
- [ ] Submit review (full)
- [ ] Save review as draft
- [ ] Update draft review
- [ ] List all reviews (Admin)
- [ ] View review details
- [ ] View my reviews
- [ ] Finalize review (Chair)
- [ ] Test error: update finalized review
- [ ] Test error: submit without ACCEPTED assignment
- [ ] View statistics

### COI Management
- [ ] Declare ADVISOR_STUDENT COI
- [ ] Declare COLLABORATION COI
- [ ] Declare all 6 COI types
- [ ] View paper COIs
- [ ] View all COIs (Admin)
- [ ] Auto-detect COI (check AUTHOR_REVIEWER found)
- [ ] Resolve COI - CONFIRM (check assignment removed)
- [ ] Resolve COI - REJECT (check assignment restored)
- [ ] View statistics

### Complete Workflow
- [ ] Bidding → Assignment → Accept → Review → Finalize
- [ ] Test with EAGER bidding (should be assigned first)
- [ ] Test with COI (should be blocked)

---

## 🎉 TESTING COMPLETE!

If all tests pass, you have successfully verified:

✅ **25 Phase 5 APIs working**  
✅ **Smart auto-assignment algorithm functional**  
✅ **Complete review workflow operational**  
✅ **COI detection and blocking working**  
✅ **All business rules enforced**  

**Phase 5: 100% TESTED AND VERIFIED!** 🎊

---

## 📝 NEXT STEPS

1. **Document any bugs found**
2. **Update collection if needed**
3. **Create test data scripts** (optional)
4. **Move to Phase 6** (5 APIs remaining!)

---

## 💡 PRO TIPS

### Tip 1: Use Collection Runner
```
Postman → Collections → Run collection
Select folder → Run all requests
```

### Tip 2: Use Test Scripts
Collection already includes auto-save scripts for IDs:
- `jwt_token` auto-saved on login
- `assignment_id` auto-saved on assignment
- `review_id` auto-saved on review
- `coi_id` auto-saved on COI

### Tip 3: Reset Test Data
```sql
-- Clear biddings
DELETE FROM Bidding WHERE user_id = 5;

-- Clear assignments
DELETE FROM PhanCongPhanBien WHERE reviewer_id = 5;

-- Clear reviews
DELETE FROM PhanBien WHERE assignment_id IN 
  (SELECT assignment_id FROM PhanCongPhanBien WHERE reviewer_id = 5);

-- Clear COIs
DELETE FROM COI WHERE reviewer_id = 5;
```

### Tip 4: Monitor Console
Watch Postman console for auto-save messages:
```
✅ Reviewer logged in successfully
Token: eyJ0eXAiOiJKV1QiLCJhbGc...
✅ Assignment created: 123
✅ Review submitted: 456
```

---

**Happy Testing!** 🚀

*Last Updated: January 2025*  
*Phase 5: Review System - Complete ✅*
