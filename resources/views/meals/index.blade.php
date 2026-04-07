@extends('layouts.app')

@section('content')
    <div class="">
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
            <form action="{{ route('meals.index') }}" method="GET" class="flex-1 flex gap-2 w-full sm:w-auto">
                <input 
                    type="date" 
                    name="filter_date" 
                    class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-transparent"
                    value="{{ $filterDate ?? '' }}">
                <select name="filter_member" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-transparent">
                    <option value="">All Members</option>
                    @foreach ($members as $member)
                        <option value="{{ $member->id }}" {{ $filterMember == $member->id ? 'selected' : '' }}>{{ $member->name }}</option>
                    @endforeach
                </select>
                <button type="submit" class="px-3 py-2 bg-sky-600 text-white text-sm font-medium rounded-lg hover:bg-sky-700 transition-colors">
                    Filter
                </button>
                <a href="{{ route('meals.index') }}" class="px-3 py-2 bg-gray-200 text-gray-900 text-sm font-medium rounded-lg hover:bg-gray-300 transition-colors">
                    Reset
                </a>
            </form>
            @can('meals.create')
                <a href="{{ route('meals.create') }}" class="px-4 py-2 bg-sky-600 text-white text-sm font-medium rounded-lg hover:bg-sky-700 transition-colors inline-flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Add
                </a>
            @endcan
        </div>

        @can('meals.view')
            <!-- Data Table -->
            <div class="bg-white rounded-lg border border-gray-200 shadow-xs overflow-hidden">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-4 py-3 text-left font-semibold text-gray-700 text-xs">Member</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-700 text-xs">Date</th>
                            <th class="px-4 py-3 text-center font-semibold text-gray-700 text-xs">B</th>
                            <th class="px-4 py-3 text-center font-semibold text-gray-700 text-xs">L</th>
                            <th class="px-4 py-3 text-center font-semibold text-gray-700 text-xs">D</th>
                            <th class="px-4 py-3 text-center font-semibold text-gray-700 text-xs">Total</th>
                            @canany(['meals.update', 'meals.delete'])
                                <th class="px-4 py-3 text-center font-semibold text-gray-700 text-xs">Actions</th>
                            @endcanany
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse ($meals as $meal)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-4 py-2 text-gray-900 font-medium">{{ $meal->user->name }}</td>
                                <td class="px-4 py-2 text-gray-600 text-xs">{{ $meal->date->format('M d') }}</td>
                                <td class="px-4 py-2 text-center">
                                    @if ($meal->breakfast_count > 0)
                                        <span class="inline-block px-2 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded">{{ $meal->breakfast_count }}</span>
                                    @else
                                        <span class="text-gray-300">-</span>
                                    @endif
                                </td>
                                <td class="px-4 py-2 text-center">
                                    @if ($meal->lunch_count > 0)
                                        <span class="inline-block px-2 py-1 bg-sky-100 text-sky-800 text-xs font-semibold rounded">{{ $meal->lunch_count }}</span>
                                    @else
                                        <span class="text-gray-300">-</span>
                                    @endif
                                </td>
                                <td class="px-4 py-2 text-center">
                                    @if ($meal->dinner_count > 0)
                                        <span class="inline-block px-2 py-1 bg-orange-100 text-orange-800 text-xs font-semibold rounded">{{ $meal->dinner_count }}</span>
                                    @else
                                        <span class="text-gray-300">-</span>
                                    @endif
                                </td>
                                <td class="px-4 py-2 text-center font-semibold text-gray-900">{{ $meal->total_meal_count }}</td>
                                @canany(['meals.update', 'meals.delete'])
                                    <td class="px-4 py-2 text-center">
                                        <div class="flex items-center justify-center gap-1">
                                            @can('update', $meal)
                                                <button type="button" onclick="openEditModal({{ $meal->id }})" class="p-1.5 text-sky-600 hover:bg-sky-100 rounded transition-colors" title="Edit">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                    </svg>
                                                </button>
                                            @endcan
                                            @can('delete', $meal)
                                                <button type="button" onclick="openDeleteModal({{ $meal->id }}, '{{ $meal->user->name }}', '{{ $meal->date->format('M d') }}')" class="p-1.5 text-red-600 hover:bg-red-100 rounded transition-colors" title="Delete">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                    </svg>
                                                </button>
                                            @endcan
                                        </div>
                                    </td>
                                @endcanany
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-8 text-center text-gray-500">No meal records</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4 flex justify-center text-sm">
                {{ $meals->appends(request()->query())->links() }}
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

    <!-- Edit Modal -->
    <div id="editModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4 transition-opacity duration-300 opacity-0">
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
                <form id="editForm" method="POST" onsubmit="handleModalSubmit(event)">
                    @csrf
                    @method('PUT')

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

    <script>
        let currentMealId = null;
        let currentDeleteMealId = null;

        // Edit Modal Functions
        function openEditModal(mealId) {
            currentMealId = mealId;
            
            // Fetch meal data via AJAX
            $.ajax({
                url: `/meals/${mealId}/edit`,
                type: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                success: function(data) {
                    // Populate modal with data
                    $('#modalMemberName').text(data.user_name);
                    $('#modalDate').text(data.date_display);
                    $('#modalUserId').val(data.user_id);
                    $('#modalDateValue').val(data.date);
                    $('#breakfast_count').val(data.breakfast_count);
                    $('#lunch_count').val(data.lunch_count);
                    $('#dinner_count').val(data.dinner_count);
                    
                    // Update total
                    updateModalTotal();
                    
                    // Set form action
                    $('#editForm').attr('action', `/meals/${mealId}`);
                    
                    // Show modal with fade-in effect
                    const $modal = $('#editModal');
                    $modal.removeClass('hidden');
                    // Trigger reflow to enable transition
                    void $modal[0].offsetWidth;
                    $modal.removeClass('opacity-0').addClass('opacity-100');
                },
                error: function() {
                    console.error('Error fetching meal data');
                    alert('Failed to load meal data');
                }
            });
        }

        function closeEditModal() {
            const $modal = $('#editModal');
            $modal.addClass('opacity-0').removeClass('opacity-100');
            // Wait for transition to complete before hiding
            setTimeout(() => {
                $modal.addClass('hidden');
                currentMealId = null;
            }, 300);
        }

        function incrementMealModal(fieldId) {
            const $field = $(`#${fieldId}`);
            $field.val((parseFloat($field.val()) + 0.5).toFixed(1));
            updateModalTotal();
        }

        function decrementMealModal(fieldId) {
            const $field = $(`#${fieldId}`);
            const value = parseFloat($field.val());
            if (value > 0) {
                $field.val(Math.max(0, (value - 0.5).toFixed(1)));
                updateModalTotal();
            }
        }

        function updateModalTotal() {
            const breakfast = parseFloat($('#breakfast_count').val()) || 0;
            const lunch = parseFloat($('#lunch_count').val()) || 0;
            const dinner = parseFloat($('#dinner_count').val()) || 0;
            const total = (breakfast + lunch + dinner).toFixed(1);
            $('#modalTotal').text(total);
        }

        function handleModalSubmit(event) {
            event.preventDefault();
            
            const mealId = currentMealId;
            if (!mealId) return;
            
            const formData = {
                _method: 'PUT',
                _token: $('meta[name="csrf-token"]').attr('content'),
                user_id: $('#modalUserId').val(),
                date: $('#modalDateValue').val(),
                breakfast_count: $('#breakfast_count').val(),
                lunch_count: $('#lunch_count').val(),
                dinner_count: $('#dinner_count').val(),
            };
            
            $.ajax({
                url: `/meals/${mealId}`,
                type: 'POST',
                data: formData,
                success: function(response) {
                    toastr.success('Meal record updated successfully');
                    closeEditModal();
                    setTimeout(() => {
                        location.reload();
                    }, 500);
                },
                error: function(xhr) {
                    if (xhr.status === 403) {
                        toastr.error('This month is closed. No modifications allowed.');
                    } else if (xhr.responseJSON && xhr.responseJSON.message) {
                        toastr.error(xhr.responseJSON.message);
                    } else {
                        toastr.error('Failed to update meal record');
                    }
                }
            });
        }

        // Close edit modal when clicking outside
        $('#editModal').on('click', function(event) {
            if (event.target === this) {
                closeEditModal();
            }
        });

        // Delete Modal Functions
        function openDeleteModal(mealId, memberName, date) {
            currentDeleteMealId = mealId;
            $('#deleteModalMember').text(memberName);
            $('#deleteModalDate').text(date);
            
            // Show modal with fade-in effect
            const $modal = $('#deleteModal');
            $modal.removeClass('hidden');
            // Trigger reflow to enable transition
            void $modal[0].offsetWidth;
            $modal.removeClass('opacity-0').addClass('opacity-100');
        }

        function closeDeleteModal() {
            const $modal = $('#deleteModal');
            $modal.addClass('opacity-0').removeClass('opacity-100');
            // Wait for transition to complete before hiding
            setTimeout(() => {
                $modal.addClass('hidden');
                currentDeleteMealId = null;
            }, 300);
        }

        function confirmDelete() {
            if (!currentDeleteMealId) return;
            
            // Create hidden form and submit
            const $form = $('<form>')
                .attr('method', 'POST')
                .attr('action', `/meals/${currentDeleteMealId}`)
                .html(`
                    @csrf
                    @method('DELETE')
                `);
            $('body').append($form);
            $form.submit();
        }

        // Close delete modal when clicking outside
        $('#deleteModal').on('click', function(event) {
            if (event.target === this) {
                closeDeleteModal();
            }
        });
    </script>
@endsection
