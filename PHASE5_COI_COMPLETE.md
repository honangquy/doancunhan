# ⚠️ PHASE 5 - COI MANAGEMENT SYSTEM COMPLETE ✅

## ✅ Status: COMPLETE & READY FOR TESTING

**Completion Date:** 2025-01-XX  
**Implementation Time:** ~2 hours  
**Total Lines of Code:** ~650 lines  
**APIs Implemented:** 6/6 (100%)

---

## 📊 What Was Implemented

### **COIController** (~650 lines)
✅ Created: `app/Http/Controllers/Api/COIController.php`

**6 API Methods:**
1. **declare()** - Manually declare COI (Reviewer)
2. **paperCOIs($paper_id)** - View all COIs for a paper (Admin/Chair)
3. **index()** - List all COIs in system (Admin)
4. **detect()** - Auto-detect COIs (Admin trigger)
5. **resolve($coi_id)** - Resolve COI decision (Chair)
6. **statistics()** - COI statistics (Admin)

---

## 🎯 API Endpoints

### **1. Declare COI** (Reviewer/Anyone)
```http
POST /api/coi/declare
Authorization: Bearer {token}
Content-Type: application/json

{
    "paper_id": 12,
    "coi_code": "AUTHOR_REVIEWER",
    "evidence": "I am a co-author on this paper"
}
```

**COI Codes (Examples):**
- `AUTHOR_REVIEWER` - Reviewer is also an author
- `SAME_INSTITUTION` - Same institution/organization
- `COLLABORATION` - Recent collaboration (< 2 years)
- `ADVISOR_STUDENT` - Advisor-student relationship
- `FAMILY_RELATION` - Family relationship
- `FINANCIAL_INTEREST` - Financial interest

**What Happens:**
- ✅ Creates COI record with source_type = 'DECLARED'
- ✅ If assignment exists → Updates status to 'COI_DECLARED'
- ✅ Cannot declare twice for same paper (409 Conflict)

**Response:**
```json
{
    "success": true,
    "message": "Conflict of Interest declared successfully",
    "data": {
        "coi_id": 5,
        "paper_id": 12,
        "paper_title": "Machine Learning in Healthcare",
        "reviewer_name": "Dr. John Smith",
        "coi_code": "AUTHOR_REVIEWER",
        "coi_name": "Reviewer is Author",
        "source_type": "DECLARED",
        "evidence": "I am a co-author on this paper",
        "created_at": "2025-01-15T14:30:00"
    }
}
```

---

### **2. View Paper COIs** (Admin/Chair Only)
```http
GET /api/papers/{paper_id}/coi
Authorization: Bearer {token}
```

**Permission:**
- ✅ Admin can view all COIs
- ✅ Track Chair can view COIs for their tracks
- ❌ Reviewers cannot view
- ❌ Authors cannot view

**Response:**
```json
{
    "success": true,
    "message": "COIs retrieved successfully",
    "data": [
        {
            "coi_id": 5,
            "reviewer_id": 6,
            "reviewer_name": "Dr. John Smith",
            "reviewer_email": "john@example.com",
            "coi_code": "AUTHOR_REVIEWER",
            "coi_name": "Reviewer is Author",
            "source_type": "DECLARED",
            "evidence": "I am a co-author on this paper",
            "created_at": "2025-01-15T14:30:00",
            "decision": {
                "decision_id": 3,
                "decision": "CONFIRMED",
                "note": "Conflict confirmed, removed from reviewers",
                "decided_by": "Dr. Chair Person",
                "decided_at": "2025-01-15T15:00:00"
            }
        },
        {
            "coi_id": 6,
            "reviewer_id": 7,
            "reviewer_name": "Dr. Jane Doe",
            "reviewer_email": "jane@example.com",
            "coi_code": "SAME_INSTITUTION",
            "coi_name": "Same Institution",
            "source_type": "DETECTED",
            "evidence": "Reviewer is also an author",
            "created_at": "2025-01-16T10:00:00",
            "decision": null
        }
    ]
}
```

---

### **3. List All COIs** (Admin Only)
```http
GET /api/coi?conference_id=1&track_id=2&source_type=DECLARED&resolved=false&page=1
Authorization: Bearer {token}
```

**Query Parameters:**
- `conference_id` (optional) - Filter by conference
- `track_id` (optional) - Filter by track
- `source_type` (optional) - DECLARED or DETECTED
- `resolved` (optional) - true/false (has decision or not)
- `page` (optional) - Pagination
- `per_page` (optional) - Items per page (default: 15)

