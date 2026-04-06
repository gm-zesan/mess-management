@extends('layouts.app')

@section('content')
<div class="py-8">
    <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                @if ($errors->any())
                    <div class="mb-4 px-4 py-3 bg-red-100 border border-red-400 text-red-700 rounded">
                        <p class="font-semibold mb-2">Errors:</p>
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if (session('success'))
                    <div class="mb-4 px-4 py-3 bg-green-100 border border-green-400 text-green-700 rounded">
                        <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
                    </div>
                @endif

                <form action="{{ route('mess.invite.store', $mess) }}" method="POST" class="space-y-6">
                    @csrf

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                            User Email <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="email" 
                            id="email" 
                            name="email" 
                            value="{{ old('email') }}"
                            class="w-full rounded-md border border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 px-3 py-2"
                            placeholder="user@example.com"
                            required
                            autofocus
                        />
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-2 text-sm text-gray-600">
                            Enter the email of an existing user to invite them to join {{ $mess->name }}
                        </p>
                    </div>

                    <div class="flex gap-4">
                        <button 
                            type="submit" 
                            class="bg-indigo-600 text-white py-2 px-6 rounded-md hover:bg-indigo-700 transition font-medium"
                        >
                            <i class="fa-solid fa-paper-plane me-2"></i> Send Invitation
                        </button>
                        <a 
                            href="{{ route('mess.profile', $mess) }}" 
                            class="bg-gray-600 text-white py-2 px-6 rounded-md hover:bg-gray-700 transition font-medium"
                        >
                            <i class="fa-solid fa-arrow-left me-2"></i> Back
                        </a>
                    </div>
                </form>

                <!-- Info Box -->
                <div class="mt-8 p-4 bg-blue-50 border border-blue-200 rounded">
                    <h3 class="font-semibold text-blue-900 mb-2">
                        <i class="fa-solid fa-circle-info me-2"></i> How it works
                    </h3>
                    <ul class="text-sm text-blue-800 space-y-1">
                        <li>✓ User receives an invitation to join {{ $mess->name }}</li>
                        <li>✓ They appear in pending requests (tab above)</li>
                        <li>✓ Approve or reject their request as manager</li>
                        <li>✓ Once approved, they can access mess features</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
