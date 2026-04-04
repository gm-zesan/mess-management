@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold text-gray-900">Members - {{ $mess->name }}</h2>
                </div>

                @if ($members->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full border-collapse border border-gray-300">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="border border-gray-300 px-4 py-2 text-left">Name</th>
                                    <th class="border border-gray-300 px-4 py-2 text-left">Email</th>
                                    <th class="border border-gray-300 px-4 py-2 text-left">Joined Date</th>
                                    <th class="border border-gray-300 px-4 py-2 text-left">Role</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($members as $member)
                                    <tr class="hover:bg-gray-50">
                                        <td class="border border-gray-300 px-4 py-2">{{ $member->user->name }}</td>
                                        <td class="border border-gray-300 px-4 py-2">{{ $member->user->email }}</td>
                                        <td class="border border-gray-300 px-4 py-2 text-sm text-gray-600">
                                            {{ $member->created_at->format('M d, Y') }}
                                        </td>
                                        <td class="border border-gray-300 px-4 py-2">
                                            @if ($member->user->hasRole('manager'))
                                                <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm font-semibold">Manager</span>
                                            @else
                                                <span class="bg-gray-100 text-gray-800 px-3 py-1 rounded-full text-sm">Member</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-12">
                        <p class="text-gray-500 text-lg">No members yet</p>
                    </div>
                @endif

                <div class="mt-6 flex gap-4">
                    <a 
                        href="{{ route('mess.invite', $mess) }}" 
                        class="bg-indigo-600 text-white py-2 px-6 rounded-md hover:bg-indigo-700 transition"
                    >
                        Invite Member
                    </a>
                    <a 
                        href="{{ route('mess.pending', $mess) }}" 
                        class="bg-yellow-600 text-white py-2 px-6 rounded-md hover:bg-yellow-700 transition"
                    >
                        View Pending
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
