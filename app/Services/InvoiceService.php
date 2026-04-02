<?php
namespace App\Services;

use App\Models\Order;

class InvoiceService
{
    public function getInvoiceData($orderId)
    {
        $order = Order::with('product', 'variant')->findOrFail($orderId);

        $supplier = [
            'name' => 'Your Shop Name',
            'email' => 'shop@example.com',
            'address' => '123, Main Street, City'
        ];

        return compact('order', 'supplier');
    }
}