# Spatie RBAC Best Practices Implementation Guide

This document covers the complete implementation of role-based access control using Spatie Role & Permission with best practices.

## Architecture Overview

### Core Components

1. **Enums** - Type-safe role and permission definitions
2. **Policies** - Model-based authorization rules
3. **Gate** - Application-wide authorization with super-admin bypass
4. **Blade Directives** - UI-level permission checks

### File Structure

```
app/
├── Enums/
│   ├── RoleEnum.php           # Role definitions
│   └── PermissionEnum.php      # Permission definitions
├── Policies/
│   ├── MealPolicy.php
│   ├── ExpensePolicy.php
│   ├── DepositPolicy.php
│   ├── UserPolicy.php
│   └── MonthPolicy.php
├── Providers/
│   └── AppServiceProvider.php  # Policy & Gate registration
└── Models/
    └── User.php               # HasRoles trait included

database/
└── seeders/
    ├── RoleSeeder.php         # Creates roles
    ├── PermissionSeeder.php   # Creates permissions
    └── DatabaseSeeder.php     # Seeds users with roles

routes/
└── web.php                    # Simplified resource routes
```

## 1. Enums

### RoleEnum.php
Defines all application roles with labels.

```php
enum RoleEnum: string
{
    case SUPERADMIN = 'superadmin';
    case MANAGER = 'manager';
    case MEMBER = 'member';

    public function label(): string { ... }
}
```

**Benefits:**
- Type-safe role references
- IDE autocomplete support
- Single source of truth for roles
- Easy to display role names

### PermissionEnum.php
Defines all application permissions with labels.

```php
enum PermissionEnum: string
{
    case DASHBOARD_VIEW = 'dashboard.view';
    case MEALS_CREATE = 'meals.create';
    // ... other permissions
    
    public function label(): string { ... }
}
```

**Benefits:**
- Type-safe permission references
- Organized by resource
- Clear permission hierarchy
- Consistent naming convention

## 2. Policies

Policies centralize authorization logic for model operations.

### Policy Structure
```php
class MealPolicy
{
    // Super-admin bypass in individual policies
    public function before(User $user, string $ability): ?bool
    {
        if ($user->hasRole('superadmin')) {
            return true;  // Grant all abilities
        }
        return null;      // Fall through to normal checks
    }

    public function viewAny(User $user): bool
    {
        return $user->can(PermissionEnum::MEALS_VIEW->value);
    }

    public function view(User $user, Meal $meal): bool
    {
        return $user->can(PermissionEnum::MEALS_VIEW->value);
    }

    public function create(User $user): bool
    {
        return $user->can(PermissionEnum::MEALS_CREATE->value);
    }

    public function update(User $user, Meal $meal): bool
    {
        return $user->can(PermissionEnum::MEALS_EDIT->value);
    }

    public function delete(User $user, Meal $meal): bool
    {
        return $user->can(PermissionEnum::MEALS_DELETE->value);
    }
}
```

### Policy Methods

| Method | Triggered By | Usage |
|--------|--------------|-------|
| `viewAny($user)` | `index` route | Authorize listing |
| `view($user, $model)` | `show` route | Authorize viewing |
| `create($user)` | `create` route | Authorize creation |
| `update($user, $model)` | `edit` route | Authorize editing |
| `delete($user, $model)` | `destroy` route | Authorize deletion |
| `before($user, $ability)` | All checks | Super-admin bypass |

### Registering Policies

In `AppServiceProvider.php`:

```php
public function boot(): void
{
    Gate::policy(Meal::class, MealPolicy::class);
    Gate::policy(Expense::class, ExpensePolicy::class);
    Gate::policy(Deposit::class, DepositPolicy::class);
    Gate::policy(User::class, UserPolicy::class);
    Gate::policy(Month::class, MonthPolicy::class);
}
```

## 3. Super-Admin Implementation

### Global Gate::before()

In `AppServiceProvider.php`:

```php
Gate::before(function (User $user, string $ability) {
    return $user->hasRole(RoleEnum::SUPERADMIN) ? true : null;
});
```

**How it works:**
- Called before any other authorization check
- Returns `true` if user is superadmin (grants permission)
- Returns `null` to fall through to policy/permission checks
- **IMPORTANT:** Must return `null` (not `false`) to allow policies to run

### Policy-Level Super-Admin

```php
public function before(User $user, string $ability): ?bool
{
    if ($user->hasRole(RoleEnum::SUPERADMIN)) {
        return true;
    }
    return null;
}
```

## 4. Routes Configuration

### Simplified Routes with Resource

```php
Route::middleware('auth')->group(function () {
    // Simple resource routes - policies handle authorization
    Route::resource('members', MemberController::class);
    Route::resource('meals', MealController::class);
    Route::resource('expenses', ExpenseController::class);
    Route::resource('deposits', DepositController::class);
    Route::resource('months', MonthController::class);
});
```

Resource routes automatically trigger these controller methods:
- `index()` → Calls policy `viewAny()`
- `show()` → Calls policy `view()`
- `create()` → Calls policy `create()`
- `edit()` → Calls policy `update()`
- `destroy()` → Calls policy `delete()`

### Authorizing in Controllers

```php
public function index()
{
    $this->authorize('viewAny', Meal::class);
    
    $meals = Meal::paginate(15);
    return view('meals.index', compact('meals'));
}

public function show(Meal $meal)
{
    $this->authorize('view', $meal);
    
    return view('meals.show', compact('meal'));
}

public function delete(Meal $meal)
{
    $this->authorize('delete', $meal);
    
    $meal->delete();
    return redirect()->back()->with('success', 'Deleted');
}
```

