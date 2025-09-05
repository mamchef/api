<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $lang === 'lt' ? 'Mokėjimas sėkmingas - Mamchef' : 'Payment Successful - Mamchef' }}</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Custom Tailwind Config -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'primary': { DEFAULT: '#15392f', 500: '#15392f', 600: '#3b5e4f', 700: '#2f4b3f' },
                        'forest': { 
                            50: '#f2f7f5', 
                            100: '#e0ebe7',
                            200: '#c3d7d0',
                            600: '#3b5e4f', 
                            700: '#2f4b3f', 
                            800: '#15392f', 
                            900: '#0f2921' 
                        },
                        'sage': { 
                            50: '#f3f8f6', 
                            100: '#e2ede9', 
                            200: '#c6dbd4', 
                            500: '#527c70', 
                            600: '#42645a' 
                        },
                        'mint': { 
                            50: '#f5faf8', 
                            100: '#e8f3ee', 
                            200: '#c2e1d7' 
                        },
                        'success': { 
                            DEFAULT: '#527c70', 
                            'light': '#c2e1d7',
                            'dark': '#42645a'
                        }
                    }
                }
            }
        }
    </script>
    
    <style>
        /* Enhanced glassmorphism effect */
        .glass {
            background-color: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
        }

        /* Success animation */
        @keyframes checkmark {
            0% { transform: scale(0); }
            50% { transform: scale(1.2); }
            100% { transform: scale(1); }
        }

        .success-icon {
            animation: checkmark 0.6s ease-out;
        }

        /* Button hover animations */
        .btn {
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .btn:hover {
            transform: scale(1.02);
        }

        .btn:active {
            transform: scale(0.98);
        }

        /* Responsive improvements */
        @media (max-width: 480px) {
            .container-mobile {
                padding: 1rem;
            }
        }
    </style>
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen  flex flex-col justify-center px-6">
        <!-- Header -->
        <header class=" top-0 left-0 right-0 px-6 " style="z-index: 10">
            <div class="text-center">
                <img src="/logo.png" alt="Mamchef Logo" class="w-32 h-32 object-contain mx-auto" />
            </div>
        </header>

        <!-- Main Content -->
        <div class="max-w-md mx-auto w-full">
            <!-- Success Card -->
            <div class="glass rounded-2xl p-8 shadow-xl border border-white/20 text-center">
                <!-- Success Icon -->
                <div class="w-20 h-20 bg-success-light/20 rounded-full flex items-center justify-center mx-auto mb-6">
                    <div class="w-12 h-12 bg-success rounded-full flex items-center justify-center success-icon">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                </div>

                <!-- Success Message -->
                <h1 class="text-2xl font-bold text-forest-900 mb-3">
                    {{ $lang === 'lt' ? 'Mokėjimas sėkmingas!' : 'Payment Successful!' }}
                </h1>
                <p class="text-forest-600 mb-2">
                    {{ $lang === 'lt' ? 'Jūsų užsakymas patvirtintas' : 'Your order has been confirmed' }}
                </p>
                
                <!-- Order Details -->
                <div class="bg-mint-100 rounded-lg p-4 mb-6">
                    <div class="text-sm text-forest-700">
                        <p><span class="font-semibold">{{ $lang === 'lt' ? 'Užsakymo numeris:' : 'Order Number:' }}</span> <span id="order-number">{{ $order->order_number ?? ($lang === 'lt' ? 'Kraunama...' : 'Loading...') }}</span></p>
                       {{-- <p><span class="font-semibold">{{ $lang === 'lt' ? 'Suma:' : 'Amount:' }}</span> <span id="order-amount">€{{ number_format($order->total ?? 0, 2) }}</span></p>--}}
                        <p><span class="font-semibold">{{ $lang === 'lt' ? 'Būsena:' : 'Status:' }}</span> <span class="text-success font-semibold">{{ $lang === 'lt' ? 'Patvirtinta' : 'Confirmed' }}</span></p>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="space-y-3">

                    <!-- Primary Action: Smart App Redirect -->
                    <button
                        onclick="smartRedirect()"
                        class="btn w-full bg-gradient-to-r from-primary-600 to-primary-700 text-white py-4 px-6 rounded-xl font-bold text-lg  shadow-lg"
                    >
                        <span id="primary-button-text">{{ $lang === 'lt' ? 'Tęsti programėlėje' : 'Continue to App' }}</span>
                    </button>

                    <!-- Secondary Actions -->
              {{--      <div class="grid grid-cols-2 gap-3">
                        <button
                            onclick="viewOrder()"
                            class="btn bg-white/80 hover:bg-white text-forest-700 hover:text-forest-900 py-3 px-4 rounded-xl font-semibold border border-forest-200 hover:border-forest-300"
                        >
                            View Order
                        </button>

                        <button
                            onclick="continueInBrowser()"
                            class="btn bg-white/80 hover:bg-white text-forest-700 hover:text-forest-900 py-3 px-4 rounded-xl font-semibold border border-forest-200 hover:border-forest-300"
                        >
                            Continue in Browser
                        </button>
                    </div>--}}
                </div>

                <!-- Additional Info -->
               {{-- <div class="mt-6 pt-6 border-t border-sage-200">
                    <p class="text-xs text-sage-600 leading-relaxed">
                        You will receive a confirmation email shortly. 
                        Your order is being prepared and you'll be notified when it's ready for delivery.
                    </p>
                </div>--}}
            </div>

            <!-- App Detection Status -->
            <div class="mt-4 text-center">
                <p class="text-xs text-sage-500" id="debug-info">{{ $lang === 'lt' ? 'Aptinkamas įrenginys...' : 'Detecting device...' }}</p>
            </div>
        </div>
    </div>

    <script>
        // Smart Redirect Functions
        function initPaymentSuccess() {
            const orderId = '{{ $order->order_number ?? "unknown" }}';
            const amount = '{{ $order->total ?? 0 }}';
            
            // Detect device and update button text
            detectDevice();
            
            // Auto-redirect after 3 seconds if mobile
            if (isMobileDevice()) {
                setTimeout(() => {
                    smartRedirect();
                }, 3000);
            }
        }

        function detectDevice() {
            const userAgent = navigator.userAgent;
            const isAndroid = /Android/i.test(userAgent);
            const isIOS = /iPhone|iPad|iPod/i.test(userAgent);
            const isMobile = isAndroid || isIOS;
            const lang = '{{ $lang }}';
            
            const debugInfo = document.getElementById('debug-info');
            const buttonText = document.getElementById('primary-button-text');
            
            if (isMobile) {
                buttonText.textContent = lang === 'lt' ? '📱 Atidaryti Mamchef programėlę' : '📱 Open Mamchef App';
                debugInfo.textContent = lang === 'lt' ? 
                    `${isAndroid ? 'Android' : 'iOS'} įrenginys aptiktas` : 
                    `${isAndroid ? 'Android' : 'iOS'} device detected`;
            } else {
                buttonText.textContent = lang === 'lt' ? '🌐 Tęsti svetainėje' : '🌐 Continue to Website';
                debugInfo.textContent = lang === 'lt' ? 'Stalinio kompiuterio naršyklė aptikta' : 'Desktop browser detected';
            }
        }

        function smartRedirect() {
            const userAgent = navigator.userAgent;
            const isAndroid = /Android/i.test(userAgent);
            const isIOS = /iPhone|iPad|iPod/i.test(userAgent);
            const orderId = '{{ $order->order_number ?? "unknown" }}';
            
            if (isAndroid) {
                // Android Intent approach
                const intent = `intent://payment/success?order_id=${orderId}#Intent;scheme=mamchef;package=com.mamchef.app;S.browser_fallback_url=${encodeURIComponent('https://app.mamchef.com/orders')};end`;
                window.location.href = intent;
                
                // Fallback after delay
                setTimeout(() => {
                    if (!document.hidden) {
                        window.location.href = `https://app.mamchef.com/orders`;
                    }
                }, 2500);
                
            } else if (isIOS) {
                // iOS Universal Links first
                window.location.href = `https://app.mamchef.com/payment/success?order_id=${orderId}`;
                
                // Then try custom scheme
                setTimeout(() => {
                    if (!document.hidden) {
                        window.location.href = `mamchef://payment/success?order_id=${orderId}`;
                    }
                }, 1000);
                
                // Final fallback
                setTimeout(() => {
                    if (!document.hidden) {
                        window.location.href = `https://app.mamchef.com/orders/${orderId}`;
                    }
                }, 3000);
                
            } else {
                // Desktop - go to website
                window.location.href = `https://app.mamchef.com/orders`;
            }
        }

        function viewOrder() {
            const orderId = '{{ $order->id ?? "latest" }}';
            
            // Try app first, then web
            if (isMobileDevice()) {
                window.location.href = `mamchef://orders/${orderId}`;
                setTimeout(() => {
                    if (!document.hidden) {
                        window.location.href = `https://app.mamchef.com/orders/${orderId}`;
                    }
                }, 2000);
            } else {
                window.location.href = `https://app.mamchef.com/orders/${orderId}`;
            }
        }

        function continueInBrowser() {
            window.location.href = 'https://app.mamchef.com';
        }

        function isMobileDevice() {
            return /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
        }

        // Initialize when page loads
        document.addEventListener('DOMContentLoaded', initPaymentSuccess);
    </script>
</body>
</html>