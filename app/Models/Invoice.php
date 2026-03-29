<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $fillable = [
        'user_id', 'order_id', 'stripe_invoice_id',
        'stripe_subscription_id', 'plan', 'amount',
        'currency', 'status', 'paid_at',
    ];

    protected $casts = [
        'paid_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function formattedAmount(): string
    {
        return '£' . number_format($this->amount / 100, 2);
    }
}