<?php

/**
 * @param $mobileNumber
 * @return false|string
 */
function toValidMobileNumber($mobileNumber)
{
    return substr($mobileNumber, -10, 10);;
}
