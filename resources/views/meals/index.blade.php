@extends('layouts.app')

@section('content')
    <div class="w-full px-4 py-8 bg-white">
        <div class="max-w-7xl mx-auto">
            <!-- Success Message -->
            @if (session('success'))
                <div class="mb-4 p-3 bg-green-50 border border-green-200 rounded-lg flex items-center justify-between text-sm">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="text-green-800">{{ session('success') }}</span>
                    </div>
                    <button onclick="this.parentElement.style.display='none'" class="text-green-600 hover:text-green-800">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                        </svg>
                    </button>
                </div>
            @endif

            <!-- Page Header -->
            <div class="mb-6 flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Meals</h1>
                    <p class="text-sm text-gray-600 mt-1">Track meal records for {{ $activeMonth->name ?? 'current month' }}</p>
                </div>
                @can('meals.create')
                    <a href="{{ route('meals.create') }}" class="px-4 py-2 bg-sky-600 text-white text-sm font-medium rounded-lg hover:bg-sky-700 transition-colors inline-flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Add Meal
                    </a>
                @endcan
            </div>

            <!-- Filter Bar -->
            <div class="mb-4 flex flex-col sm:flex-row gap-3 items-start sm:items-end justify-between">
                <div class="flex-1 flex gap-2 w-full sm:w-auto">
                    <input 
                        type="date" 
                        id="filter-date"
                        class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-transparent"
                        placeholder="Filter by date">
                    <select id="filter-member" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-transparent">
                        <option value="">All Members</option>
                        @foreach ($members as $member)
                            <option value="{{ $member->id }}">{{ $member->name }}</option>
                        @endforeach
                    </select>
                    <button id="filter-btn" class="px-3 py-2 bg-sky-600 text-white text-sm font-medium rounded-lg hover:bg-sky-700 transition-colors">
                        Filter
                    </button>
                    <button id="reset-btn" class="px-3 py-2 bg-gray-200 text-gray-900 text-sm font-medium rounded-lg hover:bg-gray-300 transition-colors">
                        Reset
                    </button>
                </div>
            </div>

            @can('meals.view')
                <!-- DataTable -->
                <div class="overflow-x-auto bg-white rounded-lg border border-gray-200 shadow-sm">
                    <table id="meals-table" class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-4 py-3 text-left font-semibold text-gray-700 text-xs">Member</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-700 text-xs">Date</th>
                                <th class="px-4 py-3 text-center font-semibold text-gray-700 text-xs">B</th>
                                <th class="px-4 py-3 text-center font-semibold text-gray-700 text-xs">L</th>
                                <th class="px-4 py-3 text-center font-semibold text-gray-700 text-xs">D</th>
                                <th class="px-4 py-3 text-center font-semibold text-gray-700 text-xs">Total</th>
                                @canany(['meals.update','meals.delete'])
                                    <th class="px-4 py-3 text-center font-semibold text-gray-700 text-xs">Actions</th>
                                @endcanany
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <!-- DataTables will populate rows here -->
                        </tbody>
                    </table>
                </div>
            @else
                <div class="p-4 bg-red-50 border border-red-200 rounded-lg flex items-center gap-3">
                    <svg class="w-5 h-5 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M13.477 14.89A6 6 0 015.11 2.476a6 6 0 018.367 8.414zm1.414-5.27a8 8 0 11-11.313-11.313 8 8 0 0111.313 11.313z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="text-sm text-red-800">You don't have permission to view meals.</span>
                </div>
            @endcan

        </div>
    </div>

    @push('scripts')
    <script>
    $(function(){
        // Initialize DataTable
        let table = $('#meals-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('meals.index') }}",
                data: function(d){
                    d.filter_date = $('#filter-date').val();
                    d.filter_member = $('#filter-member').val();
                }
            },
            columns: [
                {data: 'user', name: 'user'},
                {data: 'date', name: 'date'},
                {data: 'breakfast_count', name: 'breakfast_count', className: 'text-center'},
                {data: 'lunch_count', name: 'lunch_count', className: 'text-center'},
                {data: 'dinner_count', name: 'dinner_count', className: 'text-center'},
                {data: 'total_meal_count', name: 'total_meal_count', className: 'text-center'},
                {data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center'}
            ],
            pageLength: 15,
            order: [[1,'desc']],
        });

        // Filter button
        $('#filter-btn').click(function(){
            table.ajax.reload();
        });

        // Reset filters
        $('#reset-btn').click(function(){
            $('#filter-date').val('');
            $('#filter-member').val('');
            table.ajax.reload();
        });
    });
    </script>
    @endpush
@endsection
