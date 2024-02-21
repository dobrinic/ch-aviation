<?php

namespace App\Service;

use App\Domain\Flight;
use App\Enum\PhoneticAlphabet;
use App\Infrastructure\AirlineLookup;

class FlightAnalysisService
{
    public function findTopThreeLongestFlights(array $flights): array
    {
        usort($flights, function (Flight $a, Flight $b) {
            return $b->getActualDuration() - $a->getActualDuration();
        });

        return array_slice($flights, 0, 3);
    }

    public function findMostMissedAirline(array $flights): string
    {
        $missedLandingsByAirline = [];

        /** @var Flight $flight */
        foreach ($flights as $flight) {
            if ($flight->wasLandingMissed()) {
                $reg = $flight->getAirplane()->getRegistration();
                $missedLandingsByAirline[$reg] = ($missedLandingsByAirline[$reg] ?? 0) + 1;
            }
        }

        if (empty($missedLandingsByAirline)) {
            return 'None! Everybody is apparently arriving on time.';
        }

        arsort($missedLandingsByAirline);

        $registration = array_key_first($missedLandingsByAirline);
        return AirlineLookup::from($registration);
    }

    public function findMostOvernightStaysDestination(array $flights): ?string
    {
        $destinationsOvernightStays = [];

        usort($flights, fn($a, $b) => strcmp(
            $a->getAirplane()->getRegistration(),
            $b->getAirplane()->getRegistration())
        );
        
        /** @var Flight $flight */
        foreach ($flights as $key => $flight) {

            $nextFlight = $flights[++$key] ?? null;

            if (
                $nextFlight &&
                $flight->getAirplane()->getRegistration() === $nextFlight->getAirplane()->getRegistration() &&
                $flight->getTo() === $nextFlight->getFrom() &&
                $this->flightHasOvernightStay($flight, $nextFlight)
                )
            {
                $destination = $flight->getTo();
                $destinationsOvernightStays[$destination] = ($destinationsOvernightStays[$destination] ?? 0) + 1;
            }
        }

        arsort($destinationsOvernightStays);

        return array_key_first($destinationsOvernightStays);
    }

    public function flightHasOvernightStay(Flight $incomingFlight, Flight $outgoingFlight): bool
    {
        $midnight = $outgoingFlight->getActualStart()->setTime(0, 0, 0);

        return $incomingFlight->getActualEnd() < $midnight && $outgoingFlight->getActualStart() > $midnight;
    }

    public function spellStringPhonetically(string $string): string
    {
        $len = strlen($string);
        $arr = [];

        for ($i = 0; $i < $len; $i++){
            $letter = strtoupper($string[$i]);
            $arr[] = PhoneticAlphabet::tryFrom($letter)?->word();
        }

        return implode(' ', $arr);
    }
}
