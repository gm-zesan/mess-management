@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        @if($isSuperAdmin)
            <!-- Superadmin View -->
            <div class="mb-6">
                <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
                    <i class="fa-solid fa-crown text-yellow-500 me-2"></i> Superadmin - All Messes
                </h2>
                <p class="text-gray-600 mt-2">Click on any mess to enter and manage it. You can view all messes without joining.</p>
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

            <!-- Create New Mess -->
            <div class="mb-8 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">
                        <i class="fa-solid fa-plus me-2"></i> Create a New Mess
                    </h3>
                    
                    <form action="{{ route('mess.create') }}" method="POST" class="space-y-4">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
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
                                    rows="1"
                                    class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm px-3 py-2"
                                    placeholder="Optional description"
                                >{{ old('description') }}</textarea>
                                @error('description')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <button 
                            type="submit" 
                            class="bg-indigo-600 text-white py-2 px-6 rounded-md hover:bg-indigo-700 transition"
                        >
                            <i class="fa-solid fa-plus me-2"></i> Create Mess
                        </button>
                    </form>
                </div>
            </div>

            <!-- All Messes Grid -->
            <div>
                <h3 class="text-lg font-semibold text-gray-900 mb-4">
                    <i class="fa-solid fa-list me-2"></i> All Messes ({{ $availableMesses->count() }})
                </h3>

                @if($availableMesses->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($availableMesses as $mess)
                            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-lg transition cursor-pointer group">
                                <form action="{{ route('mess.enter', $mess) }}" method="POST" class="h-full">
                                    @csrf
                                    <button type="submit" class="w-full h-full text-left">
                                        <div class="p-6 bg-white border-b border-gray-200 group-hover:bg-gray-50 transition">
                                            <div class="flex justify-between items-start mb-3">
                                                <div class="flex-1">
                                                    <h4 class="font-semibold text-gray-900 text-lg group-hover:text-indigo-600 transition">{{ $mess->name }}</h4>
                                                    @if ($mess->description)
                                                        <p class="text-sm text-gray-600 mt-1">{{ Str::limit($mess->description, 100) }}</p>
                                                    @endif
                                                </div>
                                                <i class="fa-solid fa-arrow-right text-gray-400 group-hover:text-indigo-600 transition"></i>
                                            </div>
                                            
                                            <!-- Mess Details -->
                                            <div class="bg-gray-50 p-3 rounded mt-4 space-y-2">
                                                <div class="flex justify-between items-center text-sm">
                                                    <span class="text-gray-600">Join Code:</span>
                                                    <span class="font-mono font-bold text-gray-800">{{ $mess->join_code }}</span>
                                                </div>
                                                <div class="flex justify-between items-center text-sm">
                                                    <span class="text-gray-600">Manager:</span>
                                                    <span class="text-gray-800">{{ $mess->manager?->name ?? 'Not assigned' }}</span>
                                                </div>
                                                <div class="flex justify-between items-center text-sm">
                                                    <span class="text-gray-600">Members:</span>
                                                    <span class="text-gray-800 font-semibold">{{ $mess->messUsers()->where('status', 'approved')->count() }}</span>
                                                </div>
                                                <div class="flex justify-between items-center text-sm">
                                                    <span class="text-gray-600">Pending:</span>
                                                    <span class="text-yellow-600 font-semibold">{{ $mess->messUsers()->where('status', 'pending')->count() }}</span>
                                                </div>
                                                <div class="flex justify-between items-center text-sm">
                                                    <span class="text-gray-600">Created:</span>
                                                    <span class="text-gray-800">{{ $mess->created_at->format('M d, Y') }}</span>
                                                </div>
                                            </div>

                                            <!-- Enter Button -->
                                            <div class="mt-4">
                                                <div class="w-full bg-indigo-600 text-white py-2 px-4 rounded-md hover:bg-indigo-700 transition text-center font-medium">
                                                    <i class="fa-solid fa-arrow-right-to-bracket me-2"></i> Enter Mess
                                                </div>
                                            </div>
                                        </div>
                                    </button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 bg-white border-b border-gray-200 text-center">
                            <i class="fa-solid fa-inbox text-4xl text-gray-300 mb-4 block"></i>
                            <h3 class="text-lg font-semibold text-gray-700 mb-2">No Messes Found</h3>
                            <p class="text-gray-600">Create a new mess above to get started.</p>
                        </div>
                    </div>
                @endif
            </div>

        @else
            <!-- Regular User View -->
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
        @endif
    </div>
</div>
@endsection

