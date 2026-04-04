@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
        <div class="mb-6">
            <h2 class="font-semibold text-2xl text-gray-800 leading-tight mb-2">
                {{ __('⏳ Pending Requests - ' . $activeMess->name) }}
            </h2>
            <p class="text-gray-600">Review and approve or reject pending user requests.</p>
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

        @if($pendingUsers->count() > 0)
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <table class="w-full text-left text-sm">
                    <thead class="bg-gray-100 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3 font-semibold text-gray-700">Name</th>
                            <th class="px-6 py-3 font-semibold text-gray-700">Email</th>
                            <th class="px-6 py-3 font-semibold text-gray-700">Requested On</th>
                            <th class="px-6 py-3 font-semibold text-gray-700">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pendingUsers as $pendingUser)
                            <tr class="border-b border-gray-200 hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <div class="font-medium text-gray-900">
                                        {{ $pendingUser->user->name ?? 'Unknown User' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-gray-600">
                                    {{ $pendingUser->user->email ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 text-gray-600">
                                    {{ $pendingUser->created_at->format('d M Y H:i') }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex gap-2">
                                        <form action="{{ route('mess.approve-user', $pendingUser) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success" title="Approve User">
                                                <i class="fa-solid fa-check me-1"></i> Approve
                                            </button>
                                        </form>
                                        <form action="{{ route('mess.reject-user', $pendingUser) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-danger" title="Reject User">
                                                <i class="fa-solid fa-times me-1"></i> Reject
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-6">
                <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
                    <i class="fa-solid fa-arrow-left me-2"></i> Back to Dashboard
                </a>
            </div>
        @else
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200 text-center">
                    <i class="fa-solid fa-inbox text-4xl text-gray-300 mb-4 block"></i>
                    <h3 class="text-lg font-semibold text-gray-700 mb-2">No Pending Requests</h3>
                    <p class="text-gray-600 mb-6">All users have been approved. Your mess is all set!</p>
                    <a href="{{ route('dashboard') }}" class="btn btn-primary">
                        <i class="fa-solid fa-arrow-left me-2"></i> Back to Dashboard
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
