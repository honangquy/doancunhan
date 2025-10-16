<?php

namespace App\Console\Commands;

use App\Models\HoiThao;
use Illuminate\Console\Command;

class CreateSampleConference extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'conference:create-sample';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a sample conference for testing';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
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
            $conference->faculty_id = 1; // CNTT
            $conference->level_code = 'KHOA'; // Cấp khoa
            
            $conference->save();
            
            $this->info("Tạo hội thảo thành công với ID: {$conference->conference_id}");
            
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error("Lỗi: " . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
