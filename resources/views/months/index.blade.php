@extends('layouts.app')

@php
use App\Enums\MonthStatusEnum;
@endphp

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold">Months</h1>
        @can('months.create')
            <a href="{{ route('months.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Create Month
            </a>
        @endcan
    </div>

    @if ($message = Session::get('success'))
    <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
        {{ $message }}
    </div>
    @endif

    @can('months.view')
        <div class="overflow-x-auto bg-white rounded-lg shadow">
            <table class="min-w-full">
                <thead class="bg-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Name</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Start Date</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">End Date</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Status</th>
                        @canany(['months.update', 'months.delete'])
                            
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Actions</th>
                        @endcanany
                    </tr>
                </thead>
                <tbody>
                    @forelse ($months as $month)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm text-gray-800">{{ $month->name }}</td>
                        <td class="px-6 py-4 text-sm text-gray-800">{{ $month->start_date->format('M d, Y') }}</td>
                        <td class="px-6 py-4 text-sm text-gray-800">{{ $month->end_date->format('M d, Y') }}</td>
                        <td class="px-6 py-4 text-sm">
                            <span class="px-3 py-1 rounded-full text-sm font-semibold
                                {{ $month->status === MonthStatusEnum::ACTIVE ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ $month->status->label() }}
                            </span>
                        </td>
                        @canany(['months.update', 'months.delete'])
                            <td class="px-6 py-4 text-sm">
                                @can('update', $month)
                                    <a href="{{ route('months.edit', $month->id) }}" class="text-yellow-600 hover:text-yellow-900 mr-3">Edit</a>
                                @endcan
                                @can('delete', $month)
                                    <form action="{{ route('months.destroy', $month->id) }}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Are you sure?')">Delete</button>
                                    </form>
                                @endcan
                            </td>
                        @endcanany
                        
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">No months found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @else
        <div class="alert alert-warning">
            <i class="fas fa-lock"></i> You don't have permission to view months.
        </div>
    @endcan
</div>
@endsection
