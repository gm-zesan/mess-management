#!/usr/bin/env php
<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Start a session for the user
session_start();

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

try {
    // Create a mock request to test authentication
    $request = new Request();
    $request->merge([
        'email' => 'ashraf@example.com',
        'password' => 'password'
    ]);
    
    echo "Testing authentication with credentials:\n";
    echo "Email: ashraf@example.com\n";
    echo "Password: password\n\n";
    
    // Test authentication attempt directly
    $attempt = Auth::guard('member')->attempt([
        'email' => 'ashraf@example.com',
        'password' => 'password'
    ]);
    
    echo "Authentication result: " . ($attempt ? 'SUCCESS' : 'FAILED') . "\n";
    
    if ($attempt) {
        $user = Auth::guard('member')->user();
        echo "Authenticated as: " . $user->name . " (" . $user->email . ")\n";
    } else {
        // Debug: Try to find the member
        $member = \App\Models\Member::where('email', 'ashraf@example.com')->first();
        if ($member) {
            echo "\nMember found in database:\n";
            echo "Name: " . $member->name . "\n";
            echo "Email: " . $member->email . "\n";
            echo "Has password: " . (!empty($member->password) ? 'YES' : 'NO') . "\n";
            
            // Test hash verification
            $hash_check = \Illuminate\Support\Facades\Hash::check('password', $member->password);
            echo "Password verification result: " . ($hash_check ? 'VALID' : 'INVALID') . "\n";
        } else {
            echo "\nMember NOT found in database!\n";
        }
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
    exit(1);
}
