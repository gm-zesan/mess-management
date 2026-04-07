@props([
    'columns' => [],
    'data' => [],
    'searchPlaceholder' => 'Search...',
])

<div x-data="dataTable({
    columns: {{ json_encode($columns) }},
    data: {{ json_encode($data) }},
    itemsPerPage: 10,
})" class="space-y-4">
    
    <!-- Top Bar: Search & Actions -->
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex-1 min-w-0">
            <input 
                type="text" 
                x-model.debounce.300ms="search"
                placeholder="{{ $searchPlaceholder }}"
                class="w-full px-4 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-transparent"
            />
        </div>
        <div class="flex gap-2 text-sm text-gray-600">
            <span x-text="`${filteredData.length} of ${data.length}`"></span>
        </div>
    </div>

    <!-- Table -->
    <div class="overflow-x-auto bg-white rounded-lg border border-gray-200 shadow-sm">
        <table class="w-full text-sm">
            <!-- Header -->
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <template x-for="(column, index) in columns" :key="index">
                        <th 
                            @click="column.sortable !== false && sortData(column.key)"
                            :class="[
                                'px-4 py-3 text-left font-semibold text-gray-500 uppercase tracking-wide text-xs',
                                column.sortable !== false && 'cursor-pointer hover:bg-gray-100'
                            ]"
                        >
                            <div class="flex items-center gap-2">
                                <span x-text="column.label"></span>
                                <template x-if="column.sortable !== false">
                                    <template x-if="sortBy === column.key">
                                        <svg class="w-4 h-4 text-sky-600 transition-transform" :class="sortOrder === 'asc' ? '' : 'rotate-180'" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </template>
                                </template>
                            </div>
                        </th>
                    </template>
                </tr>
            </thead>

            <!-- Body -->
            <tbody class="divide-y divide-gray-200">
                <template x-if="loading">
                    <tr>
                        <td :colspan="columns.length" class="px-4 py-8 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <div class="w-4 h-4 border-2 border-sky-600 border-t-transparent rounded-full animate-spin"></div>
                                <span class="text-sm text-gray-500">Loading...</span>
                            </div>
                        </td>
                    </tr>
                </template>

                <template x-if="!loading && filteredData.length > 0">
                    <template x-for="(row, idx) in paginatedData" :key="idx">
                        <tr class="hover:bg-gray-50 transition-colors cursor-pointer" @click="rowClick(row)">
                            <template x-for="(column, colIdx) in columns" :key="colIdx">
                                <td class="px-4 py-3 text-gray-700">
                                    <template x-if="column.format">
                                        <span x-html="column.format(row[column.key], row)"></span>
                                    </template>
                                    <template x-if="!column.format">
                                        <span x-text="row[column.key]"></span>
                                    </template>
                                </td>
                            </template>
                        </tr>
                    </template>
                </template>

                <!-- Empty State -->
                <template x-if="!loading && filteredData.length === 0">
                    <tr>
                        <td :colspan="columns.length" class="px-4 py-8 text-center">
                            <div class="flex flex-col items-center justify-center gap-2">
                                <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                </svg>
                                <template x-if="search">
                                    <div>
                                        <p class="text-sm font-medium text-gray-700">No results found</p>
                                        <p class="text-xs text-gray-500">for "<span x-text="search"></span>"</p>
                                    </div>
                                </template>
                                <template x-if="!search">
                                    <p class="text-sm text-gray-500">No data available</p>
                                </template>
                            </div>
                        </td>
                    </tr>
                </template>
            </tbody>
        </table>
    </div>

    <!-- Pagination Footer -->
    <template x-if="totalPages > 1 && !loading">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div class="text-sm text-gray-600">
                <span x-text="`Showing ${startIndex + 1} to ${Math.min(endIndex, filteredData.length)} of ${filteredData.length} results`"></span>
            </div>
            
            <div class="flex items-center gap-2">
                <button 
                    @click="previousPage()"
                    :disabled="currentPage === 1"
                    class="px-3 py-1 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-150"
                >
                    Previous
                </button>

                <div class="flex items-center gap-1">
                    <template x-for="page in visiblePages" :key="page">
                        <button 
                            @click="goToPage(page)"
                            :class="[
                                'w-8 h-8 rounded-lg text-sm font-medium transition-all duration-150',
                                page === currentPage 
                                    ? 'bg-sky-600 text-white' 
                                    : 'border border-gray-300 text-gray-700 hover:bg-gray-50'
                            ]"
                            x-text="page"
                        ></button>
                    </template>
                </div>

                <button 
                    @click="nextPage()"
                    :disabled="currentPage === totalPages"
                    class="px-3 py-1 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-150"
                >
                    Next
                </button>
            </div>
        </div>
    </template>
</div>

<script>
function dataTable({ columns, data, itemsPerPage }) {
    return {
        columns,
        data,
        itemsPerPage,
        search: '',
        filteredData: data,
        sortBy: null,
        sortOrder: 'asc',
        currentPage: 1,
        loading: false,

        init() {
            this.$watch('search', () => this.filterData());
        },

        filterData() {
            const query = this.search.toLowerCase();
            this.filteredData = this.data.filter(row => 
                this.columns
                    .filter(col => col.searchable !== false)
                    .some(col => 
                        String(row[col.key] || '').toLowerCase().includes(query)
                    )
            );
            this.currentPage = 1;
        },

        sortData(key) {
            if (this.sortBy === key) {
                this.sortOrder = this.sortOrder === 'asc' ? 'desc' : 'asc';
            } else {
                this.sortBy = key;
                this.sortOrder = 'asc';
            }

            this.filteredData.sort((a, b) => {
                let aVal = a[key];
                let bVal = b[key];

                // Smart type conversion for numbers
                if (!isNaN(aVal) && aVal !== '' && aVal !== null) aVal = Number(aVal);
                if (!isNaN(bVal) && bVal !== '' && bVal !== null) bVal = Number(bVal);

                if (aVal === bVal) return 0;
                
                const comparison = aVal < bVal ? -1 : 1;
                return this.sortOrder === 'asc' ? comparison : -comparison;
            });

            this.currentPage = 1;
        },

        get paginatedData() {
            const start = (this.currentPage - 1) * this.itemsPerPage;
            const end = start + this.itemsPerPage;
            return this.filteredData.slice(start, end);
        },

        get totalPages() {
            return Math.ceil(this.filteredData.length / this.itemsPerPage);
        },

        get startIndex() {
            return (this.currentPage - 1) * this.itemsPerPage;
        },

        get endIndex() {
            return this.startIndex + this.itemsPerPage;
        },

        get visiblePages() {
            const pages = [];
            const maxVisible = 5;
            let startPage = Math.max(1, this.currentPage - Math.floor(maxVisible / 2));
            let endPage = Math.min(this.totalPages, startPage + maxVisible - 1);

            if (endPage - startPage + 1 < maxVisible) {
                startPage = Math.max(1, endPage - maxVisible + 1);
            }

            for (let i = startPage; i <= endPage; i++) {
                pages.push(i);
            }

            return pages;
        },

        previousPage() {
            if (this.currentPage > 1) this.currentPage--;
        },

        nextPage() {
            if (this.currentPage < this.totalPages) this.currentPage++;
        },

        goToPage(page) {
            this.currentPage = page;
        },

        rowClick(row) {
            // Override this in consuming component: @row-click="handleRowClick"
            // Or use as hook for custom behavior
        },
    };
}
</script>
