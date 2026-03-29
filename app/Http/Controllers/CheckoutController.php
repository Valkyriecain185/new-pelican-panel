<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Customer;
use Stripe\Subscription;
use Stripe\Price;

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

        $plan     = $this->plans[$planKey];
        $amount   = $request->billing === 'annual' ? $plan['annual'] : $plan['monthly'];
        $interval = $request->billing === 'annual' ? 'year' : 'month';

        Stripe::setApiKey(config('services.stripe.secret'));

        $user = auth()->user();

        // Create or retrieve Stripe customer
        if (!$user->stripe_customer_id) {
            $customer = Customer::create([
                'email'    => $user->email,
                'name'     => $user->username,
                'metadata' => ['user_id' => $user->id],
            ]);
            $user->stripe_customer_id = $customer->id;
            $user->save();
        }

        // Create a price on the fly
        $price = Price::create([
            'unit_amount'  => $amount,
            'currency'     => 'gbp',
            'recurring'    => ['interval' => $interval],
            'product_data' => ['name' => $plan['name']],
        ]);

        // Create subscription with payment_behavior = default_incomplete
        $subscription = Subscription::create([
            'customer'         => $user->stripe_customer_id,
            'items'            => [['price' => $price->id]],
            'payment_behavior' => 'default_incomplete',
            'payment_settings' => [
                'save_default_payment_method' => 'on_subscription',
                'payment_method_types'        => ['card'],
            ],
            'expand'           => ['latest_invoice.payment_intent'],
            'metadata'         => [
                'user_id'     => $user->id,
                'plan'        => $planKey,
                'billing'     => $request->billing,
                'server_name' => $request->server_name,
                'egg_id'      => $request->egg_id,
            ],
        ]);

        $paymentIntent = $subscription->latest_invoice->payment_intent ?? null;

        if (!$paymentIntent) {
            $subscription->cancel();
            return response()->json(['error' => 'Failed to initialise payment. Please try again.'], 500);
        }

        return response()->json([
            'clientSecret'   => $paymentIntent->client_secret,
            'subscriptionId' => $subscription->id,
        ]);
    }

    public function complete(Request $request)
    {
        $request->validate([
            'subscription_id' => 'required|string',
            'plan'            => 'required|string',
            'billing'         => 'required|string',
            'amount'          => 'required|integer',
        ]);

        Order::create([
            'user_id'                => auth()->id(),
            'plan'                   => $request->plan,
            'billing'                => $request->billing,
            'amount'                 => $request->amount,
            'currency'               => 'gbp',
            'stripe_subscription_id' => $request->subscription_id,
            'status'                 => 'active',
        ]);

        return response()->json(['success' => true]);
    }

    public function confirmation()
    {
        return view('order-confirmation');
    }

    public function invoices()
    {
        $invoices = Invoice::where('user_id', auth()->id())
            ->orderByDesc('created_at')
            ->get();

        return view('invoices', compact('invoices'));
    }

    public function cancel(Request $request)
    {
        $request->validate(['subscription_id' => 'required|string']);

        Stripe::setApiKey(config('services.stripe.secret'));

        $subscription = Subscription::retrieve($request->subscription_id);
        $subscription->cancel();

        Order::where('stripe_subscription_id', $request->subscription_id)
            ->update(['status' => 'cancelled']);

        return response()->json(['success' => true]);
    }
}