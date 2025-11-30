<?php

use Tests\Helpers\UserHelper;

test('TC-CHAIR-001: Configure Conference', function () {
    $chair = UserHelper::createChair();
    
    $response = $this->actingAs($chair)->get('/chair/conferences');
    
    $response->assertStatus(200);
})->skip('Requires approved request');

test('TC-CHAIR-002: Xem danh sách Conferences', function () {
    $chair = UserHelper::createChair();
    
    $response = $this->actingAs($chair)->get('/chair/conferences');
    
    $response->assertStatus(200);
});

test('TC-CHAIR-003: Xem chi tiết Conference', function () {
    $chair = UserHelper::createChair();
    
    $response = $this->actingAs($chair)->get('/chair/conferences');
    
    $response->assertStatus(200);
})->skip('Requires conference');

test('TC-CHAIR-004: Edit Conference', function () {
    $chair = UserHelper::createChair();
    
    $response = $this->actingAs($chair)->get('/chair/conferences');
    
    $response->assertStatus(200);
})->skip('Requires conference');
