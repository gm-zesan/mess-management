@extends('layouts.app')

@section('content')
@can('update', $expense)
<div class="container mx-auto px-4 py-8">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0">Edit Expense</h5>
                </div>

                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <strong>Error!</strong> Please check the form below.
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form action="{{ route('expenses.update', $expense) }}" method="POST">
                        @csrf
                        @method('PUT')

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
                            <label for="user_id" class="form-label">Select Member <span class="text-danger">*</span></label>
                            <select id="user_id" name="user_id" class="form-control @error('user_id') is-invalid @enderror" required>
                                <option value="">-- Select Member --</option>
                                @foreach ($members ?? [] as $member)
                                    <option value="{{ $member->id }}" {{ old('user_id', $expense->user_id) == $member->id ? 'selected' : '' }}>
                                        {{ $member->name }} ({{ $member->email }})
                                    </option>
                                @endforeach
                            </select>
                            @error('user_id')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="category" class="form-label">Category <span class="text-danger">*</span></label>
                            <select id="category" name="category" class="form-control @error('category') is-invalid @enderror" required>
                                <option value="">-- Select Category --</option>
                                <option value="meal" {{ old('category', $expense->category) === 'meal' ? 'selected' : '' }}>Meal</option>
                                <option value="utility" {{ old('category', $expense->category) === 'utility' ? 'selected' : '' }}>Utility</option>
                            </select>
                            @error('category')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="amount" class="form-label">Amount (৳) <span class="text-danger">*</span></label>
                            <input type="number" id="amount" name="amount" class="form-control @error('amount') is-invalid @enderror" 
                                   value="{{ old('amount', $expense->amount) }}" step="0.01" min="0" placeholder="0.00" required>
                            @error('amount')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="date" class="form-label">Date <span class="text-danger">*</span></label>
                            <input type="date" id="date" name="date" class="form-control @error('date') is-invalid @enderror" 
                                   value="{{ old('date', $expense->date->format('Y-m-d')) }}" required>
                            @error('date')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="note" class="form-label">Note</label>
                            <textarea id="note" name="note" class="form-control @error('note') is-invalid @enderror" 
                                      rows="3" placeholder="Optional notes about this expense">{{ old('note', $expense->note) }}</textarea>
                            @error('note')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('expenses.index') }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-warning">Update Expense</button>
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
            <strong>Access Denied!</strong> You don't have permission to edit this expense.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <a href="{{ route('expenses.index') }}" class="btn btn-secondary">Back to Expenses</a>
    </div>
@endcan
@endsection
