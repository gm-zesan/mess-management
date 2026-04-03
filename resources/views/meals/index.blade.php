@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Daily Meal Records</h1>
        <a href="{{ route('meals.create') }}" class="btn btn-primary">
            Add Meal Record
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
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
                            <th>Month</th>
                            <th>Date</th>
                            <th>Meal Count</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($meals as $meal)
                            <tr>
                                <td>
                                    <span class="badge bg-primary">{{ $meal->member->name }}</span>
                                </td>
                                <td>{{ $meal->month->name }}</td>
                                <td>
                                    <small class="text-muted">{{ $meal->date->format('M d, Y') }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-info">{{ $meal->meal_count }}</span>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="{{ route('meals.show', $meal) }}" class="btn btn-info btn-sm" title="View">
                                            <i class="fa-solid fa-eye"></i>
                                        </a>
                                        <a href="{{ route('meals.edit', $meal) }}" class="btn btn-warning btn-sm" title="Edit">
                                            <i class="fa-solid fa-pencil"></i>
                                        </a>
                                        <form action="{{ route('meals.destroy', $meal) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" title="Delete">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">
                                    No meal records found. <a href="{{ route('meals.create') }}">Create one</a>
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
</div>
@endsection
