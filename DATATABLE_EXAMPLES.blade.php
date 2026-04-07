<!-- 
    ================================================================================
    DATATABLE COMPONENT - SaaS-LEVEL EXAMPLES (Alpine.js + Tailwind CSS)
    ================================================================================
    
    Features:
    - Client-side search with debounce (300ms)
    - Smart numeric/string sorting
    - Pagination with smart page visibility
    - Loading states
    - Custom column formatting
    - Row click handlers
    - Searchable column control
    - Smooth transitions (transition-all duration-150)
-->

<!-- ============================================================================= -->
<!-- EXAMPLE 1: Basic Table (Minimal Setup) -->
<!-- ============================================================================= -->
@php
$columns = [
    ['key' => 'id', 'label' => 'ID', 'sortable' => true, 'searchable' => false],
    ['key' => 'name', 'label' => 'Name', 'sortable' => true, 'searchable' => true],
    ['key' => 'email', 'label' => 'Email', 'sortable' => true, 'searchable' => true],
    ['key' => 'status', 'label' => 'Status', 'sortable' => false, 'searchable' => true],
];

$data = [
    ['id' => 1, 'name' => 'John Doe', 'email' => 'john@example.com', 'status' => 'Active'],
    ['id' => 2, 'name' => 'Jane Smith', 'email' => 'jane@example.com', 'status' => 'Inactive'],
    ['id' => 3, 'name' => 'Bob Johnson', 'email' => 'bob@example.com', 'status' => 'Active'],
];
@endphp

<x-datatable 
    :columns="$columns" 
    :data="$data" 
    searchPlaceholder="Search by name or email..."
/>


<!-- ============================================================================= -->
<!-- EXAMPLE 2: Table with Actions (via format column) -->
<!-- ============================================================================= -->
@php
$columns = [
    ['key' => 'id', 'label' => 'ID', 'sortable' => true, 'searchable' => false],
    ['key' => 'name', 'label' => 'Name', 'sortable' => true],
    ['key' => 'email', 'label' => 'Email', 'sortable' => true],
    [
        'key' => 'actions',
        'label' => 'Actions',
        'sortable' => false,
        'searchable' => false,
        'format' => function($value, $row) {
            return '
                <div class="flex items-center gap-2">
                    <a href="/users/' . $row['id'] . '/edit" class="p-1.5 text-sky-600 hover:bg-sky-50 rounded transition-all duration-150">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                    </a>
                    <button onclick="confirmDelete(' . $row['id'] . ')" class="p-1.5 text-red-600 hover:bg-red-50 rounded transition-all duration-150">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </button>
                </div>
            ';
        }
    ],
];

$data = [
    ['id' => 1, 'name' => 'John Doe', 'email' => 'john@example.com'],
    ['id' => 2, 'name' => 'Jane Smith', 'email' => 'jane@example.com'],
];
@endphp

<x-datatable :columns="$columns" :data="$data" searchPlaceholder="Search users..." />


<!-- ============================================================================= -->
<!-- EXAMPLE 3: Table with Custom Formatting (Dates, Badges, etc.) -->
<!-- ============================================================================= -->
@php
$columns = [
    ['key' => 'id', 'label' => 'ID', 'sortable' => true, 'searchable' => false],
    ['key' => 'name', 'label' => 'Name', 'sortable' => true],
    [
        'key' => 'created_at',
        'label' => 'Joined',
        'sortable' => true,
        'searchable' => false,
        'format' => function($date) {
            return (new DateTime($date))->format('M d, Y');
        }
    ],
    [
        'key' => 'status',
        'label' => 'Status',
        'sortable' => false,
        'searchable' => true,
        'format' => function($status) {
            $colors = [
                'Active' => 'bg-green-100 text-green-800',
                'Inactive' => 'bg-gray-100 text-gray-800',
                'Pending' => 'bg-yellow-100 text-yellow-800',
            ];
            $class = $colors[$status] ?? 'bg-gray-100 text-gray-800';
            return '<span class="px-2.5 py-1 rounded-full text-xs font-semibold ' . $class . '">' . $status . '</span>';
        }
    ],
];

