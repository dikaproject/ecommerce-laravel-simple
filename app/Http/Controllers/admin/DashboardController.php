<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Get statistics for dashboard
        $totalOrders = Transaction::count();
        $totalRevenue = Transaction::where('status', '!=', 'cancelled')
            ->sum('total_amount');
        $totalCustomers = User::where('role', 'user')->count();
        $totalProducts = Product::count();
        
        // Calculate growth percentages
        $lastMonthOrders = Transaction::whereMonth('created_at', '=', Carbon::now()->subMonth()->month)->count();
        $orderGrowth = $lastMonthOrders > 0 
            ? round((($totalOrders - $lastMonthOrders) / $lastMonthOrders) * 100, 1)
            : 100;
            
        $lastMonthRevenue = Transaction::where('status', '!=', 'cancelled')
            ->whereMonth('created_at', '=', Carbon::now()->subMonth()->month)
            ->sum('total_amount');
        $revenueGrowth = $lastMonthRevenue > 0 
            ? round((($totalRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100, 1)
            : 100;
            
        $lastMonthCustomers = User::where('role', 'user')
            ->whereMonth('created_at', '=', Carbon::now()->subMonth()->month)
            ->count();
        $customerGrowth = $lastMonthCustomers > 0 
            ? round((($totalCustomers - $lastMonthCustomers) / $lastMonthCustomers) * 100, 1)
            : 100;
            
        $lastMonthProducts = Product::whereMonth('created_at', '=', Carbon::now()->subMonth()->month)->count();
        $productGrowth = $lastMonthProducts > 0 
            ? round((($totalProducts - $lastMonthProducts) / $lastMonthProducts) * 100, 1)
            : 100;
        
        // Get top selling products
        $topProducts = Product::select('products.*', DB::raw('COUNT(transaction_items.id) as sold'))
            ->leftJoin('transaction_items', 'products.id', '=', 'transaction_items.product_id')
            ->groupBy('products.id')
            ->orderByDesc('sold')
            ->limit(5)
            ->get();
        
        // Get recent orders
        $recentOrders = Transaction::with('user')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
        
        // Get low stock products
        $lowStockProducts = Product::where('stock', '<', 10)
            ->orderBy('stock', 'asc')
            ->limit(5)
            ->get();
        
        return view('admin.dashboard', compact(
            'totalOrders',
            'totalRevenue',
            'totalCustomers',
            'totalProducts',
            'orderGrowth',
            'revenueGrowth',
            'customerGrowth',
            'productGrowth',
            'topProducts',
            'recentOrders',
            'lowStockProducts'
        ));
    }
    
    public function salesData(Request $request)
    {
        $period = $request->input('period', 'monthly');
        $data = [];
        
        switch ($period) {
            case 'weekly':
                $data = $this->getWeeklySalesData();
                break;
            case 'yearly':
                $data = $this->getYearlySalesData();
                break;
            case 'monthly':
            default:
                $data = $this->getMonthlySalesData();
                break;
        }
        
        return response()->json($data);
    }
    
    private function getMonthlySalesData()
    {
        $startDate = Carbon::now()->subMonths(11)->startOfMonth();
        $endDate = Carbon::now()->endOfMonth();
        
        $sales = Transaction::select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('YEAR(created_at) as year'),
                DB::raw('COUNT(*) as total_orders'),
                DB::raw('SUM(total_amount) as total_revenue')
            )
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();
            
        $labels = [];
        $salesData = [];
        $revenueData = [];
        
        // Initialize all months with zero
        for ($i = 0; $i < 12; $i++) {
            $date = Carbon::now()->subMonths(11-$i);
            $labels[] = $date->format('M Y');
            $salesData[] = 0;
            $revenueData[] = 0;
        }
        
        // Fill in actual data
        foreach ($sales as $entry) {
            $date = Carbon::createFromDate($entry->year, $entry->month, 1);
            $monthIndex = Carbon::now()->subMonths(11)->diffInMonths($date);
            
            if ($monthIndex >= 0 && $monthIndex < 12) {
                $salesData[$monthIndex] = $entry->total_orders;
                $revenueData[$monthIndex] = $entry->total_revenue;
            }
        }
        
        return [
            'labels' => $labels,
            'sales' => $salesData,
            'revenue' => $revenueData
        ];
    }
    
    private function getWeeklySalesData()
    {
        $startDate = Carbon::now()->subWeeks(7)->startOfWeek();
        $endDate = Carbon::now()->endOfWeek();
        
        $sales = Transaction::select(
                DB::raw('YEARWEEK(created_at, 1) as yearweek'),
                DB::raw('COUNT(*) as total_orders'),
                DB::raw('SUM(total_amount) as total_revenue')
            )
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('yearweek')
            ->orderBy('yearweek')
            ->get();
            
        $labels = [];
        $salesData = [];
        $revenueData = [];
        
        // Initialize all weeks with zero
        for ($i = 0; $i < 8; $i++) {
            $date = Carbon::now()->subWeeks(7-$i);
            $labels[] = $date->format('d M');
            $salesData[] = 0;
            $revenueData[] = 0;
        }
        
        // Fill in actual data
        foreach ($sales as $entry) {
            $year = substr($entry->yearweek, 0, 4);
            $week = substr($entry->yearweek, 4);
            $date = Carbon::now()->setISODate($year, $week);
            $weekIndex = Carbon::now()->subWeeks(7)->diffInWeeks($date);
            
            if ($weekIndex >= 0 && $weekIndex < 8) {
                $salesData[$weekIndex] = $entry->total_orders;
                $revenueData[$weekIndex] = $entry->total_revenue;
            }
        }
        
        return [
            'labels' => $labels,
            'sales' => $salesData,
            'revenue' => $revenueData
        ];
    }
    
    private function getYearlySalesData()
    {
        $startDate = Carbon::now()->subYears(4)->startOfYear();
        $endDate = Carbon::now()->endOfYear();
        
        $sales = Transaction::select(
                DB::raw('YEAR(created_at) as year'),
                DB::raw('COUNT(*) as total_orders'),
                DB::raw('SUM(total_amount) as total_revenue')
            )
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('year')
            ->orderBy('year')
            ->get();
            
        $labels = [];
        $salesData = [];
        $revenueData = [];
        
        // Initialize all years with zero
        for ($i = 0; $i < 5; $i++) {
            $year = Carbon::now()->subYears(4-$i)->year;
            $labels[] = $year;
            $salesData[] = 0;
            $revenueData[] = 0;
        }
        
        // Fill in actual data
        foreach ($sales as $entry) {
            $yearIndex = $entry->year - Carbon::now()->subYears(4)->year;
            
            if ($yearIndex >= 0 && $yearIndex < 5) {
                $salesData[$yearIndex] = $entry->total_orders;
                $revenueData[$yearIndex] = $entry->total_revenue;
            }
        }
        
        return [
            'labels' => $labels,
            'sales' => $salesData,
            'revenue' => $revenueData
        ];
    }
}
