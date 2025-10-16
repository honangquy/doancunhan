<?php

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/bootstrap/app.php';

use App\Models\HoiThao;

try {
    // Tạo hội thảo mẫu
    $conference = new HoiThao();
    $conference->title = 'Hội thảo Khoa học CNTT HUIT 2025';
    $conference->description = 'Hội thảo khoa học thường niên về Công nghệ thông tin'; 
    $conference->detailed_description = 'Hội thảo tạo ra diễn đàn học thuật để các nhà nghiên cứu, giảng viên, sinh viên trao đổi nghiên cứu mới nhất trong lĩnh vực CNTT';
    $conference->location = 'Trường Đại học Công nghiệp TP.HCM';
    $conference->contact_email = 'conference@huit.edu.vn';
    $conference->contact_phone = '028 3894 0390';
    $conference->chair_name = 'PGS.TS. Nguyễn Văn A';
    $conference->chair_email = 'nguyenvana@huit.edu.vn';
    $conference->keywords = 'AI, Machine Learning, IoT, Cybersecurity, Cloud Computing';
    $conference->year = 2025;
    $conference->start_date = '2025-12-15';
    $conference->end_date = '2025-12-17';
    $conference->deadline_submission = '2025-11-15';
    $conference->deadline_review = '2025-11-30';
    $conference->status = 'open';
    $conference->faculty_id = 1;
    $conference->level_code = 'NATIONAL';
    
    $conference->save();
    
    echo "Tạo hội thảo thành công với ID: " . $conference->conference_id . PHP_EOL;
    
} catch (Exception $e) {
    echo "Lỗi: " . $e->getMessage() . PHP_EOL;
}