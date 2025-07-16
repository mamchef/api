<?php

namespace App\Models;

use App\Enums\Chef\ChefStore\ChefStoreStatusEnum;
use App\Enums\Chef\ChefStore\DeliveryOptionEnum;
use Carbon\Carbon;
use Cviebrock\EloquentSluggable\Sluggable;
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

    use Sluggable;

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

    public function chefStoreTimeAllow() :bool
    {
        $chefStoreStartDailyTime = explode(':', $this->start_daily_time);
        $chefStoreEndDailyTime = explode(':', $this->end_daily_time);
        $startDaily = now()->startOfDay()->addHours((int)$chefStoreStartDailyTime[0])->addMinutes(
            (int)$chefStoreStartDailyTime[1]
        );
        $endDaily = now()->startOfDay()->addHours((int)$chefStoreEndDailyTime[0])->addMinutes((int)$chefStoreEndDailyTime[1]);

        if (now()->between($startDaily, $endDaily)) {
            return true;
        }
        return false;
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
}
