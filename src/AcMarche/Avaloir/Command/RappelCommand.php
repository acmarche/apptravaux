<?php

namespace AcMarche\Avaloir\Command;

use AcMarche\Avaloir\Repository\AvaloirRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;

class RappelCommand extends Command
{
    private $mailer;
    /**
     * @var AvaloirRepository
     */
    private $avaloirRepository;
    /**
     * @var ParameterBagInterface
     */
    private $parameterBag;

    private function __construct(
        MailerInterface $mailer,
        AvaloirRepository $avaloirRepository,
        ParameterBagInterface $parameterBag
    ) {
        $this->mailer = $mailer;
        parent::__construct();
        $this->avaloirRepository = $avaloirRepository;
        $this->parameterBag = $parameterBag;
    }

    protected function configure()
    {
        $this
            ->setName('acavaloir:rappel')
            ->setDescription('Verifie les pieces jointes');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $destinataire = $this->parameterBag->get('ac_marche_avaloir_destinataire');

        $avaloirs = $this->avaloirRepository->findBy(['date_rappel' => new \DateTime()]);

        if ($avaloirs) {
            $mail = (new TemplatedEmail())
                ->subject("Rappel avaloir")
                ->from($destinataire)
                ->to($destinataire)
                ->textTemplate('email/rappel.txt.twig')
                ->context(
                    array(
                        'avaloirs' => $avaloirs,
                    )
                );

            try {
                $this->mailer->send($mail);
            } catch (TransportExceptionInterface $e) {
                $output->writeln('error mail' . $e->getMessage());
            }
        }
        return 0;
    }
}
