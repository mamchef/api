<?php

namespace App\Services\Interfaces;

use App\Models\Chef;
use App\Models\User;
use App\Notifications\BaseNotification;
use Illuminate\Pagination\LengthAwarePaginator;

interface NotificationsServiceInterface
{
    public function send(User|Chef $notifiable, BaseNotification $notification): void;

    public function sendLater($notifiable, BaseNotification $notification, $delay): void;


    public function markAsRead(string $notificationId, User|Chef $target): bool;


    public function markAllAsRead(User|Chef $target): int;


    public function getNotifications(User|Chef $target, int $perPage = 10, array $types = []): LengthAwarePaginator;

    public function getUnreadCount(User|Chef $target): int;
}