<?php

namespace App\Enums;

enum UserRoleEnum: string
{
    case OWNER = 'owner';
    case ADMIN = 'admin';
    case COMMON = 'common';

    public function isOwner(): bool
    {
        return $this === self::OWNER;
    }

    public function isAdmin(): bool
    {
        return $this === self::ADMIN;
    }

    public function isCommon(): bool
    {
        return $this === self::COMMON;
    }

    public function getLabel(): string
    {
        return match ($this) {
            self::OWNER => 'Owner',
            self::ADMIN => 'Admin',
            self::COMMON => 'Common'
        };
    }
}
