@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="row justify-content-between align-items-center mb-4">
        <div class="col-md-6">
            <h2>Expenses</h2>
        </div>
        <div class="col-md-6 text-end">
            <a href="{{ route('expenses.create') }}" class="btn btn-primary">+ Add Expense</a>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if ($expenses->count())
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>Date</th>
                        <th>Month</th>
                        <th>Category</th>
                        <th>Amount</th>
                        <th>Note</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($expenses as $expense)
                        <tr>
                            <td>{{ $expense->date->format('M d, Y') }}</td>
                            <td>{{ $expense->month->name }}</td>
                            <td>{{ $expense->category }}</td>
                            <td class="text-end">৳ {{ number_format($expense->amount, 2) }}</td>
                            <td>{{ Str::limit($expense->note, 30) }}</td>
                            <td>
                                <a href="{{ route('expenses.show', $expense) }}" class="btn btn-sm btn-info">View</a>
                                <a href="{{ route('expenses.edit', $expense) }}" class="btn btn-sm btn-warning">Edit</a>
                                <form action="{{ route('expenses.destroy', $expense) }}" method="POST" style="display: inline;">
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
            {{ $expenses->links() }}
        </div>
    @else
        <div class="alert alert-info">
            No expenses found. <a href="{{ route('expenses.create') }}">Create one</a>
        </div>
    @endif
</div>
@endsection
