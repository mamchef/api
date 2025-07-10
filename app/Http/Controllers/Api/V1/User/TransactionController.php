<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\User\Transaction\CreditResource;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    public function credit(): CreditResource
    {
        $user = Auth::user();
        return new CreditResource($user);
    }
}