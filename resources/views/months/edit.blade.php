@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-6">Edit Month</h1>

    <div class="bg-white rounded-lg shadow p-6 max-w-md">
        <form action="{{ route('months.update', $month->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">Name</label>
                <input type="text" name="name" id="name" class="w-full px-4 py-2 border rounded-lg @error('name') border-red-500 @enderror"
                    value="{{ old('name', $month->name) }}" required>
                @error('name')
                <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                @enderror
            </div>

            <div class="mb-4">
                <label for="start_date" class="block text-sm font-semibold text-gray-700 mb-2">Start Date</label>
                <input type="date" name="start_date" id="start_date" class="w-full px-4 py-2 border rounded-lg @error('start_date') border-red-500 @enderror"
                    value="{{ old('start_date', $month->start_date->format('Y-m-d')) }}" required>
                @error('start_date')
                <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                @enderror
            </div>

            <div class="mb-4">
                <label for="end_date" class="block text-sm font-semibold text-gray-700 mb-2">End Date</label>
                <input type="date" name="end_date" id="end_date" class="w-full px-4 py-2 border rounded-lg @error('end_date') border-red-500 @enderror"
                    value="{{ old('end_date', $month->end_date->format('Y-m-d')) }}" required>
                @error('end_date')
                <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                @enderror
            </div>

            <div class="mb-6">
                <label for="status" class="block text-sm font-semibold text-gray-700 mb-2">Status</label>
                <select name="status" id="status" class="w-full px-4 py-2 border rounded-lg @error('status') border-red-500 @enderror" required>
                    <option value="active" {{ old('status', $month->status) === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="closed" {{ old('status', $month->status) === 'closed' ? 'selected' : '' }}>Closed</option>
                </select>
                @error('status')
                <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                @enderror
            </div>

            <div class="flex gap-2">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Update</button>
                <a href="{{ route('months.index') }}" class="bg-gray-400 hover:bg-gray-500 text-white font-bold py-2 px-4 rounded">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
