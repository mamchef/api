<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @property int $id
 * @property string $tokenable_type
 * @property int $tokenable_id
 * @property string $token
 * @property string $device_type
 * @property bool $is_active
 * @property Carbon $last_used_at
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class FcmToken extends Model
{
    protected $guarded = [];

    protected $casts = [
        'is_active' => 'boolean',
        'last_used_at' => 'datetime',
    ];


    public function tokenable(): MorphTo
    {
        return $this->morphTo();
    }

    public function markAsUsed(): void
    {
        $this->update(['last_used_at' => now()]);
    }

    public function deactivate()
    {
        $this->update(['is_active' => false]);
    }

    public static function saveToken(Chef|User $model, string $token, array $deviceInfo = []): self
    {
        return self::updateOrCreate(
            [
                'tokenable_type' => $model->getMorphClass(),
                'tokenable_id' => $model->getKey(),
                'token' => $token,
            ],
            [
                'device_type' => $deviceInfo['type'] ?? null,
                'device_id' => $deviceInfo['id'] ?? null,
                'is_active' => true,
                'last_used_at' => now(),
            ]
        );
    }
}
