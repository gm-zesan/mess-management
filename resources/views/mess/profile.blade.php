@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-6">
            <h1 class="font-semibold text-3xl text-gray-800 leading-tight">{{ $mess->name }}</h1>
            <p class="text-gray-600 mt-2">Manage your mess profile and settings</p>
        </div>

        <!-- Success Message -->
        @if (session('success'))
            <div class="mb-4 px-4 py-3 bg-green-100 border border-green-400 text-green-700 rounded">
                {{ session('success') }}
            </div>
        @endif

        <!-- Error Message -->
        @if (session('error'))
            <div class="mb-4 px-4 py-3 bg-red-100 border border-red-400 text-red-700 rounded">
                {{ session('error') }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="lg:col-span-2">
                <!-- Profile Information -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <h2 class="text-xl font-semibold text-gray-900 mb-4">Mess Information</h2>
                        
                        <form action="{{ route('mess.profile.update', $mess) }}" method="POST" class="space-y-6">
                            @csrf
                            @method('PATCH')

                            <!-- Mess Name -->
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700">
                                    Mess Name
                                </label>
                                <input 
                                    type="text" 
                                    id="name" 
                                    name="name" 
                                    value="{{ old('name', $mess->name) }}"
                                    @if(!Auth::user()->hasRole('SUPERADMIN')) disabled @endif
                                    class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm px-3 py-2 {{ !Auth::user()->hasRole('SUPERADMIN') ? 'bg-gray-100 text-gray-600' : '' }}"
                                    placeholder="Mess name"
                                />
                                @error('name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                @if(!Auth::user()->hasRole('SUPERADMIN'))
                                    <p class="mt-1 text-xs text-gray-500">Only superadmin can edit the mess name</p>
                                @endif
                            </div>

                            <!-- Description -->
                            <div>
                                <label for="description" class="block text-sm font-medium text-gray-700">
                                    Description
                                </label>
                                <textarea 
                                    id="description" 
                                    name="description" 
                                    rows="4"
                                    class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm px-3 py-2"
                                    placeholder="Mess description"
                                >{{ old('description', $mess->description) }}</textarea>
                                @error('description')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Submit Button -->
                            <div class="flex gap-4">
                                <button 
                                    type="submit" 
                                    class="bg-indigo-600 text-white py-2 px-6 rounded-md hover:bg-indigo-700 transition"
                                >
                                    <i class="fa-solid fa-save me-2"></i> Save Changes
                                </button>
                                <a 
                                    href="{{ route('dashboard') }}" 
                                    class="bg-gray-600 text-white py-2 px-6 rounded-md hover:bg-gray-700 transition"
                                >
                                    <i class="fa-solid fa-arrow-left me-2"></i> Back
                                </a>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Mess Details -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <h2 class="text-xl font-semibold text-gray-900 mb-4">Details</h2>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <!-- Join Code -->
                            <div class="bg-gray-50 p-4 rounded">
                                <p class="text-sm text-gray-600 font-medium">Join Code</p>
                                <p class="text-lg font-mono font-bold text-gray-800 mt-1">{{ $mess->join_code }}</p>
                            </div>

                            <!-- Manager -->
                            <div class="bg-gray-50 p-4 rounded">
                                <p class="text-sm text-gray-600 font-medium">Manager</p>
                                <p class="text-lg text-gray-800 mt-1">{{ $mess->manager?->name ?? 'Not assigned' }}</p>
                            </div>

                            <!-- Members Count -->
                            <div class="bg-gray-50 p-4 rounded">
                                <p class="text-sm text-gray-600 font-medium">Approved Members</p>
                                <p class="text-lg text-gray-800 mt-1">{{ $mess->messUsers()->where('status', 'approved')->count() }}</p>
                            </div>

                            <!-- Pending Requests -->
                            <div class="bg-gray-50 p-4 rounded">
                                <p class="text-sm text-gray-600 font-medium">Pending Requests</p>
                                <p class="text-lg text-yellow-600 font-bold mt-1">{{ $mess->messUsers()->where('status', 'pending')->count() }}</p>
                            </div>

                            <!-- Created Date -->
                            <div class="bg-gray-50 p-4 rounded col-span-2">
                                <p class="text-sm text-gray-600 font-medium">Created</p>
                                <p class="text-lg text-gray-800 mt-1">{{ $mess->created_at->format('d M Y H:i') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div>
                <!-- Quick Actions -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Actions</h3>
                        
                        <div class="space-y-3">
                            <a 
                                href="{{ route('mess.invite', $mess) }}" 
                                class="block w-full text-center bg-green-600 text-white py-2 px-4 rounded-md hover:bg-green-700 transition"
                            >
                                <i class="fa-solid fa-user-plus me-2"></i> Invite Member
                            </a>
                            @if($mess->messUsers()->where('status', 'pending')->count() > 0)
                                <a 
                                    href="{{ route('mess.pending-invitations') }}" 
                                    class="block w-full text-center bg-yellow-600 text-white py-2 px-4 rounded-md hover:bg-yellow-700 transition"
                                >
                                    <i class="fa-solid fa-hourglass-end me-2"></i> 
                                    Pending Requests ({{ $mess->messUsers()->where('status', 'pending')->count() }})
                                </a>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Permissions Info -->
                <div class="bg-blue-50 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-blue-50 border border-blue-200 rounded">
                        <h3 class="text-lg font-semibold text-blue-900 mb-3">Your Permissions</h3>
                        
                        @if(Auth::user()->hasRole('SUPERADMIN'))
                            <div class="text-sm text-blue-800">
                                <p class="mb-2"><i class="fa-solid fa-check text-green-600 me-2"></i> <strong>Edit name</strong></p>
                                <p class="mb-2"><i class="fa-solid fa-check text-green-600 me-2"></i> <strong>Edit description</strong></p>
                                <p><i class="fa-solid fa-check text-green-600 me-2"></i> <strong>Full control</strong></p>
                            </div>
                        @else
                            <div class="text-sm text-blue-800">
                                <p class="mb-2"><i class="fa-solid fa-times text-red-600 me-2"></i> <strong>Edit name</strong></p>
                                <p><i class="fa-solid fa-check text-green-600 me-2"></i> <strong>Edit description</strong></p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
