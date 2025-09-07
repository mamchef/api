<?php

namespace App\Models;

use App\Enums\RegisterSourceEnum;
use App\Enums\User\UserStatusEnum;
use App\ModelFilters\UserFilter;
use App\Traits\GetTableColumn;
use Carbon\Carbon;
use EloquentFilter\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;


/**
 * @property int $id
 * @property int $uuid
 * @property string $first_name
 * @property string $last_name
 * @property string $phone_number
 * @property string $country_code
 * @property string $email
 * @property bool $commercial_agreement
 * @property Carbon $email_verified_at
 * @property string $password
 * @property UserStatusEnum $status
 * @property string $register_source
 * @property Carbon $deleted_at
 * @property Carbon $updated_at
 * @property Carbon $created_at
 * @property string $lang
 *
 *
 * Relations:
 * @property HasMany | UserAddress[] $userAddresses
 * @property HasMany | UserTransaction[] $transactions
 * @property MorphMany | Notification[] $notifications
 * @property MorphMany | Notification[] $unreadNotifications
 * @property MorphMany | FcmToken[] $fcmTokens
 * @property MorphMany | FcmToken[] $activeFcmTokens
 * @property BelongsToMany | Food[] $bookmarkFoods
 * @property HasMany | Bookmark[] $bookmarks
 */
class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens, SoftDeletes, Filterable, GetTableColumn;

    public static string $TOKEN_NAME = "user-token";

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $guarded = ["id"];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
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
            "status" => UserStatusEnum::class
        ];
    }

    // ================== SCOPES ===================== //

    public function scopeActive($query)
    {
        return $query->where('status', UserStatusEnum::ACTIVE->value);
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


    public function getFullName(): string
    {
        return $this->first_name . " " . $this->last_name;
    }


    public function routeNotificationForFcm(): array
    {
        return $this->activeFcmTokens()->pluck('token')->toArray();
    }


    public function receivesBroadcastNotificationsOn(): string
    {
        return 'user-' . $this->id;
    }

    // ================= Relations ==================== //

    public function userAddresses(): HasMany
    {
        return $this->hasMany(UserAddress::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(UserTransaction::class);
    }

    public function notifications(): MorphMany
    {
        return $this->morphMany(Notification::class, 'notifiable')->orderBy('created_at', 'desc');
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


    public function bookmarkFoods(): BelongsToMany
    {
        return $this->belongsToMany(Food::class, 'bookmarks', 'user_id', 'food_id');
    }

    public function bookmarks(): HasMany
    {
        return $this->hasMany(Bookmark::class, 'user_id', 'id');
    }

    //==================== MISC =======================//


    public function getAvailableCredit(): float
    {
        return UserTransaction::forUser($this->id)->completed()->sum('amount');
    }

    public function getModelFilterClass(): string
    {
        return UserFilter::class;
    }

}
