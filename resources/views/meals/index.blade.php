@extends('layouts.app')

@section('content')
    <div class="w-full px-4 py-8">
        <div class="max-w-7xl mx-auto">
            <!-- Table Controls (search + length) -->
            <div class="mb-4 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="flex items-center">
                        <span class="text-sm text-gray-700 mr-2">Show</span>
                        <select id="length-select" class="border-0 py-0 bg-transparent text-sm text-gray-900 font-medium cursor-pointer">
                            <option value="15">15</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                        <span class="text-sm text-gray-700 ml-2">entries</span>
                    </div>

                    <div class="relative w-80">
                        <input type="text" id="search-input" placeholder="Search" class="w-full px-4 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-sky-500">
                        <svg class="w-5 h-5 text-gray-400 absolute right-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>

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
            @can('meals.view')
                <!-- DataTable -->
                <div class="overflow-x-auto">
                    <table id="meals-table" class="w-full">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th>Member</th>
                                <th>Date</th>
                                <th>B</th>
                                <th>L</th>
                                <th>D</th>
                                <th>Total</th>
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

    @push('custom-scripts')
    <script>
    $(function(){
        // Initialize DataTable (server-side, responsive)
        function debounce(fn, wait) {
            var timeout;
            return function () {
                var context = this, args = arguments;
                clearTimeout(timeout);
                timeout = setTimeout(function () {
                    fn.apply(context, args);
                }, wait);
            };
        }

        let table = $('#meals-table').DataTable({
            processing: true,
            serverSide: true,
            responsive: { details: true },
            fixedHeader: true,
            pageLength: 15,
            lengthMenu: [15, 25, 50, 100],
            dom: 'rt<"dataTables_bottom"ip>',
            ajax: {
                url: "{{ route('meals.index') }}",
                type: 'GET',
                data: function(d){
                    d.filter_date = $('#filter-date').val();
                    d.filter_member = $('#filter-member').val();
                }
            },
            columns: [
                {data: 'user', name: 'user_name'},
                {data: 'date', name: 'date'},
                {data: 'breakfast_count', name: 'breakfast_count'},
                {data: 'lunch_count', name: 'lunch_count'},
                {data: 'dinner_count', name: 'dinner_count'},
                {data: 'total_meal_count', name: 'total_meal_count'},
                {data: 'action', name: 'action', orderable: false, searchable: false}
            ],
            order: [[1,'desc']],
        });

        // Length control
        $('#length-select').on('change', function() {
            table.page.len(parseInt($(this).val())).draw();
        });

        // Debounced global search
        var onSearchInput = debounce(function (e) {
            var q = e.target.value.trim();
            table.search(q).draw();
        }, 300);

        $('#search-input').on('input', onSearchInput);

        // Filter button
        $('#filter-btn').click(function(){
            table.ajax.reload(null, false);
        });

        // Reset filters
        $('#reset-btn').click(function(){
            $('#filter-date').val('');
            $('#filter-member').val('');
            table.ajax.reload(null, false);
        });
    });
    </script>
    @endpush
@endsection
