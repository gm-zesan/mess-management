@extends('layouts.app')

@php
use App\Models\User;
@endphp

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="mb-0">Members</h3>
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
                                            <th>Created At</th>
                                            @canany(['members.update', 'members.delete', 'members.manage-roles'])
                                                <th>Actions</th>
                                            @endcanany
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($members as $member)
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
                                                <td>{{ $member->created_at->format('Y-m-d H:i') }}</td>
                                                @canany(['members.update', 'members.delete', 'members.manage-roles'])
                                                    <td>
                                                        @can('update', $member)
                                                            <a href="{{ route('members.edit', $member) }}" class="btn btn-sm btn-warning" title="Edit">
                                                                <i class="fas fa-edit"></i>
                                                            </a>
                                                        @endcan

                                                        @can('members.manage-roles')
                                                            @if(!$member->hasRole('manager'))
                                                                <form action="{{ route('members.change-manager', $member) }}" method="POST" style="display:inline;">
                                                                    @csrf
                                                                    <button type="submit" class="btn btn-sm btn-success" title="Make Manager" 
                                                                            onclick="return confirm('Make {{ $member->name }} the manager?')">
                                                                        <i class="fas fa-crown"></i>
                                                                    </button>
                                                                </form>
                                                            @else
                                                                <span class="badge bg-warning">Current Manager</span>
                                                            @endif
                                                        @endcan

                                                        @can('delete', $member)
                                                            <form action="{{ route('members.destroy', $member) }}" method="POST" style="display:inline;">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-sm btn-danger" title="Delete" 
                                                                        onclick="return confirm('Are you sure?')">
                                                                    <i class="fas fa-trash"></i>
                                                                </button>
                                                            </form>
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
                            <div class="alert alert-info">No members found. 
                                @can('members.create')
                                    <a href="{{ route('members.create') }}">Create one now</a>
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
