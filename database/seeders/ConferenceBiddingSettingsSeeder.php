<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ConferenceBiddingSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Tạo cài đặt bidding mặc định cho tất cả conferences hiện có
        $conferences = \DB::table('hoithao')->get(['conference_id']);
        
        foreach ($conferences as $conference) {
            \DB::table('conference_bidding_settings')->updateOrInsert(
                ['conference_id' => $conference->conference_id],
                [
                    'enable_keyword_matching' => false, // Mặc định tắt để không ảnh hưởng đến hệ thống hiện tại
                    'keyword_similarity_threshold' => 0.5,
                    'allow_partial_keyword_match' => true,
                    'excluded_keywords' => null,
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            );
        }
        
        $this->command->info('Created bidding settings for ' . $conferences->count() . ' conferences');
    }
}
