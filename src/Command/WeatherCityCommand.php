<?php

namespace App\Command;

use App\Repository\LocationRepository;
use App\Service\WeatherUtil;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'weather:city',
    description: 'Fetches weather data for a given city and country code',
)]
class WeatherCityCommand extends Command
{
    public function __construct(
        private readonly LocationRepository $locationRepository,
        private readonly WeatherUtil $weatherUtil,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('country_code', InputArgument::REQUIRED, 'ISO code of the country (e.g., "PL")')
            ->addArgument('city_name', InputArgument::REQUIRED, 'Name of the city (e.g., "Szczecin")');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $countryCode = strtoupper($input->getArgument('country_code'));
        $cityName = ucfirst(strtolower($input->getArgument('city_name')));

        $location = $this->locationRepository->findOneBy([
            'country' => $countryCode,
            'city' => $cityName,
        ]);

        if (!$location) {
            $io->error(sprintf('Location not found for city "%s" in country "%s".', $cityName, $countryCode));
            return Command::FAILURE;
        }

        $measurements = $this->weatherUtil->getWeatherForLocation($location);

        $io->success(sprintf('Weather data for %s, %s:', $location->getCity(), $location->getCountry()));
        foreach ($measurements as $measurement) {
            $io->writeln(sprintf(
                "  Date: %s | Temperature: %s°C",
                $measurement->getDate()->format('Y-m-d'),
                $measurement->getCelsius()
            ));
        }

        return Command::SUCCESS;
    }
}
