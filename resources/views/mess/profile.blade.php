@extends('layouts.app')

@use('App\Enums\RoleEnum')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Status Messages -->
    @if (session('success'))
        <div class="mb-4 p-3 bg-green-50 border border-green-200 rounded-lg flex items-center gap-3">
            <svg class="w-4 h-4 text-green-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
            </svg>
            <span class="text-xs text-green-800">{{ session('success') }}</span>
        </div>
    @endif

    @if (session('error'))
        <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg flex items-center gap-3">
            <svg class="w-4 h-4 text-red-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
            </svg>
            <span class="text-xs text-red-800">{{ session('error') }}</span>
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <!-- Main Content -->
        <!-- Profile Information -->
        <div class="bg-white rounded-lg border border-gray-200 shadow-xs overflow-hidden">
            <div class="px-4 py-3 bg-gray-50 border-b border-gray-200">
                <h2 class="text-base font-semibold text-gray-900">Mess Information</h2>
                <p class="text-xs text-gray-600 mt-0.5">Update your mess details and settings</p>
            </div>
            
            
            <div class="p-5">
                <form action="{{ route('mess.profile.update', $mess) }}" method="POST" class="space-y-4">
                    @csrf
                    @method('PATCH')

                    <!-- Mess Name -->
                    <div>
                        <label for="name" class="text-xs font-semibold text-gray-900">
                            Mess Name
                        </label>
                        <input 
                            type="text" 
                            id="name" 
                            name="name" 
                            value="{{ old('name', $mess->name) }}"
                            @if(!Auth::user()->hasRole(RoleEnum::SUPERADMIN->value)) disabled @endif
                            class="mt-1 block w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-sky-500 focus:border-transparent transition-all {{ !Auth::user()->hasRole(RoleEnum::SUPERADMIN->value) ? 'bg-gray-50 text-gray-600 cursor-not-allowed' : 'bg-white' }}"
                            placeholder="Mess name"
                        />
                        @error('name')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                        @if(!Auth::user()->hasRole(RoleEnum::SUPERADMIN->value))
                            <p class="mt-1 text-xs text-gray-500">Only superadmin can edit the mess name</p>
                        @endif
                    </div>

                    <!-- Description -->
                    <div>
                        <label for="description" class="text-xs font-semibold text-gray-900">
                            Description
                        </label>
                        <textarea 
                            id="description" 
                            name="description" 
                            rows="3"
                            class="mt-1 block w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-sky-500 focus:border-transparent transition-all bg-white"
                            placeholder="Mess description"
                        >{{ old('description', $mess->description) }}</textarea>
                        @error('description')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Submit Button -->
                    <div class="flex gap-2 pt-3 border-t border-gray-200">
                        <button 
                            type="submit" 
                            class="px-3 py-1.5 bg-sky-600 hover:bg-sky-700 text-white text-xs font-semibold rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-sky-500 focus:ring-offset-2"
                        >
                            Save Changes
                        </button>
                        <a 
                            href="{{ route('dashboard') }}" 
                            class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-900 text-xs font-semibold rounded-lg transition-colors"
                        >
                            Back
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Mess Details -->
        <div class="bg-white rounded-lg border border-gray-200 shadow-xs overflow-hidden">
            <div class="px-4 py-3 bg-gray-50 border-b border-gray-200">
                <h2 class="text-base font-semibold text-gray-900">Details</h2>
            </div>
            
            <div class="p-5">
                <div class="grid grid-cols-2 gap-4">
                    <!-- Join Code -->
                    <div class="p-3 bg-gray-50 border border-gray-200 rounded-lg">
                        <p class="text-xs text-gray-600 font-semibold uppercase tracking-wider">Join Code</p>
                        <p class="text-sm font-mono font-bold text-gray-900 mt-1">{{ $mess->join_code }}</p>
                    </div>

                    <!-- Manager -->
                    <div class="p-3 bg-gray-50 border border-gray-200 rounded-lg">
                        <p class="text-xs text-gray-600 font-semibold uppercase tracking-wider">Manager</p>
                        <p class="text-sm text-gray-900 mt-1">{{ $mess->manager?->name ?? 'Not assigned' }}</p>
                    </div>

                    <!-- Members Count -->
                    <div class="p-3 bg-gray-50 border border-gray-200 rounded-lg">
                        <p class="text-xs text-gray-600 font-semibold uppercase tracking-wider">Approved Members</p>
                        <p class="text-sm font-bold text-gray-900 mt-1">{{ $mess->messUsers()->where('status', 'approved')->count() }}</p>
                    </div>

                    <!-- Pending Requests -->
                    <div class="p-3 bg-gray-50 border border-gray-200 rounded-lg">
                        <p class="text-xs text-gray-600 font-semibold uppercase tracking-wider">Pending Requests</p>
                        <p class="text-sm font-bold text-gray-900 mt-1">{{ $mess->messUsers()->where('status', 'pending')->count() }}</p>
                    </div>

                    <!-- Created Date -->
                    <div class="p-3 bg-gray-50 border border-gray-200 rounded-lg col-span-2">
                        <p class="text-xs text-gray-600 font-semibold uppercase tracking-wider">Created</p>
                        <p class="text-sm text-gray-900 mt-1">{{ $mess->created_at->format('d M Y') }} at {{ $mess->created_at->format('H:i') }}</p>
                    </div>

                    <div class="flex items-center justify-center col-span-2 gap-2">
                        <a 
                            href="{{ route('mess.invite', $mess) }}" 
                            class="block w-full text-center bg-green-600 hover:bg-green-700 text-white py-1.5 px-3 rounded-lg text-xs font-semibold transition-colors focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2"
                        >
                            Invite Member
                        </a>
                        
                        @if($mess->messUsers()->where('status', 'pending')->count() > 0)
                            <a 
                                href="{{ route('mess.pending-invitations') }}" 
                                class="block w-full text-center bg-yellow-600 hover:bg-yellow-700 text-white py-1.5 px-3 rounded-lg text-xs font-semibold transition-colors focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2"
                            >
                                Pending Requests ({{ $mess->messUsers()->where('status', 'pending')->count() }})
                            </a>
                        @endif
                        @role(RoleEnum::MEMBER->value | RoleEnum::MANAGER->value)
                        <a 
                            href="#" 
                            onclick="event.preventDefault(); document.getElementById('leave-mess-modal').showModal();"
                            class="block w-full text-center bg-red-600 hover:bg-red-700 text-white py-1.5 px-3 rounded-lg text-xs font-semibold transition-colors focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2"
                        >
                            Leave Mess 
                        </a>
                        @endrole
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- Leave Mess Form -->
<form id="leave-mess-form" action="{{ route('mess.leave', $mess) }}" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

