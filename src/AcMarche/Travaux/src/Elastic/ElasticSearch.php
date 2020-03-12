<?php


namespace AcMarche\Travaux\Elastic;


class ElasticSearch
{
    /**
     * @var ElasticServer
     */
    private $elasticServer;

    public function __construct(ElasticServer $elasticServer)
    {
        $this->elasticServer = $elasticServer;
    }

    public function search(array $params) {
        $this->elasticServer->getClient()->search($params);
    }
}