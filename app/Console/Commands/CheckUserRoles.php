<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\NguoiDung;

class CheckUserRoles extends Command
{
    protected $signature = 'check:user-roles {email}';
    protected $description = 'Check user roles by email';

    public function handle()
    {
        $email = $this->argument('email');
        $user = NguoiDung::where('email', $email)->with(['vaiTros', 'vaiTros.loaiVaiTro'])->first();
        
        if (!$user) {
            $this->error("User not found with email: $email");
            return;
        }
        
        $this->info("User: " . $user->name);
        $this->info("Email: " . $user->email);
        $this->info("User ID: " . $user->user_id);
        
        if ($user->vaiTros->count() > 0) {
            $this->info("Roles:");
            foreach ($user->vaiTros as $vaiTro) {
                $roleName = $vaiTro->loaiVaiTro ? $vaiTro->loaiVaiTro->role_name : $vaiTro->role_code;
                $this->info("  - " . $vaiTro->role_code . " (" . $roleName . ")");
            }
        } else {
            $this->warn("No roles assigned to this user");
        }
    }
}