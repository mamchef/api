<?php

namespace App\Http\Controllers\Api\V1\Chef;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\Chef\Notification\NotificationsResource;
use App\Http\Resources\V1\SuccessResponse;
use App\Models\Chef;
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
        /** @var Chef $chef */
        $chef = Auth::user();
        $notifications = $this->service->getNotifications(
            target: $chef,
        );
        return NotificationsResource::collection($notifications);
    }


    public function markAsRead(string $id): SuccessResponse
    {
        /** @var Chef $chef */
        $chef = Auth::user();

        $this->service->markAsRead(
            notificationId: $id,
            target: $chef
        );
        return new SuccessResponse();
    }

    public function markAllAsRead(): SuccessResponse
    {
        /** @var Chef $chef */
        $chef = Auth::user();

        $this->service->markAllAsRead($chef);

        return new SuccessResponse();
    }
}