@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="mb-6">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Welcome! Select or Create a Mess') }}
            </h2>
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

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Create New Mess Section -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Create a New Mess</h3>
                    
                    <form action="{{ route('mess.create') }}" method="POST" class="space-y-4">
                        @csrf

                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">
                                Mess Name <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="text" 
                                id="name" 
                                name="name" 
                                value="{{ old('name') }}"
                                class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm px-3 py-2"
                                placeholder="e.g., East Wing Mess"
                                required
                            />
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700">
                                Description
                            </label>
                            <textarea 
                                id="description" 
                                name="description" 
                                rows="3"
                                class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm px-3 py-2"
                                placeholder="Optional description for your mess"
                            >{{ old('description') }}</textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <button 
                            type="submit" 
                            class="w-full bg-indigo-600 text-white py-2 px-4 rounded-md hover:bg-indigo-700 transition"
                        >
                            Create Mess
                        </button>
                    </form>
                </div>
            </div>

            <!-- Join Existing Mess Section -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Join an Existing Mess</h3>
                    
                    @if ($availableMesses->count() > 0)
                        <div class="space-y-3 max-h-96 overflow-y-auto">
                            @foreach ($availableMesses as $mess)
                                <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition">
                                    <div class="flex justify-between items-start mb-2">
                                        <div>
                                            <h4 class="font-semibold text-gray-900">{{ $mess->name }}</h4>
                                            @if ($mess->description)
                                                <p class="text-sm text-gray-600 mt-1">{{ $mess->description }}</p>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    <!-- Join Code Display -->
                                    <div class="bg-gray-50 p-2 rounded my-2 text-center">
                                        <p class="text-xs text-gray-500 mb-1">Join Code:</p>
                                        <p class="text-sm font-mono font-bold text-gray-800">{{ $mess->join_code }}</p>
                                    </div>
                                    
                                    <div class="flex items-center justify-between mt-3">
                                        <span class="text-xs text-gray-500">
                                            👥 {{ $mess->messUsers()->where('status', 'approved')->count() }} members
                                        </span>
                                        <form action="{{ route('mess.join', $mess) }}" method="POST" style="display: inline;">
                                            @csrf
                                            <button 
                                                type="submit" 
                                                class="bg-green-600 text-white py-1 px-3 rounded text-sm hover:bg-green-700 transition"
                                            >
                                                Request to Join
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 text-center py-8">No available messes to join. Create one above!</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
