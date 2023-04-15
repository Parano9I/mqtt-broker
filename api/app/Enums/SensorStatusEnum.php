<?php

namespace App\Enums;

enum SensorStatusEnum: string
{
    case ONLINE = 'online';
    case OFFLINE = 'offline';

    public function isOnline(): bool
    {
        return $this === self::ONLINE;
    }

    public function isOffline(): bool
    {
        return $this === self::OFFLINE;
    }

    public function getLabel(): string
    {
        return match ($this) {
            self::ONLINE => 'Online',
            self::OFFLINE => 'Offline',
        };
    }
}
