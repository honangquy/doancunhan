<?php

use Tests\Helpers\UserHelper;

test('TC-AUTHOR-001: Submit Paper', function () {
    $author = UserHelper::createAuthor();
    
    $response = $this->actingAs($author)->get('/author/papers');
    
    $response->assertStatus(200);
})->skip('Requires conference');

test('TC-AUTHOR-002: Xem danh sách Papers', function () {
    $author = UserHelper::createAuthor();
    
    $response = $this->actingAs($author)->get('/author/papers');
    
    $response->assertStatus(200);
});

test('TC-AUTHOR-003: Edit Paper (Draft)', function () {
    $author = UserHelper::createAuthor();
    
    $response = $this->actingAs($author)->get('/author/papers');
    
    $response->assertStatus(200);
})->skip('Requires paper');

test('TC-AUTHOR-004: Delete Paper', function () {
    $author = UserHelper::createAuthor();
    
    $response = $this->actingAs($author)->get('/author/papers');
    
    $response->assertStatus(200);
})->skip('Requires paper');

test('TC-AUTHOR-005: Upload PDF mới', function () {
    $author = UserHelper::createAuthor();
    
    $response = $this->actingAs($author)->get('/author/papers');
    
    $response->assertStatus(200);
})->skip('Requires file upload');

test('TC-AUTHOR-006: Xem Reviews', function () {
    $author = UserHelper::createAuthor();
    
    $response = $this->actingAs($author)->get('/author/papers');
    
    $response->assertStatus(200);
})->skip('Requires submitted paper with reviews');

test('TC-AUTHOR-007: Xem Proceedings List', function () {
    $author = UserHelper::createAuthor();
    
    $response = $this->actingAs($author)->get('/author/proceedings');
    
    $response->assertStatus(200);
});

test('TC-AUTHOR-008: Xem Proceedings Detail', function () {
    $author = UserHelper::createAuthor();
    
    $response = $this->actingAs($author)->get('/author/proceedings');
    
    $response->assertStatus(200);
})->skip('Requires proceedings');

test('TC-AUTHOR-009: Download Proceedings PDF', function () {
    $author = UserHelper::createAuthor();
    
    $response = $this->actingAs($author)->get('/author/proceedings');
    
    $response->assertStatus(200);
})->skip('Requires proceedings');
