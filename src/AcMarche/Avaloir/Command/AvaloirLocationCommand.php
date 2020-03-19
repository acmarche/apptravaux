<?php

namespace AcMarche\Avaloir\Command;

use AcMarche\Avaloir\Location\LocationReverse;
use AcMarche\Avaloir\Repository\AvaloirNewRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;

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
    /**
     * @var MailerInterface
     */
    private $mailer;

    public function __construct(
        AvaloirNewRepository $avaloirRepository,
        LocationReverse $locationReverse,
        MailerInterface $mailer,
        string $name = null
    ) {
        parent::__construct($name);
        $this->avaloirRepository = $avaloirRepository;
        $this->locationReverse = $locationReverse;
        $this->mailer = $mailer;
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
                if (!isset($result['error'])) {
                    $adresse = $result['address'];
                    if (isset($adresse['road'])) {
                        $avaloir->setRue($adresse['road']);
                    } else {
                        $this->sendemail($result);
                    }
                    $avaloir->setLocalite($adresse['town']);
                } else {
                    $this->sendemail($result);
                }
            }
        }

        //   $this->avaloirRepository->flush();

        return 0;
    }

    protected function sendemail(array $result)
    {
        $mail = (new TemplatedEmail())
            ->subject('Travaux reverse error')
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
