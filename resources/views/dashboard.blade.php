@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-8">
    <!-- Header -->
    <div class="row mb-6">
        <div class="col-md-12">
            <h1 class="text-4xl font-bold text-gray-800 mb-2">📊 Dashboard</h1>
            <p class="text-gray-600">Welcome back! Here's your mess management summary.</p>
        </div>
    </div>

    @if ($activeMonth)
        <!-- Active Month Alert -->
        <div class="alert alert-info mb-4" role="alert">
            <h5 class="alert-heading">
                <i class="fa-solid fa-calendar-check"></i> Active Month
            </h5>
            <p class="mb-0">
                <strong>{{ $activeMonth->name }}</strong> 
                ({{ $activeMonth->start_date->format('d M Y') }} - {{ $activeMonth->end_date->format('d M Y') }})
                @if ($isClosed)
                    <span class="badge bg-danger ms-2">
                        <i class="fa-solid fa-lock"></i> Closed
                    </span>
                @else
                    <span class="badge bg-success ms-2">
                        <i class="fa-solid fa-circle-check"></i> Open
                    </span>
                @endif
            </p>
        </div>

        @if ($summary)
            <!-- Summary Cards -->
            <div class="row mb-6">
                <div class="col-md-3">
                    <div class="card bg-primary text-white shadow-sm">
                        <div class="card-body">
                            <h6 class="card-title text-white-50">Total Meals</h6>
                            <h2 class="card-text mb-0">{{ $summary['total_meals'] }}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-danger text-white shadow-sm">
                        <div class="card-body">
                            <h6 class="card-title text-white-50">Total Expenses</h6>
                            <h2 class="card-text mb-0">৳ {{ number_format($summary['total_expenses'], 2) }}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white shadow-sm">
                        <div class="card-body">
                            <h6 class="card-title text-white-50">Total Deposits</h6>
                            <h2 class="card-text mb-0">৳ {{ number_format($summary['total_deposits'], 2) }}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white shadow-sm">
                        <div class="card-body">
                            <h6 class="card-title text-white-50">Meal Rate</h6>
                            <h2 class="card-text mb-0">৳ {{ number_format($summary['meal_rate'], 2) }}</h2>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Member Balances -->
            <div class="row">
                <div class="col-md-12">
                    <div class="card shadow-sm">
                        <div class="card-header bg-secondary text-white">
                            <h5 class="card-title mb-0">
                                <i class="fa-solid fa-users"></i> Member Balances
                            </h5>
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
                                                <span class="badge bg-primary">{{ $balance['meals'] }}</span>
                                            </td>
                                            <td class="text-end">
                                                ৳ {{ number_format($balance['meal_cost'], 2) }}
                                            </td>
                                            <td class="text-end">
                                                ৳ {{ number_format($balance['deposited'], 2) }}
                                            </td>
                                            <td class="text-end">
                                                <strong class="{{ $balance['balance'] > 0 ? 'text-success' : ($balance['balance'] < 0 ? 'text-danger' : 'text-muted') }}">
                                                    ৳ {{ number_format($balance['balance'], 2) }}
                                                </strong>
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
                                                No member data yet
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="alert alert-warning" role="alert">
                <i class="fa-solid fa-exclamation-triangle"></i> 
                Unable to load summary data. Please try again.
            </div>
        @endif
    @else
        <!-- No Active Month -->
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="card border-warning">
                    <div class="card-body text-center py-5">
                        <h3 class="text-warning mb-3">
                            <i class="fa-solid fa-calendar-times"></i> No Active Month
                        </h3>
                        <p class="lead text-muted mb-4">
                            {{ $error ?? 'There is no active month. Please create and activate a month to get started.' }}
                        </p>
                        <a href="{{ route('months.create') }}" class="btn btn-primary btn-lg">
                            <i class="fa-solid fa-plus"></i> Create Month
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
