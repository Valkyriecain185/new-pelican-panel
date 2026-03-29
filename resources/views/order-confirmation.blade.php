<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Order Confirmed — NovaPanel</title>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'DM Sans', sans-serif;
            background: #060b18;
            color: #f0f4ff;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }
        .card {
            background: #0c1428;
            border: 1px solid rgba(79,95,232,0.2);
            border-radius: 20px;
            padding: 3rem;
            max-width: 480px;
            width: 100%;
            text-align: center;
        }
        .icon { font-size: 3rem; margin-bottom: 1.5rem; }
        h1 {
            font-family: 'Syne', sans-serif;
            font-weight: 800;
            font-size: 1.8rem;
            margin-bottom: 0.75rem;
        }
        p { color: #8a9cc4; font-size: 0.95rem; line-height: 1.7; margin-bottom: 2rem; }
        a {
            display: inline-block;
            background: #4f5fe8;
            color: #fff;
            text-decoration: none;
            padding: 0.8rem 2rem;
            border-radius: 10px;
            font-weight: 500;
            transition: background 0.2s;
        }
        a:hover { background: #6b79f0; }
    </style>
</head>
<body>
    <div class="card">
        <div class="icon">🎉</div>
        <h1>You're all set!</h1>
        <p>Your order is confirmed and your server is being provisioned. You'll receive a confirmation email shortly with your login details.</p>
        <a href="/panel">Go to dashboard</a>
    </div>
</body>
</html>