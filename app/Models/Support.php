<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Support extends Model
{
    use HasFactory;

    const TYPE_ADMIN = 'admin';
    const TYPE_SUPPORT= 'support';
    const TYPE_DEVELOPER= 'developer';
    const TYPES = [self::TYPE_ADMIN, self::TYPE_SUPPORT, self::TYPE_DEVELOPER];
}
