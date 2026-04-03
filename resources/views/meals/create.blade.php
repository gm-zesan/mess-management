@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Add Daily Meal Record</h5>
                </div>

                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <strong>Error!</strong> Please check the form below.
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form action="{{ route('meals.store') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label for="user_id" class="form-label">Member <span class="text-danger">*</span></label>
                            <select id="user_id" name="user_id" class="form-select @error('user_id') is-invalid @enderror">
                                <option value="">Select a member</option>
                                @foreach ($members as $member)
                                    <option value="{{ $member->id }}" {{ old('user_id') == $member->id ? 'selected' : '' }}>
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
                            <label for="date" class="form-label">Date <span class="text-danger">*</span></label>
                            <input type="date" id="date" name="date" class="form-control @error('date') is-invalid @enderror" 
                                   value="{{ old('date') }}" required>
                            @error('date')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="meal_count" class="form-label">Meal Count <span class="text-danger">*</span></label>
                            <select id="meal_count" name="meal_count" class="form-select @error('meal_count') is-invalid @enderror">
                                <option value="">Select meal count</option>
                                <option value="0" {{ old('meal_count') == '0' ? 'selected' : '' }}>0 (No meal)</option>
                                <option value="1" {{ old('meal_count') == '1' ? 'selected' : '' }}>1 (Half day)</option>
                                <option value="2" {{ old('meal_count') == '2' ? 'selected' : '' }}>2 (Full day)</option>
                                <option value="3" {{ old('meal_count') == '3' ? 'selected' : '' }}>3 (Full day + extra)</option>
                            </select>
                            @error('meal_count')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('meals.index') }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">Add Meal Record</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
