<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;

class Controller extends BaseController
{
    /**
     * Format and return error response
     *
     * @param  string  $message
     * @param  int  $code
     * @return array
     */
    protected function errorResponse($message, $code = 400)
    {
        return response()->json([
            'status' => 'error',
            'error' => $message
        ], $code);
    }

    /**
     * Format and return success response
     *
     * @param  string  $message
     * @param  array|string  $data
     * @param  int  $code
     * @return array
     */
    protected function successResponse($data = '', $message = '', $code = 200)
    {
        $response = ['status' => 'success'];

        if ($message != '') {
            $response['message'] = $message;
        }

        if ($data != '') {
            $response['data'] = $data;
        }

        return response()->json($response, $code);
    }
}