**Response:**
```json
{
    "success": true,
    "message": "COIs retrieved successfully",
    "data": [
        {
            "coi_id": 5,
            "paper_id": 12,
            "paper_title": "Machine Learning in Healthcare",
            "track_name": "AI Track",
            "conference_name": "HUIT Conference 2025",
            "reviewer_id": 6,
            "reviewer_name": "Dr. John Smith",
            "coi_code": "AUTHOR_REVIEWER",
            "coi_name": "Reviewer is Author",
            "source_type": "DECLARED",
            "evidence": "I am a co-author",
            "created_at": "2025-01-15T14:30:00",
            "resolved": true,
            "decision": "CONFIRMED"
        }
    ],
    "pagination": {
        "current_page": 1,
        "per_page": 15,
        "total": 25,
        "last_page": 2
    }
}
```

---

### **4. Auto-Detect COI** (Admin Only)
```http
POST /api/coi/detect
Authorization: Bearer {token}
Content-Type: application/json

{
    "paper_id": 12,
    "conference_id": 1
}
```

**Detection Algorithm:**
- ✅ Checks if reviewer is also an author on the paper
- ✅ Creates COI with source_type = 'DETECTED'
- ✅ Updates assignment status to 'COI_DETECTED'
- ✅ Skips if COI already exists

**Parameters:**
- `paper_id` (optional) - Detect for specific paper
- `conference_id` (optional) - Detect for specific conference
- No params = Detect for all papers

**Response:**
```json
{
    "success": true,
    "message": "COI detection completed",
    "data": {
        "detected_count": 3,
        "cois": [
            {
                "coi_id": 8,
                "paper_id": 15,
                "reviewer_id": 10,
                "coi_code": "AUTHOR_REVIEWER"
            },
            {
                "coi_id": 9,
                "paper_id": 18,
                "reviewer_id": 12,
                "coi_code": "AUTHOR_REVIEWER"
            }
        ]
    }
}
```

---

### **5. Resolve COI** (Chair Decision)
```http
POST /api/coi/{coi_id}/resolve
Authorization: Bearer {token}
Content-Type: application/json

{
    "decision": "CONFIRMED",
    "note": "Conflict confirmed, reviewer removed from this paper"
}
```

**Decision Options:**
- `CONFIRMED` - COI is real, remove reviewer from assignment
- `REJECTED` - COI is not valid, restore assignment

**What Happens:**

**If CONFIRMED:**
- ✅ Removes reviewer assignment (if exists)
- ✅ Reviewer cannot be assigned to this paper
- ✅ Records decision in XuLyCOI table

**If REJECTED:**
- ✅ Restores assignment status to 'INVITED'
- ✅ Reviewer can continue reviewing
- ✅ Records decision in XuLyCOI table

**Validation:**
- ❌ Cannot resolve twice (409 Conflict)
- ✅ Only Admin or Track Chair can resolve
- ✅ Decision is permanent (cannot undo)

**Response:**
```json
{
    "success": true,
    "message": "COI resolved successfully",
    "data": {
        "decision_id": 3,
        "coi_id": 5,
        "decision": "CONFIRMED",
        "note": "Conflict confirmed, reviewer removed",
        "decided_by": "Dr. Chair Person",
        "decided_at": "2025-01-15T15:00:00"
    }
}
```

---

### **6. COI Statistics** (Admin Only)
```http
GET /api/coi/statistics?conference_id=1&track_id=2
Authorization: Bearer {token}
```

**Query Parameters:**
- `conference_id` (optional) - Filter by conference
- `track_id` (optional) - Filter by track

**Response:**
```json
{
    "success": true,
    "message": "COI statistics retrieved successfully",
    "data": {
        "total_cois": 25,
        "by_source_type": [
            {
                "source_type": "DECLARED",
                "count": 15
            },
            {
                "source_type": "DETECTED",
                "count": 10
            }
        ],
        "by_type": [
            {
                "coi_code": "AUTHOR_REVIEWER",
                "coi_name": "Reviewer is Author",
                "count": 12
            },
            {
                "coi_code": "SAME_INSTITUTION",
                "coi_name": "Same Institution",
                "count": 8
            },
            {
                "coi_code": "COLLABORATION",
                "coi_name": "Recent Collaboration",
                "count": 5
            }
        ],
        "pending": 10,
        "resolved": 15,
        "by_decision": [
            {
                "decision": "CONFIRMED",
                "count": 12
            },
            {
                "decision": "REJECTED",
                "count": 3
            }
        ],
        "papers_with_cois": 18,
        "reviewers_with_cois": 15
    }
}
```

