@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-danger">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0">🔒 Month Closed</h5>
                </div>
                <div class="card-body text-center py-5">
                    <h2 class="text-danger mb-3">
                        <i class="fa-solid fa-lock"></i> Access Denied
                    </h2>
                    <p class="lead text-muted mb-4">
                        This month is closed. No further modifications are allowed.
                    </p>
                    <p class="text-muted mb-4">
                        If you need to make changes, please contact your administrator.
                    </p>
                    <a href="{{ route('months.index') }}" class="btn btn-secondary">
                        <i class="fa-solid fa-arrow-left"></i> Back to Months
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
