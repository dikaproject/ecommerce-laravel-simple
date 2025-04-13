<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'address_id',
        'order_number',
        'total_amount',
        'shipping_cost',
        'discount',
        'payment_method',
        'payment_status',
        'snap_token',
        'midtrans_id',
        'status',
        'payment_due_date',
    ];

    protected $dates = [
        'payment_due_date',
        'created_at',
        'updated_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function address()
    {
        return $this->belongsTo(Address::class);
    }

    public function items()
    {
        return $this->hasMany(TransactionItem::class);
    }
}