<?php

namespace App\Enums\Chef\FoodOptionGroup;

enum FoodOptionGroupSelectTypeEnum: string
{
    case Single = 'single';
    case Multiple = 'multiple';

    public static function values(): array
    {
        return [
            self::Single->value,
            self::Multiple->value,
        ];
    }
}
