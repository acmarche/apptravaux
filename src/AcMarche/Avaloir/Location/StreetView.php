<?php
namespace AcMarche\Avaloir\Location;

use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class StreetView
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
     * @var string
     */
    private $size;
    /**
     * @var ?int
     */
    private $heading;
    /**
     * @var int
     */
    private $fov;
    /**
     * @var int
     */
    private $pitch;
    /**
     * @var string
     */
    private $key;

    public function __construct(string $apiKeyGoogle)
    {
        $this->baseUrl = "https://maps.googleapis.com/maps/api/streetview";
        $this->client = HttpClient::create();
        $this->size = "1024x768";
        $this->heading = null; //0 => 360 90 =>EST, 180 => SUD
        $this->fov = 90; //zoom 1 => 120
        $this->pitch = 0; //90 vers le haut, -90 vers le bas
        $this->key = $apiKeyGoogle;
    }

    public function getPhoto($latitude, $longitude)
    {
        $query = [
            'key' => $this->key,
            'location' => "$latitude, $longitude",
            'size' => $this->size,
            'fov' => $this->fov,
            'pitch' => $this->pitch
        ];

        if ($this->heading) {
            $query['heading'] = $this->heading;
        }

        try {
            $request = $this->client->request(
                'GET',
                $this->baseUrl,
                [
                    'query' => $query
                ]
            );
        } catch (TransportExceptionInterface $e) {
        }

        try {
            return $request->getContent();
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


}