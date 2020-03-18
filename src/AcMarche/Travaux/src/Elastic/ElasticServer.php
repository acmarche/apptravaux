<?php


namespace AcMarche\Travaux\Elastic;

use AcMarche\Avaloir\Entity\AvaloirNew;
use Elasticsearch\ClientBuilder;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class ElasticServer
{
    /**
     * @var \Elasticsearch\Client
     */
    private $client;

    /**
     * @var string
     */
    private $indexName;
    /**
     * @var ParameterBagInterface
     */
    private $parameterBag;

    /**
     * AcElasticServerManager constructor.
     * @param string $indexName
     * @throws \Exception
     */
    public function __construct(ParameterBagInterface $parameterBag)
    {
        $hosts = [
            $parameterBag->get('acmarche_travaux.elastic.host')
        ];

        try {
            $this->client = ClientBuilder::create()
                ->setHosts($hosts)
                ->build();
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }

        $this->indexName = $parameterBag->get('acmarche_travaux.elastic.index');

        $this->params = [
            'index' => $this->indexName,
        ];
        $this->parameterBag = $parameterBag;
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function createIndex()
    {
        try {
            return $this->client->indices()->create(['index' => $this->indexName]);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function updateSettings()
    {
        $params = $this->readParams('settings');
        try {
            return $this->client->indices()->putSettings($params);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function updateMappings()
    {
        $params = $this->readParams('mappings');
        try {
            return $this->client->indices()->putMapping($params);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * @return array|bool
     * @throws \Exception
     */
    public function deleteIndex()
    {
        $params = [
            'index' => $this->indexName,
        ];

        $exist = $this->client->indices()->exists($params);

        if ($exist) {
            try {
                return $this->client->indices()->delete($params);
            } catch (\Exception $e) {
                throw new \Exception($e->getMessage());
            }
        }

        return true;
    }

    /**
     * @return \Elasticsearch\Client
     */
    public function getClient(): \Elasticsearch\Client
    {
        return $this->client;
    }

    public function close()
    {
        try {
            return $this->client->indices()->close(['index' => $this->indexName]);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function open()
    {
        try {
            return $this->client->indices()->open(['index' => $this->indexName]);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    private function readParams(string $fileName): array
    {
        switch ($fileName) {
            case 'settings':
                return json_decode(file_get_contents(__DIR__ . '/../../config/elastic/' . $fileName . '.json'), true);
                break;
            case 'mappings':
                return json_decode(file_get_contents(__DIR__ . '/../../config/elastic/' . $fileName . '.json'), true);
                break;
        }
        return [];
    }

    /**
     *
     * @return array
     * @throws \Exception
     */
    public function updateData(AvaloirNew $avaloir)
    {
        $data = [
            'index' => 'avaloir',
            'id' => $avaloir->getId(),
            'body' => [
                'id' => $avaloir->getId(),
                'location' => ['lat' => $avaloir->getLatitude(), 'lon' => $avaloir->getLongitude()],
                'description' => $avaloir->getDescription()
            ]
        ];

        try {
            return $this->client->index($data);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
}