<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout — NovaPanel</title>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;700;800&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <script src="https://js.stripe.com/v3/"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        :root {
            --navy: #060b18; --navy-2: #0c1428; --navy-3: #111d3a;
            --indigo: #4f5fe8; --indigo-light: #6b79f0; --indigo-glow: rgba(79,95,232,0.15);
            --text-primary: #f0f4ff; --text-secondary: #8a9cc4; --text-muted: #4a5a7a;
            --border: rgba(79,95,232,0.2); --green: #22c55e;
        }
        body {
            font-family: 'DM Sans', sans-serif;
            background: var(--navy);
            color: var(--text-primary);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 2rem;
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
        .checkout-wrap {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 900px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
        }
        .logo {
            grid-column: 1 / -1;
            font-family: 'Syne', sans-serif;
            font-weight: 800;
            font-size: 1.4rem;
            text-decoration: none;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
        }
        .logo span { color: var(--indigo-light); }
        .order-summary {
            background: var(--navy-2);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 2rem;
        }
        .order-summary h2 {
            font-family: 'Syne', sans-serif;
            font-weight: 700;
            font-size: 1.1rem;
            margin-bottom: 1.5rem;
            color: var(--text-secondary);
            text-transform: uppercase;
            letter-spacing: 0.08em;
            font-size: 0.8rem;
        }
        .plan-line {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.75rem 0;
            border-bottom: 1px solid var(--border);
        }
        .plan-line:last-of-type { border-bottom: none; }
        .plan-line .label { color: var(--text-secondary); font-size: 0.9rem; }
        .plan-line .value { font-weight: 500; font-size: 0.9rem; }
        .total-line {
            display: flex;
            justify-content: space-between;
            align-items: baseline;
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid var(--border);
        }
        .total-line .label {
            font-family: 'Syne', sans-serif;
            font-weight: 700;
            font-size: 1rem;
        }
        .total-line .value {
            font-family: 'Syne', sans-serif;
            font-weight: 800;
            font-size: 2rem;
            color: var(--text-primary);
        }
        .payment-box {
            background: var(--navy-2);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 2rem;
            display: flex;
            flex-direction: column;
            gap: 1.25rem;
        }
        .payment-box h2 {
            font-family: 'Syne', sans-serif;
            font-weight: 700;
            font-size: 1.2rem;
        }
        #card-element {
            background: var(--navy-3);
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 1rem;
            transition: border-color 0.2s;
        }
        #card-element.focused { border-color: var(--indigo); }
        #card-errors {
            color: #f87171;
            font-size: 0.85rem;
            min-height: 1.2rem;
        }
        .btn-pay {
            width: 100%;
            background: var(--indigo);
            color: #fff;
            border: none;
            border-radius: 10px;
            padding: 0.9rem;
            font-family: 'DM Sans', sans-serif;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            transition: background 0.2s, transform 0.15s;
        }
        .btn-pay:hover:not(:disabled) { background: var(--indigo-light); transform: translateY(-1px); }
        .btn-pay:disabled { opacity: 0.6; cursor: not-allowed; }
        .secure-note {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.8rem;
            color: var(--text-muted);
            justify-content: center;
        }
        @media (max-width: 700px) {
            .checkout-wrap { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
<div class="checkout-wrap">
    <a href="/" class="logo">Nova<span>Panel</span></a>

    <div class="order-summary">
        <h2>Order summary</h2>
        <div class="plan-line">
            <span class="label">Plan</span>
            <span class="value">{{ $planName }}</span>
        </div>
        <div class="plan-line">
            <span class="label">Billing</span>
            <span class="value">{{ ucfirst($billing) }}</span>
        </div>
        <div class="plan-line">
            <span class="label">Account</span>
            <span class="value">{{ auth()->user()->email }}</span>
        </div>
        <div class="total-line">
            <span class="label">Total today</span>
            <span class="value">£{{ $amountPounds }}</span>
        </div>
    </div>

    <div class="payment-box">
        <h2>Payment details</h2>
        <div id="card-element"></div>
        <div id="card-errors"></div>
        <button class="btn-pay" id="pay-btn">Pay £{{ $amountPounds }}</button>
        <div class="secure-note">🔒 Secured by Stripe. We never store card details.</div>
    </div>
</div>

<script>
    const stripe = Stripe('{{ $stripeKey }}');
    const elements = stripe.elements();
    const card = elements.create('card', {
        style: {
            base: {
                color: '#f0f4ff',
                fontFamily: 'DM Sans, sans-serif',
                fontSize: '15px',
                '::placeholder': { color: '#4a5a7a' },
            },
            invalid: { color: '#f87171' },
        }
    });

    card.mount('#card-element');
    card.on('focus', () => document.getElementById('card-element').classList.add('focused'));
    card.on('blur',  () => document.getElementById('card-element').classList.remove('focused'));
    card.on('change', e => {
        document.getElementById('card-errors').textContent = e.error ? e.error.message : '';
    });

    document.getElementById('pay-btn').addEventListener('click', async () => {
        const btn = document.getElementById('pay-btn');
        btn.disabled = true;
        btn.textContent = 'Processing...';

        const res = await fetch('/checkout/intent', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify({
                plan: '{{ $planKey }}',
                billing: '{{ $billing }}',
            }),
        });

        const { clientSecret, error } = await res.json();

        if (error) {
            document.getElementById('card-errors').textContent = error;
            btn.disabled = false;
            btn.textContent = 'Pay £{{ $amountPounds }}';
            return;
        }

        const result = await stripe.confirmCardPayment(clientSecret, {
            payment_method: { card }
        });

        if (result.error) {
            document.getElementById('card-errors').textContent = result.error.message;
            btn.disabled = false;
            btn.textContent = 'Pay £{{ $amountPounds }}';
        } else {
            await fetch('/checkout/complete', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: JSON.stringify({
                    payment_intent_id: result.paymentIntent.id,
                    plan: '{{ $planKey }}',
                    billing: '{{ $billing }}',
                    amount: {{ $amount }},
                }),
            });

            window.location.href = '/order/confirmation';
        }
    });
</script>
</body>
</html>