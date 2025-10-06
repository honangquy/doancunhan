<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReviewerAssignmentsSeeder extends Seeder
{
    /**
     * Seed reviewer assignments (PhanCongPhanBien).
     * 
     * This seeder assigns reviewers to papers for testing the reviewer dashboard.
     * Each paper gets 3 reviewers assigned.
     */
    public function run()
    {
        $this->command->info('🚀 Starting Reviewer Assignments Seeder...');
        
        // ========================================
        // Step 1: Get Data
        // ========================================
        $this->command->info('📝 Step 1: Getting Reviewers and Papers...');
        
        // Get reviewer IDs
        $reviewerIds = DB::table('VaiTroNguoiDung')
            ->where('role_code', 'REVIEWER')
            ->pluck('user_id')
            ->toArray();
        
        if (count($reviewerIds) < 3) {
            $this->command->error('❌ Not enough reviewers! Need at least 3 reviewers.');
            return;
        }
        
        $this->command->info("  ✅ Found " . count($reviewerIds) . " reviewers");
        
        // Get chair IDs for assigning
        $chairIds = DB::table('VaiTroNguoiDung')
            ->where('role_code', 'CHAIR')
            ->pluck('user_id')
            ->toArray();
        
        if (empty($chairIds)) {
            $this->command->error('❌ No chairs found!');
            return;
        }
        
        $this->command->info("  ✅ Found " . count($chairIds) . " chairs");
        
        // Get papers that are UNDER_REVIEW or ACCEPTED (these need reviewers)
        $papers = DB::table('BaiBao')
            ->whereIn('status_code', ['UNDER_REVIEW', 'ACCEPTED', 'REVISION'])
            ->select('paper_id', 'conference_id', 'submitter_id', 'status_code')
            ->get();
        
        if ($papers->isEmpty()) {
            $this->command->warn('⚠️  No papers found that need review.');
            return;
        }
        
        $this->command->info("  ✅ Found " . $papers->count() . " papers needing review");
        
        // ========================================
        // Step 2: Assign Reviewers to Papers
        // ========================================
        $this->command->info('📝 Step 2: Assigning Reviewers to Papers...');
        
        $assignmentCount = 0;
        
        foreach ($papers as $paper) {
            // Get 3 random reviewers who are NOT the paper submitter
            $availableReviewers = array_diff($reviewerIds, [$paper->submitter_id]);
            
            if (count($availableReviewers) < 3) {
                $this->command->warn("⚠️  Not enough reviewers for paper {$paper->paper_id}");
                continue;
            }
            
            // Randomly select 3 reviewers
            $selectedReviewers = array_rand(array_flip($availableReviewers), min(3, count($availableReviewers)));
            if (!is_array($selectedReviewers)) {
                $selectedReviewers = [$selectedReviewers];
            }
            
            // Get a random chair for this assignment
            $chairId = $chairIds[array_rand($chairIds)];
            
            // Create assignments for each reviewer
            foreach ($selectedReviewers as $reviewerId) {
                // Determine assignment status based on paper status
                if ($paper->status_code === 'ACCEPTED') {
                    $assignmentStatus = 'COMPLETED'; // Already reviewed
                } elseif ($paper->status_code === 'UNDER_REVIEW') {
                    // Mix of statuses for under review papers
                    $assignmentStatus = ['ACCEPTED', 'COMPLETED', 'INVITED'][array_rand(['ACCEPTED', 'COMPLETED', 'INVITED'])];
                } else { // REVISION
                    $assignmentStatus = 'INVITED';
                }
                
                // Set deadline (2 weeks from now for new assignments, past for completed)
                if ($assignmentStatus === 'COMPLETED') {
                    $deadline = now()->subDays(rand(1, 30));
                } else {
                    $deadline = now()->addDays(rand(7, 21));
                }
                
                DB::table('PhanCongPhanBien')->insert([
                    'paper_id' => $paper->paper_id,
                    'reviewer_id' => $reviewerId,
                    'chair_id' => $chairId,
                    'status_code' => $assignmentStatus,
                    'token' => \Illuminate\Support\Str::uuid()->toString(),
                    'deadline' => $deadline,
                    'assigned_at' => now()->subDays(rand(5, 30)),
                ]);
                
                $assignmentCount++;
            }
        }
        
        $this->command->info("  ✅ Created {$assignmentCount} reviewer assignments");
        
        // ========================================
        // Step 3: Create Some Reviews (for completed assignments)
        // ========================================
        $this->command->info('📝 Step 3: Creating Sample Reviews...');
        
        // Get completed assignments
        $completedAssignments = DB::table('PhanCongPhanBien')
            ->where('status_code', 'COMPLETED')
            ->select('assignment_id', 'paper_id', 'reviewer_id')
            ->get();
        
        $reviewCount = 0;
        foreach ($completedAssignments as $assignment) {
            // Create a review with random score and recommendation
            $score = rand(1, 5);
            
            // Recommendation based on score (using actual codes: ACCEPT, MAJOR, MINOR, REJECT)
            if ($score >= 4) {
                $recommendation = 'ACCEPT';
            } elseif ($score == 3) {
                $recommendation = 'MINOR';  // Minor revision
            } elseif ($score == 2) {
                $recommendation = 'MAJOR';  // Major revision
            } else {
                $recommendation = 'REJECT';
            }
            
            DB::table('PhanBien')->insert([
                'assignment_id' => $assignment->assignment_id,
                'recommendation_code' => $recommendation,
                'score' => $score,
                'comment_author' => 'This is a sample review for testing purposes. Overall a solid paper with minor improvements needed.',
                'comment_chair' => 'I recommend this paper for publication with minor revisions.',
                'submitted_at' => now()->subDays(rand(1, 20)),
            ]);
            
            $reviewCount++;
        }
        
        $this->command->info("  ✅ Created {$reviewCount} sample reviews");
        
        // ========================================
        // SUMMARY
        // ========================================
        $this->command->info('');
        $this->command->line('========================================');
        $this->command->info('✅ REVIEWER ASSIGNMENTS SEEDED!');
        $this->command->line('========================================');
        $this->command->info('');
        $this->command->info('📊 Summary:');
        $this->command->info("  - Papers with assignments: {$papers->count()}");
        $this->command->info("  - Total assignments: {$assignmentCount}");
        $this->command->info("  - Reviewers used: " . count($reviewerIds));
        $this->command->info("  - Reviews completed: {$reviewCount}");
        $this->command->info('');
        
        // Get status distribution
        $statusCounts = DB::table('PhanCongPhanBien')
            ->select('status_code', DB::raw('count(*) as count'))
            ->groupBy('status_code')
            ->get();
        
        $this->command->info('📈 Assignment Status Distribution:');
        foreach ($statusCounts as $status) {
            $this->command->info("  - {$status->status_code}: {$status->count}");
        }
        $this->command->info('');
    }
}