---

## 🔐 Permission Matrix

| Endpoint | Admin | Chair | Reviewer | Author |
|----------|-------|-------|----------|--------|
| POST /coi/declare | ✅ | ✅ | ✅ | ✅ |
| GET /papers/{id}/coi | ✅ | ✅* | ❌ | ❌ |
| GET /coi | ✅ | ❌ | ❌ | ❌ |
| POST /coi/detect | ✅ | ❌ | ❌ | ❌ |
| POST /coi/{id}/resolve | ✅ | ✅* | ❌ | ❌ |
| GET /coi/statistics | ✅ | ❌ | ❌ | ❌ |

**Notes:**
- `*` Track Chairs can only access COIs for their tracks
- Anyone can declare COI (self-disclosure encouraged)

---

## 🔄 COI Workflow

### **Complete COI Process**
```
1. Assignment Created
   ↓
2. COI Detection Options:
   a) Reviewer declares COI (POST /coi/declare)
   b) Bidding with CONFLICT code → Auto COI
   c) Admin runs auto-detect (POST /coi/detect)
   ↓
3. COI Record Created
   - source_type: DECLARED or DETECTED
   - Assignment status → COI_DECLARED/COI_DETECTED
   ↓
4. Chair Reviews COI
   ↓
5. Chair Resolves COI (POST /coi/{id}/resolve)
   a) CONFIRMED → Remove assignment, block future assignment
   b) REJECTED → Restore assignment, reviewer can continue
   ↓
6. Decision recorded in XuLyCOI table
```

---

## 🧪 Testing Checklist

### **Scenario 1: Reviewer Declares COI**
1. [ ] Login as Reviewer
2. [ ] POST /coi/declare (paper_id, coi_code, evidence)
3. [ ] Verify 201 Created
4. [ ] Check source_type = 'DECLARED'
5. [ ] If assigned, verify assignment status changed

### **Scenario 2: Chair Views Paper COIs**
1. [ ] Login as Chair
2. [ ] GET /papers/{id}/coi
3. [ ] Verify all COIs shown
4. [ ] Verify resolved/pending status

### **Scenario 3: Auto-Detect COI**
1. [ ] Login as Admin
2. [ ] Assign reviewer who is also author
3. [ ] POST /coi/detect
4. [ ] Verify COI created with source_type='DETECTED'
5. [ ] Check detected_count in response

### **Scenario 4: Chair Confirms COI**
1. [ ] Login as Chair
2. [ ] POST /coi/{id}/resolve (decision=CONFIRMED)
3. [ ] Verify assignment removed
4. [ ] Verify decision recorded

### **Scenario 5: Chair Rejects COI**
1. [ ] Login as Chair
2. [ ] POST /coi/{id}/resolve (decision=REJECTED)
3. [ ] Verify assignment restored
4. [ ] Verify status changed back to INVITED

### **Scenario 6: Cannot Declare Twice**
1. [ ] Declare COI for paper
2. [ ] Try to declare again
3. [ ] Verify 409 Conflict

### **Scenario 7: COI Statistics**
1. [ ] Login as Admin
2. [ ] GET /coi/statistics
3. [ ] Verify counts by source_type
4. [ ] Verify pending vs resolved
5. [ ] Verify by decision breakdown

---

## 📝 Database Schema

### **COI Table**
```sql
CREATE TABLE COI (
  coi_id INT AUTO_INCREMENT PRIMARY KEY,
  paper_id INT NOT NULL,
  reviewer_id INT NOT NULL,
  coi_code VARCHAR(30) NOT NULL,
  source_type ENUM('DECLARED','DETECTED') NOT NULL,
  evidence VARCHAR(500),
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (paper_id) REFERENCES BaiBao(paper_id),
  FOREIGN KEY (reviewer_id) REFERENCES NguoiDung(user_id),
  FOREIGN KEY (coi_code) REFERENCES LoaiCOI(coi_code)
);
```

