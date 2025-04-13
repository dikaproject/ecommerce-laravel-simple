<?php

namespace App\Services\Midtrans;

use Midtrans\Config;
use Midtrans\Snap;

class CreateSnapTokenService extends Midtrans
{
    protected $order;
    
    public function __construct($order)
    {
        parent::__construct();
        $this->order = $order;
    }
    
    public function getSnapToken()
    {
        $params = [
            'transaction_details' => [
                'order_id' => $this->order->order_number,
                'gross_amount' => (int) $this->order->total_amount,
            ],
            'customer_details' => [
                'first_name' => $this->order->user->name,
                'email' => $this->order->user->email,
                'phone' => $this->order->address->phone ?? '',
                'billing_address' => [
                    'first_name' => $this->order->address->recipient_name ?? $this->order->user->name,
                    'phone' => $this->order->address->phone ?? '',
                    'address' => $this->order->address->full_address ?? '',
                    'city' => $this->order->address->city ?? '',
                    'postal_code' => $this->order->address->postal_code ?? '',
                    'country_code' => 'IDN',
                ],
            ],
            'item_details' => $this->getItemDetails(),
        ];

        $snapToken = Snap::getSnapToken($params);
        
        return $snapToken;
    }
    
    protected function getItemDetails()
    {
        $items = [];
        
        // Add all order items
        foreach ($this->order->items as $item) {
            $items[] = [
                'id' => $item->product_id,
                'price' => (int) $item->price,
                'quantity' => $item->quantity,
                'name' => $item->product->name,
            ];
        }
        
        // Add shipping cost as an item if applicable
        if ($this->order->shipping_cost > 0) {
            $items[] = [
                'id' => 'SHIPPING',
                'price' => (int) $this->order->shipping_cost,
                'quantity' => 1,
                'name' => 'Shipping Cost',
            ];
        }
        
        // Add discount as a negative item if applicable
        if ($this->order->discount > 0) {
            $items[] = [
                'id' => 'DISCOUNT',
                'price' => (int) -$this->order->discount,
                'quantity' => 1,
                'name' => 'Discount',
            ];
        }
        
        return $items;
    }
}
