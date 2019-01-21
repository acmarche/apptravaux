<?php

namespace AcMarche\Avaloir\Tests\Controller;

class DateNettoyageControllerTest extends BaseUnit
{
    public function testAddDate()
    {
        $crawler = $this->client->request('GET', '/avaloirs/avaloir/');
        $this->assertEquals(
            200,
            $this->client->getResponse()->getStatusCode()
        );
        $crawler = $this->client->click($crawler->selectLink('Chemin des Lucioles 999')->link());
        $crawler = $this->client->click($crawler->selectLink('Ajouter une date de nettoyage')->link());

        $form = $crawler->selectButton('Ajouter')->form(
            array(
                'date_nettoyage[jour]' => '01/11/2015',
            )
        );

        $this->client->submit($form);
        $crawler = $this->client->followRedirect();

        $this->assertGreaterThan(
            0,
            $crawler->filter('li:contains("01-11-2015")')->count(),
            'Missing element li:contains("01-11-2015")'
        );
    }
}
