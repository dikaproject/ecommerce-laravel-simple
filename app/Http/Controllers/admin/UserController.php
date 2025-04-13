<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::where('role', 'user');
        
        // Apply search filter
        if ($request->has('search') && !empty($request->search)) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%')
                  ->orWhere('phone', 'like', '%' . $request->search . '%');
            });
        }
        
        $customers = $query->latest()->paginate(15);
        
        return view('admin.users.index', compact('customers'));
    }
    
    public function create()
    {
        return view('admin.users.create');
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:15',
            'role' => 'required|in:admin,user',
            'avatar' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);
        
        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->phone = $request->phone;
        $user->role = $request->role;
        
        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = 'storage/' . $avatarPath;
        }
        
        $user->save();
        
        return redirect()->route('admin.customers.index')
            ->with('success', 'Pengguna berhasil ditambahkan');
    }
    
    public function show($id)
    {
        $customer = User::with(['transactions', 'addresses'])->findOrFail($id);
        
        return view('admin.users.show', compact('customer'));
    }
    
    public function edit($id)
    {
        $customer = User::findOrFail($id);
        return view('admin.users.edit', compact('customer'));
    }
    
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'phone' => 'nullable|string|max:15',
            'role' => 'required|in:admin,user',
            'avatar' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ];
        
        // Add password validation only if password field is filled
        if ($request->filled('password')) {
            $rules['password'] = 'required|string|min:8|confirmed';
        }
        
        $request->validate($rules);
        
        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->role = $request->role;
        
        // Update password if provided
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }
        
        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            // Delete old avatar if exists
            if ($user->avatar && Storage::exists('public/' . str_replace('storage/', '', $user->avatar))) {
                Storage::delete('public/' . str_replace('storage/', '', $user->avatar));
            }
            
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = 'storage/' . $avatarPath;
        }
        
        $user->save();
        
        return redirect()->route('admin.customers.index')
            ->with('success', 'Pengguna berhasil diperbarui');
    }
    
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        
        // Delete avatar if exists
        if ($user->avatar && Storage::exists('public/' . str_replace('storage/', '', $user->avatar))) {
            Storage::delete('public/' . str_replace('storage/', '', $user->avatar));
        }
        
        $user->delete();
        
        return redirect()->route('admin.customers.index')
            ->with('success', 'Pengguna berhasil dihapus');
    }
}