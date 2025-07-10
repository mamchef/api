<?php

use App\Models\Chef;
use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int)$user->id === (int)$id;
});


Broadcast::channel('chef-{id}', function ($chef, $id) {
    \Log::info('Chef channel auth check', [
        'chef' => $chef,
        'chef_id' => $chef ? $chef->id : 'null',
        'chef_class' => $chef ? get_class($chef) : 'null',
        'requested_id' => $id,
        'id_type' => gettype($id),
        'chef_id_type' => $chef ? gettype($chef->id) : 'null',
        'comparison' => $chef ? ($chef->id == $id) : false,
        'uuid_comparison' => $chef ? ($chef->uuid == $id) : false,
    ]);

    $result = $chef instanceof Chef && ($chef->id == $id || $chef->uuid == $id);

    \Log::info('Chef channel auth result', ['result' => $result]);

    return $result;
});


Broadcast::channel('user-{id}', function ($user, $id) {
    \Log::info('User channel auth check', [
        'chef' => $user,
        'chef_id' => $user ? $user->id : 'null',
        'chef_class' => $user ? get_class($user) : 'null',
        'requested_id' => $id,
        'id_type' => gettype($id),
        'chef_id_type' => $user ? gettype($user->id) : 'null',
        'comparison' => $user ? ($user->id == $id) : false,
        'uuid_comparison' => $user ? ($user->uuid == $id) : false,
    ]);

    $result = $user instanceof User && ($user->id == $id || $user->uuid == $id);

    \Log::info('User channel auth result', ['result' => $result]);

    return $result;
});