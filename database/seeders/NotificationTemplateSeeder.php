<?php

namespace Database\Seeders;

use App\Models\NotificationTemplate;
use Illuminate\Database\Seeder;

class NotificationTemplateSeeder extends Seeder
{
    public function run()
    {
        $templates = [
            // === DEADLINE SUBMISSION REMINDERS ===
            [
                'template_code' => 'SUBMISSION_REMINDER_7D',
                'template_name' => 'Nhắc hạn nộp bài 7 ngày',
                'event_type' => 'deadline_submission',
                'days_before' => 7,
                'subject' => '[HUIT] Nhắc nhở: Còn 7 ngày để nộp bài cho hội thảo "{{title}}"',
                'body_html' => '
                    <p>Kính gửi <strong>{{user_name}}</strong>,</p>
                    <p>Đây là lời nhắc nhở về hạn chót nộp bài cho hội thảo khoa học:</p>
                    <div style="background: #fef3c7; padding: 15px; border-left: 4px solid #f59e0b; margin: 20px 0;">
                        <h3 style="margin: 0 0 10px 0; color: #92400e;">{{title}}</h3>
                        <p style="margin: 5px 0;"><strong>📅 Hạn nộp bài:</strong> {{deadline_submission}}</p>
                        <p style="margin: 5px 0;"><strong>📍 Địa điểm:</strong> {{location}}</p>
                        <p style="margin: 5px 0;"><strong>🗓️ Thời gian tổ chức:</strong> {{start_date}} - {{end_date}}</p>
                    </div>
                    <p><strong>⏰ Còn lại 7 ngày</strong> để hoàn thành và nộp bài báo của bạn.</p>
                    <p>Vui lòng truy cập hệ thống để nộp bài: <a href="{{conference_url}}" style="color: #ea580c;">{{conference_url}}</a></p>
                    <p>Trân trọng,<br>Ban tổ chức hội thảo HUIT</p>
                ',
                'body_text' => 'Kính gửi {{user_name}}, Còn 7 ngày để nộp bài cho hội thảo "{{title}}". Hạn nộp: {{deadline_submission}}. Truy cập: {{conference_url}}',
                'is_active' => true
            ],
            [
                'template_code' => 'SUBMISSION_REMINDER_3D',
                'template_name' => 'Nhắc hạn nộp bài 3 ngày',
                'event_type' => 'deadline_submission',
                'days_before' => 3,
                'subject' => '[HUIT - KHẨN] Còn 3 ngày để nộp bài cho hội thảo "{{title}}"',
                'body_html' => '
                    <p>Kính gửi <strong>{{user_name}}</strong>,</p>
                    <p><strong style="color: #dc2626;">⚠️ THÔNG BÁO KHẨN</strong></p>
                    <p>Hạn chót nộp bài cho hội thảo khoa học sắp đến:</p>
                    <div style="background: #fee2e2; padding: 15px; border-left: 4px solid #dc2626; margin: 20px 0;">
                        <h3 style="margin: 0 0 10px 0; color: #7f1d1d;">{{title}}</h3>
                        <p style="margin: 5px 0;"><strong>📅 Hạn nộp bài:</strong> <span style="color: #dc2626;">{{deadline_submission}}</span></p>
                        <p style="margin: 5px 0;"><strong>⏰ Thời gian còn lại:</strong> <strong style="color: #dc2626;">3 NGÀY</strong></p>
                    </div>
                    <p>Nếu bạn chưa nộp bài, vui lòng hoàn thành ngay để tránh bỏ lỡ cơ hội tham gia.</p>
                    <p><a href="{{conference_url}}" style="display: inline-block; background: #ea580c; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;">Nộp bài ngay</a></p>
                    <p>Trân trọng,<br>Ban tổ chức hội thảo HUIT</p>
                ',
                'body_text' => 'KHẨN: Kính gửi {{user_name}}, Còn 3 ngày để nộp bài cho "{{title}}". Hạn: {{deadline_submission}}. Nộp ngay: {{conference_url}}',
                'is_active' => true
            ],

            // === DEADLINE REVIEW REMINDERS ===
            [
                'template_code' => 'REVIEW_REMINDER_7D',
                'template_name' => 'Nhắc hạn phản biện 7 ngày',
                'event_type' => 'deadline_review',
                'days_before' => 7,
                'subject' => '[HUIT] Nhắc nhở: Còn 7 ngày để hoàn thành phản biện cho "{{title}}"',
                'body_html' => '
                    <p>Kính gửi Phản biện viên <strong>{{user_name}}</strong>,</p>
                    <p>Đây là lời nhắc nhở về hạn chót nộp kết quả phản biện:</p>
                    <div style="background: #e0e7ff; padding: 15px; border-left: 4px solid #6366f1; margin: 20px 0;">
                        <h3 style="margin: 0 0 10px 0; color: #312e81;">{{title}}</h3>
                        <p style="margin: 5px 0;"><strong>📅 Hạn phản biện:</strong> {{deadline_review}}</p>
                        <p style="margin: 5px 0;"><strong>⏰ Còn lại:</strong> 7 ngày</p>
                    </div>
                    <p>Vui lòng hoàn thành đánh giá các bài báo được phân công trước thời hạn.</p>
                    <p><a href="{{conference_url}}" style="color: #ea580c;">Truy cập hệ thống phản biện</a></p>
                    <p>Trân trọng cảm ơn sự đóng góp của bạn,<br>Ban tổ chức hội thảo HUIT</p>
                ',
                'body_text' => 'Kính gửi phản biện viên {{user_name}}, Còn 7 ngày để hoàn thành phản biện "{{title}}". Hạn: {{deadline_review}}. Hệ thống: {{conference_url}}',
                'is_active' => true
            ],
            [
                'template_code' => 'REVIEW_REMINDER_3D',
                'template_name' => 'Nhắc hạn phản biện 3 ngày',
                'event_type' => 'deadline_review',
                'days_before' => 3,
                'subject' => '[HUIT - KHẨN] Còn 3 ngày để nộp phản biện cho "{{title}}"',
                'body_html' => '
                    <p>Kính gửi Phản biện viên <strong>{{user_name}}</strong>,</p>
                    <p><strong style="color: #dc2626;">⚠️ THÔNG BÁO KHẨN</strong></p>
                    <div style="background: #fee2e2; padding: 15px; border-left: 4px solid #dc2626; margin: 20px 0;">
                        <h3 style="margin: 0 0 10px 0; color: #7f1d1d;">{{title}}</h3>
                        <p style="margin: 5px 0;"><strong>📅 Hạn phản biện:</strong> <span style="color: #dc2626;">{{deadline_review}}</span></p>
                        <p style="margin: 5px 0;"><strong>⏰ Thời gian còn lại:</strong> <strong style="color: #dc2626;">3 NGÀY</strong></p>
                    </div>
                    <p>Vui lòng ưu tiên hoàn thành công việc phản biện để đảm bảo tiến độ hội thảo.</p>
                    <p><a href="{{conference_url}}" style="display: inline-block; background: #ea580c; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;">Hoàn thành phản biện</a></p>
                    <p>Trân trọng,<br>Ban tổ chức hội thảo HUIT</p>
                ',
                'body_text' => 'KHẨN: Kính gửi {{user_name}}, Còn 3 ngày để nộp phản biện "{{title}}". Hạn: {{deadline_review}}. Hệ thống: {{conference_url}}',
                'is_active' => true
            ],

            // === CAMERA READY DEADLINE ===
            [
                'template_code' => 'CAMERA_READY_REMINDER_3D',
                'template_name' => 'Nhắc hạn bản in 3 ngày',
                'event_type' => 'deadline_camera_ready',
                'days_before' => 3,
                'subject' => '[HUIT] Nhắc nhở: Còn 3 ngày để nộp bản Camera-Ready cho "{{title}}"',
                'body_html' => '
                    <p>Kính gửi Tác giả <strong>{{user_name}}</strong>,</p>
                    <p>Bài báo của bạn đã được chấp nhận cho hội thảo <strong>{{title}}</strong>.</p>
                    <div style="background: #d1fae5; padding: 15px; border-left: 4px solid #10b981; margin: 20px 0;">
                        <h3 style="margin: 0 0 10px 0; color: #065f46;">{{title}}</h3>
                        <p style="margin: 5px 0;"><strong>📅 Hạn nộp bản Camera-Ready:</strong> {{deadline_camera_ready}}</p>
                        <p style="margin: 5px 0;"><strong>⏰ Còn lại:</strong> 3 ngày</p>
                    </div>
                    <p>Vui lòng chỉnh sửa bài báo theo ý kiến phản biện và nộp bản in cuối cùng (Camera-Ready) trước thời hạn.</p>
                    <p><a href="{{conference_url}}" style="color: #ea580c;">Nộp bản Camera-Ready</a></p>
                    <p>Trân trọng,<br>Ban tổ chức hội thảo HUIT</p>
                ',
                'body_text' => 'Kính gửi {{user_name}}, Còn 3 ngày để nộp bản Camera-Ready cho "{{title}}". Hạn: {{deadline_camera_ready}}. Hệ thống: {{conference_url}}',
                'is_active' => true
            ],

            // === CONFERENCE START REMINDER ===
            [
                'template_code' => 'CONFERENCE_START_7D',
                'template_name' => 'Nhắc khai mạc hội thảo 7 ngày',
                'event_type' => 'start_date',
                'days_before' => 7,
                'subject' => '[HUIT] Hội thảo "{{title}}" sẽ diễn ra trong 7 ngày nữa',
                'body_html' => '
                    <p>Kính gửi <strong>{{user_name}}</strong>,</p>
                    <p>Hội thảo khoa học mà bạn đăng ký tham gia sắp diễn ra:</p>
                    <div style="background: #fef3c7; padding: 15px; border-left: 4px solid #f59e0b; margin: 20px 0;">
                        <h3 style="margin: 0 0 10px 0; color: #92400e;">{{title}}</h3>
                        <p style="margin: 5px 0;"><strong>📅 Ngày khai mạc:</strong> {{start_date}}</p>
                        <p style="margin: 5px 0;"><strong>📍 Địa điểm:</strong> {{location}}</p>
                        <p style="margin: 5px 0;"><strong>⏰ Còn lại:</strong> 7 ngày</p>
                    </div>
                    <p>Vui lòng sắp xếp lịch trình và chuẩn bị tham dự hội thảo.</p>
                    <p><a href="{{conference_url}}" style="color: #ea580c;">Xem chi tiết chương trình</a></p>
                    <p>Hẹn gặp bạn tại hội thảo!<br>Ban tổ chức HUIT</p>
                ',
                'body_text' => 'Kính gửi {{user_name}}, Hội thảo "{{title}}" sẽ diễn ra sau 7 ngày. Ngày: {{start_date}}, Địa điểm: {{location}}. Chi tiết: {{conference_url}}',
                'is_active' => true
            ],

            // === CONFERENCE END REMINDER ===
            [
                'template_code' => 'CONFERENCE_END_1D',
                'template_name' => 'Nhắc bế mạc hội thảo 1 ngày',
                'event_type' => 'end_date',
                'days_before' => 1,
                'subject' => '[HUIT] Nhắc nhở: Hội thảo "{{title}}" kết thúc vào ngày mai',
                'body_html' => '
                    <p>Kính gửi <strong>{{user_name}}</strong>,</p>
                    <p>Hội thảo <strong>{{title}}</strong> sẽ kết thúc vào <strong>ngày mai ({{end_date}})</strong>.</p>
                    <div style="background: #e0e7ff; padding: 15px; border-left: 4px solid #6366f1; margin: 20px 0;">
                        <p style="margin: 5px 0;">Đây là cơ hội cuối cùng để:</p>
                        <ul style="margin: 10px 0;">
                            <li>Tham dự các phiên báo cáo cuối cùng</li>
                            <li>Kết nối với các nhà nghiên cứu</li>
                            <li>Thu thập tài liệu hội thảo</li>
                        </ul>
                    </div>
                    <p>Cảm ơn bạn đã tham gia và đóng góp cho sự thành công của hội thảo!</p>
                    <p><a href="{{conference_url}}" style="color: #ea580c;">Xem lại chương trình</a></p>
                    <p>Trân trọng,<br>Ban tổ chức hội thảo HUIT</p>
                ',
                'body_text' => 'Kính gửi {{user_name}}, Hội thảo "{{title}}" kết thúc ngày mai ({{end_date}}). Đừng bỏ lỡ phiên cuối! Chi tiết: {{conference_url}}',
                'is_active' => true
            ]
        ];

        foreach ($templates as $template) {
            NotificationTemplate::updateOrCreate(
                ['template_code' => $template['template_code']],
                $template
            );
        }

        $this->command->info('✅ Đã tạo ' . count($templates) . ' mẫu email nhắc lịch hội thảo');
    }
}