$data = [
    ['id' => 1, 'name' => 'John Doe', 'created_at' => '2025-01-15', 'status' => 'Active'],
    ['id' => 2, 'name' => 'Jane Smith', 'created_at' => '2025-02-20', 'status' => 'Active'],
    ['id' => 3, 'name' => 'Bob Wilson', 'created_at' => '2025-03-10', 'status' => 'Pending'],
];
@endphp

<x-datatable :columns="$columns" :data="$data" searchPlaceholder="Search..." />


<!-- ============================================================================= -->
<!-- EXAMPLE 4: Table with Numeric Sorting (Smart sort) -->
<!-- ============================================================================= -->
@php
$columns = [
    ['key' => 'name', 'label' => 'Product', 'sortable' => true],
    [
        'key' => 'quantity',
        'label' => 'Qty',
        'sortable' => true,
        'searchable' => false,
    ],
    [
        'key' => 'price',
        'label' => 'Price',
        'sortable' => true,
        'searchable' => false,
        'format' => fn($price) => '$' . number_format($price, 2)
    ],
];

$data = [
    ['name' => 'Widget A', 'quantity' => 100, 'price' => 19.99],
    ['name' => 'Widget B', 'quantity' => 20, 'price' => 49.99],
    ['name' => 'Widget C', 'quantity' => 5, 'price' => 199.99],
];
@endphp

<!-- Sorting now works correctly: 5 < 20 < 100 (not "100" < "20") -->
<x-datatable :columns="$columns" :data="$data" searchPlaceholder="Search products..." />


<!-- ============================================================================= -->
<!-- EXAMPLE 5: Large Dataset (Loading State) -->
<!-- ============================================================================= -->
@php
// Generate sample data
$largeData = array_map(function($i) {
    return [
        'id' => $i,
        'name' => 'User ' . $i,
        'email' => 'user' . $i . '@example.com',
        'role' => ['Admin', 'Manager', 'User'][array_rand([0, 1, 2])],
    ];
}, range(1, 100));

$columns = [
    ['key' => 'id', 'label' => 'ID', 'sortable' => true, 'searchable' => false],
    ['key' => 'name', 'label' => 'Name', 'sortable' => true],
    ['key' => 'email', 'label' => 'Email', 'sortable' => true],
    ['key' => 'role', 'label' => 'Role', 'sortable' => true],
];
@endphp

<!-- Shows loading spinner while data loads (set loading: true in controller) -->
<x-datatable :columns="$columns" :data="$largeData" searchPlaceholder="Search 100+ users..." />


<!-- ============================================================================= -->
<!-- USAGE NOTES -->
<!-- ============================================================================= -->
<!--
COLUMN CONFIGURATION:
{
    'key'       => 'field_name',           // Database field name
    'label'     => 'Display Label',        // Header text
    'sortable'  => true|false,             // Allow column sorting (default: true)
    'searchable'=> true|false,             // Include in search (default: true)
    'format'    => fn($value, $row) => ... // Custom formatting function
}

PROPS:
- columns (array): Column definitions with key, label, format, etc.
- data (array): Array of records to display
- searchPlaceholder (string): Search input placeholder text

ALPINE DATA:
- search: Current search query (debounced 300ms)
- filteredData: Results after search/filter
- sortBy: Current sort column
- sortOrder: 'asc' or 'desc'
- currentPage: Current page number
- loading: Boolean for loading state
- paginatedData: Current page results

FEATURES:
✓ Debounced search (300ms) - prevents jank
✓ Smart numeric sorting - "100" > "20" (not "100" < "20")
✓ Per-column searchable flag - skip expensive searches
✓ Custom formatting - dates, badges, actions
✓ Loading states - professional spinner
✓ Smooth transitions - all animations 150ms
✓ Empty states - smart "no results" message
✓ Row click support - for selections or navigation
✓ Smart pagination - shows 5 page buttons centered
✓ Responsive - works on mobile with overflow scroll

INTEGRATION EXAMPLE:
In controller:
$columns = [...];
$data = YourModel::get()->toArray();

In view:
<x-datatable :columns="$columns" :data="$data" />
-->