### **XuLyCOI Table (COI Decisions)**
```sql
CREATE TABLE XuLyCOI (
  decision_id INT AUTO_INCREMENT PRIMARY KEY,
  coi_id INT NOT NULL,
  chair_id INT NOT NULL,
  decision ENUM('CONFIRMED','REJECTED') NOT NULL,
  note VARCHAR(255),
  decided_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (coi_id) REFERENCES COI(coi_id),
  FOREIGN KEY (chair_id) REFERENCES NguoiDung(user_id)
);
```

### **LoaiCOI Lookup Table**
```sql
CREATE TABLE LoaiCOI (
  coi_code VARCHAR(30) PRIMARY KEY,
  coi_name VARCHAR(100) NOT NULL
);

-- Sample Values
INSERT INTO LoaiCOI VALUES
('AUTHOR_REVIEWER', 'Reviewer is Author'),
('SAME_INSTITUTION', 'Same Institution'),
('COLLABORATION', 'Recent Collaboration'),
('ADVISOR_STUDENT', 'Advisor-Student Relationship'),
('FAMILY_RELATION', 'Family Relationship'),
('FINANCIAL_INTEREST', 'Financial Interest');
```

---

## 🎯 Business Logic

### **COI Detection Algorithm**
```sql
-- Detects if reviewer is also an author
SELECT DISTINCT 
    bb.paper_id,
    pcpb.reviewer_id,
    'AUTHOR_REVIEWER' as coi_code,
    'Reviewer is also an author' as evidence
FROM PhanCongPhanBien pcpb
INNER JOIN BaiBao bb ON pcpb.paper_id = bb.paper_id
INNER JOIN TacGiaBaiBao tgbb ON bb.paper_id = tgbb.paper_id
WHERE pcpb.reviewer_id = tgbb.user_id
```

### **COI Resolution Logic**
```php
// CONFIRMED Decision
if (decision == 'CONFIRMED') {
    // Remove assignment
    PhanCongPhanBien::where('paper_id', $paper_id)
        ->where('reviewer_id', $reviewer_id)
        ->delete();
    
    // Record decision
    XuLyCOI::create([...]);
}

// REJECTED Decision
if (decision == 'REJECTED') {
    // Restore assignment status
    $assignment->status_code = 'INVITED';
    $assignment->save();
    
    // Record decision
    XuLyCOI::create([...]);
}
```

---

## 🎉 Success Summary

### **Implementation Complete**
✅ **COIController** - 650 lines, 6 methods  
✅ **COI Model** - Updated with relationships  
✅ **XuLyCOI Model** - Created with relationships  
✅ **6 API Routes** - All registered & verified  
✅ **Auto-Detection** - Smart algorithm for author-reviewer conflicts  
✅ **Decision System** - CONFIRMED/REJECTED workflow  

### **Phase 5 Progress Update**
```
Bidding System        ████████████████████ 100% (6/6 APIs) ✅
Review System         ████████████████████ 100% (7/7 APIs) ✅
COI Management        ████████████████████ 100% (6/6 APIs) ✅
Assignment System     ░░░░░░░░░░░░░░░░░░░░   0% (0/7 APIs) ⏳
```

**Phase 5 Progress: 76% (19/25 APIs)**

---

## 🚀 Next Steps

### **Immediate**
- [ ] Update Postman collection with 6 COI APIs
- [ ] Test all 7 scenarios
- [ ] Test auto-detection algorithm
- [ ] Verify COI resolution workflow

### **Final Phase 5 Controller: Assignment System (7 APIs)**
Last module to complete Phase 5:

1. **POST /api/assignments** - Manual assignment
2. **POST /api/assignments/auto-assign** - Auto-assignment algorithm (uses biddings!)
3. **DELETE /api/assignments/{id}** - Unassign reviewer
4. **GET /api/papers/{id}/assignments** - Paper assignments list
5. **GET /api/my-assignments** - My assignments (Reviewer)
6. **PUT /api/assignments/{id}/accept** - Accept/reject assignment
7. **GET /api/assignment/statistics** - Assignment statistics

**This will complete Phase 5!** 🎉

---

**Status:** ✅ **COI MANAGEMENT COMPLETE & READY FOR TESTING**  
**Total APIs:** 61/73 (83.6% overall progress)  
**Phase 5:** 76% complete (19/25 APIs)  
**Next:** Assignment System (Final Phase 5 module!)

---

**Created:** 2025-01-XX  
**Version:** 1.0  
**Status:** ✅ SUCCESS
