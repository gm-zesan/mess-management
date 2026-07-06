# Account Deletion with Financial Record Preservation

## Problem Solved

**Issue**: If a user deletes their account while having expenses, deposits, or meals, those financial records were also deleted, breaking mess accounting calculations.

**Solution**: Implement soft delete for user accounts while preserving all financial records.

---

## Architecture

### Soft Delete Strategy

Instead of hard-deleting users and their data, we now use **Laravel's SoftDeletes**:

```
User Account Deleted → User marked as deleted (soft delete)
                    → Financial records remain intact
                    → User cannot login
                    → Mess calculations preserved
```

### Data Flow

```
User clicks "Delete Account"
            ↓
Check if managing messes → Transfer role if needed
            ↓
Check if has financial records → Show preservation notice
            ↓
Validate password
            ↓
Remove from messes & roles
            ↓
Soft delete user (mark as deleted, not remove)
            ↓
Financial records remain with deleted user reference
            ↓
Logout & redirect
```

---

## Implementation

### 1. Database Migration

**File**: `database/migrations/2026_07_06_000000_add_soft_deletes_to_users.php`

Adds `deleted_at` timestamp column to users table:

```php
Schema::table('users', function (Blueprint $table) {
    $table->softDeletes();
});
```

### 2. User Model Update

**File**: `app/Models/User.php`

Added SoftDeletes trait:

```php
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles, SoftDeletes;
}
```

### 3. Service Layer Changes

**File**: `app/Services/UserDeletionService.php`

#### Method: `canDelete()`
Now checks for financial records and informs user if account will be deactivated rather than deleted:

```php
public function canDelete(User $user): array
{
    // Check if managing messes
    // Check if has financial records
    // Return appropriate message
}
```

Returns:
- `reason: 'ok'` - Account can be fully deleted (no financial records)
- `reason: 'has_financial_records'` - Account will be soft-deleted (records preserved)
- `reason: 'manager'` - Cannot delete (managing messes)

#### Method: `prepareForDeletion()`
Now PRESERVES financial records:

```php
public function prepareForDeletion(User $user): void
{
    // REMOVES: messes, mess_users, roles
    // PRESERVES: deposits, expenses, meals
    // This maintains foreign key references for accounting
}
```

#### Method: `deleteUser()`
Now uses soft delete:

```php
public function deleteUser(User $user): bool
{
    $this->prepareForDeletion($user);
    $user->delete(); // Soft delete (not hard delete)
    return true;
}
```

#### Method: `hardDeleteUser()` (NEW)
For admin use only - permanently removes user IF no financial records:

```php
public function hardDeleteUser(User $user): bool
{
    // Checks for financial records
    // Prevents deletion if records exist
    // Hard deletes if safe
}
```

### 4. Controller Updates

**File**: `app/Http/Controllers/ProfileController.php`

Updated destroy() method to return appropriate success message:

```php
$message = $canDelete['reason'] === 'has_financial_records' 
    ? 'profile-deactivated'  // Soft deleted
    : 'profile-deleted';      // Hard deleted
```

---

## User Experience

### Scenario 1: User with NO Financial Records
```
✓ User can fully delete account
✓ Account and all data removed
✓ Redirects with "Account Deleted" message
```

### Scenario 2: User with Financial Records
```
✓ User can "delete" account
✓ Account marked as deleted (deactivated)
✓ All expenses, deposits, meals preserved
✓ Mess accounting remains correct
✓ Redirects with "Account Deactivated" message
✓ User cannot login
```

### Scenario 3: Manager User
```
✗ Cannot delete without transferring role
✓ User selects recipient
✓ Manager role transferred
✓ Then follows Scenario 1 or 2
```

---

## Deleted User Visibility

### What Happens to Deleted Users

| Item | Status | Details |
|------|--------|---------|
| Can login | ❌ NO | SoftDeletes scope prevents queries |
| Appear in user lists | ❌ NO | Excluded by default scope |
| Financial records | ✅ YES | Preserved for accounting |
| Expenses attributed to | ✅ YES | Still show deleted user name/ID |
| Deposits attributed to | ✅ YES | Still show deleted user name/ID |
| Mess calculations | ✅ YES | Include their transactions |

### Querying Deleted Users

```php
// Exclude deleted users (default)
User::all(); // Only active users

// Include deleted users
User::withTrashed()->all();

// Only deleted users
User::onlyTrashed()->all();

// Restore deleted user
User::onlyTrashed()->find($id)->restore();
```

---

## Financial Record Integrity

### Before Deletion
```
User: John Doe
  - Expenses: 5 records (₹1500)
  - Deposits: 2 records (₹2000)
  - Meals: 3 records (₹600)
```

