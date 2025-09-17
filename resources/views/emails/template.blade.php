<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subject ?? 'MamChef' }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            line-height: 1.6;
            color: #333333;
            background-color: #f8f9fa;
        }

        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .header {
            background: #16392e;
            padding: 30px 20px;
            text-align: center;
        }

        .logo {
            max-width: 200px;
            height: auto;
            margin-bottom: 10px;
            filter: brightness(0) invert(1);
        }

        .header-title {
            color: #ffffff;
            font-size: 24px;
            font-weight: 600;
            margin: 0;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
        }

        .content {
            padding: 40px 30px;
        }

        .greeting {
            font-size: 18px;
            color: #2c3e50;
            margin-bottom: 20px;
            font-weight: 500;
        }

        .message {
            font-size: 16px;
            line-height: 1.7;
            color: #555555;
            margin-bottom: 25px;
        }

        .button {
            display: inline-block;
            background: linear-gradient(135deg, #ff6b6b 0%, #ee5a24 100%);
            color: #ffffff !important;
            text-decoration: none;
            padding: 15px 30px;
            border-radius: 6px;
            font-weight: 600;
            font-size: 16px;
            text-align: center;
            margin: 20px 0;
            box-shadow: 0 3px 10px rgba(238, 90, 36, 0.3);
            transition: all 0.3s ease;
        }

        .button:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(238, 90, 36, 0.4);
        }

        .footer {
            background-color: #f8f9fa;
            padding: 30px 20px;
            text-align: center;
            border-top: 1px solid #e9ecef;
        }

        .footer-content {
            font-size: 14px;
            color: #6c757d;
            line-height: 1.5;
        }

        .footer-links {
            margin-top: 15px;
        }

        .footer-links a {
            color: #ff6b6b;
            text-decoration: none;
            margin: 0 10px;
            font-weight: 500;
        }

        .footer-links a:hover {
            text-decoration: underline;
        }

        .divider {
            height: 1px;
            background: linear-gradient(90deg, transparent 0%, #dee2e6 50%, transparent 100%);
            margin: 25px 0;
        }

        .highlight {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 4px;
            padding: 15px;
            margin: 20px 0;
            border-left: 4px solid #ff6b6b;
        }

        .success {
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
        }

        .warning {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            color: #856404;
        }

        .info {
            background-color: #d1ecf1;
            border: 1px solid #bee5eb;
            color: #0c5460;
        }

        .social-links {
            margin-top: 20px;
        }

        .social-links a {
            display: inline-block;
            margin: 0 5px;
            opacity: 0.7;
            transition: opacity 0.3s ease;
        }

        .social-links a:hover {
            opacity: 1;
        }

        @media (max-width: 600px) {
            .email-container {
                margin: 0;
                border-radius: 0;
            }

            .content {
                padding: 30px 20px;
            }

            .header {
                padding: 25px 15px;
            }

            .logo {
                max-width: 150px;
            }

            .header-title {
                font-size: 20px;
            }

            .greeting {
                font-size: 16px;
            }

            .message {
                font-size: 15px;
            }

            .button {
                padding: 12px 25px;
                font-size: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header with Logo -->
        <div class="header">
            <img src="https://app.mamchef.com/logo-white.png" alt="MamChef Logo" class="logo">
            @if(isset($header_title))
                <h1 class="header-title">{{ $header_title }}</h1>
            @endif
        </div>

        <!-- Main Content -->
        <div class="content">
            @if(isset($greeting))
                <div class="greeting">{{ $greeting }}</div>
            @endif

            @if(isset($body))
                <div class="message">{!! $body !!}</div>
            @endif

            @if(isset($highlight_message))
                <div class="highlight {{ $highlight_type ?? '' }}">
                    {!! $highlight_message !!}
                </div>
            @endif

            @if(isset($button_text) && isset($button_url))
                <div style="text-align: center;">
                    <a href="{{ $button_url }}" class="button">{{ $button_text }}</a>
                </div>
            @endif

            @if(isset($additional_content))
                <div class="divider"></div>
                <div class="message">{!! $additional_content !!}</div>
            @endif
        </div>

        <!-- Footer -->
        <div class="footer">
            @if(isset($footer))
                <div class="divider"></div>
                <div class="message">{!! $footer !!}</div>
            @endif
        </div>
    </div>
</body>
</html>