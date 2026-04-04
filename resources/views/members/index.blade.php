@extends('layouts.app')

@php
use App\Models\MessUser;
@endphp

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="mb-0">Members of {{ $activeMess->name }}</h3>
                            <small class="text-muted">{{ $activeMess->description }}</small>
                        </div>
                        <span class="badge bg-primary">{{ $activeMess->messUsers()->where('status', 'approved')->count() }} Members</span>
                    </div>
                </div>
                <div class="card-body">
                    @if ($message = Session::get('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <strong>Success!</strong> {{ $message }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @can('members.create')
                        <div class="mb-3">
                            <a href="{{ route('members.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Add New Member
                            </a>
                        </div>
                    @endcan

                    @can('members.view')
                        @if ($members->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>ID</th>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Role</th>
                                            <th>Joined At</th>
                                            @canany(['members.update', 'members.delete', 'members.manage-roles'])
                                                <th>Actions</th>
                                            @endcanany
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($members as $messUser)
                                            @php
                                                $member = $messUser->user;
                                            @endphp
                                            <tr>
                                                <td>{{ $member->id }}</td>
                                                <td>{{ $member->name }}</td>
                                                <td>{{ $member->email }}</td>
                                                <td>
                                                    @forelse($member->roles as $role)
                                                        <span class="badge {{ $role->name === 'manager' ? 'bg-warning' : 'bg-info' }}">
                                                            {{ ucfirst($role->name) }}
                                                        </span>
                                                    @empty
                                                        <span class="badge bg-secondary">No Role</span>
                                                    @endforelse
                                                </td>
                                                <td>{{ $messUser->created_at->format('Y-m-d H:i') }}</td>
                                                @canany(['members.update', 'members.delete', 'members.manage-roles'])
                                                    <td>
                                                        @can('update', $member)
                                                            <a href="{{ route('members.edit', $member) }}" class="btn btn-sm btn-warning" title="Edit">
                                                                <i class="fas fa-edit"></i>
                                                            </a>
                                                        @endcan

                                                        @can('members.manage-roles')
                                                            @if(!$member->hasRole('manager') && $activeMess->manager_id !== $member->id)
                                                                <form action="{{ route('members.change-manager', $member) }}" method="POST" style="display:inline;">
                                                                    @csrf
                                                                    <button type="submit" class="btn btn-sm btn-success" title="Make Manager" 
                                                                            onclick="return confirm('Make {{ $member->name }} the manager of {{ $activeMess->name }}?')">
                                                                        <i class="fas fa-crown"></i>
                                                                    </button>
                                                                </form>
                                                            @else
                                                                <span class="badge bg-warning">Current Manager</span>
                                                            @endif
                                                        @endcan

                                                        @can('delete', $member)
                                                            @if($activeMess->manager_id !== $member->id)
                                                                <form action="{{ route('members.destroy', $member) }}" method="POST" style="display:inline;">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="btn btn-sm btn-danger" title="Remove from Mess" 
                                                                            onclick="return confirm('Remove {{ $member->name }} from {{ $activeMess->name }}?')">
                                                                        <i class="fas fa-trash"></i>
                                                                    </button>
                                                                </form>
                                                            @endif
                                                        @endcan
                                                    </td>
                                                @endcanany
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="mt-3">
                                {{ $members->links() }}
                            </div>
                        @else
                            <div class="alert alert-info">No members found in this mess.
                                @can('members.create')
                                    <a href="{{ route('members.create') }}">Invite members</a>
                                @endcan
                            </div>
                        @endif
                    @else
                        <div class="alert alert-warning">
                            <i class="fas fa-lock"></i> You don't have permission to view members.
                        </div>
                    @endcan
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
