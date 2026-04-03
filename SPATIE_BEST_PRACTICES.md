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

This follows **[Spatie's official best practice](https://spatie.be/docs/laravel-permission/v7/best-practices/using-policies)**: using Laravel's Model Policies with `$this->authorize()` for backend enforcement.

Every controller action that requires authorization should call `$this->authorize()`. This ensures **backend security** - members cannot bypass Blade directives by accessing URLs directly.

```php
class MealController extends Controller
{
    // List view - check if user can view meals
    public function index()
    {
        $this->authorize('viewAny', Meal::class);
        
        $meals = Meal::paginate(15);
        return view('meals.index', compact('meals'));
    }

    // Show create form - check if user can create
    public function create()
    {
        $this->authorize('create', Meal::class);
        
        return view('meals.create');
    }

    // Store to database - check if user can create
    public function store(StoreMealRequest $request)
    {
        $this->authorize('create', Meal::class);
        
        Meal::create($request->validated());
        return redirect()->route('meals.index')->with('success', 'Created');
    }

    // Show detail view - check if user can view
    public function show(Meal $meal)
    {
        $this->authorize('view', $meal);
        
        return view('meals.show', compact('meal'));
    }

    // Show edit form - check if user can update
    public function edit(Meal $meal)
    {
        $this->authorize('update', $meal);
        
        return view('meals.edit', compact('meal'));
    }

    // Update to database - check if user can update
    public function update(StoreMealRequest $request, Meal $meal)
    {
        $this->authorize('update', $meal);
        
        $meal->update($request->validated());
        return redirect()->route('meals.index')->with('success', 'Updated');
    }

    // Delete from database - check if user can delete
    public function destroy(Meal $meal)
    {
        $this->authorize('delete', $meal);
        
        $meal->delete();
        return redirect()->back()->with('success', 'Deleted');
    }
}
```

### How it Works

1. **Controller calls `$this->authorize()`** with ability and optionally a model instance
2. **Laravel passes to the corresponding Policy method** (create, update, delete, view, viewAny)
3. **Policy method checks Spatie permission** via `$user->can('permission.name')`
4. **Returns 403 Forbidden** if user doesn't have permission
5. **Superadmin automatically bypasses** via `Gate::before()`

**Key Points:**
- Call `$this->authorize()` in **EVERY** controller method
- Use `Model::class` for non-model-specific checks (create, viewAny)
- Use `$model` instance for model-specific checks (update, delete, view)
- Returns 403 Forbidden if unauthorized
- Provides defense-in-depth: Blade directives hide UI + Controllers enforce access
- **This is the official Spatie recommended approach** per their documentation

### Alternative: Direct Middleware (Optional)

Spatie also provides middleware if you prefer route-level protection:

```php
Route::middleware(['permission:meals.create'])->group(function () {
    Route::post('/meals', [MealController::class, 'store']);
});
```

However, **Policies with `$this->authorize()` is preferred** because they:
- Keep authorization logic centralized in one place (Policy)
- Allow complex logic mixing Spatie permissions with business rules
- Are easier to test
- Follow Laravel convention

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

### Create/View Action Buttons
```blade
@can('expenses.create')
    <a href="{{ route('expenses.create') }}" class="btn btn-primary">
        Add Expense
    </a>
@endcan
```

### Two-Level Permission Gating (View + Actions)
```blade
<!-- Wrap entire table in view permission -->
@can('expenses.view')
    <table class="table">
        <tbody>
            @foreach ($expenses as $expense)
                <tr>
                    <td>{{ $expense->amount }}</td>
                    <td>
                        <!-- Individual action permissions -->
                        @can('view', $expense)
                            <a href="{{ route('expenses.show', $expense) }}">View</a>
                        @endcan
                        
                        @can('update', $expense)
                            <a href="{{ route('expenses.edit', $expense) }}">Edit</a>
                        @endcan
                        
                        @can('delete', $expense)
                            <form action="{{ route('expenses.destroy', $expense) }}" method="POST" style="display:inline;">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                            </form>
                        @endcan
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@else
    <div class="alert alert-warning">
        You don't have permission to view expenses.
    </div>
@endcan
```

### Model-Specific Authorization
```blade
<!-- For individual resource actions -->
@can('update', $expense)
    <a href="{{ route('expenses.edit', $expense) }}" class="btn btn-warning">
        Edit Expense
    </a>
@endcan

@can('delete', $expense)
    <form action="{{ route('expenses.destroy', $expense) }}" method="POST">
        @csrf @method('DELETE')
        <button type="submit" class="btn btn-danger" 
                onclick="return confirm('Delete this expense?')">
            Delete
        </button>
    </form>
@endcan
```

### Role-Based UI
```blade
<!-- Role checks for superadmin-only features -->
@role('superadmin')
    <div class="admin-panel">
        System administration tools
    </div>
@endrole

<!-- Manager-specific features -->
@role('manager|superadmin')
    <button class="btn btn-primary">Manage Roles</button>
@endrole
```

### Practical Module Examples

#### Expenses List View
```blade
@can('expenses.create')
    <a href="{{ route('expenses.create') }}" class="btn btn-primary">Add Expense</a>
@endcan

@can('expenses.view')
    <div class="table-responsive">
        <table class="table">
            @foreach ($expenses as $expense)
                <tr>
                    <td>{{ $expense->category }}</td>
                    <td>${{ $expense->amount }}</td>
                    <td>
                        @can('view', $expense)
                            <a href="{{ route('expenses.show', $expense) }}">View</a>
                        @endcan
                        @can('update', $expense)
                            <a href="{{ route('expenses.edit', $expense) }}">Edit</a>
                        @endcan
                        @can('delete', $expense)
                            <form action="{{ route('expenses.destroy', $expense) }}" method="POST">
                                <button>Delete</button>
                            </form>
                        @endcan
                    </td>
                </tr>
            @endforeach
        </table>
    </div>
@else
    <div class="alert alert-danger">You don't have permission to view expenses.</div>
@endcan
```

#### Months Management
```blade
@can('months.create')
    <a href="{{ route('months.create') }}" class="btn btn-primary">Create Month</a>
@endcan

@can('months.view')
    <table>
        @foreach ($months as $month)
            <tr>
                <td>
                    {{ $month->month_name }}
                    @if ($month->status === \App\Enums\MonthStatusEnum::ACTIVE)
                        <span class="badge bg-success">Active</span>
                    @else
                        <span class="badge bg-danger">Closed</span>
                    @endif
                </td>
                <td>
                    @can('view', $month)
                        <a href="{{ route('months.show', $month) }}">View Report</a>
                    @endcan
                    @can('update', $month)
                        <a href="{{ route('months.edit', $month) }}">Edit</a>
                        @if ($month->status === \App\Enums\MonthStatusEnum::ACTIVE)
                            <form action="{{ route('months.close', $month) }}" method="POST">
                                <button>Close Month</button>
                            </form>
                        @endif
                    @endcan
                </td>
            </tr>
        @endforeach
    </table>
@else
    <div class="alert alert-warning">You don't have permission to view months.</div>
@endcan
```

#### Member Role Management
```blade
@can('members.create')
    <a href="{{ route('members.create') }}" class="btn btn-primary">Add Member</a>
@endcan

@can('members.view')
    <table>
        @foreach ($members as $member)
            <tr>
                <td>{{ $member->name }}</td>
                <td>
                    @foreach ($member->roles as $role)
                        <span class="badge bg-info">{{ $role->name }}</span>
                    @endforeach
                </td>
                <td>
                    @can('members.manage-roles')
                        <button class="btn btn-warning btn-sm" onclick="changeRole({{ $member->id }})">
                            <i class="fas fa-crown"></i> Change Role
                        </button>
                    @endcan
                </td>
            </tr>
        @endforeach
    </table>
@else
    <div class="alert alert-warning">You don't have permission to view members.</div>
@endcan
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
