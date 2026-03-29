<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoices — NovaPanel</title>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;700;800&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        :root {
            --navy: #060b18; --navy-2: #0c1428; --navy-3: #111d3a;
            --indigo: #4f5fe8; --indigo-light: #6b79f0;
            --text-primary: #f0f4ff; --text-secondary: #8a9cc4; --text-muted: #4a5a7a;
            --border: rgba(79,95,232,0.2); --green: #22c55e; --red: #f87171;
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
            max-width: 900px;
            margin: 0 auto;
            padding-top: 3rem;
        }
        .page-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 2rem;
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
        .invoice-table {
            width: 100%;
            border-collapse: collapse;
            background: var(--navy-2);
            border: 1px solid var(--border);
            border-radius: 16px;
            overflow: hidden;
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
        .badge {
            display: inline-block;
            padding: 0.2rem 0.6rem;
            border-radius: 100px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        .badge-paid { background: rgba(34,197,94,0.15); color: var(--green); }
        .badge-failed { background: rgba(248,113,113,0.15); color: var(--red); }
        .amount { font-family: 'Syne', sans-serif; font-weight: 700; color: var(--text-primary); }
        .empty {
            text-align: center;
            padding: 4rem 2rem;
            color: var(--text-muted);
        }
        .empty p { margin-top: 0.5rem; font-size: 0.9rem; }
    </style>
</head>
<body>
<div class="wrap">
    <div class="page-header">
        <h1>Invoices</h1>
        <a href="/panel" class="btn-back">← Back to dashboard</a>
    </div>

    @if($invoices->isEmpty())
        <div class="invoice-table">
            <div class="empty">
                <strong>No invoices yet</strong>
                <p>Your invoices will appear here after your first payment.</p>
            </div>
        </div>
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
                        <td class="amount">{{ $invoice->formattedAmount() }}</td>
                        <td><span class="badge badge-{{ $invoice->status }}">{{ ucfirst($invoice->status) }}</span></td>
                        <td>{{ $invoice->paid_at?->format('d M Y') ?? $invoice->created_at->format('d M Y') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
</body>
</html>