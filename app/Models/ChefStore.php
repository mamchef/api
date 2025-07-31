<?php

namespace App\Models;

use App\Enums\Chef\ChefStore\ChefStoreStatusEnum;
use App\Enums\Chef\ChefStore\DeliveryOptionEnum;
use App\ModelFilters\ChefStoreFilter;
use App\Traits\GetTableColumn;
use Carbon\Carbon;
use Cviebrock\EloquentSluggable\Sluggable;
use EloquentFilter\Filterable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string $short_description
 * @property string $profile_image
 * @property int $city_id
 * @property string $zip
 * @property string $address
 * @property string $building_details
 * @property string $lat
 * @property string $lng
 * @property string $phone
 * @property float $rating
 * @property string $status
 * @property string $estimated_time
 * @property string $start_daily_time
 * @property string $end_daily_time
 * @property string $main_street
 * @property bool $is_open
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon $deleted_at
 * @property DeliveryOptionEnum $delivery_method
 * @property float $delivery_cost
 *
 *
 * Relations:
 * @property BelongsTo | City $city
 * @property BelongsTo | Chef $chef
 */
class ChefStore extends Model
{

    use Sluggable, Filterable, GetTableColumn;

    protected $table = "chef_stores";

    protected $guarded = ["id"];

    protected $casts = [
        "status" => ChefStoreStatusEnum::class,
        "delivery_method" => DeliveryOptionEnum::class
    ];

    // ====================== Relations ====================== //

    public function chef(): BelongsTo
    {
        return $this->belongsTo(Chef::class);
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }


    // ====================== MIXED ====================== //

    public function hasPickup(): bool
    {
        return $this->delivery_method != DeliveryOptionEnum::DeliveryOnly;
    }

    public function chefStoreTimeAllow(): bool
    {
        if (!$this->start_daily_time || !$this->end_daily_time) {
            return false;
        }

        $now = now()->format('H:i');
        return $this->start_daily_time > $this->end_daily_time
            ? ($now >= $this->start_daily_time || $now <= $this->end_daily_time)
            : ($now >= $this->start_daily_time && $now <= $this->end_daily_time);
    }

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'name',
                "separator" => "-",
                "unique" => true,
                "onUpdate" => true
            ],
        ];
    }

    public function getModelFilterClass(): string
    {
        return ChefStoreFilter::class;
    }
}
