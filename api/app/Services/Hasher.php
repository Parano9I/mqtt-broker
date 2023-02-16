<?php

namespace App\Services;

use Illuminate\Support\Facades\Hash;

class Hasher
{
    public function hash(string $plainPassword): string
    {
        return Hash::make($plainPassword);
    }

    public function verify(string $hashedPassword, string $plainPassword): bool
    {
        return Hash::check($plainPassword, $hashedPassword);
    }

}
