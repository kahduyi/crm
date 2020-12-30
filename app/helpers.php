<?php

if (!function_exists('toValidMobileNumber')) {
    /**
     * @param $mobileNumber
     * @return false|string
     */
    function toValidMobileNumber($mobileNumber)
    {
        return substr($mobileNumber, -10, 10);;
    }
}

if (!function_exists('generateCode')) {
    /**
     * Generate a six digits code
     *
     * @param int $codeLength
     * @return string
     */
    function generateCode($codeLength = 6)
    {
        $max = pow(10, $codeLength);
        $min = $max / 10 - 1;
        $code = mt_rand($min, $max);
        return $code;
    }
}

if (!function_exists('functionName')) {
    function functionName()
    {
        $i = 1 + 1;
        return $i;
    }
}
