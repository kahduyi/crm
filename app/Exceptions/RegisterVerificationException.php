<?php

namespace App\Exceptions;

use Exception;

class RegisterVerificationException extends Exception
{
    /**
     * Report the exception.
     *
     * @return bool|void
     */
    public function report()
    {
        //
    }

    /**
     * Render the exception as an HTTP response.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function render($request)
    {
        return response([
            'status' => 'error',
            'message' => $this > $this->getMessage(),
        ], 422);
    }
}