<!-- Leave Mess Confirmation Modal -->
<dialog id="leave-mess-modal" class="rounded-lg shadow-xl backdrop:bg-black backdrop:bg-opacity-50">
    <div class="p-6 max-w-md">
        <!-- Header -->
        <div class="flex items-start gap-3 mb-4">
            <div class="flex-shrink-0 flex items-center justify-center h-10 w-10 rounded-full bg-red-100">
                <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4v2m0 0v2m0-6v2m0-4v2M7.414 5.586a2 2 0 011.414-.586h6.344a2 2 0 011.414.586L20 8.17V12a8 8 0 11-16 0V8.17l2.086-2.584z"></path>
                </svg>
            </div>
            <div>
                <h3 class="text-base font-semibold text-gray-900">Leave {{ $mess->name }}?</h3>
                <p class="text-xs text-gray-600 mt-0.5">This action cannot be undone</p>
            </div>
        </div>

        <!-- Warning Section -->
        <div class="bg-red-50 border border-red-200 rounded-lg p-3 mb-5">
            <p class="text-xs text-red-800">
                You will be removed from this mess and will no longer have access to its data.
            </p>
        </div>

        <!-- Buttons -->
        <div class="flex gap-2">
            <button 
                onclick="document.getElementById('leave-mess-modal').close();"
                class="flex-1 px-3 py-1.5 bg-gray-200 hover:bg-gray-300 text-gray-900 text-xs font-semibold rounded-lg transition-colors"
            >
                Cancel
            </button>
            <button 
                onclick="document.getElementById('leave-mess-form').submit();"
                class="flex-1 px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white text-xs font-semibold rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2"
            >
                Leave Mess
            </button>
        </div>
    </div>
</dialog>
@endsection
