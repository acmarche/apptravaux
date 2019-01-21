<?php

namespace AcMarche\Travaux\Command;

use AcMarche\Travaux\Repository\InterventionRepository;
use AcMarche\Travaux\Service\Mailer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

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
        Mailer $mailer,
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
                $path = $root.DIRECTORY_SEPARATOR.$intervention->getId();
                $fullPath = $path.DIRECTORY_SEPARATOR.$document->getFilename();

                if (!is_readable($fullPath)) {
                    $output->writeln((string)$fullPath);
                    $this->mailer->send(
                        "webmaster@marche.be",
                        "webmaster@marche.be",
                        "travaux fichier manquant",
                        $fullPath
                    );
                }
            }
        }
    }
}
