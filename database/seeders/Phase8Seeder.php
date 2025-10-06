<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class Phase8Seeder extends Seeder
{
    /**
     * Run the database seeds for Phase 8 - Backend Integration
     * This seeder creates sample data for all dashboards
     */
    public function run(): void
    {
        // Clear existing data (careful in production!)
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        // Truncate tables in correct order
        DB::table('PhanCongPhanBien')->truncate();
        DB::table('BaiBao')->truncate();
        DB::table('HoiThao')->truncate();
        DB::table('VaiTroNguoiDung')->truncate();
        DB::table('NguoiDung')->truncate();
        DB::table('Khoa')->truncate();
        
        // Clear lookup tables
        DB::table('TrangThaiBaiBao')->truncate();
        DB::table('GiaTriBidding')->truncate();
        DB::table('LoaiVaiTro')->truncate();
        DB::table('CapHoiThao')->truncate();
        DB::table('LoaiKhuyenNghi')->truncate();
        DB::table('TrangThaiPhanCong')->truncate();
        DB::table('LoaiCOI')->truncate();
        
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        
        // ========================================
        // 1. LOOKUP TABLES
        // ========================================
        
        // Paper Status
        DB::table('TrangThaiBaiBao')->insert([
            ['status_code' => 'SUBMITTED', 'status_name' => 'Đã nộp'],
            ['status_code' => 'UNDER_REVIEW', 'status_name' => 'Đang phản biện'],
            ['status_code' => 'ACCEPTED', 'status_name' => 'Chấp nhận'],
            ['status_code' => 'REJECTED', 'status_name' => 'Từ chối'],
            ['status_code' => 'REVISION', 'status_name' => 'Yêu cầu sửa'],
        ]);
        
        // Bidding Values
        DB::table('GiaTriBidding')->insert([
            ['bidding_code' => 'INTERESTED', 'bidding_name' => 'Quan tâm', 'score' => 3],
            ['bidding_code' => 'MAYBE', 'bidding_name' => 'Có thể', 'score' => 2],
            ['bidding_code' => 'NOT_INTERESTED', 'bidding_name' => 'Không quan tâm', 'score' => 1],
        ]);
        
        // Roles
        DB::table('LoaiVaiTro')->insert([
            ['role_code' => 'ADMIN', 'role_name' => 'Quản trị viên'],
            ['role_code' => 'AUTHOR', 'role_name' => 'Tác giả'],
            ['role_code' => 'REVIEWER', 'role_name' => 'Phản biện viên'],
            ['role_code' => 'CHAIR', 'role_name' => 'Chủ tịch hội thảo'],
        ]);
        
        // Conference Level
        DB::table('CapHoiThao')->insert([
            ['level_code' => 'KHOA', 'level_name' => 'Cấp khoa'],
            ['level_code' => 'TRUONG', 'level_name' => 'Cấp trường'],
        ]);
        
        // Recommendations
        DB::table('LoaiKhuyenNghi')->insert([
            ['recommendation_code' => 'ACCEPT', 'recommendation_name' => 'Chấp nhận'],
            ['recommendation_code' => 'MINOR', 'recommendation_name' => 'Sửa nhỏ'],
            ['recommendation_code' => 'MAJOR', 'recommendation_name' => 'Sửa lớn'],
            ['recommendation_code' => 'REJECT', 'recommendation_name' => 'Từ chối'],
        ]);
        
        // Assignment Status
        DB::table('TrangThaiPhanCong')->insert([
            ['status_code' => 'PENDING'],
            ['status_code' => 'ACCEPTED'],
            ['status_code' => 'DECLINED'],
            ['status_code' => 'COMPLETED'],
        ]);
        
        // COI Types
        DB::table('LoaiCOI')->insert([
            ['coi_code' => 'SAME_FACULTY', 'coi_name' => 'Cùng khoa'],
            ['coi_code' => 'ADVISOR', 'coi_name' => 'Quan hệ hướng dẫn'],
            ['coi_code' => 'COAUTHOR', 'coi_name' => 'Đồng tác giả'],
        ]);
        
        // ========================================
        // 2. FACULTIES
        // ========================================
        DB::table('Khoa')->insert([
            ['faculty_id' => 1, 'faculty_code' => 'CNTT', 'faculty_name' => 'Công nghệ Thông tin'],
            ['faculty_id' => 2, 'faculty_code' => 'KTPM', 'faculty_name' => 'Kỹ thuật Phần mềm'],
            ['faculty_id' => 3, 'faculty_code' => 'MMT', 'faculty_name' => 'Mạng máy tính và Truyền thông'],
            ['faculty_id' => 4, 'faculty_code' => 'KHMT', 'faculty_name' => 'Khoa học Máy tính'],
            ['faculty_id' => 5, 'faculty_code' => 'HTTT', 'faculty_name' => 'Hệ thống Thông tin'],
        ]);
        
        // ========================================
        // 3. USERS
        // ========================================
        $password = Hash::make('password123'); // Default password for all users
        
        // Admins (6 users)
        DB::table('NguoiDung')->insert([
            ['user_id' => 1, 'email' => 'admin@huit.edu.vn', 'password_hash' => $password, 'full_name' => 'Admin Hệ thống', 'is_student' => 0, 'faculty_id' => 1, 'organization' => 'HUIT', 'created_at' => now()],
            ['user_id' => 2, 'email' => 'admin2@huit.edu.vn', 'password_hash' => $password, 'full_name' => 'Admin Phụ', 'is_student' => 0, 'faculty_id' => 2, 'organization' => 'HUIT', 'created_at' => now()],
            ['user_id' => 3, 'email' => 'admin3@huit.edu.vn', 'password_hash' => $password, 'full_name' => 'Admin Kỹ thuật', 'is_student' => 0, 'faculty_id' => 3, 'organization' => 'HUIT', 'created_at' => now()],
            ['user_id' => 4, 'email' => 'admin4@huit.edu.vn', 'password_hash' => $password, 'full_name' => 'Admin Nội dung', 'is_student' => 0, 'faculty_id' => 4, 'organization' => 'HUIT', 'created_at' => now()],
            ['user_id' => 5, 'email' => 'admin5@huit.edu.vn', 'password_hash' => $password, 'full_name' => 'Admin Hỗ trợ', 'is_student' => 0, 'faculty_id' => 5, 'organization' => 'HUIT', 'created_at' => now()],
            ['user_id' => 6, 'email' => 'superadmin@huit.edu.vn', 'password_hash' => $password, 'full_name' => 'Super Admin', 'is_student' => 0, 'faculty_id' => 1, 'organization' => 'HUIT', 'created_at' => now()],
        ]);
        
        // Chairs (18 users - starting from ID 7)
        for ($i = 7; $i <= 24; $i++) {
            DB::table('NguoiDung')->insert([
                'user_id' => $i,
                'email' => "chair{$i}@huit.edu.vn",
                'password_hash' => $password,
                'full_name' => "Chair User {$i}",
                'is_student' => 0,
                'faculty_id' => ($i % 5) + 1,
                'organization' => 'HUIT',
                'created_at' => now(),
            ]);
        }
        
        // Reviewers (68 users - starting from ID 25)
        for ($i = 25; $i <= 92; $i++) {
            DB::table('NguoiDung')->insert([
                'user_id' => $i,
                'email' => "reviewer{$i}@huit.edu.vn",
                'password_hash' => $password,
                'full_name' => "Reviewer User {$i}",
                'is_student' => 0,
                'faculty_id' => ($i % 5) + 1,
                'organization' => 'HUIT',
                'created_at' => now(),
            ]);
        }
        
        // Authors (156 users - starting from ID 93)
        for ($i = 93; $i <= 248; $i++) {
            $isStudent = ($i % 3 == 0) ? 1 : 0; // 1/3 are students
            DB::table('NguoiDung')->insert([
                'user_id' => $i,
                'email' => "author{$i}@huit.edu.vn",
                'password_hash' => $password,
                'full_name' => "Author User {$i}",
                'is_student' => $isStudent,
                'faculty_id' => ($i % 5) + 1,
                'organization' => $isStudent ? 'HUIT Student' : 'HUIT',
                'created_at' => now(),
            ]);
        }
        
        // ========================================
        // 4. USER ROLES
        // ========================================
        
        // Assign Admin roles
        for ($i = 1; $i <= 6; $i++) {
            DB::table('VaiTroNguoiDung')->insert([
                'user_id' => $i,
                'role_code' => 'ADMIN',
                'conference_id' => null,
            ]);
        }
        
        // Assign Chair roles
        for ($i = 7; $i <= 24; $i++) {
            DB::table('VaiTroNguoiDung')->insert([
                'user_id' => $i,
                'role_code' => 'CHAIR',
                'conference_id' => null,
            ]);
        }
        
        // Assign Reviewer roles
        for ($i = 25; $i <= 92; $i++) {
            DB::table('VaiTroNguoiDung')->insert([
                'user_id' => $i,
                'role_code' => 'REVIEWER',
                'conference_id' => null,
            ]);
        }
        
        // Assign Author roles
        for ($i = 93; $i <= 248; $i++) {
            DB::table('VaiTroNguoiDung')->insert([
                'user_id' => $i,
                'role_code' => 'AUTHOR',
                'conference_id' => null,
            ]);
        }
        
        // ========================================
        // 5. CONFERENCES (18 total, 8 active)
        // ========================================
        DB::table('HoiThao')->insert([
            // Active Conferences (8)
            [
                'conference_id' => 1,
                'conference_name' => 'HUIT International Conference on ICT 2025',
                'conference_code' => 'HUIT-ICI-2025',
                'chair_id' => 7,
                'start_date' => '2025-10-15',
                'end_date' => '2025-10-18',
                'location' => 'HUIT Campus',
                'level_code' => 'TRUONG',
                'description' => 'International conference on Information and Communication Technology',
                'submission_deadline' => '2025-09-01',
                'review_deadline' => '2025-09-25',
                'notification_date' => '2025-10-05',
                'created_at' => now(),
            ],
            [
                'conference_id' => 2,
                'conference_name' => 'HUIT Security Summit 2025',
                'conference_code' => 'HUIT-SEC-2025',
                'chair_id' => 8,
                'start_date' => '2025-11-20',
                'end_date' => '2025-11-22',
                'location' => 'HUIT Campus',
                'level_code' => 'TRUONG',
                'description' => 'Cybersecurity and Information Security Conference',
                'submission_deadline' => '2025-10-01',
                'review_deadline' => '2025-10-25',
                'notification_date' => '2025-11-05',
                'created_at' => now(),
            ],
            [
                'conference_id' => 3,
                'conference_name' => 'HUIT AI & Data Science Forum 2025',
                'conference_code' => 'HUIT-AI-2025',
                'chair_id' => 9,
                'start_date' => '2025-12-05',
                'end_date' => '2025-12-07',
                'location' => 'HUIT Campus',
                'level_code' => 'TRUONG',
                'description' => 'Artificial Intelligence and Data Science Conference',
                'submission_deadline' => '2025-10-15',
                'review_deadline' => '2025-11-10',
                'notification_date' => '2025-11-20',
                'created_at' => now(),
            ],
            [
                'conference_id' => 4,
                'conference_name' => 'HUIT Software Engineering Workshop 2025',
                'conference_code' => 'HUIT-SE-2025',
                'chair_id' => 10,
                'start_date' => '2025-10-25',
                'end_date' => '2025-10-26',
                'location' => 'HUIT Campus',
                'level_code' => 'KHOA',
                'description' => 'Software Engineering Best Practices',
                'submission_deadline' => '2025-09-20',
                'review_deadline' => '2025-10-10',
                'notification_date' => '2025-10-15',
                'created_at' => now(),
            ],
            [
                'conference_id' => 5,
                'conference_name' => 'HUIT IoT & Smart Systems 2025',
                'conference_code' => 'HUIT-IOT-2025',
                'chair_id' => 11,
                'start_date' => '2025-11-15',
                'end_date' => '2025-11-17',
                'location' => 'HUIT Campus',
                'level_code' => 'TRUONG',
                'description' => 'Internet of Things and Smart Systems',
                'submission_deadline' => '2025-09-30',
                'review_deadline' => '2025-10-20',
                'notification_date' => '2025-11-01',
                'created_at' => now(),
            ],
            [
                'conference_id' => 6,
                'conference_name' => 'HUIT Cloud Computing Summit 2025',
                'conference_code' => 'HUIT-CLOUD-2025',
                'chair_id' => 12,
                'start_date' => '2025-12-10',
                'end_date' => '2025-12-12',
                'location' => 'HUIT Campus',
                'level_code' => 'TRUONG',
                'description' => 'Cloud Computing and Distributed Systems',
                'submission_deadline' => '2025-10-20',
                'review_deadline' => '2025-11-15',
                'notification_date' => '2025-11-25',
                'created_at' => now(),
            ],
            [
                'conference_id' => 7,
                'conference_name' => 'HUIT Blockchain Technology 2025',
                'conference_code' => 'HUIT-BLOCK-2025',
                'chair_id' => 13,
                'start_date' => '2025-11-05',
                'end_date' => '2025-11-07',
                'location' => 'HUIT Campus',
                'level_code' => 'KHOA',
                'description' => 'Blockchain and Distributed Ledger Technology',
                'submission_deadline' => '2025-09-25',
                'review_deadline' => '2025-10-15',
                'notification_date' => '2025-10-25',
                'created_at' => now(),
            ],
            [
                'conference_id' => 8,
                'conference_name' => 'HUIT Mobile Computing 2025',
                'conference_code' => 'HUIT-MOBILE-2025',
                'chair_id' => 14,
                'start_date' => '2025-12-15',
                'end_date' => '2025-12-17',
                'location' => 'HUIT Campus',
                'level_code' => 'KHOA',
                'description' => 'Mobile Applications and Computing',
                'submission_deadline' => '2025-10-25',
                'review_deadline' => '2025-11-20',
                'notification_date' => '2025-12-01',
                'created_at' => now(),
            ],
            
            // Past Conferences (10 - for historical data)
            [
                'conference_id' => 9,
                'conference_name' => 'HUIT Tech Symposium 2024',
                'conference_code' => 'HUIT-TECH-2024',
                'chair_id' => 15,
                'start_date' => '2024-12-01',
                'end_date' => '2024-12-03',
                'location' => 'HUIT Campus',
                'level_code' => 'TRUONG',
                'description' => 'Past technology symposium',
                'submission_deadline' => '2024-10-01',
                'review_deadline' => '2024-11-01',
                'notification_date' => '2024-11-15',
                'created_at' => '2024-09-01 00:00:00',
            ],
            [
                'conference_id' => 10,
                'conference_name' => 'HUIT Database Systems 2024',
                'conference_code' => 'HUIT-DB-2024',
                'chair_id' => 16,
                'start_date' => '2024-11-20',
                'end_date' => '2024-11-22',
                'location' => 'HUIT Campus',
                'level_code' => 'KHOA',
                'description' => 'Past database conference',
                'submission_deadline' => '2024-09-20',
                'review_deadline' => '2024-10-20',
                'notification_date' => '2024-11-05',
                'created_at' => '2024-08-01 00:00:00',
            ],
        ]);
        
        // Add 8 more past conferences
        for ($i = 11; $i <= 18; $i++) {
            DB::table('HoiThao')->insert([
                'conference_id' => $i,
                'conference_name' => "HUIT Conference {$i} - 2024",
                'conference_code' => "HUIT-CONF{$i}-2024",
                'chair_id' => 17 + ($i % 8),
                'start_date' => '2024-' . str_pad($i - 10, 2, '0', STR_PAD_LEFT) . '-01',
                'end_date' => '2024-' . str_pad($i - 10, 2, '0', STR_PAD_LEFT) . '-03',
                'location' => 'HUIT Campus',
                'level_code' => ($i % 2 == 0) ? 'KHOA' : 'TRUONG',
                'description' => "Past conference {$i}",
                'submission_deadline' => '2024-' . str_pad($i - 11, 2, '0', STR_PAD_LEFT) . '-01',
                'review_deadline' => '2024-' . str_pad($i - 11, 2, '0', STR_PAD_LEFT) . '-15',
                'notification_date' => '2024-' . str_pad($i - 11, 2, '0', STR_PAD_LEFT) . '-25',
                'created_at' => '2024-01-01 00:00:00',
            ]);
        }
        
        $this->command->info('✅ Phase 8 Seeder: Step 1-5 completed (Users, Roles, Conferences)');
        $this->command->info('📊 Stats: 248 Users (6 Admin, 18 Chair, 68 Reviewer, 156 Author)');
        $this->command->info('📊 Stats: 18 Conferences (8 Active, 10 Past)');
    }
}
