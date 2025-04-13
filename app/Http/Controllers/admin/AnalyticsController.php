<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class AnalyticsController extends Controller
{
    public function index(Request $request)
    {
        $period = $request->input('period', 'month');
        $startDate = null;
        $endDate = null;
        $previousStartDate = null;
        $previousEndDate = null;
        
        // Determine date ranges based on selected period
        switch ($period) {
            case 'today':
                $startDate = Carbon::today();
                $endDate = Carbon::today()->endOfDay();
                $previousStartDate = Carbon::yesterday();
                $previousEndDate = Carbon::yesterday()->endOfDay();
                break;
                
            case 'week':
                $startDate = Carbon::now()->startOfWeek();
                $endDate = Carbon::now()->endOfWeek();
                $previousStartDate = Carbon::now()->subWeek()->startOfWeek();
                $previousEndDate = Carbon::now()->subWeek()->endOfWeek();
                break;
                
            case 'year':
                $startDate = Carbon::now()->startOfYear();
                $endDate = Carbon::now()->endOfYear();
                $previousStartDate = Carbon::now()->subYear()->startOfYear();
                $previousEndDate = Carbon::now()->subYear()->endOfYear();
                break;
                
            case 'custom':
                $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date')) : Carbon::now()->startOfMonth();
                $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date'))->endOfDay() : Carbon::now()->endOfDay();
                $daysDiff = $startDate->diffInDays($endDate);
                $previousStartDate = (clone $startDate)->subDays($daysDiff);
                $previousEndDate = (clone $endDate)->subDays($daysDiff);
                break;
                
            case 'month':
            default:
                $startDate = Carbon::now()->startOfMonth();
                $endDate = Carbon::now()->endOfMonth();
                $previousStartDate = Carbon::now()->subMonth()->startOfMonth();
                $previousEndDate = Carbon::now()->subMonth()->endOfMonth();
                break;
        }
        
        // Get sales data for current period
        $totalSales = Transaction::whereBetween('created_at', [$startDate, $endDate])->count();
        $totalRevenue = Transaction::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', '!=', 'cancelled')
            ->sum('total_amount');
        
        // Get sales data for previous period for comparison
        $previousSales = Transaction::whereBetween('created_at', [$previousStartDate, $previousEndDate])->count();
        $previousRevenue = Transaction::whereBetween('created_at', [$previousStartDate, $previousEndDate])
            ->where('status', '!=', 'cancelled')
            ->sum('total_amount');
        
        // Calculate growth percentages
        $salesGrowth = $previousSales > 0 ? round((($totalSales - $previousSales) / $previousSales) * 100, 1) : 100;
        $revenueGrowth = $previousRevenue > 0 ? round((($totalRevenue - $previousRevenue) / $previousRevenue) * 100, 1) : 100;
        
        // Calculate average order value
        $averageOrderValue = $totalSales > 0 ? round($totalRevenue / $totalSales, 0) : 0;
        $previousAOV = $previousSales > 0 ? round($previousRevenue / $previousSales, 0) : 0;
        $aovGrowth = $previousAOV > 0 ? round((($averageOrderValue - $previousAOV) / $previousAOV) * 100, 1) : 100;
        
        // Calculate total units sold
        $totalUnitsSold = TransactionItem::whereHas('transaction', function($query) use ($startDate, $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate])
                  ->where('status', '!=', 'cancelled');
        })->sum('quantity');
        
        $previousUnitsSold = TransactionItem::whereHas('transaction', function($query) use ($previousStartDate, $previousEndDate) {
            $query->whereBetween('created_at', [$previousStartDate, $previousEndDate])
                  ->where('status', '!=', 'cancelled');
        })->sum('quantity');
        
        $unitsSoldGrowth = $previousUnitsSold > 0 ? round((($totalUnitsSold - $previousUnitsSold) / $previousUnitsSold) * 100, 1) : 100;
        
        // Get top products
        $topProducts = Product::select('products.*', 
        DB::raw('SUM(transaction_items.quantity) as sold'))
    ->leftJoin('transaction_items', 'products.id', '=', 'transaction_items.product_id')
    ->leftJoin('transactions', function($join) use ($startDate, $endDate) {
        $join->on('transaction_items.transaction_id', '=', 'transactions.id')
             ->whereBetween('transactions.created_at', [$startDate, $endDate])
             ->where('transactions.status', '!=', 'cancelled');
    })
    ->groupBy('products.id')
    ->orderBy('sold', 'desc')
    ->take(5)
    ->get();

