#!/bin/bash

# Test script for Profile Delete Functionality
# Run this to verify all components are in place

echo "================================"
echo "Profile Delete Implementation Test"
echo "================================"
echo ""

cd /Users/zesan/Desktop/My-Work/mess-management

# Test 1: Check PHP syntax
echo "1️⃣  Checking PHP syntax..."
php -l app/Http/Controllers/ProfileController.php > /dev/null 2>&1
if [ $? -eq 0 ]; then
    echo "   ✅ ProfileController syntax OK"
else
    echo "   ❌ ProfileController has syntax errors"
fi

php -l app/Services/UserDeletionService.php > /dev/null 2>&1
if [ $? -eq 0 ]; then
    echo "   ✅ UserDeletionService syntax OK"
else
    echo "   ❌ UserDeletionService has syntax errors"
fi

echo ""

# Test 2: Check routes
echo "2️⃣  Checking routes..."
php artisan route:list --name=profile 2>/dev/null | grep -q "profile.check-deletion"
if [ $? -eq 0 ]; then
    echo "   ✅ profile.check-deletion route registered"
else
    echo "   ❌ profile.check-deletion route NOT found"
fi

php artisan route:list --name=profile 2>/dev/null | grep -q "profile.transfer-manager"
if [ $? -eq 0 ]; then
    echo "   ✅ profile.transfer-manager route registered"
else
    echo "   ❌ profile.transfer-manager route NOT found"
fi

php artisan route:list --name=profile 2>/dev/null | grep -q "profile.destroy"
if [ $? -eq 0 ]; then
    echo "   ✅ profile.destroy route registered"
else
    echo "   ❌ profile.destroy route NOT found"
fi

echo ""

# Test 3: Check database columns
echo "3️⃣  Checking database columns..."
php artisan tinker --execute "
use Illuminate\Support\Facades\Schema;
echo (Schema::hasColumn('messes', 'manager_id') ? '✅ manager_id column exists' : '❌ manager_id NOT found') . PHP_EOL;
" 2>/dev/null | grep -E "✅|❌"

echo ""

# Test 4: Check files exist
echo "4️⃣  Checking implementation files..."
[ -f "app/Http/Controllers/ProfileController.php" ] && echo "   ✅ ProfileController exists" || echo "   ❌ ProfileController missing"
[ -f "app/Services/UserDeletionService.php" ] && echo "   ✅ UserDeletionService exists" || echo "   ❌ UserDeletionService missing"
[ -f "resources/views/profile/partials/delete-user-form.blade.php" ] && echo "   ✅ delete-user-form exists" || echo "   ❌ delete-user-form missing"

echo ""

# Test 5: Summary
echo "================================"
echo "✅ All checks completed!"
echo "================================"
echo ""
echo "Next steps:"
echo "1. Open http://127.0.0.1:8000/profile in browser"
echo "2. Scroll to 'Delete Account' section"
echo "3. Click 'Delete Account' button"
echo "4. If manager: Select new manager for each mess"
echo "5. Confirm password and delete"
echo ""
