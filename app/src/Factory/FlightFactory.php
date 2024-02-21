<?php

namespace App\Factory;

use App\Domain\Airline;
use App\Domain\Airplane;
use App\Domain\Flight;
use App\Infrastructure\AirlineLookup;
use Exception;

class FlightFactory
{
    /**
     * @throws Exception
     */
    public static function createFromJsonLine(string $jsonLine): Flight
    {
        $data = json_decode($jsonLine, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \InvalidArgumentException('Invalid JSON data: ' . json_last_error_msg());
        }

        if (
            !isset($data['registration']) ||
            !isset($data['from']) ||
            !isset($data['to']) ||
            !isset($data['scheduled_start']) ||
            !isset($data['scheduled_end']) ||
            !isset($data['actual_start']) ||
            !isset($data['actual_end'])
        ) {
            throw new \InvalidArgumentException('Invalid JSON data: Some fields in JSON line are missing!');
        }

        $airlineName = AirlineLookup::from($data['registration']);
        $airline = new Airline($airlineName);
        $airplane = new Airplane($data['registration'], $airline);

        return new Flight(
            $airplane,
            $data['from'],
            $data['to'],
            new \DateTimeImmutable($data['scheduled_start']),
            new \DateTimeImmutable($data['scheduled_end']),
            new \DateTimeImmutable($data['actual_start']),
            new \DateTimeImmutable($data['actual_end'])
        );
    }
}
