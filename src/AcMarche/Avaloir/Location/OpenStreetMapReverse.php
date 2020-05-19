<?php

namespace AcMarche\Avaloir\Location;

use Symfony\Component\HttpClient\Exception\ClientException;
use Symfony\Component\HttpClient\HttpClient;

/**
 * https://nominatim.org/release-docs/develop/api/Overview/
 * Class OpenStreetMapReverse
 * @package AcMarche\Avaloir\Location
 */
class OpenStreetMapReverse implements LocationReverseInterface
{
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

    public function __construct()
    {
        $this->baseUrl = 'https://nominatim.openstreetmap.org/reverse';
        $this->client = HttpClient::create();
    }

    /**
     * @param $latitude
     * @param $longitude
     * @return array
     * @throws \Exception
     */
    public function reverse($latitude, $longitude): array
    {
        sleep(1);//policy
        try {
            $request = $this->client->request(
                'GET',
                $this->baseUrl,
                [
                    'query' => [
                        'format' => 'json',
                        'zoom' => 16,
                        'addressdetails' => 1,
                        'namedetails' => 0,
                        'extratags' => 0,
                        'lat' => $latitude,
                        'lon' => $longitude,
                    ],
                ]
            );

            $this->result = json_decode($request->getContent(), true);

            return $this->result;
        } catch (ClientException $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function getRoad(): string
    {
        return $this->extractRoad();
    }

    protected function extractRoad(): ?string
    {
        $address = $this->result['address'];

        if (isset($address['road'])) {
            return $address['road'];
        }

        if (isset($address['pedestrian'])) {
            return $address['pedestrian'];
        }

        return null;
    }

    public function getLocality(): string
    {
        return $this->result['address']['town'];
    }

    /**
     * {
     * "place_id":188259342,
     * "licence":"Data © OpenStreetMap contributors, ODbL 1.0. https://osm.org/copyright",
     * "osm_type":"way",
     * "osm_id":458163018,
     * "lat":"50.23603135598228",
     * "lon":"5.36188848497033",
     * "display_name":"Chaussée de l'Ourthe, Marche-en-Famenne, Luxembourg, Wallonie, 6900, België - Belgique - Belgien",
     * "address":{
     * "road":"Chaussée de l'Ourthe",
     * "town":"Marche-en-Famenne",
     * "county":"Luxembourg",
     * "state":"Wallonie",
     * "postcode":"6900",
     * "country":"België - Belgique - Belgien",
     * "country_code":"be"
     * },
     * "boundingbox":[
     * "50.23454",
     * "50.2394055",
     * "5.3576441",
     * "5.3723272"
     * ]
     * }
     */
}
