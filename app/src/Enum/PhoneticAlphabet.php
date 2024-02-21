<?php

namespace App\Enum;

enum PhoneticAlphabet: string
{
    case ALPHA = 'A';
    case BRAVO = 'B';
    case CHARLIE = 'C';
    case DELTA = 'D';
    case ECHO = 'E';
    case FOXTROT = 'F';
    case GOLF = 'G';
    case HOTEL = 'H';
    case INDIA = 'I';
    case JULIET = 'J';
    case KILO = 'K';
    case LIMA = 'L';
    case MIKE = 'M';
    case NOVEMBER = 'N';
    case OSCAR = 'O';
    case PAPA = 'P';
    case QUEBEC = 'Q';
    case ROMEO = 'R';
    case SIERRA = 'S';
    case TANGO = 'T';
    case UNIFORM = 'U';
    case VICTOR = 'V';
    case WHISKEY = 'W';
    case XRAY = 'X';
    case YANKEE = 'Y';
    case ZULU = 'Z';

    public function word(): string
    {
        return match ($this) {
            self::ALPHA => 'ALPHA',
            self::BRAVO => 'BRAVO',
            self::CHARLIE => 'CHARLIE',
            self::DELTA => 'DELTA',
            self::ECHO => 'ECHO',
            self::FOXTROT => 'FOXTROT',
            self::GOLF => 'GOLF',
            self::HOTEL => 'HOTEL',
            self::INDIA => 'INDIA',
            self::JULIET => 'JULIET',
            self::KILO => 'KILO',
            self::LIMA => 'LIMA',
            self::MIKE => 'MIKE',
            self::NOVEMBER => 'NOVEMBER',
            self::OSCAR => 'OSCAR',
            self::PAPA => 'PAPA',
            self::QUEBEC => 'QUEBEC',
            self::ROMEO => 'ROMEO',
            self::SIERRA => 'SIERRA',
            self::TANGO => 'TANGO',
            self::UNIFORM => 'UNIFORM',
            self::VICTOR => 'VICTOR',
            self::WHISKEY => 'WHISKEY',
            self::XRAY => 'XRAY',
            self::YANKEE => 'YANKEE',
            self::ZULU => 'ZULU'
        };
    }
}
