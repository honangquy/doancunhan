<?php

use App\Models\User;

test('TC-CONF-REQ-001: Tạo yêu cầu tổ chức hội thảo', function () {
    $user = User::factory()->create(['email_verified_at' => now()]);
    
    $response = $this->actingAs($user)->get('/create-conference');
    
    $response->assertStatus(200);
});

test('TC-CONF-REQ-002: Xem danh sách requests', function () {
    $user = User::factory()->create(['email_verified_at' => now()]);
    
    $response = $this->actingAs($user)->get('/conference-management/requests');
    
    $response->assertStatus(200);
});
