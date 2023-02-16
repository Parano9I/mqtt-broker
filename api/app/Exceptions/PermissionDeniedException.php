<?php

namespace App\Exceptions;

use Exception;

class PermissionDeniedException extends Exception
{
    public function render($request)
    {
        return response([
            'messages' => [
                'title' => 'Permission denied',
                'detail' => $this->getMessage(),
                'status' => 403
            ]
        ], 403);
    }
}
