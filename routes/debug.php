Route::get('/debug/test-join/{token}', function($token) {
    // Simulate a join request with invitation
    $invitation = DB::table('reviewer_invitations')
        ->where('token', $token)
        ->where('status', 'PENDING')
        ->first();
    
    if (!$invitation) {
        return 'Invitation not found';
    }
    
    $user = DB::table('nguoidung')->where('email', $invitation->email)->first();
    if (!$user) {
        return 'User not found with email: ' . $invitation->email;
    }
    
    // Check existing roles
    $existingRoles = DB::table('vaitronguoidung')
        ->where('user_id', $user->user_id)
        ->get();
    
    $output = "User: {$user->full_name} (ID: {$user->user_id})\n";
    $output .= "Email: {$user->email}\n";
    $output .= "Existing roles: " . count($existingRoles) . "\n";
    foreach($existingRoles as $role) {
        $output .= "- {$role->role_code} for conference {$role->conference_id}\n";
    }
    
    // Test role assignment
    $existingReviewerRole = DB::table('vaitronguoidung')
        ->where('user_id', $user->user_id)
        ->where('conference_id', $invitation->conference_id)
        ->where('role_code', 'REVIEWER')
        ->first();
    
    if (!$existingReviewerRole) {
        DB::table('vaitronguoidung')->insert([
            'user_id' => $user->user_id,
            'conference_id' => $invitation->conference_id,
            'role_code' => 'REVIEWER'
        ]);
        $output .= "\n✅ REVIEWER role assigned!";
    } else {
        $output .= "\n⚠️ REVIEWER role already exists";
    }
    
    return nl2br($output);
});