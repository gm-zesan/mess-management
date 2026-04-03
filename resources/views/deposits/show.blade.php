@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">Deposit Details</h5>
                </div>

                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label"><strong>Member:</strong></label>
                            <p>{{ $deposit->user->name }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label"><strong>Month:</strong></label>
                            <p>{{ $deposit->month->name }}</p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label"><strong>Date:</strong></label>
                            <p>{{ $deposit->date->format('M d, Y') }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label"><strong>Amount:</strong></label>
                            <p>৳ {{ number_format($deposit->amount, 2) }}</p>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('deposits.index') }}" class="btn btn-secondary">Back</a>
                        <div>
                            <a href="{{ route('deposits.edit', $deposit) }}" class="btn btn-warning">Edit</a>
                            <form action="{{ route('deposits.destroy', $deposit) }}" method="POST" style="display: inline;">
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
