<?php

namespace App\DTOs\Chef\Auth;

use App\DTOs\BaseDTO;
use App\Models\Chef;

readonly class ForgotPasswordDTO extends BaseDTO
{

    public function __construct(
        private string $email,
        private string $password,
    ) {
    }


    public function getEmail(): string
    {
        return $this->email;
    }


    public function getPassword(): string
    {
        return Chef::generatePassword($this->password);
    }

    public function toArray(): array
    {
        return
            [
                "email" => $this->getEmail(),
                "password" => $this->getPassword(),
            ];
    }


}