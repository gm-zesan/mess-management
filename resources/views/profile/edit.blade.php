@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Status Messages -->
    @if (session('status') === 'profile-updated')
        <div class="mb-4 p-3 bg-green-50 border border-green-200 rounded-lg flex items-center gap-3">
            <svg class="w-4 h-4 text-green-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
            </svg>
            <span class="text-xs text-green-800">{{ __('Profile updated successfully.') }}</span>
        </div>
    @endif

    @if (session('status') === 'password-updated')
        <div class="mb-4 p-3 bg-green-50 border border-green-200 rounded-lg flex items-center gap-3">
            <svg class="w-4 h-4 text-green-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
            </svg>
            <span class="text-xs text-green-800">{{ __('Password updated successfully.') }}</span>
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <!-- Update Profile Information -->
        <div class="bg-white rounded-lg border border-gray-200 shadow-xs overflow-hidden hover:shadow-sm transition-shadow">
            <div class="px-4 py-3 bg-gray-50 border-b border-gray-200">
                <h2 class="text-base font-semibold text-gray-900">{{ __('Profile Information') }}</h2>
                <p class="text-xs text-gray-600 mt-0.5">Update your personal information and email address</p>
            </div>
            <div class="p-5">
                @include('profile.partials.update-profile-information-form')
            </div>
        </div>

        <!-- Update Password -->
        <div class="bg-white rounded-lg border border-gray-200 shadow-xs overflow-hidden hover:shadow-sm transition-shadow">
            <div class="px-4 py-3 bg-gray-50 border-b border-gray-200">
                <h2 class="text-base font-semibold text-gray-900">{{ __('Update Password') }}</h2>
                <p class="text-xs text-gray-600 mt-0.5">Change your password to keep your account secure</p>
            </div>
            <div class="p-5 pt-0">
                @include('profile.partials.update-password-form')
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-4 mt-4">
        <div class="bg-white rounded-lg border border-gray-200 shadow-xs overflow-hidden hover:shadow-sm transition-shadow">
            <div class="px-4 py-3 bg-gray-50 border-b border-gray-200">
                <h2 class="text-base font-semibold text-gray-900">{{ __('Delete Account') }}</h2>
                <p class="text-xs text-gray-600 mt-0.5">Permanently delete your account and all associated data</p>
            </div>
            <div class="p-5">
                @include('profile.partials.delete-user-form')
            </div>
        </div>
    </div>
</div>
@endsection