**Automatic Authorization:**
- Laravel automatically calls relevant policy method
- Returns 403 Forbidden if unauthorized
- Checks happen transparently in routes

## 5. Seeding

### RoleSeeder.php
```php
foreach (RoleEnum::cases() as $role) {
    Role::firstOrCreate(['name' => $role->value, 'guard_name' => 'web']);
}
```

### PermissionSeeder.php
```php
// Create all permissions
foreach (PermissionEnum::cases() as $permission) {
    Permission::firstOrCreate(['name' => $permission->value, 'guard_name' => 'web']);
}

// Assign to roles
$superAdmin->syncPermissions(Permission::all());
$manager->syncPermissions(Permission::whereNotIn('name', [])->get());
$member->syncPermissions(Permission::whereIn('name', [...])->get());
```

### DatabaseSeeder.php
```php
$user = User::factory()->create([...]);
$user->assignRole(RoleEnum::MANAGER);  // Type-safe!
```

## 6. Blade Directives

### Permission Checks
```blade
@can('expenses.create')
    <a href="{{ route('expenses.create') }}">Add Expense</a>
@endcan

@cannot('members.delete')
    <span>No permission to delete members</span>
@endcannot
```

### Role Checks
```blade
@role('manager')
    <div>Manager only content</div>
@endrole

@role('manager|superadmin')
    <div>Managers and superadmin only</div>
@endrole
```

### Conditional UI
```blade
<table>
    <tr>
        <td>{{ $expense->amount }}</td>
        <td>
            @can('expenses.edit')
                <a href="{{ route('expenses.edit', $expense) }}">Edit</a>
            @endcan
            
            @can('expenses.delete')
                <form action="{{ route('expenses.destroy', $expense) }}" method="POST">
                    <button type="submit">Delete</button>
                </form>
            @endcan
        </td>
    </tr>
</table>
```

## 7. Usage Patterns

### In Controllers
```php
// Authorize action
$this->authorize('delete', $meal);

// Or using gate
Gate::authorize('delete-meal');

// Check permission
if (auth()->user()->can('create', Meal::class)) {
    // Can create
}
```

### In Blade
```blade
@can('view', $meal)
    {{ $meal->name }}
@endcan

@role('manager|superadmin')
    <button>Manage</button>
@endrole
```

### In Queries
```php
// Filter based on permissions
$meals = Meal::whereHas('user', function($q) {
    $q->where('created_by', auth()->id());
})->get();
```

## 8. Best Practices

### ✅ DO:
- Use Enums for type safety
- Use Model Policies for authorization
- Use Blade directives for UI checks
- Use `Gate::before()` for super-admin
- Authorize in controller methods
- Use permission names (not roles) in policies
- Return `null` (not `false`) in before() checks

### ❌ DON'T:
- Use role-based authorization in policies
- Mix `hasRole()` checks throughout code
- Forget `null` return in policy `before()` 
- Give all permissions to superadmin role
- Use middleware with can: for every route
- Check permissions directly in views

## 9. Role Definitions

### Superadmin
- **Permissions:** ALL (granted by Gate::before)
- **Access:** Everything
- **Restrictions:** None

### Manager
- **Permissions:** Everything except superadmin control
- **Access:** Full CRUD on all resources
- **Restrictions:** Cannot manage superadmin

### Member  
- **Permissions:** Dashboard, Meals, Members, Expenses, Deposits, Reports (READ ONLY)
- **Access:** View operations only
- **Restrictions:** No create, edit, delete operations

## 10. Testing Permissions

```php
public function test_manager_can_create_meals()
{
    $manager = User::factory()
        ->has(Role::whereIn('name', ['manager'])->first())
        ->create();

    $this->actingAs($manager)
        ->get(route('meals.create'))
        ->assertOk();
}

public function test_member_cannot_delete_meals()
{
    $member = User::factory()
        ->has(Role::whereIn('name', ['member'])->first())
        ->create();

    $meal = Meal::factory()->create();

    $this->actingAs($member)
        ->delete(route('meals.destroy', $meal))
        ->assertForbidden();
}
```

## 11. Troubleshooting

### Permission Not Working?
- Check enum value matches database
- Verify policy is registered in AppServiceProvider
- Ensure `before()` returns `null` (not `false`)
- Check user has correct role assigned

### Role Not Assigned?
- Run `php artisan db:seed DatabaseSeeder`
- Verify role exists in database
- Check user_role entry in model_has_roles table

### Superadmin Can't Access?
- Verify Gate::before is configured
- Check user has superadmin role
- Clear cache: `php artisan cache:clear`

## References

- [Spatie Permission Docs](https://spatie.be/docs/laravel-permission/v7)
- [Model Policies](https://spatie.be/docs/laravel-permission/v7/best-practices/using-policies)
- [Enums](https://spatie.be/docs/laravel-permission/v7/basic-usage/enums)
- [Super-Admin](https://spatie.be/docs/laravel-permission/v7/basic-usage/super-admin)
- [Blade Directives](https://spatie.be/docs/laravel-permission/v7/basic-usage/blade-directives)

## Summary

This implementation follows Spatie best practices by:
1. Using Enums for type-safe role/permission references
2. Using Model Policies for centralized authorization
3. Using Gate::before() for transparent super-admin bypass
4. Simplifying routes with automatic policy authorization
5. Using Blade directives for clean, readable permission checks
6. Avoiding role-based authorization in business logic
7. Keeping authorization logic DRY and maintainable
