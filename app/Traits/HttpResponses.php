<?php

namespace App\Traits;

trait HttpResponses
{
    protected function success($data, $message = null, $statusCode = 200)
    {
        return response()->json([
            "status" => "request was succesful",
            "message" => $message,
            "data" => $data,
        ], $statusCode);
    }
    protected function error($data, $message = null, $statusCode)
    {
        return response()->json([
            "status" => "Error has occured",
            "message" => $message,
            "data" => $data,
        ], $statusCode);
    }
}
