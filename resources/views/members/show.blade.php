@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="mb-0">Member Details</h3>
                </div>
                <div class="card-body">
                    @if ($message = Session::get('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <strong>Success!</strong> {{ $message }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <div class="mb-3">
                        <label class="form-label fw-bold">ID:</label>
                        <p class="form-control-plaintext">{{ $member->id }}</p>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Name:</label>
                        <p class="form-control-plaintext">{{ $member->name }}</p>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Email:</label>
                        <p class="form-control-plaintext">{{ $member->email }}</p>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Created At:</label>
                        <p class="form-control-plaintext">{{ $member->created_at->format('Y-m-d H:i:s') }}</p>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Updated At:</label>
                        <p class="form-control-plaintext">{{ $member->updated_at->format('Y-m-d H:i:s') }}</p>
                    </div>

                    <div class="d-flex gap-2">
                        <a href="{{ route('members.edit', $member) }}" class="btn btn-warning">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <a href="{{ route('members.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
                        <a href="{{ route('members.edit', $member) }}" class="btn btn-warning">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <a href="{{ route('members.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </a>
                        <form action="{{ route('members.destroy', $member) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure?')">
                                <i class="fas fa-trash"></i> Delete
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
