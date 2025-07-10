<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BroadcastAuthController extends Controller
{
    public function authenticate(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['error' => 'Unauthenticated'], 403);
        }

        $channelName = $request->input('channel_name');
        $socketId = $request->input('socket_id');

        // Manual authorization check
        $channel = str_replace('private-', '', $channelName);

        // Check if user can access this channel
        if ($channel === 'chef-' . $user->id || $channel === 'chef-' . $user->uuid) {
            // Generate auth signature manually
            $stringToSign = $socketId . ':' . $channelName;
            $signature = hash_hmac(
                'sha256',
                $stringToSign,
                config('broadcasting.connections.reverb.secret')
            );

            return response()->json([
                'auth' => config('broadcasting.connections.reverb.key') . ':' . $signature
            ]);
        }

        return response()->json(['error' => 'Forbidden'], 403);
    }


    public function userAuthenticate(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['error' => 'Unauthenticated'], 403);
        }

        $channelName = $request->input('channel_name');
        $socketId = $request->input('socket_id');

        // Manual authorization check
        $channel = str_replace('private-', '', $channelName);

        // Check if user can access this channel
        if ($channel === 'user-' . $user->id || $channel === 'user-' . $user->uuid) {
            // Generate auth signature manually
            $stringToSign = $socketId . ':' . $channelName;
            $signature = hash_hmac(
                'sha256',
                $stringToSign,
                config('broadcasting.connections.reverb.secret')
            );

            return response()->json([
                'auth' => config('broadcasting.connections.reverb.key') . ':' . $signature
            ]);
        }

        return response()->json(['error' => 'Forbidden'], 403);
    }
}