### After Account Deletion (Soft Delete)
```
User: John Doe (DELETED)
  - Expenses: 5 records (₹1500) ← PRESERVED
  - Deposits: 2 records (₹2000) ← PRESERVED
  - Meals: 3 records (₹600) ← PRESERVED

Mess Balance Impact: ← SAME (no changes)
  - Total expenses: ₹1500 ✓
  - Total deposits: ₹2000 ✓
  - Settlement: ₹500 owed ✓
```

---

## Security Implications

### Authentication Prevention
- SoftDeletes scope automatically excludes deleted users from queries
- User cannot authenticate because they won't be found by email
- Guard against manually forcing soft-deleted user to authenticate

### Data Visibility
- Deleted users cannot access their dashboard
- Deleted users cannot modify any data
- But their transaction history remains in the system

### Admin Capabilities
- Admins can view deleted users with `User::withTrashed()`
- Admins can restore accounts if needed
- Hard delete available for GDPR compliance (after financial records handled)

---

## Database Schema

### Users Table
```sql
id (PK)
name
email
password
deleted_at  ← NEW (NULL = active, timestamp = deleted)
created_at
updated_at
```

### Expenses, Deposits, Meals
```sql
user_id (FK to users.id)  ← References deleted user, still valid
```

---

## Migration Instructions

### Step 1: Run Migration
```bash
php artisan migrate
```

This adds the `deleted_at` column to the users table.

### Step 2: Test
```bash
php artisan tinker

# Test soft delete
$user = User::find(2);
$service = new \App\Services\UserDeletionService();
$service->deleteUser($user);

# Verify soft delete
User::where('id', 2)->exists(); // false (excluded by scope)
User::withTrashed()->where('id', 2)->exists(); // true (with scope)

# Check financial records
User::withTrashed()->find(2)->expenses()->count(); // > 0
```

---

## Files Modified

1. **database/migrations/2026_07_06_000000_add_soft_deletes_to_users.php** (NEW)
   - Added soft delete migration

2. **app/Models/User.php**
   - Added SoftDeletes trait
   - Added expenses() relationship

3. **app/Services/UserDeletionService.php**
   - Updated canDelete() to check financial records
   - Updated prepareForDeletion() to preserve financial data
   - Updated deleteUser() to use soft delete
   - Added hardDeleteUser() for permanent deletion

4. **app/Http/Controllers/ProfileController.php**
   - Updated destroy() to return appropriate success message

---

## Next Steps (Optional)

### 1. Prevent Deleted User Login (Already Handled by SoftDeletes)
```php
// In User.php or Guard
// SoftDeletes scope automatically excludes deleted users
// No login possible since user won't be found by email
```

### 2. Admin Panel for Deleted Users
```php
// View deleted users
Route::get('/admin/deleted-users', [AdminController::class, 'showDeleted']);

// Restore deleted user
Route::post('/admin/users/{id}/restore', [AdminController::class, 'restore']);

// Permanent delete with financial record handling
Route::delete('/admin/users/{id}/permanent', [AdminController::class, 'permanentDelete']);
```

### 3. GDPR Compliance
```php
// For hard delete requirement:
1. Archive all financial records with reference to deleted user ID
2. Delete all personal data
3. Keep only transaction history with anonymized user reference
```

---

## Accounting Impact

### Mess Balance Calculations
✅ **NOT AFFECTED** - Uses user_id foreign key which remains valid

### Settlement Calculations  
✅ **NOT AFFECTED** - All transactions preserved

### Report Generation
✅ **WORKS** - Financial records accessible via `User::withTrashed()`

### Audit Trail
✅ **COMPLETE** - All transactions attributed to original (deleted) user

---

## Testing Checklist

- [ ] Migration runs without errors
- [ ] User can delete account with financial records
- [ ] Financial records remain after deletion
- [ ] User cannot login after deletion
- [ ] Mess calculations unchanged after deletion
- [ ] Deleted users excluded from user lists
- [ ] Admin can view deleted users
- [ ] Admin can restore deleted users
- [ ] Hard delete fails if user has financial records
- [ ] Hard delete succeeds if user has no financial records

---

## Support

**For users asking "Where did my data go?"**
- Account has been deactivated, not deleted
- Your financial history is preserved
- Mess accounting remains accurate
- Your transactions are still visible in reports

**For admins needing to restore a user**
```php
User::onlyTrashed()->find($id)->restore();
```

**For admins permanently deleting a user**
```php
// Only if user has no financial records
$service->hardDeleteUser($user);
```
