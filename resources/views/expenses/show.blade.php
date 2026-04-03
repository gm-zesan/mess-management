@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">Expense Details</h5>
                </div>

                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label"><strong>Month:</strong></label>
                            <p>{{ $expense->month->name }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label"><strong>Who Spent:</strong></label>
                            <p>{{ $expense->user?->name ?? 'N/A' }}</p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label"><strong>Date:</strong></label>
                            <p>{{ $expense->date->format('M d, Y') }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label"><strong>Amount:</strong></label>
                            <p>৳ {{ number_format($expense->amount, 2) }}</p>
                        </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label"><strong>Category:</strong></label>
                            <p>{{ $expense->category }}</p>
                        </div>
                    </div>
                        <div class="mb-3">
                            <label class="form-label"><strong>Note:</strong></label>
                            <p>{{ $expense->note }}</p>
                        </div>
                    @endif

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('expenses.index') }}" class="btn btn-secondary">Back</a>
                        <div>
                            <a href="{{ route('expenses.edit', $expense) }}" class="btn btn-warning">Edit</a>
                            <form action="{{ route('expenses.destroy', $expense) }}" method="POST" style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
