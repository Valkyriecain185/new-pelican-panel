<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NovaPanel — Game Server Hosting</title>
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
        }

        html { scroll-behavior: smooth; }

        body {
            font-family: 'DM Sans', sans-serif;
            background: var(--navy);
            color: var(--text-primary);
            overflow-x: hidden;
        }

        /* Grid background */
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

        .nav-links a:hover { color: var(--text-primary); }

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
        }

        .btn-primary:hover {
            background: var(--indigo-light);
            transform: translateY(-1px);
        }

        /* HERO */
        .hero {
            position: relative;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 8rem 6% 6rem;
            z-index: 1;
        }

        .hero-glow {
            position: absolute;
            top: 20%;
            left: 50%;
            transform: translateX(-50%);
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(79,95,232,0.12) 0%, transparent 70%);
            pointer-events: none;
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: var(--indigo-glow);
            border: 1px solid var(--border);
            border-radius: 100px;
            padding: 0.35rem 1rem;
            font-size: 0.8rem;
            color: var(--indigo-light);
            margin-bottom: 2rem;
            font-weight: 500;
        }

        .hero-badge-dot {
            width: 6px;
            height: 6px;
            background: var(--indigo-light);
            border-radius: 50%;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.4; }
        }

        .hero h1 {
            font-family: 'Syne', sans-serif;
            font-weight: 800;
            font-size: clamp(2.8rem, 7vw, 5.5rem);
            line-height: 1.05;
            letter-spacing: -0.04em;
            margin-bottom: 1.5rem;
            max-width: 900px;
        }

        .hero h1 .accent {
            color: var(--indigo-light);
        }

        .hero p {
            font-size: clamp(1rem, 2vw, 1.2rem);
            color: var(--text-secondary);
            max-width: 560px;
            margin: 0 auto 2.5rem;
            line-height: 1.7;
            font-weight: 300;
        }

        .hero-actions {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .btn-large {
            padding: 0.85rem 2rem;
            font-size: 1rem;
            border-radius: 10px;
        }

        .btn-outline {
            background: transparent;
            border: 1px solid var(--border);
            color: var(--text-secondary);
            text-decoration: none;
            font-family: 'DM Sans', sans-serif;
            font-size: 1rem;
            font-weight: 500;
            padding: 0.85rem 2rem;
            border-radius: 10px;
            transition: border-color 0.2s, color 0.2s, transform 0.15s;
        }

        .btn-outline:hover {
            border-color: var(--border-hover);
            color: var(--text-primary);
            transform: translateY(-1px);
        }

        .hero-stats {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 3rem;
            margin-top: 5rem;
            padding-top: 3rem;
            border-top: 1px solid var(--border);
            flex-wrap: wrap;
        }

        .stat-item {
            text-align: center;
        }

        .stat-number {
            font-family: 'Syne', sans-serif;
            font-size: 2rem;
            font-weight: 700;
            color: var(--text-primary);
        }

        .stat-label {
            font-size: 0.85rem;
            color: var(--text-muted);
            margin-top: 0.25rem;
        }

        /* FEATURES */
        section {
            position: relative;
            z-index: 1;
            padding: 6rem 6%;
        }

        .section-label {
            font-size: 0.8rem;
            font-weight: 600;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: var(--indigo-light);
            margin-bottom: 1rem;
        }

        .section-title {
            font-family: 'Syne', sans-serif;
            font-weight: 700;
            font-size: clamp(1.8rem, 4vw, 2.8rem);
            letter-spacing: -0.03em;
            line-height: 1.15;
            margin-bottom: 1rem;
        }

        .section-sub {
            color: var(--text-secondary);
            font-size: 1.05rem;
            max-width: 520px;
            line-height: 1.7;
            font-weight: 300;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5px;
            background: var(--border);
            border: 1px solid var(--border);
            border-radius: 16px;
            overflow: hidden;
            margin-top: 4rem;
        }

        .feature-card {
            background: var(--navy);
            padding: 2rem;
            transition: background 0.2s;
        }

        .feature-card:hover {
            background: var(--navy-2);
        }

        .feature-icon {
            width: 44px;
            height: 44px;
            background: var(--indigo-glow);
            border: 1px solid var(--border);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.25rem;
        }

        .feature-icon svg {
            width: 22px;
            height: 22px;
            stroke: var(--indigo-light);
            fill: none;
            stroke-width: 1.5;
            stroke-linecap: round;
            stroke-linejoin: round;
        }

        .feature-card h3 {
            font-family: 'Syne', sans-serif;
            font-weight: 600;
            font-size: 1.05rem;
            margin-bottom: 0.6rem;
        }

        .feature-card p {
            color: var(--text-secondary);
            font-size: 0.9rem;
            line-height: 1.65;
            font-weight: 300;
        }

        /* PRICING */
        .pricing-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem;
            margin-top: 4rem;
            max-width: 1100px;
            margin-left: auto;
            margin-right: auto;
        }

        .pricing-card {
            background: var(--navy-2);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 2rem;
            position: relative;
            transition: border-color 0.2s, transform 0.2s;
        }

        .pricing-card:hover {
            border-color: var(--border-hover);
            transform: translateY(-3px);
        }

        .pricing-card.featured {
            border-color: var(--indigo);
            background: var(--navy-3);
        }

        .pricing-badge {
            position: absolute;
            top: -13px;
            left: 50%;
            transform: translateX(-50%);
            background: var(--indigo);
            color: #fff;
            font-size: 0.75rem;
            font-weight: 600;
            padding: 0.25rem 1rem;
            border-radius: 100px;
            white-space: nowrap;
        }

        .pricing-tier {
            font-family: 'Syne', sans-serif;
            font-weight: 700;
            font-size: 1.1rem;
            margin-bottom: 0.5rem;
        }

        .pricing-desc {
            color: var(--text-muted);
            font-size: 0.85rem;
            margin-bottom: 1.5rem;
        }

        .pricing-price {
            display: flex;
            align-items: baseline;
            gap: 0.25rem;
            margin-bottom: 1.75rem;
        }

        .pricing-currency {
            font-size: 1.2rem;
            font-weight: 500;
            color: var(--text-secondary);
        }

        .pricing-amount {
            font-family: 'Syne', sans-serif;
            font-size: 3rem;
            font-weight: 800;
            line-height: 1;
        }

        .pricing-period {
            font-size: 0.9rem;
            color: var(--text-muted);
        }

        .pricing-divider {
            height: 1px;
            background: var(--border);
            margin-bottom: 1.5rem;
        }

        .pricing-features {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
            margin-bottom: 2rem;
        }

        .pricing-features li {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-size: 0.9rem;
            color: var(--text-secondary);
        }

        .pricing-features li::before {
            content: '';
            width: 16px;
            height: 16px;
            min-width: 16px;
            background: var(--indigo-glow);
            border: 1px solid var(--border);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background-image: url("data:image/svg+xml,%3Csvg width='10' height='10' viewBox='0 0 10 10' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M2 5L4 7L8 3' stroke='%236b79f0' stroke-width='1.5' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: center;
        }

        .btn-full {
            width: 100%;
            text-align: center;
            display: block;
        }

        /* GAMES */
        .games-section {
            text-align: center;
        }

        .games-grid {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 0.75rem;
            margin-top: 3rem;
        }

        .game-tag {
            background: var(--navy-2);
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 0.5rem 1.1rem;
            font-size: 0.875rem;
            color: var(--text-secondary);
            transition: border-color 0.2s, color 0.2s;
        }

        .game-tag:hover {
            border-color: var(--border-hover);
            color: var(--text-primary);
        }

        /* CTA BANNER */
        .cta-banner {
            background: var(--navy-3);
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 4rem;
            text-align: center;
            position: relative;
            overflow: hidden;
            margin: 0 6% 6rem;
            z-index: 1;
        }

        .cta-banner::before {
            content: '';
            position: absolute;
            top: -50%;
            left: 50%;
            transform: translateX(-50%);
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(79,95,232,0.1) 0%, transparent 70%);
            pointer-events: none;
        }

        .cta-banner h2 {
            font-family: 'Syne', sans-serif;
            font-weight: 800;
            font-size: clamp(1.8rem, 4vw, 2.8rem);
            letter-spacing: -0.03em;
            margin-bottom: 1rem;
        }

        .cta-banner p {
            color: var(--text-secondary);
            font-size: 1.05rem;
            margin-bottom: 2rem;
            font-weight: 300;
        }

        /* FOOTER */
        footer {
            border-top: 1px solid var(--border);
            padding: 3rem 6%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: gap;
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

        /* ANIMATIONS */
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .hero-content > * {
            animation: fadeUp 0.7s ease forwards;
        }

        .hero-content > *:nth-child(1) { animation-delay: 0.1s; opacity: 0; }
        .hero-content > *:nth-child(2) { animation-delay: 0.2s; opacity: 0; }
        .hero-content > *:nth-child(3) { animation-delay: 0.3s; opacity: 0; }
        .hero-content > *:nth-child(4) { animation-delay: 0.4s; opacity: 0; }

        @media (max-width: 768px) {
            .nav-links { display: none; }
            .hero-stats { gap: 2rem; }
            footer { flex-direction: column; text-align: center; }
            .cta-banner { padding: 2.5rem 1.5rem; }
        }
    </style>
</head>
<body>

<!-- NAV -->
<nav>
    <a href="/" class="nav-logo">Nova<span>Panel</span></a>
    <ul class="nav-links">
        <li><a href="#features">Features</a></li>
        <li><a href="#pricing">Pricing</a></li>
        <li><a href="#games">Games</a></li>
        <li><a href="#support">Support</a></li>
    </ul>
    <div class="nav-cta">
        <a href="/panel/login" class="btn-ghost">Sign in</a>
        <a href="/panel/register" class="btn-primary">Get started</a>
    </div>
</nav>

<!-- HERO -->
<section class="hero">
    <div class="hero-glow"></div>
    <div class="hero-content">
        <div class="hero-badge">
            <span class="hero-badge-dot"></span>
            Powered by Sentinel Development
        </div>
        <h1>Game servers.<br>
            <span class="accent">Zero friction.</span>
        </h1>
        <p>Deploy, manage, and scale your game servers in seconds. NovaPanel gives you enterprise-grade infrastructure without the enterprise-grade headache.</p>
        <div class="hero-actions">
            <a href="/panel/register" class="btn-primary btn-large">Deploy your first server</a>
            <a href="#pricing" class="btn-outline">View pricing</a>
        </div>
        <div class="hero-stats">
            <div class="stat-item">
                <div class="stat-number">99.9%</div>
                <div class="stat-label">Uptime SLA</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">&lt;30s</div>
                <div class="stat-label">Deploy time</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">50+</div>
                <div class="stat-label">Supported games</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">24/7</div>
                <div class="stat-label">Support</div>
            </div>
        </div>
    </div>
</section>

<!-- FEATURES -->
<section id="features">
    <div style="max-width: 600px;">
        <div class="section-label">Features</div>
        <h2 class="section-title">Everything you need to run game servers</h2>
        <p class="section-sub">Built on battle-tested infrastructure, designed for gamers and server admins who want things to just work.</p>
    </div>
    <div class="features-grid">
        <div class="feature-card">
            <div class="feature-icon">
                <svg viewBox="0 0 24 24"><path d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            </div>
            <h3>Instant deployment</h3>
            <p>Spin up a game server in under 30 seconds. Pre-configured images for every major game, ready to go.</p>
        </div>
        <div class="feature-card">
            <div class="feature-icon">
                <svg viewBox="0 0 24 24"><rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8M12 17v4"/></svg>
            </div>
            <h3>Intuitive control panel</h3>
            <p>Manage files, configure settings, view console output, and monitor resources — all from one clean dashboard.</p>
        </div>
        <div class="feature-card">
            <div class="feature-icon">
                <svg viewBox="0 0 24 24"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
            </div>
            <h3>DDoS protection</h3>
            <p>Enterprise-grade protection keeps your servers online and your players connected, even under attack.</p>
        </div>
        <div class="feature-card">
            <div class="feature-icon">
                <svg viewBox="0 0 24 24"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
            </div>
            <h3>Real-time monitoring</h3>
            <p>Live CPU, RAM, and network graphs so you always know how your server is performing.</p>
        </div>
        <div class="feature-card">
            <div class="feature-icon">
                <svg viewBox="0 0 24 24"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/></svg>
            </div>
            <h3>Automatic backups</h3>
            <p>Scheduled backups with one-click restore. Never lose your world or configs again.</p>
        </div>
        <div class="feature-card">
            <div class="feature-icon">
                <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
            </div>
            <h3>Custom domain routing</h3>
            <p>Route your own domain to your server using Cloudflare tunnel integration. No IP sharing required.</p>
        </div>
    </div>
</section>

<!-- PRICING -->
<section id="pricing">
    <div style="text-align: center; max-width: 600px; margin: 0 auto;">
        <div class="section-label">Pricing</div>
        <h2 class="section-title">Simple, transparent pricing</h2>
        <p class="section-sub" style="margin: 0 auto;">No hidden fees. No contracts. Cancel anytime.</p>
    </div>
    <div class="pricing-grid">
        <div class="pricing-card">
            <div class="pricing-tier">Starter</div>
            <div class="pricing-desc">Perfect for small communities</div>
            <div class="pricing-price">
                <span class="pricing-currency">£</span>
                <span class="pricing-amount">5</span>
                <span class="pricing-period">/mo</span>
            </div>
            <div class="pricing-divider"></div>
            <ul class="pricing-features">
                <li>2GB RAM</li>
                <li>2 vCPU cores</li>
                <li>20GB SSD storage</li>
                <li>1 server</li>
                <li>Daily backups</li>
                <li>Community support</li>
            </ul>
            <a href="/panel/register" class="btn-primary btn-full">Get started</a>
        </div>
        <div class="pricing-card featured">
            <div class="pricing-badge">Most popular</div>
            <div class="pricing-tier">Pro</div>
            <div class="pricing-desc">For serious server owners</div>
            <div class="pricing-price">
                <span class="pricing-currency">£</span>
                <span class="pricing-amount">15</span>
                <span class="pricing-period">/mo</span>
            </div>
            <div class="pricing-divider"></div>
            <ul class="pricing-features">
                <li>6GB RAM</li>
                <li>4 vCPU cores</li>
                <li>50GB SSD storage</li>
                <li>3 servers</li>
                <li>Hourly backups</li>
                <li>Custom domain routing</li>
                <li>Priority support</li>
            </ul>
            <a href="/panel/register" class="btn-primary btn-full">Get started</a>
        </div>
        <div class="pricing-card">
            <div class="pricing-tier">Business</div>
            <div class="pricing-desc">For hosting businesses</div>
            <div class="pricing-price">
                <span class="pricing-currency">£</span>
                <span class="pricing-amount">40</span>
                <span class="pricing-period">/mo</span>
            </div>
            <div class="pricing-divider"></div>
            <ul class="pricing-features">
                <li>16GB RAM</li>
                <li>8 vCPU cores</li>
                <li>200GB SSD storage</li>
                <li>Unlimited servers</li>
                <li>Continuous backups</li>
                <li>Custom domain routing</li>
                <li>Dedicated support</li>
                <li>SLA guarantee</li>
            </ul>
            <a href="/panel/register" class="btn-primary btn-full">Get started</a>
        </div>
    </div>
</section>

<!-- GAMES -->
<section id="games" class="games-section">
    <div class="section-label">Supported games</div>
    <h2 class="section-title">50+ games supported out of the box</h2>
    <div class="games-grid">
        <span class="game-tag">Minecraft</span>
        <span class="game-tag">Valheim</span>
        <span class="game-tag">DayZ</span>
        <span class="game-tag">ARK: Survival</span>
        <span class="game-tag">Rust</span>
        <span class="game-tag">Palworld</span>
        <span class="game-tag">7 Days to Die</span>
        <span class="game-tag">Project Zomboid</span>
        <span class="game-tag">Terraria</span>
        <span class="game-tag">Satisfactory</span>
        <span class="game-tag">Counter-Strike 2</span>
        <span class="game-tag">Enshrouded</span>
        <span class="game-tag">Sons of the Forest</span>
        <span class="game-tag">Factorio</span>
        <span class="game-tag">V Rising</span>
        <span class="game-tag">Team Fortress 2</span>
        <span class="game-tag">Left 4 Dead 2</span>
        <span class="game-tag">Arma 3</span>
        <span class="game-tag">Starbound</span>
        <span class="game-tag">+ many more</span>
    </div>
</section>

<!-- CTA -->
<div class="cta-banner">
    <h2>Ready to deploy?</h2>
    <p>Get your first server online in under 60 seconds.</p>
    <a href="/panel/register" class="btn-primary btn-large">Create your account</a>
</div>

<!-- FOOTER -->
<footer>
    <a href="/" class="footer-logo">Nova<span>Panel</span></a>
    <ul class="footer-links">
        <li><a href="#features">Features</a></li>
        <li><a href="#pricing">Pricing</a></li>
        <li><a href="/panel/login">Login</a></li>
        <li><a href="https://sentinel-development.co.uk">Sentinel Development</a></li>
    </ul>
    <p class="footer-copy">&copy; {{ date('Y') }} NovaPanel by Sentinel Development</p>
</footer>
<script>
    document.querySelectorAll('a[href="/panel/register"], a[href="/panel/login"]').forEach(function(link) {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            window.location.href = this.getAttribute('href');
        });
    });
</script>
</body>
</html>