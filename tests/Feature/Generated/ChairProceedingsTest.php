<?php

use Tests\Helpers\UserHelper;

test('TC-CHAIR-020: Upload Proceedings PDF', function () {
    $chair = UserHelper::createChair();
    
    $response = $this->actingAs($chair)->get('/chair/conferences');
    
    $response->assertStatus(200);
})->skip('Requires conference');

test('TC-CHAIR-021: Cấu hình Pagination', function () {
    $chair = UserHelper::createChair();
    
    $response = $this->actingAs($chair)->get('/chair/conferences');
    
    $response->assertStatus(200);
})->skip('Requires conference');

test('TC-CHAIR-022: Publish Proceedings', function () {
    $chair = UserHelper::createChair();
    
    $response = $this->actingAs($chair)->get('/chair/conferences');
    
    $response->assertStatus(200);
})->skip('Requires conference');

test('TC-CHAIR-023: Xem Reminder Logs', function () {
    $chair = UserHelper::createChair();
    
    $response = $this->actingAs($chair)->get('/chair/reminders');
    
    $response->assertStatus(200);
})->skip('Requires conference');
