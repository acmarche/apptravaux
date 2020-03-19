<?php


namespace AcMarche\Avaloir\Location;


use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class LocationReverse
{
    public function __construct()
    {
        $this->baseUrl = 'https://nominatim.openstreetmap.org/reverse?format=json&zoom=18&addressdetails=1&namedetails=1&extratags=1';
        $this->client = HttpClient::create();
    }

    public function reverse($latitude, $longitude): array
    {
        try {
            $request = $this->client->request(
                'GET',
                $this->baseUrl,
                [
                    'query' => [
                        'lat' => $latitude,
                        'lon' => $longitude,
                    ]
                ]
            );
        } catch (TransportExceptionInterface $e) {
        }

        try {
            return json_decode($request->getContent(), true);
        } catch (ClientExceptionInterface $e) {
            return $this->createError($e->getMessage());
        } catch (RedirectionExceptionInterface $e) {
            return $this->createError($e->getMessage());
        } catch (ServerExceptionInterface $e) {
            return $this->createError($e->getMessage());
        } catch (TransportExceptionInterface $e) {
            return $this->createError($e->getMessage());
        }
    }

    protected function createError(string $message)
    {
        return ['error' => true, 'message' => $message];
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