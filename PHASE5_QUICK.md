# 🚀 Phase 5 Bidding - Quick Reference

## ⚡ 2-Minute Setup

### 1️⃣ Test Bidding APIs (30 seconds)
```bash
# In Postman, test these endpoints:
GET  /api/my-biddings          # Your bids (Reviewer)
POST /api/papers/1/bid         # Submit bid (Reviewer)
GET  /api/papers/1/biddings    # View all bids (Admin)
GET  /api/bidding/statistics   # Statistics (Admin)
```

### 2️⃣ Bidding Codes (Remember These!)
```
EAGER      → 😍 Love to review!
WILLING    → 👍 Happy to review
NEUTRAL    → 😐 Can review if needed
UNWILLING  → 👎 Prefer not to
CONFLICT   → ⚠️ COI (auto-creates COI record)
```

### 3️⃣ Key Rules
- ✅ Reviewers can bid on any paper (except own papers)
- ❌ Cannot update/withdraw after assigned
- ⚠️ CONFLICT bidding → Auto COI record
- 🔒 Authors blocked from bidding on own papers

---

## 📋 API Quick Reference

### **Submit Bid** (Reviewer)
```http
POST /api/papers/{paper_id}/bid
{
    "bidding_code": "EAGER",
    "note": "My expertise area"
}
```

### **My Biddings** (Reviewer)
```http
GET /api/my-biddings?conference_id=1&bidding_code=EAGER
```

### **Update Bid** (Reviewer)
```http
PUT /api/biddings/{paper_id}
{
    "bidding_code": "WILLING",
    "note": "Changed preference"
}
```

### **Withdraw Bid** (Reviewer)
```http
DELETE /api/biddings/{paper_id}
```

### **View Paper Biddings** (Admin/Chair)
```http
GET /api/papers/{paper_id}/biddings
```

### **Statistics** (Admin)
```http
GET /api/bidding/statistics?conference_id=1
```

---

## 🔥 Common Scenarios

### Scenario 1: Reviewer Bids on Paper
```json
// 1. Browse papers
GET /api/papers?conference_id=1

// 2. Submit bid
POST /api/papers/12/bid
{
    "bidding_code": "EAGER",
    "note": "This is my research area"
}

// 3. Check my bids
GET /api/my-biddings
```

### Scenario 2: Declare COI via Bidding
```json
// Submit CONFLICT bid
POST /api/papers/15/bid
{
    "bidding_code": "CONFLICT",
    "note": "Co-author on this paper"
}

// ✅ Auto-creates COI record
// ✅ Status = PENDING
// ✅ Source = DECLARED
```

### Scenario 3: Chair Views Biddings
```json
// 1. Get all biddings for paper
GET /api/papers/12/biddings

// Response shows:
{
    "data": [
        {
            "reviewer_name": "Dr. Smith",
            "bidding_code": "EAGER",
            "bidding_name": "Eager to Review",
            "note": "My expertise"
        }
    ]
}

// 2. Check statistics
GET /api/bidding/statistics
```

---

## ❌ Common Errors

### Error 403: Cannot bid on own paper
```json
{
    "success": false,
    "message": "You cannot bid on your own paper. COI automatically recorded."
}
```
**Fix:** This is expected. Authors cannot bid on their own papers.

---

### Error 409: Already bid
```json
{
    "success": false,
    "message": "You have already bid on this paper. Use PUT to update."
}
```
**Fix:** Use `PUT /api/biddings/{paper_id}` to update instead.

---

### Error 403: Cannot modify after assignment
```json
{
    "success": false,
    "message": "Cannot modify bid after reviewer assignment"
}
```
**Fix:** Bid is locked after assignment. Contact chair to unassign first.

---

## 🎯 Testing Checklist (5 min)

- [ ] **Step 1:** Login as Reviewer
- [ ] **Step 2:** GET /my-biddings (empty at first)
- [ ] **Step 3:** POST /papers/1/bid (EAGER)
- [ ] **Step 4:** GET /my-biddings (should see 1 bid)
- [ ] **Step 5:** PUT /biddings/1 (change to WILLING)
- [ ] **Step 6:** DELETE /biddings/1 (withdraw)
- [ ] **Step 7:** POST /papers/2/bid (CONFLICT) → Check COI created
- [ ] **Step 8:** Login as Admin
- [ ] **Step 9:** GET /papers/1/biddings (view all)
- [ ] **Step 10:** GET /bidding/statistics (check stats)

✅ All green? **Bidding system working!**

---

## 🔧 Troubleshooting

### Problem: "Unauthenticated" error
**Solution:** Add Bearer token to Authorization header

### Problem: No biddings shown
**Solution:** Check conference_id filter, verify conference status = OPEN

### Problem: Cannot submit bid
**Solution:** 
1. Verify you're logged in as Reviewer
2. Check conference is OPEN
3. Verify you're not author of that paper

---

## 📊 Statistics Explained

```json
{
    "total_bids": 150,              // Total bids across all papers
    "by_bidding_code": {
        "EAGER": 45,                // 30% eager
        "WILLING": 60,              // 40% willing
        "NEUTRAL": 30,              // 20% neutral
        "UNWILLING": 10,            // 7% unwilling
        "CONFLICT": 5               // 3% conflict
    },
    "papers_with_bids": 50,         // 50 papers have bids
    "reviewers_who_bid": 25,        // 25 reviewers participated
    "average_bids_per_paper": 3.0   // 3 bids per paper average
}
```

**Good Coverage:** 3+ bids per paper, 80%+ EAGER/WILLING  
**Needs Attention:** <2 bids per paper, >20% UNWILLING/CONFLICT

---

## 🚀 Next: Review Controller

After bidding system works, implement Review Controller:
- Submit reviews (score, confidence, comments)
- List reviews per paper
- Update reviews before deadline
- Finalize reviews

See `PHASE5_REVIEW_APIS.md` (coming soon)

---

**File:** `PHASE5_QUICK.md`  
**Version:** 1.0  
**Status:** ✅ Ready for Testing
