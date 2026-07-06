@extends('layouts.app')

@section('content')
    @can('reports.view')
        <div class="w-full px-2 sm:px-4 py-3 sm:py-6">
            <!-- Summary Section -->
            <div class="bg-white border border-gray-200 shadow-xs p-3 sm:p-6 mb-4 sm:mb-6">
                <div class="mb-3 sm:mb-6 text-center">
                    <h3 class="text-xs sm:text-md font-bold text-gray-900">Current Month Details of {{ $month->mess->name }}</h3>
                    <h2 class="text-sm sm:text-lg font-bold text-gray-900">{{ $month->name }}</h2>
                </div>
                
                <div class="grid grid-cols-2 gap-2 sm:gap-12">
                    <!-- Left Column -->
                    <div class="space-y-1 sm:space-y-2">
                        <div>
                            <p class="text-xs sm:text-sm text-gray-700">Current Balance: <span class="font-semibold">{{ number_format($summary['net_balance'], 2) }} tk</span></p>
                        </div>
                        
                        <div>
                            <p class="text-xs sm:text-sm text-gray-700">Total Meal: <span class="font-semibold">{{ $summary['total_meals'] }}</span></p>
                        </div>
                        
                        <div>
                            <p class="text-xs sm:text-sm text-gray-700">Total Cost: <span class="font-semibold">{{ number_format($summary['total_expenses'], 2) }} tk</span></p>
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div class="space-y-1 sm:space-y-2 text-right">
                        <div>
                            <p class="text-xs sm:text-sm text-gray-700">Date: <span class="font-semibold">{{ now()->format('d M Y') }}</span></p>
                        </div>
                        
                        
                        <div>
                            <p class="text-xs sm:text-sm text-gray-700">Total Deposit: <span class="font-semibold">{{ number_format($summary['total_deposits'], 2) }} tk</span></p>
                        </div>
                        
                        <div>
                            <p class="text-xs sm:text-sm text-gray-700">Meal Rate: <span class="font-semibold">{{ number_format($summary['meal_rate'], 2) }} tk</span></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Member Balance Table -->
            <div class="bg-white border border-gray-200 shadow-xs overflow-hidden mb-4 sm:mb-6">
                <div class="px-2 sm:px-4 py-2 sm:py-3 bg-gray-50 border-b border-gray-200">
                    <h3 class="text-xs sm:text-lg font-semibold text-gray-900">Member Summary Info</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-xs sm:text-sm border-collapse min-w-full">
                        <thead class="bg-white border border-gray-300">
                            <tr>
                                <th class="px-1 sm:px-4 py-1 sm:py-2 text-center font-semibold text-gray-700 text-xs border border-gray-300">Name</th>
                                <th class="px-1 sm:px-4 py-1 sm:py-2 text-center font-semibold text-gray-700 text-xs border border-gray-300">Meals</th>
                                <th class="px-1 sm:px-4 py-1 sm:py-2 text-center font-semibold text-gray-700 text-xs border border-gray-300">Deposit</th>
                                <th class="px-1 sm:px-4 py-1 sm:py-2 text-center font-semibold text-gray-700 text-xs border border-gray-300">Meal Cost</th>
                                <th class="px-1 sm:px-4 py-1 sm:py-2 text-center font-semibold text-gray-700 text-xs border border-gray-300">Balance</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white">
                            @forelse($summary['member_balances'] as $balance)
                                <tr class="border border-gray-300">
                                    <td class="px-1 sm:px-4 py-1 sm:py-2 text-center text-gray-700 border border-gray-300 text-xs sm:text-sm">{{ $balance['member_name'] }}</td>
                                    <td class="px-1 sm:px-4 py-1 sm:py-2 text-center text-gray-700 border border-gray-300 text-xs sm:text-sm">{{ $balance['meals'] }}</td>
                                    <td class="px-1 sm:px-4 py-1 sm:py-2 text-center text-gray-700 border border-gray-300 text-xs sm:text-sm">{{ number_format($balance['deposited'], 0) }}</td>
                                    <td class="px-1 sm:px-4 py-1 sm:py-2 text-center text-gray-700 border border-gray-300 text-xs sm:text-sm">{{ number_format($balance['meal_cost'], 0) }}</td>
                                    <td class="px-1 sm:px-4 py-1 sm:py-2 text-center text-gray-700 border border-gray-300 text-xs sm:text-sm">{{ number_format($balance['balance'], 0) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-2 sm:px-4 py-4 sm:py-8 text-center text-gray-500 text-xs sm:text-sm">
                                        No members found in this month
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Meal Table -->
            <div class="bg-white border border-gray-200 shadow-xs overflow-hidden mb-4 sm:mb-6">
                <div class="px-2 sm:px-4 py-2 sm:py-3 bg-gray-50 border-b border-gray-200">
                    <h3 class="text-xs sm:text-lg font-semibold text-gray-900">Meal Table :</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full table-auto border-collapse text-xs">
                        <thead>
                            <tr>
                                <th class="border border-gray-300 px-1 sm:px-2 py-1 sm:py-2 text-center font-semibold bg-white text-gray-700 sticky left-0 z-10 w-16 sm:w-24 text-xs">Name</th>
                                @for($day = 1; $day <= $month->end_date->day; $day++)
                                    <th class="border border-gray-300 px-0.5 sm:px-1 py-1 sm:py-2 text-center font-semibold bg-white text-gray-700 w-6 sm:w-8 text-xs">{{ $day }}</th>
                                @endfor
                                <th class="border border-gray-300 px-1 sm:px-2 py-1 sm:py-2 text-center font-semibold bg-white text-gray-700 sticky right-0 z-10 w-10 sm:w-12 text-xs">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $meals = $month->meals()->with('user')->get();
                                $memberMeals = [];
                                $dailyTotals = array_fill(1, $month->end_date->day, 0);
                                
                                // Organize meals by member and day
                                foreach($summary['member_balances'] as $member) {
                                    $memberMeals[$member['user_id']] = array_fill(1, $month->end_date->day, 0);
                                }
                                
                                foreach($meals as $meal) {
                                    $day = $meal->date->day;
                                    $memberId = $meal->user_id;
                                    if(isset($memberMeals[$memberId])) {
                                        $memberMeals[$memberId][$day] += $meal->breakfast_count + $meal->lunch_count + $meal->dinner_count;
                                        $dailyTotals[$day] += $meal->breakfast_count + $meal->lunch_count + $meal->dinner_count;
                                    }
                                }
                            @endphp
                            
                            @forelse($summary['member_balances'] as $balance)
                                <tr>
                                    <td class="border border-gray-300 px-1 sm:px-2 py-1 sm:py-2 text-center font-medium bg-gray-50 sticky left-0 z-10 text-gray-700 text-xs">{{ substr($balance['member_name'], 0, 10) }}</td>
                                    @for($day = 1; $day <= $month->end_date->day; $day++)
                                        <td class="border border-gray-300 px-0.5 sm:px-1 py-1 sm:py-2 text-center text-gray-700 text-xs">
                                            @php
                                                $mealCount = $memberMeals[$balance['user_id']][$day] ?? 0;
                                                echo $mealCount > 0 ? $mealCount : '';
                                            @endphp
                                        </td>
                                    @endfor
                                    <td class="border border-gray-300 px-1 sm:px-2 py-1 sm:py-2 text-center font-semibold bg-white sticky right-0 z-10 text-gray-700 text-xs">{{ $balance['meals'] }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ $month->end_date->day + 2 }}" class="px-2 sm:px-4 py-4 sm:py-8 text-center text-gray-500 text-xs sm:text-sm">
                                        No members found
                                    </td>
                                </tr>
                            @endforelse
                            
                            <tr class="bg-gray-50">
                                <td class="border border-gray-300 px-1 sm:px-2 py-1 sm:py-2 text-center font-semibold text-gray-700 sticky left-0 z-10 text-xs">Total</td>
                                @for($day = 1; $day <= $month->end_date->day; $day++)
                                    <td class="border border-gray-300 px-0.5 sm:px-1 py-1 sm:py-2 text-center font-semibold text-gray-700 text-xs">
                                        @php
                                            echo $dailyTotals[$day] > 0 ? $dailyTotals[$day] : '';
                                        @endphp
                                    </td>
                                @endfor
                                <td class="border border-gray-300 px-1 sm:px-2 py-1 sm:py-2 text-center font-semibold bg-gray-50 sticky right-0 z-10 text-gray-700 text-xs">{{ $summary['total_meals'] }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Meal/Bazar Cost Table -->
            <div class="bg-white border border-gray-200 shadow-xs overflow-hidden mb-4 sm:mb-6">
                <div class="px-2 sm:px-4 py-2 sm:py-3 bg-gray-50 border-b border-gray-200">
                    <h3 class="text-xs sm:text-lg font-semibold text-gray-900">Meal/Bazar Cost Table :</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-xs sm:text-sm border-collapse min-w-full">
                        <thead class="bg-white border border-gray-300">
                            <tr>
                                <th class="border border-gray-300 px-1 sm:px-4 py-1 sm:py-2 text-center font-semibold text-gray-700 text-xs">Name</th>
                                <th class="border border-gray-300 px-1 sm:px-4 py-1 sm:py-2 text-center font-semibold text-gray-700 text-xs">Date</th>
                                <th class="border border-gray-300 px-1 sm:px-4 py-1 sm:py-2 text-center font-semibold text-gray-700 text-xs">Amount</th>
                                <th class="border border-gray-300 px-1 sm:px-4 py-1 sm:py-2 text-center font-semibold text-gray-700 text-xs">Bazar Details</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white">
                            @php
                                $mealExpenses = $month->expenses()->orderBy('created_at')->get();
                            @endphp
                            @forelse($mealExpenses as $expense)
                                <tr class="border border-gray-300">
                                    <td class="border border-gray-300 px-1 sm:px-4 py-1 sm:py-2 text-center text-gray-700 text-xs sm:text-sm">{{ $expense->user->name ?? 'N/A' }}</td>
                                    <td class="border border-gray-300 px-1 sm:px-4 py-1 sm:py-2 text-center text-gray-700 text-xs sm:text-sm">{{ $expense->created_at->format('Y-m-d') }}</td>
                                    <td class="border border-gray-300 px-1 sm:px-4 py-1 sm:py-2 text-right text-gray-700 text-xs sm:text-sm">{{ number_format($expense->amount, 2) }}</td>
                                    <td class="border border-gray-300 px-1 sm:px-4 py-1 sm:py-2 text-left text-gray-700 text-xs sm:text-sm truncate">{{ $expense->note ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-2 sm:px-4 py-4 sm:py-8 text-center text-gray-500 text-xs sm:text-sm">
                                        No meal/bazar costs recorded
                                    </td>
                                </tr>
                            @endforelse
                            <tr class="bg-gray-50 border border-gray-300">
                                <td colspan="2" class="border border-gray-300 px-1 sm:px-4 py-1 sm:py-2 text-center font-semibold text-gray-700 text-xs">Total Meal Cost</td>
                                <td class="border border-gray-300 px-1 sm:px-4 py-1 sm:py-2 text-right font-semibold text-gray-700 text-xs">{{ number_format($mealExpenses->sum('amount'), 2) }}</td>
                                <td class="border border-gray-300 px-1 sm:px-4 py-1 sm:py-2"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Deposit Table -->
            <div class="bg-white border border-gray-200 shadow-xs overflow-hidden mb-4 sm:mb-6">
                <div class="px-2 sm:px-4 py-2 sm:py-3 bg-gray-50 border-b border-gray-200">
                    <h3 class="text-xs sm:text-lg font-semibold text-gray-900">Deposit Table :</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-xs sm:text-sm border-collapse min-w-full">
                        <thead class="bg-white border border-gray-300">
                            <tr>
                                <th class="border border-gray-300 px-1 sm:px-4 py-1 sm:py-2 text-center font-semibold text-gray-700 text-xs">Name</th>
                                <th class="border border-gray-300 px-1 sm:px-4 py-1 sm:py-2 text-center font-semibold text-gray-700 text-xs">Date</th>
                                <th class="border border-gray-300 px-1 sm:px-4 py-1 sm:py-2 text-center font-semibold text-gray-700 text-xs">Amount</th>
                                <th class="border border-gray-300 px-1 sm:px-4 py-1 sm:py-2 text-center font-semibold text-gray-700 text-xs">Remarks</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white">
                            @php
                                $deposits = $month->deposits()->orderBy('created_at')->get();
                            @endphp
                            @forelse($deposits as $deposit)
                                <tr class="border border-gray-300">
                                    <td class="border border-gray-300 px-1 sm:px-4 py-1 sm:py-2 text-center text-gray-700 text-xs sm:text-sm">{{ $deposit->user->name ?? 'N/A' }}</td>
                                    <td class="border border-gray-300 px-1 sm:px-4 py-1 sm:py-2 text-center text-gray-700 text-xs sm:text-sm">{{ $deposit->created_at->format('Y-m-d') }}</td>
                                    <td class="border border-gray-300 px-1 sm:px-4 py-1 sm:py-2 text-right text-gray-700 text-xs sm:text-sm">{{ number_format($deposit->amount, 2) }}</td>
                                    <td class="border border-gray-300 px-1 sm:px-4 py-1 sm:py-2 text-left text-gray-700 text-xs sm:text-sm truncate">{{ $deposit->remarks ?? 'Added Bazar Cost' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-2 sm:px-4 py-4 sm:py-8 text-center text-gray-500 text-xs sm:text-sm">
                                        No deposits recorded
                                    </td>
                                </tr>
                            @endforelse
                            <tr class="bg-gray-50 border border-gray-300">
                                <td colspan="2" class="border border-gray-300 px-1 sm:px-4 py-1 sm:py-2 text-center font-semibold text-gray-700 text-xs">Total Deposit</td>
                                <td class="border border-gray-300 px-1 sm:px-4 py-1 sm:py-2 text-right font-semibold text-gray-700 text-xs">{{ number_format($deposits->sum('amount'), 2) }}</td>
                                <td class="border border-gray-300 px-1 sm:px-4 py-1 sm:py-2"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        <style>
            @media print {
                .max-w-7xl {
                    max-width: 100%;
                }
                button, a.inline-flex {
                    display: none !important;
                }
                .px-4 {
                    padding-left: 1rem;
                    padding-right: 1rem;
                }
            }
        </style>
    @else
        <div class="w-full px-2 sm:px-4 py-4 sm:py-6">
            <div class="p-2 sm:p-4 bg-red-50 border border-red-200 rounded-lg flex items-center gap-2 sm:gap-3 mb-4">
                <svg class="w-4 h-4 sm:w-5 sm:h-5 text-red-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M13.477 14.89A6 6 0 015.11 2.476a6 6 0 018.367 8.414zm1.414-5.27a8 8 0 11-11.313-11.313 8 8 0 0111.313 11.313z" clip-rule="evenodd"></path>
                </svg>
                <span class="text-xs sm:text-sm text-red-800">You don't have permission to view reports.</span>
            </div>
            <a href="{{ route('reports.all-months') }}" class="inline-flex items-center gap-2 px-3 sm:px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white text-xs sm:text-sm font-semibold rounded-lg transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                Back
            </a>
        </div>
    @endcan
@endsection
