@extends('layouts.app')

@section('content')
@can('reports.view')
<div class="container-fluid px-4 py-8">
    <!-- Summary Card -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card border-primary">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        📊 Monthly Report - <strong>{{ $month->name }}</strong>
                        <small class="float-end text-muted">
                            {{ $month->start_date->format('d M Y') }} to {{ $month->end_date->format('d M Y') }}
                        </small>
                    </h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="text-center p-3 bg-light rounded">
                                <h5 class="text-muted mb-1">Total Meals</h5>
                                <h2 class="text-primary mb-0">{{ $summary['total_meals'] }}</h2>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center p-3 bg-light rounded">
                                <h5 class="text-muted mb-1">Total Expenses</h5>
                                <h2 class="text-danger mb-0">৳ {{ number_format($summary['total_expenses'], 2) }}</h2>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center p-3 bg-light rounded">
                                <h5 class="text-muted mb-1">Meal Rate</h5>
                                <h2 class="text-info mb-0">৳ {{ number_format($summary['meal_rate'], 2) }}</h2>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center p-3 bg-light rounded">
                                <h5 class="text-muted mb-1">Total Deposits</h5>
                                <h2 class="text-success mb-0">৳ {{ number_format($summary['total_deposits'], 2) }}</h2>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Member Balance Table -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0">📋 Member-wise Balance</h5>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Member</th>
                                <th class="text-center">Meals</th>
                                <th class="text-end">Meal Cost</th>
                                <th class="text-end">Deposited</th>
                                <th class="text-end">Balance</th>
                                <th class="text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($summary['member_balances'] as $balance)
                                <tr>
                                    <td>
                                        <strong>{{ $balance['member_name'] }}</strong>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-info">{{ $balance['meals'] }}</span>
                                    </td>
                                    <td class="text-end">
                                        ৳ {{ number_format($balance['meal_cost'], 2) }}
                                    </td>
                                    <td class="text-end">
                                        ৳ {{ number_format($balance['deposited'], 2) }}
                                    </td>
                                    <td class="text-end">
                                        <strong>৳ {{ number_format($balance['balance'], 2) }}</strong>
                                    </td>
                                    <td class="text-center">
                                        @if($balance['status'] === 'credit')
                                            <span class="badge bg-success">Credit</span>
                                        @elseif($balance['status'] === 'due')
                                            <span class="badge bg-danger">Due</span>
                                        @else
                                            <span class="badge bg-secondary">Settled</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">
                                        No members found in this month
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Net Balance -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="alert {{ $summary['net_balance'] >= 0 ? 'alert-success' : 'alert-danger' }} d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    {{ $summary['net_balance'] >= 0 ? '✓ Surplus' : '✗ Deficit' }}
                </h5>
                <h4 class="mb-0">
                    ৳ {{ number_format(abs($summary['net_balance']), 2) }}
                </h4>
            </div>
        </div>
    </div>

    <!-- Print Button -->
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex gap-2">
                <button class="btn btn-primary" onclick="window.print()">
                    <i class="fa-solid fa-print"></i> Print Report
                </button>
                <a href="{{ route('months.show', $month) }}" class="btn btn-secondary">
                    <i class="fa-solid fa-arrow-left"></i> Back
                </a>
            </div>
        </div>
    </div>
</div>

<style>
    @media print {
        .btn, .nav, .navbar {
            display: none !important;
        }
        .container-fluid {
            padding: 0 !important;
        }
    }
</style>
@else
    <div class="container-fluid px-4 py-8">
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Access Denied!</strong> You don't have permission to view reports.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <a href="{{ route('months.show', $month) }}" class="btn btn-secondary">Back</a>
    </div>
@endcan
@endsection
