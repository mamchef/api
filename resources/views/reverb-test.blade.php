<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laravel Reverb Test</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        .status {
            padding: 10px;
            margin: 10px 0;
            border-radius: 4px;
        }
        .connected {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .disconnected {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        button {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            margin: 5px;
        }
        button:hover {
            background-color: #0056b3;
        }
        #log {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            padding: 10px;
            height: 300px;
            overflow-y: auto;
            font-family: monospace;
            white-space: pre-wrap;
        }
    </style>
</head>
<body>
<h1>Laravel Reverb Test</h1>

<div id="connection-status" class="status disconnected">
    Connection Status: Disconnected
</div>

<div>
    <button onclick="initEcho()">Initialize Echo</button>
    <button onclick="testConnection()">Test Connection</button>
    <button onclick="subscribeToChannel()">Subscribe to Channel</button>
    <button onclick="clearLog()">Clear Log</button>
</div>

<div>
    <h3>Activity Log:</h3>
    <div id="log"></div>
</div>

<script src="https://cdn.jsdelivr.net/npm/pusher-js@8.4.0-rc2/dist/web/pusher.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.15.3/dist/echo.iife.js"></script>

<script>
    let echo;
    let isConnected = false;

    function log(message) {
        const logElement = document.getElementById('log');
        const timestamp = new Date().toLocaleTimeString();
        logElement.textContent += '[' + timestamp + '] ' + message + '\n';
        logElement.scrollTop = logElement.scrollHeight;
        console.log(message);
    }

    function updateConnectionStatus(connected) {
        isConnected = connected;
        const statusElement = document.getElementById('connection-status');
        if (connected) {
            statusElement.className = 'status connected';
            statusElement.textContent = 'Connection Status: Connected ✅';
        } else {
            statusElement.className = 'status disconnected';
            statusElement.textContent = 'Connection Status: Disconnected ❌';
        }
    }

    function initEcho() {
        try {
            log('Initializing Laravel Echo...');

            const config = {
                broadcaster: 'pusher',
                key: 'pymsis7zggc9gukyxj4w',
                wsHost: 'localhost',
                wsPort: 8080,
                wssPort: 8080,
                forceTLS: false,
                enabledTransports: ['ws', 'wss'],
                disableStats: true
            };

            log('Config: ' + JSON.stringify(config, null, 2));

            window.Echo = new Echo(config);
            echo = window.Echo;

            if (!echo) {
                log('❌ Failed to create Echo instance');
                return;
            }

            log('✅ Echo instance created');

            setTimeout(function() {
                if (echo && echo.connector && echo.connector.pusher) {
                    log('✅ Pusher connector ready');

                    echo.connector.pusher.connection.bind('connected', function() {
                        log('✅ Connected to Reverb server');
                        updateConnectionStatus(true);
                    });

                    echo.connector.pusher.connection.bind('disconnected', function() {
                        log('❌ Disconnected from Reverb server');
                        updateConnectionStatus(false);
                    });

                    echo.connector.pusher.connection.bind('error', function(error) {
                        log('❌ Connection error: ' + JSON.stringify(error));
                        updateConnectionStatus(false);
                    });

                    echo.connector.pusher.connection.bind('state_change', function(states) {
                        log('🔄 State: ' + states.previous + ' → ' + states.current);
                    });

                    log('Current state: ' + echo.connector.pusher.connection.state);
                } else {
                    log('❌ Echo connector not ready');
                }
            }, 1000);

        } catch (error) {
            log('❌ Error: ' + error.message);
            console.error(error);
        }
    }

    function testConnection() {
        if (!echo) {
            log('❌ Echo not initialized');
            return;
        }

        if (echo.connector && echo.connector.pusher) {
            const state = echo.connector.pusher.connection.state;
            log('Current connection state: ' + state);
            updateConnectionStatus(state === 'connected');
        } else {
            log('❌ No pusher connector found');
        }
    }

    function subscribeToChannel() {
        if (!echo) {
            log('❌ Echo not initialized');
            return;
        }

        try {
            log('📡 Subscribing to test-channel...');

            echo.channel('test-channel')
                .listen('TestEvent', function(e) {
                    log('📨 Event received: ' + JSON.stringify(e));
                });

            log('✅ Subscribed to test-channel');
        } catch (error) {
            log('❌ Subscribe error: ' + error.message);
        }
    }

    function clearLog() {
        document.getElementById('log').textContent = '';
    }

    // Auto-initialize when page loads
    document.addEventListener('DOMContentLoaded', function() {
        log('🚀 Page loaded');
        initEcho();
    });
</script>
</body>
</html>