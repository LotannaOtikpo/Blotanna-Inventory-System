<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Sale;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Use configured timezone
        $timezone = Setting::getValue('timezone', 'Africa/Lagos');
        $now = Carbon::now($timezone);

        $totalProducts = Product::count();
        
        // Monthly Sales: Start of month in Configured Timezone -> converted to UTC for DB query
        $startOfMonth = $now->copy()->startOfMonth()->setTimezone('UTC');
        $endOfMonth = $now->copy()->endOfMonth()->setTimezone('UTC');
        
        $monthlySales = Sale::whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->sum('total_amount');
            
        // Today's Revenue: Start of day in Configured Timezone -> converted to UTC for DB query
        $startOfDay = $now->copy()->startOfDay()->setTimezone('UTC');
        $endOfDay = $now->copy()->endOfDay()->setTimezone('UTC');
        
        $todaysRevenue = Sale::whereBetween('created_at', [$startOfDay, $endOfDay])
            ->sum('total_amount');
            
        // Get actual low stock products for the notification dropdown using dynamic setting
        $threshold = (int) Setting::getValue('low_stock_threshold', 10);
        $lowStockProducts = Product::where('quantity', '<', $threshold)->get();
        $lowStockItems = $lowStockProducts->count();

        // Top selling products logic
        $topProducts = Product::withCount('saleItems')
            ->orderBy('sale_items_count', 'desc')
            ->take(5)
            ->get();

        // Weekly Sales Chart Data (Current Week: Sunday to Saturday)
        $chartData = [];
        $maxSales = 0;
        
        // Determine start of current week (Sunday) in Configured Timezone
        $currentWeekStart = $now->copy()->startOfWeek(Carbon::SUNDAY);
        
        for ($i = 0; $i < 7; $i++) {
            // Get the specific day in the week loop (Configured Timezone)
            $day = $currentWeekStart->copy()->addDays($i);
            $dayName = $day->format('D'); // Sun, Mon, etc.
            
            // Convert that day's full range to UTC for querying the database
            $dayStart = $day->copy()->startOfDay()->setTimezone('UTC');
            $dayEnd = $day->copy()->endOfDay()->setTimezone('UTC');
            
            $amount = Sale::whereBetween('created_at', [$dayStart, $dayEnd])->sum('total_amount');
            
            if ($amount > $maxSales) {
                $maxSales = $amount;
            }
            
            $chartData[] = [
                'day' => $dayName,
                'amount' => $amount
            ];
        }

        // Calendar Data: Map Date (YYYY-MM-DD) -> Total Revenue
        $calendarSales = Sale::orderBy('created_at')
            ->get()
            ->groupBy(function($sale) use ($timezone) {
                return $sale->created_at->setTimezone($timezone)->format('Y-m-d');
            })
            ->map(function($group) {
                return $group->sum('total_amount');
            });

        return view('dashboard', compact(
            'totalProducts', 
            'monthlySales', 
            'todaysRevenue', 
            'lowStockItems',
            'lowStockProducts',
            'topProducts',
            'chartData',
            'maxSales',
            'calendarSales'
        ));
    }

    public function getSalesByDate(Request $request)
    {
        $request->validate(['date' => 'required|date']);
        
        $timezone = Setting::getValue('timezone', 'Africa/Lagos');
        $date = $request->query('date');
        
        // Calculate range in UTC based on the local day
        $start = Carbon::parse($date, $timezone)->startOfDay()->setTimezone('UTC');
        $end = Carbon::parse($date, $timezone)->endOfDay()->setTimezone('UTC');

        $sales = Sale::with('user')
            ->whereBetween('created_at', [$start, $end])
            ->latest()
            ->get();

        return response()->json([
            'date_formatted' => Carbon::parse($date)->format('F d, Y'),
            'total_revenue' => $sales->sum('total_amount'),
            'total_count' => $sales->count(),
            'sales' => $sales->map(function($sale) use ($timezone) {
                return [
                    'id' => $sale->id,
                    'transaction_id' => $sale->transaction_id,
                    'time' => $sale->created_at->setTimezone($timezone)->format('h:i A'),
                    'customer' => $sale->customer_name ?: 'Walk-in Customer',
                    'amount' => $sale->total_amount,
                    'payment_method' => $sale->payment_method,
                    'status' => $sale->status
                ];
            })
        ]);
    }
}
