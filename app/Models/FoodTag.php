<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $food_id
 * @property int $tag_id
 */
class FoodTag extends Model
{
    protected $table = 'food_tags';
    protected $guarded = ['id'];
}
