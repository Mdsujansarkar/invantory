<x-app-layout title="Stock Movement Report">
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-foreground leading-tight">
                {{ __('Stock Movement Report') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-4 space-y-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Filter Form -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-6">
                <form method="GET" action="{{ route('reports.stock-movement') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end">
                    <div>
                        <label for="sku" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Product SKU
                        </label>
                        <input
                            type="text"
                            name="sku"
                            id="sku"
                            value="{{ old('sku', $sku) }}"
                            class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            placeholder="Enter SKU..."
                            autocomplete="off"
                        >
                    </div>
                    <div>
                        <label for="start_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Start Date
                        </label>
                        <input
                            type="date"
                            name="start_date"
                            id="start_date"
                            value="{{ old('start_date', $startDate) }}"
                            class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        >
                    </div>
                    <div>
                        <label for="end_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            End Date
                        </label>
                        <input
                            type="date"
                            name="end_date"
                            id="end_date"
                            value="{{ old('end_date', $endDate) }}"
                            class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        >
                    </div>
                    <div>
                        <label for="type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Movement Type
                        </label>
                        <select
                            name="type"
                            id="type"
                            class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        >
                            <option value="">All Types</option>
                            <option value="in" {{ $type === 'in' ? 'selected' : '' }}>Stock In (Additions)</option>
                            <option value="out" {{ $type === 'out' ? 'selected' : '' }}>Stock Out (Deductions)</option>
                        </select>
                    </div>
                    <div>
                        <x-primary-button type="submit">
                            {{ __('Search') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>

            @if($movements->count() > 0 || $sku || $startDate || $endDate || $type)
                <!-- Summary Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                            Total Stock In
                        </dt>
                        <dd class="mt-2 text-3xl font-semibold text-green-600">
                            {{ $totalIn }}
                        </dd>
                        <dd class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            Units added
                        </dd>
                    </div>

                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                            Total Stock Out
                        </dt>
                        <dd class="mt-2 text-3xl font-semibold text-red-600">
                            {{ $totalOut }}
                        </dd>
                        <dd class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            Units deducted
                        </dd>
                    </div>

                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                            Net Movement
                        </dt>
                        <dd class="mt-2 text-3xl font-semibold {{ ($totalIn - $totalOut) >= 0 ? 'text-green-600' : 'text-red-600' }}">
                            {{ $totalIn - $totalOut >= 0 ? '+' : '' }}{{ $totalIn - $totalOut }}
                        </dd>
                        <dd class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            Net change
                        </dd>
                    </div>
                </div>

                <!-- Stock Movements Table -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden mb-6">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                            Stock Movement History
                        </h3>
                        <span class="text-sm text-gray-500 dark:text-gray-400">
                            {{ $movements->total() }} records
                        </span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Date
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        SKU
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Product
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Type
                                    </th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Quantity
                                    </th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Before
                                    </th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        After
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Reference
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Notes
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($movements as $movement)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                            {{ $movement->created_at?->format('Y-m-d H:i') ?? 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white font-medium">
                                            {{ $movement->sku }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                            {{ $movement->product_name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($movement->type === 'in')
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                                    Stock In
                                                </span>
                                            @else
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                                    Stock Out
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white text-right {{ $movement->type === 'in' ? 'text-green-600' : 'text-red-600' }} font-medium">
                                            {{ $movement->type === 'in' ? '+' : '-' }}{{ $movement->quantity }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white text-right">
                                            {{ $movement->before_quantity }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white text-right font-medium">
                                            {{ $movement->after_quantity }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            @if($movement->reference_number)
                                                {{ $movement->reference_number }}
                                            @elseif($movement->reference_type)
                                                {{ ucfirst(str_replace('_', ' ', $movement->reference_type)) }} #{{ $movement->reference_id }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                            {{ $movement->notes ?? '-' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($movements->hasPages())
                        <div class="bg-white dark:bg-gray-800 px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                            {{ $movements->appends(['sku' => $sku, 'start_date' => $startDate, 'end_date' => $endDate, 'type' => $type])->links() }}
                        </div>
                    @endif
                </div>

                <!-- Summary by Product -->
                @if($summary->count() > 0)
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                                Summary by Product
                            </h3>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            SKU
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Product Name
                                        </th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Total In
                                        </th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Total Out
                                        </th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Net Change
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach($summary as $item)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white font-medium">
                                                {{ $item->sku }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                                {{ $item->product_name }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600 text-right">
                                                +{{ $item->total_in }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-red-600 text-right">
                                                -{{ $item->total_out }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-medium {{ $item->net_change >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                                {{ $item->net_change >= 0 ? '+' : '' }}{{ $item->net_change }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
            @else
                <!-- Empty State -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-12 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No movements found</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Get started by selecting filters to search for stock movements.
                    </p>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
