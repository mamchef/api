<!DOCTYPE html>
<html lang="{{ $lang ?? 'en' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $lang === 'lt' ? 'Mokėjimų sąranka - MamChef' : 'Payment Setup - MamChef' }}</title>
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
            font-size: 3rem;
            margin-bottom: 1rem;
            color: #28a745;
        }
        h1 {
            color: #333;
            margin-bottom: 1rem;
            font-size: 1.5rem;
        }
        p {
            color: #666;
            line-height: 1.6;
            margin-bottom: 2rem;
        }
        .logo {
            margin-bottom: 2rem;
            font-size: 1.5rem;
            font-weight: bold;
            color: #28a745;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">MamChef</div>
        <div class="icon">✅</div>
        <h1>
            @if($lang === 'lt')
                Mokėjimų sąranka sėkminga!
            @else
                Payment Setup Successful!
            @endif
        </h1>
        <p>
            @if($lang === 'lt')
                Jūsų Stripe mokėjimų sąranka buvo sėkmingai užbaigta. Dabar galite priimti mokėjimus iš užsakymų.
            @else
                Your Stripe payment setup has been completed successfully. You can now receive payments from orders.
            @endif
        </p>
        <p>
            @if($lang === 'lt')
                Galite uždaryti šį langą ir grįžti į savo virtuvės valdymo panelį.
            @else
                You can close this window and return to your kitchen dashboard.
            @endif
        </p>
    </div>
</body>
</html>