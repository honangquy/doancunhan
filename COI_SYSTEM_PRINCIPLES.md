# 🔍 NGUYÊN LÝ HOẠT ĐỘNG HỆ THỐNG XỬ LÝ COI

**Date:** October 6, 2025  
**Version:** Phase 8.10  
**Status:** ✅ Production Ready

---

## 📋 MỤC LỤC

1. [Tổng quan COI](#tổng-quan-coi)
2. [Kiến trúc hệ thống](#kiến-trúc-hệ-thống)
3. [Quy trình xử lý](#quy-trình-xử-lý)
4. [Database Schema](#database-schema)
5. [Routes & Controllers](#routes--controllers)
6. [Business Logic](#business-logic)
7. [UI/UX Flow](#uiux-flow)

---

## 🎯 TỔNG QUAN COI

### COI là gì?

**COI (Conflict of Interest)** = Xung đột lợi ích

Trong hệ thống hội thảo khoa học:
- Reviewer KHÔNG được phản biện bài báo có **xung đột lợi ích**
- Xung đột có thể do: cùng tổ chức, hợp tác nghiên cứu, quan hệ cá nhân, etc.

### Mục tiêu hệ thống COI

✅ **Phát hiện** xung đột tự động hoặc thủ công  
✅ **Khai báo** xung đột bởi Reviewer  
✅ **Xử lý** xung đột bởi Chair (chấp nhận/từ chối)  
✅ **Ngăn chặn** phân công không hợp lệ  
✅ **Ghi log** đầy đủ lịch sử  

---

## 🏗️ KIẾN TRÚC HỆ THỐNG

### Các thành phần chính

```
┌─────────────────────────────────────────────────────────────┐
│                    COI MANAGEMENT SYSTEM                     │
├─────────────────────────────────────────────────────────────┤
│                                                               │
│  ┌──────────────┐      ┌──────────────┐      ┌────────────┐ │
│  │   REVIEWER   │──────│     CHAIR    │──────│   SYSTEM   │ │
│  │   (Declare)  │      │  (Resolve)   │      │  (Detect)  │ │
│  └──────────────┘      └──────────────┘      └────────────┘ │
│         │                      │                     │        │
│         ▼                      ▼                     ▼        │
│  ┌────────────────────────────────────────────────────────┐  │
│  │              XuLyCOI Table (Database)                  │  │
│  │  - coi_id (PK)                                         │  │
│  │  - paper_id → BaiBao                                   │  │
│  │  - reviewer_id → NguoiDung                             │  │
│  │  - detection_type (ENUM: declared/detected)           │  │
│  │  - reason (TEXT)                                       │  │
│  │  - decision (ENUM: pending/accepted/rejected)         │  │
│  │  - decided_at, chair_id                               │  │
│  └────────────────────────────────────────────────────────┘  │
│                                                               │
└─────────────────────────────────────────────────────────────┘
```

### Vai trò và quyền hạn

| Vai trò | Quyền hạn | Routes |
|---------|-----------|--------|
| **Reviewer** | Xem COI của mình, Khai báo COI mới, Thu hồi khai báo | `/reviewer/coi/*` |
| **Chair** | Xem tất cả COI, Xử lý COI, Xem thống kê | `/chair/coi/*` |
| **System** | Tự động phát hiện COI (future) | Background jobs |

---

## ⚙️ QUY TRÌNH XỬ LÝ

### Flow 1: Reviewer khai báo COI

```
1. Reviewer login → Dashboard
   │
2. Click "Kiểm tra COI" → /reviewer/coi
   │
3. Click "Khai báo COI mới" → /reviewer/coi/create
   │
4. Tìm kiếm bài báo (AJAX search)
   │  GET /reviewer/coi/search-papers?query=...
   │  → Trả về danh sách bài báo matching
   │
5. Chọn bài báo + Nhập lý do
   │
6. Submit form
   │  POST /reviewer/coi
   │  Body: { paper_id, reason }
   │
7. Controller: Reviewer\COIController@store()
   │  - Validate input
   │  - Check duplicate (same reviewer + paper)
   │  - Insert into XuLyCOI:
   │    * detection_type = 'declared'
   │    * decision = 'pending'
   │    * declared_by = current_reviewer_id
   │  - Return success/error
   │
8. Redirect → /reviewer/coi (với flash message)
```

### Flow 2: Chair xử lý COI

```
1. Chair login → Dashboard
   │
2. Click "Kiểm tra COI" → /chair/coi
   │
3. Xem danh sách COI (filter theo conference)
   │  - Chưa xử lý (pending)
   │  - Đã xử lý (accepted/rejected)
   │
4. Click "Xem chi tiết" → /chair/coi/{id}
   │  - Thông tin Reviewer
   │  - Thông tin bài báo
   │  - Lý do khai báo
   │  - Lịch sử xử lý
   │
5. Click "Xử lý" → /chair/coi/{id}/resolve
   │
6. Chọn quyết định: Accept / Reject
   │  Nhập ghi chú (optional)
   │
7. Submit form
   │  POST /chair/coi/{id}/resolve
   │  Body: { decision, notes }
   │
8. Controller: Chair\COIController@resolve()
   │  - Validate decision (accepted/rejected)
   │  - Update XuLyCOI:
   │    * decision = accepted/rejected
   │    * chair_id = current_chair_id
   │    * decided_at = now()
   │    * notes = input notes
   │  - If accepted → Remove PhanCongPhanBien (if exists)
   │  - Return success
   │
9. Redirect → /chair/coi (với flash message)
```

### Flow 3: System tự động phát hiện (Future)

```
[Planned - Not implemented yet]

1. Author submit paper
   │
2. Trigger: System check COI rules
   │  - Same institution as reviewers?
   │  - Co-author relationships?
   │  - Recent collaborations?
   │
3. If detected → Insert XuLyCOI
   │  detection_type = 'detected'
   │  decision = 'pending'
   │  reason = 'Auto-detected: [rule]'
   │
4. Notify Chair via email/notification
```

---

## 💾 DATABASE SCHEMA

### Bảng: XuLyCOI

```sql
CREATE TABLE XuLyCOI (
    coi_id INT PRIMARY KEY AUTO_INCREMENT,
    paper_id INT NOT NULL,              -- FK → BaiBao.paper_id
    reviewer_id INT NOT NULL,           -- FK → NguoiDung.user_id
    conference_id INT NOT NULL,         -- FK → HoiThao.conference_id
    
    -- Phát hiện
    detection_type ENUM('declared', 'detected') NOT NULL,
    reason TEXT NOT NULL,               -- Lý do xung đột
    declared_by INT NULL,               -- Reviewer khai báo
    declared_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    
    -- Xử lý
    decision ENUM('pending', 'accepted', 'rejected') DEFAULT 'pending',
    chair_id INT NULL,                  -- Chair xử lý
    decided_at DATETIME NULL,           -- Thời gian xử lý
    notes TEXT NULL,                    -- Ghi chú của Chair
    
    -- Metadata
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Indexes
    INDEX idx_paper (paper_id),
    INDEX idx_reviewer (reviewer_id),
    INDEX idx_conference (conference_id),
    INDEX idx_decision (decision),
    
    -- Constraints
    FOREIGN KEY (paper_id) REFERENCES BaiBao(paper_id) ON DELETE CASCADE,
    FOREIGN KEY (reviewer_id) REFERENCES NguoiDung(user_id) ON DELETE CASCADE,
    FOREIGN KEY (conference_id) REFERENCES HoiThao(conference_id) ON DELETE CASCADE,
    FOREIGN KEY (declared_by) REFERENCES NguoiDung(user_id) ON DELETE SET NULL,
    FOREIGN KEY (chair_id) REFERENCES NguoiDung(user_id) ON DELETE SET NULL,
    
    -- Unique constraint: 1 reviewer chỉ khai báo 1 lần cho 1 bài
    UNIQUE KEY unique_coi (paper_id, reviewer_id)
);
```

### Quan hệ với các bảng khác

```
XuLyCOI
  ├─→ BaiBao (paper_id)
  │     ├─ title, abstract, authors
  │     └─→ HoiThao (conference_id)
  │
  ├─→ NguoiDung (reviewer_id)
  │     ├─ full_name, email, affiliation
  │     └─ Reviewer information
  │
  ├─→ NguoiDung (declared_by) [Optional]
  │     └─ Người khai báo COI
  │
  └─→ NguoiDung (chair_id) [Optional]
        └─ Người xử lý COI
```

---

## 🛣️ ROUTES & CONTROLLERS

### Reviewer Routes (6 routes)

```php
// File: routes/web.php (lines 95-100)

Route::prefix('reviewer')->name('reviewer.')->group(function () {
    // Danh sách COI của reviewer
    Route::get('/coi', 
        [\App\Http\Controllers\Reviewer\COIController::class, 'index'])
        ->name('coi.index');
    
    // Form khai báo COI mới
    Route::get('/coi/create', 
        [\App\Http\Controllers\Reviewer\COIController::class, 'create'])
        ->name('coi.create');
    
    // Submit khai báo COI
    Route::post('/coi', 
        [\App\Http\Controllers\Reviewer\COIController::class, 'store'])
        ->name('coi.store');
    
    // Xem chi tiết 1 COI
    Route::get('/coi/{id}', 
        [\App\Http\Controllers\Reviewer\COIController::class, 'show'])
        ->name('coi.show');
    
    // Thu hồi khai báo (xóa)
    Route::delete('/coi/{id}', 
        [\App\Http\Controllers\Reviewer\COIController::class, 'retract'])
        ->name('coi.retract');
    
    // AJAX: Tìm kiếm bài báo
    Route::get('/coi/search-papers', 
        [\App\Http\Controllers\Reviewer\COIController::class, 'searchPapers'])
        ->name('coi.search-papers');
});
```

### Chair Routes (5 routes)

```php
// File: routes/web.php (lines 130-134)

Route::prefix('chair')->name('chair.')->group(function () {
    // Danh sách tất cả COI
    Route::get('/coi', 
        [\App\Http\Controllers\Chair\COIController::class, 'index'])
        ->name('coi.index');
    
    // Xem chi tiết 1 COI
    Route::get('/coi/{id}', 
        [\App\Http\Controllers\Chair\COIController::class, 'show'])
        ->name('coi.show');
    
    // Form xử lý COI (GET)
    Route::get('/coi/{id}/resolve', 
        [\App\Http\Controllers\Chair\COIController::class, 'resolveForm'])
        ->name('coi.resolve-form');
    
    // Submit xử lý COI (POST)
    Route::post('/coi/{id}/resolve', 
        [\App\Http\Controllers\Chair\COIController::class, 'resolve'])
        ->name('coi.resolve');
    
    // Thống kê COI theo conference
    Route::get('/conferences/{conferenceId}/coi-statistics', 
        [\App\Http\Controllers\Chair\COIController::class, 'statistics'])
        ->name('coi.statistics');
});
```

---

## 🧠 BUSINESS LOGIC

### Reviewer\COIController Logic

#### Method: store() - Khai báo COI

```php
// File: app/Http/Controllers/Reviewer/COIController.php

public function store(Request $request)
{
    // 1. VALIDATION
    $validated = $request->validate([
        'paper_id' => 'required|exists:BaiBao,paper_id',
        'reason' => 'required|string|min:10|max:1000',
    ]);
    
    $reviewerId = auth()->id();
    
    // 2. CHECK DUPLICATE
    $existing = DB::table('XuLyCOI')
        ->where('paper_id', $validated['paper_id'])
        ->where('reviewer_id', $reviewerId)
        ->exists();
    
    if ($existing) {
        return back()->with('error', 'Bạn đã khai báo COI cho bài báo này rồi');
    }
    
    // 3. GET CONFERENCE_ID
    $paper = DB::table('BaiBao')
        ->where('paper_id', $validated['paper_id'])
        ->first();
    
    // 4. INSERT COI
    $coiId = DB::table('XuLyCOI')->insertGetId([
        'paper_id' => $validated['paper_id'],
        'reviewer_id' => $reviewerId,
        'conference_id' => $paper->conference_id,
        'detection_type' => 'declared',  // Khai báo thủ công
        'reason' => $validated['reason'],
        'declared_by' => $reviewerId,
        'declared_at' => now(),
        'decision' => 'pending',         // Chờ Chair xử lý
    ]);
    
    // 5. LOG (optional)
    Log::info('COI declared', [
        'coi_id' => $coiId,
        'reviewer_id' => $reviewerId,
        'paper_id' => $validated['paper_id'],
    ]);
    
    // 6. REDIRECT
    return redirect()->route('reviewer.coi.index')
        ->with('success', 'Đã khai báo COI thành công. Chair sẽ xem xét.');
}
```

#### Method: retract() - Thu hồi khai báo

```php
public function retract($id)
{
    $reviewerId = auth()->id();
    
    // 1. FIND COI
    $coi = DB::table('XuLyCOI')
        ->where('coi_id', $id)
        ->where('reviewer_id', $reviewerId)  // Chỉ được xóa COI của mình
        ->first();
    
    if (!$coi) {
        return back()->with('error', 'Không tìm thấy COI');
    }
    
    // 2. CHECK IF PROCESSED
    if ($coi->decision !== 'pending') {
        return back()->with('error', 'Không thể thu hồi COI đã được xử lý');
    }
    
    // 3. DELETE
    DB::table('XuLyCOI')->where('coi_id', $id)->delete();
    
    // 4. LOG
    Log::info('COI retracted', ['coi_id' => $id, 'reviewer_id' => $reviewerId]);
    
    return back()->with('success', 'Đã thu hồi khai báo COI');
}
```

### Chair\COIController Logic

#### Method: resolve() - Xử lý COI

```php
// File: app/Http/Controllers/Chair/COIController.php

public function resolve(Request $request, $id)
{
    // 1. VALIDATION
    $validated = $request->validate([
        'decision' => 'required|in:accepted,rejected',
        'notes' => 'nullable|string|max:1000',
    ]);
    
    $chairId = auth()->id();
    
    // 2. FIND COI
    $coi = DB::table('XuLyCOI')->where('coi_id', $id)->first();
    
    if (!$coi) {
        return back()->with('error', 'Không tìm thấy COI');
    }
    
    if ($coi->decision !== 'pending') {
        return back()->with('error', 'COI này đã được xử lý rồi');
    }
    
    // 3. UPDATE COI
    DB::table('XuLyCOI')
        ->where('coi_id', $id)
        ->update([
            'decision' => $validated['decision'],
            'chair_id' => $chairId,
            'decided_at' => now(),
            'notes' => $validated['notes'],
        ]);
    
    // 4. IF ACCEPTED → REMOVE ASSIGNMENT (if exists)
    if ($validated['decision'] === 'accepted') {
        $removed = DB::table('PhanCongPhanBien')
            ->where('paper_id', $coi->paper_id)
            ->where('reviewer_id', $coi->reviewer_id)
            ->delete();
        
        if ($removed > 0) {
            Log::info('Assignment removed due to COI', [
                'coi_id' => $id,
                'paper_id' => $coi->paper_id,
                'reviewer_id' => $coi->reviewer_id,
            ]);
        }
    }
    
    // 5. LOG
    Log::info('COI resolved', [
        'coi_id' => $id,
        'decision' => $validated['decision'],
        'chair_id' => $chairId,
    ]);
    
    // 6. REDIRECT
    $message = $validated['decision'] === 'accepted' 
        ? 'Đã chấp nhận COI. Phân công đã bị hủy (nếu có).'
        : 'Đã từ chối COI.';
    
    return redirect()->route('chair.coi.index')
        ->with('success', $message);
}
```

---

## 🎨 UI/UX FLOW

### Reviewer Interface

#### 1. Danh sách COI (`/reviewer/coi`)

```
┌─────────────────────────────────────────────────────────────┐
│  📋 Khai báo xung đột lợi ích của tôi           [+ Khai báo] │
├─────────────────────────────────────────────────────────────┤
│                                                               │
│  🔍 Tìm kiếm: [____________]  📅 Filter: [Tất cả ▾]         │
│                                                               │
│  ┌───────────────────────────────────────────────────────┐  │
│  │ COI-001 │ Bài báo X       │ ⏳ Pending  │ 2025-10-01 │  │
│  │         │ Cùng tổ chức    │             │            │  │
│  │         │ [Xem] [Thu hồi]                            │  │
│  ├─────────────────────────────────────────────────────────┤
│  │ COI-002 │ Bài báo Y       │ ✅ Accepted │ 2025-10-02 │  │
│  │         │ Đồng tác giả    │             │            │  │
│  │         │ [Xem]                                       │  │
│  └─────────────────────────────────────────────────────────┘
│                                                               │
└─────────────────────────────────────────────────────────────┘
```

#### 2. Form khai báo (`/reviewer/coi/create`)

```
┌─────────────────────────────────────────────────────────────┐
│  🆕 Khai báo xung đột lợi ích mới                            │
├─────────────────────────────────────────────────────────────┤
│                                                               │
│  📄 Tìm bài báo:                                             │
│  ┌─────────────────────────────────────────────┐             │
│  │ [Nhập tên bài báo hoặc mã...            🔍] │             │
│  └─────────────────────────────────────────────┘             │
│                                                               │
│  💡 Kết quả tìm kiếm:                                        │
│  ┌─────────────────────────────────────────────┐             │
│  │ ○ Paper #123: Machine Learning for...      │             │
│  │ ○ Paper #145: Deep Learning Approach...    │             │
│  │ ○ Paper #178: AI in Healthcare...          │             │
│  └─────────────────────────────────────────────┘             │
│                                                               │
│  📝 Lý do xung đột: *                                        │
│  ┌─────────────────────────────────────────────┐             │
│  │                                             │             │
│  │  (Tối thiểu 10 ký tự)                      │             │
│  │                                             │             │
│  └─────────────────────────────────────────────┘             │
│                                                               │
│  [Hủy]                              [Khai báo COI]           │
│                                                               │
└─────────────────────────────────────────────────────────────┘
```

### Chair Interface

#### 1. Danh sách COI (`/chair/coi`)

```
┌─────────────────────────────────────────────────────────────┐
│  ⚠️  Quản lý xung đột lợi ích                                │
├─────────────────────────────────────────────────────────────┤
│                                                               │
│  📊 Thống kê nhanh:                                          │
│  ┌────────┬────────┬────────┬────────┬────────┐             │
│  │  Tổng  │ Chưa XL│ Đã XL  │ Chấp N.│ Từ chối│             │
│  │   45   │   12   │   33   │   28   │    5   │             │
│  └────────┴────────┴────────┴────────┴────────┘             │
│                                                               │
│  🔍 Hội thảo: [HUIT 2025 ▾]  Trạng thái: [Tất cả ▾]        │
│                                                               │
│  ┌───────────────────────────────────────────────────────┐  │
│  │ COI  │ Reviewer     │ Paper       │ Status  │ Action  │  │
│  ├─────┼──────────────┼─────────────┼─────────┼─────────┤  │
│  │ 001 │ Nguyễn Văn A │ Paper #123  │ ⏳ Pend.│ [Xử lý] │  │
│  │ 002 │ Trần Thị B   │ Paper #145  │ ✅ Acc. │ [Chi tƒ]│  │
│  │ 003 │ Lê Văn C     │ Paper #178  │ ❌ Rej. │ [Chi tƒ]│  │
│  └─────┴──────────────┴─────────────┴─────────┴─────────┘  │
│                                                               │
│  📄 Hiển thị 1-10 / 45        [◀] [1] [2] [3] [4] [▶]     │
│                                                               │
└─────────────────────────────────────────────────────────────┘
```

#### 2. Form xử lý (`/chair/coi/{id}/resolve`)

```
┌─────────────────────────────────────────────────────────────┐
│  ⚖️  Xử lý xung đột lợi ích #COI-001                         │
├─────────────────────────────────────────────────────────────┤
│                                                               │
│  📋 Thông tin COI:                                           │
│  ┌─────────────────────────────────────────────┐             │
│  │ Reviewer: Nguyễn Văn A (reviewer@test.com) │             │
│  │ Bài báo:  Paper #123 - Machine Learning... │             │
│  │ Khai báo: 2025-10-01 09:30                 │             │
│  │ Lý do:    "Tôi từng hợp tác nghiên cứu...  │             │
│  │           với tác giả chính của bài này." │             │
│  └─────────────────────────────────────────────┘             │
│                                                               │
│  ⚖️  Quyết định xử lý: *                                     │
│  ┌─────────────────────────────────────────────┐             │
│  │ ○ Chấp nhận - Xác nhận có xung đột          │             │
│  │   → Sẽ tự động hủy phân công (nếu có)      │             │
│  │                                             │             │
│  │ ○ Từ chối - Không có xung đột thực sự      │             │
│  │   → Reviewer có thể tiếp tục phản biện     │             │
│  └─────────────────────────────────────────────┘             │
│                                                               │
│  📝 Ghi chú (tùy chọn):                                      │
│  ┌─────────────────────────────────────────────┐             │
│  │  Ghi chú cho reviewer...                   │             │
│  └─────────────────────────────────────────────┘             │
│                                                               │
│  [Hủy]                              [Xác nhận xử lý]         │
│                                                               │
└─────────────────────────────────────────────────────────────┘
```

---

## 🔐 SECURITY & VALIDATION

### Authorization Rules

```php
// Reviewer chỉ được:
- Xem COI của chính mình
- Khai báo COI cho bài báo trong các hội thảo đang mở
- Thu hồi COI chưa được xử lý
- KHÔNG xem được COI của reviewer khác

// Chair được:
- Xem TẤT CẢ COI trong hội thảo mình quản lý
- Xử lý bất kỳ COI nào
- Xem thống kê COI
- KHÔNG được sửa/xóa COI (chỉ xử lý)
```

### Input Validation

```php
// Khai báo COI
'paper_id' => 'required|exists:BaiBao,paper_id',
'reason' => 'required|string|min:10|max:1000',

// Xử lý COI
'decision' => 'required|in:accepted,rejected',
'notes' => 'nullable|string|max:1000',
```

### Business Rules

1. **No Duplicate**: 1 reviewer chỉ khai báo 1 lần cho 1 bài
2. **No Retract After Decision**: Không thu hồi sau khi Chair xử lý
3. **Auto-Remove Assignment**: Chấp nhận COI → Xóa phân công
4. **Immutable History**: Không xóa lịch sử COI (soft delete future)

---

## 📊 STATISTICS & REPORTING

### COI Statistics API

```php
// GET /chair/conferences/{conferenceId}/coi-statistics

Response:
{
    "conference": {
        "id": 1,
        "title": "HUIT Conference 2025"
    },
    "statistics": {
        "total": 45,
        "by_status": {
            "pending": 12,
            "accepted": 28,
            "rejected": 5
        },
        "by_type": {
            "declared": 40,    // Reviewer khai báo
            "detected": 5      // System phát hiện
        },
        "by_paper": [
            { "paper_id": 123, "title": "...", "coi_count": 3 },
            { "paper_id": 145, "title": "...", "coi_count": 2 }
        ],
        "by_reviewer": [
            { "reviewer_id": 5, "name": "Nguyễn Văn A", "coi_count": 2 },
            { "reviewer_id": 8, "name": "Trần Thị B", "coi_count": 1 }
        ]
    }
}
```

---

## 🚀 PERFORMANCE OPTIMIZATION

### Database Indexes

```sql
-- XuLyCOI table
CREATE INDEX idx_paper ON XuLyCOI(paper_id);
CREATE INDEX idx_reviewer ON XuLyCOI(reviewer_id);
CREATE INDEX idx_conference ON XuLyCOI(conference_id);
CREATE INDEX idx_decision ON XuLyCOI(decision);
CREATE INDEX idx_declared_at ON XuLyCOI(declared_at);

-- Composite index for common queries
CREATE INDEX idx_conference_decision 
    ON XuLyCOI(conference_id, decision);
```

### Query Optimization

```php
// ❌ BAD: N+1 problem
foreach ($cois as $coi) {
    $paper = DB::table('BaiBao')->find($coi->paper_id);
    $reviewer = DB::table('NguoiDung')->find($coi->reviewer_id);
}

// ✅ GOOD: Eager loading with JOIN
$cois = DB::table('XuLyCOI as xc')
    ->join('BaiBao as bb', 'xc.paper_id', '=', 'bb.paper_id')
    ->join('NguoiDung as nd', 'xc.reviewer_id', '=', 'nd.user_id')
    ->select('xc.*', 'bb.title', 'nd.full_name')
    ->where('xc.conference_id', $conferenceId)
    ->get();
```

### Caching Strategy

```php
// Cache statistics (TTL: 5 minutes)
$stats = Cache::remember("coi_stats_{$conferenceId}", 300, function() {
    return DB::table('XuLyCOI')
        ->where('conference_id', $conferenceId)
        ->select(
            DB::raw('COUNT(*) as total'),
            DB::raw('SUM(decision = "pending") as pending'),
            DB::raw('SUM(decision = "accepted") as accepted'),
            DB::raw('SUM(decision = "rejected") as rejected')
        )
        ->first();
});
```

---

## 🧪 TESTING CHECKLIST

### Unit Tests

```php
// tests/Unit/COITest.php

✅ test_reviewer_can_declare_coi()
✅ test_reviewer_cannot_declare_duplicate_coi()
✅ test_reviewer_can_retract_pending_coi()
✅ test_reviewer_cannot_retract_processed_coi()
✅ test_chair_can_accept_coi()
✅ test_chair_can_reject_coi()
✅ test_accepting_coi_removes_assignment()
✅ test_coi_validation_rules()
```

### Integration Tests

```php
// tests/Feature/COIFlowTest.php

✅ test_full_reviewer_coi_declaration_flow()
✅ test_full_chair_coi_resolution_flow()
✅ test_coi_prevents_invalid_assignment()
✅ test_coi_statistics_accuracy()
```

### Manual Testing Scenarios

Xem file: `PHASE_8_10_TESTING_GUIDE.md` (22 scenarios)

---

## 📚 RELATED DOCUMENTATION

- `PHASE_8_10_COMPLETE.md` - Phase 8.10 implementation summary
- `PHASE_8_10_TESTING_GUIDE.md` - Comprehensive testing guide
- `SCHEMA_AUDIT_8.10.md` - Database schema corrections
- `BUG_FIX_8.10.002_SCHEMA_MISMATCHES.md` - Schema fixes applied
- `COI_VIEW_FIX_DASHBOARD_STRUCTURE.md` - UI/UX structure guide

---

## 🔮 FUTURE ENHANCEMENTS

### Phase 8.11+ Planned Features

1. **Auto-Detection System**
   - Check same institution
   - Check co-author relationships
   - Check recent collaborations
   - Integration with ORCID/Google Scholar

2. **Email Notifications**
   - Notify Chair when new COI declared
   - Notify Reviewer when COI resolved
   - Weekly COI summary email

3. **Advanced Analytics**
   - COI trends over time
   - Reviewer COI patterns
   - Paper COI heatmap
   - Export to Excel/PDF

4. **Bulk Operations**
   - Batch accept/reject COIs
   - Import COI from CSV
   - Export COI reports

5. **Audit Trail**
   - Full history of changes
   - Who viewed what and when
   - Compliance reporting

---

*Document created: October 6, 2025*  
*Last updated: October 6, 2025*  
*Version: 1.0*
