<?php

namespace App\Tests\Domain;

use App\Domain\Airplane;
use App\Domain\Airline;
use App\Domain\Flight;
use PHPUnit\Framework\TestCase;

class FlightTest extends TestCase
{
    public function testGetActualDuration()
    {
        $actualStart = new \DateTimeImmutable('2021-12-17T10:12:00+00:00');
        $actualEnd = new \DateTimeImmutable('2021-12-17T11:33:00+00:00');

        $flight = new Flight(
            new Airplane('RegistrationCode', new Airline('AirlineName')),
            'FromAirport',
            'ToAirport',
            new \DateTimeImmutable('2021-12-17T10:00:00+00:00'),
            new \DateTimeImmutable('2021-12-17T11:30:00+00:00'),
            $actualStart,
            $actualEnd
        );

        $expectedDuration = $actualStart->getTimestamp() - $actualEnd->getTimestamp();

        $this->assertEquals($expectedDuration, $flight->getActualDuration());
    }

    public function testWasLandingMissed()
    {
        $flight = new Flight(
            new Airplane('RegistrationCode', new Airline('AirlineName')),
            'FromAirport',
            'ToAirport',
            new \DateTimeImmutable('2021-12-17T10:00:00+00:00'),
            new \DateTimeImmutable('2021-12-17T11:30:00+00:00'),
            new \DateTimeImmutable('2021-12-17T10:12:00+00:00'),
            new \DateTimeImmutable('2021-12-17T11:36:00+00:00')
        );

        $this->assertTrue($flight->wasLandingMissed());

        $flight = new Flight(
            new Airplane('RegistrationCode', new Airline('AirlineName')),
            'FromAirport',
            'ToAirport',
            new \DateTimeImmutable('2021-12-17T10:00:00+00:00'),
            new \DateTimeImmutable('2021-12-17T11:30:00+00:00'),
            new \DateTimeImmutable('2021-12-17T10:12:00+00:00'),
            new \DateTimeImmutable('2021-12-17T11:34:00+00:00')
        );

        $this->assertFalse($flight->wasLandingMissed());
    }
}
