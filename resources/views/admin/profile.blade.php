@extends('layouts.admin')

@section('title', 'Profil Admin')
@section('content-title', 'Profil Admin')

@section('styles')
<style>
    .profile-header {
        display: flex;
        align-items: center;
        margin-bottom: 2rem;
    }
    
    .profile-avatar {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        object-fit: cover;
        margin-right: 1.5rem;
        border: 3px solid #fff;
        box-shadow: 0 3px 10px rgba(0,0,0,0.1);
    }
    
    .profile-info h4 {
        margin-bottom: 0.5rem;
    }
    
    .profile-info p {
        color: #6c757d;
        margin-bottom: 0;
    }
    
    .form-label {
        font-weight: 500;
    }
    
    .avatar-preview {
        width: 150px;
        height: 150px;
        border-radius: 50%;
        object-fit: cover;
        margin-top: 10px;
        border: 3px solid #fff;
        box-shadow: 0 3px 10px rgba(0,0,0,0.1);
    }
</style>
@endsection

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="admin-card">
            <div class="admin-card-header">
                <h5 class="admin-card-title">Informasi Pribadi</h5>
            </div>
            <div class="admin-card-body">
                <div class="profile-header">
                    <img src="{{ $user->avatar ? asset('storage/avatars/' . $user->avatar) : asset('images/admin-avatar.jpg') }}" 
                         alt="{{ $user->name }}" class="profile-avatar">
                    <div class="profile-info">
                        <h4>{{ $user->name }}</h4>
                        <p>{{ $user->email }}</p>
                        <p>{{ $user->role == 'admin' ? 'Administrator' : 'Pengguna' }}</p>
                    </div>
                </div>
                
                @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif
                
                <form action="{{ route('admin.profile.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="name" class="form-label">Nama Lengkap</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       id="name" name="name" value="{{ old('name', $user->name) }}">
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                       id="email" name="email" value="{{ old('email', $user->email) }}">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="mb-3">
                                <label for="phone" class="form-label">Nomor Telepon</label>
                                <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                                       id="phone" name="phone" value="{{ old('phone', $user->phone) }}">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="avatar" class="form-label">Foto Profil</label>
                                <input type="file" class="form-control @error('avatar') is-invalid @enderror" 
                                       id="avatar" name="avatar" onchange="previewImage(event)">
                                <small class="form-text text-muted">Upload foto profil baru (opsional)</small>
                                @error('avatar')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                
                                <img src="{{ $user->avatar ? asset('storage/avatars/' . $user->avatar) : asset('images/admin-avatar.jpg') }}" 
                                     class="avatar-preview" id="avatar-preview">
                            </div>
                        </div>
                    </div>
                    
                    <hr class="my-4">
                    <h5 class="mb-3">Ubah Password</h5>
                    <p class="text-muted mb-4">Biarkan kosong jika tidak ingin mengubah password</p>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="current_password" class="form-label">Password Saat Ini</label>
                                <input type="password" class="form-control @error('current_password') is-invalid @enderror" 
                                       id="current_password" name="current_password">
                                @error('current_password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="password" class="form-label">Password Baru</label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                       id="password" name="password">
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="password_confirmation" class="form-label">Konfirmasi Password Baru</label>
                                <input type="password" class="form-control" 
                                       id="password_confirmation" name="password_confirmation">
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-4 text-end">
                        <button type="submit" class="btn btn-danger">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function previewImage(event) {
        const reader = new FileReader();
        reader.onload = function() {
            const preview = document.getElementById('avatar-preview');
            preview.src = reader.result;
        }
        reader.readAsDataURL(event.target.files[0]);
    }
</script>
@endsection