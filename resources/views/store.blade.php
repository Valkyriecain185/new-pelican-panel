<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Store — NovaPanel</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;500;600;700;800&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --navy: #060b18;
            --navy-2: #0c1428;
            --navy-3: #111d3a;
            --navy-4: #162348;
            --indigo: #4f5fe8;
            --indigo-light: #6b79f0;
            --indigo-dim: #2a2f6e;
            --indigo-glow: rgba(79,95,232,0.15);
            --text-primary: #f0f4ff;
            --text-secondary: #8a9cc4;
            --text-muted: #4a5a7a;
            --border: rgba(79,95,232,0.2);
            --border-hover: rgba(79,95,232,0.5);
            --green: #22c55e;
            --green-glow: rgba(34,197,94,0.15);
        }

        html { scroll-behavior: smooth; }

        body {
            font-family: 'DM Sans', sans-serif;
            background: var(--navy);
            color: var(--text-primary);
            overflow-x: hidden;
            min-height: 100vh;
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

        /* NAV */
        nav {
            position: fixed;
            top: 0; left: 0; right: 0;
            z-index: 100;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 6%;
            height: 68px;
            background: rgba(6,11,24,0.85);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--border);
        }

        .nav-logo {
            font-family: 'Syne', sans-serif;
            font-weight: 800;
            font-size: 1.4rem;
            color: var(--text-primary);
            text-decoration: none;
            letter-spacing: -0.03em;
        }

        .nav-logo span { color: var(--indigo-light); }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 2.5rem;
            list-style: none;
        }

        .nav-links a {
            color: var(--text-secondary);
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 500;
            transition: color 0.2s;
        }

        .nav-links a:hover, .nav-links a.active { color: var(--text-primary); }

        .nav-cta {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .btn-ghost {
            color: var(--text-secondary);
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 500;
            transition: color 0.2s;
        }

        .btn-ghost:hover { color: var(--text-primary); }

        .btn-primary {
            background: var(--indigo);
            color: #fff;
            text-decoration: none;
            font-family: 'DM Sans', sans-serif;
            font-size: 0.875rem;
            font-weight: 500;
            padding: 0.55rem 1.4rem;
            border-radius: 8px;
            transition: background 0.2s, transform 0.15s;
            border: none;
            cursor: pointer;
            display: inline-block;
        }

        .btn-primary:hover {
            background: var(--indigo-light);
            transform: translateY(-1px);
        }

        /* PAGE HEADER */
        .page-header {
            position: relative;
            z-index: 1;
            padding: 10rem 6% 4rem;
            text-align: center;
        }

        .page-header .section-label {
            font-size: 0.8rem;
            font-weight: 600;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: var(--indigo-light);
            margin-bottom: 1rem;
        }

        .page-header h1 {
            font-family: 'Syne', sans-serif;
            font-weight: 800;
            font-size: clamp(2rem, 5vw, 3.5rem);
            letter-spacing: -0.04em;
            margin-bottom: 1rem;
        }

        .page-header p {
            color: var(--text-secondary);
            font-size: 1.05rem;
            font-weight: 300;
            max-width: 500px;
            margin: 0 auto;
            line-height: 1.7;
        }

        /* BILLING TOGGLE */
        .billing-toggle {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 1rem;
            margin: 2.5rem 0;
            position: relative;
            z-index: 1;
        }

        .toggle-label {
            font-size: 0.9rem;
            color: var(--text-secondary);
            font-weight: 500;
        }

        .toggle-label.active { color: var(--text-primary); }

        .toggle-switch {
            width: 48px;
            height: 26px;
            background: var(--navy-3);
            border: 1px solid var(--border);
            border-radius: 100px;
            position: relative;
            cursor: pointer;
            transition: background 0.2s;
        }

        .toggle-switch.on { background: var(--indigo); border-color: var(--indigo); }

        .toggle-switch::after {
            content: '';
            position: absolute;
            top: 3px;
            left: 3px;
            width: 18px;
            height: 18px;
            background: #fff;
            border-radius: 50%;
            transition: transform 0.2s;
        }

        .toggle-switch.on::after { transform: translateX(22px); }

        .save-badge {
            background: var(--green-glow);
            border: 1px solid rgba(34,197,94,0.3);
            color: var(--green);
            font-size: 0.75rem;
            font-weight: 600;
            padding: 0.2rem 0.6rem;
            border-radius: 100px;
        }

        /* PLANS GRID */
        .plans-section {
            position: relative;
            z-index: 1;
            padding: 0 6% 6rem;
            max-width: 1400px;
            margin: 0 auto;
        }

        /* CATEGORY TABS */
        .category-tabs {
            display: flex;
            gap: 0.5rem;
            margin-bottom: 3rem;
            flex-wrap: wrap;
            justify-content: center;
        }

        .tab-btn {
            background: transparent;
            border: 1px solid var(--border);
            color: var(--text-secondary);
            font-family: 'DM Sans', sans-serif;
            font-size: 0.875rem;
            font-weight: 500;
            padding: 0.5rem 1.25rem;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s;
        }

        .tab-btn:hover {
            border-color: var(--border-hover);
            color: var(--text-primary);
        }

        .tab-btn.active {
            background: var(--indigo);
            border-color: var(--indigo);
            color: #fff;
        }

        /* PLANS */
        .plans-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 1.5rem;
            justify-items: center;
        }

        .plan-card {
            background: var(--navy-2);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 1.75rem;
            position: relative;
            transition: border-color 0.2s, transform 0.2s;
            display: flex;
            flex-direction: column;
            width: 100%;
            max-width: 360px;
        }

        .plan-card:hover {
            border-color: var(--border-hover);
            transform: translateY(-3px);
        }

        .plan-card.featured {
            border-color: var(--indigo);
            background: var(--navy-3);
        }

        .plan-popular {
            position: absolute;
            top: -13px;
            left: 50%;
            transform: translateX(-50%);
            background: var(--indigo);
            color: #fff;
            font-size: 0.72rem;
            font-weight: 600;
            padding: 0.22rem 0.9rem;
            border-radius: 100px;
            white-space: nowrap;
        }

        .plan-header {
            margin-bottom: 1.25rem;
        }

        .plan-name {
            font-family: 'Syne', sans-serif;
            font-weight: 700;
            font-size: 1.15rem;
            margin-bottom: 0.3rem;
        }

        .plan-desc {
            color: var(--text-muted);
            font-size: 0.85rem;
        }

        .plan-price {
            display: flex;
            align-items: baseline;
            gap: 0.2rem;
            margin-bottom: 1.5rem;
        }

        .plan-currency {
            font-size: 1.1rem;
            color: var(--text-secondary);
            font-weight: 500;
        }

        .plan-amount {
            font-family: 'Syne', sans-serif;
            font-size: 2.8rem;
            font-weight: 800;
            line-height: 1;
        }

        .plan-period {
            font-size: 0.875rem;
            color: var(--text-muted);
        }

        .plan-original {
            font-size: 0.8rem;
            color: var(--text-muted);
            text-decoration: line-through;
            margin-left: 0.25rem;
        }

        .plan-divider {
            height: 1px;
            background: var(--border);
            margin-bottom: 1.25rem;
        }

        .plan-specs {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.75rem;
            margin-bottom: 1.25rem;
        }

        .spec-item {
            display: flex;
            flex-direction: column;
            gap: 0.15rem;
        }

        .spec-value {
            font-family: 'Syne', sans-serif;
            font-weight: 700;
            font-size: 1rem;
            color: var(--text-primary);
        }

        .spec-label {
            font-size: 0.75rem;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .plan-features {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 0.6rem;
            margin-bottom: 1.5rem;
            flex: 1;
        }

        .plan-features li {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            font-size: 0.875rem;
            color: var(--text-secondary);
        }

        .plan-features li::before {
            content: '';
            width: 15px;
            height: 15px;
            min-width: 15px;
            background: var(--indigo-glow);
            border: 1px solid var(--border);
            border-radius: 50%;
            background-image: url("data:image/svg+xml,%3Csvg width='9' height='9' viewBox='0 0 9 9' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M1.5 4.5L3.5 6.5L7.5 2.5' stroke='%236b79f0' stroke-width='1.5' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: center;
        }

        .btn-buy {
            width: 100%;
            text-align: center;
            padding: 0.8rem;
            font-size: 0.95rem;
            border-radius: 10px;
            font-weight: 500;
        }

        .btn-outline-full {
            width: 100%;
            text-align: center;
            padding: 0.8rem;
            font-size: 0.95rem;
            border-radius: 10px;
            font-weight: 500;
            background: transparent;
            border: 1px solid var(--border);
            color: var(--text-secondary);
            text-decoration: none;
            font-family: 'DM Sans', sans-serif;
            cursor: pointer;
            transition: border-color 0.2s, color 0.2s;
            display: block;
        }

        .btn-outline-full:hover {
            border-color: var(--border-hover);
            color: var(--text-primary);
        }

        /* ADDONS */
        .addons-section {
            position: relative;
            z-index: 1;
            padding: 0 6% 6rem;
            max-width: 1400px;
            margin: 0 auto;
        }

        .addons-header {
            margin-bottom: 2rem;
        }

        .addons-header h2 {
            font-family: 'Syne', sans-serif;
            font-weight: 700;
            font-size: 1.6rem;
            letter-spacing: -0.03em;
            margin-bottom: 0.4rem;
        }

        .addons-header p {
            color: var(--text-secondary);
            font-size: 0.9rem;
            font-weight: 300;
        }

        .addons-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
            gap: 1rem;
        }

        .addon-card {
            background: var(--navy-2);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 1.25rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            transition: border-color 0.2s;
        }

        .addon-card:hover { border-color: var(--border-hover); }

        .addon-info h4 {
            font-family: 'Syne', sans-serif;
            font-weight: 600;
            font-size: 0.95rem;
            margin-bottom: 0.2rem;
        }

        .addon-info p {
            color: var(--text-muted);
            font-size: 0.8rem;
        }

        .addon-price {
            font-family: 'Syne', sans-serif;
            font-weight: 700;
            font-size: 1rem;
            color: var(--indigo-light);
            white-space: nowrap;
        }

        /* FAQ */
        .faq-section {
            position: relative;
            z-index: 1;
            padding: 0 6% 6rem;
            max-width: 800px;
            margin: 0 auto;
        }

        .faq-header {
            text-align: center;
            margin-bottom: 2.5rem;
        }

        .faq-header h2 {
            font-family: 'Syne', sans-serif;
            font-weight: 700;
            font-size: 1.8rem;
            letter-spacing: -0.03em;
        }

        .faq-item {
            border: 1px solid var(--border);
            border-radius: 12px;
            margin-bottom: 0.75rem;
            overflow: hidden;
        }

        .faq-question {
            width: 100%;
            background: var(--navy-2);
            border: none;
            padding: 1.1rem 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            cursor: pointer;
            font-family: 'DM Sans', sans-serif;
            font-size: 0.95rem;
            font-weight: 500;
            color: var(--text-primary);
            text-align: left;
            transition: background 0.2s;
        }

        .faq-question:hover { background: var(--navy-3); }

        .faq-icon {
            width: 20px;
            height: 20px;
            min-width: 20px;
            border: 1px solid var(--border);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: transform 0.2s;
            font-size: 0.9rem;
            color: var(--indigo-light);
        }

        .faq-item.open .faq-icon { transform: rotate(45deg); }

        .faq-answer {
            display: none;
            padding: 1rem 1.5rem 1.25rem;
            background: var(--navy-2);
            color: var(--text-secondary);
            font-size: 0.9rem;
            line-height: 1.7;
            font-weight: 300;
            border-top: 1px solid var(--border);
        }

        .faq-item.open .faq-answer { display: block; }

        /* FOOTER */
        footer {
            border-top: 1px solid var(--border);
            padding: 3rem 6%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 1rem;
            z-index: 1;
            position: relative;
        }

        .footer-logo {
            font-family: 'Syne', sans-serif;
            font-weight: 800;
            font-size: 1.1rem;
            color: var(--text-primary);
            text-decoration: none;
        }

        .footer-logo span { color: var(--indigo-light); }

        .footer-links {
            display: flex;
            gap: 2rem;
            list-style: none;
        }

        .footer-links a {
            color: var(--text-muted);
            text-decoration: none;
            font-size: 0.875rem;
            transition: color 0.2s;
        }

        .footer-links a:hover { color: var(--text-secondary); }

        .footer-copy {
            color: var(--text-muted);
            font-size: 0.8rem;
        }

        @media (max-width: 768px) {
            .nav-links { display: none; }
            .plans-grid { grid-template-columns: 1fr; }
            footer { flex-direction: column; text-align: center; }
        }
    </style>
</head>
<body>

<!-- NAV -->
<nav>
    <a href="/" class="nav-logo">Nova<span>Panel</span></a>
    <ul class="nav-links">
        <li><a href="/">Home</a></li>
        <li><a href="/store" class="active">Store</a></li>
        <li><a href="/#features">Features</a></li>
        <li><a href="#faq">FAQ</a></li>
    </ul>
    <div class="nav-cta">
        @auth
            <a href="/panel" class="btn-ghost">Dashboard</a>
        @else
            <a href="/panel/login" class="btn-ghost">Sign in</a>
            <a href="/panel/register" class="btn-primary">Get started</a>
        @endauth
    </div>
</nav>

<!-- PAGE HEADER -->
<div class="page-header">
    <div class="section-label">Store</div>
    <h1>Choose your plan</h1>
    <p>All plans include DDoS protection, daily backups, and 24/7 support. No hidden fees.</p>
</div>

<!-- BILLING TOGGLE -->
<div class="billing-toggle">
    <span class="toggle-label active" id="monthly-label">Monthly</span>
    <div class="toggle-switch" id="billing-toggle" onclick="toggleBilling()"></div>
    <span class="toggle-label" id="annual-label">Annual <span class="save-badge">Save 20%</span></span>
</div>

<!-- PLANS -->
<div class="plans-section">
    <div class="category-tabs">
        <button class="tab-btn active" onclick="switchCategory('game', this)">Game Servers</button>
        <button class="tab-btn" onclick="switchCategory('web', this)">Web Hosting</button>
        <button class="tab-btn" onclick="switchCategory('discord', this)">Discord Bots</button>
        <button class="tab-btn" onclick="switchCategory('vps', this)">VPS</button>
    </div>

    <!-- GAME SERVERS -->
    <div class="plans-grid" id="category-game">
        <div class="plan-card">
            <div class="plan-header">
                <div class="plan-name">Starter</div>
                <div class="plan-desc">Small communities & friends</div>
            </div>
            <div class="plan-price">
                <span class="plan-currency">£</span>
                <span class="plan-amount" data-monthly="5" data-annual="4">5</span>
                <span class="plan-period">/mo</span>
            </div>
            <div class="plan-divider"></div>
            <div class="plan-specs">
                <div class="spec-item">
                    <span class="spec-value">2GB</span>
                    <span class="spec-label">RAM</span>
                </div>
                <div class="spec-item">
                    <span class="spec-value">2 vCPU</span>
                    <span class="spec-label">Cores</span>
                </div>
                <div class="spec-item">
                    <span class="spec-value">20GB</span>
                    <span class="spec-label">Storage</span>
                </div>
                <div class="spec-item">
                    <span class="spec-value">1</span>
                    <span class="spec-label">Server</span>
                </div>
            </div>
            <ul class="plan-features">
                <li>Daily backups</li>
                <li>DDoS protection</li>
                <li>Community support</li>
                <li>Custom subdomain</li>
            </ul>
            <a href="{{ auth()->check() ? '/checkout?plan=starter' : '/panel/register?plan=starter' }}" class="btn-primary btn-buy">Get started</a>
        </div>

        <div class="plan-card featured">
            <div class="plan-popular">Most popular</div>
            <div class="plan-header">
                <div class="plan-name">Pro</div>
                <div class="plan-desc">Serious server owners</div>
            </div>
            <div class="plan-price">
                <span class="plan-currency">£</span>
                <span class="plan-amount" data-monthly="15" data-annual="12">15</span>
                <span class="plan-period">/mo</span>
            </div>
            <div class="plan-divider"></div>
            <div class="plan-specs">
                <div class="spec-item">
                    <span class="spec-value">6GB</span>
                    <span class="spec-label">RAM</span>
                </div>
                <div class="spec-item">
                    <span class="spec-value">4 vCPU</span>
                    <span class="spec-label">Cores</span>
                </div>
                <div class="spec-item">
                    <span class="spec-value">50GB</span>
                    <span class="spec-label">Storage</span>
                </div>
                <div class="spec-item">
                    <span class="spec-value">3</span>
                    <span class="spec-label">Servers</span>
                </div>
            </div>
            <ul class="plan-features">
                <li>Hourly backups</li>
                <li>DDoS protection</li>
                <li>Priority support</li>
                <li>Custom domain routing</li>
                <li>Plugin auto-installer</li>
            </ul>
            <a href="{{ auth()->check() ? '/checkout?plan=pro' : '/panel/register?plan=pro' }}" class="btn-primary btn-buy">Get started</a>
        </div>

        <div class="plan-card">
            <div class="plan-header">
                <div class="plan-name">Business</div>
                <div class="plan-desc">Hosting businesses & networks</div>
            </div>
            <div class="plan-price">
                <span class="plan-currency">£</span>
                <span class="plan-amount" data-monthly="40" data-annual="32">40</span>
                <span class="plan-period">/mo</span>
            </div>
            <div class="plan-divider"></div>
            <div class="plan-specs">
                <div class="spec-item">
                    <span class="spec-value">16GB</span>
                    <span class="spec-label">RAM</span>
                </div>
                <div class="spec-item">
                    <span class="spec-value">8 vCPU</span>
                    <span class="spec-label">Cores</span>
                </div>
                <div class="spec-item">
                    <span class="spec-value">200GB</span>
                    <span class="spec-label">Storage</span>
                </div>
                <div class="spec-item">
                    <span class="spec-value">∞</span>
                    <span class="spec-label">Servers</span>
                </div>
            </div>
            <ul class="plan-features">
                <li>Continuous backups</li>
                <li>DDoS protection</li>
                <li>Dedicated support</li>
                <li>Custom domain routing</li>
                <li>SLA guarantee</li>
                <li>White-label option</li>
            </ul>
            <a href="{{ auth()->check() ? '/checkout?plan=business' : '/panel/register?plan=business' }}" class="btn-primary btn-buy">Get started</a>
        </div>

        <div class="plan-card">
            <div class="plan-header">
                <div class="plan-name">Enterprise</div>
                <div class="plan-desc">Custom solutions for large operators</div>
            </div>
            <div class="plan-price">
                <span class="plan-amount" style="font-size: 2rem;">Custom</span>
            </div>
            <div class="plan-divider"></div>
            <ul class="plan-features" style="margin-top: 0.5rem;">
                <li>Custom RAM & CPU allocation</li>
                <li>Dedicated hardware options</li>
                <li>Custom SLA</li>
                <li>Dedicated account manager</li>
                <li>Priority infrastructure</li>
                <li>Custom billing terms</li>
            </ul>
            <a href="mailto:support@sentinel-development.co.uk" class="btn-outline-full">Contact us</a>
        </div>
    </div>

    <!-- WEB HOSTING -->
    <div class="plans-grid" id="category-web" style="display:none;">
        <div class="plan-card">
            <div class="plan-header">
                <div class="plan-name">Web Starter</div>
                <div class="plan-desc">Personal sites & portfolios</div>
            </div>
            <div class="plan-price">
                <span class="plan-currency">£</span>
                <span class="plan-amount" data-monthly="3" data-annual="2">3</span>
                <span class="plan-period">/mo</span>
            </div>
            <div class="plan-divider"></div>
            <ul class="plan-features">
                <li>1GB storage</li>
                <li>Free SSL certificate</li>
                <li>Custom domain support</li>
                <li>Cloudflare tunnel routing</li>
            </ul>
            <a href="{{ auth()->check() ? '/checkout?plan=web-starter' : '/panel/register?plan=web-starter' }}" class="btn-primary btn-buy">Get started</a>
        </div>
        <div class="plan-card featured">
            <div class="plan-popular">Most popular</div>
            <div class="plan-header">
                <div class="plan-name">Web Pro</div>
                <div class="plan-desc">Small businesses & projects</div>
            </div>
            <div class="plan-price">
                <span class="plan-currency">£</span>
                <span class="plan-amount" data-monthly="8" data-annual="6">8</span>
                <span class="plan-period">/mo</span>
            </div>
            <div class="plan-divider"></div>
            <ul class="plan-features">
                <li>10GB storage</li>
                <li>Free SSL certificate</li>
                <li>Custom domain support</li>
                <li>Cloudflare tunnel routing</li>
                <li>Daily backups</li>
                <li>Priority support</li>
            </ul>
            <a href="{{ auth()->check() ? '/checkout?plan=web-pro' : '/panel/register?plan=web-pro' }}" class="btn-primary btn-buy">Get started</a>
        </div>
    </div>

    <!-- DISCORD BOTS -->
    <div class="plans-grid" id="category-discord" style="display:none;">
        <div class="plan-card">
            <div class="plan-header">
                <div class="plan-name">Bot Starter</div>
                <div class="plan-desc">Small Discord communities</div>
            </div>
            <div class="plan-price">
                <span class="plan-currency">£</span>
                <span class="plan-amount" data-monthly="2" data-annual="1.60">2</span>
                <span class="plan-period">/mo</span>
            </div>
            <div class="plan-divider"></div>
            <ul class="plan-features">
                <li>512MB RAM</li>
                <li>1 vCPU</li>
                <li>5GB storage</li>
                <li>Always-on hosting</li>
            </ul>
            <a href="{{ auth()->check() ? '/checkout?plan=bot-starter' : '/panel/register?plan=bot-starter' }}" class="btn-primary btn-buy">Get started</a>
        </div>
        <div class="plan-card featured">
            <div class="plan-popular">Most popular</div>
            <div class="plan-header">
                <div class="plan-name">Bot Pro</div>
                <div class="plan-desc">Large servers & multiple bots</div>
            </div>
            <div class="plan-price">
                <span class="plan-currency">£</span>
                <span class="plan-amount" data-monthly="6" data-annual="5">6</span>
                <span class="plan-period">/mo</span>
            </div>
            <div class="plan-divider"></div>
            <ul class="plan-features">
                <li>2GB RAM</li>
                <li>2 vCPU</li>
                <li>20GB storage</li>
                <li>Always-on hosting</li>
                <li>Multiple bot support</li>
            </ul>
            <a href="{{ auth()->check() ? '/checkout?plan=bot-pro' : '/panel/register?plan=bot-pro' }}" class="btn-primary btn-buy">Get started</a>
        </div>
    </div>

    <!-- VPS -->
    <div class="plans-grid" id="category-vps" style="display:none;">
        <div class="plan-card">
            <div class="plan-header">
                <div class="plan-name">VPS Nano</div>
                <div class="plan-desc">Dev environments & testing</div>
            </div>
            <div class="plan-price">
                <span class="plan-currency">£</span>
                <span class="plan-amount" data-monthly="10" data-annual="8">10</span>
                <span class="plan-period">/mo</span>
            </div>
            <div class="plan-divider"></div>
            <div class="plan-specs">
                <div class="spec-item">
                    <span class="spec-value">2GB</span>
                    <span class="spec-label">RAM</span>
                </div>
                <div class="spec-item">
                    <span class="spec-value">1 vCPU</span>
                    <span class="spec-label">Core</span>
                </div>
                <div class="spec-item">
                    <span class="spec-value">40GB</span>
                    <span class="spec-label">SSD</span>
                </div>
                <div class="spec-item">
                    <span class="spec-value">1TB</span>
                    <span class="spec-label">Bandwidth</span>
                </div>
            </div>
            <ul class="plan-features">
                <li>Full root access</li>
                <li>Ubuntu / Debian / CentOS</li>
                <li>Cloudflare tunnel support</li>
            </ul>
            <a href="{{ auth()->check() ? '/checkout?plan=vps-nano' : '/panel/register?plan=vps-nano' }}" class="btn-primary btn-buy">Get started</a>
        </div>
        <div class="plan-card featured">
            <div class="plan-popular">Most popular</div>
            <div class="plan-header">
                <div class="plan-name">VPS Standard</div>
                <div class="plan-desc">Production workloads</div>
            </div>
            <div class="plan-price">
                <span class="plan-currency">£</span>
                <span class="plan-amount" data-monthly="25" data-annual="20">25</span>
                <span class="plan-period">/mo</span>
            </div>
            <div class="plan-divider"></div>
            <div class="plan-specs">
                <div class="spec-item">
                    <span class="spec-value">8GB</span>
                    <span class="spec-label">RAM</span>
                </div>
                <div class="spec-item">
                    <span class="spec-value">4 vCPU</span>
                    <span class="spec-label">Cores</span>
                </div>
                <div class="spec-item">
                    <span class="spec-value">160GB</span>
                    <span class="spec-label">SSD</span>
                </div>
                <div class="spec-item">
                    <span class="spec-value">5TB</span>
                    <span class="spec-label">Bandwidth</span>
                </div>
            </div>
            <ul class="plan-features">
                <li>Full root access</li>
                <li>Ubuntu / Debian / CentOS</li>
                <li>Cloudflare tunnel support</li>
                <li>Daily backups</li>
                <li>Priority support</li>
            </ul>
            <a href="{{ auth()->check() ? '/checkout?plan=vps-standard' : '/panel/register?plan=vps-standard' }}" class="btn-primary btn-buy">Get started</a>
        </div>
    </div>
</div>

<!-- ADDONS -->
<div class="addons-section">
    <div class="addons-header">
        <h2>Add-ons</h2>
        <p>Bolt extras onto any plan</p>
    </div>
    <div class="addons-grid">
        <div class="addon-card">
            <div class="addon-info">
                <h4>Extra backup slot</h4>
                <p>+5 additional backup slots</p>
            </div>
            <div class="addon-price">£1/mo</div>
        </div>
        <div class="addon-card">
            <div class="addon-info">
                <h4>Extra RAM</h4>
                <p>Add 2GB RAM to any server</p>
            </div>
            <div class="addon-price">£3/mo</div>
        </div>
        <div class="addon-card">
            <div class="addon-info">
                <h4>Extra storage</h4>
                <p>Add 20GB SSD storage</p>
            </div>
            <div class="addon-price">£2/mo</div>
        </div>
        <div class="addon-card">
            <div class="addon-info">
                <h4>Custom domain</h4>
                <p>Route your own domain via Cloudflare</p>
            </div>
            <div class="addon-price">£2/mo</div>
        </div>
        <div class="addon-card">
            <div class="addon-info">
                <h4>Dedicated IP</h4>
                <p>Unique IP address for your server</p>
            </div>
            <div class="addon-price">£3/mo</div>
        </div>
        <div class="addon-card">
            <div class="addon-info">
                <h4>Priority support</h4>
                <p>Guaranteed 1hr response time</p>
            </div>
            <div class="addon-price">£5/mo</div>
        </div>
    </div>
</div>

<!-- FAQ -->
<div class="faq-section" id="faq">
    <div class="faq-header">
        <h2>Frequently asked questions</h2>
    </div>
    <div class="faq-item">
        <button class="faq-question" onclick="toggleFaq(this)">
            Can I upgrade or downgrade my plan?
            <span class="faq-icon">+</span>
        </button>
        <div class="faq-answer">Yes, you can upgrade or downgrade at any time. Changes take effect immediately and billing is prorated.</div>
    </div>
    <div class="faq-item">
        <button class="faq-question" onclick="toggleFaq(this)">
            What games are supported?
            <span class="faq-icon">+</span>
        </button>
        <div class="faq-answer">We support 50+ games including Minecraft, DayZ, Rust, ARK, Valheim, Palworld, Project Zomboid, and many more. If your game isn't listed, contact us and we'll get it set up.</div>
    </div>
    <div class="faq-item">
        <button class="faq-question" onclick="toggleFaq(this)">
            How quickly is my server deployed?
            <span class="faq-icon">+</span>
        </button>
        <div class="faq-answer">Servers are deployed automatically within 30 seconds of payment confirmation. You'll receive an email with your login details instantly.</div>
    </div>
    <div class="faq-item">
        <button class="faq-question" onclick="toggleFaq(this)">
            Do you offer refunds?
            <span class="faq-icon">+</span>
        </button>
        <div class="faq-answer">We offer a 48-hour money-back guarantee on all new plans. After that, refunds are handled on a case-by-case basis.</div>
    </div>
    <div class="faq-item">
        <button class="faq-question" onclick="toggleFaq(this)">
            Can I use my own domain?
            <span class="faq-icon">+</span>
        </button>
        <div class="faq-answer">Yes, on Pro plans and above you can route your own domain to your server using our Cloudflare tunnel integration. No IP sharing required.</div>
    </div>
    <div class="faq-item">
        <button class="faq-question" onclick="toggleFaq(this)">
            What payment methods do you accept?
            <span class="faq-icon">+</span>
        </button>
        <div class="faq-answer">We accept all major credit and debit cards via Stripe. All payments are processed securely and we never store your card details.</div>
    </div>
</div>

<!-- FOOTER -->
<footer>
    <a href="/" class="footer-logo">Nova<span>Panel</span></a>
    <ul class="footer-links">
        <li><a href="/">Home</a></li>
        <li><a href="/store">Store</a></li>
        <li><a href="/panel/login">Login</a></li>
        <li><a href="https://sentinel-development.co.uk">Sentinel Development</a></li>
    </ul>
    <p class="footer-copy">&copy; {{ date('Y') }} NovaPanel by Sentinel Development</p>
</footer>

<script>
    let isAnnual = false;

    function toggleBilling() {
        isAnnual = !isAnnual;
        const toggle = document.getElementById('billing-toggle');
        const monthlyLabel = document.getElementById('monthly-label');
        const annualLabel = document.getElementById('annual-label');

        toggle.classList.toggle('on', isAnnual);
        monthlyLabel.classList.toggle('active', !isAnnual);
        annualLabel.classList.toggle('active', isAnnual);

        document.querySelectorAll('.plan-amount[data-monthly]').forEach(el => {
            el.textContent = isAnnual ? el.dataset.annual : el.dataset.monthly;
        });
    }

    function switchCategory(category, btn) {
        document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');

        document.querySelectorAll('[id^="category-"]').forEach(el => el.style.display = 'none');
        document.getElementById('category-' + category).style.display = 'grid';
    }

    function toggleFaq(btn) {
        const item = btn.parentElement;
        item.classList.toggle('open');
    }
</script>

</body>
</html>