#!/usr/bin/env php
<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Member;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

try {
    // Get the member
    $member = Member::where('email', 'ashraf@example.com')->first();
    
    if (!$member) {
        echo "Member not found!\n";
        exit(1);
    }
    
    echo "Member found: " . $member->name . "\n";
    echo "Email: " . $member->email . "\n";
    echo "Password hash: " . $member->password . "\n";
    
    // Test password verification
    $password = 'password';
    $isValid = Hash::check($password, $member->password);
    echo "Password verification (Hash::check): " . ($isValid ? 'YES' : 'NO') . "\n";
    
    // Test authentication attempt
    $attempt = Auth::guard('member')->attempt([
        'email' => 'ashraf@example.com',
        'password' => 'password'
    ]);
    
    echo "Authentication attempt: " . ($attempt ? 'SUCCESS' : 'FAILED') . "\n";
    
    if ($attempt) {
        $user = Auth::guard('member')->user();
        echo "Authenticated as: " . $user->name . "\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
    exit(1);
}