foreach ($topProducts as $product) {
    $product->rating = 0; 
}
        
        // Get top categories
        $topCategories = Category::select('categories.name', 
                DB::raw('SUM(transaction_items.quantity) as total_sold'),
                DB::raw('SUM(transaction_items.quantity * transaction_items.price) as total_revenue'))
            ->join('products', 'categories.id', '=', 'products.category_id')
            ->join('transaction_items', 'products.id', '=', 'transaction_items.product_id')
            ->join('transactions', function($join) use ($startDate, $endDate) {
                $join->on('transaction_items.transaction_id', '=', 'transactions.id')
                     ->whereBetween('transactions.created_at', [$startDate, $endDate])
                     ->where('transactions.status', '!=', 'cancelled');
            })
            ->groupBy('categories.id')
            ->orderBy('total_revenue', 'desc')
            ->take(5)
            ->get();
        
        // Calculate growth for each category
        foreach ($topCategories as $category) {
            $previousRevenue = DB::table('categories')
                ->join('products', 'categories.id', '=', 'products.category_id')
                ->join('transaction_items', 'products.id', '=', 'transaction_items.product_id')
                ->join('transactions', function($join) use ($previousStartDate, $previousEndDate) {
                    $join->on('transaction_items.transaction_id', '=', 'transactions.id')
                         ->whereBetween('transactions.created_at', [$previousStartDate, $previousEndDate])
                         ->where('transactions.status', '!=', 'cancelled');
                })
                ->where('categories.name', $category->name)
                ->sum(DB::raw('transaction_items.quantity * transaction_items.price'));
            
            $category->growth = $previousRevenue > 0 ? 
                round((($category->total_revenue - $previousRevenue) / $previousRevenue) * 100, 1) : 100;
        }
        
        // Get data for categories pie chart
        $categoriesChartData = [
            'labels' => [],
            'data' => []
        ];
        
        $categoryData = Category::select('categories.name', 
                DB::raw('SUM(transaction_items.quantity * transaction_items.price) as revenue'))
            ->join('products', 'categories.id', '=', 'products.category_id')
            ->join('transaction_items', 'products.id', '=', 'transaction_items.product_id')
            ->join('transactions', function($join) use ($startDate, $endDate) {
                $join->on('transaction_items.transaction_id', '=', 'transactions.id')
                     ->whereBetween('transactions.created_at', [$startDate, $endDate])
                     ->where('transactions.status', '!=', 'cancelled');
            })
            ->groupBy('categories.id')
            ->orderBy('revenue', 'desc')
            ->take(7)
            ->get();
        
        foreach ($categoryData as $category) {
            $categoriesChartData['labels'][] = $category->name;
            $categoriesChartData['data'][] = $category->revenue;
        }
        
        return view('admin.analytics.index', compact(
            'totalSales', 'totalRevenue', 'averageOrderValue', 'totalUnitsSold',
            'salesGrowth', 'revenueGrowth', 'aovGrowth', 'unitsSoldGrowth',
            'topProducts', 'topCategories', 'categoriesChartData'
        ));
    }
    
    public function salesData(Request $request)
    {
        $period = $request->input('period', 'daily');
        $endDate = Carbon::now();
        $labels = [];
        $salesData = [];
        $revenueData = [];
        
        switch ($period) {
            case 'daily':
                $startDate = Carbon::now()->subDays(30);
                $format = 'd M';
                
                for ($date = clone $startDate; $date <= $endDate; $date->addDay()) {
                    $labels[] = $date->format($format);
                    
                    $sales = Transaction::whereDate('created_at', $date->format('Y-m-d'))->count();
                    $salesData[] = $sales;
                    
                    $revenue = Transaction::whereDate('created_at', $date->format('Y-m-d'))
                        ->where('status', '!=', 'cancelled')
                        ->sum('total_amount');
                    $revenueData[] = $revenue;
                }
                break;
                
            case 'weekly':
                $startDate = Carbon::now()->subWeeks(12);
                
                for ($date = clone $startDate; $date <= $endDate; $date->addWeek()) {
                    $weekEnd = clone $date;
                    $weekEnd->addDays(6);
                    
                    $labels[] = $date->format('d M') . ' - ' . $weekEnd->format('d M');
                    
                    $sales = Transaction::whereBetween('created_at', [
                        $date->format('Y-m-d'), 
                        $weekEnd->format('Y-m-d') . ' 23:59:59'
                    ])->count();
                    $salesData[] = $sales;
                    
                    $revenue = Transaction::whereBetween('created_at', [
                        $date->format('Y-m-d'), 
                        $weekEnd->format('Y-m-d') . ' 23:59:59'
                    ])
                    ->where('status', '!=', 'cancelled')
                    ->sum('total_amount');
                    $revenueData[] = $revenue;
                }
                break;
                
            case 'monthly':
                $startDate = Carbon::now()->subMonths(12);
                
                for ($date = clone $startDate; $date <= $endDate; $date->addMonth()) {
                    $labels[] = $date->format('M Y');
                    
                    $monthEnd = clone $date;
                    $monthEnd->endOfMonth();
                    
                    $sales = Transaction::whereBetween('created_at', [
                        $date->format('Y-m-d'), 
                        $monthEnd->format('Y-m-d') . ' 23:59:59'
                    ])->count();
                    $salesData[] = $sales;
                    
                    $revenue = Transaction::whereBetween('created_at', [
                        $date->format('Y-m-d'), 
                        $monthEnd->format('Y-m-d') . ' 23:59:59'
                    ])
                    ->where('status', '!=', 'cancelled')
                    ->sum('total_amount');
                    $revenueData[] = $revenue;
                }
                break;
        }
        
        return response()->json([
            'labels' => $labels,
            'sales' => $salesData,
            'revenue' => $revenueData
        ]);
    }
    
    public function export()
    {
        $startDate = Carbon::now()->subDays(30);
        $endDate = Carbon::now();
        $fileName = 'sales_analytics_' . Carbon::now()->format('Ymd_His') . '.csv';

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        $output = fopen('php://output', 'w');

        // Title and period info
        fputcsv($output, ['Sales Analytics Report']);
        fputcsv($output, ['Period:', $startDate->format('d M Y') . ' - ' . $endDate->format('d M Y')]);
        fputcsv($output, []); // empty line

        // Column headers
        fputcsv($output, ['Date', 'Transactions', 'Revenue', 'Avg Order Value', 'Units Sold']);

        for ($date = clone $startDate; $date <= $endDate; $date->addDay()) {
            $sales = Transaction::whereDate('created_at', $date->format('Y-m-d'))->count();
            $revenue = Transaction::whereDate('created_at', $date->format('Y-m-d'))
                ->where('status', '!=', 'cancelled')
                ->sum('total_amount');
            $aov = $sales > 0 ? $revenue / $sales : 0;
            $unitsSold = TransactionItem::whereHas('transaction', function($query) use ($date) {
                $query->whereDate('created_at', $date->format('Y-m-d'))
                      ->where('status', '!=', 'cancelled');
            })->sum('quantity');

            fputcsv($output, [
                $date->format('d M Y'),
                $sales,
                $revenue,
                round($aov, 2),
                $unitsSold
            ]);
        }

        // Summary
        fputcsv($output, []);
        fputcsv($output, ['Summary']);

        $totalSales = Transaction::whereBetween('created_at', [$startDate, $endDate])->count();
        $totalRevenue = Transaction::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', '!=', 'cancelled')
            ->sum('total_amount');
        $totalAOV = $totalSales > 0 ? $totalRevenue / $totalSales : 0;
        $totalUnitsSold = TransactionItem::whereHas('transaction', function($query) use ($startDate, $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate])
                  ->where('status', '!=', 'cancelled');
        })->sum('quantity');

        fputcsv($output, ['Total Transactions:', $totalSales]);
        fputcsv($output, ['Total Revenue:', $totalRevenue]);
        fputcsv($output, ['Average Order Value:', round($totalAOV, 2)]);
        fputcsv($output, ['Total Units Sold:', $totalUnitsSold]);

        // Add top products
        fputcsv($output, []);
        fputcsv($output, ['Top Products']);
        fputcsv($output, ['Product Name', 'Units Sold', 'Price']);
        
        $topProducts = Product::select('products.*', DB::raw('SUM(transaction_items.quantity) as sold'))
            ->leftJoin('transaction_items', 'products.id', '=', 'transaction_items.product_id')
            ->leftJoin('transactions', function($join) use ($startDate, $endDate) {
                $join->on('transaction_items.transaction_id', '=', 'transactions.id')
                    ->whereBetween('transactions.created_at', [$startDate, $endDate])
                    ->where('transactions.status', '!=', 'cancelled');
            })
            ->groupBy('products.id')
            ->orderBy('sold', 'desc')
            ->take(5)
            ->get();
            
        foreach ($topProducts as $product) {
            fputcsv($output, [
                $product->name,
                $product->sold,
                $product->price
            ]);
        }

        fclose($output);
        exit;
    }
}