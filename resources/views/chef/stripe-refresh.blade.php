<!DOCTYPE html>
<html lang="{{ $lang ?? 'en' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $lang === 'lt' ? 'Mokėjimų sąranka - MamChef' : 'Payment Setup - MamChef' }}</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);
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
            color: #ffc107;
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
            color: #ffc107;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">MamChef</div>
        <div class="icon">⚡</div>
        <h1>
            @if($lang === 'lt')
                Reikalinga mokėjimų sąranka
            @else
                Payment Setup Required
            @endif
        </h1>
        <p>
            @if($lang === 'lt')
                Prašome užbaigti Stripe mokėjimų sąranką, kad galėtumėte priimti mokėjimus iš užsakymų.
            @else
                Please complete your Stripe payment setup to start receiving payments from orders.
            @endif
        </p>
        <p>
            @if($lang === 'lt')
                Galite uždaryti šį langą ir patikrinti el. paštą dėl sąrankos nuorodos.
            @else
                You can close this window and check your email for the setup link.
            @endif
        </p>
    </div>
</body>
</html>