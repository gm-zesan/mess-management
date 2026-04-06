@extends('layouts.app')

@section('content')
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

        <!-- Filter & Action Bar -->
        <div class="mb-4 flex flex-col sm:flex-row gap-3 items-start sm:items-end justify-between">
            <div class="text-sm">
                <span class="text-gray-600">Active Month:</span>
                <span class="font-semibold text-gray-900">{{ $activeMonth?->name ?? 'No Active Month' }}</span>
            </div>
            @can('expenses.create')
                <button type="button" onclick="openCreateModal()" class="px-4 py-2 bg-sky-600 text-white text-sm font-medium rounded-lg hover:bg-sky-700 transition-colors inline-flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Add
                </button>
            @endcan
        </div>

        @can('expenses.view')
            @if ($expenses->count())
                <!-- Data Table -->
                <div class="bg-white rounded-lg border border-gray-200 shadow-xs overflow-hidden">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-4 py-3 text-left font-semibold text-gray-700 text-xs">Date</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-700 text-xs">Member</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-700 text-xs">Category</th>
                                <th class="px-4 py-3 text-right font-semibold text-gray-700 text-xs">Amount</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-700 text-xs">Note</th>
                                @canany(['expenses.update', 'expenses.delete'])
                                    <th class="px-4 py-3 text-center font-semibold text-gray-700 text-xs">Actions</th>
                                @endcanany
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach ($expenses as $expense)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-4 py-2 text-gray-600 text-xs">{{ $expense->date->format('M d') }}</td>
                                    <td class="px-4 py-2 text-gray-900 font-medium">{{ $expense->user?->name ?? 'N/A' }}</td>
                                    <td class="px-4 py-2">
                                        <span class="inline-block px-2 py-1 bg-blue-100 text-blue-800 text-xs font-semibold rounded capitalize">{{ $expense->category }}</span>
                                    </td>
                                    <td class="px-4 py-2 text-right font-semibold text-gray-900">৳ {{ number_format($expense->amount, 2) }}</td>
                                    <td class="px-4 py-2 text-gray-600 text-xs">{{ Str::limit($expense->note, 30) ?? '-' }}</td>
                                    @canany(['expenses.update', 'expenses.delete'])
                                        <td class="px-4 py-2 text-center">
                                            <div class="flex items-center justify-center gap-1">
                                                @can('update', $expense)
                                                    <button type="button" onclick="openEditModal({{ $expense->id }})" class="p-1.5 text-sky-600 hover:bg-sky-100 rounded transition-colors" title="Edit">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                        </svg>
                                                    </button>
                                                @endcan
                                                @can('delete', $expense)
                                                    <button type="button" onclick="openDeleteModal({{ $expense->id }}, '{{ $expense->user->name }}', '৳ ' + {{ $expense->amount }})" class="p-1.5 text-red-600 hover:bg-red-100 rounded transition-colors" title="Delete">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                        </svg>
                                                    </button>
                                                @endcan
                                            </div>
                                        </td>
                                    @endcanany
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-4 flex justify-center text-sm">
                    {{ $expenses->links() }}
                </div>
            @else
                <div class="p-4 bg-blue-50 border border-blue-200 rounded-lg flex items-center gap-3">
                    <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 5v8a2 2 0 01-2 2h-5l-5 4v-4H4a2 2 0 01-2-2V5a2 2 0 012-2h12a2 2 0 012 2z" clip-rule="evenodd"></path>
                    </svg>
                    <div>
                        <span class="text-sm text-blue-800">No expenses found.</span>
                        @can('expenses.create')
                            <a href="{{ route('expenses.create') }}" class="ml-2 font-medium text-blue-600 hover:text-blue-800">Create one</a>
                        @endcan
                    </div>
                </div>
            @endif
        @else
            <div class="p-4 bg-red-50 border border-red-200 rounded-lg flex items-center gap-3">
                <svg class="w-5 h-5 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M13.477 14.89A6 6 0 015.11 2.476a6 6 0 018.367 8.414zm1.414-5.27a8 8 0 11-11.313-11.313 8 8 0 0111.313 11.313z" clip-rule="evenodd"></path>
                </svg>
                <span class="text-sm text-red-800">You don't have permission to view expenses.</span>
            </div>
        @endcan

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

    <script>
        let currentDeleteExpenseId = null;
        let currentEditExpenseId = null;

        // Create Modal Functions
        function openCreateModal() {
            $('#createForm')[0].reset();
            const $modal = $('#createModal');
            $modal.removeClass('hidden');
            void $modal[0].offsetWidth;
            $modal.removeClass('opacity-0').addClass('opacity-100');
        }

        function closeCreateModal() {
            const $modal = $('#createModal');
            $modal.addClass('opacity-0').removeClass('opacity-100');
            setTimeout(() => {
                $modal.addClass('hidden');
            }, 300);
        }

        // Edit Modal Functions
        function openEditModal(expenseId) {
            currentEditExpenseId = expenseId;
            
            // Fetch expense data via AJAX
            $.ajax({
                url: `/expenses/${expenseId}/edit`,
                type: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                success: function(data) {
                    // Populate modal with data
                    $('#editMemberName').text(data.user_name);
                    $('#editDateDisplay').text(data.date_display);
                    $('#editUserId').val(data.user_id);
                    $('#editDateValue').val(data.date);
                    $('#editCategory').val(data.category);
                    $('#editAmount').val(data.amount);
                    $('#editNote').val(data.note);
                    
                    // Set form action
                    $('#editForm').attr('action', `/expenses/${expenseId}`);
                    
                    // Show modal with fade-in effect
                    const $modal = $('#editModal');
                    $modal.removeClass('hidden');
                    void $modal[0].offsetWidth;
                    $modal.removeClass('opacity-0').addClass('opacity-100');
                },
                error: function() {
                    console.error('Error fetching expense data');
                    alert('Failed to load expense data');
                }
            });
        }

        function closeEditModal() {
            const $modal = $('#editModal');
            $modal.addClass('opacity-0').removeClass('opacity-100');
            setTimeout(() => {
                $modal.addClass('hidden');
                currentEditExpenseId = null;
            }, 300);
        }

        // Delete Modal Functions
        function openDeleteModal(expenseId, memberName, amount) {
            currentDeleteExpenseId = expenseId;
            $('#deleteModalMember').text(memberName);
            $('#deleteModalAmount').text(amount);
            
            const $modal = $('#deleteModal');
            $modal.removeClass('hidden');
            void $modal[0].offsetWidth;
            $modal.removeClass('opacity-0').addClass('opacity-100');
        }

        function closeDeleteModal() {
            const $modal = $('#deleteModal');
            $modal.addClass('opacity-0').removeClass('opacity-100');
            setTimeout(() => {
                $modal.addClass('hidden');
                currentDeleteExpenseId = null;
            }, 300);
        }

        function confirmDelete() {
            if (!currentDeleteExpenseId) return;
            
            const $form = $('<form>')
                .attr('method', 'POST')
                .attr('action', `/expenses/${currentDeleteExpenseId}`)
                .html(`
                    @csrf
                    @method('DELETE')
                `);
            $('body').append($form);
            $form.submit();
        }

        // Close modals when clicking outside
        $('#createModal').on('click', function(event) {
            if (event.target === this) {
                closeCreateModal();
            }
        });

        $('#editModal').on('click', function(event) {
            if (event.target === this) {
                closeEditModal();
            }
        });

        $('#deleteModal').on('click', function(event) {
            if (event.target === this) {
                closeDeleteModal();
            }
        });

        // Handle edit form submission
        $('#editForm').on('submit', function(event) {
            event.preventDefault();
            this.submit();
        });
    </script>
@endsection
