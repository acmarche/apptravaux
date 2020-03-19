<?php

namespace AcMarche\Avaloir\Command;

use AcMarche\Avaloir\Location\LocationReverse;
use AcMarche\Avaloir\Repository\AvaloirNewRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class AvaloirLocationCommand extends Command
{
    protected static $defaultName = 'avaloir:location';

    /**
     * @var AvaloirNewRepository
     */
    private $avaloirRepository;
    /**
     * @var LocationReverse
     */
    private $locationReverse;

    public function __construct(
        AvaloirNewRepository $avaloirRepository,
        LocationReverse $locationReverse,
        string $name = null
    ) {
        parent::__construct($name);
        $this->avaloirRepository = $avaloirRepository;
        $this->locationReverse = $locationReverse;
    }

    protected function configure()
    {
        $this
            ->setDescription('Reverse address');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $avaloirs = $this->avaloirRepository->findAll();

        foreach ($avaloirs as $avaloir) {
            if (!$avaloir->getRue()) {
                $result = $this->locationReverse->reverse($avaloir->getLatitude(), $avaloir->getLongitude());
                //var_dump($result);
                if (!isset($result['error'])) {
                    $adresse = $result['address'];
                    $avaloir->setRue($adresse['road']);
                    $avaloir->setLocalite($adresse['town']);
                } else {
                    $io->error($result['message']);
                }
            }
        }

        $this->avaloirRepository->flush();

        return 0;
    }
}
