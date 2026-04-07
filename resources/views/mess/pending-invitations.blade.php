@extends('layouts.app')

@section('content')
<div class="w-full">

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

    @if (session('error'))
        <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg flex items-center justify-between text-sm">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                </svg>
                <span class="text-red-800">{{ session('error') }}</span>
            </div>
            <button onclick="this.parentElement.style.display='none'" class="text-red-600 hover:text-red-800">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                </svg>
            </button>
        </div>
    @endif

    @if($pendingUsers->count() > 0)
        <!-- Data Table -->
        <div class="bg-white rounded-lg border border-gray-200 shadow-xs overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700 text-xs">Name</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700 text-xs">Email</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700 text-xs">Requested On</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-700 text-xs">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($pendingUsers as $pendingUser)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-2 text-gray-900 font-medium">{{ $pendingUser->user->name ?? 'Unknown User' }}</td>
                            <td class="px-4 py-2 text-gray-600 text-xs">{{ $pendingUser->user->email ?? 'N/A' }}</td>
                            <td class="px-4 py-2 text-gray-600 text-xs">{{ $pendingUser->created_at->format('M d, Y') }}</td>
                            <td class="px-4 py-2 text-center">
                                <div class="flex items-center justify-center gap-1">
                                    <button type="button" onclick="openApproveModal({{ $pendingUser->id }}, '{{ $pendingUser->user->name }}')" class="p-1.5 text-green-600 hover:bg-green-100 rounded transition-colors" title="Approve">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    </button>
                                    <button type="button" onclick="openRejectModal({{ $pendingUser->id }}, '{{ $pendingUser->user->name }}')" class="p-1.5 text-red-600 hover:bg-red-100 rounded transition-colors" title="Reject">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="p-8 bg-white rounded-lg border border-gray-200 flex flex-col items-center justify-center text-center">
            <svg class="w-12 h-12 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <h3 class="text-lg font-semibold text-gray-900 mb-1">No Pending Requests</h3>
            <p class="text-gray-600 text-sm mb-6">All users have been approved. Your mess is all set!</p>
            <a href="{{ route('dashboard') }}" class="px-4 py-2 bg-sky-600 text-white text-sm font-medium rounded-md hover:bg-sky-700 transition-colors">
                Back to Dashboard
            </a>
        </div>
    @endif

</div>

<!-- Approve Confirmation Modal -->
<div id="approveModal" class="hidden pointer-events-none fixed inset-0 bg-black/50 flex items-center justify-center z-50 transition-opacity duration-300 opacity-0 p-4">
    <div class="bg-white rounded-lg shadow-xl max-w-sm w-full">
        <!-- Modal Header -->
        <div class="flex items-center justify-between p-4 border-b border-gray-200">
            <div class="flex items-center gap-3">
                <div class="flex items-center justify-center w-10 h-10 rounded-full bg-green-100">
                    <svg class="w-6 h-6 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900">Approve Request?</h3>
            </div>
            <button type="button" onclick="closeApproveModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <!-- Modal Content -->
        <div class="p-4">
            <p class="text-sm text-gray-600">Approve <span id="approveMemberName" class="font-semibold">-</span> to join {{ $activeMess->name }}?</p>
            <p class="text-xs text-green-700 bg-green-50 p-2 rounded mt-3">They will have member access to this mess.</p>
        </div>

        <!-- Modal Actions -->
        <div class="flex gap-2 p-4 border-t border-gray-200">
            <button type="button" onclick="closeApproveModal()" class="flex-1 px-4 py-2 text-gray-700 bg-gray-200 hover:bg-gray-300 text-sm font-medium rounded">Cancel</button>
            <button type="button" onclick="confirmApprove()" class="flex-1 px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded">Approve</button>
        </div>
    </div>
</div>

<!-- Reject Confirmation Modal -->
<div id="rejectModal" class="hidden pointer-events-none fixed inset-0 bg-black/50 flex items-center justify-center z-50 transition-opacity duration-300 opacity-0 p-4">
    <div class="bg-white rounded-lg shadow-xl max-w-sm w-full">
        <!-- Modal Header -->
        <div class="flex items-center justify-between p-4 border-b border-gray-200">
            <div class="flex items-center gap-3">
                <div class="flex items-center justify-center w-10 h-10 rounded-full bg-red-100">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4v.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900">Reject Request?</h3>
            </div>
            <button type="button" onclick="closeRejectModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <!-- Modal Content -->
        <div class="p-4">
            <p class="text-sm text-gray-600">Reject request from <span id="rejectMemberName" class="font-semibold">-</span>?</p>
            <p class="text-xs text-red-700 bg-red-50 p-2 rounded mt-3">They will not have access to this mess.</p>
        </div>

        <!-- Modal Actions -->
        <div class="flex gap-2 p-4 border-t border-gray-200">
            <button type="button" onclick="closeRejectModal()" class="flex-1 px-4 py-2 text-gray-700 bg-gray-200 hover:bg-gray-300 text-sm font-medium rounded">Cancel</button>
            <button type="button" onclick="confirmReject()" class="flex-1 px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded">Reject</button>
        </div>
    </div>
</div>

<script>
    let currentApproveMemberId = null;
    let currentRejectMemberId = null;

    // Approve Modal Functions
    function openApproveModal(memberId, memberName) {
        currentApproveMemberId = memberId;
        $('#approveMemberName').text(memberName);
        
        const $modal = $('#approveModal');
        $modal.removeClass('hidden pointer-events-none');
        void $modal[0].offsetWidth;
        $modal.removeClass('opacity-0').addClass('opacity-100');
    }

    function closeApproveModal() {
        const $modal = $('#approveModal');
        $modal.addClass('opacity-0').removeClass('opacity-100');
        setTimeout(() => {
            $modal.addClass('hidden pointer-events-none');
            currentApproveMemberId = null;
        }, 300);
    }

    function confirmApprove() {
        if (!currentApproveMemberId) return;
        
        const $form = $('<form>')
            .attr('method', 'POST')
            .attr('action', `/mess/pending-invitations/${currentApproveMemberId}/approve`)
            .html(`@csrf`);
        $('body').append($form);
        $form.submit();
    }

    // Reject Modal Functions
    function openRejectModal(memberId, memberName) {
        currentRejectMemberId = memberId;
        $('#rejectMemberName').text(memberName);
        
        const $modal = $('#rejectModal');
        $modal.removeClass('hidden pointer-events-none');
        void $modal[0].offsetWidth;
        $modal.removeClass('opacity-0').addClass('opacity-100');
    }

    function closeRejectModal() {
        const $modal = $('#rejectModal');
        $modal.addClass('opacity-0').removeClass('opacity-100');
        setTimeout(() => {
            $modal.addClass('hidden pointer-events-none');
            currentRejectMemberId = null;
        }, 300);
    }

    function confirmReject() {
        if (!currentRejectMemberId) return;
        
        const $form = $('<form>')
            .attr('method', 'POST')
            .attr('action', `/mess/pending-invitations/${currentRejectMemberId}/reject`)
            .html(`@csrf`);
        $('body').append($form);
        $form.submit();
    }

    // Close modals when clicking outside
    $('#approveModal').on('click', function(e) {
        if (e.target === this) {
            closeApproveModal();
        }
    });

    $('#rejectModal').on('click', function(e) {
        if (e.target === this) {
            closeRejectModal();
        }
    });
</script>
@endsection
