<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class TestAuthController extends Controller
{
    public function test()
    {
        // Test member authentication
        $result = Auth::guard('member')->attempt([
            'email' => 'ashraf@example.com',
            'password' => 'password'
        ]);
        
        return response()->json([
            'authentication_result' => $result ? 'SUCCESS' : 'FAILED',
            'message' => $result ? 'Authentication successful' : 'Authentication failed',
            'user' => $result ? Auth::guard('member')->user() : null,
        ]);
    }

    public function listMembers()
    {
        $members = \App\Models\Member::all(['id', 'name', 'email'])->toArray();
        return response()->json(['members' => $members]);
    }
}
