<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Stripe\Webhook;

class StripeWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $secret = config('services.stripe.webhook_secret');

        try {
            $event = Webhook::constructEvent($payload, $sigHeader, $secret);
        } catch (\Exception $e) {
            Log::error('Stripe webhook signature failed: ' . $e->getMessage());
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        if ($event->type === 'payment_intent.succeeded') {
            $intent = $event->data->object;
            $this->provision($intent);
        }

        return response()->json(['status' => 'ok']);
    }

    private function provision($intent)
    {
        $userId  = $intent->metadata->user_id;
        $planKey = $intent->metadata->plan;
        $billing = $intent->metadata->billing;

        Order::where('stripe_payment_intent', $intent->id)
            ->update(['status' => 'active']);

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
            Log::error('Unknown plan in webhook: ' . $planKey);
            return;
        }

        $spec     = $specs[$planKey];
        $panelUrl = 'https://panel-dev.sentinel-development.co.uk';
        $apiKey   = 'papp_AKp1307MLiN6GoyT91iuzwC57P2c3LDJ2Erp9s1xeVl';

        // Get a free allocation
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
            'name'         => "Server-{$userId}-{$planKey}",
            'user'         => (int) $userId,
            'egg'          => 1,
            'docker_image' => 'ghcr.io/pterodactyl/yolks:java_17',
            'startup'      => 'java -Xms128M -Xmx{{SERVER_MEMORY}}M -jar server.jar',
            'environment'  => [
                'SERVER_JARFILE'  => 'server.jar',
                'VANILLA_VERSION' => 'latest',
            ],
            'limits' => [
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

