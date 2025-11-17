# Module Báo cáo & Thống kê - Admin Dashboard

## 📋 Tổng quan

Module **Báo cáo & Thống kê** cung cấp dashboard tổng quan toàn diện về hệ thống quản lý hội thảo khoa học HUIT, bao gồm:

- **Thống kê tổng quan:** Người dùng, hội thảo, bài báo, reviewer, kỷ yếu
- **Biểu đồ trực quan:** Hội thảo theo năm, bài báo theo track, tiến độ phản biện, trạng thái bài báo
- **Bảng chi tiết:** Top reviewers, top tác giả, hội thảo gần đây, kỷ yếu xuất bản

## 🗂️ Cấu trúc Files

```
├── app/Http/Controllers/Admin/
│   └── AdminReportController.php      # Controller xử lý logic báo cáo
│
├── resources/views/admin/reports/
│   └── index.blade.php                # View hiển thị dashboard
│
└── routes/web.php                      # Routes đã thêm
```

## 🚀 Routes

### 1. Trang báo cáo chính
```
GET /admin/reports
Route name: admin.reports.index
Controller: AdminReportController@index
```

**Query Parameters (Filters):**
- `year` - Lọc theo năm (ví dụ: 2024, 2025)
- `faculty` - Lọc theo khoa (faculty_id)
- `level` - Lọc theo cấp hội thảo
- `status` - Lọc theo trạng thái bài báo

**Ví dụ:**
```
/admin/reports?year=2025&faculty=1
```

### 2. API endpoint cho biểu đồ (JSON)
```
GET /admin/reports/data
Route name: admin.reports.data
Controller: AdminReportController@data
```

**Query Parameters:**
- `chart` - Loại biểu đồ: `conferences`, `tracks`, `reviews`, `status`
- `year` - Filter theo năm
- `faculty` - Filter theo khoa
- `level` - Filter theo cấp hội thảo

**Ví dụ:**
```
/admin/reports/data?chart=conferences&year=2025
```

**Response (JSON):**
```json
{
  "labels": [2021, 2022, 2023, 2024, 2025],
  "values": [5, 8, 12, 15, 20]
}
```

## 📊 Thống kê được hiển thị

### Thống kê tổng quan (8 cards)

1. **Tổng người dùng** - Tất cả user trong hệ thống
2. **Tổng hội thảo** - Số hội thảo (có thể filter theo năm)
3. **Tổng bài báo** - Số bài báo đã nộp
4. **Reviewer hoạt động** - Số reviewer có ít nhất 1 assignment
5. **Tổng lượt phản biện** - Số lượt review (COMPLETED/ACCEPTED)
6. **Đang phản biện** - Bài báo status UNDER_REVIEW
7. **Đã chấp nhận** - Bài báo status ACCEPTED/APPROVED
8. **Kỷ yếu xuất bản** - Hội thảo status COMPLETED/PUBLISHED

### Biểu đồ (4 charts - Chart.js)

