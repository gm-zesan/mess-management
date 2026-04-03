# Blade Directives for Permission Control

## Overview
This guide shows how to use Blade directives to conditionally show/hide UI elements based on roles and permissions.

## Using @can Directive for Permissions

### Check Single Permission
```blade
@can('expenses.create')
    <a href="{{ route('expenses.create') }}" class="btn btn-primary">
        Create Expense
    </a>
@endcan
```

### Check Multiple Permissions (Any)
```blade
@can('expenses.create')
    <div class="action-buttons">
        <a href="{{ route('expenses.create') }}" class="btn btn-primary">Create</a>
        <a href="{{ route('expenses.edit', $expense) }}" class="btn btn-warning">Edit</a>
    </div>
@endcan
```

### Using Else Block
```blade
@can('expenses.delete')
    <form action="{{ route('expenses.destroy', $expense) }}" method="POST" style="display: inline;">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-danger">Delete</button>
    </form>
@else
    <span class="text-muted">You don't have permission to delete this</span>
@endcan
```

## Using @role Directive for Roles

### Check Single Role
```blade
@role('manager')
    <div class="manager-panel">
        <h5>Manager Controls</h5>
        <p>Only managers can see this section</p>
    </div>
@endrole
```

### Check Multiple Roles (Any)
```blade
@role('manager|superadmin')
    <a href="{{ route('members.create') }}" class="btn btn-success">
        Add New Member
    </a>
@endrole
```

### Using Else Block
```blade
@role('manager')
    <button class="btn btn-danger">Close Month</button>
@else
    <span class="badge bg-secondary">Only managers can close months</span>
@endrole
```

## Using @cannot Directive

### Show Content When User Cannot Do Something
```blade
@cannot('meals.delete')
    <p class="alert alert-info">
        <i class="fas fa-info-circle"></i> 
        You don't have permission to delete meals
    </p>
@endcannot
```

## Using @hasrole and @hasanyrole (Direct Methods)

While @role directive is preferred, you can also use direct checking:

```blade
@if(auth()->user()->hasRole('manager'))
    <div class="manager-section">
        <!-- Manager content -->
    </div>
@endif
```

## Practical Examples

### Expense Create Button with Permission Check
```blade
@can('expenses.create')
    <div class="mb-3">
        <a href="{{ route('expenses.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add Expense
        </a>
    </div>
@endcan
```

### Member List with Action Buttons
```blade
@forelse($members as $member)
    <tr>
        <td>{{ $member->name }}</td>
        <td>{{ $member->email }}</td>
        <td>
            @can('members.view')
                <a href="{{ route('members.show', $member) }}" class="btn btn-sm btn-info">
                    <i class="fas fa-eye"></i> View
                </a>
            @endcan

            @can('members.edit')
                <a href="{{ route('members.edit', $member) }}" class="btn btn-sm btn-warning">
                    <i class="fas fa-edit"></i> Edit
                </a>
            @endcan

            @can('members.delete')
                <form action="{{ route('members.destroy', $member) }}" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-danger" 
                            onclick="return confirm('Are you sure?')">
                        <i class="fas fa-trash"></i> Delete
                    </button>
                </form>
            @endcan
        </td>
    </tr>
@empty
    <tr>
        <td colspan="5" class="text-center text-muted">No members found</td>
    </tr>
@endforelse
```

### Dashboard with Role-Based Sections
```blade
<div class="dashboard">
    <h1>Welcome, {{ auth()->user()->name }}</h1>

    @role('manager|superadmin')
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5>Manager Dashboard</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    @can('expenses.view')
                        <div class="col-md-6">
                            <a href="{{ route('expenses.index') }}" class="btn btn-outline-primary w-100">
                                View Expenses
                            </a>
                        </div>
                    @endcan

                    @can('months.close')
                        <div class="col-md-6">
                            <a href="{{ route('months.index') }}" class="btn btn-outline-danger w-100">
                                Manage Months
                            </a>
                        </div>
                    @endcan
                </div>
            </div>
        </div>
    @endrole

    @role('member')
        <div class="card mb-4">
            <div class="card-header bg-info text-white">
                <h5>Member View</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <a href="{{ route('meals.index') }}" class="btn btn-outline-info w-100">
                            View Meals
                        </a>
                    </div>
                    <div class="col-md-6">
                        <a href="{{ route('reports.all-months') }}" class="btn btn-outline-info w-100">
                            View Reports
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endrole
</div>
```

