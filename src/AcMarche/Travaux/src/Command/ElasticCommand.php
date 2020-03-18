<?php

namespace AcMarche\Travaux\Command;

use AcMarche\Avaloir\Repository\AvaloirNewRepository;
use AcMarche\Avaloir\Repository\AvaloirRepository;
use AcMarche\Travaux\Elastic\ElasticSearch;
use AcMarche\Travaux\Elastic\ElasticServer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ElasticCommand extends Command
{
    protected static $defaultName = 'ElasticCommand';
    /**
     * @var ElasticServer
     */
    private $elasticServer;
    /**
     * @var AvaloirNewRepository
     */
    private $avaloirRepository;
    /**
     * @var ElasticSearch
     */
    private $elasticSearch;

    public function __construct(
        ElasticServer $elasticServer,
        ElasticSearch $elasticSearch,
        AvaloirNewRepository $avaloirRepository
    ) {
        parent::__construct();
        $this->elasticServer = $elasticServer;
        $this->avaloirRepository = $avaloirRepository;
        $this->elasticSearch = $elasticSearch;
    }

    protected function configure()
    {
        $this
            ->setDescription('Add a short description for your command');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $update = false;

        if ($update) {
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

        $result = $this->elasticSearch->search("500km", 50.22403140, 5.29429060);
      //  var_dump($result);
          $this->updateAvaloirs();

        return 0;
    }

    private function updateAvaloirs()
    {
        foreach ($this->avaloirRepository->findAll() as $avaloir) {
            $result = $this->elasticServer->updateData($avaloir);
            var_dump($result);
        }
        $this->elasticServer->getClient()->indices()->refresh();
        return [];
    }
}
