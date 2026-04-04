@extends('layouts.app')

@section('content')
@can('meals.create')
<div class="container mx-auto px-4 py-8">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Bulk Add Meal Records for a Date</h5>
                </div>

                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <strong>Error!</strong> Please check the form below.
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form action="{{ route('meals.store') }}" method="POST">
                        @csrf

                        <!-- Date Selection -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="date" class="form-label">
                                    Select Date <span class="text-danger">*</span>
                                </label>
                                <input type="date" 
                                       id="date" 
                                       name="date" 
                                       class="form-control @error('date') is-invalid @enderror" 
                                       value="{{ old('date') }}" 
                                       required>
                                @error('date')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <div class="alert alert-info mt-4">
                                    <strong>Active Month:</strong> {{ $activeMonth?->name ?? 'No Active Month' }}
                                </div>
                            </div>
                        </div>

                        <!-- Members Meal Selection Table -->
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 25%;">Member Name</th>
                                        <th style="width: 25%;" class="text-center">
                                            <strong>Breakfast</strong>
                                        </th>
                                        <th style="width: 25%;" class="text-center">
                                            <strong>Lunch</strong>
                                        </th>
                                        <th style="width: 25%;" class="text-center">
                                            <strong>Dinner</strong>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($members as $member)
                                    <tr>
                                            <td>
                                                <strong>{{ $member->name }}</strong>
                                                <small class="text-muted d-block">{{ $member->email }}</small>
                                            </td>
                                            <td class="text-center">
                                                <div class="input-group input-group-sm" style="width: 130px; margin: 0 auto;">
                                                    <button type="button" class="btn btn-outline-secondary" onclick="decrementMeal('breakfast_{{ $member->id }}')">
                                                        <i class="fas fa-minus"></i>
                                                    </button>
                                                    <input 
                                                        type="number"
                                                        class="form-control text-center"
                                                        id="breakfast_{{ $member->id }}"
                                                        name="meals[{{ $member->id }}][breakfast_count]" 
                                                        value="{{ old("meals.{$member->id}.breakfast_count", 0) }}"
                                                        step="0.5"
                                                        min="0"
                                                        placeholder="0">
                                                    <button type="button" class="btn btn-outline-secondary" onclick="incrementMeal('breakfast_{{ $member->id }}')">
                                                        <i class="fas fa-plus"></i>
                                                    </button>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <div class="input-group input-group-sm" style="width: 130px; margin: 0 auto;">
                                                    <button type="button" class="btn btn-outline-secondary" onclick="decrementMeal('lunch_{{ $member->id }}')">
                                                        <i class="fas fa-minus"></i>
                                                    </button>
                                                    <input 
                                                        type="number"
                                                        class="form-control text-center"
                                                        id="lunch_{{ $member->id }}"
                                                        name="meals[{{ $member->id }}][lunch_count]" 
                                                        value="{{ old("meals.{$member->id}.lunch_count", 0) }}"
                                                        step="0.5"
                                                        min="0"
                                                        placeholder="0">
                                                    <button type="button" class="btn btn-outline-secondary" onclick="incrementMeal('lunch_{{ $member->id }}')">
                                                        <i class="fas fa-plus"></i>
                                                    </button>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <div class="input-group input-group-sm" style="width: 130px; margin: 0 auto;">
                                                    <button type="button" class="btn btn-outline-secondary" onclick="decrementMeal('dinner_{{ $member->id }}')">
                                                        <i class="fas fa-minus"></i>
                                                    </button>
                                                    <input 
                                                        type="number"
                                                        class="form-control text-center"
                                                        id="dinner_{{ $member->id }}"
                                                        name="meals[{{ $member->id }}][dinner_count]" 
                                                        value="{{ old("meals.{$member->id}.dinner_count", 0) }}"
                                                        step="0.5"
                                                        min="0"
                                                        placeholder="0">
                                                    <button type="button" class="btn btn-outline-secondary" onclick="incrementMeal('dinner_{{ $member->id }}')">
                                                        <i class="fas fa-plus"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center text-muted py-4">
                                                No members found. Please add members first.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('meals.index') }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Add Meal Records
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function incrementMeal(fieldId) {
    const input = document.getElementById(fieldId);
    let currentValue = parseFloat(input.value) || 0;
    input.value = (currentValue + 0.5).toFixed(1);
}

function decrementMeal(fieldId) {
    const input = document.getElementById(fieldId);
    let currentValue = parseFloat(input.value) || 0;
    if (currentValue > 0) {
        input.value = (currentValue - 0.5).toFixed(1);
    }
}
</script>
@else
    <div class="container mx-auto px-4 py-8">
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Access Denied!</strong> You don't have permission to create meals.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <a href="{{ route('meals.index') }}" class="btn btn-secondary">Back to Meals</a>
    </div>
@endcan
@endsection
