@extends('layouts.app')

@use('App\Enums\RoleEnum')

@php
use App\Models\MessUser;
@endphp

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

        @can('members.view')
            @if ($members->count() > 0)
                <!-- Data Table -->
                <div class="bg-white rounded-lg border border-gray-200 shadow-xs overflow-hidden">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-4 py-3 text-left font-semibold text-gray-700 text-xs">Name</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-700 text-xs">Email</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-700 text-xs">Role</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-700 text-xs">Joined</th>
                                @canany(['members.update', 'members.delete', 'members.manage-roles'])
                                    <th class="px-4 py-3 text-center font-semibold text-gray-700 text-xs">Actions</th>
                                @endcanany
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach ($members as $messUser)
                                @php
                                    $member = $messUser->user;
                                @endphp
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-4 py-2 text-gray-900 font-medium">{{ $member->name }}</td>
                                    <td class="px-4 py-2 text-gray-600 text-xs">{{ $member->email }}</td>
                                    <td class="px-4 py-2">
                                        @forelse($member->roles as $role)
                                            <span class="inline-block px-2 py-1 {{ $role->name === 'manager' ? 'bg-amber-100 text-amber-800' : 'bg-blue-100 text-blue-800' }} text-xs font-semibold rounded capitalize">
                                                {{ $role->name }}
                                            </span>
                                        @empty
                                            <span class="inline-block px-2 py-1 bg-gray-100 text-gray-800 text-xs font-semibold rounded">
                                                No Role
                                            </span>
                                        @endforelse
                                    </td>
                                    <td class="px-4 py-2 text-gray-600 text-xs">{{ $messUser->created_at->format('M d, Y') }}</td>
                                    @canany(['members.update', 'members.delete', 'members.manage-roles'])
                                        <td class="px-4 py-2 text-center">
                                            <div class="flex items-center justify-center gap-1">
                                                @can('update', $member)
                                                    <button type="button" onclick="openEditModal({{ $member->id }})" class="p-1.5 text-sky-600 hover:bg-sky-100 rounded transition-colors" title="Edit">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                        </svg>
                                                    </button>
                                                @endcan

                                                @can('members.manage-roles')
                                                    @if(!$member->hasRole(RoleEnum::MANAGER->value) && $activeMess->manager_id !== $member->id)
                                                        <button type="button" onclick="openManagerModal({{ $member->id }}, '{{ $member->name }}')" class="p-1.5 text-amber-600 hover:bg-amber-100 rounded transition-colors" title="Make Manager">
                                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                            </svg>
                                                        </button>
                                                    @elseif($activeMess->manager_id === $member->id)
                                                        <span class="inline-block px-2 py-1 bg-amber-100 text-amber-800 text-xs font-semibold rounded">Manager</span>
                                                    @endif
                                                @endcan

                                                @can('delete', $member)
                                                    @if($activeMess->manager_id !== $member->id)
                                                        <button type="button" onclick="openDeleteModal({{ $member->id }}, '{{ $member->name }}')" class="p-1.5 text-red-600 hover:bg-red-100 rounded transition-colors" title="Remove">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                            </svg>
                                                        </button>
                                                    @endif
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
                    {{ $members->links() }}
                </div>
            @else
                <div class="p-4 bg-blue-50 border border-blue-200 rounded-lg flex items-center gap-3">
                    <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 5v8a2 2 0 01-2 2h-5l-5 4v-4H4a2 2 0 01-2-2V5a2 2 0 012-2h12a2 2 0 012 2z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="text-sm text-blue-800">No members found in this mess.</span>
                </div>
            @endif
        @else
            <div class="p-4 bg-red-50 border border-red-200 rounded-lg flex items-center gap-3">
                <svg class="w-5 h-5 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M13.477 14.89A6 6 0 015.11 2.476a6 6 0 018.367 8.414zm1.414-5.27a8 8 0 11-11.313-11.313 8 8 0 0111.313 11.313z" clip-rule="evenodd"></path>
                </svg>
                <span class="text-sm text-red-800">You don't have permission to view members.</span>
            </div>
        @endcan

    </div>

    <!-- Edit Member Modal -->
    <div id="editModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4 transition-opacity duration-300 opacity-0">
        <div class="bg-white rounded-lg max-w-md w-full shadow-xl max-h-[90vh] overflow-y-auto">
            <!-- Modal Header -->
            <div class="flex items-center justify-between p-4 border-b border-gray-200 sticky top-0 bg-white">
                <h3 class="text-lg font-semibold text-gray-900">Edit Member</h3>
                <button type="button" onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Modal Content -->
            <div class="p-4">
                <form id="editForm" method="POST" class="space-y-3">
                    @csrf
                    @method('PUT')

                    <!-- Member Info -->
                    <div class="p-3 bg-gray-50 rounded">
                        <p class="text-xs text-gray-600 font-medium mb-2">Member</p>
                        <p id="editMemberName" class="font-semibold text-gray-900">-</p>
                        <p id="editMemberEmail" class="text-xs text-gray-600 mt-1">-</p>
                    </div>

                    <!-- Name Input -->
                    <div>
                        <label for="editName" class="block text-xs font-medium text-gray-600 mb-1">Name <span class="text-red-600">*</span></label>
                        <input type="text" id="editName" name="name" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-transparent" required>
                    </div>

                    <!-- Email Input -->
                    <div>
                        <label for="editEmail" class="block text-xs font-medium text-gray-600 mb-1">Email <span class="text-red-600">*</span></label>
                        <input type="email" id="editEmail" name="email" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-transparent" required>
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

    <!-- Make Manager Modal -->
    <div id="managerModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4 transition-opacity duration-300 opacity-0">
        <div class="bg-white rounded-lg max-w-sm w-full shadow-xl">
            <!-- Modal Header -->
            <div class="flex items-center justify-between p-4 border-b border-gray-200">
                <div class="flex items-center gap-3">
                    <div class="flex items-center justify-center w-10 h-10 rounded-full bg-amber-100">
                        <svg class="w-6 h-6 text-amber-600" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900">Make Manager?</h3>
                </div>
                <button type="button" onclick="closeManagerModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Modal Content -->
            <div class="p-4">
                <p class="text-sm text-gray-600 mb-3">Make <span id="managerModalMemberName" class="font-semibold">-</span> the manager of {{ $activeMess->name }}?</p>
                <p class="text-xs text-amber-700 bg-amber-50 p-2 rounded">Manager will have administrative control over this mess.</p>
            </div>

            <!-- Modal Actions -->
            <div class="flex gap-2 p-4 border-t border-gray-200">
                <button type="button" onclick="closeManagerModal()" class="flex-1 px-4 py-2 text-gray-700 bg-gray-200 hover:bg-gray-300 text-sm font-medium rounded">Cancel</button>
                <button type="button" onclick="confirmMakeManager()" class="flex-1 px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white text-sm font-medium rounded">Make Manager</button>
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
                    <h3 class="text-lg font-semibold text-gray-900">Remove Member?</h3>
                </div>
                <button type="button" onclick="closeDeleteModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Modal Content -->
            <div class="p-4">
                <p class="text-sm text-gray-600 mb-2">Remove <span id="deleteModalMemberName" class="font-semibold">-</span> from {{ $activeMess->name }}?</p>
                <p class="text-xs text-red-700 bg-red-50 p-2 rounded">This action cannot be undone.</p>
            </div>

            <!-- Modal Actions -->
            <div class="flex gap-2 p-4 border-t border-gray-200">
                <button type="button" onclick="closeDeleteModal()" class="flex-1 px-4 py-2 text-gray-700 bg-gray-200 hover:bg-gray-300 text-sm font-medium rounded">Cancel</button>
                <button type="button" onclick="confirmDelete()" class="flex-1 px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded">Remove</button>
            </div>
        </div>
    </div>

    <script>
        let currentEditMemberId = null;
        let currentManagerMemberId = null;
        let currentDeleteMemberId = null;

        // Edit Modal Functions
        function openEditModal(memberId) {
            currentEditMemberId = memberId;
            
            // Fetch member data via AJAX
            $.ajax({
                url: `/members/${memberId}/edit`,
                type: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                success: function(data) {
                    // Populate modal with data
                    $('#editMemberName').text(data.name);
                    $('#editMemberEmail').text(data.email);
                    $('#editName').val(data.name);
                    $('#editEmail').val(data.email);
                    
                    // Set form action
                    $('#editForm').attr('action', `/members/${memberId}`);
                    
                    // Show modal with fade-in effect
                    const $modal = $('#editModal');
                    $modal.removeClass('hidden');
                    void $modal[0].offsetWidth;
                    $modal.removeClass('opacity-0').addClass('opacity-100');
                },
                error: function() {
                    console.error('Error fetching member data');
                    alert('Failed to load member data');
                }
            });
        }

        function closeEditModal() {
            const $modal = $('#editModal');
            $modal.addClass('opacity-0').removeClass('opacity-100');
            setTimeout(() => {
                $modal.addClass('hidden');
                currentEditMemberId = null;
            }, 300);
        }

        // Manager Modal Functions
        function openManagerModal(memberId, memberName) {
            currentManagerMemberId = memberId;
            $('#managerModalMemberName').text(memberName);
            
            const $modal = $('#managerModal');
            $modal.removeClass('hidden');
            void $modal[0].offsetWidth;
            $modal.removeClass('opacity-0').addClass('opacity-100');
        }

        function closeManagerModal() {
            const $modal = $('#managerModal');
            $modal.addClass('opacity-0').removeClass('opacity-100');
            setTimeout(() => {
                $modal.addClass('hidden');
                currentManagerMemberId = null;
            }, 300);
        }

        function confirmMakeManager() {
            if (!currentManagerMemberId) return;
            
            const $form = $('<form>')
                .attr('method', 'POST')
                .attr('action', `/members/${currentManagerMemberId}/change-manager`)
                .html(`@csrf`);
            $('body').append($form);
            $form.submit();
        }

        // Delete Modal Functions
        function openDeleteModal(memberId, memberName) {
            currentDeleteMemberId = memberId;
            $('#deleteModalMemberName').text(memberName);
            
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
                currentDeleteMemberId = null;
            }, 300);
        }

        function confirmDelete() {
            if (!currentDeleteMemberId) return;
            
            const $form = $('<form>')
                .attr('method', 'POST')
                .attr('action', `/members/${currentDeleteMemberId}`)
                .html(`
                    @csrf
                    @method('DELETE')
                `);
            $('body').append($form);
            $form.submit();
        }

        // Close modals when clicking outside
        $('#editModal').on('click', function(event) {
            if (event.target === this) {
                closeEditModal();
            }
        });

        $('#managerModal').on('click', function(event) {
            if (event.target === this) {
                closeManagerModal();
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
