<?php

namespace App\DTOs;

class DoNotChange
{
    public static function value(): self
    {
        return new self();
    }
}