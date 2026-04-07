@extends('layouts.app')

@section('content')
    <div class="w-full px-4 py-8">
        <div class="max-w-7xl mx-auto">
            <!-- Table Controls (search + length) -->
            <div class="mb-4 flex items-center justify-between">
                <div class="flex items-center gap-x-[4rem]">
                    <div class="flex items-center">
                        <span class="text-sm text-gray-700">Show</span>
                        <select id="length-select" class="border-0 py-0 bg-transparent text-sm text-gray-900 font-medium cursor-pointer">
                            <option value="15">15</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                        <span class="text-sm text-gray-700">entries</span>
                    </div>

                    <div class="relative w-80">
                        <input type="text" id="search-input" placeholder="Search" class="w-full px-4 py-2 border-0 border-b border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-sky-500">
                        <svg class="w-5 h-5 text-gray-400 absolute right-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-3 items-start sm:items-end justify-between">
                        <div class="flex-1 flex gap-2 w-full sm:w-auto">
                            <input 
                                type="date" 
                                id="filter-date"
                                class="px-3 py-2 border-0 border-b border-gray-300 rounded text-sm focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-transparent"
                                placeholder="Filter by date">
                            <select id="filter-member" class="px-3 py-2 border-0 border-b border-gray-300 rounded text-sm focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-transparent">
                                <option value="">All Members</option>
                                @foreach ($members as $member)
                                    <option value="{{ $member->id }}">{{ $member->name }}</option>
                                @endforeach
                            </select>
                            <button id="filter-btn" class="px-3 py-2 bg-sky-600 text-white text-sm font-medium rounded hover:bg-sky-700 transition-colors">
                                Filter
                            </button>
                            <button id="reset-btn" class="px-3 py-2 bg-gray-200 text-gray-900 text-sm font-medium rounded hover:bg-gray-300 transition-colors">
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

        <!-- Edit Modal -->
        <div id="editModal" class="hidden fixed inset-0 bg-black bg-opacity-50 items-center justify-center z-50 p-4 transition-opacity duration-300 opacity-0">
            <div class="bg-white rounded-lg max-w-md w-full shadow-xl">
                <!-- Modal Header -->
                <div class="flex items-center justify-between p-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Edit Meal Record</h3>
                    <button type="button" onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- Modal Content -->
                <div class="p-4 pt-0">
                    <form id="editForm" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" id="editId" name="meal_id">

                        <!-- Member & Date Info -->
                        <div class="flex items-center justify-between mb-4 p-3 bg-gray-50 rounded text-sm">
                            <div class="mb-3">
                                <p class="text-xs text-gray-600 font-medium">Member</p>
                                <p id="modalMemberName" class="font-semibold text-gray-900">-</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-600 font-medium">Date</p>
                                <p id="modalDate" class="font-semibold text-gray-900">-</p>
                                <input type="hidden" name="user_id" id="modalUserId">
                                <input type="hidden" name="date" id="modalDateValue">
                            </div>
                        </div>

                        <!-- Meal Counts -->
                        <div class="space-y-4">
                            <div class="flex items-center justify-between text-sm font-medium text-gray-700">
                                <!-- Breakfast -->
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-2">Breakfast</label>
                                    <div class="flex items-center gap-1">
                                        <button type="button" class="p-1 text-gray-600 hover:bg-gray-100 rounded" onclick="decrementMealModal('breakfast_count')">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                                            </svg>
                                        </button>
                                        <input type="number" id="breakfast_count" name="breakfast_count" class="w-16 text-center px-2 py-1 border border-gray-300 rounded text-sm font-semibold" min="0" step="0.5" required>
                                        <button type="button" class="p-1 text-gray-600 hover:bg-gray-100 rounded" onclick="incrementMealModal('breakfast_count')">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </div>

                                <!-- Lunch -->
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-2">Lunch</label>
                                    <div class="flex items-center gap-1">
                                        <button type="button" class="p-1 text-gray-600 hover:bg-gray-100 rounded" onclick="decrementMealModal('lunch_count')">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                                            </svg>
                                        </button>
                                        <input type="number" id="lunch_count" name="lunch_count" class="w-16 text-center px-2 py-1 border border-gray-300 rounded text-sm font-semibold" min="0" step="0.5" required>
                                        <button type="button" class="p-1 text-gray-600 hover:bg-gray-100 rounded" onclick="incrementMealModal('lunch_count')">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </div>

                                <!-- Dinner -->
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-2">Dinner</label>
                                    <div class="flex items-center gap-1">
                                        <button type="button" class="p-1 text-gray-600 hover:bg-gray-100 rounded" onclick="decrementMealModal('dinner_count')">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                                            </svg>
                                        </button>
                                        <input type="number" id="dinner_count" name="dinner_count" class="w-16 text-center px-2 py-1 border border-gray-300 rounded text-sm font-semibold" min="0" step="0.5" required>
                                        <button type="button" class="p-1 text-gray-600 hover:bg-gray-100 rounded" onclick="incrementMealModal('dinner_count')">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Total -->
                            <div class="mt-4 p-2 bg-gray-50 rounded flex justify-between items-center">
                                <span class="text-sm font-medium text-gray-700">Total</span>
                                <span id="modalTotal" class="text-lg font-bold text-gray-900">0.0</span>
                            </div>
                        </div>

                        <!-- Modal Actions -->
                        <div class="mt-6 flex gap-2">
                            <button type="button" onclick="closeEditModal()" class="flex-1 px-3 py-2 text-gray-700 bg-gray-200 hover:bg-gray-300 text-sm font-medium rounded">Cancel</button>
                            <button type="submit" class="flex-1 px-3 py-2 bg-sky-600 hover:bg-sky-700 text-white text-sm font-medium rounded">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <div id="deleteModal" class="hidden fixed inset-0 bg-black bg-opacity-50 items-center justify-center z-50 p-4 transition-opacity duration-300 opacity-0">
            <div class="bg-white rounded-lg max-w-sm w-full shadow-xl">
                <!-- Modal Header -->
                <div class="flex items-center justify-between p-4 border-b border-gray-200">
                    <div class="flex items-center gap-3">
                        <div class="flex items-center justify-center w-10 h-10 rounded-full bg-red-100">
                            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4v.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900">Delete Meal Record?</h3>
                    </div>
                    <button type="button" onclick="closeDeleteModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- Modal Content -->
                <div class="p-4 pt-0">
                    <p class="text-sm text-gray-600 mb-2">Are you sure you want to delete this meal record?</p>
                    <div class="p-3 bg-gray-50 rounded space-y-1">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Member:</span>
                            <span id="deleteModalMember" class="font-medium text-gray-900">-</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Date:</span>
                            <span id="deleteModalDate" class="font-medium text-gray-900">-</span>
                        </div>
                    </div>
                    <p class="text-xs text-red-600 mt-3">This action cannot be undone.</p>
                </div>

                <!-- Modal Actions -->
                <div class="flex gap-2 p-4 border-t border-gray-200">
                    <button type="button" onclick="closeDeleteModal()" class="flex-1 px-4 py-2 text-gray-700 bg-gray-200 hover:bg-gray-300 text-sm font-medium rounded">Cancel</button>
                    <button type="button" onclick="confirmDelete()" class="flex-1 px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded">Delete</button>
                </div>
            </div>
        </div>

        @push('custom-scripts')
    <script type="text/javascript">
        var currentDeleteId = null;

        // Modal show/hide functions (expenses pattern)
        function openEditModal(id) {
            $('#editId').val(id);
            $.ajax({
                url: BASE_URL + '/meals/' + id + '/edit',
                type: 'GET',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                success: function(data) {
                    $('#modalMemberName').text(data.user_name || '-');
                    $('#modalUserId').val(data.user_id || '');
                    $('#modalDateValue').val(data.date || '');
                    $('#modalDate').text(data.date_display || data.date || '-');
                    $('#breakfast_count').val(data.breakfast_count ?? 0);
                    $('#lunch_count').val(data.lunch_count ?? 0);
                    $('#dinner_count').val(data.dinner_count ?? 0);
                    updateModalTotal();
                    $('#editModal').removeClass('hidden').addClass('flex');
                    $('#editModal')[0].offsetHeight; // Trigger reflow
                    $('#editModal').removeClass('opacity-0').addClass('opacity-100');
                },
                error: function() {
                    if (window.toastr) toastr.error('Error loading meal details');
                }
            });
        }

        function closeEditModal() {
            $('#editModal').removeClass('opacity-100').addClass('opacity-0');
            setTimeout(function() {
                $('#editModal').addClass('hidden').removeClass('flex');
            }, 300);
        }

        function openDeleteModal(id) {
            currentDeleteId = id;
            $.ajax({
                url: BASE_URL + '/meals/' + id + '/edit',
                type: 'GET',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                success: function(data) {
                    $('#deleteModalMember').text(data.user_name || '-');
                    $('#deleteModalDate').text(data.date_display || data.date || '-');
                    $('#deleteModal').removeClass('hidden').addClass('flex');
                    $('#deleteModal')[0].offsetHeight; // Trigger reflow
                    $('#deleteModal').removeClass('opacity-0').addClass('opacity-100');
                },
                error: function() {
                    if (window.toastr) toastr.error('Error loading meal details');
                }
            });
        }

        function closeDeleteModal() {
            $('#deleteModal').removeClass('opacity-100').addClass('opacity-0');
            setTimeout(function() {
                $('#deleteModal').addClass('hidden').removeClass('flex');
                currentDeleteId = null;
            }, 300);
        }

        function confirmDelete() {
            if (!currentDeleteId) return;
            $.ajax({
                url: BASE_URL + '/meals/' + currentDeleteId,
                type: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'X-Requested-With': 'XMLHttpRequest'
                },
                success: function(response) {
                    closeDeleteModal();
                    $('#meals-table').DataTable().ajax.reload();
                    if (window.toastr) toastr.success('Meal deleted successfully');
                },
                error: function() {
                    if (window.toastr) toastr.error('Error deleting meal');
                }
            });
        }

        function incrementMealModal(field) {
            var $f = $('#' + field);
            var step = parseFloat($f.attr('step')) || 1;
            var val = parseFloat($f.val() || 0) + step;
            $f.val(val);
            updateModalTotal();
        }

        function decrementMealModal(field) {
            var $f = $('#' + field);
            var step = parseFloat($f.attr('step')) || 1;
            var val = parseFloat($f.val() || 0) - step;
            if (val < 0) val = 0;
            $f.val(val);
            updateModalTotal();
        }

        function updateModalTotal() {
            var b = parseFloat($('#breakfast_count').val() || 0);
            var l = parseFloat($('#lunch_count').val() || 0);
            var d = parseFloat($('#dinner_count').val() || 0);
            var total = (b + l + d).toFixed(1);
            $('#modalTotal').text(total);
        }

        function handleModalSubmit(e) {
            e.preventDefault();
            var id = $('#editId').val();
            if (!id) {
                if (window.toastr) toastr.error('Error: No meal ID');
                return;
            }
            $.ajax({
                url: BASE_URL + '/meals/' + id,
                type: 'PUT',
                data: $('#editForm').serialize(),
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'X-Requested-With': 'XMLHttpRequest'
                },
                success: function(response) {
                    closeEditModal();
                    $('#meals-table').DataTable().ajax.reload(null, false);
                    if (window.toastr) toastr.success('Meal updated successfully');
                },
                error: function() {
                    if (window.toastr) toastr.error('Error updating meal');
                }
            });
        }

        $(document).ready(function() {
            function debounce(fn, wait) {
                var timeout;
                return function() {
                    var context = this, args = arguments;
                    clearTimeout(timeout);
                    timeout = setTimeout(function() {
                        fn.apply(context, args);
                    }, wait);
                };
            }

            var table = $('#meals-table').DataTable({
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
                    data: function(d) {
                        d.filter_date = $('#filter-date').val();
                        d.filter_member = $('#filter-member').val();
                    }
                },
                columns: [
                    { data: 'user', name: 'user_name' },
                    { data: 'date', name: 'date' },
                    { data: 'breakfast_count', name: 'breakfast_count' },
                    { data: 'lunch_count', name: 'lunch_count' },
                    { data: 'dinner_count', name: 'dinner_count' },
                    { data: 'total_meal_count', name: 'total_meal_count' },
                    {
                        data: 'id',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            var btns = '<div class="flex items-center justify-center gap-2">';
                            if (row.can_edit) {
                                btns += '<a onclick="openEditModal(' + data + ')" class="text-sky-600 hover:text-sky-800 cursor-pointer text-sm" title="Edit">Edit</a>';
                            }
                            if (row.can_delete) {
                                btns += '<a href="#" class="delete-btn text-red-600 hover:text-red-800 text-sm" data-id="' + data + '">Delete</a>';
                            }
                            btns += '</div>';
                            return btns;
                        }
                    }
                ],
                order: [[1, 'desc']]
            });

            // Length control
            $('#length-select').on('change', function() {
                table.page.len(parseInt($(this).val())).draw();
            });

            // Debounced search
            var onSearchInput = debounce(function(e) {
                var q = e.target.value.trim();
                table.search(q).draw();
            }, 300);

            $('#search-input').on('input', onSearchInput);

            // Filter buttons
            $('#filter-btn').click(function() {
                table.ajax.reload(null, false);
            });

            $('#reset-btn').click(function() {
                $('#filter-date').val('');
                $('#filter-member').val('');
                table.ajax.reload(null, false);
            });

            // Delegated delete button handler
            $(document).on('click', '.delete-btn', function(e) {
                e.preventDefault();
                var id = $(this).data('id');
                openDeleteModal(id);
            });

            // Edit form submission
            $('#editForm').on('submit', function(e) {
                e.preventDefault();
                handleModalSubmit(e);
            });

            // Close modals when clicking outside
            $(document).on('click', function(e) {
                if (e.target.id === 'editModal') closeEditModal();
                if (e.target.id === 'deleteModal') closeDeleteModal();
            });
        });
    </script>
    @endpush
@endsection
