<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SampleDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Khoa
        DB::table('Khoa')->insert([
            ['faculty_code' => 'CNTT', 'faculty_name' => 'Khoa Công nghệ thông tin'],
            ['faculty_code' => 'DIEN', 'faculty_name' => 'Khoa Điện - Điện tử'],
            ['faculty_code' => 'KD', 'faculty_name' => 'Khoa Kinh doanh và Quản trị'],
        ]);

        // NguoiDung - Admin
        DB::table('NguoiDung')->insert([
            'email' => 'admin@huit.edu.vn',
            'password_hash' => Hash::make('admin123'),
            'full_name' => 'Quản trị viên',
            'is_student' => false,
            'faculty_id' => 1,
            'organization' => 'Trường Đại học Công Thương TP. Hồ Chí Minhệ TP.HCM - HUIT',
            'locked' => false,
        ]);

        // VaiTroNguoiDung - Admin
        DB::table('VaiTroNguoiDung')->insert([
            'user_id' => 1,
            'role_code' => 'ADMIN',
            'conference_id' => null,
        ]);

        // NguoiDung - Sample Authors
        for ($i = 2; $i <= 5; $i++) {
            DB::table('NguoiDung')->insert([
                'email' => "author{$i}@huit.edu.vn",
                'password_hash' => Hash::make('password123'),
                'full_name' => "Tác giả {$i}",
                'is_student' => $i > 3,
                'faculty_id' => ($i % 3) + 1,
                'organization' => 'Trường Đại học Công Thương TP. Hồ Chí Minhệ TP.HCM',
                'locked' => false,
            ]);

            DB::table('VaiTroNguoiDung')->insert([
                'user_id' => $i,
                'role_code' => 'AUTHOR',
                'conference_id' => null,
            ]);
        }

        // NguoiDung - Sample Reviewers
        for ($i = 6; $i <= 10; $i++) {
            DB::table('NguoiDung')->insert([
                'email' => "reviewer{$i}@huit.edu.vn",
                'password_hash' => Hash::make('password123'),
                'full_name' => "Phản biện viên {$i}",
                'is_student' => false,
                'faculty_id' => ($i % 3) + 1,
                'organization' => 'Trường Đại học Công Thương TP. Hồ Chí Minhệ TP.HCM',
                'locked' => false,
            ]);

            DB::table('VaiTroNguoiDung')->insert([
                'user_id' => $i,
                'role_code' => 'REVIEWER',
                'conference_id' => null,
            ]);
        }

        // HoiThao mẫu
        DB::table('HoiThao')->insert([
            [
                'parent_id' => null,
                'level_code' => 'TRUONG',
                'faculty_id' => 1,
                'title' => 'Hội thảo Khoa học CNTT HUIT 2025',
                'year' => 2025,
                'start_date' => '2025-11-25',
                'end_date' => '2025-11-30',
                'deadline_submission' => '2025-09-30',
                'deadline_review' => '2025-10-20',
                'deadline_camera_ready' => '2025-11-25',
                'status' => 'OPEN',
            ],
            [
                'parent_id' => null,
                'level_code' => 'KHOA',
                'faculty_id' => 2,
                'title' => 'Hội thảo Điện - Điện tử và Tự động hóa 2025',
                'year' => 2025,
                'start_date' => '2025-12-10',
                'end_date' => '2025-12-15',
                'deadline_submission' => '2025-10-10',
                'deadline_review' => '2025-10-20',
                'deadline_camera_ready' => '2025-12-05',
                'status' => 'OPEN',
            ],
        ]);

        // TieuBan
        DB::table('TieuBan')->insert([
            ['conference_id' => 1, 'title' => 'Trí tuệ nhân tạo và Machine Learning', 'chair_id' => 6],
            ['conference_id' => 1, 'title' => 'An ninh mạng và Bảo mật thông tin', 'chair_id' => 7],
            ['conference_id' => 1, 'title' => 'Phát triển phần mềm và Hệ thống', 'chair_id' => 8],
        ]);

        // Gán role CHAIR cho conference
        DB::table('VaiTroNguoiDung')->insert([
            ['user_id' => 6, 'role_code' => 'CHAIR', 'conference_id' => 1],
            ['user_id' => 7, 'role_code' => 'CHAIR', 'conference_id' => 1],
            ['user_id' => 8, 'role_code' => 'CHAIR', 'conference_id' => 1],
        ]);
    }
}
