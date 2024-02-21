<?php

namespace App\Tests\Factory;

use App\Domain\Airline;
use App\Domain\Airplane;
use App\Domain\Flight;
use App\Factory\FlightFactory;
use PHPUnit\Framework\TestCase;

class FlightFactoryTest extends TestCase
{
    public function testCreateFromJsonLine()
    {
        $jsonLine = '{"registration":"OO-AAA","from":"ZAG","to":"BUD","scheduled_start":"2020-01-01T00:00:00+00:00","scheduled_end":"2020-01-01T00:50:00+00:00","actual_start":"2020-01-01T00:04:00+00:00","actual_end":"2020-01-01T01:03:00+00:00"}';

        $flight = FlightFactory::createFromJsonLine($jsonLine);

        $this->assertInstanceOf(Flight::class, $flight);

        $this->assertInstanceOf(Airplane::class, $flight->getAirplane());
        $this->assertInstanceOf(Airline::class, $flight->getAirplane()->getAirline());

        $this->assertEquals('OO-AAA', $flight->getAirplane()->getRegistration());
        $this->assertEquals('ZAG', $flight->getFrom());
        $this->assertEquals('BUD', $flight->getTo());
    }

    public function testCreateFromJsonLineWithInvalidJson()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid JSON data');

        FlightFactory::createFromJsonLine('Invalid JSON');
    }

    public function testCreateFromJsonLineWithMissingFields()
    {
        $jsonLine = '{"registration":"OO-AAA","from":"ZAG","to":"BUD"}';

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid JSON data: Some fields in JSON line are missing!');

        FlightFactory::createFromJsonLine($jsonLine);
    }

    public function testCreateFromJsonLineWithNullValue()
    {
        $jsonLine = '{"registration":null,"from":"ZAG","to":"BUD","scheduled_start":"2020-01-01T00:00:00+00:00","scheduled_end":"2020-01-01T00:50:00+00:00","actual_start":"2020-01-01T00:04:00+00:00","actual_end":"2020-01-01T01:03:00+00:00"}';

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid JSON data: Some fields in JSON line are missing!');

        FlightFactory::createFromJsonLine($jsonLine);
    }

    public function testCreateFromJsonLineWithUnknownRegistration()
    {
        $jsonLine = '{"registration":"OO-AAA-error","from":"ZAG","to":"BUD","scheduled_start":"2020-01-01T00:00:00+00:00","scheduled_end":"2020-01-01T00:50:00+00:00","actual_start":"2020-01-01T00:04:00+00:00","actual_end":"2020-01-01T01:03:00+00:00"}';

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Missing registration mapping.');

        FlightFactory::createFromJsonLine($jsonLine);
    }
}
