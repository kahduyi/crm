<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

/**
 * @property mixed name
 * @property mixed lastname
 * @property mixed verified_at
 */
class Employee extends Authenticatable
{
    use Notifiable, HasFactory;

    protected $guard = 'employee';

    protected $fillable = [
        'name', 'lastname', 'mobile', 'personnelCode', 'email', 'password', 'position', 'isAdmin',
        'avatar', 'active', 'verified_at', 'dateBirth', 'ip', 'website',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'verified_at' => 'datetime',
    ];

    /**
     * @return string
     */
    public function getFullNameAttribute()
    {
        return $this->name . ' ' . $this->lastname;
    }

    /**
     * value of $field is mobile or email!
     * @param $field
     * @return bool
     */
    public function operationVerified()
    {
        $this->forceFill([
            'verified_at' => $this->freshTimestamp(),
        ])->save();
    }

    public function setMobileAttribute($value)
    {
        $this->attributes['mobile'] = toValidMobileNumber($value);
    }

    public function verifyCode()
    {
        return $this->hasOne(VerifyCode::class);
    }
}
