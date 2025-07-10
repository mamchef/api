<?php

namespace App\Models;

use App\Enums\Chef\FoodOptionGroup\FoodOptionGroupSelectTypeEnum;
use Carbon\Carbon;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property int $food_id
 * @property string $name
 * @property string $slug
 * @property FoodOptionGroupSelectTypeEnum $selection_type
 * @property string $max_selections
 * @property boolean $is_required
 * @property Carbon $deleted_at
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * Relations:
 * @property BelongsTo | Food $food
 * @property HasMany | FoodOption[] $options
 */
class FoodOptionGroup extends Model
{
    use SoftDeletes, Sluggable;

    protected $table = 'food_option_groups';

    protected $guarded = ['id'];


    protected $casts = [
        "selection_type" => FoodOptionGroupSelectTypeEnum::class
    ];

    //===================== RELATIONS ====================//

    public function food(): BelongsTo
    {
        return $this->belongsTo(Food::class);
    }

    public function options(): HasMany
    {
        return $this->hasMany(FoodOption::class)->orderBy('sort_order');
    }


    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => ['food.slug', "name"],
                "separator" => "-",
                "unique" => true,
                "onUpdate" => true
            ],
        ];
    }

}
