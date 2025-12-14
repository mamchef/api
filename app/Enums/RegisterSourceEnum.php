<?php

namespace App\Enums;

enum RegisterSourceEnum: string
{
    case Direct = 'direct';
    case Gmail = 'gmail';
    case Facebook = 'facebook';
    case Apple = 'apple';


    public static function getEnum(string $value): RegisterSourceEnum
    {
        return match ($value) {
            self::Direct->value => self::Direct,
            self::Gmail->value => self::Gmail,
            self::Facebook->value => self::Facebook,
            self::Apple->value => self::Apple,
        };
    }
}
