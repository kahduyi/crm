<?php

namespace App\Models;

use App\Exceptions\RegisterVerificationException;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

/**
 * @property mixed used
 * @property mixed created_at
 * @property mixed code
 */
class VerifyCode extends Model
{
    use HasFactory;

    /**
     * in minutes.
     */
    const EXPIRATION_TIME = 15;

    protected $fillable = ['code', 'used', 'employee_id'];

    public function __construct(array $attributes = [])
    {
        if (!isset($attributes['code'])) {
            $attributes['code'] = $this->generateCode(config('auth.length_register_verify_code'));
        }
        parent::__construct($attributes);
    }

    /**
     * Generate a six digits code
     *
     * @param int $codeLength
     * @return string
     */
    private function generateCode($codeLength = 6)
    {
        $max = pow(10, $codeLength);
        $min = $max / 10 - 1;
        $code = mt_rand($min, $max);
        return $code;
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * True if the verify code is not used nor expired
     *
     * @return bool
     */
    public function isValid()
    {
        return !$this->isUsed() && !$this->isExpired();
    }

    /**
     * Is the current verify code used
     *
     * @return bool
     */
    public function isUsed()
    {
        return $this->used;
    }

    /**
     * Is the current token expired
     *
     * @return bool
     */
    public function isExpired()
    {
        return $this->created_at->diffInMinutes(Carbon::now()) > static::EXPIRATION_TIME;
    }

    public function sendCode()
    {
        if (!$this->employee) {
            throw new RegisterVerificationException(__('message.user.The-user-is-invalid'));
        }

        if (!$this->code) {
            $this->code = $this->generateCode();
        }

        try {
            //TODO ارسال پیامک
            /*Cache::put('user-auth-register-mobile', compact('verifyCode'),
                config('auth.register_cache_expiration', 1440));*/
            Log::info('SEND-REGISTER-CODE-MESSAGE-TO-USER', ['code' => $this->code]);
            // send code via SMS
        } catch (\Exception $ex) {
            return false; //enable to send SMS
        }

        return true;
    }
}
