<?php

namespace AcMarche\Avaloir\Command;

use AcMarche\Avaloir\Location\LocationReverseInterface;
use AcMarche\Avaloir\Location\LocationUpdater;
use AcMarche\Avaloir\MailerAvaloir;
use AcMarche\Avaloir\Repository\AvaloirRepository;
use AcMarche\Avaloir\Repository\RueRepository;
use AcMarche\Stock\Service\SerializeApi;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Mailer\MailerInterface;

class LocationCommand extends Command
{
    protected static $defaultName = 'avaloir:location';

    /**
     * @var AvaloirRepository
     */
    private $avaloirRepository;
    /**
     * @var LocationReverseInterface
     */
    private $locationReverse;
    /**
     * @var SerializeApi
     */
    private $serializeApi;
    /**
     * @var SymfonyStyle
     */
    private $io;
    /**
     * @var RueRepository
     */
    private $rueRepository;
    /**
     * @var MailerAvaloir
     */
    private $mailerAvaloir;
    /**
     * @var LocationUpdater
     */
    private $locationUpdater;

    public function __construct(
        AvaloirRepository $avaloirRepository,
        LocationReverseInterface $locationReverse,
        LocationUpdater $locationUpdater,
        string $name = null
    ) {
        parent::__construct($name);
        $this->avaloirRepository = $avaloirRepository;
        $this->locationReverse = $locationReverse;
        $this->locationUpdater = $locationUpdater;
    }

    protected function configure()
    {
        $this
            ->setDescription('Reverse address')
            ->addArgument('latitude')
            ->addArgument('longitude');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->io = new SymfonyStyle($input, $output);

      //   $avaloir = $this->avaloirRepository->find(2);
      //   $this->locationUpdater->updateRueAndLocalite($avaloir);

    //    $this->testLocation($input->getArgument('latitude'), $input->getArgument('longitude'));

        $this->reverseAll();

        return 0;
    }

    protected function testLocation(string $latitude, string $longitude)
    {
        $result = $this->locationReverse->reverse($latitude, $longitude);
        print_r(json_encode($result));
        $this->io->writeln($this->locationReverse->getRoad());
        $this->io->writeln($this->locationReverse->getLocality());
    }

    protected function reverseAll()
    {
        $avaloirs = $this->avaloirRepository->findAll();

        foreach ($avaloirs as $avaloir) {
            //$this->serializeApi->serializeAvaloir($avaloir);
            //  if (!$avaloir->getRue()) {
            $this->locationUpdater->updateRueAndLocalite($avaloir);
            // }
        }
    }
}
