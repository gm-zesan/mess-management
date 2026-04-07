@extends('layouts.app')

@php
use App\Enums\MonthStatusEnum;
@endphp

@section('content')
    @can('reports.view')
        <div class="w-full">

            @if ($months->count() > 0)
                <!-- Data Table -->
                <div class="bg-white rounded-lg border border-gray-200 shadow-xs overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50 border-b border-gray-200">
                                <tr>
                                    <th class="px-4 py-3 text-left font-semibold text-gray-700 text-xs">Month</th>
                                    <th class="px-4 py-3 text-center font-semibold text-gray-700 text-xs">Meals</th>
                                    <th class="px-4 py-3 text-right font-semibold text-gray-700 text-xs">Expenses</th>
                                    <th class="px-4 py-3 text-right font-semibold text-gray-700 text-xs">Meal Rate</th>
                                    <th class="px-4 py-3 text-right font-semibold text-gray-700 text-xs">Deposits</th>
                                    <th class="px-4 py-3 text-center font-semibold text-gray-700 text-xs">Members</th>
                                    <th class="px-4 py-3 text-right font-semibold text-gray-700 text-xs">Balance</th>
                                    <th class="px-4 py-3 text-center font-semibold text-gray-700 text-xs">Status</th>
                                    <th class="px-4 py-3 text-center font-semibold text-gray-700 text-xs">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @forelse($months as $month)
                                    @php
                                        $report = $reports[$month->id];
                                    @endphp
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-4 py-3">
                                            <div>
                                                <p class="font-semibold text-gray-900">{{ $month->name }}</p>
                                                <p class="text-xs text-gray-500 mt-1">{{ $month->start_date->format('M d') }} - {{ $month->end_date->format('M d, Y') }}</p>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <span class="inline-block px-2 py-1 bg-blue-100 text-blue-800 text-xs font-semibold rounded">{{ $report['total_meals'] }}</span>
                                        </td>
                                        <td class="px-4 py-3 text-right">
                                            <span class="font-semibold text-gray-900">৳ {{ number_format($report['total_expenses'], 2) }}</span>
                                        </td>
                                        <td class="px-4 py-3 text-right text-gray-600 text-xs">
                                            ৳ {{ number_format($report['meal_rate'], 2) }}
                                        </td>
                                        <td class="px-4 py-3 text-right">
                                            <span class="font-semibold text-green-600">৳ {{ number_format($report['total_deposits'], 2) }}</span>
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <span class="inline-block px-2 py-1 bg-gray-100 text-gray-800 text-xs font-semibold rounded">{{ $report['total_members'] }}</span>
                                        </td>
                                        <td class="px-4 py-3 text-right">
                                            <span class="font-semibold {{ $report['net_balance'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                                ৳ {{ number_format(abs($report['net_balance']), 2) }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <span class="inline-block px-2 py-1 {{ $month->status === MonthStatusEnum::ACTIVE ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }} text-xs font-semibold rounded">
                                                {{ $month->status->label() }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <div class="flex items-center justify-center gap-1">
                                                <a href="{{ route('reports.monthly', $month) }}" class="p-1.5 text-sky-600 hover:bg-sky-100 rounded transition-colors" title="View Report">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                    </svg>
                                                </a>
                                                @if($month->status === MonthStatusEnum::CLOSED)
                                                    <button type="button" class="p-1.5 text-green-600 hover:bg-green-100 rounded transition-colors" title="Download PDF" onclick="alert('PDF download feature coming soon')">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                                        </svg>
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="px-4 py-8 text-center text-gray-500">
                                            <div class="flex flex-col items-center gap-2">
                                                <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                </svg>
                                                <p class="text-sm">No months found.</p>
                                                <a href="{{ route('months.create') }}" class="text-sky-600 hover:text-sky-700 text-sm font-semibold">Create one now</a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            @else
                <div class="p-6 bg-blue-50 border border-blue-200 rounded-lg flex items-center gap-3">
                    <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 5v8a2 2 0 01-2 2h-5l-5 4v-4H4a2 2 0 01-2-2V5a2 2 0 012-2h12a2 2 0 012 2z" clip-rule="evenodd"></path>
                    </svg>
                    <div>
                        <p class="text-sm font-semibold text-blue-800">No months found</p>
                        <p class="text-xs text-blue-700 mt-1">Create your first month to see reports here.</p>
                    </div>
                </div>
            @endif

        </div>
    @else
        <div class="w-full px-4 sm:px-6 lg:px-8 py-6">
            <div class="p-4 bg-red-50 border border-red-200 rounded-lg flex items-center gap-3 mb-4">
                <svg class="w-5 h-5 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M13.477 14.89A6 6 0 015.11 2.476a6 6 0 018.367 8.414zm1.414-5.27a8 8 0 11-11.313-11.313 8 8 0 0111.313 11.313z" clip-rule="evenodd"></path>
                </svg>
                <span class="text-sm text-red-800">You don't have permission to view reports.</span>
            </div>
            <a href="{{ route('months.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm font-semibold rounded-lg transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                Back to Months
            </a>
        </div>
    @endcan
@endsection
