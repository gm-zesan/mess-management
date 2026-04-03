@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="row justify-content-between align-items-center mb-4">
        <div class="col-md-6">
            <h2>Deposits</h2>
            <small class="text-muted">{{ $activeMonth?->name ?? 'No Active Month' }}</small>
        </div>
        <div class="col-md-6 text-end">
            @can('deposits.create')
                <a href="{{ route('deposits.create') }}" class="btn btn-primary">+ Add Deposit</a>
            @endcan
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @can('deposits.view')
        @if ($deposits->count())
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>Date</th>
                            <th>Member</th>
                            <th>Month</th>
                            <th>Amount</th>
                            @canany(['deposits.update', 'deposits.delete'])
                                <th>Actions</th>
                            @endcanany
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($deposits as $deposit)
                            <tr>
                                <td>{{ $deposit->date->format('M d, Y') }}</td>
                                <td>{{ $deposit->user->name }}</td>
                                <td>{{ $deposit->month->name }}</td>
                                <td class="text-end">৳ {{ number_format($deposit->amount, 2) }}</td>
                                @canany(['deposits.update', 'deposits.delete'])
                                    <td>
                                        @can('update', $deposit)
                                            <a href="{{ route('deposits.edit', $deposit) }}" class="btn btn-sm btn-warning">Edit</a>
                                        @endcan
                                        @can('delete', $deposit)
                                            <form action="{{ route('deposits.destroy', $deposit) }}" method="POST" style="display: inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                                            </form>
                                        @endcan
                                    </td>
                                @endcanany
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-center">
                {{ $deposits->links() }}
            </div>
        @else
            <div class="alert alert-info">
                No deposits found.
                @can('deposits.create')
                    <a href="{{ route('deposits.create') }}">Create one</a>
                @endcan
            </div>
        @endif
    @else
        <div class="alert alert-warning">
            <i class="fas fa-lock"></i> You don't have permission to view deposits.
        </div>
    @endcan
</div>
@endsection
