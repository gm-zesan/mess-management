# Role-Based Access Control (RBAC) Implementation

## Overview
Implemented a comprehensive role-based permission system using **Spatie Role and Permission** package for the Mess Management Application.

## Roles Defined

### 1. **Superadmin** 
- **Permissions**: ALL (25 total permissions)
- **Capabilities**: 
  - Full access to all features and operations
  - Can manage all users including managers
  - Cannot be restricted or limited

### 2. **Manager**
- **Permissions**: 24 permissions (all except `manage-superadmin`)
- **Capabilities**:
  - Full CRUD operations on all resources
  - Can create, edit, and delete other managers
  - Can manage all business operations
  - Cannot access superadmin-specific features
  - Only one manager can be active at a time (enforced at application level)

### 3. **Member**
- **Permissions**: 6 specific permissions only
- **Capabilities**:
  - `dashboard.view` - Access personal dashboard
  - `meals.view` - View meal records (read-only)
  - `members.view` - View member list (read-only)
  - `expenses.view` - View expense records (read-only)
  - `deposits.view` - View deposit records (read-only)
  - `reports.view` - Access reports (read-only)
- **Restrictions**:
  - Cannot create, edit, or delete any records
  - No access to month management or configuration
  - Read-only access to specific modules

## Permissions List

### Dashboard
- `dashboard.view` - View dashboard

### Meals Management
- `meals.view` - List meals
- `meals.create` - Create new meal
- `meals.edit` - Edit meal details
- `meals.delete` - Delete meal records

### Members/Users Management  
- `members.view` - List users
- `members.create` - Create new user
- `members.edit` - Edit user details
- `members.delete` - Delete user
- `members.manage-roles` - Assign/change roles (Manager + Superadmin only)

### Expenses Management
- `expenses.view` - List expenses
- `expenses.create` - Create new expense
- `expenses.edit` - Edit expense
- `expenses.delete` - Delete expense

### Deposits Management
- `deposits.view` - List deposits
- `deposits.create` - Create new deposit
- `deposits.edit` - Edit deposit
- `deposits.delete` - Delete deposit

### Months Management
- `months.view` - List months
- `months.create` - Create new month
- `months.edit` - Edit month
- `months.delete` - Delete month
- `months.close` - Close/finalize month

### Reports
- `reports.view` - Access all reports

### Special Permissions
- `manage-superadmin` - Manage superadmin access (Superadmin only)

## Database Seeders

### RoleSeeder
Creates three roles in the database:
- superadmin
- manager
- member

### PermissionSeeder
Creates all 25 permissions and assigns them to roles:
- Superadmin gets all permissions
- Manager gets all except `manage-superadmin`
- Member gets 6 specific read-only permissions

### DatabaseSeeder
Creates 5 test users with roles assigned:

| Email | Name | Role | Password |
|-------|------|------|----------|
| superadmin@example.com | Super Admin | superadmin | password |
| test@example.com | Test User | manager | password |
| ashraf@example.com | Ashraf Ahmed | member | password |
| karim@example.com | Karim Khan | member | password |
| fatima@example.com | Fatima Hassan | member | password |

## Route Protection

All routes are protected using Laravel's `can:` middleware with specific permissions:

```php
// Example route protection
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware('can:dashboard.view')
    ->name('dashboard');

Route::get('/meals', [MealController::class, 'index'])
    ->middleware('can:meals.view')
    ->name('meals.index');
```

### Protected Resource Routes

All CRUD operations are granularly protected:

- **List/View**: `can:resource.view`
- **Create**: `can:resource.create`
- **Edit**: `can:resource.edit`
- **Delete**: `can:resource.delete`

Routes protected include:
- Dashboard
- Members/Users
- Meals
- Expenses
- Deposits
- Months
- Reports

## User Model

The `User` model includes:
- `HasRoles` trait from Spatie Permission
- Methods to check roles and permissions:
  ```php
  $user->hasRole('manager');
  $user->hasPermissionTo('meals.create');
  $user->assignRole('member');
  $user->givePermissionTo('expenses.create');
  ```

## How to Use

### Check User Permissions in Code
```php
if (auth()->user()->hasPermissionTo('expenses.create')) {
    // User can create expenses
}

if (auth()->user()->hasRole('manager')) {
    // User is a manager
}
```

### Check Permissions in Blade Templates
```blade
@can('meals.create')
    <a href="{{ route('meals.create') }}" class="btn btn-primary">
        Create Meal
    </a>
@endcan

@role('manager')
    <div class="manager-controls">
        <!-- Manager-only content -->
    </div>
@endrole
```

### Assign Role to User Programmatically
```php
$user->assignRole('member');
// or
$user->syncRoles(['manager', 'member']);
```

### Give Permission to User
```php
$user->givePermissionTo('expenses.create');
```

## Database Tables Created by Spatie

1. **roles** - Stores role definitions
2. **permissions** - Stores permission definitions  
3. **role_has_permissions** - Maps permissions to roles
4. **model_has_roles** - Maps roles to users
5. **model_has_permissions** - Maps permissions directly to users

## Testing the Setup

Run `php artisan migrate:fresh --seed` to:
1. Reset the database
2. Create all tables
3. Create roles and permissions
4. Create test users with assigned roles

## Implementing Manager Constraint (Only One at a Time)

To enforce only one active manager, add this to the User model or a Manager service:

```php
// In User model or UserService
public function setRoleToManager()
{
    // Remove manager role from all users
    User::role('manager')->syncRoles([]);
    
    // Assign manager role to this user
    $this->assignRole('manager');
}
```

## Access Control Policy

### Member Access Levels
- ✅ View own dashboard
- ✅ View meals (read-only)
- ✅ View member list (read-only)
- ✅ View expenses (read-only)
- ✅ View deposits (read-only)
- ✅ View reports (read-only)
- ❌ Create/Edit/Delete any records
- ❌ Manage months or settings

### Manager Access Levels
- ✅ Full CRUD on all resources
- ✅ Create and manage other managers
- ✅ Manage all application features
- ✅ View all reports and analytics
- ❌ Access superadmin features

### Superadmin Access Levels
- ✅ Full system access
- ✅ Manage all users and roles
- ✅ Manage all permissions
- ✅ Access all features and settings

## Configuration

The permission system uses:
- **Guard**: `web` (standard Laravel authentication guard)
- **Package**: `spatie/laravel-permission`
- **Implementation**: Model-based with middleware protection

## Files Modified/Created

1. **database/seeders/RoleSeeder.php** - Role creation (updated)
2. **database/seeders/PermissionSeeder.php** - Permission creation (new)
3. **database/seeders/DatabaseSeeder.php** - User creation with roles (updated)
4. **routes/web.php** - Route protection with permissions (updated)
5. **app/Models/User.php** - Already has HasRoles trait

## Next Steps

1. Update blade templates to conditionally show buttons based on permissions
2. Implement manager selection logic to ensure only one active manager
3. Add permission checks in controllers for additional security
4. Create admin panel for role and permission management
5. Implement audit logging for permission changes
