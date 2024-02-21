<?php

namespace App\Enum;

enum AirportsLookup: string
{
    case MAD = 'MAD';
    case STN = 'STN';
    case BUD = 'BUD';
    case ZAG = 'ZAG';
    case BER = 'BER';

    public function city(): string
    {
        return match ($this) {
            self::MAD => 'Madrid',
            self::STN => 'Stansted',
            self::BUD => 'Budapest',
            self::ZAG => 'Zagreb',
            self::BER => 'Berlin',
        };
    }
}
