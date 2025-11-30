<?php

use Tests\Helpers\UserHelper;

test('TC-CHAIR-017: Tạo Broadcast Announcement', function () {
    $chair = UserHelper::createChair();
    
    $response = $this->actingAs($chair)->get('/chair/announcements/create');
    
    $response->assertStatus(200);
});

test('TC-CHAIR-018: Xem danh sách Announcements', function () {
    $chair = UserHelper::createChair();
    
    $response = $this->actingAs($chair)->get('/chair/announcements');
    
    $response->assertStatus(200);
});

test('TC-CHAIR-019: Xem Statistics', function () {
    $chair = UserHelper::createChair();
    
    $response = $this->actingAs($chair)->get('/chair/announcements');
    
    $response->assertStatus(200);
})->skip('Requires announcement model');
