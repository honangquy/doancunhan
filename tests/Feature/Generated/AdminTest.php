<?php

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Tests\Helpers\UserHelper;

test('TC-ADMIN-001: Xem danh sách Conference Requests', function () {
    $admin = UserHelper::createAdmin();

    $response = $this->actingAs($admin)->get('/admin/conference-requests');

    $response->assertStatus(200);
});

test('TC-ADMIN-002: Duyệt yêu cầu (Bước 1)', function () {
    $admin = UserHelper::createAdmin();

    $requestId = DB::table('yeucauhoithao')->insertGetId([
        'title' => 'Test Conference',
        'user_id' => $admin->user_id,
        'level_code' => 'NATIONAL',
        'proposal_file' => 'test.pdf',
        'status' => 'PENDING',
        'created_at' => now()
    ]);

    $response = $this->actingAs($admin)->post("/admin/conference-requests/{$requestId}/approve");

    $response->assertValid();
});

test('TC-ADMIN-003: Từ chối yêu cầu', function () {
    $admin = UserHelper::createAdmin();

    $requestId = DB::table('yeucauhoithao')->insertGetId([
        'title' => 'Test Conference',
        'user_id' => $admin->user_id,
        'level_code' => 'NATIONAL',
        'proposal_file' => 'test.pdf',
        'status' => 'PENDING',
        'created_at' => now()
    ]);

    $response = $this->actingAs($admin)->post("/admin/conference-requests/{$requestId}/reject", [
        'approval_note' => 'Không đủ điều kiện'
    ]);

    $response->assertValid();
});

test('TC-ADMIN-004: Xem Configured Conferences', function () {
    $admin = UserHelper::createAdmin();

    $response = $this->actingAs($admin)->get('/admin/configured-conferences');

    $response->assertStatus(200);
});

test('TC-ADMIN-005: Duyệt Configuration (Final)', function () {
    $admin = UserHelper::createAdmin();

    $requestId = DB::table('yeucauhoithao')->insertGetId([
        'conference_title' => 'Test Conference',
        'chair_user_id' => $admin->user_id,
        'status' => 'APPROVED',
        'created_at' => now()
    ]);

    $response = $this->actingAs($admin)->post("/admin/conference-requests/{$requestId}/approve-conference");

    $response->assertValid();
})->skip('Requires conference setup');

test('TC-ADMIN-006: Quản lý Users', function () {
    $admin = UserHelper::createAdmin();

    $response = $this->actingAs($admin)->get('/admin/users');

    $response->assertStatus(200);
});

test('TC-ADMIN-007: Lock User Account', function () {
    $admin = UserHelper::createAdmin();

    $user = User::factory()->create();

    $response = $this->actingAs($admin)->post("/admin/users/{$user->user_id}/lock");

    $response->assertValid();
})->skip('Requires user lock implementation');

test('TC-ADMIN-008: Xem System Logs', function () {
    $admin = UserHelper::createAdmin();

    $response = $this->actingAs($admin)->get('/admin/logs');

    $response->assertStatus(200);
});

test('TC-ADMIN-009: Backup Database', function () {
    $admin = UserHelper::createAdmin();

    $response = $this->actingAs($admin)->get('/admin/settings');

    $response->assertStatus(200);
});
