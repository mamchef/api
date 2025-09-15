<?php

return [
    // Auth Services
    'auth' => [
        'admin_invalid_credentials' => 'Password or email is incorrect',
        'user_invalid_credentials' => 'Password or phone number is incorrect',
        'user_invalid_phone' => 'Phone number is incorrect',
        'chef_invalid_credentials' => 'Password or email is incorrect',
    ],

    // Profile Services
    'profile' => [
        'same_password' => 'New password must be different from current password',
        'current_password_not_match' => 'Current password is incorrect',
    ],

    // Order Services
    'order' => [
        'insufficient_credit' => 'Insufficient credit to pay.',
        'payment_service_error' => 'Payment service error',
        'order_already_processed' => 'Order already processed',
        'pickup_not_supported' => 'Chef store does not support pickup',
        'cannot_rate_order' => 'You cannot rate for this order',
        'already_rated' => 'You have already rated this order',
        'invalid_order_status' => 'Invalid order status for this operation',
        'delivery_not_supported' => 'Chef store does not support delivery',
        'order_not_found' => 'Order not found',
        'unauthorized_access' => 'You are not authorized to access this order',
    ],

    // Food Services
    'food' => [
        'max_tags_exceeded' => 'Maximum tag is 3',
        'food_not_found' => 'Food not found',
        'invalid_food_status' => 'Invalid food status',
    ],

    // Rate Limit Services
    'rate_limit' => [
        'too_many_attempts' => 'Too many attempts. Available in :seconds seconds',
        'exceeded_limit' => 'Rate limit exceeded',
    ],

    // Payment Services
    'payment' => [
        'insufficient_funds' => 'Insufficient funds',
        'payment_failed' => 'Payment failed',
        'invalid_payment_method' => 'Invalid payment method',
        'payment_already_processed' => 'Payment already processed',
    ],

    // General
    'general' => [
        'operation_denied' => 'Operation denied',
        'invalid_request' => 'Invalid request',
        'server_error' => 'Internal server error occurred',
        'not_found' => 'Resource not found',
        'unauthorized' => 'Unauthorized access',
        'forbidden' => 'Access forbidden',
    ],
];