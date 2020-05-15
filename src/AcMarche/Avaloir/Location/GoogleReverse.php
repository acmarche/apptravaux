<?php


namespace AcMarche\Avaloir\Location;


use Symfony\Component\HttpClient\Exception\ClientException;
use Symfony\Component\HttpClient\HttpClient;

class GoogleReverse implements LocationReverseInterface
{
    private $url = 'https://maps.googleapis.com/maps/api/geocode/json?latlng=40.714224,-73.961452&key=YOUR_API_KEY';
    /**
     * @var string
     */
    private $apiKeyGoogle;
    /**
     * @var string
     */
    private $baseUrl;
    /**
     * @var \Symfony\Contracts\HttpClient\HttpClientInterface
     */
    private $client;
    /**
     * @var array
     */
    private $result = [];

    public function __construct(string $apiKeyGoogle)
    {
        $this->baseUrl = 'https://maps.googleapis.com/maps/api/geocode/json';
        $this->client = HttpClient::create();
        $this->apiKeyGoogle = $apiKeyGoogle;
    }

    public function reverse($latitude, $longitude): array
    {
        try {
            $request = $this->client->request(
                'GET',
                $this->baseUrl,
                [
                    'query' => [
                        'key' => $this->apiKeyGoogle,
                        'latlng' => $latitude.','.$longitude,
                    ],
                ]
            );

            $this->result = json_decode($request->getContent(), true);

            return $this->result;
        } catch (ClientException $e) {
            return ['error' => true, 'message' => $e->getMessage()];
        }
    }

    public function getRoad(): string
    {
        $results = $this->result['results'];
        $first = $results[0];
        $road = $first['address_components'][1]['long_name'];

        return $road;
    }

    public function getLocality(): string
    {
        $results = $this->result['results'];
        $first = $results[0];
        $road = $first['address_components'][2]['long_name'];

        return $road;
    }
}
