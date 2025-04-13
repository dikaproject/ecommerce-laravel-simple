<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PDF;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaction::with(['user', 'items', 'address']);
        
        // Apply filters
        if ($request->has('search') && !empty($request->search)) {
            $query->where(function($q) use ($request) {
                $q->where('id', 'like', '%' . $request->search . '%')
                  ->orWhere('order_number', 'like', '%' . $request->search . '%');
            });
        }
        
        if ($request->has('payment_method') && !empty($request->payment_method)) {
            $query->where('payment_method', $request->payment_method);
        }
        
        if ($request->has('status') && !empty($request->status)) {
            $query->where('status', $request->status);
        }
        
        // Get transactions with pagination
        $transactions = $query->latest()->paginate(15);
        
        // Get transaction summary
        $todayTransactionsCount = Transaction::whereDate('created_at', Carbon::today())->count();
        $todayTransactionsAmount = Transaction::whereDate('created_at', Carbon::today())
            ->where('status', '!=', 'cancelled')
            ->sum('total_amount');
            
        $weekTransactionsCount = Transaction::whereBetween('created_at', [
            Carbon::now()->startOfWeek(), 
            Carbon::now()->endOfWeek()
        ])->count();
        
        $weekTransactionsAmount = Transaction::whereBetween('created_at', [
            Carbon::now()->startOfWeek(), 
            Carbon::now()->endOfWeek()
        ])->where('status', '!=', 'cancelled')
          ->sum('total_amount');
        
        $monthTransactionsCount = Transaction::whereBetween('created_at', [
            Carbon::now()->startOfMonth(), 
            Carbon::now()->endOfMonth()
        ])->count();
        
        $monthTransactionsAmount = Transaction::whereBetween('created_at', [
            Carbon::now()->startOfMonth(), 
            Carbon::now()->endOfMonth()
        ])->where('status', '!=', 'cancelled')
          ->sum('total_amount');
        
        return view('admin.transactions.index', compact(
            'transactions',
            'todayTransactionsCount',
            'todayTransactionsAmount',
            'weekTransactionsCount',
            'weekTransactionsAmount',
            'monthTransactionsCount',
            'monthTransactionsAmount'
        ));
    }
    
    public function show($id)
    {
        $transaction = Transaction::with(['user', 'items.product', 'address'])->findOrFail($id);
        
        return view('admin.transactions.show', compact('transaction'));
    }

    public function edit($id)
    {
        $transaction = Transaction::with(['user', 'items.product', 'address'])->findOrFail($id);
        
        return view('admin.transactions.edit', compact('transaction'));
    }

    public function update(Request $request, $id)
    {
        $transaction = Transaction::findOrFail($id);
        
        $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled',
            'payment_status' => 'required|in:unpaid,paid,refunded',
        ]);
        
        $transaction->status = $request->status;
        $transaction->payment_status = $request->payment_status;
        $transaction->save();
        
        return redirect()->route('admin.transactions.show', $transaction->id)
        ->with('success', 'Pesanan berhasil diperbarui.');
    }

    public function updateStatus(Request $request, $id)
    {
        $transaction = Transaction::findOrFail($id);
        
        $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled',
        ]);
        
        $transaction->status = $request->status;
        $transaction->save();
        
        return redirect()->route('admin.transactions.show', $transaction->id)
            ->with('success', 'Status pesanan berhasil diperbarui.');
    }
    
    public function confirm($id)
    {
        $transaction = Transaction::findOrFail($id);
        
        if ($transaction->status !== 'pending') {
            return redirect()->back()->with('error', 'Hanya transaksi dengan status pending yang dapat dikonfirmasi.');
        }
        
        $transaction->status = 'success';
        $transaction->payment_status = 'paid';
        $transaction->save();
        
        // Update product stock if needed
        foreach ($transaction->items as $item) {
            $product = $item->product;
            $product->stock = $product->stock - $item->quantity;
            $product->save();
        }
        
        return redirect()->route('admin.transactions.index')
            ->with('success', 'Transaksi berhasil dikonfirmasi.');
    }
    
    public function export()
    {
        $fileName = 'laporan_transaksi_' . Carbon::now()->format('Ymd_His') . '.csv';
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        $output = fopen('php://output', 'w');

        // Title
        fputcsv($output, ['Laporan Transaksi']);
        fputcsv($output, ['Tanggal:', Carbon::now()->format('d M Y')]);
        fputcsv($output, []); // empty line

        // Column headers
        fputcsv($output, ['No', 'ID Transaksi', 'No. Pesanan', 'Tanggal', 'Pelanggan', 'Metode Pembayaran', 'Total', 'Status']);

        $transactions = Transaction::with(['user', 'items'])
            ->orderBy('created_at', 'desc')
            ->get();
        $no = 1;
        foreach ($transactions as $transaction) {
            // Map payment method
            if ($transaction->payment_method == 'bank_transfer') {
                $paymentMethod = 'Bank Transfer';
            } elseif ($transaction->payment_method == 'credit_card') {
                $paymentMethod = 'Kartu Kredit';
            } elseif ($transaction->payment_method == 'e_wallet') {
                $paymentMethod = 'E-Wallet';
            } else {
                $paymentMethod = $transaction->payment_method;
            }
            // Map status
            if ($transaction->status == 'success') {
                $status = 'Berhasil';
            } elseif ($transaction->status == 'pending') {
                $status = 'Pending';
            } elseif ($transaction->status == 'failed') {
                $status = 'Gagal';
            } else {
                $status = $transaction->status;
            }

            fputcsv($output, [
                $no,
                '#' . $transaction->id,
                '#' . $transaction->order_number,
                $transaction->created_at->format('d M Y, H:i'),
                $transaction->user->name,
                $paymentMethod,
                $transaction->total_amount,
                $status
            ]);
            $no++;
        }

        // Summary
        fputcsv($output, []);
        fputcsv($output, ['Ringkasan']);
        $totalTransactions = Transaction::count();
        $successfulTransactions = Transaction::where('status', 'success')->count();
        $pendingTransactions = Transaction::where('status', 'pending')->count();
        $failedTransactions = Transaction::where('status', 'failed')->count();
        $totalRevenue = Transaction::where('status', 'success')->sum('total_amount');

        fputcsv($output, ['Total Transaksi:', $totalTransactions]);
        fputcsv($output, ['Transaksi Berhasil:', $successfulTransactions]);
        fputcsv($output, ['Transaksi Pending:', $pendingTransactions]);
        fputcsv($output, ['Transaksi Gagal:', $failedTransactions]);
        fputcsv($output, ['Total Pendapatan:', 'Rp ' . number_format($totalRevenue, 0, ',', '.')]);

        fclose($output);
        exit;
    }
}
