@props([
    'tableId' => 'datatable',
    'ajaxUrl' => '',
    'columns' => [],
])

@php
    $hasActions = !empty($slot) || isset($actions);
@endphp

<div class="overflow-x-auto bg-white rounded-lg border border-gray-200 shadow-sm">
    <table id="{{ $tableId }}" class="min-w-full divide-y divide-gray-200 text-sm">
        <thead class="bg-gray-50">
            <tr>
                @foreach($columns as $col)
                    <th class="px-4 py-3 text-left font-semibold text-gray-700">{{ $col['label'] }}</th>
                @endforeach
                @if($hasActions)
                    <th class="px-4 py-3 text-center font-semibold text-gray-700">Actions</th>
                @endif
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        let table = $('#{{ $tableId }}').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ $ajaxUrl }}',
            responsive: true,
            columns: [
                @foreach($columns as $col)
                    { data: '{{ $col['key'] }}', name: '{{ $col['key'] }}' },
                @endforeach
                @if($hasActions)
                    { data: 'id', orderable: false, render: function(data){
                        return `{{ addslashes((string)$slot) }}`.replace(/__ID__/g, data);
                    }}
                @endif
            ],
            pageLength: 20,
            lengthMenu: [20,50,100,500],
            order: [[0, 'asc']],
            language: {
                processing: '<div class="spinner"></div>',
                search: 'Search:',
                lengthMenu: 'Show _MENU_ entries',
                info: 'Showing _START_ to _END_ of _TOTAL_ entries',
                infoEmpty: 'Showing 0 to 0 of 0 entries',
                loadedRecords: 'Loaded _TOTAL_ records',
                paginate: {
                    first: 'First',
                    last: 'Last',
                    next: 'Next',
                    previous: 'Previous'
                }
            }
        });
    });
</script>
@endpush
