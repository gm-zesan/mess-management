@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">Pending Members - {{ $mess->name }}</h2>

                @if (session('success'))
                    <div class="mb-4 px-4 py-3 bg-green-100 border border-green-400 text-green-700 rounded">
                        {{ session('success') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="mb-4 px-4 py-3 bg-red-100 border border-red-400 text-red-700 rounded">
                        {{ session('error') }}
                    </div>
                @endif

                @if ($pendingMembers->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full border-collapse border border-gray-300">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="border border-gray-300 px-4 py-2 text-left">Name</th>
                                    <th class="border border-gray-300 px-4 py-2 text-left">Email</th>
                                    <th class="border border-gray-300 px-4 py-2 text-left">Invited By</th>
                                    <th class="border border-gray-300 px-4 py-2 text-left">Date</th>
                                    <th class="border border-gray-300 px-4 py-2 text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($pendingMembers as $member)
                                    <tr class="hover:bg-gray-50">
                                        <td class="border border-gray-300 px-4 py-2">{{ $member->user->name }}</td>
                                        <td class="border border-gray-300 px-4 py-2">{{ $member->user->email }}</td>
                                        <td class="border border-gray-300 px-4 py-2">
                                            {{ $member->invitedBy?->name ?? 'System' }}
                                        </td>
                                        <td class="border border-gray-300 px-4 py-2 text-sm text-gray-600">
                                            {{ $member->created_at->format('M d, Y') }}
                                        </td>
                                        <td class="border border-gray-300 px-4 py-2">
                                            <div class="flex gap-2 justify-center">
                                                <form action="{{ route('mess.approve', [$mess, $member]) }}" method="POST" style="display: inline;">
                                                    @csrf
                                                    <button 
                                                        type="submit" 
                                                        class="bg-green-600 text-white py-1 px-3 rounded text-sm hover:bg-green-700 transition"
                                                    >
                                                        Approve
                                                    </button>
                                                </form>
                                                <form action="{{ route('mess.reject', [$mess, $member]) }}" method="POST" style="display: inline;">
                                                    @csrf
                                                    <button 
                                                        type="submit" 
                                                        class="bg-red-600 text-white py-1 px-3 rounded text-sm hover:bg-red-700 transition"
                                                    >
                                                        Reject
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-12">
                        <p class="text-gray-500 text-lg">No pending members</p>
                    </div>
                @endif

                <div class="mt-6">
                    <a 
                        href="{{ route('mess.invite', $mess) }}" 
                        class="bg-indigo-600 text-white py-2 px-6 rounded-md hover:bg-indigo-700 transition"
                    >
                        Invite New Member
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
