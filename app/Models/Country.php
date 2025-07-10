<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $name
 * @property string $description
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @property HasMany | City[] $cities
 */
class Country extends Model
{
    protected $table = "countries";
    protected $guarded = ["id"];


    public function cities(): HasMany
    {
        return $this->hasMany(City::class);
    }
}