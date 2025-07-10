<?php

namespace App\Models;

use App\Traits\GetTableColumn;
use Carbon\Carbon;
use Cviebrock\EloquentSluggable\Sluggable;
use EloquentFilter\Filterable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property  string $description
 * @property string $image
 * @property float $price
 * @property int $available_qty
 * @property int $chef_store_id
 * @property int $category_id
 * @property int $display_order
 * @property float $rating
 * @property boolean $status
 * @property Carbon $deleted_at
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 *
 * Relations:
 * @property BelongsTo | ChefStore $chefStore
 * @property BelongsTo | Tag $tags
 * @property HasMany | FoodOptionGroup[] $optionGroups
 * @property BelongsToMany | User[] $bookmarkUsers
 * @property HasMany | Bookmark[] $bookmarks
 */
class Food extends Model
{
    use SoftDeletes, Sluggable, Filterable, GetTableColumn;

    protected $table = "foods";

    protected $guarded = ["id"];

    //===================== RELATIONS ====================//

    public function chefStore(): BelongsTo
    {
        return $this->belongsTo(ChefStore::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'food_tags', 'food_id', 'tag_id');
    }


    public function optionGroups(): HasMany
    {
        return $this->hasMany(FoodOptionGroup::class);
    }


    public function bookmarkUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'bookmarks', 'food_id', 'user_id');
    }

    public function bookmarks(): HasMany
    {
        return $this->hasMany(Bookmark::class, 'food_id', 'id');
    }

    //==================== Scopes =======================//


    protected function scopeChefStoreFoods(Builder $query, int $chefStoreID): Builder
    {
        return $query->where('chef_store_id', $chefStoreID);
    }

    public function scopeInStock($query)
    {
        return $query->where('available_qty', '>', 0);
    }

    //==================== MISC =======================//

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => ['chefStore.slug', "name"],
                "separator" => "-",
                "unique" => true,
                "onUpdate" => true
            ],
        ];
    }
}
