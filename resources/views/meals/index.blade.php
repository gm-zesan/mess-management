@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="row justify-content-between align-items-center mb-4">
        <div class="col">
            <h2 class="h2">Daily Meal Records</h2>
            <small class="text-muted">{{ $activeMonth?->name ?? 'No Active Month' }}</small>
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

    <!-- Filter Section -->
    <div class="card mb-3">
        <div class="card-header bg-light">
            <h6 class="mb-0">
                <i class="fas fa-filter"></i> Filter Meals
            </h6>
        </div>
        <div class="card-body">
            <form action="{{ route('meals.index') }}" method="GET" class="row g-3">
                <div class="col-md-4">
                    <label for="filter_date" class="form-label">Filter by Date</label>
                    <input 
                        type="date" 
                        id="filter_date" 
                        name="filter_date" 
                        class="form-control"
                        value="{{ $filterDate ?? '' }}">
                </div>

                <div class="col-md-4">
                    <label for="filter_member" class="form-label">Filter by Member</label>
                    <select id="filter_member" name="filter_member" class="form-select">
                        <option value="">-- All Members --</option>
                        @foreach ($members as $member)
                            <option value="{{ $member->id }}" {{ $filterMember == $member->id ? 'selected' : '' }}>
                                {{ $member->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i> Filter
                    </button>
                    <a href="{{ route('meals.index') }}" class="btn btn-secondary">
                        <i class="fas fa-redo"></i> Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    @can('meals.view')
        <!-- Active Filters Display -->
        @if ($filterDate || $filterMember)
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                <strong>Active Filters:</strong>
                @if ($filterDate)
                    <span class="badge bg-info">Date: {{ \Carbon\Carbon::parse($filterDate)->format('M d, Y') }}</span>
                @endif
                @if ($filterMember)
                    <span class="badge bg-info">Member: {{ $members->find($filterMember)?->name }}</span>
                @endif
                <a href="{{ route('meals.index') }}" class="alert-link ms-2">Clear all filters</a>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Member Name</th>
                                <th>Date</th>
                                <th>Breakfast</th>
                                <th>Lunch</th>
                                <th>Dinner</th>
                                <th>Total</th>
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
                                    <td>
                                        <small class="text-muted">{{ $meal->date->format('M d, Y') }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-success">
                                            {{ $meal->breakfast_count > 0 ? $meal->breakfast_count : '-' }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">
                                            {{ $meal->lunch_count > 0 ? $meal->lunch_count : '-' }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-warning text-dark">
                                            {{ $meal->dinner_count > 0 ? $meal->dinner_count : '-' }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary">{{ $meal->total_meal_count }}</span>
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
                                    <td colspan="7" class="text-center text-muted py-4">
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
                    {{ $meals->appends(request()->query())->links() }}
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
