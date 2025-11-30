<?php

use Tests\Helpers\UserHelper;

test('TC-CHAIR-005: Xem danh sách Papers', function () {
    $chair = UserHelper::createChair();
    
    $response = $this->actingAs($chair)->get('/chair/papers');
    
    $response->assertStatus(200);
})->skip('Requires conference');

test('TC-CHAIR-006: Xem chi tiết Paper', function () {
    $chair = UserHelper::createChair();
    
    $response = $this->actingAs($chair)->get('/chair/papers');
    
    $response->assertStatus(200);
})->skip('Requires conference');

test('TC-CHAIR-007: Download Paper PDF', function () {
    $chair = UserHelper::createChair();
    
    $response = $this->actingAs($chair)->get('/chair/papers');
    
    $response->assertStatus(200);
})->skip('Requires conference');

test('TC-CHAIR-008: Xem Reviews', function () {
    $chair = UserHelper::createChair();
    
    $response = $this->actingAs($chair)->get('/chair/papers');
    
    $response->assertStatus(200);
})->skip('Requires conference');

test('TC-CHAIR-009: Make Final Decision', function () {
    $chair = UserHelper::createChair();
    
    $response = $this->actingAs($chair)->get('/chair/papers');
    
    $response->assertStatus(200);
})->skip('Requires conference');
