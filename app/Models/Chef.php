<?php

namespace App\Models;

use App\DTOs\BaseDTO;
use App\Enums\Chef\ChefStatusEnum;
use App\Enums\RegisterSourceEnum;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;

/**
 * @property int $id
 * @property string $uuid
 * @property string $id_number
 * @property string $first_name
 * @property string $last_name
 * @property string $email
 * @property Carbon $email_verified_at
 * @property string $password
 * @property string $phone
 * @property int $city_id
 * @property string $main_street
 * @property string $address
 * @property string $zip
 * @property ChefStatusEnum $status
 * @property string $document_1
 * @property string $document_2
 * @property string $contract_id
 * @property string $contract
 * @property Carbon $created_at
 * @property string $updated_at
 *
 *
 * Relations:
 * @property BelongsTo | City $city
 * @property HasOne | ChefStore $chefStore
 * @property MorphMany | Notification[] $notifications
 * @property MorphMany | Notification[] $unreadNotifications
 * @property MorphMany | FcmToken[] $fcmTokens
 * @property MorphMany | FcmToken[] $activeFcmTokens
 */
class Chef extends Authenticatable
{
    use HasFactory, HasApiTokens , Notifiable;

    public static string $TOKEN_NAME = "chef-token";

    protected $guarded = ["id"];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            "register_source" => RegisterSourceEnum::class,
            "status" => ChefStatusEnum::class,
        ];
    }


    // ================== QUERIES ===================== //


    public static function generatePassword(string $password): string
    {
        return Hash::make($password);
    }


    public function passwordCheck(string $password): bool
    {
        return Hash::check($password, $this->password);
    }


    /**
     * @param BaseDTO $dto
     * @return bool
     */
    public function updateByDTO(BaseDto $dto): self
    {
        if ($this->id) {
            $this->update($dto->toArray());
            return $this->fresh();
        }
        throw new ModelNotFoundException();
    }


    public function getFullName(): string
    {
        return  $this->first_name . " " . $this->last_name;
    }

    public function routeNotificationForFcm(): array
    {
        return $this->activeFcmTokens()->pluck('token')->toArray();
    }


    public function receivesBroadcastNotificationsOn(): string
    {
        return 'chef-' . $this->id;
    }

    // ================= Relations ==================== //

    public function chefStore(): HasOne
    {
        return $this->hasOne(ChefStore::class);
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }


    public function unreadNotifications(): MorphMany
    {
        return $this->notifications()->whereNull('read_at');
    }

    public function fcmTokens(): MorphMany
    {
        return $this->morphMany(FcmToken::class, 'tokenable')->orderBy('created_at', 'desc');
    }


    public function activeFcmTokens(): MorphMany
    {
        return $this->fcmTokens()->where('is_active', true);
    }
}
