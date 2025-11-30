<?php

namespace Tests\Helpers;

use App\Models\User;
use Illuminate\Support\Facades\DB;

class UserHelper
{
    /**
     * Create user with specific role
     */
    public static function createUserWithRole(string $roleCode, array $attributes = [])
    {
        $user = User::factory()->create(array_merge([
            'email_verified_at' => now()
        ], $attributes));

        // Ensure role exists in LoaiVaiTro
        self::ensureRoleExists($roleCode);

        // Assign role
        DB::table('vaitronguoidung')->insert([
            'user_id' => $user->user_id,
            'role_code' => $roleCode
        ]);

        return $user;
    }

    /**
     * Ensure role exists in LoaiVaiTro table
     */
    public static function ensureRoleExists(string $roleCode)
    {
        $roleNames = [
            'ADMIN' => 'Administrator',
            'CHAIR' => 'Conference Chair',
            'AUTHOR' => 'Author',
            'REVIEWER' => 'Reviewer',
            'USER' => 'User'
        ];

        DB::table('LoaiVaiTro')->insertOrIgnore([
            'role_code' => $roleCode,
            'role_name' => $roleNames[$roleCode] ?? $roleCode
        ]);
    }

    /**
     * Create ADMIN user
     */
    public static function createAdmin(array $attributes = [])
    {
        return self::createUserWithRole('ADMIN', $attributes);
    }

    /**
     * Create CHAIR user
     */
    public static function createChair(array $attributes = [])
    {
        return self::createUserWithRole('CHAIR', $attributes);
    }

    /**
     * Create AUTHOR user
     */
    public static function createAuthor(array $attributes = [])
    {
        return self::createUserWithRole('AUTHOR', $attributes);
    }

    /**
     * Create REVIEWER user
     */
    public static function createReviewer(array $attributes = [])
    {
        return self::createUserWithRole('REVIEWER', $attributes);
    }
}
