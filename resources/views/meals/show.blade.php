@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">Meal Record Details</h5>
                </div>

                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-muted">Member</label>
                                <p class="form-control-plaintext">
                                    <span class="badge bg-primary">{{ $meal->member->name }}</span>
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-muted">Month</label>
                                <p class="form-control-plaintext">{{ $meal->month->name }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-muted">Date</label>
                                <p class="form-control-plaintext">{{ $meal->date->format('F d, Y') }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-muted">Meal Count</label>
                                <p class="form-control-plaintext">
                                    <span class="badge bg-info">{{ $meal->meal_count }}</span>
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label text-muted">Created</label>
                        <p class="form-control-plaintext">{{ $meal->created_at->format('M d, Y \a\t H:i') }}</p>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('meals.index') }}" class="btn btn-secondary">Back to List</a>
                        <div>
                            <a href="{{ route('meals.edit', $meal) }}" class="btn btn-warning">Edit</a>
                            <form action="{{ route('meals.destroy', $meal) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">Delete</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
