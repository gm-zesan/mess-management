@use('App\Enums\RoleEnum')
@extends('layouts.app')

@section('content')
    <!-- Main Container -->
    <div class="max-w-7xl mx-auto">
        @if ($activeMonth)
            @if ($summary)
                <!-- Summary Cards - Compact 4-Column -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 mb-6">
                    <!-- Total Meals -->
                    <div class="bg-white rounded-lg border border-gray-200 shadow-xs p-4 hover:shadow-sm transition-shadow">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs text-gray-500 font-medium">TOTAL MEALS</p>
                                <p class="text-2xl font-bold text-gray-900 mt-1">{{ $summary['total_meals'] }}</p>
                                <p class="text-xs text-gray-400 mt-1">All members</p>
                            </div>
                            <div class="p-2.5 bg-sky-100 rounded-lg">
                                <svg class="w-5 h-5 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Total Expenses -->
                    <div class="bg-white rounded-lg border border-gray-200 shadow-xs p-4 hover:shadow-sm transition-shadow">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs text-gray-500 font-medium">TOTAL EXPENSES</p>
                                <p class="text-2xl font-bold text-gray-900 mt-1">৳ {{ number_format($summary['total_expenses'], 0) }}</p>
                                <p class="text-xs text-gray-400 mt-1">Total cost</p>
                            </div>
                            <div class="p-2.5 bg-red-100 rounded-lg">
                                <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Total Deposits -->
                    <div class="bg-white rounded-lg border border-gray-200 shadow-xs p-4 hover:shadow-sm transition-shadow">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs text-gray-500 font-medium">TOTAL DEPOSITS</p>
                                <p class="text-2xl font-bold text-gray-900 mt-1">৳ {{ number_format($summary['total_deposits'], 0) }}</p>
                                <p class="text-xs text-gray-400 mt-1">Total paid</p>
                            </div>
                            <div class="p-2.5 bg-green-100 rounded-lg">
                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Meal Rate -->
                    <div class="bg-white rounded-lg border border-gray-200 shadow-xs p-4 hover:shadow-sm transition-shadow">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs text-gray-500 font-medium">MEAL RATE</p>
                                <p class="text-2xl font-bold text-gray-900 mt-1">৳ {{ number_format($summary['meal_rate'], 2) }}</p>
                                <p class="text-xs text-gray-400 mt-1">Per meal</p>
                            </div>
                            <div class="p-2.5 bg-purple-100 rounded-lg">
                                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
                @if(!isSuperAdminInMess())
                    <!-- Regular User View: Personal Summary -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 mb-6">
                        <!-- My Meals -->
                        <div class="bg-white rounded-lg border border-gray-200 shadow-xs p-4 hover:shadow-sm transition-shadow">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-xs text-gray-500 font-medium">MY MEALS</p>
                                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ $summary['user_meals'] ?? 0 }}</p>
                                    <p class="text-xs text-gray-400 mt-1">This month</p>
                                </div>
                                <div class="p-2.5 bg-sky-100 rounded-lg">
                                    <svg class="w-5 h-5 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <!-- My Meal Cost -->
                        <div class="bg-white rounded-lg border border-gray-200 shadow-xs p-4 hover:shadow-sm transition-shadow">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-xs text-gray-500 font-medium">MY MEAL COST</p>
                                    <p class="text-2xl font-bold text-gray-900 mt-1">৳ {{ number_format($summary['user_meal_cost'] ?? 0, 0) }}</p>
                                    <p class="text-xs text-gray-400 mt-1">Total cost</p>
                                </div>
                                <div class="p-2.5 bg-red-100 rounded-lg">
                                    <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <!-- My Deposit -->
                        <div class="bg-white rounded-lg border border-gray-200 shadow-xs p-4 hover:shadow-sm transition-shadow">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-xs text-gray-500 font-medium">MY DEPOSIT</p>
                                    <p class="text-2xl font-bold text-gray-900 mt-1">৳ {{ number_format($summary['user_deposit'] ?? 0, 0) }}</p>
                                    <p class="text-xs text-gray-400 mt-1">Total paid</p>
                                </div>
                                <div class="p-2.5 bg-green-100 rounded-lg">
                                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <!-- My Balance -->
                        <div class="bg-white rounded-lg border border-gray-200 shadow-xs p-4 hover:shadow-sm transition-shadow">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-xs text-gray-500 font-medium">MY BALANCE</p>
                                    <p class="text-2xl font-bold {{ ($summary['user_balance'] ?? 0) > 0 ? 'text-green-600' : (($summary['user_balance'] ?? 0) < 0 ? 'text-red-600' : 'text-gray-900') }} mt-1">৳ {{ number_format($summary['user_balance'] ?? 0, 0) }}</p>
                                    <p class="text-xs text-gray-400 mt-1">
                                        @if(($summary['user_balance'] ?? 0) > 0)
                                            Credit
                                        @elseif(($summary['user_balance'] ?? 0) < 0)
                                            Due
                                        @else
                                            Settled
                                        @endif
                                    </p>
                                </div>
                                <div class="p-2.5 bg-purple-100 rounded-lg">
                                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Charts Row -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-6">
                    <!-- Financial Distribution Pie -->
                    <div class="bg-white rounded-lg border border-gray-200 shadow-xs p-4">
                        <h3 class="text-sm font-semibold text-gray-900 mb-3">Financial Distribution</h3>
                        <div class="flex items-center justify-center py-6">
                            <div class="text-center">
                                @php
                                    $totalFinance = $summary['total_expenses'] + $summary['total_deposits'];
                                    $expensePercentage = $totalFinance > 0 ? ($summary['total_expenses'] / $totalFinance) * 100 : 0;
                                    $expenseDashArray = $totalFinance > 0 ? ($summary['total_expenses'] / $totalFinance) * 251.2 : 0;
                                @endphp
                                <div class="w-24 h-24 mx-auto mb-3">
                                    <svg viewBox="0 0 100 100" class="w-full h-full">
                                        <circle cx="50" cy="50" r="40" fill="none" stroke="#0284c7" stroke-width="8" stroke-dasharray="{{ $expenseDashArray }} 251.2"/>
                                        <circle cx="50" cy="50" r="40" fill="none" stroke="#10B981" stroke-width="8" stroke-dasharray="251.2" stroke-dashoffset="-{{ $expenseDashArray }}" opacity="0.4"/>
                                    </svg>
                                </div>
                                <p class="text-xs text-gray-600">
                                    <span class="font-semibold text-sky-600">{{ round($expensePercentage) }}%</span> Expenses
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Balance Status -->
                    <div class="bg-white rounded-lg border border-gray-200 shadow-xs p-4">
                        <h3 class="text-sm font-semibold text-gray-900 mb-3">Balance Status</h3>
                        <div class="space-y-3">
                            @php
                                $credits = collect($summary['member_balances'])->where('status', 'credit')->count();
                                $dues = collect($summary['member_balances'])->where('status', 'due')->count();
                                $settled = collect($summary['member_balances'])->where('status', 'settled')->count();
                            @endphp
                            <div>
                                <div class="flex items-center justify-between mb-1">
                                    <span class="text-xs font-medium text-gray-600">Credit</span>
                                    <span class="text-xs font-semibold text-green-600">{{ $credits }}</span>
                                </div>
                                <div class="w-full h-2 bg-gray-200 rounded-full overflow-hidden">
                                    <div class="h-full bg-green-500" style="width: {{ ($credits / max(1, count($summary['member_balances']))) * 100 }}%"></div>
                                </div>
                            </div>
                            <div>
                                <div class="flex items-center justify-between mb-1">
                                    <span class="text-xs font-medium text-gray-600">Due</span>
                                    <span class="text-xs font-semibold text-red-600">{{ $dues }}</span>
                                </div>
                                <div class="w-full h-2 bg-gray-200 rounded-full overflow-hidden">
                                    <div class="h-full bg-red-500" style="width: {{ ($dues / max(1, count($summary['member_balances']))) * 100 }}%"></div>
                                </div>
                            </div>
                            <div>
                                <div class="flex items-center justify-between mb-1">
                                    <span class="text-xs font-medium text-gray-600">Settled</span>
                                    <span class="text-xs font-semibold text-gray-600">{{ $settled }}</span>
                                </div>
                                <div class="w-full h-2 bg-gray-200 rounded-full overflow-hidden">
                                    <div class="h-full bg-gray-400" style="width: {{ ($settled / max(1, count($summary['member_balances']))) * 100 }}%"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Members Overview -->
                    <div class="bg-white rounded-lg border border-gray-200 shadow-xs p-4">
                        <h3 class="text-sm font-semibold text-gray-900 mb-3">Members Overview</h3>
                        <div class="space-y-2">
                            <div class="flex items-center justify-between p-2 bg-gray-50 rounded">
                                <span class="text-xs text-gray-600">Total Members</span>
                                <span class="text-sm font-bold text-gray-900">{{ count($summary['member_balances']) }}</span>
                            </div>
                            <div class="flex items-center justify-between p-2 bg-sky-50 rounded">
                                <span class="text-xs text-sky-700">Avg. Meals</span>
                                <span class="text-sm font-bold text-sky-900">{{ round($summary['total_meals'] / max(1, count($summary['member_balances']))) }}</span>
                            </div>
                            <div class="flex items-center justify-between p-2 bg-purple-50 rounded">
                                <span class="text-xs text-purple-700">Avg. Cost/Member</span>
                                <span class="text-sm font-bold text-purple-900">৳ {{ number_format($summary['total_expenses'] / max(1, count($summary['member_balances'])), 0) }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Member Balances Table - Compact -->
                <div class="bg-white rounded-lg border border-gray-200 shadow-xs overflow-hidden">
                    <div class="px-4 py-3 border-b border-gray-200 bg-gray-50">
                        <h2 class="text-sm font-semibold text-gray-900">Member Balances</h2>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50 border-b border-gray-200">
                                <tr>
                                    <th class="px-4 py-2 text-left font-medium text-gray-600 text-xs">Member</th>
                                    <th class="px-4 py-2 text-center font-medium text-gray-600 text-xs">Meals</th>
                                    <th class="px-4 py-2 text-right font-medium text-gray-600 text-xs">Cost</th>
                                    <th class="px-4 py-2 text-right font-medium text-gray-600 text-xs">Paid</th>
                                    <th class="px-4 py-2 text-right font-medium text-gray-600 text-xs">Balance</th>
                                    <th class="px-4 py-2 text-center font-medium text-gray-600 text-xs">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @forelse($summary['member_balances'] as $balance)
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-4 py-2.5 font-medium text-gray-900">{{ $balance['member_name'] }}</td>
                                        <td class="px-4 py-2.5 text-center">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-sky-100 text-sky-800">
                                                {{ $balance['meals'] }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-2.5 text-right text-gray-900 text-xs">৳ {{ number_format($balance['meal_cost'], 0) }}</td>
                                        <td class="px-4 py-2.5 text-right text-gray-900 text-xs">৳ {{ number_format($balance['deposited'], 0) }}</td>
                                        <td class="px-4 py-2.5 text-right font-semibold text-xs {{ $balance['balance'] > 0 ? 'text-green-600' : ($balance['balance'] < 0 ? 'text-red-600' : 'text-gray-600') }}">
                                            ৳ {{ number_format($balance['balance'], 0) }}
                                        </td>
                                        <td class="px-4 py-2.5 text-center">
                                            @if($balance['status'] === 'credit')
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">✓</span>
                                            @elseif($balance['status'] === 'due')
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">✗</span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">−</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-4 py-8 text-center text-gray-500 text-xs">
                                            No member data yet
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            @else
                <div class="p-4 bg-red-50 border border-red-200 rounded-lg text-sm text-red-800">
                    ⚠️ Unable to load summary data. Please try again.
                </div>
            @endif
        @else
            <!-- No Active Month State - Compact -->
            <div class="bg-white rounded-lg border border-gray-200 shadow-xs p-8 text-center">
                <svg class="w-10 h-10 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h18M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                <h3 class="text-base font-semibold text-gray-900 mb-1">No Active Month</h3>
                <p class="text-xs text-gray-600 mb-4">
                    There is currently no active month for <strong>{{ $activeMess?->name ?? 'your mess' }}</strong>.
                </p>
                <div class="flex gap-2 justify-center">
                    @if(auth()->user()->hasRole(RoleEnum::MANAGER->value))
                        <a href="{{ route('months.create') }}" class="px-3 py-1.5 bg-sky-600 text-white text-xs font-medium rounded-lg hover:bg-sky-700 transition-colors">
                            Create Month
                        </a>
                    @endif
                    <a href="{{ route('months.index') }}" class="px-3 py-1.5 bg-gray-200 text-gray-900 text-xs font-medium rounded-lg hover:bg-gray-300 transition-colors">
                        View All
                    </a>
                </div>
            </div>
        @endif
    </div>
@endsection
