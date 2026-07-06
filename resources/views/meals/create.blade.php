@extends('layouts.app')

@section('content')
@can('meals.create')
<div class="w-full px-2 sm:px-4 py-4 sm:py-8">

    <!-- Success Message -->
    @if (session('success'))
        <div class="mb-4 sm:mb-6 p-3 sm:p-4 bg-green-50 border border-green-200 rounded-lg flex items-center justify-between">
            <div class="flex items-center gap-2 sm:gap-3">
                <svg class="w-4 h-4 sm:w-5 sm:h-5 text-green-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                </svg>
                <span class="text-xs sm:text-sm text-green-800">{{ session('success') }}</span>
            </div>
            <button onclick="this.parentElement.style.display='none'" class="text-green-600 hover:text-green-800 flex-shrink-0">
                <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                </svg>
            </button>
        </div>
    @endif

    <form action="{{ route('meals.store') }}" method="POST">
        @csrf
        
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-4 sm:gap-6 mb-6 sm:mb-8">
            <!-- Left Sidebar: Date & Month Info -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg border border-gray-200 shadow-xs p-4 sm:p-6 lg:sticky lg:top-8">
                    <h3 class="font-semibold text-gray-900 mb-4 text-sm sm:text-base">Meal Record Details</h3>
                    
                    <div class="mb-6">
                        <label for="date" class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">Select Date <span class="text-red-600">*</span></label>
                        <input 
                            type="date" 
                            id="date" 
                            name="date" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-transparent @error('date') border-red-500 @enderror"
                            value="{{ old('date', today()->toDateString()) }}"
                            required>
                        @error('date')
                            <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="bg-sky-50 border border-sky-200 rounded-lg p-3 sm:p-4">
                        <p class="text-xs text-sky-700 font-medium mb-1">Active Month</p>
                        <p class="text-base sm:text-lg font-semibold text-sky-900">{{ $activeMonth?->name ?? 'N/A' }}</p>
                        @if ($activeMonth?->end_date)
                            <p class="text-xs text-sky-600 mt-2">
                                Until {{ $activeMonth->end_date->format('M d, Y') }}
                            </p>
                        @endif
                    </div>

                    <div class="mt-6 p-3 sm:p-4 bg-gray-50 rounded-lg border border-gray-200">
                        <p class="text-xs text-gray-600 font-medium mb-2">💡 Tip</p>
                        <p class="text-xs text-gray-600 leading-relaxed">Use the form below to add breakfast, lunch, and dinner counts for each member in your mess.</p>
                    </div>
                </div>
            </div>

            <!-- Right Content: Members Meal Entry -->
            <div class="lg:col-span-3">
                <div class="bg-white rounded-lg border border-gray-200 shadow-xs overflow-hidden">
                    <div class="px-4 sm:px-6 py-3 sm:py-4 bg-gray-50 border-b border-gray-200">
                        <h2 class="text-xs sm:text-sm font-semibold text-gray-900">Member Meal Counts</h2>
                        <p class="text-xs text-gray-500 mt-1">Enter meal counts for each member</p>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full min-w-full">
                            <thead class="bg-gray-50 border-b border-gray-200">
                                <tr>
                                    <th class="px-3 sm:px-6 py-2 sm:py-3 text-left font-medium text-gray-700 text-xs uppercase tracking-wider whitespace-nowrap">Member</th>
                                    <th class="px-2 sm:px-6 py-2 sm:py-3 text-center font-medium text-gray-700 text-xs uppercase tracking-wider whitespace-nowrap">B</th>
                                    <th class="px-2 sm:px-6 py-2 sm:py-3 text-center font-medium text-gray-700 text-xs uppercase tracking-wider whitespace-nowrap">L</th>
                                    <th class="px-2 sm:px-6 py-2 sm:py-3 text-center font-medium text-gray-700 text-xs uppercase tracking-wider whitespace-nowrap">D</th>
                                    <th class="px-2 sm:px-6 py-2 sm:py-3 text-center font-medium text-gray-700 text-xs uppercase tracking-wider whitespace-nowrap">Total</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @forelse ($members as $member)
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-3 sm:px-6 py-3 sm:py-4">
                                            <span class="inline-flex items-center px-2 sm:px-2.5 py-0.5 rounded-full text-xs font-medium bg-sky-100 text-sky-800 truncate">
                                                {{ $member->name }}
                                            </span>
                                        </td>

                                        <!-- Breakfast Counter -->
                                        <td class="px-2 sm:px-6 py-3 sm:py-4">
                                            <div class="flex items-center justify-center gap-1 sm:gap-2">
                                                <button type="button" class="p-1 sm:p-1.5 text-gray-600 hover:bg-gray-100 rounded-lg transition-colors flex-shrink-0" onclick="decrementMeal('breakfast_' + {{ $member->id }})">
                                                    <svg class="w-3 h-3 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                                                    </svg>
                                                </button>
                                                <input 
                                                    type="number" 
                                                    id="breakfast_{{ $member->id }}" 
                                                    name="meals[{{ $member->id }}][breakfast_count]" 
                                                    class="w-16 sm:w-20 text-center border border-gray-300 rounded-lg text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                                    value="0" 
                                                    min="0"
                                                    step="0.5"
                                                    onchange="updateTotal({{ $member->id }})">
                                                <button type="button" class="p-1 sm:p-1.5 text-gray-600 hover:bg-gray-100 rounded-lg transition-colors flex-shrink-0" onclick="incrementMeal('breakfast_' + {{ $member->id }})">
                                                    <svg class="w-3 h-3 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                                    </svg>
                                                </button>
                                            </div>
                                        </td>

                                        <!-- Lunch Counter -->
                                        <td class="px-2 sm:px-6 py-3 sm:py-4">
                                            <div class="flex items-center justify-center gap-1 sm:gap-2">
                                                <button type="button" class="p-1 sm:p-1.5 text-gray-600 hover:bg-gray-100 rounded-lg transition-colors flex-shrink-0" onclick="decrementMeal('lunch_' + {{ $member->id }})">
                                                    <svg class="w-3 h-3 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                                                    </svg>
                                                </button>
                                                <input 
                                                    type="number" 
                                                    id="lunch_{{ $member->id }}" 
                                                    name="meals[{{ $member->id }}][lunch_count]" 
                                                    class="w-16 sm:w-20 text-center border border-gray-300 rounded-lg text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-transparent"
                                                    value="0" 
                                                    min="0"
                                                    step="0.5"
                                                    onchange="updateTotal({{ $member->id }})">
                                                <button type="button" class="p-1 sm:p-1.5 text-gray-600 hover:bg-gray-100 rounded-lg transition-colors flex-shrink-0" onclick="incrementMeal('lunch_' + {{ $member->id }})">
                                                    <svg class="w-3 h-3 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                                    </svg>
                                                </button>
                                            </div>
                                        </td>

                                        <!-- Dinner Counter -->
                                        <td class="px-2 sm:px-6 py-3 sm:py-4">
                                            <div class="flex items-center justify-center gap-1 sm:gap-2">
                                                <button type="button" class="p-1 sm:p-1.5 text-gray-600 hover:bg-gray-100 rounded-lg transition-colors flex-shrink-0" onclick="decrementMeal('dinner_' + {{ $member->id }})">
                                                    <svg class="w-3 h-3 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                                                    </svg>
                                                </button>
                                                <input 
                                                    type="number" 
                                                    id="dinner_{{ $member->id }}" 
                                                    name="meals[{{ $member->id }}][dinner_count]" 
                                                    class="w-16 sm:w-20 text-center border border-gray-300 rounded-lg text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                                                    value="0" 
                                                    min="0"
                                                    step="0.5"
                                                    onchange="updateTotal({{ $member->id }})">
                                                <button type="button" class="p-1 sm:p-1.5 text-gray-600 hover:bg-gray-100 rounded-lg transition-colors flex-shrink-0" onclick="incrementMeal('dinner_' + {{ $member->id }})">
                                                    <svg class="w-3 h-3 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                                    </svg>
                                                </button>
                                            </div>
                                        </td>

                                        <!-- Total -->
                                        <td class="px-2 sm:px-6 py-3 sm:py-4 text-center">
                                            <span id="total_{{ $member->id }}" class="inline-flex items-center px-2 sm:px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                                0.0
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-4 sm:px-6 py-8 sm:py-12 text-center">
                                            <p class="text-gray-600 font-medium mb-2 text-sm">No members found in your mess</p>
                                            <p class="text-gray-500 text-xs sm:text-sm">Add members first to record meals</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-col-reverse sm:flex-row justify-end gap-3 bottom-6 py-3 sm:py-0 px-2 sm:px-0">
            <a href="{{ route('meals.index') }}" class="px-4 sm:px-6 py-2 bg-gray-200 text-gray-900 font-medium rounded-lg hover:bg-gray-300 transition-colors flex items-center justify-center sm:justify-start gap-2 text-xs sm:text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
                Cancel
            </a>
            <button type="submit" class="px-4 sm:px-6 py-2 bg-sky-600 text-white font-medium rounded-lg hover:bg-sky-700 transition-colors flex items-center justify-center sm:justify-start gap-2 text-xs sm:text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                Save Meal Records
            </button>
        </div>
    </form>
