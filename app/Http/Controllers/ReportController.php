<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Product;
use App\Models\ActivityLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        // Daily Sales (Last 30 days)
        $dailySales = Sale::select(
                DB::raw('DATE(created_at) as date'), 
                DB::raw('SUM(total_amount) as total')
            )
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        // Stats
        $totalRevenue = Sale::sum('total_amount');
        $totalOrders = Sale::count();
        $averageOrderValue = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;
        
        // Low Stock
        $threshold = (int) (\App\Models\Setting::where('key', 'low_stock_threshold')->value('value') ?? 10);
        $lowStockProducts = Product::where('quantity', '<', $threshold)->get();

        // Top Selling Categories
        $topCategories = DB::table('sale_items')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->select('categories.name', DB::raw('count(sale_items.id) as total_sales'))
            ->groupBy('categories.name')
            ->orderByDesc('total_sales')
            ->limit(5)
            ->get();

        // Fetch Activity Logs
        // Now that AppServiceProvider ensures the table exists, we can fetch directly.
        $activityLogs = ActivityLog::with('user')
            ->latest()
            ->paginate(15);

        return view('reports.index', compact(
            'dailySales',
            'totalRevenue',
            'totalOrders',
            'averageOrderValue',
            'lowStockProducts',
            'topCategories',
            'activityLogs'
        ));
    }
}