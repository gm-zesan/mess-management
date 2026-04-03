@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="row mb-4">
        <div class="col-md-12">
            <h1 class="mb-4">Profile Settings</h1>
        </div>
    </div>

    @if (session('status') === 'profile-updated')
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ __('Profile updated successfully.') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-md-8">
            <!-- Update Profile Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">{{ __('Profile Information') }}</h5>
                </div>
                <div class="card-body">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <!-- Update Password -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">{{ __('Update Password') }}</h5>
                </div>
                <div class="card-body">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <!-- Delete Account -->
            <div class="card mb-4">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0">{{ __('Delete Account') }}</h5>
                </div>
                <div class="card-body">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
