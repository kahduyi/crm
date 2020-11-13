<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    const TYPE_SERVER = 'server';
    const TYPE_DESKTOP = 'desktop';
    const TYPES = [self::TYPE_SERVER, self::TYPE_DESKTOP];
}
