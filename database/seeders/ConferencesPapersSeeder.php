<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ConferencesPapersSeeder extends Seeder
{
    /**
     * Seed conferences and papers for existing users
     */
    public function run(): void
    {
        $this->command->info('🚀 Starting Conferences & Papers Seeder...');
        
        // ========================================
        // 1. CONFERENCES (3 active conferences)
        // ========================================
        $this->command->info('📝 Step 1: Seeding Conferences...');
        
        $conferences = [
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
                'status' => 'IN_PROGRESS',
            ],
        ];
        
        $conferenceIds = [];
        foreach ($conferences as $conf) {
            $confId = DB::table('HoiThao')->insertGetId($conf);
            $conferenceIds[] = $confId;
            $this->command->info("  ✅ Created: {$conf['title']} (ID: {$confId})");
        }
        
        // ========================================
        // 2. PAPERS (45 papers total)
        // ========================================
        $this->command->info('📝 Step 2: Seeding Papers...');
        
        // Get author user IDs (users with AUTHOR role)
        $authorIds = DB::table('VaiTroNguoiDung')
            ->where('role_code', 'AUTHOR')
            ->pluck('user_id')
            ->toArray();
        
        if (empty($authorIds)) {
            $this->command->warn('⚠️  No authors found! Skipping papers...');
            return;
        }
        
        $papers = [
            // Conference 1: HUIT-ICI-2025 (28 papers)
            ['title' => 'Deep Learning Optimization Techniques', 'conference_id' => $conferenceIds[0], 'status' => 'ACCEPTED'],
            ['title' => 'Blockchain in Financial Systems', 'conference_id' => $conferenceIds[0], 'status' => 'UNDER_REVIEW'],
            ['title' => 'IoT Security Framework', 'conference_id' => $conferenceIds[0], 'status' => 'SUBMITTED'],
            ['title' => 'Machine Learning in Healthcare', 'conference_id' => $conferenceIds[0], 'status' => 'ACCEPTED'],
            ['title' => 'Cloud Computing Architecture', 'conference_id' => $conferenceIds[0], 'status' => 'UNDER_REVIEW'],
            ['title' => 'Big Data Analytics Platform', 'conference_id' => $conferenceIds[0], 'status' => 'ACCEPTED'],
            ['title' => 'Artificial Intelligence Ethics', 'conference_id' => $conferenceIds[0], 'status' => 'REVISION'],
            ['title' => 'Neural Network Architectures', 'conference_id' => $conferenceIds[0], 'status' => 'ACCEPTED'],
            ['title' => 'Quantum Computing Applications', 'conference_id' => $conferenceIds[0], 'status' => 'UNDER_REVIEW'],
            ['title' => 'Edge Computing for IoT', 'conference_id' => $conferenceIds[0], 'status' => 'SUBMITTED'],
            ['title' => 'Natural Language Processing', 'conference_id' => $conferenceIds[0], 'status' => 'ACCEPTED'],
            ['title' => 'Computer Vision Systems', 'conference_id' => $conferenceIds[0], 'status' => 'UNDER_REVIEW'],
            ['title' => 'Cybersecurity Best Practices', 'conference_id' => $conferenceIds[0], 'status' => 'ACCEPTED'],
            ['title' => 'Distributed Systems Design', 'conference_id' => $conferenceIds[0], 'status' => 'REVISION'],
            ['title' => 'Mobile Application Development', 'conference_id' => $conferenceIds[0], 'status' => 'ACCEPTED'],
            ['title' => 'Web3 and Decentralization', 'conference_id' => $conferenceIds[0], 'status' => 'UNDER_REVIEW'],
            ['title' => 'DevOps Implementation Strategies', 'conference_id' => $conferenceIds[0], 'status' => 'ACCEPTED'],
            ['title' => 'Microservices Architecture', 'conference_id' => $conferenceIds[0], 'status' => 'SUBMITTED'],
            ['title' => 'Data Privacy Regulations', 'conference_id' => $conferenceIds[0], 'status' => 'ACCEPTED'],
            ['title' => 'Software Testing Automation', 'conference_id' => $conferenceIds[0], 'status' => 'UNDER_REVIEW'],
            ['title' => 'Agile Project Management', 'conference_id' => $conferenceIds[0], 'status' => 'ACCEPTED'],
            ['title' => '5G Network Technology', 'conference_id' => $conferenceIds[0], 'status' => 'REVISION'],
            ['title' => 'Augmented Reality Applications', 'conference_id' => $conferenceIds[0], 'status' => 'ACCEPTED'],
            ['title' => 'Smart City Infrastructure', 'conference_id' => $conferenceIds[0], 'status' => 'UNDER_REVIEW'],
            ['title' => 'Renewable Energy Systems', 'conference_id' => $conferenceIds[0], 'status' => 'REJECTED'],
            ['title' => 'Digital Transformation Strategy', 'conference_id' => $conferenceIds[0], 'status' => 'ACCEPTED'],
            ['title' => 'Cloud Native Applications', 'conference_id' => $conferenceIds[0], 'status' => 'ACCEPTED'],
            ['title' => 'AI-Powered Analytics', 'conference_id' => $conferenceIds[0], 'status' => 'ACCEPTED'],
            
            // Conference 2: HUIT-SEC-2025 (12 papers)
            ['title' => 'Network Intrusion Detection', 'conference_id' => $conferenceIds[1], 'status' => 'ACCEPTED'],
            ['title' => 'Cryptography Algorithms', 'conference_id' => $conferenceIds[1], 'status' => 'UNDER_REVIEW'],
            ['title' => 'Zero Trust Architecture', 'conference_id' => $conferenceIds[1], 'status' => 'ACCEPTED'],
            ['title' => 'Malware Analysis Techniques', 'conference_id' => $conferenceIds[1], 'status' => 'REVISION'],
            ['title' => 'Security Information Management', 'conference_id' => $conferenceIds[1], 'status' => 'ACCEPTED'],
            ['title' => 'Penetration Testing Methods', 'conference_id' => $conferenceIds[1], 'status' => 'UNDER_REVIEW'],
            ['title' => 'Identity Access Management', 'conference_id' => $conferenceIds[1], 'status' => 'ACCEPTED'],
            ['title' => 'Cloud Security Framework', 'conference_id' => $conferenceIds[1], 'status' => 'SUBMITTED'],
            ['title' => 'Threat Intelligence Platform', 'conference_id' => $conferenceIds[1], 'status' => 'ACCEPTED'],
            ['title' => 'Blockchain Security', 'conference_id' => $conferenceIds[1], 'status' => 'UNDER_REVIEW'],
            ['title' => 'IoT Device Security', 'conference_id' => $conferenceIds[1], 'status' => 'ACCEPTED'],
            ['title' => 'Incident Response Planning', 'conference_id' => $conferenceIds[1], 'status' => 'ACCEPTED'],
            
            // Conference 3: HUIT-AI-2025 (5 papers)
            ['title' => 'Reinforcement Learning', 'conference_id' => $conferenceIds[2], 'status' => 'UNDER_REVIEW'],
            ['title' => 'Generative AI Models', 'conference_id' => $conferenceIds[2], 'status' => 'UNDER_REVIEW'],
            ['title' => 'Explainable AI Systems', 'conference_id' => $conferenceIds[2], 'status' => 'SUBMITTED'],
            ['title' => 'Data Science Pipeline', 'conference_id' => $conferenceIds[2], 'status' => 'UNDER_REVIEW'],
            ['title' => 'Predictive Analytics', 'conference_id' => $conferenceIds[2], 'status' => 'SUBMITTED'],
        ];
        
        $paperCount = 0;
        foreach ($papers as $paper) {
            // Assign random author
            $authorId = $authorIds[array_rand($authorIds)];
            
            DB::table('BaiBao')->insert([
                'conference_id' => $paper['conference_id'],
                'submitter_id' => $authorId,
                'title' => $paper['title'],
                'status_code' => $paper['status'],
                'created_at' => now()->subDays(rand(5, 60)),
            ]);
            
            $paperCount++;
        }
        
        $this->command->info("  ✅ Created {$paperCount} papers");
        
        // ========================================
        // SUMMARY
        // ========================================
        $this->command->info('');
        $this->command->info('========================================');
        $this->command->info('✅ CONFERENCES & PAPERS SEEDED!');
        $this->command->info('========================================');
        $this->command->info('');
        $this->command->info('📊 Summary:');
        $this->command->info('  - Conferences: 3');
        $this->command->info('    * HUIT-ICI-2025: 28 papers');
        $this->command->info('    * HUIT-SEC-2025: 12 papers');
        $this->command->info('    * HUIT-AI-2025: 5 papers');
        $this->command->info('  - Total Papers: 45');
        $this->command->info('');
        $this->command->info('📈 Paper Status Distribution:');
        $accepted = collect($papers)->where('status', 'ACCEPTED')->count();
        $underReview = collect($papers)->where('status', 'UNDER_REVIEW')->count();
        $submitted = collect($papers)->where('status', 'SUBMITTED')->count();
        $revision = collect($papers)->where('status', 'REVISION')->count();
        $rejected = collect($papers)->where('status', 'REJECTED')->count();
        
        $this->command->info("  - Accepted: {$accepted}");
        $this->command->info("  - Under Review: {$underReview}");
        $this->command->info("  - Submitted: {$submitted}");
        $this->command->info("  - Revision: {$revision}");
        $this->command->info("  - Rejected: {$rejected}");
        $this->command->info('');
    }
}
