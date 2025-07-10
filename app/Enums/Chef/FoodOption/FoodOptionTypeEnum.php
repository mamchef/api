<?php

namespace App\Enums\Chef\FoodOption;

enum FoodOptionTypeEnum: string
{
    case Quantitative = 'quantitative';
    case Qualitative = 'qualitative';

    public static function values(): array
    {
        return [
            self::Quantitative->value,
            self::Qualitative->value,
        ];
    }

    public function label(): string
    {
        return match ($this) {
            self::Quantitative => 'Quantitative',
            self::Qualitative => 'Qualitative',
        };
    }
}
