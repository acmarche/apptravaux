<?php

namespace AcMarche\Avaloir\Command;

use AcMarche\Avaloir\Repository\AvaloirRepository;
use AcMarche\Travaux\Elastic\ElasticSearch;
use AcMarche\Travaux\Elastic\ElasticServer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ElasticCommand extends Command
{
    protected static $defaultName = 'avaloir:elastic';
    /**
     * @var ElasticServer
     */
    private $elasticServer;
    /**
     * @var AvaloirRepository
     */
    private $avaloirRepository;
    /**
     * @var ElasticSearch
     */
    private $elasticSearch;

    public function __construct(
        ElasticServer $elasticServer,
        ElasticSearch $elasticSearch,
        AvaloirRepository $avaloirRepository,
        string $name = null
    ) {
        parent::__construct($name);
        $this->elasticServer = $elasticServer;
        $this->avaloirRepository = $avaloirRepository;
        $this->elasticSearch = $elasticSearch;
    }

    protected function configure()
    {
        $this
            ->setDescription('Mise à jour du moteur de recherche')
            ->addOption('raz', null, InputOption::VALUE_NONE, 'Remise à zéro du moteur');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $raz = $input->getOption('raz');

        if ($raz) {
            try {
                $this->elasticServer->deleteIndex();
                $this->elasticServer->createIndex();
                $this->elasticServer->close();
                $this->elasticServer->updateSettings();
                $this->elasticServer->open();
                $this->elasticServer->updateMappings();
            } catch (\Exception $e) {
                $io->error($e->getMessage());
            }
        }

        //$result = $this->elasticSearch->search("500km", 50.22403140, 5.29429060);

        $this->updateAvaloirs();

        return 0;
    }

    private function updateAvaloirs()
    {
        foreach ($this->avaloirRepository->findAll() as $avaloir) {
            $result = $this->elasticServer->updateData($avaloir);
            var_dump($result);
        }
        //$this->elasticServer->getClient()->indices()->refresh();
        return [];
    }
}
