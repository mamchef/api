<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @property int $id
 * @property int $referral_code_id
 * @property int $referred_id
 * @property int $referred_type
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * Relations:
 * @property MorphTo $referred
 * @property BelongsTo | ReferralCode $referralCode
 *
 */
class Referral extends Model
{
    protected $guarded = [];

    public function referred(): MorphTo
    {
        return $this->morphTo();
    }

    public function referralCode(): BelongsTo
    {
        return $this->belongsTo(ReferralCode::class);
    }
}
