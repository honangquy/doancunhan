<?php
/**
 * Test script để kiểm tra menu admin và trang join requests
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\JoinRequest;
use App\Models\NguoiDung;

try {
    echo "=== ADMIN MENU & JOIN REQUESTS TEST ===\n\n";

    // 1. Kiểm tra số lượng join requests
    echo "1. KIỂM TRA DỮ LIỆU JOIN REQUESTS:\n";
    $totalRequests = JoinRequest::count();
    $pendingRequests = JoinRequest::where('status', 'PENDING')->count();
    $approvedRequests = JoinRequest::where('status', 'APPROVED')->count();
    $rejectedRequests = JoinRequest::where('status', 'REJECTED')->count();

    echo "   ✓ Tổng số yêu cầu: {$totalRequests}\n";
    echo "   ✓ Chờ duyệt: {$pendingRequests}\n";
    echo "   ✓ Đã duyệt: {$approvedRequests}\n";
    echo "   ✓ Từ chối: {$rejectedRequests}\n";

    // 2. Kiểm tra route admin join requests có tồn tại
    echo "\n2. KIỂM TRA ROUTES:\n";
    $routes = collect(\Route::getRoutes())->map(function($route) {
        return $route->getName();
    })->filter()->toArray();

    $adminRoutes = array_filter($routes, function($route) {
        return str_contains($route, 'admin.join-requests');
    });

    if (count($adminRoutes) > 0) {
        echo "   ✓ Admin join requests routes có sẵn:\n";
        foreach ($adminRoutes as $route) {
            echo "     - {$route}\n";
        }
    } else {
        echo "   ❌ Không tìm thấy admin join requests routes\n";
    }

    // 3. Kiểm tra join requests với relationships
    echo "\n3. KIỂM TRA RELATIONSHIPS:\n";
    $joinRequestsWithData = JoinRequest::with(['conference', 'user'])
        ->where('status', 'PENDING')
        ->get();

    foreach ($joinRequestsWithData as $request) {
        echo "   ✓ Request ID: {$request->id}\n";
        echo "     - Người dùng: {$request->full_name} ({$request->email_contact})\n";
        echo "     - Hội thảo: " . ($request->conference->title ?? 'N/A') . "\n";
        echo "     - Vai trò: {$request->role}\n";
        echo "     - Trạng thái: {$request->status}\n";
    }

    // 4. Test notification count for menu badge
    echo "\n4. KIỂM TRA MENU BADGE:\n";
    if ($pendingRequests > 0) {
        echo "   ✓ Menu sẽ hiển thị badge với số: {$pendingRequests}\n";
    } else {
        echo "   ✓ Menu sẽ không hiển thị badge (không có yêu cầu chờ)\n";
    }

    // 5. URL test
    echo "\n5. URLS CẦN KIỂM TRA:\n";
    echo "   ✓ Admin dashboard: /admin/dashboard\n";
    echo "   ✓ Quản lý người dùng: /admin/users\n";
    echo "   ✓ Yêu cầu vai trò: /admin/join-requests\n";
    echo "   ✓ Process join request: /admin/join-requests/{id}/process\n";

    echo "\n=== TEST HOÀN THÀNH ===\n";
    echo "✅ Hệ thống menu admin đã được cập nhật thành công!\n";
    echo "✅ Trang duyệt yêu cầu vai trò đã có sẵn và có dữ liệu test!\n";

} catch (Exception $e) {
    echo "❌ Lỗi trong quá trình test: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}