<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $lang === 'lt' ? 'Mokėjimas nepavyko - Mamchef' : 'Payment Failed - Mamchef' }}</title>
    
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
                        'rust': { 
                            50: '#fef6f4', 
                            100: '#fee8e3',
                            200: '#fccfc5',
                            500: '#ef5a3a',
                            600: '#b93d22'
                        },
                        'danger': { 
                            DEFAULT: '#b93d22', 
                            'light': '#ef5a3a',
                            'dark': '#9a311b'
                        },
                        'accent': { 
                            DEFAULT: '#eba743', 
                            'dark': '#d18a28' 
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

        /* Error animation */
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }

        .error-icon {
            animation: shake 0.6s ease-out;
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

        /* Pulsing retry button */
        @keyframes pulse {
            0%, 100% { box-shadow: 0 0 0 0 rgba(21, 57, 47, 0.4); }
            50% { box-shadow: 0 0 0 10px rgba(21, 57, 47, 0); }
        }

        .btn-primary:hover {
            animation: pulse 2s infinite;
        }

        /* Responsive improvements */
        @media (max-width: 480px) {
            .container-mobile {
                padding: 1rem;
            }
            
            .grid-cols-2 {
                grid-template-columns: 1fr;
                gap: 0.75rem;
            }
        }
    </style>
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen bg-gradient-to-br from-rust-50 to-danger-100 flex flex-col justify-center px-6 py-8">
        <!-- Header -->
        <header class="absolute top-0 left-0 right-0 px-6 py-6">
            <div class="text-center">
                <img src="/logo.png" alt="Mamchef Logo" class="w-24 h-24 object-contain mx-auto" />
            </div>
        </header>

        <!-- Main Content -->
        <div class="max-w-md mx-auto w-full">
            <!-- Failed Card -->
            <div class="glass rounded-2xl p-8 shadow-xl border border-white/20 text-center">
                <!-- Error Icon -->
                <div class="w-20 h-20 bg-danger-light/20 rounded-full flex items-center justify-center mx-auto mb-6">
                    <div class="w-12 h-12 bg-danger rounded-full flex items-center justify-center error-icon">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </div>
                </div>

                <!-- Error Message -->
                <h1 class="text-2xl font-bold text-forest-900 mb-3">
                    {{ $lang === 'lt' ? 'Mokėjimas nepavyko' : 'Payment Failed' }}
                </h1>
                <p class="text-forest-600 mb-2">
                    {{ $lang === 'lt' ? 'Negalėjome apdoroti jūsų mokėjimo' : 'We couldn\'t process your payment' }}
                </p>
                
                <!-- Error Details -->
                <div class="bg-rust-50 border border-rust-200 rounded-lg p-4 mb-6">
                    <div class="text-sm text-forest-700">
                        <p class="mb-2"><span class="font-semibold">{{ $lang === 'lt' ? 'Priežastis:' : 'Reason:' }}</span> <span id="error-reason">{{ $error_message ?? ($lang === 'lt' ? 'Mokėjimas atmestas banko' : 'Payment declined by bank') }}</span></p>
                        <p><span class="font-semibold">{{ $lang === 'lt' ? 'Nuoroda:' : 'Reference:' }}</span> <span id="error-reference">{{ strtoupper(substr($session_id ?? 'unknown', -8)) }}</span></p>
                    </div>
                </div>

                <!-- Common Solutions -->
               {{-- <div class="text-left mb-6 bg-sage-50 rounded-lg p-4">
                    <h3 class="font-semibold text-forest-800 mb-3">💡 What you can try:</h3>
                    <ul class="text-sm text-forest-700 space-y-2">
                        <li>• Check your card details and try again</li>
                        <li>• Ensure you have sufficient funds</li>
                        <li>• Try a different payment method</li>
                        <li>• Contact your bank if the issue persists</li>
                    </ul>
                </div>--}}

                <!-- Action Buttons -->
                <div class="space-y-3">
                    <!-- Primary Action: Retry Payment -->
                    <button
                        onclick="retryPayment()"
                        class="btn btn-primary w-full bg-gradient-to-r from-primary-600 to-primary-700 text-white py-4 px-6 rounded-xl font-bold text-lg hover:from-primary-700 hover:to-primary-800 shadow-lg"
                    >
                        <span id="retry-button-text">{{ $lang === 'lt' ? '🔄 Bandyti dar kartą' : '🔄 Try Again' }}</span>
                    </button>

                    <!-- Secondary Actions -->
                   {{-- <div class="grid grid-cols-2 gap-3">
                        <button
                            onclick="goToCart()"
                            class="btn bg-white/80 hover:bg-white text-forest-700 hover:text-forest-900 py-3 px-4 rounded-xl font-semibold border border-forest-200 hover:border-forest-300"
                        >
                            Back to Cart
                        </button>
                        
                        <button
                            onclick="contactSupport()"
                            class="btn bg-white/80 hover:bg-white text-forest-700 hover:text-forest-900 py-3 px-4 rounded-xl font-semibold border border-forest-200 hover:border-forest-300"
                        >
                            Get Help
                        </button>
                    </div>
--}}
                    <!-- Alternative: Continue without Payment -->
           {{--         <button
                        onclick="continueWithoutPayment()"
                        class="btn w-full bg-amber-50 hover:bg-amber-100 text-amber-800 py-3 px-4 rounded-xl font-semibold border border-amber-200 hover:border-amber-300"
                    >
                        💰 Pay Later / Cash on Delivery
                    </button>--}}
                </div>

                <!-- Help Text -->
               {{-- <div class="mt-6 pt-6 border-t border-sage-200">
                    <p class="text-xs text-sage-600 leading-relaxed">
                        Having trouble? Our support team is here to help. 
                        <button onclick="contactSupport()" class="text-accent-dark hover:text-accent underline font-medium">Contact us</button>
                        and we'll resolve this quickly.
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
        // Payment Failed Functions
        function initPaymentFailed() {
            // Laravel provides the error details via Blade variables
            detectDevice();
        }

        function detectDevice() {
            const userAgent = navigator.userAgent;
            const isAndroid = /Android/i.test(userAgent);
            const isIOS = /iPhone|iPad|iPod/i.test(userAgent);
            const isMobile = isAndroid || isIOS;
            const lang = '{{ $lang }}';
            
            const debugInfo = document.getElementById('debug-info');
            const retryButton = document.getElementById('retry-button-text');
            
            if (isMobile) {
                retryButton.textContent = lang === 'lt' ? '📱 Bandyti programėlėje' : '📱 Retry in App';
                debugInfo.textContent = lang === 'lt' ? 
                    `${isAndroid ? 'Android' : 'iOS'} įrenginys aptiktas` : 
                    `${isAndroid ? 'Android' : 'iOS'} device detected`;
            } else {
                retryButton.textContent = lang === 'lt' ? '🔄 Bandyti dar kartą' : '🔄 Try Again';
                debugInfo.textContent = lang === 'lt' ? 'Stalinio kompiuterio naršyklė aptikta' : 'Desktop browser detected';
            }
        }

        function retryPayment() {
            const userAgent = navigator.userAgent;
            const isAndroid = /Android/i.test(userAgent);
            const isIOS = /iPhone|iPad|iPod/i.test(userAgent);
            const cartId = '{{ $cart_id ?? "retry" }}';
            
            if (isAndroid) {
                // Android Intent to cart/checkout
                const intent = `intent://cart?retry=true&cart_id=${cartId}#Intent;scheme=mamchef;package=com.mamchef.app;S.browser_fallback_url=${encodeURIComponent('https://app.mamchef.com/cart?retry=true')};end`;
                window.location.href = intent;
                
                // Fallback after delay
                setTimeout(() => {
                    if (!document.hidden) {
                        window.location.href = `https://app.mamchef.com/cart?retry=true&cart_id=${cartId}`;
                    }
                }, 2500);
                
            } else if (isIOS) {
                // iOS Universal Links first
                window.location.href = `https://app.mamchef.com/cart?retry=true&cart_id=${cartId}`;
                
                // Then try custom scheme
                setTimeout(() => {
                    if (!document.hidden) {
                        window.location.href = `mamchef://cart?retry=true&cart_id=${cartId}`;
                    }
                }, 1000);
                
                // Final fallback
                setTimeout(() => {
                    if (!document.hidden) {
                        window.location.href = `https://app.mamchef.com/cart?retry=true`;
                    }
                }, 3000);
                
            } else {
                // Desktop - go to cart on website
                window.location.href = `https://app.mamchef.com/cart?retry=true&cart_id=${cartId}`;
            }
        }

        function goToCart() {
            const cartId = '{{ $cart_id ?? "" }}';
            
            // Try app first, then web
            if (isMobileDevice()) {
                window.location.href = `mamchef://cart?cart_id=${cartId}`;
                setTimeout(() => {
                    if (!document.hidden) {
                        window.location.href = `https://app.mamchef.com/cart`;
                    }
                }, 2000);
            } else {
                window.location.href = `https://app.mamchef.com/cart`;
            }
        }

        function contactSupport() {
            // Try app support first, then web
            if (isMobileDevice()) {
                window.location.href = 'mamchef://support?issue=payment_failed';
                setTimeout(() => {
                    if (!document.hidden) {
                        window.location.href = 'https://app.mamchef.com/support?issue=payment_failed';
                    }
                }, 2000);
            } else {
                window.location.href = 'https://app.mamchef.com/support?issue=payment_failed';
            }
        }

        function continueWithoutPayment() {
            const cartId = '{{ $cart_id ?? "" }}';
            
            // Redirect to cash/pay-later flow
            if (isMobileDevice()) {
                window.location.href = `mamchef://checkout?payment_method=cash&cart_id=${cartId}`;
                setTimeout(() => {
                    if (!document.hidden) {
                        window.location.href = `https://app.mamchef.com/checkout?payment_method=cash&cart_id=${cartId}`;
                    }
                }, 2000);
            } else {
                window.location.href = `https://app.mamchef.com/checkout?payment_method=cash&cart_id=${cartId}`;
            }
        }

        function isMobileDevice() {
            return /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
        }

        // Initialize when page loads
        document.addEventListener('DOMContentLoaded', initPaymentFailed);
    </script>
</body>
</html>