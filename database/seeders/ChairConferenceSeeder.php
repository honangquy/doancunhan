<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ChairConferenceSeeder extends Seeder
{
    /**
     * Run the database seeds - Assign chairs to conferences
     *
     * @return void
     */
    public function run()
    {
        // Get all conferences
        $conferences = DB::table('HoiThao')->orderBy('conference_id')->get();
        
        // Get chair users (user_id 7-16 are chairs based on Phase8Seeder)
        $chairUsers = range(7, 16);
        
        echo "Assigning chairs to conferences...\n";
        
        foreach ($conferences as $index => $conference) {
            // Rotate through available chairs
            $chairId = $chairUsers[$index % count($chairUsers)];
            
            // Check if this user has a CHAIR role entry
            $existingRole = DB::table('VaiTroNguoiDung')
                ->where('user_id', $chairId)
                ->where('role_code', 'CHAIR')
                ->first();
            
            if ($existingRole) {
                // Update existing role to add conference_id
                DB::table('VaiTroNguoiDung')
                    ->where('user_role_id', $existingRole->user_role_id)
                    ->update([
                        'conference_id' => $conference->conference_id
                    ]);
                
                echo "  ✅ Updated: User {$chairId} -> Conference {$conference->conference_id} ({$conference->title})\n";
            } else {
                // Create new CHAIR role assignment
                DB::table('VaiTroNguoiDung')->insert([
                    'user_id' => $chairId,
                    'role_code' => 'CHAIR',
                    'conference_id' => $conference->conference_id
                ]);
                
                echo "  ✅ Created: User {$chairId} -> Conference {$conference->conference_id} ({$conference->title})\n";
            }
        }
        
        echo "\n✅ Chair-Conference assignments complete!\n";
        
        // Show summary
        $summary = DB::table('VaiTroNguoiDung as vt')
            ->join('NguoiDung as nd', 'vt.user_id', '=', 'nd.user_id')
            ->join('HoiThao as ht', 'vt.conference_id', '=', 'ht.conference_id')
            ->where('vt.role_code', 'CHAIR')
            ->whereNotNull('vt.conference_id')
            ->select('nd.full_name', 'nd.email', 'ht.title')
            ->get();
        
        echo "\n📋 Current Chair Assignments:\n";
        foreach ($summary as $item) {
            echo "  - {$item->full_name} ({$item->email}) → {$item->title}\n";
        }
    }
}
