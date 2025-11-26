<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\NguoiDung;
use App\Models\VaiTroNguoiDung;

class AssignChairRole extends Command
{
    protected $signature = 'assign:chair-role {email}';
    protected $description = 'Assign CHAIR role to user by email';

    public function handle()
    {
        $email = $this->argument('email');
        $user = NguoiDung::where('email', $email)->first();
        
        if (!$user) {
            $this->error("User not found with email: $email");
            return;
        }
        
        // Check if already has CHAIR role
        $hasChairRole = VaiTroNguoiDung::where('user_id', $user->user_id)
            ->where('role_code', 'CHAIR')
            ->exists();
            
        if ($hasChairRole) {
            $this->info("User already has CHAIR role");
            return;
        }
        
        // Assign CHAIR role
        VaiTroNguoiDung::create([
            'user_id' => $user->user_id,
            'role_code' => 'CHAIR',
        ]);
        
        $this->info("CHAIR role assigned successfully to: " . $user->name);
    }
}