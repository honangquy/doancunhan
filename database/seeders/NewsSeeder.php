<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class NewsSeeder extends Seeder
{
    public function run()
    {
        // Get first user from nguoidung table
        $userId = DB::table('nguoidung')->value('user_id') ?? 1;
        $conferenceId = DB::table('hoithao')->first()->conference_id ?? null;

        $newsData = [
            [
                'title' => 'Thông báo khai mạc Hội thảo Khoa học Quốc tế năm 2025',
                'slug' => 'thong-bao-khai-mac-hoi-thao-khoa-hoc-quoc-te-2025',
                'category' => 'ANNOUNCEMENT',
                'conference_id' => $conferenceId,
                'summary' => 'Hội thảo khoa học quốc tế sẽ được tổ chức vào ngày 15/02/2025 tại Hội trường A, Trường Đại học Công Thương TP. Hồ Chí Minhệ Thông tin.',
                'content' => '<h2>Thông báo chính thức</h2><p>Ban tổ chức trân trọng thông báo lịch khai mạc Hội thảo Khoa học Quốc tế năm 2025.</p><h3>Thời gian</h3><ul><li>Ngày: 15/02/2025</li><li>Giờ: 08:00 AM</li></ul><h3>Địa điểm</h3><p>Hội trường A - Trường Đại học Công Thương TP. Hồ Chí Minhệ Thông tin</p>',
                'cover_image' => null,
                'is_featured' => true,
                'status' => 'PUBLISHED',
                'published_at' => now()->subDays(2),
                'created_by' => $userId,
                'created_at' => now()->subDays(5),
                'updated_at' => now()->subDays(2),
            ],
            [
                'title' => 'Hướng dẫn nộp bài viết cho Hội thảo',
                'slug' => 'huong-dan-nop-bai-viet-cho-hoi-thao',
                'category' => 'GUIDE',
                'conference_id' => $conferenceId,
                'summary' => 'Tài liệu hướng dẫn chi tiết cách thức nộp bài viết, format chuẩn và thời hạn nộp bài.',
                'content' => '<h2>Quy trình nộp bài</h2><ol><li>Đăng ký tài khoản trên hệ thống</li><li>Chọn hội thảo muốn tham gia</li><li>Tải lên bài viết theo định dạng IEEE</li><li>Chờ phản biện và thông báo kết quả</li></ol><h3>Yêu cầu định dạng</h3><ul><li>File: PDF hoặc DOCX</li><li>Kích thước: Tối đa 10MB</li><li>Font chữ: Times New Roman, cỡ 12</li></ul>',
                'cover_image' => null,
                'is_featured' => false,
                'status' => 'PUBLISHED',
                'published_at' => now()->subDays(10),
                'created_by' => $userId,
                'created_at' => now()->subDays(15),
                'updated_at' => now()->subDays(10),
            ],
            [
                'title' => 'Danh sách diễn giả chính tại Hội thảo 2025',
                'slug' => 'danh-sach-dien-gia-chinh-tai-hoi-thao-2025',
                'category' => 'EVENT',
                'conference_id' => $conferenceId,
                'summary' => 'Giới thiệu về các diễn giả chính sẽ tham gia chia sẻ tại Hội thảo năm nay.',
                'content' => '<h2>Các diễn giả nổi bật</h2><h3>1. TS. Nguyễn Văn A</h3><p>Chuyên gia về Trí tuệ nhân tạo - Đại học Stanford</p><h3>2. PGS.TS. Trần Thị B</h3><p>Nghiên cứu viên cao cấp về Blockchain - MIT</p><h3>3. Prof. John Smith</h3><p>Giảng viên về Machine Learning - Oxford University</p>',
                'cover_image' => null,
                'is_featured' => true,
                'status' => 'PUBLISHED',
                'published_at' => now()->subDay(),
                'created_by' => $userId,
                'created_at' => now()->subDays(3),
                'updated_at' => now()->subDay(),
            ],
            [
                'title' => 'Cập nhật lịch trình chi tiết các phiên thảo luận',
                'slug' => 'cap-nhat-lich-trinh-chi-tiet-cac-phien-thao-luan',
                'category' => 'NEWS',
                'conference_id' => null,
                'summary' => 'Lịch trình chi tiết các phiên thảo luận đã được cập nhật, vui lòng kiểm tra thông tin mới nhất.',
                'content' => '<h2>Lịch trình mới</h2><p>Ban tổ chức đã cập nhật lịch trình các phiên thảo luận để phù hợp hơn với thời gian của diễn giả và người tham dự.</p><h3>Sáng 15/02</h3><ul><li>08:00 - 09:30: Keynote Speech</li><li>09:30 - 10:00: Coffee Break</li><li>10:00 - 12:00: Session 1 - AI & ML</li></ul><h3>Chiều 15/02</h3><ul><li>13:30 - 15:30: Session 2 - Blockchain</li><li>15:30 - 16:00: Coffee Break</li><li>16:00 - 17:30: Session 3 - IoT</li></ul>',
                'cover_image' => null,
                'is_featured' => false,
                'status' => 'PUBLISHED',
                'published_at' => now(),
                'created_by' => $userId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => '[Bản nháp] Bài viết sắp xuất bản về AI trong Y tế',
                'slug' => 'ban-nhap-bai-viet-sap-xuat-ban-ve-ai-trong-y-te',
                'category' => 'NEWS',
                'conference_id' => null,
                'summary' => 'Đây là bản nháp đang soạn thảo về ứng dụng AI trong lĩnh vực y tế.',
                'content' => '<p>Nội dung đang được hoàn thiện...</p>',
                'cover_image' => null,
                'is_featured' => false,
                'status' => 'DRAFT',
                'published_at' => null,
                'created_by' => $userId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($newsData as $news) {
            DB::table('news')->insert($news);
        }

        $this->command->info('✅ Đã tạo ' . count($newsData) . ' tin tức mẫu!');
    }
}
