@extends('layouts.app')

@php
use App\Enums\MonthStatusEnum;
@endphp

@section('content')
@can('reports.view')
<div class="container-fluid px-4 py-8">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center">
                <h2>📈 All Months Reports</h2>
                @can('months.view')
                    <a href="{{ route('months.index') }}" class="btn btn-secondary">
                        <i class="fa-solid fa-arrow-left"></i> Back to Months
                    </a>
                @endcan
            </div>
            <hr>
        </div>
    </div>

    <!-- Summary Table -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Monthly Summary</h5>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Month</th>
                                <th class="text-center">Total Meals</th>
                                <th class="text-end">Total Expenses</th>
                                <th class="text-end">Meal Rate</th>
                                <th class="text-end">Total Deposits</th>
                                <th class="text-center">Members</th>
                                <th class="text-center">Net Balance</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($months as $month)
                                @php
                                    $report = $reports[$month->id];
                                @endphp
                                <tr>
                                    <td>
                                        <strong>{{ $month->name }}</strong>
                                        <br>
                                        <small class="text-muted">
                                            {{ $month->start_date->format('d M Y') }} - {{ $month->end_date->format('d M Y') }}
                                        </small>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-info">{{ $report['total_meals'] }}</span>
                                    </td>
                                    <td class="text-end">
                                        <strong>৳ {{ number_format($report['total_expenses'], 2) }}</strong>
                                    </td>
                                    <td class="text-end">
                                        ৳ {{ number_format($report['meal_rate'], 2) }}
                                    </td>
                                    <td class="text-end">
                                        <strong class="text-success">৳ {{ number_format($report['total_deposits'], 2) }}</strong>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-secondary">{{ $report['total_members'] }}</span>
                                    </td>
                                    <td class="text-end">
                                        <strong class="{{ $report['net_balance'] >= 0 ? 'text-success' : 'text-danger' }}">
                                            ৳ {{ number_format(abs($report['net_balance']), 2) }}
                                        </strong>
                                    </td>
                                    <td class="text-center">
                                        @if($month->status === MonthStatusEnum::ACTIVE)
                                            <span class="badge bg-success">{{ MonthStatusEnum::ACTIVE->label() }}</span>
                                        @else
                                            <span class="badge bg-secondary">{{ MonthStatusEnum::CLOSED->label() }}</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('reports.monthly', $month) }}" class="btn btn-sm btn-primary" title="View Report">
                                            <i class="fa-solid fa-eye"></i>
                                        </a>
                                        @if($month->status === MonthStatusEnum::CLOSED)
                                            <a href="#" class="btn btn-sm btn-success" title="Download PDF" onclick="alert('PDF download feature coming soon')">
                                                <i class="fa-solid fa-download"></i>
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center text-muted py-4">
                                        No months found. <a href="{{ route('months.create') }}">Create one now</a>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@else
    <div class="container-fluid px-4 py-8">
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Access Denied!</strong> You don't have permission to view reports.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <a href="{{ route('months.index') }}" class="btn btn-secondary">Back to Months</a>
    </div>
@endcan
@endsection
