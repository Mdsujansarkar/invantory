<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\SaleItem;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductReportController extends Controller
{
    /**
     * Stock Report - Filter by SKU to show stock and total sold
     */
    public function stockReport(Request $request)
    {
        $sku = $request->input('sku');
        $product = null;
        $totalSold = 0;
        $totalRevenue = 0;
        $salesData = [];

        if ($sku) {
            $product = Product::where('sku', $sku)->first();

            if ($product) {
                // Calculate total sold quantity
                $totalSold = SaleItem::where('product_id', $product->id)
                    ->whereHas('sale', function ($query) {
                        $query->whereNotIn('status', ['draft', 'cancelled']);
                    })
                    ->sum('quantity');

                // Calculate total revenue from this product
                $totalRevenue = SaleItem::where('product_id', $product->id)
                    ->whereHas('sale', function ($query) {
                        $query->whereNotIn('status', ['draft', 'cancelled']);
                    })
                    ->sum('final_price');

                // Get sales breakdown (optional - top 10 recent sales)
                $salesData = SaleItem::with(['sale.customer', 'sale'])
                    ->where('product_id', $product->id)
                    ->whereHas('sale', function ($query) {
                        $query->whereNotIn('status', ['draft', 'cancelled']);
                    })
                    ->orderBy('created_at', 'desc')
                    ->limit(10)
                    ->get();
            }
        }

        return view('reports.stock-report', compact(
            'product',
            'sku',
            'totalSold',
            'totalRevenue',
            'salesData'
        ));
    }

    /**
     * Sales Report - Filter by date range and SKU to show products sold
     */
    public function salesReport(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $sku = $request->input('sku');

        $query = SaleItem::with(['product', 'sale.customer'])
            ->whereHas('sale', function ($q) {
                $q->whereNotIn('status', ['draft', 'cancelled']);
            });

        // Filter by SKU if provided
        if ($sku) {
            $query->whereHas('product', function ($q) use ($sku) {
                $q->where('sku', $sku);
            });
        }

        // Filter by date range if provided
        if ($startDate) {
            $query->whereHas('sale', function ($q) use ($startDate) {
                $q->whereDate('sale_date', '>=', $startDate);
            });
        }

        if ($endDate) {
            $query->whereHas('sale', function ($q) use ($endDate) {
                $q->whereDate('sale_date', '<=', $endDate);
            });
        }

        // Get grouped results by product
        $salesData = $query->orderBy('created_at', 'desc')
            ->paginate(50);

        // Get summary statistics
        $summaryQuery = SaleItem::select(
                'products.sku',
                'products.name',
                DB::raw('COUNT(DISTINCT sale_items.sale_id) as total_orders'),
                DB::raw('SUM(sale_items.quantity) as total_quantity'),
                DB::raw('SUM(sale_items.subtotal) as total_revenue')
            )
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->whereNotIn('sales.status', ['draft', 'cancelled']);

        if ($sku) {
            $summaryQuery->where('products.sku', $sku);
        }

        if ($startDate) {
            $summaryQuery->whereDate('sales.sale_date', '>=', $startDate);
        }

        if ($endDate) {
            $summaryQuery->whereDate('sales.sale_date', '<=', $endDate);
        }

        $summaryQuery->groupBy('products.id', 'products.sku', 'products.name');

        $summary = $summaryQuery->get();

        // Calculate totals
        $totalRevenue = $summary->sum('total_revenue');
        $totalQuantity = $summary->sum('total_quantity');
        $totalOrders = $summary->sum('total_orders');

        return view('reports.sales-report', compact(
            'salesData',
            'summary',
            'startDate',
            'endDate',
            'sku',
            'totalRevenue',
            'totalQuantity',
            'totalOrders'
        ));
    }
}
