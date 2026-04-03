# Quick Reference & Common Tasks

## Quick Enum Usage

### Check Role
```php
// In code
if (auth()->user()->hasRole(RoleEnum::MANAGER)) {
    // Is manager
}

// In Blade
@role('manager')
@endrole
```

### Check Permission
```php
// In code
if (auth()->user()->can(PermissionEnum::EXPENSES_CREATE->value)) {
    // Can create expenses
}

// In Blade
@can('expenses.create')
@endcan
```

### Assign Role
```php
$user->assignRole(RoleEnum::MEMBER);

// Or enum directly (supported by Spatie)
$user->assignRole(RoleEnum::MANAGER);
```

## Controller Authorization

### Basic Authorization
```php
public function create()
{
    $this->authorize('create', Meal::class);
    
    return view('meals.create');
}

public function store(StoreMealRequest $request)
{
    $this->authorize('create', Meal::class);
    
    Meal::create($request->validated());
    return redirect()->back()->with('success', 'Created');
}

public function edit(Meal $meal)
{
    $this->authorize('update', $meal);
    
    return view('meals.edit', compact('meal'));
}

public function update(UpdateMealRequest $request, Meal $meal)
{
    $this->authorize('update', $meal);
    
    $meal->update($request->validated());
    return redirect()->back()->with('success', 'Updated');
}

public function destroy(Meal $meal)
{
    $this->authorize('delete', $meal);
    
    $meal->delete();
    return redirect()->back()->with('success', 'Deleted');
}
```

### Manual Authorization Check
```php
// Using Gate
if (Gate::allows('create', Meal::class)) {
    // Can create
}

if (Gate::denies('delete', $meal)) {
    return redirect()->back()->with('error', 'Not authorized');
}

// Using user can
if (auth()->user()->can('view', $meal)) {
    // Can view
}
```

### Authorization Exceptions

```php
// Throws 403 if unauthorized
$this->authorize('delete', $meal);

// Manual throw
if (!auth()->user()->can('edit', $meal)) {
    throw new \Illuminate\Auth\Access\AuthorizationException();
}
```

## Blade Directives

### Simple Permission Check
```blade
@can('create', \App\Models\Meal::class)
    <button class="btn btn-primary">Create Meal</button>
@endcan
```

### Permission with Model
```blade
@can('update', $meal)
    <a href="{{ route('meals.edit', $meal) }}" class="btn btn-warning">
        Edit
    </a>
@endcan
```

### Cannot (Negative Check)
```blade
@cannot('delete', $meal)
    <p class="alert">You cannot delete this meal</p>
@endcannot
```

### Role Check
```blade
@role('manager')
    <div class="admin-panel">
        Admin Controls
    </div>
@endrole
```

### Multiple Roles
```blade
@role('manager|superadmin')
    <div>Managers and admins only</div>
@endrole
```

### With Else
```blade
@can('meals.create')
    <button>Create</button>
@else
    <span class="text-muted">No permission</span>
@endcan
```

## Querying Permissions

### In Seeder or Migrations
```php
use App\Enums\PermissionEnum;

// Get or create permission
$permission = Permission::firstOrCreate([
    'name' => PermissionEnum::EXPENSES_CREATE->value,
    'guard_name' => 'web'
]);

// Assign to role
$managerRole->givePermissionTo($permission);

// or
$managerRole->givePermissionTo(PermissionEnum::EXPENSES_CREATE->value);
```

### In Service
```php
use App\Enums\RoleEnum;

// Find role
$manager = Role::findByName(RoleEnum::MANAGER->value);

// Get all permissions for role
$permissions = $manager->getAllPermissions();

// Check if role has permission
if ($manager->hasPermissionTo(PermissionEnum::MEALS_CREATE->value)) {
    // Has permission
}
```

## Role Management

### Assign Role
```php
$user->assignRole(RoleEnum::MEMBER);
$user->assignRole([RoleEnum::MEMBER, RoleEnum::MANAGER]);
```

### Remove Role
```php
$user->removeRole(RoleEnum::MEMBER);
```

### Sync Roles (Replace All)
```php
// Keep only these roles
$user->syncRoles([RoleEnum::MANAGER]);

// This removes all other roles
```

### Check Role
```php
$user->hasRole(RoleEnum::MANAGER);
$user->hasAnyRole([RoleEnum::MANAGER, RoleEnum::SUPERADMIN]);
$user->hasAllRoles([RoleEnum::MANAGER, RoleEnum::MEMBER]);
```

## Permission Management

### Give Permission to User
```php
$user->givePermissionTo(PermissionEnum::MEALS_CREATE->value);
```

### Remove Permission from User
```php
$user->revokePermissionTo(PermissionEnum::MEALS_DELETE->value);
```

