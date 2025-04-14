<!-- resources/views/pages/payment.blade.php -->
@extends('layouts.app')

@section('title', 'Pembayaran - Izzi Craft')

@section('styles')
<style>
    .payment-header {
        background-color: #f8f9fa;
        padding: 40px 0 20px;
        margin-bottom: 30px;
    }
    
    .payment-container {
        max-width: 800px;
        margin: 0 auto;
    }
    
    .payment-section {
        background-color: #fff;
        border-radius: 5px;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        padding: 20px;
        margin-bottom: 20px;
    }
    
    .section-title {
        font-size: 1.1rem;
        font-weight: 600;
        margin-bottom: 15px;
        display: flex;
        align-items: center;
    }
    
    .section-title i {
        margin-right: 8px;
        color: #dc3545;
    }
    
    .order-info {
        display: flex;
        justify-content: space-between;
        margin-bottom: 10px;
    }
    
    .payment-amount {
        font-size: 1.8rem;
        font-weight: 700;
        color: #dc3545;
        text-align: center;
        margin: 20px 0;
    }
    
    .countdown-timer {
        text-align: center;
        margin-bottom: 20px;
    }
    
    .countdown-timer .timer {
        font-size: 2rem;
        font-weight: 700;
        color: #dc3545;
    }
    
    .countdown-timer .timer-label {
        font-size: 0.9rem;
        color: #6c757d;
    }
    
    .payment-instruction {
        background-color: #f8f9fa;
        border-radius: 5px;
        padding: 15px;
        margin-bottom: 20px;
    }
    
    .instruction-step {
        margin-bottom: 10px;
        padding-left: 25px;
        position: relative;
    }
    
    .instruction-step .step-number {
        position: absolute;
        left: 0;
        top: 0;
        width: 20px;
        height: 20px;
        background-color: #dc3545;
        color: white;
        border-radius: 50%;
        font-size: 0.8rem;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .payment-status {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 30px 0;
    }
    
    .payment-status i {
        font-size: 4rem;
        color: #dc3545;
        margin-bottom: 20px;
    }
    
    .bank-logo {
        height: 40px;
        object-fit: contain;
        margin-bottom: 10px;
    }
    
    .account-info {
        background-color: #f8f9fa;
        border-radius: 5px;
        padding: 15px;
        margin-bottom: 15px;
    }
    
    .account-number {
        font-size: 1.2rem;
        font-weight: 600;
        font-family: monospace;
        letter-spacing: 2px;
    }
    
    .copy-btn {
        cursor: pointer;
        color: #dc3545;
    }
    
    .copy-btn:hover {
        text-decoration: underline;
    }
    
    .midtrans-container {
        border: 1px solid #dee2e6;
        border-radius: 5px;
        padding: 20px;
        margin-bottom: 20px;
    }
</style>
@endsection

@section('content')
<!-- Payment Header -->
<div class="payment-header">
    <div class="container">
        <h1 class="fw-bold">Pembayaran</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none">Beranda</a></li>
                <li class="breadcrumb-item"><a href="{{ route('cart.index') }}" class="text-decoration-none">Keranjang</a></li>
                <li class="breadcrumb-item"><a href="{{ route('cart.checkout') }}" class="text-decoration-none">Checkout</a></li>
                <li class="breadcrumb-item active" aria-current="page">Pembayaran</li>
            </ol>
        </nav>
    </div>
</div>

<div class="container pb-5">
    <div class="payment-container">
        <!-- Order Info -->
        <div class="payment-section">
            <div class="section-title">
                <i class="fas fa-file-invoice"></i> Informasi Pesanan
            </div>
            
            <div class="order-info">
                <span>Nomor Pesanan:</span>
                <span>{{ $order->order_number }}</span>
            </div>
            <div class="order-info">
                <span>Tanggal Pemesanan:</span>
                <span>{{ $order->created_at->format('d M Y, H:i') }}</span>
            </div>
            <div class="order-info">
                <span>Status Pembayaran:</span>
                <span class="badge bg-warning">Menunggu Pembayaran</span>
            </div>
        </div>
        
        <!-- Payment Amount & Countdown -->
        <div class="payment-section">
            <div class="payment-amount">
                Rp{{ number_format($order->total_amount, 0, ',', '.') }}
            </div>
            
            <div class="countdown-timer">
                <div class="timer-label">Selesaikan pembayaran sebelum</div>
                <div class="timer" id="countdown">23:59:59</div>
                <div class="timer-label">{{ $order->payment_due_date->format('d M Y, H:i') }}</div>
            </div>
        </div>
        
        <!-- Midtrans Payment Gateway -->
        <div class="payment-section">
            <div class="section-title">
                <i class="fas fa-credit-card"></i> Pembayaran Via Midtrans
            </div>
            
            <div class="text-center mb-4">
                <img src="{{ asset('images/Midtrans.webp') }}" alt="Midtrans" height="40">
                <p class="mt-2">Pembayaran aman dan terpercaya dengan berbagai metode pembayaran</p>
            </div>
            
            <div class="midtrans-container">
                <!-- Midtrans Payment Button/Iframe will be rendered here -->
                <div id="midtrans-payment-container"></div>
                
                <div class="d-grid gap-2 mt-3">
                    <button type="button" class="btn btn-danger btn-lg" id="pay-button">
                        Bayar Sekarang
                    </button>
                </div>
            </div>
            
            <div class="payment-instruction">
                <h6>Instruksi Pembayaran:</h6>
                <ol class="list-unstyled">
                    <li class="instruction-step">
                        <span class="step-number">1</span>
                        <div>Klik tombol "Bayar Sekarang" di atas.</div>
                    </li>
                    <li class="instruction-step">
                        <span class="step-number">2</span>
                        <div>Pilih metode pembayaran yang diinginkan (Transfer Bank, E-Wallet, QRIS, dll).</div>
                    </li>
                    <li class="instruction-step">
                        <span class="step-number">3</span>
                        <div>Ikuti petunjuk pembayaran sesuai metode yang dipilih.</div>
                    </li>
                    <li class="instruction-step">
                        <span class="step-number">4</span>
                        <div>Setelah pembayaran berhasil, Anda akan diarahkan kembali ke halaman konfirmasi.</div>
                    </li>
                </ol>
            </div>
        </div>
        
        <!-- Action Buttons -->
        <div class="d-grid gap-2">
            <a href="{{ route('orders.show', $order->id) }}" class="btn btn-outline-secondary">
                <i class="fas fa-clipboard-list me-1"></i> Detail Pesanan
            </a>
            <a href="{{ route('home') }}" class="btn btn-outline-secondary">
                <i class="fas fa-home me-1"></i> Kembali ke Beranda
            </a>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<!-- Midtrans JS SDK -->
<script src="https://app.{{ config('midtrans.is_production') ? '' : 'sandbox.' }}midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>

<script>
    // Countdown Timer
    function startCountdown() {
        const dueDate = new Date("{{ $order->payment_due_date }}").getTime();
        
        const countdownTimer = setInterval(function() {
            const now = new Date().getTime();
            const distance = dueDate - now;
            
            // Time calculations
            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);
            
            // Display the result
            document.getElementById("countdown").innerHTML = 
                (hours < 10 ? "0" + hours : hours) + ":" +
                (minutes < 10 ? "0" + minutes : minutes) + ":" +
                (seconds < 10 ? "0" + seconds : seconds);
            
            // If the countdown is finished
            if (distance < 0) {
                clearInterval(countdownTimer);
                document.getElementById("countdown").innerHTML = "00:00:00";
                
                // Redirect to order expired page or show expired message
                alert("Waktu pembayaran telah habis. Pesanan Anda akan dibatalkan.");
                window.location.href = "{{ route('orders.show', $order->id) }}";
            }
        }, 1000);
    }
    
    // Handle Midtrans payment
    document.getElementById('pay-button').addEventListener('click', function() {
        // Call Midtrans SNAP
        window.snap.pay('{{ $snapToken }}', {
            onSuccess: function(result) {
                // Send the result to your backend
                sendPaymentStatus(result, 'success');
            },
            onPending: function(result) {
                // Send the result to your backend
                sendPaymentStatus(result, 'pending');
            },
            onError: function(result) {
                // Send the result to your backend
                sendPaymentStatus(result, 'error');
            },
            onClose: function() {
                // If customer closed the popup without finishing the payment
                alert('Anda menutup jendela pembayaran sebelum menyelesaikan transaksi.');
            }
        });
    });
    
    // Send payment status to backend
    function sendPaymentStatus(result, status) {
        fetch('{{ route('payments.update', $order->id) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                status: status,
                result: result
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Redirect to order success page
                window.location.href = data.redirect_url;
            } else {
                // Show error message
                alert(data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat memproses pembayaran Anda.');
        });
    }
    
    // Start countdown when page loads
    window.addEventListener('DOMContentLoaded', function() {
        startCountdown();
    });
</script>
@endsection