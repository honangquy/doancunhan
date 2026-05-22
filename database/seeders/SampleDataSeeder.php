<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SampleDataSeeder extends Seeder
{
    /**
     * Seed the application's database with initial admin account and sample data.
     *
     * Admin credentials are read from .env variables:
     *   ADMIN_EMAIL, ADMIN_PASSWORD, ADMIN_NAME
     *
     * @return void
     */
    public function run()
    {
        // ========================================
        // 1. Khoa (Faculties)
        // ========================================
        DB::table('Khoa')->insertOrIgnore([
            ['faculty_code' => 'CNTT', 'faculty_name' => 'Khoa Công nghệ thông tin'],
            ['faculty_code' => 'DIEN', 'faculty_name' => 'Khoa Điện - Điện tử'],
            ['faculty_code' => 'KD', 'faculty_name' => 'Khoa Kinh doanh và Quản trị'],
        ]);

        // ========================================
        // 2. Admin account (from .env)
        // ========================================
        $adminEmail    = env('ADMIN_EMAIL', 'admin@huit.edu.vn');
        $adminPassword = env('ADMIN_PASSWORD', 'changeme');
        $adminName     = env('ADMIN_NAME', 'Quản trị viên');

        $adminId = DB::table('NguoiDung')->insertGetId([
            'email'         => $adminEmail,
            'password_hash' => Hash::make($adminPassword),
            'full_name'     => $adminName,
            'is_student'    => false,
            'faculty_id'    => 1,
            'organization'  => 'Trường Đại học Công Thương TP.HCM - HUIT',
            'locked'        => false,
            'created_at'    => now(),
        ]);

        DB::table('VaiTroNguoiDung')->insert([
            'user_id'       => $adminId,
            'role_code'     => 'ADMIN',
            'conference_id' => null,
        ]);

        $this->command->info("Admin account created: {$adminEmail}");

        // ========================================
        // 3. Sample conferences
        // ========================================
        DB::table('HoiThao')->insertOrIgnore([
            [
                'parent_id'              => null,
                'level_code'             => 'TRUONG',
                'faculty_id'             => 1,
                'title'                  => 'Hội thảo Khoa học CNTT HUIT 2025',
                'year'                   => 2025,
                'start_date'             => '2025-11-25',
                'end_date'               => '2025-11-30',
                'deadline_submission'    => '2025-09-30',
                'deadline_review'        => '2025-10-20',
                'deadline_camera_ready'  => '2025-11-25',
                'status'                 => 'OPEN',
            ],
            [
                'parent_id'              => null,
                'level_code'             => 'KHOA',
                'faculty_id'             => 2,
                'title'                  => 'Hội thảo Điện - Điện tử và Tự động hóa 2025',
                'year'                   => 2025,
                'start_date'             => '2025-12-10',
                'end_date'               => '2025-12-15',
                'deadline_submission'    => '2025-10-10',
                'deadline_review'        => '2025-10-20',
                'deadline_camera_ready'  => '2025-12-05',
                'status'                 => 'OPEN',
            ],
        ]);
    }
}
