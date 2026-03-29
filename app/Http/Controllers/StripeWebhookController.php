<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Stripe\Webhook;

class StripeWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $payload   = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $secret    = config('services.stripe.webhook_secret');

        try {
            $event = Webhook::constructEvent($payload, $sigHeader, $secret);
        } catch (\Exception $e) {
            Log::error('Stripe webhook signature failed: ' . $e->getMessage());
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        match ($event->type) {
            'invoice.payment_succeeded'  => $this->handleInvoicePaid($event->data->object),
            'invoice.payment_failed'     => $this->handleInvoiceFailed($event->data->object),
            'customer.subscription.deleted' => $this->handleSubscriptionDeleted($event->data->object),
            default => null,
        };

        return response()->json(['status' => 'ok']);
    }

    private function handleInvoicePaid($invoice)
    {
        $subscriptionId = $invoice->subscription;
        $customerId     = $invoice->customer;

        // Find the subscription to get metadata
        \Stripe\Stripe::setApiKey(config('services.stripe.secret'));
        $subscription = \Stripe\Subscription::retrieve([
            'id'     => $subscriptionId,
            'expand' => [],
        ]);

        $metadata = $subscription->metadata;
        $userId   = $metadata->user_id ?? null;
        $planKey  = $metadata->plan ?? null;

        if (!$userId || !$planKey) {
            Log::error('Missing metadata on subscription: ' . $subscriptionId);
            return;
        }

        // Create invoice record
        Invoice::create([
            'user_id'               => $userId,
            'stripe_invoice_id'     => $invoice->id,
            'stripe_subscription_id'=> $subscriptionId,
            'plan'                  => $planKey,
            'amount'                => $invoice->amount_paid,
            'currency'              => $invoice->currency,
            'status'                => 'paid',
            'paid_at'               => now(),
        ]);

        // Only provision server on first invoice
        if ($invoice->billing_reason === 'subscription_create') {
            $this->provision($subscription);
        }

        // Make sure order is active
        Order::where('stripe_subscription_id', $subscriptionId)
            ->update(['status' => 'active']);

        Log::info("Invoice paid for user {$userId} on plan {$planKey}");
    }

    private function handleInvoiceFailed($invoice)
    {
        $subscriptionId = $invoice->subscription;

        // Set grace period — mark as past_due, server stays up for 1 day
        Order::where('stripe_subscription_id', $subscriptionId)
            ->update(['status' => 'past_due']);

        Log::warning("Invoice payment failed for subscription {$subscriptionId} — grace period started");
    }

    private function handleSubscriptionDeleted($subscription)
    {
        $subscriptionId = $subscription->id;
        $metadata       = $subscription->metadata;
        $userId         = $metadata->user_id ?? null;

        Order::where('stripe_subscription_id', $subscriptionId)
            ->update(['status' => 'cancelled']);

        // Suspend the server via Pelican API
        $panelUrl = 'https://panel-dev.sentinel-development.co.uk';
        $apiKey   = 'papp_AKp1307MLiN6GoyT91iuzwC57P2c3LDJ2Erp9s1xeVl';

        // Find servers belonging to this user and suspend them
        $serversResponse = Http::withHeaders([
            'Authorization' => 'Bearer ' . $apiKey,
            'Accept'        => 'application/json',
        ])->get("{$panelUrl}/api/application/servers?filter[external_id]={$subscriptionId}");

        foreach ($serversResponse->json('data') ?? [] as $server) {
            $serverId = $server['attributes']['id'];
            Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Accept'        => 'application/json',
            ])->post("{$panelUrl}/api/application/servers/{$serverId}/suspend");
        }

        Log::info("Subscription {$subscriptionId} cancelled — server suspended");
    }

    private function provision($subscription)
    {
        $metadata   = $subscription->metadata;
        $userId     = $metadata->user_id;
        $planKey    = $metadata->plan;
        $serverName = $metadata->server_name ?? "Server-{$userId}-{$planKey}";
        $eggId      = (int) ($metadata->egg_id ?? 1);

        $specs = [
            'starter'      => ['memory' => 2048,  'disk' => 20480,  'cpu' => 200],
            'pro'          => ['memory' => 6144,  'disk' => 51200,  'cpu' => 400],
            'business'     => ['memory' => 16384, 'disk' => 204800, 'cpu' => 800],
            'web-starter'  => ['memory' => 512,   'disk' => 1024,   'cpu' => 100],
            'web-pro'      => ['memory' => 1024,  'disk' => 10240,  'cpu' => 100],
            'bot-starter'  => ['memory' => 512,   'disk' => 5120,   'cpu' => 100],
            'bot-pro'      => ['memory' => 2048,  'disk' => 20480,  'cpu' => 200],
            'vps-nano'     => ['memory' => 2048,  'disk' => 40960,  'cpu' => 100],
            'vps-standard' => ['memory' => 8192,  'disk' => 163840, 'cpu' => 400],
        ];

        if (!isset($specs[$planKey])) {
            Log::error('Unknown plan: ' . $planKey);
            return;
        }

        $spec     = $specs[$planKey];
        $panelUrl = 'https://panel-dev.sentinel-development.co.uk';
        $apiKey   = 'papp_AKp1307MLiN6GoyT91iuzwC57P2c3LDJ2Erp9s1xeVl';

        // Fetch egg details
        $eggResponse = Http::withHeaders([
            'Authorization' => 'Bearer ' . $apiKey,
            'Accept'        => 'application/json',
        ])->get("{$panelUrl}/api/application/eggs/{$eggId}?include=variables");

        if (!$eggResponse->successful()) {
            Log::error("Failed to fetch egg {$eggId}: " . $eggResponse->body());
            return;
        }

        $egg         = $eggResponse->json('attributes');
        $startup     = $egg['startup'];
        $dockerImage = is_array($egg['docker_images'])
            ? array_values($egg['docker_images'])[0]
            : $egg['docker_image'];

        $environment = [];
        foreach ($egg['relationships']['variables']['data'] ?? [] as $var) {
            $attr = $var['attributes'];
            $environment[$attr['env_variable']] = $attr['default_value'];
        }

        // Get free allocation
        $allocResponse = Http::withHeaders([
            'Authorization' => 'Bearer ' . $apiKey,
            'Accept'        => 'application/json',
        ])->get("{$panelUrl}/api/application/nodes/1/allocations");

        $freeAllocation = collect($allocResponse->json('data'))
            ->first(fn($a) => !$a['attributes']['assigned']);

        if (!$freeAllocation) {
            Log::error('No free allocations available');
            return;
        }

        $allocationId = $freeAllocation['attributes']['id'];

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $apiKey,
            'Accept'        => 'application/json',
            'Content-Type'  => 'application/json',
        ])->post("{$panelUrl}/api/application/servers", [
            'name'         => $serverName,
            'user'         => (int) $userId,
            'egg'          => $eggId,
            'docker_image' => $dockerImage,
            'startup'      => $startup,
            'environment'  => $environment,
            'limits'       => [
                'memory' => $spec['memory'],
                'swap'   => 0,
                'disk'   => $spec['disk'],
                'io'     => 500,
                'cpu'    => $spec['cpu'],
            ],
            'feature_limits' => [
                'databases'   => 1,
                'backups'     => 2,
                'allocations' => 1,
            ],
            'allocation' => [
                'default' => $allocationId,
            ],
        ]);

        if ($response->successful()) {
            Log::info("Server provisioned for user {$userId} on plan {$planKey}");
        } else {
            Log::error("Server provisioning failed: " . $response->body());
        }
    }
}