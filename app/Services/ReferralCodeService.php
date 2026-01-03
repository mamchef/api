<?php

namespace App\Services;

use App\Enums\Chef\ChefStatusEnum;
use App\Models\Chef;
use App\Models\Referral;
use App\Models\ReferralCode;
use App\Models\User;
use App\Models\UserTransaction;
use App\Services\Interfaces\ReferralCodeServiceInterface;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ReferralCodeService implements ReferralCodeServiceInterface
{

    /** @inheritDoc */
    public function getReferralCodeByCode(string $code): ReferralCode
    {
        return ReferralCode::query()->where('code', $code)->firstOrFail();
    }

    /** @inheritDoc */
    public function submitChefReferredByCode(int $chefId, string $referralCode): void
    {
        $alreadyReferred = Referral::query()->where('referred_id', $chefId)
            ->where('referred_type', Chef::class)
            ->first();
        if ($alreadyReferred) {
            #this chef already referred
            return;
        }

        try {
            $referralCode = $this->getReferralCodeByCode($referralCode);
        } catch (\Exception $exception) {
            return;
        }

        try {
            Referral::query()->create([
                'referral_code_id' => $referralCode->id,
                'referred_id' => $chefId,
                'referred_type' => Chef::class,
            ]);
        } catch (\Exception $exception) {
            Log::error('referral-code-submit-error', [
                'referral_code_id' => $referralCode->id,
                'referral_code' => $referralCode->code,
                'message' => $exception->getMessage(),
            ]);
        }

    }


    /** @inheritDoc */
    public function getUserReferralCode(int $userId): ReferralCode
    {
        return ReferralCode::query()->firstOrCreate(
            [
                'referrable_id' => $userId,
                'referrable_type' => User::class],
            [
                'code' => self::generateCodeForReferral(),
            ]);
    }


    /** @inheritDoc */
    public function disbursementReferralPrize(Referral $referral): void
    {
        if ($referral->referred_type == Chef::class and
            $referral->referralCode->referrable_type == User::class
        ) {
            $chef = Chef::query()->find($referral->referred_id);
            if ($chef and $chef->status == ChefStatusEnum::Approved) {
                UserTransaction::createReferralPayment(
                    userId: $referral->referralCode->referrable_id,
                    referralId: $referral->id
                );
            }
        }
    }


    /**
     * @return string
     */
    public static function generateCodeForReferral(): string
    {
        do {
            $code = Str::lower(Str::random(6));
        } while (ReferralCode::query()->where('code', $code)->first());
        return $code;
    }
}