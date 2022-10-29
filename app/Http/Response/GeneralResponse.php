<?php

namespace App\Http\Response;

class GeneralResponse
{
    public function default_json($success = null, $message = null, $data = null, $code = null)
    {
        return response()->json([
            'success' => $success,
            'message' => $message,
            'data' => $data,
            'code' => $code,
        ], $code);
    }
}
