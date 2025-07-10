<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $food_id
 * @property int $user_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * Relations:
 * @property User | BelongsTo $user
 * @property Food | BelongsTo $food
 */
class Bookmark extends Model
{

    protected $table = 'bookmarks';
    protected $guarded = ['id'];


    // ================= Relations ==================== //

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function food(): BelongsTo
    {
        return $this->belongsTo(Food::class);
    }
}
