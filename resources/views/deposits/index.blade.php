@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="row justify-content-between align-items-center mb-4">
        <div class="col-md-6">
            <h2>Deposits</h2>
        </div>
        <div class="col-md-6 text-end">
            <a href="{{ route('deposits.create') }}" class="btn btn-primary">+ Add Deposit</a>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if ($deposits->count())
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>Date</th>
                        <th>Member</th>
                        <th>Month</th>
                        <th>Amount</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($deposits as $deposit)
                        <tr>
                            <td>{{ $deposit->date->format('M d, Y') }}</td>
                            <td>{{ $deposit->member->name }}</td>
                            <td>{{ $deposit->month->name }}</td>
                            <td class="text-end">৳ {{ number_format($deposit->amount, 2) }}</td>
                            <td>
                                <a href="{{ route('deposits.show', $deposit) }}" class="btn btn-sm btn-info">View</a>
                                <a href="{{ route('deposits.edit', $deposit) }}" class="btn btn-sm btn-warning">Edit</a>
                                <form action="{{ route('deposits.destroy', $deposit) }}" method="POST" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                                </form>
                            </td>
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
            No deposits found. <a href="{{ route('deposits.create') }}">Create one</a>
        </div>
    @endif
</div>
@endsection
