@extends('layouts.admin')

@section('title', 'Edit Pengguna')

@section('content-title', 'Edit Pengguna')

@section('styles')
<style>
    .card {
        border-radius: 8px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        margin-bottom: 24px;
        border: none;
    }
    
    .card-header {
        background-color: #fff;
        border-bottom: 1px solid #f0f0f0;
        padding: 16px 20px;
        font-weight: 600;
    }
    
    .card-body {
        padding: 20px;
    }
    
    .form-label {
        font-weight: 500;
        font-size: 14px;
        color: #495057;
        margin-bottom: 8px;
    }
    
    .form-control {
        border-radius: 4px;
        padding: 10px 15px;
        font-size: 14px;
        border: 1px solid #ced4da;
    }
    
    .form-control:focus {
        border-color: #dc3545;
        box-shadow: 0 0 0 0.25rem rgba(220, 53, 69, 0.25);
    }
    
    .btn-save {
        background-color: #dc3545;
        color: white;
        font-weight: 500;
        padding: 10px 20px;
        border-radius: 4px;
        border: none;
        transition: all 0.2s;
    }
    
    .btn-save:hover {
        background-color: #c82333;
        color: white;
    }
    
    .user-avatar {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        object-fit: cover;
    }
    
    .avatar-placeholder {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        background-color: #f8f9fa;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 32px;
        color: #adb5bd;
    }
    
    .form-text {
        font-size: 12px;
        color: #6c757d;
    }
</style>
@endsection

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0">Edit Pengguna</h1>
    <a href="{{ route('admin.customers.index') }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-1"></i> Kembali
    </a>
</div>

<div class="card">
    <div class="card-header">
        Form Edit Pengguna
    </div>
    <div class="card-body">
        <form action="{{ route('admin.customers.update', $customer->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="row mb-4">
                <div class="col-md-2 text-center">
                    @if($customer->avatar)
                        <img src="{{ asset($customer->avatar) }}" alt="{{ $customer->name }}" class="user-avatar mb-3">
                    @else
                        <div class="avatar-placeholder mb-3 mx-auto">
                            <i class="fas fa-user"></i>
                        </div>
                    @endif
                </div>
                <div class="col-md-10">
                    <div class="mb-3">
                        <label for="avatar" class="form-label">Foto Profil</label>
                        <input type="file" class="form-control @error('avatar') is-invalid @enderror" id="avatar" name="avatar">
                        <div class="form-text">Upload gambar (maksimal 2MB, format: JPG, PNG)</div>
                        @error('avatar')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="name" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $customer->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $customer->email) }}" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="phone" class="form-label">Nomor Telepon</label>
                        <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone', $customer->phone) }}">
                        @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="role" class="form-label">Role <span class="text-danger">*</span></label>
                        <select class="form-select @error('role') is-invalid @enderror" id="role" name="role" required>
                            <option value="user" {{ old('role', $customer->role) == 'user' ? 'selected' : '' }}>User</option>
                            <option value="admin" {{ old('role', $customer->role) == 'admin' ? 'selected' : '' }}>Admin</option>
                        </select>
                        @error('role')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-12">
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password">
                        <div class="form-text">Kosongkan jika tidak ingin mengubah password</div>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-12">
                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
                    </div>
                </div>
            </div>
            
            <div class="mt-4 text-end">
                <a href="{{ route('admin.customers.index') }}" class="btn btn-outline-secondary me-2">Batal</a>
                <button type="submit" class="btn btn-save">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>
@endsection
