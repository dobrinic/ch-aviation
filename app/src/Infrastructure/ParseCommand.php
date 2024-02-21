<?php

namespace App\Infrastructure;

use App\Enum\AirportsLookup;
use App\Factory\FlightFactory;
use App\Service\FlightAnalysisService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'parse')]
class ParseCommand extends Command
{
    private const CSV_PATH = '/app/var/input.jsonl';

    public function __construct(private FlightAnalysisService $flightAnalysis)
    {
        parent::__construct();
    }
    

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Analysis starting');
        
        $flights = $this->createFromJsonLineFile(self::CSV_PATH, $io);

        if (empty($flights)) {
            $io->warning('There are no well formatted flights in given dataset.');
            return Command::FAILURE;
        }

        $threeLongestFlights = $this->flightAnalysis->findTopThreeLongestFlights($flights);
        $mostMissedAirline = $this->flightAnalysis->findMostMissedAirline($flights);
        $mostOvernightStaysDestination = $this->flightAnalysis->findMostOvernightStaysDestination($flights);
        $mostOvernightStaysCity = AirportsLookup::tryFrom($mostOvernightStaysDestination)?->city() ?? 'Unknown City'; //TODO: this could be better with try-catch to warn developers their data source is incomplite
        $phoneticString = $this->flightAnalysis->spellStringPhonetically('HRV');

        $this->formatTopThreeLongestFlights($threeLongestFlights, $io);
        $io->newLine();
        $io->text(sprintf('The airline with the most missed landings is: %s', $mostMissedAirline));
        $io->newLine();
        $io->text(sprintf('The destination with the most overnight stays is: %s', $mostOvernightStaysCity));
        $io->newLine();
        $io->text(['If I had to spell my country code over the radio it would sound something like this:', $phoneticString]);
        
        return Command::SUCCESS;
    }

    private function createFromJsonLineFile(string $filePath, SymfonyStyle $io): array
    {
        $flights = [];

        $file = fopen($filePath, 'r');

        if ($file) {
            $io->progressStart();
            while (($line = fgets($file)) !== false) {
                try {
                    $flights[] = FlightFactory::createFromJsonLine($line);
                } catch (\Throwable $th) {
                    // log $th->getMessage() and continue skipping that flight
                }
                $io->progressAdvance();
            }

            fclose($file);

            $io->progressFinish();
        }

        return $flights;
    }

    private function formatTopThreeLongestFlights(array $flights, SymfonyStyle $io): void
    {
        $io->text('Three longest flights were:');
        foreach ($flights as $key => $flight) {
            $io->text(sprintf('%d. %s fligth with rgeistratin number: %s from %s to %s.',
                ++$key,
                $flight->getAirplane()->getAirline()->getName(),
                $flight->getAirplane()->getRegistration(),
                $flight->getFrom(),
                $flight->getTo(),
            ));
        }
    }
}