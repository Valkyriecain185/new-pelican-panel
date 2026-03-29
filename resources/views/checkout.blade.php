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
            --border: rgba(79,95,232,0.2); --border-hover: rgba(79,95,232,0.5); --green: #22c55e;
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
            max-width: 1000px;
            display: grid;
            grid-template-columns: 1fr 1.4fr;
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
            align-self: start;
        }
        .order-summary h2 {
            font-family: 'Syne', sans-serif;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            font-size: 0.8rem;
            color: var(--text-secondary);
            margin-bottom: 1.5rem;
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
        .total-line .label { font-family: 'Syne', sans-serif; font-weight: 700; font-size: 1rem; }
        .total-line .value { font-family: 'Syne', sans-serif; font-weight: 800; font-size: 2rem; }
        .payment-box {
            background: var(--navy-2);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 2rem;
            display: flex;
            flex-direction: column;
            gap: 1.25rem;
        }
        .payment-box h2 { font-family: 'Syne', sans-serif; font-weight: 700; font-size: 1.2rem; }
        .field-label {
            font-size: 0.8rem;
            font-weight: 500;
            color: var(--text-secondary);
            text-transform: uppercase;
            letter-spacing: 0.06em;
            margin-bottom: 0.4rem;
        }
        .field-input {
            width: 100%;
            background: var(--navy-3);
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 0.8rem 1rem;
            color: var(--text-primary);
            font-family: 'DM Sans', sans-serif;
            font-size: 0.95rem;
            outline: none;
            transition: border-color 0.2s;
        }
        .field-input:focus { border-color: var(--indigo); }
        .field-input::placeholder { color: var(--text-muted); }
        .field-select {
            width: 100%;
            background: var(--navy-3);
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 0.8rem 1rem;
            color: var(--text-primary);
            font-family: 'DM Sans', sans-serif;
            font-size: 0.95rem;
            outline: none;
            transition: border-color 0.2s;
            cursor: pointer;
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg width='12' height='8' viewBox='0 0 12 8' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M1 1L6 7L11 1' stroke='%238a9cc4' stroke-width='1.5' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 1rem center;
            padding-right: 2.5rem;
        }
        .field-select:focus { border-color: var(--indigo); }
        .field-select option { background: var(--navy-3); color: var(--text-primary); }
        .field-select optgroup { color: var(--indigo-light); font-weight: 600; }
        #card-element {
            background: var(--navy-3);
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 1rem;
            transition: border-color 0.2s;
        }
        #card-element.focused { border-color: var(--indigo); }
        #card-errors { color: #f87171; font-size: 0.85rem; min-height: 1.2rem; }
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
        .divider {
            height: 1px;
            background: var(--border);
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
        <h2>Server setup</h2>

        <div>
            <div class="field-label">Server name</div>
            <input type="text" id="server-name" class="field-input" placeholder="e.g. My Minecraft Server" maxlength="64">
        </div>

        <div>
            <div class="field-label">Server type</div>
            <select id="egg-select" class="field-select">
                <optgroup label="— Minecraft">
                    <option value="1">Paper</option>
                    <option value="187">Vanilla Minecraft</option>
                    <option value="150">Fabric</option>
                    <option value="154">Forge Minecraft</option>
                    <option value="166">NeoForge</option>
                    <option value="170">Purpur</option>
                    <option value="153">Folia</option>
                    <option value="173">Spigot</option>
                    <option value="145">Bungeecord</option>
                    <option value="189">Velocity</option>
                    <option value="185">Vanilla Bedrock</option>
                    <option value="163">Modrinth Generic</option>
                    <option value="147">CurseForge Generic</option>
                </optgroup>
                <optgroup label="— Popular Games">
                    <option value="86">Rust</option>
                    <option value="72">Palworld</option>
                    <option value="122">Valheim</option>
                    <option value="78">Project Zomboid</option>
                    <option value="33">DayZ</option>
                    <option value="38">Enshrouded</option>
                    <option value="91">Satisfactory</option>
                    <option value="35">Don't Starve Together</option>
                    <option value="24">Counter-Strike 2</option>
                    <option value="109">Team Fortress 2</option>
                    <option value="43">Garry's Mod</option>
                    <option value="8">Ark: Survival Evolved</option>
                    <option value="5">ARK: Survival Ascended</option>
                </optgroup>
                <optgroup label="— More Games">
                    <option value="4">7 Days To Die</option>
                    <option value="6">Abiotic Factor</option>
                    <option value="9">Arma 3</option>
                    <option value="10">Arma Reforger</option>
                    <option value="17">Barotrauma</option>
                    <option value="22">Conan Exiles</option>
                    <option value="36">Eco</option>
                    <option value="52">Killing Floor 2</option>
                    <option value="53">Left 4 Dead</option>
                    <option value="54">Left 4 Dead 2</option>
                    <option value="58">Mordhau</option>
                    <option value="85">Risk of Rain 2</option>
                    <option value="89">SCP:SL</option>
                    <option value="95">Sons Of The Forest</option>
                    <option value="98">Space Engineers</option>
                    <option value="100">Squad</option>
                    <option value="102">Starbound</option>
                    <option value="118">Unturned</option>
                    <option value="119">V Rising</option>
                    <option value="141">Rust (Generic)</option>
                </optgroup>
                <optgroup label="— Discord Bots / Apps">
                    <option value="138">Node.js</option>
                    <option value="140">Python</option>
                    <option value="136">Go</option>
                    <option value="133">C#</option>
                    <option value="134">Java</option>
                    <option value="130">Bun</option>
                    <option value="131">Deno</option>
                    <option value="132">Elixir</option>
                </optgroup>
                <optgroup label="— Voice & Comms">
                    <option value="200">Teamspeak 3</option>
                    <option value="198">Mumble</option>
                </optgroup>
                <optgroup label="— Databases">
                    <option value="2">Postgres 16</option>
                    <option value="3">MariaDB 11.5</option>
                </optgroup>
                <optgroup label="— Other">
                    <option value="203">Uptime Kuma</option>
                    <option value="196">Minio S3</option>
                    <option value="197">SFTP Storage</option>
                </optgroup>
            </select>
        </div>

        <div class="divider"></div>

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
        hidePostalCode: true,
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
        const serverName = document.getElementById('server-name').value.trim();
        const eggId = document.getElementById('egg-select').value;

        if (!serverName) {
            document.getElementById('card-errors').textContent = 'Please enter a server name.';
            return;
        }

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
                server_name: serverName,
                egg_id: eggId,
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
                    server_name: serverName,
                    egg_id: eggId,
                }),
            });

            window.location.href = '/order/confirmation';
        }
    });
</script>
</body>
</html>