<?php

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * TEST MODULE: Authentication & Authorization
 * Dựa trên TEST_SCENARIOS.md v2.0
 */

test('TC-AUTH-001: Đăng nhập thành công', function () {
    // Route: POST /login
    // Mong đợi: Redirect to role dashboard, session created

    $user = User::factory()->create([
        'email' => 'test@huit.edu.vn',
        'password' => Hash::make('123456789'),
        'email_verified_at' => now()
    ]);

    // Assign USER role (system will redirect to home if only USER role)
    DB::table('vaitronguoidung')->insert([
        'user_id' => $user->user_id,
        'role_code' => 'USER'
    ]);

    $response = $this->post('/login', [
        'email' => 'test@huit.edu.vn',
        'password' => '123456789'
    ]);

    // User with only USER role redirects to home
    $response->assertRedirect('/');
    $this->assertAuthenticated();
});

test('TC-AUTH-002: Đăng nhập thất bại - Sai password', function () {
    // Route: POST /login
    // Mong đợi: Validation error, form retained

    $user = User::factory()->create([
        'email' => 'test@huit.edu.vn',
        'password' => Hash::make('correct_password')
    ]);

    $response = $this->post('/login', [
        'email' => 'test@huit.edu.vn',
        'password' => 'wrong_password'
    ]);

    $response->assertSessionHasErrors();
    $this->assertGuest();
});

test('TC-AUTH-003: Đăng ký tài khoản mới', function () {
    // Route: POST /register
    // Mong đợi: Account created, redirect /email/verify

    $response = $this->post('/register', [
        'full_name' => 'Test User',
        'email' => 'newuser@huit.edu.vn',
        'password' => 'Test@123456',
        'password_confirmation' => 'Test@123456'
    ]);

    // After registration, redirects to verification notice
    $response->assertRedirect();
    $this->assertDatabaseHas('nguoidung', [
        'email' => 'newuser@huit.edu.vn'
    ]);
});

test('TC-AUTH-004: Email Verification', function () {
    // Route: GET /email/verify/{id}/{hash}
    // Mong đợi: email_verified_at set, redirect based on role

    $user = User::factory()->create([
        'email_verified_at' => null
    ]);

    // Assign role so user can access dashboard
    DB::table('vaitronguoidung')->insert([
        'user_id' => $user->user_id,
        'role_code' => 'USER'
    ]);

    $verificationUrl = \Illuminate\Support\Facades\URL::temporarySignedRoute(
        'verification.verify',
        now()->addMinutes(60),
        ['id' => $user->user_id, 'hash' => sha1($user->email)]
    );

    $response = $this->actingAs($user)->get($verificationUrl);

    // After verification, redirects based on role
    $response->assertRedirect();
    $this->assertNotNull($user->fresh()->email_verified_at);
});

test('TC-AUTH-005: Forgot Password', function () {
    // Route: POST /forgot-password
    // Mong đợi: Email sent, token created

    $user = User::factory()->create([
        'email' => 'test@huit.edu.vn',
        'email_verified_at' => now()
    ]);

    $response = $this->post('/forgot-password', [
        'email' => 'test@huit.edu.vn'
    ]);

    // Check response (may redirect back with status)
    $response->assertValid();
    $this->assertDatabaseHas('password_resets', [
        'email' => 'test@huit.edu.vn'
    ]);
});

test('TC-AUTH-006: Reset Password', function () {
    // Route: POST /reset-password
    // Mong đợi: Password updated, token deleted, redirect login

    $user = User::factory()->create();
    $token = \Illuminate\Support\Facades\Password::createToken($user);

    $response = $this->post('/reset-password', [
        'token' => $token,
        'email' => $user->email,
        'password' => 'NewPassword123',
        'password_confirmation' => 'NewPassword123'
    ]);

    $response->assertRedirect('/login');
    $this->assertTrue(Hash::check('NewPassword123', $user->fresh()->password));
});

test('TC-AUTH-007: Logout', function () {
    // Route: POST /logout
    // Mong đợi: Session cleared, redirect /login

    $user = User::factory()->create();

    $response = $this->actingAs($user)->post('/logout');

    $response->assertRedirect('/login');
    $this->assertGuest();
});

test('TC-AUTH-008: Profile Management', function () {
    // Route: PUT /profile
    // Mong đợi: Profile updated, success message

    $user = User::factory()->create([
        'email_verified_at' => now()
    ]);

    $response = $this->actingAs($user)->put('/profile', [
        'full_name' => 'Updated Name',
        'organization' => 'HUIT'
    ]);

    $response->assertValid();
    $this->assertDatabaseHas('nguoidung', [
        'user_id' => $user->user_id,
        'full_name' => 'Updated Name'
    ]);
});

test('TC-AUTH-009: Avatar Upload', function () {
    // Route: POST /profile/avatar
    // Mong đợi: Avatar saved, path updated in DB

    $user = User::factory()->create();
    $file = \Illuminate\Http\UploadedFile::fake()->image('avatar.jpg', 200, 200);

    $response = $this->actingAs($user)->post('/profile/avatar', [
        'avatar' => $file
    ]);

    $response->assertSessionHas('success');
    $this->assertNotNull($user->fresh()->avatar);
})->skip('Requires storage setup');