### Navigation with Permission-Based Menus
```blade
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container-fluid">
        <a class="navbar-brand" href="{{ route('dashboard') }}">
            Mess Management
        </a>

        <div class="collapse navbar-collapse">
            <ul class="navbar-nav ms-auto">
                @can('meals.view')
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('meals.index') }}">Meals</a>
                    </li>
                @endcan

                @can('expenses.view')
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('expenses.index') }}">Expenses</a>
                    </li>
                @endcan

                @can('deposits.view')
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('deposits.index') }}">Deposits</a>
                    </li>
                @endcan

                @role('manager|superadmin')
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            Management
                        </a>
                        <ul class="dropdown-menu">
                            <li>
                                <a class="dropdown-item" href="{{ route('members.index') }}">
                                    Members
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="{{ route('months.index') }}">
                                    Months
                                </a>
                            </li>
                        </ul>
                    </li>
                @endrole

                @can('reports.view')
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('reports.all-months') }}">Reports</a>
                    </li>
                @endcan
            </ul>
        </div>
    </div>
</nav>
```

## Using Enums with Blade Directives

When using enums, you need to pass the enum value:

```blade
@can(\App\Enums\PermissionEnum::EXPENSES_CREATE->value)
    <a href="{{ route('expenses.create') }}" class="btn btn-primary">
        Create Expense
    </a>
@endcan
```

Or create a helper for cleaner code:

```php
// In app/Helpers/PermissionHelper.php
function canDo(PermissionEnum $permission): bool
{
    return auth()->user()->can($permission->value);
}

function hasRole(RoleEnum $role): bool
{
    return auth()->user()->hasRole($role);
}
```

Then in Blade:
```blade
@if(canDo(PermissionEnum::EXPENSES_CREATE))
    <a href="{{ route('expenses.create') }}" class="btn btn-primary">
        Create Expense
    </a>
@endif
```

## In Controllers

### Checking Permissions
```php
// Controller method
public function index()
{
    $this->authorize('viewAny', Meal::class);
    
    $meals = Meal::paginate(15);
    return view('meals.index', compact('meals'));
}

// With custom responses
public function delete(Meal $meal)
{
    $this->authorize('delete', $meal);
    
    $meal->delete();
    return redirect()->back()->with('success', 'Meal deleted successfully');
}
```

### Checking with Gate
```php
if (Gate::allows('view-dashboard')) {
    // User can view dashboard
}

if (Gate::denies('delete-meal')) {
    return redirect()->back()->with('error', 'Not authorized');
}
```

## Best Practices

1. **Use @can for Permissions** - Check specific actions/permissions
2. **Use @role for Role-Based UI** - Show/hide sections based on roles
3. **Use Policies in Controllers** - Authorize actions in controller methods
4. **Prefer Permissions over Roles** - Use role-based access only when necessary
5. **Be Consistent** - Use same approach throughout the application
6. **Test Permissions** - Create tests for policy rules

## Quick Reference

| Directive | Usage | Example |
|-----------|-------|---------|
| @can | Check permission | `@can('expenses.create')` |
| @cannot | Check permission denied | `@cannot('expenses.delete')` |
| @canany | Check any of permissions | `@canany(['create', 'update'])` |
| @elsecannot | Alternative to cannot | Used with @cannot/@endcannot |
| @role | Check single role | `@role('manager')` |
| @elserole | Alternative role | Used with @role/@endrole |
| @hasrole | Check multiple roles | `@hasrole('manager\|superadmin')` |

## Superadmin Bypass

Thanks to the `Gate::before()` rule configured in `AppServiceProvider`, superadmins bypass all permission checks automatically in Blade directives.

No special code needed - the system handles it transparently!
