@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="row justify-content-between align-items-center mb-4">
        <div class="col">
            <h2 class="h2">Daily Meal Records</h2>
        </div>
        <div class="col-auto">
            @can('meals.create')
                <a href="{{ route('meals.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add Meal Record
                </a>
            @endcan
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @can('meals.view')
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Member Name</th>
                                <th>Month</th>
                                <th>Date</th>
                                <th>Meal Count</th>
                                @canany(['meals.update', 'meals.delete'])
                                    <th>Actions</th>
                                @endcanany
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($meals as $meal)
                                <tr>
                                    <td>
                                        <span class="badge bg-primary">{{ $meal->user->name }}</span>
                                    </td>
                                    <td>{{ $meal->month->name }}</td>
                                    <td>
                                        <small class="text-muted">{{ $meal->date->format('M d, Y') }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ $meal->meal_count }}</span>
                                    </td>
                                    @canany(['meals.update', 'meals.delete'])
                                        <td>
                                            <div class="btn-group btn-group-sm" role="group">
                                                @can('update', $meal)
                                                    <a href="{{ route('meals.edit', $meal) }}" class="btn btn-warning btn-sm" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                @endcan

                                                @can('delete', $meal)
                                                    <form action="{{ route('meals.destroy', $meal) }}" method="POST" style="display: inline;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger btn-sm" title="Delete" 
                                                                onclick="return confirm('Are you sure you want to delete this meal record?');">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                @endcan
                                            </div>
                                        </td>
                                    @endcanany
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">
                                        No meal records found. 
                                        @can('meals.create')
                                            <a href="{{ route('meals.create') }}">Create one</a>
                                        @endcan
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-center mt-4">
                    {{ $meals->links() }}
                </div>
            </div>
        </div>
    @else
        <div class="alert alert-warning">
            <i class="fas fa-lock"></i> You don't have permission to view meals.
        </div>
    @endcan
</div>
@endsection
