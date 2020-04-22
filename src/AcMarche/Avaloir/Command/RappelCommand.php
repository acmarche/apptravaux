<?php

namespace AcMarche\Avaloir\Command;

use AcMarche\Avaloir\Repository\AvaloirRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;

class RappelCommand extends Command
{
    protected static $defaultName = 'avaloir:rappel';
    /**
     * @var MailerInterface
     */
    private $mailer;
    /**
     * @var AvaloirRepository
     */
    private $avaloirRepository;
    /**
     * @var ParameterBagInterface
     */
    private $parameterBag;
    /**
     * @var string|null
     */
    private $name;

    public function __construct(
        MailerInterface $mailer,
        AvaloirRepository $avaloirRepository,
        ParameterBagInterface $parameterBag,
        string $name = null
    ) {
        parent::__construct($name);
        $this->mailer = $mailer;
        $this->avaloirRepository = $avaloirRepository;
        $this->parameterBag = $parameterBag;
        $this->name = $name;
    }

    protected function configure()
    {
        $this
            ->setDescription('Lance les rappels');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $destinataire = $this->parameterBag->get('ac_marche_avaloir_destinataire');

        $avaloirs = $this->avaloirRepository->findBy(['date_rappel' => new \DateTime()]);

        if ($avaloirs) {
            $mail = (new TemplatedEmail())
                ->subject("Rappel avaloir")
                ->from($destinataire)
                ->to($destinataire)
                ->textTemplate('@AcMarcheAvaloir/mail/rappel.txt.twig')
                ->context(
                    array(
                        'avaloirs' => $avaloirs,
                    )
                );

            try {
                $this->mailer->send($mail);
            } catch (TransportExceptionInterface $e) {
                $output->writeln('error mail'.$e->getMessage());
            }
        }

        return 0;
    }
}
