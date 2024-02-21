<?php 

namespace App\Tests\Service;

use App\Domain\Airline;
use App\Domain\Airplane;
use App\Domain\Flight;
use App\Service\FlightAnalysisService;
use PHPUnit\Framework\TestCase;

class FlightAnalysisServiceTest extends TestCase
{
    public function testFindTopThreeLongestFlights()
    {
        $service = new FlightAnalysisService();

        $flight1 = $this->createTestFlight('OO-AAA', 'ZAG', 'BUD', '2020-01-01T00:00:00+00:00', '2020-01-01T00:50:00+00:00', '2020-01-01T00:04:00+00:00', '2020-01-01T01:03:00+00:00');
        $flight2 = $this->createTestFlight('D-AAC', 'ZAG', 'STN', '2020-01-01T05:00:00+00:00', '2020-01-01T05:50:00+00:00', '2020-01-01T06:04:00+00:00', '2020-01-01T06:53:00+00:00');
        $flight3 = $this->createTestFlight('OO-AAB', 'STN', 'BUD', '2020-01-01T08:00:00+00:00', '2020-01-01T08:50:00+00:00', '2020-01-01T09:04:00+00:00', '2020-01-01T09:53:00+00:00');
        $flights = [$flight1, $flight2, $flight3];

        $result = $service->findTopThreeLongestFlights($flights);

        $this->assertCount(3, $result);
        $this->assertEquals($flight2, $result[0]);
        $this->assertEquals($flight3, $result[1]);
        $this->assertEquals($flight1, $result[2]);
    }

    public function testFindMostMissedAirline()
    {
        $service = new FlightAnalysisService();

        $flight1 = $this->createTestFlight('OO-AAA', 'ZAG', 'BUD', '2020-01-01T00:00:00+00:00', '2020-01-01T00:50:00+00:00', '2020-01-01T00:04:00+00:00', '2020-01-01T01:03:00+00:00');
        $flight2 = $this->createTestFlight('D-AAC', 'ZAG', 'STN', '2020-01-01T05:00:00+00:00', '2020-01-01T05:50:00+00:00', '2020-01-01T06:04:00+00:00', '2020-01-01T06:53:00+00:00');
        $flight3 = $this->createTestFlight('OO-AAB', 'STN', 'BUD', '2020-01-01T08:00:00+00:00', '2020-01-01T08:50:00+00:00', '2020-01-01T09:04:00+00:00', '2020-01-01T09:53:00+00:00');
        $flights = [$flight1, $flight2, $flight3];

        $result = $service->findMostMissedAirline($flights);

        $this->assertEquals('Oscar Air', $result);
    }

    public function testFindMostOvernightStaysDestination()
    {
        $service = new FlightAnalysisService();

        // expected ZAG two overnight stays, STAN one overnight stay
        $flight1 = $this->createTestFlight('OO-AAA', 'BUD', 'ZAG', '2020-01-01T00:00:00+00:00', '2020-01-01T00:50:00+00:00', '2020-02-01T00:04:00+00:00', '2020-02-01T01:03:00+00:00');
        $flight2 = $this->createTestFlight('OO-AAA', 'ZAG', 'BUD', '2020-01-01T03:00:00+00:00', '2020-01-01T03:50:00+00:00', '2020-03-01T04:04:00+00:00', '2020-01-01T04:53:00+00:00');
        $flight3 = $this->createTestFlight('D-AAC', 'ZAG', 'STN', '2020-01-01T05:00:00+00:00', '2020-01-01T05:50:00+00:00', '2020-01-01T06:04:00+00:00', '2020-01-01T06:53:00+00:00');
        $flight4 = $this->createTestFlight('OO-AAA', 'BUD', 'STN', '2020-01-01T05:00:00+00:00', '2020-01-01T05:50:00+00:00', '2020-01-01T06:04:00+00:00', '2020-01-01T06:53:00+00:00');
        $flight5 = $this->createTestFlight('OO-AAA', 'STN', 'ZAG', '2020-01-01T05:00:00+00:00', '2020-01-01T05:50:00+00:00', '2020-02-01T06:04:00+00:00', '2020-02-01T06:53:00+00:00');
        $flight6 = $this->createTestFlight('OO-AAA', 'ZAG', 'BUD', '2020-01-01T00:00:00+00:00', '2020-01-01T00:50:00+00:00', '2020-03-01T00:04:00+00:00', '2020-01-01T01:03:00+00:00');
        $flights = [$flight1, $flight2, $flight3, $flight4, $flight5, $flight6];

        $result = $service->findMostOvernightStaysDestination($flights);

        $this->assertEquals('ZAG', $result);
    }

    public function testSpellStringPhonetically()
    {
        $service = new FlightAnalysisService();

        $result = $service->spellStringPhonetically('HRV');

        $this->assertSame('HOTEL ROMEO VICTOR', $result);
    }

    private function createTestFlight($registration, $from, $to, $scheduledStart, $scheduledEnd, $actualStart, $actualEnd)
    {
        $airline = new Airline('Test Airline');
        $airplane = new Airplane($registration, $airline);

        return new Flight(
            $airplane,
            $from,
            $to,
            new \DateTimeImmutable($scheduledStart),
            new \DateTimeImmutable($scheduledEnd),
            new \DateTimeImmutable($actualStart),
            new \DateTimeImmutable($actualEnd)
        );
    }
}
