<?php

use Tests\Helpers\UserHelper;

test('TC-REVIEWER-009: Xem Assignments', function () {
    $reviewer = UserHelper::createReviewer();
    
    $response = $this->actingAs($reviewer)->get('/reviewer/assignments');
    
    $response->assertStatus(200);
});

test('TC-REVIEWER-010: Accept Assignment', function () {
    $reviewer = UserHelper::createReviewer();
    
    $response = $this->actingAs($reviewer)->get('/reviewer/assignments');
    
    $response->assertStatus(200);
})->skip('Requires assignment');

test('TC-REVIEWER-011: Decline Assignment', function () {
    $reviewer = UserHelper::createReviewer();
    
    $response = $this->actingAs($reviewer)->get('/reviewer/assignments');
    
    $response->assertStatus(200);
})->skip('Requires assignment');

test('TC-REVIEWER-012: View Assignment Detail', function () {
    $reviewer = UserHelper::createReviewer();
    
    $response = $this->actingAs($reviewer)->get('/reviewer/assignments');
    
    $response->assertStatus(200);
})->skip('Requires assignment');

test('TC-REVIEWER-013: Download Paper', function () {
    $reviewer = UserHelper::createReviewer();
    
    $response = $this->actingAs($reviewer)->get('/reviewer/assignments');
    
    $response->assertStatus(200);
})->skip('Requires assignment');

test('TC-REVIEWER-014: Write Review - Create', function () {
    $reviewer = UserHelper::createReviewer();
    
    $response = $this->actingAs($reviewer)->get('/reviewer/assignments');
    
    $response->assertStatus(200);
})->skip('Requires assignment');

test('TC-REVIEWER-015: Edit Review (Draft)', function () {
    $reviewer = UserHelper::createReviewer();
    
    $response = $this->actingAs($reviewer)->get('/reviewer/reviews');
    
    $response->assertStatus(200);
})->skip('Requires review');

test('TC-REVIEWER-016: Submit Review Final', function () {
    $reviewer = UserHelper::createReviewer();
    
    $response = $this->actingAs($reviewer)->get('/reviewer/reviews');
    
    $response->assertStatus(200);
});

test('TC-REVIEWER-017: Xem lịch sử Reviews', function () {
    $reviewer = UserHelper::createReviewer();
    
    $response = $this->actingAs($reviewer)->get('/reviewer/reviews');
    
    $response->assertStatus(200);
});
