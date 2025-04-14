@extends('layouts.app')

@section('title', 'Daftar - Izzi Craft')

@section('styles')
<style>
    .auth-container {
        max-width: 500px;
        margin: 0 auto;
        padding: 40px 0;
    }
    
    .auth-logo {
        text-align: center;
        margin-bottom: 30px;
    }
    
    .auth-logo img {
        max-height: 60px;
    }
    
    .auth-card {
        background-color: #fff;
        border-radius: 5px;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        padding: 30px;
        margin-bottom: 20px;
    }
    
    .auth-title {
        font-size: 1.5rem;
        font-weight: 700;
        margin-bottom: 20px;
        text-align: center;
    }
    
    .auth-subtitle {
        text-align: center;
        margin-bottom: 30px;
        color: #6c757d;
    }
    
    .form-text {
        font-size: 0.85rem;
        color: #6c757d;
    }
</style>
@endsection

@section('content')
<div class="container">
    <div class="auth-container">
        <div class="auth-logo">
            <a href="{{ route('home') }}">
                <img src="{{ asset('images/logo-izzicraft.png') }}" alt="Izzi Craft Logo">
            </a>
        </div>
        
        <div class="auth-card">
            <h1 class="auth-title">Daftar Akun</h1>
            <p class="auth-subtitle">Bergabunglah dengan Izzi Craft untuk pengalaman berbelanja yang lebih baik</p>
            
            <!-- Registration Form -->
            <form method="POST" action="{{ route('register') }}">
                @csrf
                
                <!-- Show Alert for Errors -->
                @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif
                
                <!-- Name -->
                <div class="mb-3">
                    <label for="name" class="form-label">Nama Lengkap</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                           id="name" name="name" value="{{ old('name') }}" required autofocus>
                    @error('name')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                    @enderror
                </div>
                
                <!-- Email Address -->
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control @error('email') is-invalid @enderror" 
                           id="email" name="email" value="{{ old('email') }}" required>
                    @error('email')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                    @enderror
                </div>
                
                <!-- Phone Number -->
                <div class="mb-3">
                    <label for="phone" class="form-label">Nomor Telepon</label>
                    <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                           id="phone" name="phone" value="{{ old('phone') }}" required>
                    @error('phone')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                    @enderror
                </div>
                
                <!-- Password -->
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control @error('password') is-invalid @enderror" 
                           id="password" name="password" required>
                    <div class="form-text">
                        Password harus minimal 8 karakter dan mengandung huruf dan angka.
                    </div>
                    @error('password')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                    @enderror
                </div>
                
                <!-- Confirm Password -->
                <div class="mb-3">
                    <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
                    <input type="password" class="form-control" 
                           id="password_confirmation" name="password_confirmation" required>
                </div>
                
                <!-- Terms & Conditions -->
                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input @error('terms') is-invalid @enderror" 
                           id="terms" name="terms" required>
                    <label class="form-check-label" for="terms">
                        Saya setuju dengan <a href="{{ route('terms') }}" class="text-decoration-none">Syarat & Ketentuan</a> 
                        dan <a href="{{ route('privacy') }}" class="text-decoration-none">Kebijakan Privasi</a>
                    </label>
                    @error('terms')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                    @enderror
                </div>
                
                <!-- Submit Button -->
                <div class="d-grid mb-3">
                    <button type="submit" class="btn btn-danger btn-lg">Daftar</button>
                </div>
            </form>
        </div>
        
        <!-- Login Link -->
        <div class="text-center">
            <p>Sudah punya akun? <a href="{{ route('login') }}" class="text-decoration-none">Masuk sekarang</a></p>
        </div>
    </div>
</div>
@endsection