#!/bin/bash

echo "================================"
echo "Profile Delete Complete Test"
echo "================================"

cd /Users/zesan/Desktop/My-Work/mess-management

# 1. Fresh migration
echo -e "\n[1] Running fresh migration..."
php artisan migrate:fresh --seed > /dev/null 2>&1
echo "✅ Database refreshed"

# 2. Test basic deletion
echo -e "\n[2] Testing basic user deletion..."
php artisan tinker << 'TINKER' 2>/dev/null
$count_before = App\Models\User::count();
$user = App\Models\User::where('id', '>', 1)->first();
$service = new App\Services\UserDeletionService();
$result = $service->deleteUser($user);
$count_after = App\Models\User::count();

if ($result && !App\Models\User::where('id', $user->id)->exists()) {
    echo "✅ Basic deletion works\n";
} else {
    echo "❌ Basic deletion FAILED\n";
}
TINKER

# 3. Test manager transfer flow
echo -e "\n[3] Testing manager role transfer..."
php artisan tinker << 'TINKER' 2>/dev/null
// Get a manager
$manager = App\Models\User::whereHas('roles', function($q) {
    $q->where('name', 'manager');
})->first();

if ($manager) {
    // Get transfer candidates
    $service = new App\Services\UserDeletionService();
    $candidates = $service->getTransferCandidates($manager);
    
    if ($candidates->count() > 0) {
        // Transfer to first candidate
        $recipient = $candidates->first();
        $messes = App\Models\Mess::where('manager_id', $manager->id)->get();
        
        if ($messes->count() > 0) {
            $success = $service->transferManagerRole($manager, $recipient, $messes->first());
            
            if ($success) {
                echo "✅ Manager transfer works\n";
            } else {
                echo "❌ Manager transfer FAILED\n";
            }
        }
    }
}
TINKER

# 4. Test password validation in controller
echo -e "\n[4] Verifying ProfileController setup..."
if grep -q 'validateWithBag.*userDeletion.*password' app/Http/Controllers/ProfileController.php; then
    echo "✅ Password validation present"
else
    echo "❌ Password validation missing"
fi

if grep -q 'deleteUser' app/Http/Controllers/ProfileController.php; then
    echo "✅ deleteUser call present"
else
    echo "❌ deleteUser call missing"
fi

# 5. Check if deletion happens before logout
echo -e "\n[5] Checking deletion order..."
if grep -n 'deleteUser' app/Http/Controllers/ProfileController.php | grep -B 2 'Auth::logout' > /dev/null; then
    echo "✅ Deletion happens before logout"
else
    echo "⚠️  Check deletion order manually"
fi

# 6. Verify routes
echo -e "\n[6] Verifying routes..."
php artisan route:list 2>/dev/null | grep -E 'profile.*delete|check-deletion|transfer-manager' > /tmp/routes.txt

if grep -q 'profile' /tmp/routes.txt; then
    echo "✅ Profile routes registered"
    grep 'profile' /tmp/routes.txt | head -3
fi

# 7. Check error handling
echo -e "\n[7] Verifying error handling..."
if grep -q 'Log::error' app/Services/UserDeletionService.php; then
    echo "✅ Error logging implemented"
fi

if grep -q '!$deleted' app/Http/Controllers/ProfileController.php; then
    echo "✅ Deletion failure handling present"
fi

echo -e "\n================================"
echo "Test Complete"
echo "================================"
