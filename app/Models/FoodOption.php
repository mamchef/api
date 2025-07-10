<?php

namespace App\Models;

use App\Enums\Chef\FoodOption\FoodOptionTypeEnum;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property int $food_option_group_id
 * @property FoodOptionTypeEnum $type
 * @property string $name
 * @property string $description
 * @property float $price
 * @property int $sort_order
 * @property int $maximum_allowed
 * @property Carbon $deleted_at
 * @property Carbon $update_at
 * @property callable $created_at
 *
 *
 * Relations:
 * @property BelongsTo | FoodOptionGroup $optionGroup
 */
class FoodOption extends Model
{
    use SoftDeletes;

    protected $table = 'food_options';

    protected $guarded = ["id"];


    protected $casts = [
        'type' => FoodOptionTypeEnum::class
    ];

    //===================== QUERIES ======================//


    //===================== RELATIONS ====================//

    public function optionGroup(): BelongsTo
    {
        return $this->belongsTo(FoodOptionGroup::class, 'food_option_group_id', 'id');
    }

    //===================== MIXED ====================//
}
