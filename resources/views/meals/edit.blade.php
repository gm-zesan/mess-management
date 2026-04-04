@extends('layouts.app')

@section('content')
@can('update', $meal)
<div class="container mx-auto px-4 py-8">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0">Edit Meal Record</h5>
                </div>

                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <strong>Error!</strong> Please check the form below.
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form action="{{ route('meals.update', $meal) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="user_id" class="form-label">Member <span class="text-danger">*</span></label>
                            <select id="user_id" name="user_id" class="form-select @error('user_id') is-invalid @enderror">
                                <option value="">Select a member</option>
                                @foreach ($members as $member)
                                    <option value="{{ $member->id }}" {{ old('user_id', $meal->user_id) == $member->id ? 'selected' : '' }}>
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
                                <div class="alert alert-info mb-3">Current month: <strong>{{ $meal->month->name }}</strong></div>
                                <div class="form-control bg-light" disabled>
                                    {{ $meal->month->name }}
                                </div>
                            @else
                                <div class="alert alert-warning mb-3">No active month found.</div>
                            @endif
                            @error('month_id')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="date" class="form-label">Date <span class="text-danger">*</span></label>
                            <input type="date" id="date" name="date" class="form-control @error('date') is-invalid @enderror" 
                                   value="{{ old('date', $meal->date->format('Y-m-d')) }}" required>
                            @error('date')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="breakfast_count" class="form-label">Breakfast <span class="text-danger">*</span></label>
                                    <input type="number" 
                                           id="breakfast_count" 
                                           name="breakfast_count" 
                                           class="form-control @error('breakfast_count') is-invalid @enderror"
                                           value="{{ old('breakfast_count', $meal->breakfast_count) }}"
                                           step="0.5"
                                           min="0">
                                    @error('breakfast_count')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="lunch_count" class="form-label">Lunch <span class="text-danger">*</span></label>
                                    <input type="number" 
                                           id="lunch_count" 
                                           name="lunch_count" 
                                           class="form-control @error('lunch_count') is-invalid @enderror"
                                           value="{{ old('lunch_count', $meal->lunch_count) }}"
                                           step="0.5"
                                           min="0">
                                    @error('lunch_count')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="dinner_count" class="form-label">Dinner <span class="text-danger">*</span></label>
                                    <input type="number" 
                                           id="dinner_count" 
                                           name="dinner_count" 
                                           class="form-control @error('dinner_count') is-invalid @enderror"
                                           value="{{ old('dinner_count', $meal->dinner_count) }}"
                                           step="0.5"
                                           min="0">
                                    @error('dinner_count')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('meals.index') }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-warning">Update Meal Record</button>
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
            <strong>Access Denied!</strong> You don't have permission to edit this meal.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <a href="{{ route('meals.index') }}" class="btn btn-secondary">Back to Meals</a>
    </div>
@endcan
@endsection
