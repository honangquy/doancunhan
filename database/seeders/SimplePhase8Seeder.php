<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SimplePhase8Seeder extends Seeder
{
    /**
     * Simplified seeder for Phase 8 - matches actual schema
     */
    public function run(): void
    {
        $this->command->info('🚀 Starting Simple Phase 8 Seeder...');
        
        // Disable foreign key checks temporarily
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        // ========================================
        // 1. LOOKUP TABLES
        // ========================================
        $this->command->info('📝 Step 1: Seeding Lookup Tables...');
        
        // Paper Status
        DB::table('TrangThaiBaiBao')->insertOrIgnore([
            ['status_code' => 'SUBMITTED', 'status_name' => 'Đã nộp'],
            ['status_code' => 'UNDER_REVIEW', 'status_name' => 'Đang phản biện'],
            ['status_code' => 'ACCEPTED', 'status_name' => 'Chấp nhận'],
            ['status_code' => 'REJECTED', 'status_name' => 'Từ chối'],
            ['status_code' => 'REVISION', 'status_name' => 'Yêu cầu sửa'],
        ]);
        
        // Bidding Values
        DB::table('GiaTriBidding')->insertOrIgnore([
            ['bidding_code' => 'INTERESTED', 'bidding_name' => 'Quan tâm', 'score' => 3],
            ['bidding_code' => 'MAYBE', 'bidding_name' => 'Có thể', 'score' => 2],
            ['bidding_code' => 'NOT_INTERESTED', 'bidding_name' => 'Không quan tâm', 'score' => 1],
        ]);
        
        // Roles
        DB::table('LoaiVaiTro')->insertOrIgnore([
            ['role_code' => 'ADMIN', 'role_name' => 'Quản trị viên'],
            ['role_code' => 'AUTHOR', 'role_name' => 'Tác giả'],
            ['role_code' => 'REVIEWER', 'role_name' => 'Phản biện viên'],
            ['role_code' => 'CHAIR', 'role_name' => 'Chủ tịch hội thảo'],
        ]);
        
        // Conference Level
        DB::table('CapHoiThao')->insertOrIgnore([
            ['level_code' => 'KHOA', 'level_name' => 'Cấp khoa'],
            ['level_code' => 'TRUONG', 'level_name' => 'Cấp trường'],
        ]);
        
        // Recommendations
        DB::table('LoaiKhuyenNghi')->insertOrIgnore([
            ['recommendation_code' => 'ACCEPT', 'recommendation_name' => 'Chấp nhận'],
            ['recommendation_code' => 'MINOR', 'recommendation_name' => 'Sửa nhỏ'],
            ['recommendation_code' => 'MAJOR', 'recommendation_name' => 'Sửa lớn'],
            ['recommendation_code' => 'REJECT', 'recommendation_name' => 'Từ chối'],
        ]);
        
        // Assignment Status
        DB::table('TrangThaiPhanCong')->insertOrIgnore([
            ['status_code' => 'INVITED'],
            ['status_code' => 'ACCEPTED'],
            ['status_code' => 'DECLINED'],
            ['status_code' => 'COMPLETED'],
        ]);
        
        // COI Types
        DB::table('LoaiCOI')->insertOrIgnore([
            ['coi_code' => 'SAME_FACULTY', 'coi_name' => 'Cùng khoa'],
            ['coi_code' => 'ADVISOR', 'coi_name' => 'Quan hệ hướng dẫn'],
            ['coi_code' => 'COAUTHOR', 'coi_name' => 'Đồng tác giả'],
        ]);
        
        $this->command->info('✅ Lookup tables seeded successfully!');
        
        // ========================================
        // 2. FACULTIES
        // ========================================
        $this->command->info('📝 Step 2: Seeding Faculties...');
        
        DB::table('Khoa')->insertOrIgnore([
            ['faculty_code' => 'CNTT', 'faculty_name' => 'Công nghệ Thông tin'],
            ['faculty_code' => 'KTPM', 'faculty_name' => 'Kỹ thuật Phần mềm'],
            ['faculty_code' => 'MMT', 'faculty_name' => 'Mạng máy tính và Truyền thông'],
            ['faculty_code' => 'KHMT', 'faculty_name' => 'Khoa học Máy tính'],
            ['faculty_code' => 'HTTT', 'faculty_name' => 'Hệ thống Thông tin'],
        ]);
        
        $this->command->info('✅ Faculties seeded successfully!');
        
        // ========================================
        // 3. USERS (Simple version - 20 users only)
        // ========================================
        $this->command->info('📝 Step 3: Seeding Users...');
        
        $password = Hash::make('password123');
        
        $users = [
            // Admins (3)
            ['email' => 'admin@huit.edu.vn', 'full_name' => 'Admin Hệ thống', 'faculty_id' => 1, 'role' => 'ADMIN'],
            ['email' => 'admin2@huit.edu.vn', 'full_name' => 'Admin Phụ', 'faculty_id' => 2, 'role' => 'ADMIN'],
            ['email' => 'superadmin@huit.edu.vn', 'full_name' => 'Super Admin', 'faculty_id' => 1, 'role' => 'ADMIN'],
            
            // Chairs (3)
            ['email' => 'chair1@huit.edu.vn', 'full_name' => 'Chair Nguyễn Văn A', 'faculty_id' => 1, 'role' => 'CHAIR'],
            ['email' => 'chair2@huit.edu.vn', 'full_name' => 'Chair Trần Thị B', 'faculty_id' => 2, 'role' => 'CHAIR'],
            ['email' => 'chair3@huit.edu.vn', 'full_name' => 'Chair Lê Văn C', 'faculty_id' => 3, 'role' => 'CHAIR'],
            
            // Reviewers (7)
            ['email' => 'reviewer1@huit.edu.vn', 'full_name' => 'Reviewer Phạm Văn D', 'faculty_id' => 1, 'role' => 'REVIEWER'],
            ['email' => 'reviewer2@huit.edu.vn', 'full_name' => 'Reviewer Hoàng Thị E', 'faculty_id' => 2, 'role' => 'REVIEWER'],
            ['email' => 'reviewer3@huit.edu.vn', 'full_name' => 'Reviewer Đỗ Văn F', 'faculty_id' => 3, 'role' => 'REVIEWER'],
            ['email' => 'reviewer4@huit.edu.vn', 'full_name' => 'Reviewer Vũ Thị G', 'faculty_id' => 4, 'role' => 'REVIEWER'],
            ['email' => 'reviewer5@huit.edu.vn', 'full_name' => 'Reviewer Bùi Văn H', 'faculty_id' => 5, 'role' => 'REVIEWER'],
            ['email' => 'reviewer6@huit.edu.vn', 'full_name' => 'Reviewer Đinh Thị I', 'faculty_id' => 1, 'role' => 'REVIEWER'],
            ['email' => 'reviewer7@huit.edu.vn', 'full_name' => 'Reviewer Mai Văn K', 'faculty_id' => 2, 'role' => 'REVIEWER'],
            
            // Authors (7)
            ['email' => 'author1@huit.edu.vn', 'full_name' => 'Author Ngô Văn L', 'faculty_id' => 1, 'role' => 'AUTHOR'],
            ['email' => 'author2@huit.edu.vn', 'full_name' => 'Author Dương Thị M', 'faculty_id' => 2, 'role' => 'AUTHOR'],
            ['email' => 'author3@huit.edu.vn', 'full_name' => 'Author Cao Văn N', 'faculty_id' => 3, 'role' => 'AUTHOR'],
            ['email' => 'author4@huit.edu.vn', 'full_name' => 'Author Lý Thị O', 'faculty_id' => 4, 'role' => 'AUTHOR'],
            ['email' => 'author5@huit.edu.vn', 'full_name' => 'Author Trương Văn P', 'faculty_id' => 5, 'role' => 'AUTHOR'],
            ['email' => 'author6@huit.edu.vn', 'full_name' => 'Author Võ Thị Q', 'faculty_id' => 1, 'role' => 'AUTHOR'],
            ['email' => 'author7@huit.edu.vn', 'full_name' => 'Author Phan Văn R', 'faculty_id' => 2, 'role' => 'AUTHOR'],
        ];
        
        foreach ($users as $user) {
            $userId = DB::table('NguoiDung')->insertGetId([
                'email' => $user['email'],
                'password_hash' => $password,
                'full_name' => $user['full_name'],
                'is_student' => 0,
                'faculty_id' => $user['faculty_id'],
                'organization' => 'HUIT',
                'locked' => 0,
                'created_at' => now(),
            ]);
            
            // Assign role
            DB::table('VaiTroNguoiDung')->insert([
                'user_id' => $userId,
                'role_code' => $user['role'],
                'conference_id' => null,
            ]);
        }
        
        $this->command->info('✅ Users seeded successfully! (20 users created)');
        
        // ========================================
        // 4. CONFERENCES (3 conferences)
        // ========================================
        $this->command->info('📝 Step 4: Seeding Conferences...');
        
        DB::table('HoiThao')->insert([
            [
                'parent_id' => null,
                'level_code' => 'TRUONG',
                'faculty_id' => 1,
                'title' => 'HUIT International Conference on ICT 2025',
                'year' => 2025,
                'start_date' => '2025-10-15',
                'end_date' => '2025-10-18',
                'deadline_submission' => '2025-09-01',
                'deadline_review' => '2025-09-25',
                'deadline_camera_ready' => '2025-10-10',
                'status' => 'ACTIVE',
            ],
            [
                'parent_id' => null,
                'level_code' => 'TRUONG',
                'faculty_id' => 2,
                'title' => 'HUIT Security Summit 2025',
                'year' => 2025,
                'start_date' => '2025-11-20',
                'end_date' => '2025-11-22',
                'deadline_submission' => '2025-10-01',
                'deadline_review' => '2025-10-25',
                'deadline_camera_ready' => '2025-11-10',
                'status' => 'ACTIVE',
            ],
            [
                'parent_id' => null,
                'level_code' => 'KHOA',
                'faculty_id' => 3,
                'title' => 'HUIT AI & Data Science Forum 2025',
                'year' => 2025,
                'start_date' => '2025-12-05',
                'end_date' => '2025-12-07',
                'deadline_submission' => '2025-10-15',
                'deadline_review' => '2025-11-10',
                'deadline_camera_ready' => '2025-11-25',
                'status' => 'ACTIVE',
            ],
        ]);
        
        $this->command->info('✅ Conferences seeded successfully!');
        
        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        
        // ========================================
        // SUMMARY
        // ========================================
        $this->command->info('');
        $this->command->info('========================================');
        $this->command->info('✅ PHASE 8 SEEDER COMPLETED SUCCESSFULLY!');
        $this->command->info('========================================');
        $this->command->info('');
        $this->command->info('📊 Summary:');
        $this->command->info('  - Lookup Tables: ✅ All seeded');
        $this->command->info('  - Faculties: ✅ 5 faculties');
        $this->command->info('  - Users: ✅ 20 users');
        $this->command->info('    * 3 Admins');
        $this->command->info('    * 3 Chairs');
        $this->command->info('    * 7 Reviewers');
        $this->command->info('    * 7 Authors');
        $this->command->info('  - Conferences: ✅ 3 conferences');
        $this->command->info('');
        $this->command->info('🔑 Default Password: password123');
        $this->command->info('');
        $this->command->info('📧 Sample Login Accounts:');
        $this->command->info('  Admin: admin@huit.edu.vn / password123');
        $this->command->info('  Chair: chair1@huit.edu.vn / password123');
        $this->command->info('  Reviewer: reviewer1@huit.edu.vn / password123');
        $this->command->info('  Author: author1@huit.edu.vn / password123');
        $this->command->info('');
    }
}
