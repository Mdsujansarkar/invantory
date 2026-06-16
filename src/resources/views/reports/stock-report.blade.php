<x-app-layout title="Stock Report">
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-foreground leading-tight">
                {{ __('Stock Report') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-4 space-y-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Filter Form -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-6">
                <form method="GET" action="{{ route('reports.stock') }}" class="flex gap-4 items-end">
                    <div class="flex-1">
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
                        <x-primary-button type="submit">
                            {{ __('Search') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>

            @if($product)
                <!-- Product Info Card -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden mb-6">
                    <div class="p-6">
                        <div class="flex items-start justify-between">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                    {{ $product->name }}
                                </h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                    SKU: {{ $product->sku }}
                                </p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    Category: {{ $product->category?->name ?? 'N/A' }}
                                </p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm text-gray-500 dark:text-gray-400">Unit Price</p>
                                <p class="text-lg font-semibold text-gray-900 dark:text-white">
                                    {{ format_money($product->selling_price) }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Stats Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-3 divide-y md:divide-y-0 md:divide-x divide-gray-200 dark:divide-gray-700">
                        <div class="p-6 text-center">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                                Current Stock
                            </dt>
                            <dd class="mt-2 text-3xl font-semibold text-gray-900 dark:text-white">
                                {{ $product->quantity }}
                            </dd>
                            <dd class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                {{ $product->unit?->symbol ?? 'units' }}
                            </dd>
                        </div>

                        <div class="p-6 text-center">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                                Total Sold
                            </dt>
                            <dd class="mt-2 text-3xl font-semibold text-green-600">
                                {{ $totalSold }}
                            </dd>
                            <dd class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                {{ $product->unit?->symbol ?? 'units' }}
                            </dd>
                        </div>

                        <div class="p-6 text-center">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                                Total Revenue
                            </dt>
                            <dd class="mt-2 text-3xl font-semibold text-indigo-600">
                                {{ format_money($totalRevenue) }}
                            </dd>
                            <dd class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                From sales
                            </dd>
                        </div>
                    </div>

                    <!-- Stock Status Indicator -->
                    <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700">
                        @if($product->quantity <= $product->min_stock)
                            <div class="flex items-center text-red-600 dark:text-red-400">
                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                                <span class="font-medium">Low Stock Alert!</span>
                                <span class="ml-2">Current stock ({{ $product->quantity }}) is at or below minimum level ({{ $product->min_stock }})</span>
                            </div>
                        @else
                            <div class="flex items-center text-green-600 dark:text-green-400">
                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                <span class="font-medium">Stock Level OK</span>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Recent Sales (Optional) -->
                @if($salesData->count() > 0)
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                                Recent Sales (Last 10)
                            </h3>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Date
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Invoice
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Customer
                                        </th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Quantity
                                        </th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Total
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach($salesData as $item)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                                {{ $item->sale?->sale_date?->format('d M Y') ?? '-' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                                {{ $item->sale?->invoice_number ?? '-' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                                {{ $item->sale?->customer?->name ?? 'Walk-in' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white text-right">
                                                {{ $item->quantity }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white text-right">
                                                {{ format_money($item->subtotal) }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif

            @elseif($sku)
                <!-- Product Not Found -->
                <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-6">
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-yellow-400 mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                        <p class="text-yellow-800 dark:text-yellow-200">
                            No product found with SKU: <strong>{{ $sku }}</strong>
                        </p>
                    </div>
                </div>

            @else
                <!-- Initial State -->
                <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-12 text-center">
                    <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    <p class="text-gray-500 dark:text-gray-400 text-lg">
                        Enter a product SKU above to view stock information
                    </p>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
