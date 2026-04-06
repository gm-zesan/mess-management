@extends('layouts.app')

@section('content')
    @can('reports.view')
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">

            <!-- Header -->
            <div class="mb-6 flex flex-col sm:flex-row gap-3 items-start sm:items-center justify-between">
                <div>
                    <h2 class="text-3xl font-bold text-gray-900">{{ $month->name }}</h2>
                    <p class="text-sm text-gray-600 mt-1">{{ $month->start_date->format('d M Y') }} to {{ $month->end_date->format('d M Y') }}</p>
                </div>
                <div class="flex gap-2">
                    <button onclick="window.print()" class="inline-flex items-center gap-2 px-4 py-2 bg-sky-600 hover:bg-sky-700 text-white text-sm font-semibold rounded-lg transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                        </svg>
                        Print
                    </button>
                    <a href="{{ route('reports.all-months') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm font-semibold rounded-lg transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                        Back
                    </a>
                </div>
            </div>

            <!-- Summary Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <div class="bg-white rounded-lg border border-gray-200 p-4">
                    <p class="text-xs font-semibold text-gray-600 mb-2">Total Meals</p>
                    <p class="text-2xl font-bold text-sky-600">{{ $summary['total_meals'] }}</p>
                </div>
                <div class="bg-white rounded-lg border border-gray-200 p-4">
                    <p class="text-xs font-semibold text-gray-600 mb-2">Total Expenses</p>
                    <p class="text-2xl font-bold text-red-600">৳ {{ number_format($summary['total_expenses'], 2) }}</p>
                </div>
                <div class="bg-white rounded-lg border border-gray-200 p-4">
                    <p class="text-xs font-semibold text-gray-600 mb-2">Meal Rate</p>
                    <p class="text-2xl font-bold text-blue-600">৳ {{ number_format($summary['meal_rate'], 2) }}</p>
                </div>
                <div class="bg-white rounded-lg border border-gray-200 p-4">
                    <p class="text-xs font-semibold text-gray-600 mb-2">Total Deposits</p>
                    <p class="text-2xl font-bold text-green-600">৳ {{ number_format($summary['total_deposits'], 2) }}</p>
                </div>
            </div>

            <!-- Member Balance Table -->
            <div class="bg-white rounded-lg border border-gray-200 shadow-xs overflow-hidden mb-6">
                <div class="px-4 py-3 bg-gray-50 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Member-wise Balance</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-4 py-3 text-left font-semibold text-gray-700 text-xs">Member</th>
                                <th class="px-4 py-3 text-center font-semibold text-gray-700 text-xs">Meals</th>
                                <th class="px-4 py-3 text-right font-semibold text-gray-700 text-xs">Meal Cost</th>
                                <th class="px-4 py-3 text-right font-semibold text-gray-700 text-xs">Deposited</th>
                                <th class="px-4 py-3 text-right font-semibold text-gray-700 text-xs">Balance</th>
                                <th class="px-4 py-3 text-center font-semibold text-gray-700 text-xs">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($summary['member_balances'] as $balance)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-4 py-3 text-gray-900 font-medium">{{ $balance['member_name'] }}</td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="inline-block px-2 py-1 bg-blue-100 text-blue-800 text-xs font-semibold rounded">{{ $balance['meals'] }}</span>
                                    </td>
                                    <td class="px-4 py-3 text-right text-gray-700">৳ {{ number_format($balance['meal_cost'], 2) }}</td>
                                    <td class="px-4 py-3 text-right text-gray-700">৳ {{ number_format($balance['deposited'], 2) }}</td>
                                    <td class="px-4 py-3 text-right font-semibold {{ $balance['balance'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                        ৳ {{ number_format($balance['balance'], 2) }}
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        @if($balance['status'] === 'credit')
                                            <span class="inline-block px-2 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded">Credit</span>
                                        @elseif($balance['status'] === 'due')
                                            <span class="inline-block px-2 py-1 bg-red-100 text-red-800 text-xs font-semibold rounded">Due</span>
                                        @else
                                            <span class="inline-block px-2 py-1 bg-gray-100 text-gray-800 text-xs font-semibold rounded">Settled</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                                        No members found in this month
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Net Balance Alert -->
            <div class="mb-6 p-4 {{ $summary['net_balance'] >= 0 ? 'bg-green-50 border border-green-200' : 'bg-red-50 border border-red-200' }} rounded-lg flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <svg class="w-6 h-6 {{ $summary['net_balance'] >= 0 ? 'text-green-600' : 'text-red-600' }}" fill="currentColor" viewBox="0 0 20 20">
                        @if($summary['net_balance'] >= 0)
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        @else
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                        @endif
                    </svg>
                    <div>
                        <p class="text-sm font-semibold {{ $summary['net_balance'] >= 0 ? 'text-green-800' : 'text-red-800' }}">
                            {{ $summary['net_balance'] >= 0 ? 'Surplus' : 'Deficit' }}
                        </p>
                        <p class="text-xs {{ $summary['net_balance'] >= 0 ? 'text-green-700' : 'text-red-700' }}">
                            Net balance for this month
                        </p>
                    </div>
                </div>
                <p class="text-2xl font-bold {{ $summary['net_balance'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                    ৳ {{ number_format(abs($summary['net_balance']), 2) }}
                </p>
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
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="p-4 bg-red-50 border border-red-200 rounded-lg flex items-center gap-3 mb-4">
                <svg class="w-5 h-5 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M13.477 14.89A6 6 0 015.11 2.476a6 6 0 018.367 8.414zm1.414-5.27a8 8 0 11-11.313-11.313 8 8 0 0111.313 11.313z" clip-rule="evenodd"></path>
                </svg>
                <span class="text-sm text-red-800">You don't have permission to view reports.</span>
            </div>
            <a href="{{ route('reports.all-months') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm font-semibold rounded-lg transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                Back
            </a>
        </div>
    @endcan
@endsection
