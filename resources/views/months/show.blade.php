@extends('layouts.app')

@php
use App\Enums\MonthStatusEnum;
@endphp

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold">{{ $month->name }} - Monthly Summary</h1>
        <div class="flex gap-2">
            <a href="{{ route('months.report', $month->id) }}" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">Detailed Report</a>
            @if (!$month->isClosed())
                <a href="{{ route('months.edit', $month->id) }}" class="bg-yellow-600 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded">Edit</a>
                <form action="{{ route('months.close', $month->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to close this month? This action cannot be undone.');">
                    @csrf
                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                        <i class="fa-solid fa-lock"></i> Close Month
                    </button>
                </form>
            @else
                <div class="bg-gray-500 text-white font-bold py-2 px-4 rounded cursor-not-allowed">
                    <i class="fa-solid fa-lock"></i> Month Closed
                </div>
            @endif
            <a href="{{ route('months.index') }}" class="bg-gray-400 hover:bg-gray-500 text-white font-bold py-2 px-4 rounded">Back</a>
        </div>
    </div>

    <!-- Month Details Card -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-6">
        <h2 class="text-xl font-bold text-blue-900 mb-4">Month Details</h2>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <p class="text-sm text-blue-700">Start Date</p>
                <p class="text-lg font-semibold text-blue-900">{{ $month->start_date->format('M d, Y') }}</p>
            </div>
            <div>
                <p class="text-sm text-blue-700">End Date</p>
                <p class="text-lg font-semibold text-blue-900">{{ $month->end_date->format('M d, Y') }}</p>
            </div>
            <div>
                <p class="text-sm text-blue-700">Status</p>
                <p class="text-lg font-semibold">
                    <span class="px-3 py-1 rounded-full text-sm font-semibold
                        {{ $month->status === MonthStatusEnum::ACTIVE ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                        {{ $month->status->label() }}
                    </span>
                </p>
            </div>
            @if ($month->isClosed())
            <div>
                <p class="text-sm text-blue-700">Closed At</p>
                <p class="text-lg font-semibold text-blue-900">{{ $month->closed_at->format('M d, Y H:i') }}</p>
            </div>
            @endif
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-gray-600 text-sm">Total Meals</p>
            <p class="text-3xl font-bold text-blue-600">{{ $totalMeals }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-gray-600 text-sm">Total Expenses</p>
            <p class="text-3xl font-bold text-red-600">৳{{ number_format($totalExpenses, 2) }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-gray-600 text-sm">Total Deposits</p>
            <p class="text-3xl font-bold text-green-600">৳{{ number_format($totalDeposits, 2) }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-gray-600 text-sm">Cost per Meal</p>
            <p class="text-3xl font-bold text-purple-600">৳{{ number_format($costPerMeal, 2) }}</p>
        </div>
    </div>

    <!-- Member Balance Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-xl font-bold">Member Balance Summary</h2>
        </div>
        @if (count($memberBalance) > 0)
            <table class="w-full">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Member</th>
                        <th class="px-6 py-3 text-right text-sm font-semibold text-gray-700">Meals</th>
                        <th class="px-6 py-3 text-right text-sm font-semibold text-gray-700">Meal Cost</th>
                        <th class="px-6 py-3 text-right text-sm font-semibold text-gray-700">Deposited</th>
                        <th class="px-6 py-3 text-right text-sm font-semibold text-gray-700">Balance</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($memberBalance as $memberName => $balance)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $memberName }}</td>
                        <td class="px-6 py-4 text-sm text-right text-gray-800">{{ $balance['meals'] }}</td>
                        <td class="px-6 py-4 text-sm text-right text-red-600 font-semibold">৳{{ number_format($balance['meal_cost'], 2) }}</td>
                        <td class="px-6 py-4 text-sm text-right text-green-600 font-semibold">৳{{ number_format($balance['deposited'], 2) }}</td>
                        <td class="px-6 py-4 text-sm text-right font-bold
                            {{ $balance['balance'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                            ৳{{ number_format($balance['balance'], 2) }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="p-6 text-center text-gray-500">
                No member data for this month yet.
            </div>
        @endif
    </div>
</div>
@endsection
