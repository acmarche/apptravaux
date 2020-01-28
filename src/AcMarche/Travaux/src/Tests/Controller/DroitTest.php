<?php

namespace AcMarche\Travaux\Tests\Controller;

use Symfony\Component\Panther\Client;

class DroitTest extends BaseUnit
{
    /**
     * @var Client
     */
    protected $client;

    public function testTravauxGest()
    {
        $this->client = $this->auteur;

        $this->getUrlsAnnexe(403);
        $this->getUrlsAdd(200);
    }

    public function testTravauxAdd()
    {
        $this->client = $this->contributeur;

        $this->getUrlsAnnexe(403);
        $this->getUrlsAdd(200);
    }

    public function testTravauxRead()
    {
        $this->client = $this->lecteur;

        $this->getUrlsAnnexe(403);
        $this->getUrlsAdd(403);
    }

    private function getUrlsAnnexe($result)
    {
        $this->client->request('GET', '/batiment/');
        $this->assertEquals($result, $this->client->getResponse()->getStatusCode(), "/batiment");

        $this->client->request('GET', '/service/');
        $this->assertEquals($result, $this->client->getResponse()->getStatusCode(), "/service");

        $this->client->request('GET', '/categorie/');
        $this->assertEquals($result, $this->client->getResponse()->getStatusCode(), "/categorie");

        $this->client->request('GET', '/domaine/');
        $this->assertEquals($result, $this->client->getResponse()->getStatusCode(), "/domaine");
    }

    private function getUrlsAdd($result = 403)
    {
        $this->client->request('GET', '/intervention/new');
        $this->assertEquals(
            $result,
            $this->client->getResponse()->getStatusCode(),
            "Unexpected HTTP status code for GET /travaux/new"
        );
    }
}
