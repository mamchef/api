<?php

namespace App\Models;

use App\Enums\Admin\AdminRoleEnum;
use App\Enums\Admin\AdminStatusEnum;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;

/**
 * @property int $id
 * @property string $uuid
 * @property string $first_name
 * @property string $last_name
 * @property string $email
 * @property string $password
 * @property AdminStatusEnum $status
 * @property AdminRoleEnum $role
 * @property Carbon $deleted_at
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 *
 * Relations:
 * @method static Builder active()
 */
class Admin extends Authenticatable
{
    use HasApiTokens, Notifiable, SoftDeletes;

    protected $guarded = ['id'];

    public static string $TOKEN_NAME = "admin-token";

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'status' => AdminStatusEnum::class,
        'role' => AdminRoleEnum::class,
    ];

    // ================== SCOPES ===================== //

    public function scopeActive($query): Builder
    {
        return $query->where('status', AdminStatusEnum::ACTIVE->value);
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

    // ================= Relations ==================== //


    //==================== MISC =======================//
}
