<?php

namespace AcMarche\Avaloir\Tests\Controller;

use AcMarche\Avaloir\Entity\Rue;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RvQuartierControllerTest extends BaseUnit
{
    public function testIndex()
    {
        $crawler = $this->client->request('GET', '/avaloirs/quartier/');
        $this->assertEquals(
            200,
            $this->client->getResponse()->getStatusCode()
        );
    }

    public function testAdd()
    {
        $crawler = $this->client->request('GET', '/avaloirs/quartier/new');
        $this->assertEquals(
            200,
            $this->client->getResponse()->getStatusCode()
        );

        $form = $crawler->selectButton('Ajouter')->form(
            array(
                'quartier[nom]' => 'Les Carme',
            )
        );

        $this->client->submit($form);
        $crawler = $this->client->followRedirect();

        $this->assertGreaterThan(
            0,
            $crawler->filter('td:contains("Les Carme")')->count(),
            'Missing element td:contains("Les Carme")'
        );
    }

    public function testEdit()
    {
        $crawler = $this->client->request('GET', '/avaloirs/quartier/');
        $this->assertEquals(
            200,
            $this->client->getResponse()->getStatusCode(),
            "Unexpected HTTP status code for GET /quartier"
        );
        $crawler = $this->client->click($crawler->selectLink('Les Carme')->link());
        $crawler = $this->client->click($crawler->selectLink('Editer')->link());

        $form = $crawler->selectButton('Mettre à jour')->form(
            array(
                'quartier[nom]' => 'Les Carmes',
            )
        );

        $this->client->submit($form);
        $crawler = $this->client->followRedirect();

        $this->assertGreaterThan(
            0,
            $crawler->filter('td:contains("Les Carmes")')->count(),
            'Missing element td:contains("Les Carmes")'
        );
    }

    public function testAddRues()
    {
        $rue = $this->getRues();

        $crawler = $this->client->request('GET', '/avaloirs/quartier/');
        $crawler = $this->client->click($crawler->selectLink('Les Carmes')->link());
        $crawler = $this->client->click($crawler->selectLink('Ses rues')->link());

        $form = $crawler->selectButton('Mettre à jour')->form(
            array(
                'quartier_rue[rueids]' => $rue->getId(),
                'quartier_rue[tokenfield]' => 'zeze',
            )
        );

        $this->client->submit($form);
        $crawler = $this->client->followRedirect();

        $this->assertGreaterThan(
            0,
            $crawler->filter('td:contains("Rue Victor Libert")')->count(),
            'Missing element td:contains("Rue Victor Libert")'
        );
    }

    public function testAssocRueToAvaloir()
    {
        $rue = $this->getRues();

        $crawler = $this->client->request('GET', '/avaloirs/avaloir/new');
        $this->assertEquals(
            200,
            $this->client->getResponse()->getStatusCode()
        );

        $form = $crawler->selectButton('Ajouter')->form(
            array(
                'avaloir[dates][0][jour]' => '09/06/2015',
                'avaloir[numero]' => 666,
                'avaloir[rue]' => $rue->getNom(),
                'avaloir[rueId]' => $rue->getId(),
            )
        );

        $this->client->submit($form);
        $crawler = $this->client->followRedirect();

        $this->assertGreaterThan(
            0,
            $crawler->filter('td:contains("Chemin des Lucioles")')->count(),
            'Missing element td:contains("Chemin des Lucioles")'
        );
    }

    public function testAddDate()
    {
        $crawler = $this->client->request('GET', '/avaloirs/quartier/');
        $crawler = $this->client->click($crawler->selectLink('Les Carmes')->link());
        $crawler = $this->client->click($crawler->selectLink('Ajouter une date de nettoyage')->link());

        $form = $crawler->selectButton('Ajouter')->form(
            array(
                'nettoyage_quartier[jour]' => '06/11/2015',
            )
        );

        $this->client->submit($form);
        $crawler = $this->client->followRedirect();

        $this->assertGreaterThan(
            0,
            $crawler->filter('td:contains("06-11-2015")')->count()
        );
    }

}
