<?php

namespace AcMarche\Avaloir\Command;

use AcMarche\Avaloir\Entity\Avaloir;
use AcMarche\Avaloir\Image\ImageService;
use AcMarche\Avaloir\Location\LocationReverseInterface;
use AcMarche\Avaloir\Repository\AvaloirRepository;
use AcMarche\Stock\Service\SerializeApi;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
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
     * @var MailerInterface
     */
    private $mailer;
    /**
     * @var SerializeApi
     */
    private $serializeApi;
    /**
     * @var SymfonyStyle
     */
    private $io;
    /**
     * @var ImageService
     */
    private $imageService;
    /**
     * @var string|null
     */
    private $name;

    public function __construct(
        AvaloirRepository $avaloirRepository,
        LocationReverseInterface $locationReverse,
        MailerInterface $mailer,
        SerializeApi $serializeApi,
        ImageService $imageService,
        string $name = null
    ) {
        parent::__construct($name);
        $this->avaloirRepository = $avaloirRepository;
        $this->locationReverse = $locationReverse;
        $this->mailer = $mailer;
        $this->serializeApi = $serializeApi;
        $this->imageService = $imageService;
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
        $this->imageService->rotateImage('/home/jfsenechal/Bureau/avaloirs/36/aval-36.jpg');

//        $this->testLocation($input->getArgument('latitude'), $input->getArgument('longitude'));

        return 0;
        $avaloirs = $this->avaloirRepository->findAll();

        foreach ($avaloirs as $avaloir) {
            //$this->serializeApi->serializeAvaloir($avaloir);
            if (!$avaloir->getRue()) {
                try {
                    $result = $this->locationReverse->reverse($avaloir->getLatitude(), $avaloir->getLongitude());
                    $avaloir->setLocalite($this->locationReverse->getLocality());
                    $avaloir->setRue($this->locationReverse->getRoad());
                } catch (\Exception $e) {
                    $this->sendemail($result);
                }
            }
        }

        $this->avaloirRepository->flush();

        return 0;
    }

    protected function testLocation(string $latitude, string $longitude)
    {
        $result = $this->locationReverse->reverse($latitude, $longitude);
        $this->io->writeln($this->locationReverse->getRoad());
        $this->io->writeln($this->locationReverse->getLocality());
        //  print_r($result);
    }

    protected function sendemail(array $result)
    {
        $mail = (new TemplatedEmail())
            ->subject('[Avaloir] reverse error')
            ->from("webmaster@marche.be")
            ->to("webmaster@marche.be")
            ->textTemplate("@AcMarcheAvaloir/mail/reverse.txt.twig")
            ->context(['result' => $result]);

        try {
            $this->mailer->send($mail);
        } catch (TransportExceptionInterface $e) {
        }
    }
}
