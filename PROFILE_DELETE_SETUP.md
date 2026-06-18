# Profile Delete Implementation - Complete Setup

## Overview

The profile delete functionality has been fully implemented with support for:
1. ✅ Regular users - Direct account deletion
2. ✅ Manager users - Manager role transfer before deletion
3. ✅ Complete cleanup of user associations

## Implementation Details

### Files Modified/Created:

1. **`app/Http/Controllers/ProfileController.php`** - Enhanced with:
   - `checkDeletionEligibility()` - Check if user can delete
   - `transferManagerRole()` - Handle manager role transfers
   - `destroy()` - Enhanced deletion with validation

2. **`app/Services/UserDeletionService.php`** - New service with:
   - `canDelete()` - Validate deletion eligibility
   - `getTransferCandidates()` - Get eligible transfer recipients
   - `transferManagerRole()` - Transfer manager responsibility
   - `deleteUser()` - Complete deletion with cleanup

3. **`resources/views/profile/partials/delete-user-form.blade.php`** - Enhanced UI with:
   - Two-modal system (Transfer + Delete)
   - Dynamic dropdown selection for new managers
   - Real-time validation

4. **`routes/web.php`** - New routes:
   - `GET /profile/check-deletion` → `checkDeletionEligibility()`
   - `POST /profile/transfer-manager` → `transferManagerRole()`
   - `DELETE /profile` → `destroy()` (enhanced)

## How It Works

### For Non-Manager Users:
```
1. Click "Delete Account"
2. checkEligibility() → allowed=true
3. Shows standard delete modal
4. Confirm password
5. Account deleted ✓
```

### For Manager Users:
```
1. Click "Delete Account"
2. checkEligibility() → allowed=false, reason=manager
3. Shows transfer modal with:
   - List of messes to transfer
   - Dropdown for each mess to select new manager
4. Select new manager for each mess
5. Click "Proceed to Delete"
6. transferManagerRole() sends transfers to backend
7. Backend validates and updates managers
8. Shows delete confirmation modal
9. Confirm password
10. Account deleted ✓
```

## Frontend Flow (JavaScript)

### Step 1: Check Eligibility
```javascript
checkEligibility() {
  fetch('/profile/check-deletion')
  → Response: { allowed, reason, messes?, candidates? }
}
```

### Step 2: Show Transfer Modal (if manager)
```javascript
showManagerTransferModal(data) {
  // Display messes that need transfer
  // Display eligible candidates for each mess
  // Create dropdowns for selection
}
```

### Step 3: Send Transfers
```javascript
proceedToDelete() {
  fetch('/profile/transfer-manager', {
    method: 'POST',
    body: { transfers: [
      { mess_id: 1, new_manager_id: 5 },
      { mess_id: 2, new_manager_id: 3 }
    ]}
  })
  → If success: Show delete modal
}
```

### Step 4: Delete Account
```javascript
// Standard form submission
POST /profile (DELETE method)
password: <user-entered>
→ Validated and deleted
```

## Backend Flow (PHP)

### ProfileController::checkDeletionEligibility()
```php
1. Get current user
2. Call $service->canDelete($user)
3. If not allowed and reason='manager':
   - Get transfer candidates
   - Return JSON with messes and candidates
4. Else return allowed=true
```

### ProfileController::transferManagerRole()
```php
1. Get transfer requests
2. For each transfer:
   - Find target user
   - Find target mess
   - Call $service->transferManagerRole()
3. Return success JSON
```

### ProfileController::destroy()
```php
1. Validate password
2. Call $service->canDelete()
3. If not allowed → Redirect with error
4. Logout user
5. Delete user account
6. Invalidate session
7. Redirect to home
```

### UserDeletionService::canDelete()
```php
1. Query for messes where user is manager
2. If found → Return { allowed: false, messes: [...] }
3. Else → Return { allowed: true }
```

### UserDeletionService::transferManagerRole()
```php
1. Find target mess
2. Update mess.manager_id = new_user_id
3. Assign MANAGER role to new user
4. Remove MANAGER role from old user
5. Add MEMBER role to old user
```

### UserDeletionService::deleteUser()
```php
1. Remove from all messes
2. Remove all roles
3. Delete user record
```

