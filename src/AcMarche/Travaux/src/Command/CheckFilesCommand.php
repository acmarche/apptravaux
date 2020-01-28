<?php

namespace AcMarche\Travaux\Command;

use AcMarche\Travaux\Repository\InterventionRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;

class CheckFilesCommand extends Command
{
    private $mailer;
    /**
     * @var InterventionRepository
     */
    private $interventionRepository;
    /**
     * @var ParameterBagInterface
     */
    private $parameterBag;

    public function __construct(
        MailerInterface $mailer,
        InterventionRepository $interventionRepository,
        ParameterBagInterface $parameterBag
    ) {
        $this->mailer = $mailer;
        parent::__construct();
        $this->interventionRepository = $interventionRepository;
        $this->parameterBag = $parameterBag;
    }

    protected function configure()
    {
        $this
            ->setName('actravaux:checkfiles')
            ->setDescription('Verifie les pieces jointes');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $root = $this->parameterBag->get('ac_marche_travaux.upload.directory');

        $interventions = $this->interventionRepository->findAll();
        foreach ($interventions as $intervention) {
            foreach ($intervention->getDocuments() as $document) {
                $path = $root . DIRECTORY_SEPARATOR . $intervention->getId();
                $fullPath = $path . DIRECTORY_SEPARATOR . $document->getFilename();

                if (!is_readable($fullPath)) {
                    $mail = (new TemplatedEmail())
                        ->subject('Travaux fichier manquant')
                        ->from("webmaster@marche.be")
                        ->to("webmaster@marche.be")
                        ->text("travaux fichier manquant: " . $fullPath);

                    try {
                        $this->mailer->send($mail);
                    } catch (TransportExceptionInterface $e) {
                        $output->writeln('error mail' . $e->getMessage());
                    }

                    $output->writeln((string)$fullPath);
                }
            }
        }

        return 0;
    }
}
