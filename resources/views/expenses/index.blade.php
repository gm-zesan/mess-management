@extends('layouts.app')

@section('content')
    <div class="w-full px-4 py-8">
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

            @can('expenses.view')
                <!-- DataTable Header -->
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-x-[4rem]">
                        <div class="flex items-center">
                            <span class="text-sm text-gray-700">Show</span>
                            <select id="length-select" class="border-0 py-0 bg-transparent text-sm text-gray-900 font-medium cursor-pointer" style="padding-right:20px">
                                <option value="15">15</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select>
                            <span class="text-sm text-gray-700">entries</span>
                        </div>
                        <div class="relative w-80">
                            <input type="text" id="search-input" placeholder="Search" class="w-full px-4 py-2 border-0 border-b-2 border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-sky-500">
                            <svg class="w-5 h-5 text-gray-400 absolute right-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                    </div>
                    
                    @can('expenses.create')
                        <button onclick="openCreateModal()" class="px-4 py-2 bg-sky-500 text-white text-sm font-medium rounded hover:bg-sky-700 transition-colors inline-flex items-center gap-2">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"></path>
                            </svg>
                            Add Expense
                        </button>
                    @endcan
                </div>

                <!-- DataTable -->
                <div class="overflow-hidden">
                    <table id="expenses-table" class="w-full">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Date</th>
                                <th>Member</th>
                                <th>Category</th>
                                <th>Amount</th>
                                <th>Description</th>
                                @canany(['expenses.update','expenses.delete'])
                                    <th>Actions</th>
                                @endcanany
                            </tr>
                        </thead>
                        <tbody>
                            <!-- DataTables will populate rows here -->
                        </tbody>
                    </table>
                </div>
            @else
                <div class="p-4 bg-red-50 border border-red-200 rounded-lg flex items-center gap-3">
                    <svg class="w-5 h-5 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M13.477 14.89A6 6 0 015.11 2.476a6 6 0 018.367 8.414zm1.414-5.27a8 8 0 11-11.313-11.313 8 8 0 0111.313 11.313z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="text-sm text-red-800">You don't have permission to view expenses.</span>
                </div>
            @endcan

        </div>
    </div>

    <!-- Create Expense Modal -->
    <div id="createModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4 transition-opacity duration-300 opacity-0">
        <div class="bg-white rounded-lg max-w-md w-full shadow-xl max-h-[90vh] overflow-y-auto">
            <!-- Modal Header -->
            <div class="flex items-center justify-between p-4 border-b border-gray-200 sticky top-0 bg-white">
                <h3 class="text-lg font-semibold text-gray-900">Add Expense</h3>
                <button type="button" onclick="closeCreateModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Modal Content -->
            <div class="p-4 pt-0">
                <form id="createForm" method="POST" action="{{ route('expenses.store') }}" class="space-y-3">
                    @csrf

                    @if ($activeMonth)
                        <input type="hidden" name="month_id" value="{{ $activeMonth->id }}">
                    @endif

                    <!-- Member Select -->
                    <div>
                        <label for="createUser" class="block text-xs font-medium text-gray-600 mb-1">Member <span class="text-red-600">*</span></label>
                        <select id="createUser" name="user_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-transparent" required>
                            <option value="">-- Select Member --</option>
                            @foreach ($members ?? [] as $member)
                                <option value="{{ $member->id }}">{{ $member->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Category Select -->
                    <div>
                        <label for="createCategory" class="block text-xs font-medium text-gray-600 mb-1">Category <span class="text-red-600">*</span></label>
                        <select id="createCategory" name="category" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-transparent" required>
                            <option value="">-- Select Category --</option>
                            <option value="meal">Meal</option>
                            <option value="utility">Utility</option>
                        </select>
                    </div>

                    <!-- Amount Input -->
                    <div>
                        <label for="createAmount" class="block text-xs font-medium text-gray-600 mb-1">Amount (৳) <span class="text-red-600">*</span></label>
                        <input type="number" id="createAmount" name="amount" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-transparent" 
                               step="0.01" min="0" placeholder="0.00" required>
                    </div>

                    <!-- Date Input -->
                    <div>
                        <label for="createDate" class="block text-xs font-medium text-gray-600 mb-1">Date <span class="text-red-600">*</span></label>
                        <input type="date" id="createDate" name="date" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-transparent" required>
                    </div>

                    <!-- Note Textarea -->
                    <div>
                        <label for="createNote" class="block text-xs font-medium text-gray-600 mb-1">Note</label>
                        <textarea id="createNote" name="note" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-transparent" 
                                  rows="2" placeholder="Optional notes"></textarea>
                    </div>

                    <!-- Deposit Checkbox -->
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" id="createDeposit" name="with_deposit" value="1" class="w-4 h-4 text-sky-600 border-gray-300 rounded focus:ring-2 focus:ring-sky-500">
                        <span class="text-xs text-gray-700">Create Deposit</span>
                    </label>

                    <!-- Form Actions -->
                    <div class="border-t border-gray-200 pt-3 flex gap-2">
                        <button type="button" onclick="closeCreateModal()" class="flex-1 px-3 py-2 text-gray-700 bg-gray-200 hover:bg-gray-300 text-xs font-medium rounded">Cancel</button>
                        <button type="submit" class="flex-1 px-3 py-2 bg-sky-600 hover:bg-sky-700 text-white text-xs font-medium rounded">Add</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Expense Modal -->
    <div id="editModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4 transition-opacity duration-300 opacity-0">
        <div class="bg-white rounded-lg max-w-md w-full shadow-xl max-h-[90vh] overflow-y-auto">
            <!-- Modal Header -->
            <div class="flex items-center justify-between p-4 border-b border-gray-200 sticky top-0 bg-white">
                <h3 class="text-lg font-semibold text-gray-900">Edit Expense</h3>
                <button type="button" onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Modal Content -->
            <div class="p-4 pt-0">
                <form id="editForm" method="POST" class="space-y-3">
                    @csrf
                    @method('PUT')

                    <!-- Member & Date Info -->
                    <div class="flex items-center justify-between p-2 bg-gray-50 rounded text-xs">
                        <div>
                            <p class="text-gray-600 font-medium">Member</p>
                            <p id="editMemberName" class="font-semibold text-gray-900">-</p>
                        </div>
                        <div>
                            <p class="text-gray-600 font-medium">Date</p>
                            <p id="editDateDisplay" class="font-semibold text-gray-900">-</p>
                            <input type="hidden" id="editUserId" name="user_id">
                            <input type="hidden" id="editDateValue" name="date">
                        </div>
                    </div>

                    <!-- Category Select -->
                    <div>
                        <label for="editCategory" class="block text-xs font-medium text-gray-600 mb-1">Category <span class="text-red-600">*</span></label>
                        <select id="editCategory" name="category" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-transparent" required>
                            <option value="meal">Meal</option>
                            <option value="utility">Utility</option>
                        </select>
                    </div>

                    <!-- Amount Input -->
                    <div>
                        <label for="editAmount" class="block text-xs font-medium text-gray-600 mb-1">Amount (৳) <span class="text-red-600">*</span></label>
                        <input type="number" id="editAmount" name="amount" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-transparent" 
                               step="0.01" min="0" placeholder="0.00" required>
                    </div>

                    <!-- Note Textarea -->
                    <div>
                        <label for="editNote" class="block text-xs font-medium text-gray-600 mb-1">Note</label>
                        <textarea id="editNote" name="note" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-transparent" 
                                  rows="2" placeholder="Optional notes"></textarea>
                    </div>

                    <!-- Form Actions -->
                    <div class="border-t border-gray-200 pt-3 flex gap-2">
                        <button type="button" onclick="closeEditModal()" class="flex-1 px-3 py-2 text-gray-700 bg-gray-200 hover:bg-gray-300 text-xs font-medium rounded">Cancel</button>
                        <button type="submit" class="flex-1 px-3 py-2 bg-sky-600 hover:bg-sky-700 text-white text-xs font-medium rounded">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4 transition-opacity duration-300 opacity-0">
        <div class="bg-white rounded-lg max-w-sm w-full shadow-xl">
            <!-- Modal Header -->
            <div class="flex items-center justify-between p-4 border-b border-gray-200">
                <div class="flex items-center gap-3">
                    <div class="flex items-center justify-center w-10 h-10 rounded-full bg-red-100">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4v.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900">Delete Expense?</h3>
                </div>
                <button type="button" onclick="closeDeleteModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Modal Content -->
            <div class="p-4 pt-0">
                <p class="text-sm text-gray-600 mb-2">Are you sure you want to delete this expense?</p>
                <div class="p-3 bg-gray-50 rounded space-y-1">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Member:</span>
                        <span id="deleteModalMember" class="font-medium text-gray-900">-</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Amount:</span>
                        <span id="deleteModalAmount" class="font-medium text-gray-900">-</span>
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
        var listUrl = "{{ route('expenses.index') }}";
        var SITEURL = "{{ URL::to('') }}";
        var currentDeleteId = null;

        // Modal functions
        function openCreateModal() {
            document.getElementById('createForm').reset();
            document.getElementById('createModal').classList.remove('hidden');
            document.getElementById('createModal').classList.add('flex');
            document.getElementById('createModal').offsetHeight; // Trigger reflow
            document.getElementById('createModal').classList.remove('opacity-0');
            document.getElementById('createModal').classList.add('opacity-100');
        }

        function closeCreateModal() {
            document.getElementById('createModal').classList.remove('opacity-100');
            document.getElementById('createModal').classList.add('opacity-0');
            setTimeout(() => {
                document.getElementById('createModal').classList.add('hidden');
                document.getElementById('createModal').classList.remove('flex');
            }, 300);
        }

        function openEditModal(id) {
            $.ajax({
                url: SITEURL + '/expenses/' + id + '/edit',
                type: 'GET',
                success: function(data) {
                    // Populate edit form with data
                    document.getElementById('editUserId').value = data.user_id;
                    document.getElementById('editDateValue').value = data.date;
                    document.getElementById('editMemberName').textContent = data.user_name;
                    document.getElementById('editDateDisplay').textContent = data.formatted_date;
                    document.getElementById('editCategory').value = data.category;
                    document.getElementById('editAmount').value = data.amount;
                    document.getElementById('editNote').value = data.note || '';
                    
                    // Set form action
                    document.getElementById('editForm').action = SITEURL + '/expenses/' + id;
                    
                    // Show modal
                    document.getElementById('editModal').classList.remove('hidden');
                    document.getElementById('editModal').classList.add('flex');
                    document.getElementById('editModal').offsetHeight; // Trigger reflow
                    document.getElementById('editModal').classList.remove('opacity-0');
                    document.getElementById('editModal').classList.add('opacity-100');
                }
            });
        }

        function closeEditModal() {
            document.getElementById('editModal').classList.remove('opacity-100');
            document.getElementById('editModal').classList.add('opacity-0');
            setTimeout(() => {
                document.getElementById('editModal').classList.add('hidden');
                document.getElementById('editModal').classList.remove('flex');
            }, 300);
        }

        function openDeleteModal(id, member, amount) {
            currentDeleteId = id;
            document.getElementById('deleteModalMember').textContent = member;
            document.getElementById('deleteModalAmount').textContent = amount;
            
            document.getElementById('deleteModal').classList.remove('hidden');
            document.getElementById('deleteModal').classList.add('flex');
            document.getElementById('deleteModal').offsetHeight; // Trigger reflow
            document.getElementById('deleteModal').classList.remove('opacity-0');
            document.getElementById('deleteModal').classList.add('opacity-100');
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').classList.remove('opacity-100');
            document.getElementById('deleteModal').classList.add('opacity-0');
            setTimeout(() => {
                document.getElementById('deleteModal').classList.add('hidden');
                document.getElementById('deleteModal').classList.remove('flex');
                currentDeleteId = null;
            }, 300);
        }

        function confirmDelete() {
            if (currentDeleteId) {
                $.ajax({
                    url: SITEURL + '/expenses/' + currentDeleteId,
                    type: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        closeDeleteModal();
                        $('#expenses-table').DataTable().ajax.reload();
                        // Show success message
                        toastr.success('Expense deleted successfully');
                    },
                    error: function(error) {
                        toastr.error('Error deleting expense');
                    }
                });
            }
        }

        $(document).ready(function () {
            var table = $('#expenses-table').DataTable({
                processing: true,
                responsive: true,
                serverSide: true,
                fixedHeader: true,
                "pageLength": 15,
                "lengthMenu": [15, 25, 50, 100],
                "dom": 'rt<"dataTables_bottom"ip>',
                ajax: {
                    url: listUrl,
                    type: 'GET'
                },
                columns: [
                    { data: 'id', name: 'id', orderable: false, searchable: false, render: function(data, type, row) {
                        return '<span class="font-medium text-gray-900">' + data + '</span>';
                    }},
                    { data: 'date', name: 'date', orderable: true },
                    { data: 'user', name: 'user_id', orderable: true },
                    { data: 'category', name: 'category', orderable: true },
                    { data: 'amount', name: 'amount', orderable: true },
                    // `note` is the DB column; use it for server-side searching
                    { data: 'description', name: 'note', orderable: true },
                    {
                        data: 'id', 
                        orderable: false, 
                        searchable: false,
                        render: function (data, type, row) {
                            var btns = '<div class="flex items-center justify-center gap-3">';
                            btns += '<a onclick="openEditModal(' + data + ')" class="text-sky-600 hover:text-sky-800 font-medium text-sm cursor-pointer" title="Edit">Edit</a>';
                            btns += '<a onclick="openDeleteModal(' + data + ', \'' + row.user + '\', \'' + row.amount + '\')" class="text-red-600 hover:text-red-800 font-medium text-sm cursor-pointer" title="Delete">Delete</a>';
                            btns += '</div>';
                            return btns;
                        }
                    }
                ],
                order: [[1, 'desc']],
            });

            // Handle page length change
            document.getElementById('length-select').addEventListener('change', function(e) {
                table.page.len(parseInt(e.target.value)).draw();
            });

            // Debounce helper for reducing requests while typing
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

            // Handle search input (debounced)
            var onSearchInput = debounce(function (e) {
                var q = e.target.value.trim();
                table.search(q).draw();
            }, 300);

            document.getElementById('search-input').addEventListener('input', onSearchInput);

            // Close modals when clicking outside
            $(document).on('click', function(e) {
                if (e.target.id === 'createModal') closeCreateModal();
                if (e.target.id === 'editModal') closeEditModal();
                if (e.target.id === 'deleteModal') closeDeleteModal();
            });

            // Handle create form submission
            document.getElementById('createForm').addEventListener('submit', function(e) {
                e.preventDefault();
                $.ajax({
                    url: SITEURL + '/expenses',
                    type: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        closeCreateModal();
                        table.ajax.reload();
                        toastr.success('Expense created successfully');
                    },
                    error: function(error) {
                        toastr.error('Error creating expense');
                    }
                });
            });

            // Handle edit form submission
            document.getElementById('editForm').addEventListener('submit', function(e) {
                e.preventDefault();
                $.ajax({
                    url: this.action,
                    type: 'PUT',
                    data: $(this).serialize(),
                    success: function(response) {
                        closeEditModal();
                        table.ajax.reload();
                        toastr.success('Expense updated successfully');
                    },
                    error: function(error) {
                        toastr.error('Error updating expense');
                    }
                });
            });
        });
    </script>
    @endpush
@endsection
