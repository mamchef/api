<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property int $name
 * @property int $user_id
 * @property int $city_id
 * @property string $address
 * @property string $apartment
 * @property string $entry_code
 * @property string $floor
 * @property Carbon $deleted_at
 * @property Carbon $updated_at
 * @property Carbon $created_at
 *
 *
 * Relations:
 * @property User $user
 * @property City $city
 */
class UserAddress extends Model
{
    use SoftDeletes;

    protected $table = 'user_addresses';

    protected $guarded = ["id"];

    // ================== QUERIES ===================== //


    // ================= Relations ==================== //

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }


    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    //==================== MISC =======================//
}