</div>

<script>
    function incrementMeal(fieldId) {
        const field = document.getElementById(fieldId);
        field.value = (parseFloat(field.value) + 0.5).toFixed(1);
        const memberId = fieldId.split('_')[1];
        updateTotal(memberId);
    }

    function decrementMeal(fieldId) {
        const field = document.getElementById(fieldId);
        const value = parseFloat(field.value);
        if (value > 0) {
            field.value = Math.max(0, (value - 0.5).toFixed(1));
            const memberId = fieldId.split('_')[1];
            updateTotal(memberId);
        }
    }

    function updateTotal(memberId) {
        const breakfast = parseFloat(document.getElementById('breakfast_' + memberId).value) || 0;
        const lunch = parseFloat(document.getElementById('lunch_' + memberId).value) || 0;
        const dinner = parseFloat(document.getElementById('dinner_' + memberId).value) || 0;
        const total = (breakfast + lunch + dinner).toFixed(1);
        
        document.getElementById('total_' + memberId).textContent = total;
    }
</script>
@else
    <div class="w-full px-2 sm:px-4 py-4 sm:py-8">
        <div class="p-3 sm:p-4 bg-red-50 border border-red-200 rounded-lg flex items-center gap-3">
            <svg class="w-4 h-4 sm:w-5 sm:h-5 text-red-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M13.477 14.89A6 6 0 015.11 2.476a6 6 0 018.367 8.414zm1.414-5.27a8 8 0 11-11.313-11.313 8 8 0 0111.313 11.313z" clip-rule="evenodd"></path>
            </svg>
            <span class="text-xs sm:text-sm text-red-800">You don't have permission to create meals.</span>
        </div>
        <a href="{{ route('meals.index') }}" class="text-sky-600 hover:text-sky-800 font-medium flex items-center gap-1 mt-4 text-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            Back to Meal Records
        </a>
    </div>
@endcan
@endsection
