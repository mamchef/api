<?php

namespace App\DTOs\Admin\User;

use App\DTOs\BaseDTO;
use App\DTOs\DoNotChange;
use App\Enums\Chef\ChefStatusEnum;
use App\Enums\RegisterSourceEnum;
use App\Enums\User\UserStatusEnum;
use Illuminate\Http\UploadedFile;

readonly class UserUpdateByAdminDTO extends BaseDTO
{
    public function __construct(
        public string|DoNotChange $first_name = new DoNotChange(),
        public string|DoNotChange $last_name = new DoNotChange(),
        public string|DoNotChange $email = new DoNotChange(),
        public string|DoNotChange $password = new DoNotChange(),
        public string|DoNotChange $phone = new DoNotChange(),
        public UserStatusEnum|DoNotChange $status = new DoNotChange(),
    ) {
    }

}