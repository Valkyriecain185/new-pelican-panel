<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoices & Subscriptions — NovaPanel</title>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;700;800&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        :root {
            --navy: #060b18; --navy-2: #0c1428; --navy-3: #111d3a;
            --indigo: #4f5fe8; --indigo-light: #6b79f0;
            --text-primary: #f0f4ff; --text-secondary: #8a9cc4; --text-muted: #4a5a7a;
            --border: rgba(79,95,232,0.2); --green: #22c55e; --red: #f87171; --orange: #fb923c;
        }
        body {
            font-family: 'DM Sans', sans-serif;
            background: var(--navy);
            color: var(--text-primary);
            min-height: 100vh;
            padding: 2rem 6%;
        }
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background-image:
                linear-gradient(rgba(79,95,232,0.04) 1px, transparent 1px),
                linear-gradient(90deg, rgba(79,95,232,0.04) 1px, transparent 1px);
            background-size: 60px 60px;
            pointer-events: none;
            z-index: 0;
        }
        .wrap {
            position: relative;
            z-index: 1;
            max-width: 960px;
            margin: 0 auto;
            padding-top: 3rem;
        }
        .page-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 2.5rem;
        }
        .page-header h1 {
            font-family: 'Syne', sans-serif;
            font-weight: 800;
            font-size: 1.8rem;
            letter-spacing: -0.03em;
        }
        .btn-back {
            color: var(--text-secondary);
            text-decoration: none;
            font-size: 0.875rem;
            transition: color 0.2s;
        }
        .btn-back:hover { color: var(--text-primary); }
        .section-title {
            font-family: 'Syne', sans-serif;
            font-weight: 700;
            font-size: 1rem;
            color: var(--text-secondary);
            text-transform: uppercase;
            letter-spacing: 0.08em;
            margin-bottom: 1rem;
        }
        .card {
            background: var(--navy-2);
            border: 1px solid var(--border);
            border-radius: 16px;
            overflow: hidden;
            margin-bottom: 2.5rem;
        }
        /* Subscriptions */
        .sub-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid var(--border);
            gap: 1rem;
            flex-wrap: wrap;
        }
        .sub-item:last-child { border-bottom: none; }
        .sub-info h3 {
            font-family: 'Syne', sans-serif;
            font-weight: 700;
            font-size: 1rem;
            margin-bottom: 0.25rem;
        }
        .sub-info p {
            font-size: 0.85rem;
            color: var(--text-muted);
        }
        .sub-right {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        .sub-amount {
            font-family: 'Syne', sans-serif;
            font-weight: 700;
            font-size: 1.1rem;
        }
        .badge {
            display: inline-block;
            padding: 0.2rem 0.6rem;
            border-radius: 100px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        .badge-active    { background: rgba(34,197,94,0.15);   color: var(--green); }
        .badge-cancelled { background: rgba(248,113,113,0.15); color: var(--red); }
        .badge-past_due  { background: rgba(251,146,60,0.15);  color: var(--orange); }
        .badge-paid      { background: rgba(34,197,94,0.15);   color: var(--green); }
        .btn-cancel {
            background: transparent;
            border: 1px solid rgba(248,113,113,0.3);
            color: var(--red);
            font-family: 'DM Sans', sans-serif;
            font-size: 0.8rem;
            font-weight: 500;
            padding: 0.4rem 0.9rem;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s;
        }
        .btn-cancel:hover {
            background: rgba(248,113,113,0.1);
            border-color: var(--red);
        }
        /* Invoice table */
        .invoice-table {
            width: 100%;
            border-collapse: collapse;
        }
        .invoice-table th {
            padding: 1rem 1.5rem;
            text-align: left;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: var(--text-muted);
            border-bottom: 1px solid var(--border);
        }
        .invoice-table td {
            padding: 1rem 1.5rem;
            font-size: 0.9rem;
            border-bottom: 1px solid var(--border);
            color: var(--text-secondary);
        }
        .invoice-table tr:last-child td { border-bottom: none; }
        .invoice-table tr:hover td { background: var(--navy-3); }
        .amount { font-family: 'Syne', sans-serif; font-weight: 700; color: var(--text-primary); }
        .empty {
            text-align: center;
            padding: 3rem 2rem;
            color: var(--text-muted);
        }
        .modal-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(6,11,24,0.8);
            z-index: 100;
            align-items: center;
            justify-content: center;
        }
        .modal-overlay.open { display: flex; }
        .modal {
            background: var(--navy-2);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 2rem;
            max-width: 420px;
            width: 90%;
            text-align: center;
        }
        .modal h2 {
            font-family: 'Syne', sans-serif;
            font-weight: 700;
            font-size: 1.2rem;
            margin-bottom: 0.75rem;
        }
        .modal p {
            color: var(--text-secondary);
            font-size: 0.9rem;
            line-height: 1.6;
            margin-bottom: 1.5rem;
        }
        .modal-actions {
            display: flex;
            gap: 0.75rem;
            justify-content: center;
        }
        .btn-confirm-cancel {
            background: var(--red);
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: 0.7rem 1.5rem;
            font-family: 'DM Sans', sans-serif;
            font-size: 0.9rem;
            font-weight: 500;
            cursor: pointer;
            transition: opacity 0.2s;
        }
        .btn-confirm-cancel:hover { opacity: 0.85; }
        .btn-dismiss {
            background: transparent;
            border: 1px solid var(--border);
            color: var(--text-secondary);
            border-radius: 8px;
            padding: 0.7rem 1.5rem;
            font-family: 'DM Sans', sans-serif;
            font-size: 0.9rem;
            cursor: pointer;
            transition: border-color 0.2s;
        }
        .btn-dismiss:hover { border-color: var(--indigo); color: var(--text-primary); }
    </style>
</head>
<body>
<div class="wrap">
    <div class="page-header">
        <h1>Billing</h1>
        <a href="/panel" class="btn-back">← Back to dashboard</a>
    </div>

    {{-- ACTIVE SUBSCRIPTIONS --}}
    <div class="section-title">Active subscriptions</div>
    <div class="card">
        @php
            $activeOrders = $orders->whereIn('status', ['active', 'past_due']);
        @endphp

        @if($activeOrders->isEmpty())
            <div class="empty">No active subscriptions.</div>
        @else
            @foreach($activeOrders as $order)
                <div class="sub-item">
                    <div class="sub-info">
                        <h3>{{ $order->plan }}</h3>
                        <p>{{ ucfirst($order->billing) }} billing · started {{ $order->created_at->format('d M Y') }}</p>
                    </div>
                    <div class="sub-right">
                        <span class="sub-amount">£{{ number_format($order->amount / 100, 2) }}</span>
                        <span class="badge badge-{{ $order->status }}">{{ ucfirst($order->status) }}</span>
                        @if($order->status === 'active' && $order->stripe_subscription_id)
                            <button class="btn-cancel" onclick="openCancel('{{ $order->stripe_subscription_id }}', '{{ $order->plan }}')">
                                Cancel
                            </button>
                        @endif
                    </div>
                </div>
            @endforeach
        @endif
    </div>

    {{-- INVOICE HISTORY --}}
    <div class="section-title">Invoice history</div>
    <div class="card">
        @if($invoices->isEmpty())
            <div class="empty">No invoices yet.</div>
        @else
            <table class="invoice-table">
                <thead>
                    <tr>
                        <th>Invoice</th>
                        <th>Plan</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($invoices as $invoice)
                        <tr>
                            <td>#{{ str_pad($invoice->id, 5, '0', STR_PAD_LEFT) }}</td>
                            <td>{{ $invoice->plan }}</td>
                            <td class="amount">£{{ number_format($invoice->amount / 100, 2) }}</td>
                            <td><span class="badge badge-{{ $invoice->status }}">{{ ucfirst($invoice->status) }}</span></td>
                            <td>{{ $invoice->paid_at?->format('d M Y') ?? $invoice->created_at->format('d M Y') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</div>

{{-- CANCEL MODAL --}}
<div class="modal-overlay" id="cancel-modal">
    <div class="modal">
        <h2>Cancel subscription?</h2>
        <p>Your <strong id="cancel-plan-name"></strong> subscription will remain active until the end of the current billing period, then cancel automatically. Your server will be suspended at that point.</p>
        <div class="modal-actions">
            <button class="btn-dismiss" onclick="closeCancel()">Keep subscription</button>
            <button class="btn-confirm-cancel" onclick="confirmCancel()">Yes, cancel</button>
        </div>
    </div>
</div>

<script>
    let pendingSubId = null;

    function openCancel(subId, planName) {
        pendingSubId = subId;
        document.getElementById('cancel-plan-name').textContent = planName;
        document.getElementById('cancel-modal').classList.add('open');
    }

    function closeCancel() {
        pendingSubId = null;
        document.getElementById('cancel-modal').classList.remove('open');
    }

    async function confirmCancel() {
        if (!pendingSubId) return;

        const res = await fetch('/subscription/cancel', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify({ subscription_id: pendingSubId }),
        });

        const data = await res.json();

        if (data.success) {
            window.location.reload();
        } else {
            alert('Something went wrong. Please try again.');
        }

        closeCancel();
    }
</script>
</body>
</html>