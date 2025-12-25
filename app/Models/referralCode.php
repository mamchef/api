<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @property int $id
 * @property int $referrable_id
 * @property string $referrable_type
 * @property string $code
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 *
 * Relations:
 * @property BelongsTo | User $user
 * @property MorphTo referrable
 * @property HasMany | referral $referrals
 */
class referralCode extends Model
{
    protected $guarded = [];


    // ================= Relations ==================== //
    public function referrable(): MorphTo
    {
        return $this->morphTo();
    }

    public function referrals(): HasMany
    {
        return $this->hasMany(Referral::class);
    }
}
