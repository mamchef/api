<?php

namespace App\Services\Interfaces;

use App\Models\Referral;
use App\Models\ReferralCode;

interface ReferralCodeServiceInterface
{

    /**
     * @param string $code
     * @return referralCode
     */
    public function getReferralCodeByCode(string $code): ReferralCode;

    /**
     * @param int $chefId
     * @param string $referralCode
     * @return void
     */
    public function submitChefReferredByCode(int $chefId, string $referralCode): void;


    /**
     * @param int $userId
     * @return ReferralCode
     */
    public function getUserReferralCode(int $userId): ReferralCode;


    /**
     * @param Referral $referral
     * @return void
     */
    public function disbursementReferralPrize(Referral $referral): void;
}