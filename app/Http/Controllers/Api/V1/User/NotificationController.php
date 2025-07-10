<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\SuccessResponse;
use App\Http\Resources\V1\User\Notification\NotificationsResource;
use App\Models\User;
use App\Services\Interfaces\NotificationsServiceInterface;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function __construct(protected NotificationsServiceInterface $service)
    {
    }

    public function index(): ResourceCollection
    {
        /** @var User $user */
        $user = Auth::user();
        $notifications = $this->service->getNotifications(
            target: $user,
        );
        return NotificationsResource::collection($notifications);
    }


    public function markAsRead(string $id): SuccessResponse
    {
        /** @var User $user */
        $user = Auth::user();

        $this->service->markAsRead(
            notificationId: $id,
            target: $user
        );
        return new SuccessResponse();
    }

    public function markAllAsRead(): SuccessResponse
    {
        /** @var User $user */
        $user = Auth::user();

        $this->service->markAllAsRead($user);

        return new SuccessResponse();
    }
}