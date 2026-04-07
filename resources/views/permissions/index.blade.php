@extends('layouts.app')

@section('content')
<div class="w-full">
    <!-- Status Messages -->
    @if ($message = Session::get('success'))
        <div class="mb-4 p-3 bg-green-50 border border-green-200 rounded-lg flex items-center gap-3">
            <svg class="w-4 h-4 text-green-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
            </svg>
            <span class="text-xs text-green-800">{{ $message }}</span>
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg flex items-start gap-3">
            <svg class="w-4 h-4 text-red-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
            </svg>
            <div class="text-xs text-red-700">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Manage Permissions Form -->
    <div class="bg-white rounded-lg border border-gray-200 shadow-xs overflow-hidden mb-4">
        <div class="px-4 py-3 bg-gray-50 border-b border-gray-200">
            <h2 class="text-base font-semibold text-gray-900">Assign/Revoke Permissions</h2>
            <p class="text-xs text-gray-600 mt-0.5">Select a role and permission to manage access</p>
        </div>

        <div class="p-5">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                <!-- Role Select -->
                <div>
                    <label for="roleSelect" class="text-xs font-semibold text-gray-900">
                        Role
                    </label>
                    <select id="roleSelect" class="mt-1 block w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-sky-500 focus:border-transparent transition-all bg-white">
                        <option value="">-- Select Role --</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}">{{ ucfirst($role->name) }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Permission Select -->
                <div>
                    <label for="permissionSelect" class="text-xs font-semibold text-gray-900">
                        Permission
                    </label>
                    <select id="permissionSelect" class="mt-1 block w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-sky-500 focus:border-transparent transition-all bg-white">
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

                <!-- Action Buttons -->
                <div class="flex flex-col gap-2 justify-end">
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
                    
                    <button 
                        type="button" 
                        onclick="assignPermission()" 
                        class="px-3 py-1.5 bg-sky-600 hover:bg-sky-700 text-white text-xs font-semibold rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-sky-500 focus:ring-offset-2"
                    >
                        Assign Permission
                    </button>
                    <button 
                        type="button" 
                        onclick="revokePermission()" 
                        class="px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white text-xs font-semibold rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2"
                    >
                        Revoke Permission
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Roles and Permissions List -->
    <div class="bg-white rounded-lg border border-gray-200 shadow-xs overflow-hidden">
        <div class="px-4 py-3 bg-gray-50 border-b border-gray-200">
            <h2 class="text-base font-semibold text-gray-900">Current Permissions by Role</h2>
        </div>

        <div class="p-5">
            <div class="space-y-4">
                @foreach($roles as $role)
                    <div class="border border-gray-200 rounded-lg p-4 hover:border-gray-300 transition-colors">
                        <h3 class="text-sm font-semibold text-gray-900 mb-3">
                            {{ ucfirst($role->name) }}
                        </h3>
                        
                        @if($role->permissions->count() > 0)
                            <div class="flex flex-wrap gap-2">
                                @foreach($role->permissions as $permission)
                                    <span class="px-3 py-1.5 bg-sky-100 text-sky-700 text-xs font-semibold rounded-full border border-sky-200 hover:bg-sky-200 transition-colors">
                                        {{ $permission->name }}
                                    </span>
                                @endforeach
                            </div>
                        @else
                            <div class="p-3 bg-gray-50 border border-gray-200 rounded-lg">
                                <p class="text-xs text-gray-600">No permissions assigned</p>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
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

