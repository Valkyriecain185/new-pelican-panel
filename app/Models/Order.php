<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_id', 'plan', 'billing', 'amount',
        'currency', 'stripe_payment_intent',
        'stripe_subscription_id', 'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}