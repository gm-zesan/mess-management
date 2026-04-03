@extends('layouts.app')

@section('content')
<div class="container">
    <!-- Member Welcome Header -->
    <div class="row mb-4">
        <div class="col-md-8">
            <h2 class="mb-0">
                <i class="fa-solid fa-user-circle"></i> Welcome, {{ $member->name }}
            </h2>
            <p class="text-muted">Member ID: {{ $member->id }}</p>
        </div>
        <div class="col-md-4 text-md-end">
            <form method="POST" action="{{ route('member.logout') }}" style="display: inline;">
                @csrf
                <button type="submit" class="btn btn-outline-danger">
                    <i class="fa-solid fa-sign-out-alt"></i> Logout
                </button>
            </form>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fa-solid fa-check-circle"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Active Month Information -->
    @php
        $activeMonth = activeMonth();
    @endphp

    @if ($activeMonth)
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0">Current Month: <strong>{{ $activeMonth->name }}</strong></h5>
                    </div>
                    <div class="card-body">
                        <p class="text-muted">
                            <i class="fa-solid fa-calendar-alt"></i>
                            {{ $activeMonth->start_date->format('M d, Y') }} to {{ $activeMonth->end_date->format('M d, Y') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Financial Summary Cards -->
        @php
            $summary = app(\App\Services\CalculationService::class)->getMemberMonthSummary($member, $activeMonth);
        @endphp

        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h6 class="card-title text-muted">
                            <i class="fa-solid fa-utensils text-warning"></i> Meals
                        </h6>
                        <h3 class="card-text">{{ $summary['meal_count'] ?? 0 }}</h3>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h6 class="card-title text-muted">
                            <i class="fa-solid fa-coins text-success"></i> Meal Cost
                        </h6>
                        <h3 class="card-text">৳{{ number_format($summary['meal_cost'] ?? 0, 2) }}</h3>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h6 class="card-title text-muted">
                            <i class="fa-solid fa-arrow-circle-down text-info"></i> Deposits
                        </h6>
                        <h3 class="card-text">৳{{ number_format($summary['deposits'] ?? 0, 2) }}</h3>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h6 class="card-title text-muted">
                            <i class="fa-solid fa-balance-scale"></i> Balance
                        </h6>
                        <h3 class="card-text @if(($summary['balance'] ?? 0) < 0) text-danger @elseif(($summary['balance'] ?? 0) > 0) text-success @endif">
                            ৳{{ number_format($summary['balance'] ?? 0, 2) }}
                        </h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Current Month Status -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fa-solid fa-circle-info"></i> Financial Status
                        </h5>
                    </div>
                    <div class="card-body">
                        @php
                            $balance = $summary['balance'] ?? 0;
                            if ($balance > 0) {
                                $statusBadge = 'success';
                                $statusText = 'Credit - You have a credit of ৳' . number_format($balance, 2);
                                $statusIcon = 'fa-check-circle';
                            } elseif ($balance < 0) {
                                $statusBadge = 'danger';
                                $statusText = 'Due - You owe ৳' . number_format(abs($balance), 2);
                                $statusIcon = 'fa-exclamation-circle';
                            } else {
                                $statusBadge = 'secondary';
                                $statusText = 'Settled - Your account is balanced';
                                $statusIcon = 'fa-balance-scale';
                            }
                        @endphp

                        <p class="mb-0">
                            <span class="badge bg-{{ $statusBadge }} p-2">
                                <i class="fa-solid fa-{{ $statusIcon }}"></i> {{ $statusText }}
                            </span>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Meals This Month -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fa-solid fa-utensils"></i> Meals This Month
                        </h5>
                    </div>
                    <div class="card-body">
                        @php
                            $meals = $member->meals()
                                ->where('month_id', $activeMonth->id)
                                ->orderBy('date', 'desc')
                                ->get();
                        @endphp

                        @if ($meals->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-sm table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Date</th>
                                            <th>Day</th>
                                            <th>Quantity</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($meals as $meal)
                                            <tr>
                                                <td>{{ $meal->date->format('M d, Y') }}</td>
                                                <td>{{ $meal->date->format('l') }}</td>
                                                <td>
                                                    <span class="badge bg-info">{{ $meal->quantity }}</span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-success">
                                                        <i class="fa-solid fa-check"></i> Recorded
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="alert alert-info mb-0">
                                <i class="fa-solid fa-info-circle"></i> No meals recorded yet this month.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Deposits This Month -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fa-solid fa-arrow-circle-down"></i> Deposits This Month
                        </h5>
                    </div>
                    <div class="card-body">
                        @php
                            $deposits = $member->deposits()
                                ->where('month_id', $activeMonth->id)
                                ->orderBy('date', 'desc')
                                ->get();
                        @endphp

                        @if ($deposits->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-sm table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Date</th>
                                            <th>Amount</th>
                                            <th>Description</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($deposits as $deposit)
                                            <tr>
                                                <td>{{ $deposit->date->format('M d, Y') }}</td>
                                                <td>
                                                    <strong class="text-success">৳{{ number_format($deposit->amount, 2) }}</strong>
                                                </td>
                                                <td>{{ $deposit->description ?? 'Deposit' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="alert alert-info mb-0">
                                <i class="fa-solid fa-info-circle"></i> No deposits made yet this month.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="alert alert-warning">
            <i class="fa-solid fa-exclamation-triangle"></i> No active month has been set yet. Please contact the administrator.
        </div>
    @endif

    <!-- Info Box -->
    <div class="row">
        <div class="col-md-12">
            <div class="alert alert-light border">
                <p class="mb-0 small text-muted">
                    <i class="fa-solid fa-shield-alt"></i>
                    <strong>Note:</strong> This is a view-only portal. You can see your meals, deposits, and balance information. To make any changes, please contact the administrator.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
