<?php

namespace AcMarche\Avaloir\Tests\Controller;

class RueControllerTest extends BaseUnit
{
    public function testIndex()
    {
        $crawler = $this->client->request('GET', '/avaloirs/rue/');
        $this->assertEquals(
            200,
            $this->client->getResponse()->getStatusCode()
        );
    }

    public function testAdd()
    {
        $crawler = $this->client->request('GET', '/avaloirs/rue/new');
        $this->assertEquals(
            200,
            $this->client->getResponse()->getStatusCode()
        );

        $form = $crawler->selectButton('Ajouter')->form(
            array(
                'rue[nom]' => 'Rue Springfiel',
            )
        );

        $option = $crawler->filter('#rue_village option:contains("Aye")');
        $this->assertEquals(1, count($option), 'Aye');
        $village = $option->attr('value');
        $form['rue[village]']->select($village);

        $this->client->submit($form);
        $crawler = $this->client->followRedirect();

        $this->assertGreaterThan(
            0,
            $crawler->filter('h3:contains("Rue Springfiel")')->count()
        );
    }

    public function testEdit()
    {
        $crawler = $this->client->request('GET', '/avaloirs/rue/');
        $this->assertEquals(
            200,
            $this->client->getResponse()->getStatusCode(),
            "Unexpected HTTP status code for GET /rue"
        );
        $crawler = $this->client->click($crawler->selectLink('Rue Springfiel')->link());
        $crawler = $this->client->click($crawler->selectLink('Editer')->link());

        $form = $crawler->selectButton('Mettre à jour')->form(
            array(
                'rue[nom]' => 'Rue Springfield',
            )
        );

        $this->client->submit($form);
        $crawler = $this->client->followRedirect();

        $this->assertGreaterThan(
            0,
            $crawler->filter('td:contains("Rue Springfield")')->count()
        );
    }
}
