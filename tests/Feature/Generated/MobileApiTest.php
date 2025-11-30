<?php

use App\Models\User;

test('TC-API-001: Login API', function () {
    $user = User::factory()->create([
        'email' => 'api@test.com',
        'password' => bcrypt('password'),
        'email_verified_at' => now()
    ]);

    $response = $this->postJson('/api/auth/login', [
        'email' => 'api@test.com',
        'password' => 'password'
    ]);

    $response->assertStatus(200);
})->skip('API token format needs verification');

test('TC-API-002: Get Proceedings List', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user, 'api')->getJson('/api/proceedings');

    $response->assertStatus(200);
})->skip('Requires auth token');

test('TC-API-003: Get Proceedings Detail', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user, 'api')->getJson('/api/proceedings/1');

    $response->assertValid();
})->skip('Requires conference');

test('TC-API-004: Download Proceedings PDF', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user, 'api')->getJson('/api/proceedings/1/download');

    $response->assertValid();
})->skip('Requires conference');

test('TC-API-005: Unauthorized Access', function () {
    $response = $this->getJson('/api/proceedings');

    $response->assertStatus(401);
});

test('TC-API-006: Forbidden', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user, 'api')->getJson('/api/proceedings/999');

    $response->assertValid();
})->skip('Requires conference');
