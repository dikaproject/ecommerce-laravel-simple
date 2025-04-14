@extends('layouts.app')

@section('title', 'Masuk - Izzi Craft')

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
    
    .form-check-label {
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
            <h1 class="auth-title">Masuk</h1>
            <p class="auth-subtitle">Selamat datang kembali di Izzi Craft</p>
            
            <!-- Login Form -->
            <form method="POST" action="{{ route('login') }}">
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
                
                <!-- Email Address -->
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control @error('email') is-invalid @enderror" 
                           id="email" name="email" value="{{ old('email') }}" required autofocus>
                    @error('email')
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
                    @error('password')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                    @enderror
                </div>
                
                <!-- Remember Me -->
                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" id="remember" name="remember">
                    <label class="form-check-label" for="remember">Ingat saya</label>
                </div>
                
                <!-- Submit Button -->
                <div class="d-grid mb-3">
                    <button type="submit" class="btn btn-danger btn-lg">Masuk</button>
                </div>
            
            </form>
        </div>
        
        <!-- Register Link -->
        <div class="text-center">
            <p>Belum punya akun? <a href="{{ route('register') }}" class="text-decoration-none">Daftar sekarang</a></p>
        </div>
    </div>
</div>
@endsection