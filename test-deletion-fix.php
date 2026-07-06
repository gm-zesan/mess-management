<?php
/**
 * Quick test to verify user deletion works correctly
 * Run with: php artisan tinker < test-deletion-fix.php
 */

use App\Models\User;
use App\Services\UserDeletionService;
use App\Enums\RoleEnum;

echo "=== Testing User Deletion Fix ===\n\n";

// Get a manager user from the database
$manager = User::whereHas('roles', function ($query) {
    $query->where('name', RoleEnum::Manager->value);
})->first();

if (!$manager) {
    echo "❌ No manager users found!\n";
    exit(1);
}

echo "Found manager: {$manager->name} (ID: {$manager->id})\n";

// Check if they can be deleted (should be false if they manage messes)
$deletionService = new UserDeletionService();
$canDelete = $deletionService->canDelete($manager);

echo "\nCan delete check result:\n";
echo "  Allowed: " . ($canDelete['allowed'] ? 'true' : 'false') . "\n";
echo "  Message: {$canDelete['message']}\n";

// Try deletion anyway (with error handling)
echo "\nAttempting deletion...\n";
$deleted = $deletionService->deleteUser($manager);

if ($deleted) {
    echo "✅ User deleted successfully!\n";
    
    // Verify deletion
    $stillExists = User::where('id', $manager->id)->exists();
    if ($stillExists) {
        echo "❌ WARNING: User still exists in database!\n";
    } else {
        echo "✅ Verified: User completely removed from database\n";
    }
} else {
    echo "❌ Deletion failed - check logs\n";
}

echo "\n=== Test Complete ===\n";
