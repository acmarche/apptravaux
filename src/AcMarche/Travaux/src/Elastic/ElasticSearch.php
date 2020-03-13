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

    /**
     * @param string $distance
     * @param float $latitude
     * @param float $longitude
     * @return array
     */
    public function search(string $distance, $latitude, $longitude)
    {
        $json = '{
     "query": {
        "bool" : {
            "must" : {
                "match_all" : {}
            },
            "filter" : {
                "geo_distance" : {
                    "distance" : "' . $distance . '",
                    "location" : {
                        "lat" : ' . $latitude . ',
                        "lon" : ' . $longitude . '
                    }
                }
            }
        }
    }
 }';
        $params = [
            'index' => 'avaloir',
            'body' => $json
        ];

        return $this->elasticServer->getClient()->search($params);
    }

    private function test()
    {
        $params = [
            'index' => 'avaloir',
            'body' => [
                'query' => [
                    'match_all' => (object)[]
                ],
                'filter' => [
                    'geo_distance' => [
                        'distance' => '50km',
                        'location' => [
                            'lat' => 50,
                            'lon' => 5
                        ]
                    ]
                ]
            ]
        ];
    }
}