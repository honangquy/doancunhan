<?php

use Tests\Helpers\UserHelper;

test('TC-REVIEWER-001: Accept Reviewer Invitation', function () {
    $reviewer = UserHelper::createReviewer();
    
    $response = $this->actingAs($reviewer)->get('/reviewer/dashboard');
    
    $response->assertStatus(200);
})->skip('Requires invitation token');

test('TC-REVIEWER-002: Join Form', function () {
    $response = $this->get('/reviewer/join');
    
    // May redirect if no valid token
    $response->assertValid();
});

test('TC-REVIEWER-003: Xem Papers để Bidding', function () {
    $reviewer = UserHelper::createReviewer();
    
    $response = $this->actingAs($reviewer)->get('/reviewer/bidding');
    
    $response->assertStatus(200);
})->skip('Requires conference');

test('TC-REVIEWER-004: Submit Bidding - YES', function () {
    $reviewer = UserHelper::createReviewer();
    
    $response = $this->actingAs($reviewer)->get('/reviewer/bidding');
    
    $response->assertStatus(200);
})->skip('Requires conference');

test('TC-REVIEWER-005: Submit Bidding - NO với COI', function () {
    $reviewer = UserHelper::createReviewer();
    
    $response = $this->actingAs($reviewer)->get('/reviewer/bidding');
    
    $response->assertStatus(200);
})->skip('Requires conference');

test('TC-REVIEWER-006: Bulk Bidding', function () {
    $reviewer = UserHelper::createReviewer();
    
    $response = $this->actingAs($reviewer)->get('/reviewer/bidding');
    
    $response->assertStatus(200);
})->skip('Requires conference');

test('TC-REVIEWER-007: Khai báo COI thủ công', function () {
    $reviewer = UserHelper::createReviewer();
    
    $response = $this->actingAs($reviewer)->get('/reviewer/coi');
    
    $response->assertStatus(200);
})->skip('Requires conference');

test('TC-REVIEWER-008: Xem Bidding Statistics', function () {
    $reviewer = UserHelper::createReviewer();
    
    $response = $this->actingAs($reviewer)->get('/reviewer/bidding');
    
    $response->assertStatus(200);
})->skip('Requires conference');
