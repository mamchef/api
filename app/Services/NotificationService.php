<?php

namespace App\Services;

use App\Models\Chef;
use App\Models\Notification;
use App\Models\User;
use App\Notifications\BaseNotification;
use App\Services\Interfaces\NotificationsServiceInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Notification as NotificationFacade;

class NotificationService implements NotificationsServiceInterface
{
    public function send(User|Chef $notifiable, BaseNotification $notification): void
    {
        NotificationFacade::send($notifiable, $notification);
    }

    /**
     * Send notification with delay
     */
    public function sendLater($notifiable, BaseNotification $notification, $delay): void
    {
        NotificationFacade::sendNow($notifiable, $notification->delay($delay));
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(string $notificationId, User|Chef $target): bool
    {
        $notification = Notification::query()->where('id', $notificationId)
            ->where('notifiable_type', $target->getMorphClass())
            ->where('notifiable_id', $target->getKey())
            ->first();

        if ($notification) {
            $notification->markAsRead();
            return true;
        }

        return false;
    }

    /**
     * Mark all notifications as read for a user
     */
    public function markAllAsRead(User|Chef $target): int
    {
        return Notification::query()->where('notifiable_type', $target->getMorphClass())
            ->where('notifiable_id', $target->getKey())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }

    /**
     * Get paginated notifications for user
     */
    public function getNotifications(User|Chef $target, int $perPage = 10, array $types = []): LengthAwarePaginator
    {
        $query = Notification::query()->where('notifiable_type', $target->getMorphClass())
            ->where('notifiable_id', $target->getKey());

        if (!empty($types)) {
            $query->whereIn('type', $types);
        }

        return $query->latest()->paginate($perPage);
    }

    /**
     * Get unread notification count
     */
    public function getUnreadCount(User|Chef $target): int
    {
        return Notification::query()->where('notifiable_type', $target->getMorphClass())
            ->where('notifiable_id', $target->getKey())
            ->whereNull('read_at')
            ->count();
    }

}