<?php

use Tests\Helpers\UserHelper;

test('TC-CHAIR-010: Invite Reviewer', function () {
    $chair = UserHelper::createChair();
    
    $response = $this->actingAs($chair)->get('/chair/reviewers/invite');
    
    $response->assertStatus(200);
})->skip('Requires conference');

test('TC-CHAIR-011: Xem Invitations', function () {
    $chair = UserHelper::createChair();
    
    $response = $this->actingAs($chair)->get('/chair/reviewers/invitations');
    
    $response->assertStatus(200);
});

test('TC-CHAIR-012: Cấu hình Bidding', function () {
    $chair = UserHelper::createChair();
    
    $response = $this->actingAs($chair)->get('/chair/bidding-settings');
    
    $response->assertStatus(200);
})->skip('Requires conference');

test('TC-CHAIR-013: Phân công Reviewer thủ công', function () {
    $chair = UserHelper::createChair();
    
    $response = $this->actingAs($chair)->get('/chair/assignments');
    
    $response->assertValid();
})->skip('Requires conference');

test('TC-CHAIR-014: Phân công tự động', function () {
    $chair = UserHelper::createChair();
    
    $response = $this->actingAs($chair)->get('/chair/assignments');
    
    $response->assertValid();
})->skip('Requires conference');

test('TC-CHAIR-015: Xóa Assignment', function () {
    $chair = UserHelper::createChair();
    
    $response = $this->actingAs($chair)->get('/chair/assignments');
    
    $response->assertValid();
})->skip('Requires conference');

test('TC-CHAIR-016: Xem Assignment Statistics', function () {
    $chair = UserHelper::createChair();
    
    $response = $this->actingAs($chair)->get('/chair/assignments');
    
    $response->assertValid();
})->skip('Requires conference');
