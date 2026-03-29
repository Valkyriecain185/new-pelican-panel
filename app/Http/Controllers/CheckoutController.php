<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\PaymentIntent;

class CheckoutController extends Controller
{
    private array $plans = [
        'starter'      => ['name' => 'Game Server — Starter',  'monthly' => 500,  'annual' => 4800],
        'pro'          => ['name' => 'Game Server — Pro',       'monthly' => 1500, 'annual' => 14400],
        'business'     => ['name' => 'Game Server — Business',  'monthly' => 4000, 'annual' => 38400],
        'web-starter'  => ['name' => 'Web Hosting — Starter',   'monthly' => 300,  'annual' => 2880],
        'web-pro'      => ['name' => 'Web Hosting — Pro',       'monthly' => 800,  'annual' => 7680],
        'bot-starter'  => ['name' => 'Discord Bot — Starter',   'monthly' => 200,  'annual' => 1920],
        'bot-pro'      => ['name' => 'Discord Bot — Pro',       'monthly' => 600,  'annual' => 5760],
        'vps-nano'     => ['name' => 'VPS — Nano',              'monthly' => 1000, 'annual' => 9600],
        'vps-standard' => ['name' => 'VPS — Standard',          'monthly' => 2500, 'annual' => 24000],
    ];

    public function show(Request $request)
    {
        $planKey = $request->query('plan', 'starter');
        $billing = $request->query('billing', 'monthly');

        if (!array_key_exists($planKey, $this->plans)) {
            abort(404);
        }

        $plan   = $this->plans[$planKey];
        $amount = $billing === 'annual' ? $plan['annual'] : $plan['monthly'];

        return view('checkout', [
            'planKey'      => $planKey,
            'planName'     => $plan['name'],
            'billing'      => $billing,
            'amount'       => $amount,
            'amountPounds' => number_format($amount / 100, 2),
            'stripeKey'    => config('services.stripe.key'),
        ]);
    }

    public function createIntent(Request $request)
    {
        $request->validate([
            'plan'        => 'required|string',
            'billing'     => 'required|in:monthly,annual',
            'server_name' => 'required|string|max:64',
            'egg_id'      => 'required|integer',
        ]);

        $planKey = $request->plan;

        if (!array_key_exists($planKey, $this->plans)) {
            return response()->json(['error' => 'Invalid plan'], 422);
        }

        $plan   = $this->plans[$planKey];
        $amount = $request->billing === 'annual' ? $plan['annual'] : $plan['monthly'];

        Stripe::setApiKey(config('services.stripe.secret'));

        $intent = PaymentIntent::create([
            'amount'   => $amount,
            'currency' => 'gbp',
            'metadata' => [
                'user_id'     => auth()->id(),
                'plan'        => $planKey,
                'billing'     => $request->billing,
                'server_name' => $request->server_name,
                'egg_id'      => $request->egg_id,
            ],
        ]);

        return response()->json(['clientSecret' => $intent->client_secret]);
    }

    public function complete(Request $request)
    {
        $request->validate([
            'payment_intent_id' => 'required|string',
            'plan'              => 'required|string',
            'billing'           => 'required|string',
            'amount'            => 'required|integer',
        ]);

        Order::create([
            'user_id'               => auth()->id(),
            'plan'                  => $request->plan,
            'billing'               => $request->billing,
            'amount'                => $request->amount,
            'currency'              => 'gbp',
            'stripe_payment_intent' => $request->payment_intent_id,
            'status'                => 'active',
        ]);

        return response()->json(['success' => true]);
    }

    public function confirmation()
    {
        return view('order-confirmation');
    }
}