## API Endpoints

### GET `/profile/check-deletion`
**Purpose**: Check if user can delete their account

**Response (allowed)**:
```json
{
  "allowed": true,
  "reason": "ok",
  "message": "User can delete their account"
}
```

**Response (manager)**:
```json
{
  "allowed": false,
  "reason": "manager",
  "message": "You cannot delete...",
  "messes": [
    { "id": 1, "name": "Mess A" }
  ],
  "candidates": [
    { "id": 2, "name": "John", "email": "john@example.com" },
    { "id": 3, "name": "Jane", "email": "jane@example.com" }
  ]
}
```

### POST `/profile/transfer-manager`
**Purpose**: Transfer manager roles to other users

**Request Body**:
```json
{
  "transfers": [
    { "mess_id": 1, "new_manager_id": 2 },
    { "mess_id": 2, "new_manager_id": 3 }
  ]
}
```

**Response**:
```json
{
  "success": true,
  "message": "Manager roles transferred successfully"
}
```

### DELETE `/profile`
**Purpose**: Delete user account

**Request Body**:
```
password=<user_password>
```

**Response**: Redirect to `/` with status message

## Testing Checklist

- [ ] Non-manager user can delete directly
  - [ ] Click "Delete Account"
  - [ ] See standard delete modal
  - [ ] Enter password
  - [ ] Account deleted
  - [ ] Redirected to home

- [ ] Manager user sees transfer modal
  - [ ] Click "Delete Account"
  - [ ] See list of messes to transfer
  - [ ] See eligible candidates
  - [ ] Cannot proceed without selecting all transfers

- [ ] Manager role transfer works
  - [ ] Select new manager for each mess
  - [ ] Click "Proceed to Delete"
  - [ ] Transfers saved
  - [ ] New manager has MANAGER role
  - [ ] Old user has MEMBER role

- [ ] Final deletion works
  - [ ] After transfers, see delete confirmation
  - [ ] Enter password
  - [ ] User deleted
  - [ ] User removed from all messes
  - [ ] Redirected to home

- [ ] Error handling
  - [ ] Invalid user selection → Error message
  - [ ] Invalid mess selection → Error message
  - [ ] Wrong password → Validation error
  - [ ] Already transferred → Cannot see modal again

## Edge Cases Handled

1. **Manager with multiple messes**
   - Shows all messes requiring transfer
   - Requires selection for each mess
   - All transfers validated before deletion

2. **No eligible candidates**
   - Service returns empty candidates array
   - Cannot proceed with deletion
   - Clear error message

3. **User in multiple messes**
   - Removed from all messes during deletion
   - All roles removed
   - All associations cleaned up

4. **Concurrent deletion attempts**
   - First deletion succeeds
   - Second attempt returns auth error (user no longer exists)

5. **Password validation**
   - Required at final deletion step
   - Uses Laravel's built-in validation
   - Prevents unauthorized deletion

## Production Readiness

✅ **Code Quality**
- No hardcoded values
- Proper error handling
- Service layer for business logic
- Type hints throughout

✅ **Security**
- Password confirmation required
- Authorization checks
- CSRF protection
- Proper validation

✅ **User Experience**
- Clear modal workflow
- Real-time validation
- Error messages
- Success feedback

✅ **Data Integrity**
- Proper role transfers
- Complete cleanup
- Transactional operations
- Foreign key constraints

## Deployment Notes

1. Ensure `messes.manager_id` column exists
2. Run migrations if needed
3. Test with manager and non-manager accounts
4. Verify role transfers work correctly
5. Monitor deletion process in logs

## Support & Troubleshooting

### "Cannot find manager transfer candidates"
- Ensure other approved members exist in the mess
- Check `mess_users` table for approved status

### "Manager role not transferring"
- Verify Spatie Permission is installed
- Check role enum values match database
- Ensure roles exist in `roles` table

### "Delete button not working"
- Check browser console for JavaScript errors
- Verify routes are registered
- Test CSRF token in meta tag
- Clear browser cache and reload

---

**Implementation Status**: ✅ Complete and Ready for Testing

**Last Updated**: 2026-06-18
