<?php

use App\Http\Controllers\BroadcastAuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Route;

require __DIR__.'/api/chef.php';
require __DIR__.'/api/user.php';
require __DIR__.'/api/admin.php';
require __DIR__.'/api/public.php';


Route::post('/broadcasting/auth', [BroadcastAuthController::class, 'authenticate'])
    ->middleware('chef-auth');

Route::post('/user/broadcasting/auth', [BroadcastAuthController::class, 'userAuthenticate'])
    ->middleware('user-auth');


Route::get('app-version',function(Request $request){
    return  new \App\Http\Resources\V1\AppVersionResponse();
});



Route::get('chef-version',function(Request $request){
    return  new \App\Http\Resources\V1\ChefVersionResponse();
});