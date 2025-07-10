<?php

namespace App\Http\Resources\V1\User\Notification;

use App\Http\Resources\V1\BaseResource;
use App\Models\Notification;

class NotificationsResource extends BaseResource
{
    public function prePareData($request): array
    {
        /** @var Notification $notification */
        $notification = $this->resource;
        return $notification->toArray();
    }
}