#### 1. Biểu đồ cột - Hội thảo theo năm
- **Loại:** Bar Chart
- **Dữ liệu:** Số hội thảo trong 5 năm gần nhất
- **Màu:** Primary (#4e73df)

#### 2. Biểu đồ Doughnut - Phân bố bài báo theo track
- **Loại:** Doughnut Chart
- **Dữ liệu:** Top 10 track có nhiều bài báo nhất
- **Màu:** Multi-color (10 màu khác nhau)

#### 3. Biểu đồ Line - Tiến độ phản biện theo tháng
- **Loại:** Line Chart
- **Dữ liệu:** Số lượt review hoàn thành trong 12 tháng gần nhất
- **Màu:** Info (#36b9cc)
- **Fill:** Gradient

#### 4. Biểu đồ Pie - Trạng thái bài báo
- **Loại:** Pie Chart
- **Dữ liệu:** Phân bố status (Nháp, Đang phản biện, Chấp nhận, Từ chối...)
- **Màu:** Success/Warning/Info/Danger

### Bảng chi tiết (4 tables)

#### 1. Top 10 Reviewers
- Sắp xếp theo số lượt review hoàn thành
- Hiển thị: Họ tên, Email, Số lượt review

#### 2. Top 10 Tác giả
- Sắp xếp theo số bài báo đã nộp
- Hiển thị: Họ tên, Email, Số bài báo

#### 3. Hội thảo gần đây
- 10 hội thảo mới tạo nhất
- Hiển thị: Tên, Năm, Chair, Trạng thái
- Link đến chi tiết hội thảo

#### 4. Kỷ yếu gần đây
- 10 hội thảo đã hoàn thành gần đây
- Hiển thị: Tên, Năm, Ngày kết thúc, Trạng thái
- Link đến chi tiết

## 🔧 Models sử dụng

Module sử dụng các model hiện có, **KHÔNG tạo migration mới**:

```php
use App\Models\NguoiDung;           // Người dùng
use App\Models\HoiThao;             // Hội thảo
use App\Models\BaiBao;              // Bài báo
use App\Models\ReviewerAssignment;  // Phân công phản biện
use App\Models\TieuBan;             // Track/Tiểu ban
```

### Relationships được dùng:

- `BaiBao::hoiThao()` - Bài báo thuộc hội thảo nào
- `BaiBao::tieuBan()` - Bài báo thuộc track nào
- `BaiBao::submitter()` - Tác giả bài báo
- `HoiThao::chair()` - Chair của hội thảo
- `ReviewerAssignment::reviewer()` - Reviewer được phân công

## 💡 Logic xử lý chính

### Controller: `AdminReportController.php`

#### Method: `index(Request $request)`

**Chức năng:** Hiển thị trang báo cáo với đầy đủ thống kê

**Flow:**
1. Đọc filter parameters (year, faculty, level, status)
2. Truy vấn 8 thống kê tổng quan
3. Chuẩn bị data cho 4 biểu đồ
4. Lấy dữ liệu cho 4 bảng chi tiết
5. Lấy options cho filter dropdown
6. Return view với tất cả data

**Ví dụ query:**

```php
// Tổng người dùng
$totalUsers = NguoiDung::count();

// Hội thảo theo năm (có filter)
$totalConferences = HoiThao::when($year, function($query) use ($year) {
    return $query->where('year', $year);
})->count();

// Top reviewers
$topReviewers = ReviewerAssignment::select('user_id', DB::raw('COUNT(*) as review_count'))
    ->where('status', ReviewerAssignment::STATUS_COMPLETED)
    ->groupBy('user_id')
    ->orderByDesc('review_count')
    ->limit(10)
    ->with('reviewer:user_id,full_name,email')
    ->get();
```

#### Method: `data(Request $request)`

**Chức năng:** API endpoint trả JSON cho biểu đồ (dùng cho AJAX refresh)

**Parameters:**
- `chart` (required) - Loại biểu đồ
- `year`, `faculty`, `level` (optional) - Filters

**Response:**
```json
{
  "labels": ["Label 1", "Label 2"],
  "values": [10, 20]
}
```

#### Helper: `getStatusLabel($statusCode)`

Map status code sang tiếng Việt:
- `SUBMITTED` → "Đã nộp"
- `UNDER_REVIEW` → "Đang phản biện"
- `ACCEPTED` → "Đã chấp nhận"
- v.v.

## 🎨 View: `index.blade.php`

### Cấu trúc:

1. **Header** - Tiêu đề + Filter controls
2. **Stats Cards** - 2 rows × 4 cards
3. **Charts Row 1** - 2 columns (Bar + Doughnut)
4. **Charts Row 2** - 8 columns + 4 columns (Line + Pie)
5. **Tables Row** - 2 columns (Top reviewers + Top authors)
6. **Recent Row** - 2 columns (Conferences + Proceedings)

### Technologies:

- **Framework:** Bootstrap 5
- **Charts:** Chart.js 4.4.0
- **Icons:** SVG inline (Heroicons style)
- **Layout:** Blade template extending `layouts.app`

### Sections:

```blade
@section('title', 'Báo cáo & Thống kê')
@section('content') ... @endsection
@push('styles') ... @endpush
@push('scripts') ... @endpush
```

### Chart Configuration:

Mỗi biểu đồ được render bằng Chart.js:

```javascript
new Chart(ctx, {
    type: 'bar', // hoặc 'doughnut', 'line', 'pie'
    data: {
        labels: @json($yearsData),
        datasets: [{
            label: 'Số hội thảo',
            data: @json($conferenceCountsData),
            backgroundColor: chartColors.primary
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true
    }
});
```

### Filter Function:

```javascript
function applyFilters() {
    const year = document.getElementById('filterYear').value;
    const faculty = document.getElementById('filterFaculty').value;
    
    const params = new URLSearchParams();
    if (year) params.append('year', year);
    if (faculty) params.append('faculty', faculty);
    
    window.location.href = '{{ route("admin.reports.index") }}?' + params.toString();
}
```

## 🔐 Middleware & Permissions

Routes được bảo vệ bởi:

```php
Route::prefix('admin')->middleware('role:ADMIN')->name('admin.')->group(function () {
    Route::get('/reports', [AdminReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/data', [AdminReportController::class, 'data'])->name('reports.data');
});
```

**Yêu cầu:**
- User phải đăng nhập (`auth` middleware)
- User phải có role `ADMIN` (`role:ADMIN` middleware)

## 🧪 Testing

### Test routes:

```bash
php artisan route:list --name=admin.reports
```

### Test trong browser:

1. Đăng nhập với tài khoản ADMIN
2. Truy cập: `http://localhost:8000/admin/reports`
3. Test filter: Chọn năm, khoa → Click "Lọc"
4. Test API: `http://localhost:8000/admin/reports/data?chart=conferences`

### Expected result:

- ✅ 8 thẻ thống kê hiển thị số liệu
- ✅ 4 biểu đồ render đúng với Chart.js
- ✅ 4 bảng hiển thị dữ liệu (có thể empty nếu DB chưa có data)
- ✅ Filter dropdown load đúng options
- ✅ Khi chọn filter và click "Lọc" → URL thay đổi, data cập nhật

## 🐛 Troubleshooting

### 1. Charts không hiển thị

**Nguyên nhân:** Chart.js chưa load  
**Giải pháp:** Kiểm tra console browser, đảm bảo CDN Chart.js load thành công

```html
<!-- Kiểm tra trong view -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
```

### 2. Bảng hiển thị "Chưa có dữ liệu"

**Nguyên nhân:** Database chưa có dữ liệu test  
**Giải pháp:** 
- Seed data mẫu vào DB
- Hoặc tạo hội thảo, bài báo, assignment thủ công

### 3. Error: Class not found

**Nguyên nhân:** Controller chưa được tạo hoặc namespace sai  
**Giải pháp:**
```bash
# Kiểm tra file tồn tại
ls app/Http/Controllers/Admin/AdminReportController.php

# Clear cache
php artisan route:clear
php artisan config:clear
php artisan cache:clear
```

### 4. Error: Undefined relationship

**Nguyên nhân:** Model thiếu relationship  
**Giải pháp:** Kiểm tra các model có đầy đủ relationships:

```php
// ReviewerAssignment.php
public function reviewer() {
    return $this->belongsTo(NguoiDung::class, 'user_id', 'user_id');
}

// HoiThao.php
public function chair() {
    return $this->belongsTo(NguoiDung::class, 'chair_id', 'user_id');
}
```

### 5. Filter không hoạt động

**Nguyên nhân:** JavaScript error hoặc form submit sai  
**Giải pháp:**
- Mở Console browser, kiểm tra error
- Đảm bảo function `applyFilters()` được định nghĩa
- Kiểm tra ID của select elements

## 📝 Customization

### Thêm thống kê mới:

1. **Trong Controller:**
```php
// Thêm query
$myNewStat = Model::where(...)->count();

// Thêm vào return
return view('admin.reports.index', [
    // ... existing stats
    'myNewStat' => $myNewStat,
]);
```

2. **Trong View:**
```blade
<div class="col-xl-3 col-md-6">
    <div class="card border-left-primary shadow h-100 py-2">
        <div class="card-body">
            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                Tiêu đề stat mới
            </div>
            <div class="h5 mb-0 font-weight-bold text-gray-800">
                {{ number_format($myNewStat) }}
            </div>
        </div>
    </div>
</div>
```

### Thêm biểu đồ mới:

1. **Trong Controller - chuẩn bị data:**
```php
$myChartData = Model::selectRaw('column, COUNT(*) as total')
    ->groupBy('column')
    ->get();

$myLabels = $myChartData->pluck('column')->toArray();
$myValues = $myChartData->pluck('total')->toArray();
```

2. **Trong View - thêm canvas:**
```blade
<canvas id="myNewChart"></canvas>
```

3. **Trong @push('scripts'):**
```javascript
new Chart(document.getElementById('myNewChart').getContext('2d'), {
    type: 'bar',
    data: {
        labels: @json($myLabels),
        datasets: [{
            data: @json($myValues),
            backgroundColor: chartColors.primary
        }]
    }
});
```

### Thay đổi màu sắc:

```javascript
// Trong @push('scripts')
const chartColors = {
    primary: '#4e73df',    // Xanh dương
    success: '#1cc88a',    // Xanh lá
    info: '#36b9cc',       // Xanh lam
    warning: '#f6c23e',    // Vàng
    danger: '#e74a3b',     // Đỏ
};
```

## 📚 Dependencies

- **Laravel:** 9.x
- **PHP:** 8.1+
- **Chart.js:** 4.4.0
- **Bootstrap:** 5.x
- **Database:** MySQL

## 🎯 Performance Tips

1. **Caching:** Cache thống kê tổng quan (5-10 phút)
```php
$totalUsers = Cache::remember('admin.reports.total_users', 600, function() {
    return NguoiDung::count();
});
```

2. **Eager Loading:** Luôn dùng `with()` khi load relationships
```php
$topReviewers = ReviewerAssignment::with('reviewer:user_id,full_name,email')
    ->select('user_id', DB::raw('COUNT(*) as review_count'))
    ->groupBy('user_id')
    ->get();
```

3. **Index Database:** Đảm bảo các cột filter có index
```sql
-- Nếu cần thêm index
ALTER TABLE hoithao ADD INDEX idx_year (year);
ALTER TABLE baibao ADD INDEX idx_status (status_code);
```

## 📄 License

Module này là một phần của hệ thống Quản lý Hội thảo Khoa học HUIT.

---

**Tác giả:** Backend Team  
**Ngày tạo:** 14/11/2025  
**Version:** 1.0
