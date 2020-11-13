<?php

namespace App\Models\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Employee extends Authenticatable
{
    use Notifiable, HasFactory;

    protected $guard = 'employee';

    protected $fillable = [
        'name', 'lastname', 'mobile', 'email', 'position', 'isAdmin',
        'avatar', 'activated', 'active', 'verified_at', 'dateBirth', 'user_code', 'ip',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];
}
