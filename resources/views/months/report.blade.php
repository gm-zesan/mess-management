@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold">{{ $month->name }} - Detailed Monthly Report</h1>
        <div class="flex gap-2">
            <button onclick="window.print()" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Print</button>
            <a href="{{ route('months.show', $month->id) }}" class="bg-gray-400 hover:bg-gray-500 text-white font-bold py-2 px-4 rounded">Back to Summary</a>
        </div>
    </div>

    <!-- Month Details Card -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-6">
        <h2 class="text-xl font-bold text-blue-900 mb-4">Month Details</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div>
                <p class="text-sm text-blue-700">Period</p>
                <p class="text-lg font-semibold text-blue-900">{{ $month->start_date->format('M d') }} - {{ $month->end_date->format('M d, Y') }}</p>
            </div>
            <div>
                <p class="text-sm text-blue-700">Total Meals</p>
                <p class="text-lg font-semibold text-blue-900">{{ $totalMeals }}</p>
            </div>
            <div>
                <p class="text-sm text-blue-700">Total Expenses</p>
                <p class="text-lg font-semibold text-red-600">৳{{ number_format($totalExpenses, 2) }}</p>
            </div>
            <div>
                <p class="text-sm text-blue-700">Cost per Meal</p>
                <p class="text-lg font-semibold text-purple-600">৳{{ number_format($costPerMeal, 2) }}</p>
            </div>
        </div>
    </div>

    <!-- Member-wise Detailed Reports -->
    @foreach ($memberDetails as $memberId => $details)
        @if($details['total_meal_count'] > 0 || count($details['deposits']) > 0)
        <div class="bg-white rounded-lg shadow mb-6 overflow-hidden">
            <!-- Member Header -->
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 text-white p-6">
                <div class="flex justify-between items-center">
                    <h3 class="text-2xl font-bold">{{ $details['name'] }}</h3>
                    <div class="text-right">
                        <p class="text-sm opacity-90">Balance</p>
                        <p class="text-3xl font-bold {{ $details['balance'] >= 0 ? 'text-green-300' : 'text-red-300' }}">
                            ৳{{ number_format($details['balance'], 2) }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="p-6">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                    <div class="bg-gray-50 p-4 rounded">
                        <p class="text-sm text-gray-600">Total Meals</p>
                        <p class="text-2xl font-bold text-blue-600">{{ $details['total_meal_count'] }}</p>
                    </div>
                    <div class="bg-gray-50 p-4 rounded">
                        <p class="text-sm text-gray-600">Meal Cost</p>
                        <p class="text-2xl font-bold text-red-600">৳{{ number_format($details['meal_cost'], 2) }}</p>
                    </div>
                    <div class="bg-gray-50 p-4 rounded">
                        <p class="text-sm text-gray-600">Total Deposited</p>
                        <p class="text-2xl font-bold text-green-600">৳{{ number_format($details['total_deposited'], 2) }}</p>
                    </div>
                    <div class="bg-gray-50 p-4 rounded">
                        <p class="text-sm text-gray-600">Status</p>
                        <p class="text-lg font-bold {{ $details['balance'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                            {{ $details['balance'] >= 0 ? ucfirst($details['total_deposited'] > $details['meal_cost'] ? 'Paid' : 'Balanced') : 'Dues' }}
                        </p>
                    </div>
                </div>

                <!-- Meals Section -->
                @if(count($details['meals']) > 0)
                    <div class="mb-6">
                        <h4 class="text-lg font-semibold text-gray-800 mb-3 pb-2 border-b">Meal Records</h4>
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th class="px-4 py-2 text-left font-semibold text-gray-700">Date</th>
                                        <th class="px-4 py-2 text-right font-semibold text-gray-700">Meal Count</th>
                                        <th class="px-4 py-2 text-right font-semibold text-gray-700">Meal Cost</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($details['meals'] as $meal)
                                    <tr class="border-b hover:bg-gray-50">
                                        <td class="px-4 py-2 text-gray-800">{{ $meal->date->format('M d, Y') }}</td>
                                        <td class="px-4 py-2 text-right text-gray-800">{{ $meal->meal_count }}</td>
                                        <td class="px-4 py-2 text-right text-red-600 font-semibold">৳{{ number_format($meal->meal_count * $costPerMeal, 2) }}</td>
                                    </tr>
                                    @endforeach
                                    <tr class="bg-gray-50 font-semibold">
                                        <td class="px-4 py-2 text-gray-800">Total Meals</td>
                                        <td class="px-4 py-2 text-right text-gray-800">{{ $details['total_meal_count'] }}</td>
                                        <td class="px-4 py-2 text-right text-red-600">৳{{ number_format($details['meal_cost'], 2) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif

                <!-- Deposits Section -->
                @if(count($details['deposits']) > 0)
                    <div class="mb-6">
                        <h4 class="text-lg font-semibold text-gray-800 mb-3 pb-2 border-b">Deposit Records</h4>
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th class="px-4 py-2 text-left font-semibold text-gray-700">Date</th>
                                        <th class="px-4 py-2 text-right font-semibold text-gray-700">Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($details['deposits'] as $deposit)
                                    <tr class="border-b hover:bg-gray-50">
                                        <td class="px-4 py-2 text-gray-800">{{ $deposit->date->format('M d, Y') }}</td>
                                        <td class="px-4 py-2 text-right text-green-600 font-semibold">৳{{ number_format($deposit->amount, 2) }}</td>
                                    </tr>
                                    @endforeach
                                    <tr class="bg-gray-50 font-semibold">
                                        <td class="px-4 py-2 text-gray-800">Total Deposited</td>
                                        <td class="px-4 py-2 text-right text-green-600">৳{{ number_format($details['total_deposited'], 2) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif

                <!-- No Data -->
                @if(count($details['meals']) == 0 && count($details['deposits']) == 0)
                    <div class="text-center text-gray-500 py-4">
                        No meal or deposit records for this member in this month.
                    </div>
                @endif
            </div>
        </div>
        @endif
    @endforeach

    <!-- Overall Summary Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden mt-6">
        <div class="p-6 border-b border-gray-200 bg-gray-50">
            <h2 class="text-xl font-bold">Overall Member Summary</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Member</th>
                        <th class="px-6 py-3 text-right text-sm font-semibold text-gray-700">Meals</th>
                        <th class="px-6 py-3 text-right text-sm font-semibold text-gray-700">Meal Cost</th>
                        <th class="px-6 py-3 text-right text-sm font-semibold text-gray-700">Deposited</th>
                        <th class="px-6 py-3 text-right text-sm font-semibold text-gray-700">Balance</th>
                        <th class="px-6 py-3 text-center text-sm font-semibold text-gray-700">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($memberDetails as $memberId => $details)
                    @if($details['total_meal_count'] > 0 || count($details['deposits']) > 0)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $details['name'] }}</td>
                        <td class="px-6 py-4 text-sm text-right text-gray-800">{{ $details['total_meal_count'] }}</td>
                        <td class="px-6 py-4 text-sm text-right text-red-600 font-semibold">৳{{ number_format($details['meal_cost'], 2) }}</td>
                        <td class="px-6 py-4 text-sm text-right text-green-600 font-semibold">৳{{ number_format($details['total_deposited'], 2) }}</td>
                        <td class="px-6 py-4 text-sm text-right font-bold {{ $details['balance'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                            ৳{{ number_format($details['balance'], 2) }}
                        </td>
                        <td class="px-6 py-4 text-sm text-center">
                            @if($details['balance'] > 0)
                                <span class="px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">Credit</span>
                            @elseif($details['balance'] < 0)
                                <span class="px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800">Dues</span>
                            @else
                                <span class="px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-800">Settled</span>
                            @endif
                        </td>
                    </tr>
                    @endif
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<style type="text/css" media="print">
    @page {
        size: A4;
        margin: 1cm;
    }
    button {
        display: none;
    }
</style>
@endsection
