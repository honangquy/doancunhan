<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LookupTablesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // TrangThaiBaiBao
        DB::table('TrangThaiBaiBao')->insert([
            ['status_code' => 'SUBMITTED', 'status_name' => 'Đã nộp'],
            ['status_code' => 'UNDER_REVIEW', 'status_name' => 'Đang phản biện'],
            ['status_code' => 'REVISION_REQUIRED', 'status_name' => 'Yêu cầu chỉnh sửa'],
            ['status_code' => 'PENDING_CHAIR_REVIEW', 'status_name' => 'Chờ Chair duyệt lại'],
            ['status_code' => 'ACCEPTED', 'status_name' => 'Chấp nhận'],
            ['status_code' => 'REJECTED', 'status_name' => 'Từ chối'],
            ['status_code' => 'WITHDRAWN', 'status_name' => 'Đã rút'],
        ]);

        // GiaTriBidding
        DB::table('GiaTriBidding')->insert([
            ['bidding_code' => 'WANT', 'bidding_name' => 'Muốn phản biện', 'score' => 3],
            ['bidding_code' => 'CAN', 'bidding_name' => 'Có thể phản biện', 'score' => 2],
            ['bidding_code' => 'NO', 'bidding_name' => 'Không muốn', 'score' => 0],
            ['bidding_code' => 'CONFLICT', 'bidding_name' => 'Xung đột lợi ích', 'score' => -999],
        ]);

        // LoaiCOI
        DB::table('LoaiCOI')->insert([
            ['coi_code' => 'COAUTHOR', 'coi_name' => 'Đồng tác giả trong 3 năm gần đây'],
            ['coi_code' => 'ADVISOR', 'coi_name' => 'Quan hệ thầy trò'],
            ['coi_code' => 'SAME_ORG', 'coi_name' => 'Cùng tổ chức'],
            ['coi_code' => 'FAMILY', 'coi_name' => 'Quan hệ họ hàng'],
            ['coi_code' => 'COLLABORATION', 'coi_name' => 'Đang hợp tác nghiên cứu'],
            ['coi_code' => 'OTHER', 'coi_name' => 'Lý do khác'],
        ]);

        // LoaiVaiTro
        DB::table('LoaiVaiTro')->insert([
            ['role_code' => 'ADMIN', 'role_name' => 'Quản trị viên'],
            ['role_code' => 'AUTHOR', 'role_name' => 'Tác giả'],
            ['role_code' => 'REVIEWER', 'role_name' => 'Phản biện viên'],
            ['role_code' => 'CHAIR', 'role_name' => 'Chủ tịch hội thảo'],
            ['role_code' => 'PC', 'role_name' => 'Program Committee'],
        ]);

        // CapHoiThao
        DB::table('CapHoiThao')->insert([
            ['level_code' => 'KHOA', 'level_name' => 'Cấp Khoa'],
            ['level_code' => 'TRUONG', 'level_name' => 'Cấp Trường'],
        ]);

        // TrangThaiPhanCong
        DB::table('TrangThaiPhanCong')->insert([
            ['status_code' => 'INVITED'],
            ['status_code' => 'ACCEPTED'],
            ['status_code' => 'DECLINED'],
            ['status_code' => 'CANCELLED'],
            ['status_code' => 'COMPLETED'],
        ]);

        // LoaiKhuyenNghi
        DB::table('LoaiKhuyenNghi')->insert([
            ['recommendation_code' => 'STRONG_ACCEPT', 'recommendation_name' => 'Chấp nhận mạnh'],
            ['recommendation_code' => 'ACCEPT', 'recommendation_name' => 'Chấp nhận'],
            ['recommendation_code' => 'WEAK_ACCEPT', 'recommendation_name' => 'Chấp nhận yếu'],
            ['recommendation_code' => 'BORDERLINE', 'recommendation_name' => 'Biên giới'],
            ['recommendation_code' => 'WEAK_REJECT', 'recommendation_name' => 'Từ chối yếu'],
            ['recommendation_code' => 'REJECT', 'recommendation_name' => 'Từ chối'],
            ['recommendation_code' => 'STRONG_REJECT', 'recommendation_name' => 'Từ chối mạnh'],
        ]);
    }
}
