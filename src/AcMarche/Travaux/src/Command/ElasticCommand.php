<?php

namespace AcMarche\Travaux\Command;

use AcMarche\Avaloir\Repository\AvaloirNewRepository;
use AcMarche\Avaloir\Repository\AvaloirRepository;
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

    public function __construct(ElasticServer $elasticServer, AvaloirNewRepository $avaloirRepository)
    {
        parent::__construct();
        $this->elasticServer = $elasticServer;
        $this->avaloirRepository = $avaloirRepository;
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
            $this->elasticServer->deleteIndex();
            $this->elasticServer->createIndex();
            $this->elasticServer->close();
            $this->elasticServer->updateSettings();
            $this->elasticServer->open();
            $this->elasticServer->updateMappings();
        }
        $this->updateAvaloirs();
        return 0;
    }

    private function updateAvaloirs()
    {
        foreach ($this->avaloirRepository->findAll() as $avaloir) {
            $data = [
                'index' => 'avaloir',
                'id' => $avaloir->getId(),
                'body' => [
                    'location' => ['lat' => $avaloir->getLatitude(), 'lon' => $avaloir->getLongitude()],
                    'description' => $avaloir->getDescription()
                ]
            ];
            $this->elasticServer->updateData($data);
        }
        $this->elasticServer->getClient()->indices()->refresh();
    }
}
