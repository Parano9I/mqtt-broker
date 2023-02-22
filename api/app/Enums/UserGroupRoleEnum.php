<?php

namespace App\Enums;

enum UserGroupRoleEnum: int
{
    case OWNER = 0;
    case ADMIN = 1;
    case COMMON = 2;

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
