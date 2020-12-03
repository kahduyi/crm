<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

/**
 * @property mixed name
 * @property mixed lastname
 */
class Employee extends Authenticatable
{
    use Notifiable, HasFactory;

    protected $guard = 'employee';

    protected $fillable = [
        'name', 'lastname', 'mobile', 'personnelCode', 'email', 'password', 'position', 'isAdmin',
        'avatar', 'active', 'verified_at', 'dateBirth', 'verify_code', 'ip','website',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'verified_at'  => 'datetime',
    ];

    /**
     * @return string
     */
    public function getFullNameAttribute()
    {
        return $this->name . ' ' . $this->lastname;
    }
}
