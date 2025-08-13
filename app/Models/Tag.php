<?php

namespace App\Models;

use App\ModelFilters\TagFilter;
use App\Traits\GetTableColumn;
use Carbon\Carbon;
use Cviebrock\EloquentSluggable\Sluggable;
use EloquentFilter\Filterable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string $description
 * @property int $tag_id
 * @property bool $status
 * @property bool $homepage
 * @property string $icon
 * @property string $icon_type
 * @property int $priority
 * @property Carbon $created_at
 * @property Carbon $update_at
 *
 * Relations :
 * @property BelongsTo | Tag $parent
 * @property HasMany | Food[] $foods
 */
class Tag extends Model
{

    use Sluggable, Filterable, GetTableColumn;

    protected $table = "tags";

    protected $guarded = ["id"];

    //===================== SCOPES ================//

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    //==================== Relations =============//
    public function foods(): HasMany
    {
        return $this->hasMany(Food::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class,'tag_id','id');
    }


    //==================== MISC =============//

    public function sluggable(): array
    {
        return [
            "slug" => [
                "source" => "name",
                "separator" => "-",
                "unique" => true,
                "onUpdate" => true
            ],
        ];
    }

    public function getModelFilterClass(): string
    {
        return TagFilter::class;
}
}
