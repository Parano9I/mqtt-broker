<?php

namespace App\Services;

use Illuminate\Support\Str;

class SensorService
{
    public function generateSecret(): string
    {
        return Str::random(40);
    }
}
