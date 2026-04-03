@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-6">Permission Management</h1>

    @if ($message = Session::get('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ $message }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        @foreach ($errors->all() as $error)
            <div>{{ $error }}</div>
        @endforeach
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <!-- Manage Permissions Form -->
    <div class="card mb-6">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Assign/Revoke Permissions</h5>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="form-label"><strong>Role</strong></label>
                    <select id="roleSelect" class="form-select">
                        <option value="">-- Select Role --</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}">{{ ucfirst($role->name) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label"><strong>Permission</strong></label>
                    <select id="permissionSelect" class="form-select">
                        <option value="">-- Select Permission --</option>
                        @foreach($allPermissions as $module => $permissions)
                            <optgroup label="{{ ucfirst($module) }}">
                                @foreach($permissions as $permission)
                                    <option value="{{ $permission->id }}">{{ $permission->name }}</option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 d-flex align-items-end gap-2">
                    <form id="assignForm" action="{{ route('permissions.assign') }}" method="POST" style="display: none;">
                        @csrf
                        <input type="hidden" name="role_id" id="assignRoleId">
                        <input type="hidden" name="permission_id" id="assignPermissionId">
                    </form>
                    <form id="revokeForm" action="{{ route('permissions.revoke') }}" method="POST" style="display: none;">
                        @csrf
                        <input type="hidden" name="role_id" id="revokeRoleId">
                        <input type="hidden" name="permission_id" id="revokePermissionId">
                    </form>
                    <button type="button" class="btn btn-success" onclick="assignPermission()">Assign</button>
                    <button type="button" class="btn btn-warning" onclick="revokePermission()">Revoke</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Roles and Permissions List -->
    <div class="card">
        <div class="card-header bg-secondary text-white">
            <h5 class="mb-0">Current Permissions by Role</h5>
        </div>
        <div class="card-body">
            @foreach($roles as $role)
            <div class="mb-4 p-3 border rounded">
                <h6 class="font-bold mb-2">{{ ucfirst($role->name) }}</h6>
                @if($role->permissions->count() > 0)
                    <div class="badge-list">
                        @foreach($role->permissions as $permission)
                            <span class="badge bg-info me-1 mb-1">{{ $permission->name }}</span>
                        @endforeach
                    </div>
                @else
                    <span class="text-muted">No permissions assigned</span>
                @endif
            </div>
            @endforeach
        </div>
    </div>
</div>

<script>
function assignPermission() {
    const roleId = document.getElementById('roleSelect').value;
    const permissionId = document.getElementById('permissionSelect').value;
    
    if (!roleId || !permissionId) {
        alert('Please select both Role and Permission');
        return;
    }
    
    document.getElementById('assignRoleId').value = roleId;
    document.getElementById('assignPermissionId').value = permissionId;
    document.getElementById('assignForm').submit();
}

function revokePermission() {
    const roleId = document.getElementById('roleSelect').value;
    const permissionId = document.getElementById('permissionSelect').value;
    
    if (!roleId || !permissionId) {
        alert('Please select both Role and Permission');
        return;
    }
    
    if (!confirm('Are you sure you want to revoke this permission?')) {
        return;
    }
    
    document.getElementById('revokeRoleId').value = roleId;
    document.getElementById('revokePermissionId').value = permissionId;
    document.getElementById('revokeForm').submit();
}
</script>
@endsection

