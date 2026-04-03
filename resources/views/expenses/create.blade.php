@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Add Expense</h5>
                </div>

                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <strong>Error!</strong> Please check the form below.
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form action="{{ route('expenses.store') }}" method="POST">
                        @csrf

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
                            <label for="category" class="form-label">Category <span class="text-danger">*</span></label>
                            <select id="category" name="category" class="form-control @error('category') is-invalid @enderror" required>
                                <option value="">-- Select Category --</option>
                                <option value="meal" {{ old('category') === 'meal' ? 'selected' : '' }}>Meal</option>
                                <option value="utility" {{ old('category') === 'utility' ? 'selected' : '' }}>Utility</option>
                            </select>
                            @error('category')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="amount" class="form-label">Amount (৳) <span class="text-danger">*</span></label>
                            <input type="number" id="amount" name="amount" class="form-control @error('amount') is-invalid @enderror" 
                                   value="{{ old('amount') }}" step="0.01" min="0" placeholder="0.00" required>
                            @error('amount')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="date" class="form-label">Date <span class="text-danger">*</span></label>
                            <input type="date" id="date" name="date" class="form-control @error('date') is-invalid @enderror" 
                                   value="{{ old('date') }}" required>
                            @error('date')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="note" class="form-label">Note</label>
                            <textarea id="note" name="note" class="form-control @error('note') is-invalid @enderror" 
                                      rows="3" placeholder="Optional notes about this expense">{{ old('note') }}</textarea>
                            @error('note')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('expenses.index') }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">Add Expense</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