### Sync Permissions
```php
// Replace all user permissions
$user->syncPermissions([
    PermissionEnum::MEALS_VIEW->value,
    PermissionEnum::EXPENSES_VIEW->value
]);
```

### Check Permission
```php
$user->hasPermissionTo(PermissionEnum::MEALS_CREATE->value);
$user->hasAnyPermission([PermissionEnum::MEALS_CREATE, PermissionEnum::MEALS_EDIT]);
$user->hasAllPermissions([PermissionEnum::MEALS_CREATE, PermissionEnum::MEALS_DELETE]);
```

## Testing Authorization

### Test Permission Works
```php
use App\Models\User;
use App\Enums\RoleEnum;

public function test_manager_can_create_meals()
{
    $manager = User::factory()->create();
    $manager->assignRole(RoleEnum::MANAGER);

    $this->actingAs($manager)
        ->get(route('meals.create'))
        ->assertOk();
}
```

### Test Permission Denied
```php
public function test_member_cannot_delete_meals()
{
    $member = User::factory()->create();
    $member->assignRole(RoleEnum::MEMBER);
    
    $meal = Meal::factory()->create();

    $this->actingAs($member)
        ->delete(route('meals.destroy', $meal))
        ->assertForbidden();
}
```

### Test Superadmin Access
```php
public function test_superadmin_can_do_anything()
{
    $superadmin = User::factory()->create();
    $superadmin->assignRole(RoleEnum::SUPERADMIN);
    
    $meal = Meal::factory()->create();

    $this->actingAs($superadmin)
        ->delete(route('meals.destroy', $meal))
        ->assertOk();
}
```

## Debugging

### Check User Roles
```php
$user = User::find(1);
dd($user->getRoleNames());  // Collection of role names
dd($user->getPermissionNames());  // Collection of permission names
```

### Check in Tinker
```bash
php artisan tinker

>>> $user = User::find(1)
>>> $user->getRoleNames()
>>> $user->getPermissionNames()
>>> $user->hasRole('manager')
>>> $user->can('expenses.create')
```

### Check Enum Values
```php
// Get all enum cases
RoleEnum::cases();          // Returns array of cases
PermissionEnum::cases();    // Returns array of cases

// Get enum value
RoleEnum::MANAGER->value;  // 'manager'
PermissionEnum::MEALS_VIEW->value;  // 'meals.view'

// Get enum label
RoleEnum::MANAGER->label();  // 'Manager'
```

## Common Mistakes & Solutions

### ❌ Permission Not Found
```php
// WRONG - Role name vs permission
$role->givePermissionTo('admin');  // This is a role, not permission

// RIGHT
$role->givePermissionTo('meals.create');
```

### ❌ Forgot Enum Value
```php
// WRONG
$user->can(RoleEnum::MANAGER);

// RIGHT
$user->can(PermissionEnum::MEALS_CREATE->value);
```

### ❌ Policy Before Returns False
```php
// WRONG - breaks policies when returning false
public function before(User $user, string $ability): bool
{
    return false;  // This prevents all other policies from running!
}

// RIGHT - return null to fall through
public function before(User $user, string $ability): ?bool
{
    return $user->hasRole('superadmin') ? true : null;
}
```

### ❌ Forgot to Register Policy
```php
// WRONG - Policy exists but not registered
// Policies are registered in AppServiceProvider

// RIGHT
// In AppServiceProvider boot()
Gate::policy(Meal::class, MealPolicy::class);
```

### ❌ Mixed Role & Permission Checks
```php
// AVOID - mixing approaches
if ($user->hasRole('manager') && $user->can('meals.create')) {
    // ...
}

// PREFER - use just permissions
if ($user->can('meals.create')) {
    // ...
}
```

## Performance Tips

1. **Cache Permissions**
   - Already handled by Spatie
   - Clear cache after role/permission changes: `php artisan cache:clear`

2. **Eager Load Roles**
   ```php
   User::with('roles', 'permissions')->get();
   ```

3. **Use Policies over Middleware**
   - Policies are more efficient
   - Shared between controller and Blade

4. **Check Superadmin Early**
   - Gate::before() fires first
   - No need for multiple checks

## Quick Copy-Paste Templates

### Authorization in Controller
```php
public function METHODNAME()
{
    $this->authorize('ABILITY', MODEL::class);  // or $model instance
    
    // Your logic here
}
```

### Permission Blade Check
```blade
@can('PERMISSION_NAME')
    <!-- Content -->
@endcan
```

### Role-Based Blade
```blade
@role('ROLE_NAME')
    <!-- Content -->
@endrole
```

### Enum Usage
```php
use App\Enums\RoleEnum;
use App\Enums\PermissionEnum;

$user->assignRole(RoleEnum::MANAGER);
$user->can(PermissionEnum::MEALS_CREATE->value);
```

---

**Last Updated:** April 4, 2026
**Version:** 2.0
