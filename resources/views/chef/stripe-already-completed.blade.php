<!DOCTYPE html>
<html lang="{{ $lang ?? 'en' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $lang === 'lt' ? 'Mokėjimų sąranka baigta - MamChef' : 'Payment Setup Complete - MamChef' }}</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            margin: 0;
            padding: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .container {
            background: white;
            padding: 3rem;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            text-align: center;
            max-width: 400px;
            width: 90%;
        }
        .icon {
            font-size: 4rem;
            margin-bottom: 1rem;
        }
        h1 {
            color: #333;
            margin-bottom: 1rem;
            font-size: 1.5rem;
        }
        p {
            color: #666;
            line-height: 1.6;
            margin-bottom: 1.5rem;
        }
        .logo {
            margin-bottom: 2rem;
            font-size: 1.5rem;
            font-weight: bold;
            color: #28a745;
        }
        .success-badge {
            display: inline-block;
            background: #d4edda;
            color: #155724;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 600;
            margin-top: 1rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">MamChef</div>
        <div class="icon">✅</div>
        <h1>
            @if($lang === 'lt')
                Mokėjimų sąranka baigta!
            @else
                Payment Setup Complete!
            @endif
        </h1>
        <p>
            @if($lang === 'lt')
                Jūsų Stripe sąskaita jau aktyvuota ir visiškai paruošta priimti mokėjimus.
            @else
                Your Stripe account is already activated and fully ready to receive payments.
            @endif
        </p>
        <p>
            @if($lang === 'lt')
                Galite uždaryti šį langą. Jūsų sąskaita veikia ir jūs galite pradėti priimti užsakymus!
            @else
                You can close this window. Your account is active and you can start receiving orders!
            @endif
        </p>
        <div class="success-badge">
            @if($lang === 'lt')
                ✓ Patvirtinta
            @else
                ✓ Verified
            @endif
        </div>
    </div>
</body>
</html>
