@extends('layouts.app')
@use('App\Enums\RoleEnum')

@section('content')
<div class="bg-gray-50 overflow-hidden flex">
    <!-- Left Side: Create Mess Form (for ALL users) -->
    <div class="w-2/5 bg-white border-r border-gray-200 flex flex-col overflow-hidden">
        <div class="p-8 flex flex-col h-full">
            <!-- Heading -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900">Create New Mess</h1>
                <p class="text-sm text-gray-600 mt-1">Add a new mess to the system</p>
            </div>

            <!-- Alert Messages -->
            @if (session('success'))
                <div class="mb-6 px-4 py-3 bg-green-50 border border-green-200 text-green-700 rounded-md flex items-center gap-3 text-sm">
                    <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if (session('error'))
                <div class="mb-6 px-4 py-3 bg-red-50 border border-red-200 text-red-700 rounded-md flex items-center gap-3 text-sm">
                    <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                    </svg>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            <!-- Form -->
            <form action="{{ route('mess.create') }}" method="POST" class="space-y-4">
                @csrf
                <div class="flex flex-col gap-1">
                    <label for="name" class="text-xs font-semibold uppercase tracking-wider text-gray-900">
                        Mess Name <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        id="name" 
                        name="name" 
                        value="{{ old('name') }}"
                        class="rounded-md border border-gray-300 bg-white py-2.5 px-3 text-sm font-normal text-gray-900 placeholder-gray-400 transition-all focus:border-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-100 {{ $errors->has('name') ? 'border-red-500' : '' }}"
                        placeholder="e.g., East Wing Mess"
                        required
                    />
                    @error('name')
                        <p class="text-xs font-medium text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex flex-col gap-1">
                    <label for="description" class="text-xs font-semibold uppercase tracking-wider text-gray-900">
                        Description <span class="text-gray-500">(Optional)</span>
                    </label>
                    <textarea 
                        id="description" 
                        name="description" 
                        rows="5"
                        class="rounded-md border border-gray-300 bg-white py-2.5 px-3 text-sm font-normal text-gray-900 placeholder-gray-400 transition-all focus:border-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-100 resize-none {{ $errors->has('description') ? 'border-red-500' : '' }}"
                        placeholder="Building name, location, or other details"
                    >{{ old('description') }}</textarea>
                    @error('description')
                        <p class="text-xs font-medium text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <button 
                    type="submit" 
                    class="w-full rounded-md bg-sky-600 px-4 py-2.5 font-semibold text-white transition-all hover:bg-sky-700 active:scale-98 focus:outline-none focus:ring-2 focus:ring-sky-100 mt-8"
                >
                    Create Mess
                </button>
            </form>
        </div>
    </div>

    <!-- Right Side -->
    @if(Auth::user()->hasRole(RoleEnum::SUPERADMIN->value))
        <!-- Right Side: All Messes List (for superadmin) -->
        <div class="w-3/5 bg-gray-50 overflow-y-auto flex flex-col">
            <div class="p-6 flex-1 overflow-y-auto">
                <h2 class="text-xl font-bold text-gray-900 mb-1">All Messes</h2>
                <p class="text-xs text-gray-600 mb-4">{{ $availableMesses->total() }} total mess{{ $availableMesses->total() !== 1 ? 'es' : '' }} in system</p>
                
                @if($availableMesses->count() > 0)
                    <div class="grid grid-cols-3 gap-3">
                        @foreach($availableMesses as $mess)
                            <form action="{{ route('mess.enter', $mess) }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full text-left group h-full">
                                    <div class="bg-white rounded border border-gray-200 p-3 hover:border-sky-300 hover:shadow-sm transition-all h-full flex flex-col">
                                        <h3 class="text-sm font-semibold text-gray-900 truncate group-hover:text-sky-600 transition-colors line-clamp-2">{{ $mess->name }}</h3>
                                        <div class="mt-auto pt-2 flex items-center gap-2 text-xs text-gray-600 border-t border-gray-100">
                                            <span class="flex items-center gap-0.5">
                                                <svg class="w-3 h-3 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM9 12a6 6 0 11-12 0 6 6 0 0112 0z"></path>
                                                </svg>
                                                <span class="font-medium text-gray-800">{{ $mess->messUsers()->where('status', 'approved')->count() }}</span>
                                            </span>
                                            <span class="text-gray-400">•</span>
                                            <span class="text-gray-600 text-xs">{{ $mess->created_at->format('M d') }}</span>
                                        </div>
                                    </div>
                                </button>
                            </form>
                        @endforeach
                    </div>
                @else
                    <div class="flex flex-col items-center justify-center h-64">
                        <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                        </svg>
                        <p class="text-gray-600 font-medium text-sm">No messes yet</p>
                        <p class="text-gray-500 text-xs mt-0.5">Create one on the left to get started</p>
                    </div>
                @endif
            </div>

            <!-- Pagination -->
            @if($availableMesses->hasPages())
                <div class="p-4 border-t border-gray-200 bg-white">
                    <div class="flex items-center justify-between text-xs">
                        <div class="text-gray-600">
                            Page {{ $availableMesses->currentPage() }} of {{ $availableMesses->lastPage() }}
                        </div>
                        
                        <div class="flex gap-2">
                            {{-- Previous Page Link --}}
                            @if ($availableMesses->onFirstPage())
                                <span class="px-2 py-1 rounded border border-gray-300 text-gray-400 cursor-not-allowed bg-gray-50">
                                    ← Prev
                                </span>
                            @else
                                <a href="{{ $availableMesses->previousPageUrl() }}" class="px-2 py-1 rounded border border-gray-300 text-gray-700 hover:border-sky-300 hover:text-sky-600 transition-colors">
                                    ← Prev
                                </a>
                            @endif

                            {{-- Next Page Link --}}
                            @if ($availableMesses->hasMorePages())
                                <a href="{{ $availableMesses->nextPageUrl() }}" class="px-2 py-1 rounded border border-gray-300 text-gray-700 hover:border-sky-300 hover:text-sky-600 transition-colors">
                                    Next →
                                </a>
                            @else
                                <span class="px-2 py-1 rounded border border-gray-300 text-gray-400 cursor-not-allowed bg-gray-50">
                                    Next →
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        </div>
    @else
        <!-- Right Side: Join by Code Form (for non-superadmin) -->
        <div class="w-3/5 bg-gray-50 flex items-center justify-center p-8">
            <div class="w-full max-w-md bg-white rounded-lg shadow-sm border border-gray-200 p-8">
                <!-- Heading -->
                <div class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-900">Join a Mess</h2>
                    <p class="text-sm text-gray-600 mt-1">Enter the join code to join an existing mess</p>
                </div>

                <!-- Alert Messages -->
                @if (session('success'))
                    <div class="mb-6 px-4 py-3 bg-green-50 border border-green-200 text-green-700 rounded-md flex items-center gap-3 text-sm">
                        <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                @if (session('error'))
                    <div class="mb-6 px-4 py-3 bg-red-50 border border-red-200 text-red-700 rounded-md flex items-center gap-3 text-sm">
                        <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                        </svg>
                        <span>{{ session('error') }}</span>
                    </div>
                @endif

                <!-- Join by Code Form -->
                <form action="{{ route('mess.join-by-code') }}" method="POST" class="space-y-4">
                    @csrf
                    <div class="flex flex-col gap-1">
                        <label for="join_code" class="text-xs font-semibold uppercase tracking-wider text-gray-900">
                            Join Code <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="text" 
                            id="join_code" 
                            name="join_code" 
                            value="{{ old('join_code') }}"
                            class="rounded-md border border-gray-300 bg-white py-2.5 px-3 text-sm font-mono font-semibold text-gray-900 placeholder-gray-400 transition-all focus:border-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-100 uppercase {{ $errors->has('join_code') ? 'border-red-500' : '' }}"
                            placeholder="e.g., ABCD1234"
                            maxlength="8"
                            required
                        />
                        @error('join_code')
                            <p class="text-xs font-medium text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <button 
                        type="submit" 
                        class="w-full rounded-md bg-sky-600 px-4 py-2.5 font-semibold text-white transition-all hover:bg-sky-700 active:scale-98 focus:outline-none focus:ring-2 focus:ring-sky-100"
                    >
                        Join Mess
                    </button>
                </form>

                <!-- Info -->
                <div class="mt-6 p-4 bg-sky-50 border border-sky-100 rounded-md">
                    <p class="text-xs text-sky-800 font-medium">💡 Ask your mess manager for the join code to get started.</p>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection

