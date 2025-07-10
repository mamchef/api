<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property string $name
 * @property string $description
 * @property int $country_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @property BelongsTo | Country $country
 */
class City extends Model
{
    protected $table = "cities";
    protected $guarded = ["id"];


    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

}