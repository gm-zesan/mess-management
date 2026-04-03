@extends('layouts.app')

@section('content')
@can('update', $deposit)
<div class="container mx-auto px-4 py-8">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0">Edit Deposit</h5>
                </div>

                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <strong>Error!</strong> Please check the form below.
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form action="{{ route('deposits.update', $deposit) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="user_id" class="form-label">Member <span class="text-danger">*</span></label>
                            <select id="user_id" name="user_id" class="form-select @error('user_id') is-invalid @enderror">
                                <option value="">Select a member</option>
                                @foreach ($members as $member)
                                    <option value="{{ $member->id }}" {{ old('user_id', $deposit->user_id) == $member->id ? 'selected' : '' }}>
                                        {{ $member->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('user_id')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="month_id" class="form-label">Month <span class="text-danger">*</span></label>
                            @if ($activeMonth)
                                <div class="alert alert-info mb-3">Current active month: <strong>{{ $activeMonth->name }}</strong></div>
                                <input type="hidden" name="month_id" value="{{ $activeMonth->id }}">
                                <div class="form-control bg-light" disabled>
                                    {{ $activeMonth->name }}
                                </div>
                            @else
                                <div class="alert alert-warning mb-3">No active month found. Please create one first.</div>
                                <input type="hidden" name="month_id" value="">
                            @endif
                            @error('month_id')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="amount" class="form-label">Amount (৳) <span class="text-danger">*</span></label>
                            <input type="number" id="amount" name="amount" class="form-control @error('amount') is-invalid @enderror" 
                                   value="{{ old('amount', $deposit->amount) }}" step="0.01" min="0" placeholder="0.00" required>
                            @error('amount')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="date" class="form-label">Date <span class="text-danger">*</span></label>
                            <input type="date" id="date" name="date" class="form-control @error('date') is-invalid @enderror" 
                                   value="{{ old('date', $deposit->date->format('Y-m-d')) }}" required>
                            @error('date')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('deposits.index') }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-warning">Update Deposit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@else
    <div class="container mx-auto px-4 py-8">
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Access Denied!</strong> You don't have permission to edit this deposit.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <a href="{{ route('deposits.index') }}" class="btn btn-secondary">Back to Deposits</a>
    </div>
@endcan
@endsection
