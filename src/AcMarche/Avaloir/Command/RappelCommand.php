<?php

namespace AcMarche\Avaloir\Command;

use AcMarche\Avaloir\Repository\AvaloirRepository;
use AcMarche\Travaux\Service\Mailer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

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
    /**
     * @var \Twig_Environment
     */
    private $twigEnvironment;

    private function __construct(
        Mailer $mailer,
        AvaloirRepository $avaloirRepository,
        ParameterBagInterface $parameterBag,
        \Twig_Environment $twigEnvironment
    ) {
        $this->mailer = $mailer;
        parent::__construct();
        $this->avaloirRepository = $avaloirRepository;
        $this->parameterBag = $parameterBag;
        $this->twigEnvironment = $twigEnvironment;
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
            $body = $this->twigEnvironment->render(
                'email/rappel.txt.twig',
                array(
                    'avaloirs' => $avaloirs,
                )
            );

            $this->mailer->send(
                $destinataire,
                $destinataire,
                "Rappel avaloir",
                $body
            );
        }
    }
}
