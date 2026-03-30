<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
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

        // Create a SetupIntent to collect and save the card first
        $setupIntent = \Stripe\SetupIntent::create([
            'customer'             => $user->stripe_customer_id,
            'payment_method_types' => ['card'],
            'metadata'             => [
                'user_id'     => $user->id,
                'plan'        => $planKey,
                'billing'     => $request->billing,
                'server_name' => $request->server_name,
                'egg_id'      => $request->egg_id,
                'price_id'    => $price->id,
            ],
        ]);

        return response()->json([
            'clientSecret' => $setupIntent->client_secret,
        ]);
    }

    public function complete(Request $request)
    {
        $request->validate([
            'setup_intent_id' => 'required|string',
            'plan'            => 'required|string',
            'billing'         => 'required|string',
            'amount'          => 'required|integer',
        ]);

        Stripe::setApiKey(config('services.stripe.secret'));

        $user = auth()->user();

        // Retrieve the setup intent to get the saved payment method
        $setupIntent     = \Stripe\SetupIntent::retrieve($request->setup_intent_id);
        $paymentMethodId = $setupIntent->payment_method;

        // Set as default payment method on customer
        Customer::update($user->stripe_customer_id, [
            'invoice_settings' => ['default_payment_method' => $paymentMethodId],
        ]);

        // Create the subscription with the saved card
        $subscription = Subscription::create([
            'customer'               => $user->stripe_customer_id,
            'items'                  => [['price' => $setupIntent->metadata->price_id]],
            'default_payment_method' => $paymentMethodId,
            'metadata'               => [
                'user_id'     => $user->id,
                'plan'        => $setupIntent->metadata->plan,
                'billing'     => $setupIntent->metadata->billing,
                'server_name' => $setupIntent->metadata->server_name,
                'egg_id'      => $setupIntent->metadata->egg_id,
            ],
        ]);

        $order = Order::create([
            'user_id'                => auth()->id(),
            'plan'                   => $request->plan,
            'billing'                => $request->billing,
            'amount'                 => $request->amount,
            'currency'               => 'gbp',
            'stripe_subscription_id' => $subscription->id,
            'status'                 => 'active',
        ]);

        // Create invoice record
        \App\Models\Invoice::create([
            'user_id'                => auth()->id(),
            'order_id'               => $order->id,
            'stripe_subscription_id' => $subscription->id,
            'plan'                   => $request->plan,
            'amount'                 => $request->amount,
            'currency'               => 'gbp',
            'status'                 => 'paid',
            'paid_at'                => now(),
        ]);

        // Provision server immediately
        app(\App\Http\Controllers\StripeWebhookController::class)->provisionFromSubscription($subscription);

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

        $orders = Order::where('user_id', auth()->id())
            ->orderByDesc('created_at')
            ->get();

        return view('invoices', compact('invoices', 'orders'));
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