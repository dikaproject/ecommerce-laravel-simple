@extends('layouts.admin')

@section('title', 'Kelola Pengguna')

@section('content-title', 'Kelola Pengguna')

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
    
    .table {
        width: 100%;
        font-size: 14px;
    }
    
    .table th {
        font-weight: 600;
        color: #333;
        border-top: none;
        background-color: #f8f9fa;
    }
    
    .table td, .table th {
        padding: 12px 16px;
        vertical-align: middle;
    }
    
    .btn-action {
        padding: 5px 10px;
        font-size: 13px;
        border-radius: 4px;
    }
    
    .btn-add {
        background-color: #28a745;
        color: white;
        font-weight: 500;
        padding: 8px 16px;
        border-radius: 4px;
        border: none;
        transition: all 0.2s;
    }
    
    .btn-add:hover {
        background-color: #218838;
        color: white;
    }
    
    .search-box {
        max-width: 300px;
    }
    
    .avatar-sm {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        object-fit: cover;
    }
    
    .user-role {
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 12px;
        font-weight: 500;
    }
    
    .role-admin {
        background-color: rgba(220, 53, 69, 0.1);
        color: #dc3545;
    }
    
    .role-user {
        background-color: rgba(13, 110, 253, 0.1);
        color: #0d6efd;
    }
</style>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Kelola Pengguna</h1>
    <a href="{{ route('admin.customers.create') }}" class="btn-add">
        <i class="fas fa-plus me-2"></i>Tambah Pengguna
    </a>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>Daftar Pengguna</span>
        <form action="{{ route('admin.customers.index') }}" method="GET" class="search-box">
            <div class="input-group">
                <input type="text" class="form-control form-control-sm" placeholder="Cari pengguna..." name="search" value="{{ request('search') }}">
                <button class="btn btn-sm btn-outline-secondary" type="submit">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </form>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col">Foto</th>
                        <th scope="col">Nama</th>
                        <th scope="col">Email</th>
                        <th scope="col">No. Telepon</th>
                        <th scope="col">Role</th>
                        <th scope="col">Terdaftar Pada</th>
                        <th scope="col">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($customers as $customer)
                    <tr>
                        <td>{{ $customer->id }}</td>
                        <td>
                            @if($customer->avatar)
                                <img src="{{ asset($customer->avatar) }}" alt="{{ $customer->name }}" class="avatar-sm">
                            @else
                                <div class="avatar-sm d-flex align-items-center justify-content-center bg-light text-secondary">
                                    <i class="fas fa-user"></i>
                                </div>
                            @endif
                        </td>
                        <td>{{ $customer->name }}</td>
                        <td>{{ $customer->email }}</td>
                        <td>{{ $customer->phone ?? '-' }}</td>
                        <td>
                            <span class="user-role {{ $customer->role == 'admin' ? 'role-admin' : 'role-user' }}">
                                {{ ucfirst($customer->role) }}
                            </span>
                        </td>
                        <td>{{ $customer->created_at->format('d M Y') }}</td>
                        <td>
                            <div class="btn-group">
                                <a href="{{ route('admin.customers.show', $customer->id) }}" class="btn btn-sm btn-outline-primary btn-action">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.customers.edit', $customer->id) }}" class="btn btn-sm btn-outline-secondary btn-action">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button type="button" class="btn btn-sm btn-outline-danger btn-action" 
                                    onclick="if(confirm('Apakah Anda yakin ingin menghapus pengguna ini?')) { document.getElementById('delete-form-{{ $customer->id }}').submit(); }">
                                    <i class="fas fa-trash"></i>
                                </button>
                                <form id="delete-form-{{ $customer->id }}" action="{{ route('admin.customers.destroy', $customer->id) }}" method="POST" style="display: none;">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-4">Tidak ada pengguna yang ditemukan</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer">
        <div class="d-flex justify-content-end">
            {{ $customers->links() }}
        </div>
    </div>
</div>
@endsection
