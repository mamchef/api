<?php

namespace App\Enums;

enum RegisterSourceEnum: string
{
    case Direct = 'direct';
    case Gmail = 'gmail';
    case Facebook = 'facebook';
}
