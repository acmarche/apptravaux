<?php

namespace AcMarche\Avaloir\Tests\Controller;

use AcMarche\Avaloir\Entity\Rue;

class AvaloirControllerTest extends BaseUnit
{
    public function testIndex()
    {
        $crawler = $this->client->request('GET', '/avaloirs/avaloir/');
        $this->assertEquals(
            200,
            $this->client->getResponse()->getStatusCode()
        );
    }

    public function testAdd()
    {
        $rues = $this->entityManager->getRepository(Rue::class)->search(array('nom' => 'Lucioles'));
        $rue = $rues[0];

        $crawler = $this->client->request('GET', '/avaloirs/avaloir/new');
        $this->assertEquals(
            200,
            $this->client->getResponse()->getStatusCode()
        );

        $form = $crawler->selectButton('Ajouter')->form(
            array(
                'avaloir[dates][0][jour]' => '2015-01-10',
                'avaloir[numero]' => 999,
                'avaloir[rue]' => $rue->getNom(),
                'avaloir[rueId]' => $rue->getId(),
            )
        );

        $this->client->submit($form);
        $crawler = $this->client->followRedirect();

        $this->assertGreaterThan(
            0,
            $crawler->filter('td:contains("Chemin des Lucioles")')->count()
        );
    }

    public function testEdit()
    {
        $crawler = $this->client->request('GET', '/avaloirs/avaloir/');
        $this->assertEquals(
            200,
            $this->client->getResponse()->getStatusCode()
        );
        $crawler = $this->client->click($crawler->selectLink('Chemin des Lucioles 999')->link());
        $crawler = $this->client->click($crawler->selectLink('Editer')->link());

        $form = $crawler->selectButton('Mettre à jour')->form(
            array(
                'avaloir_edit[descriptif]' => 'Blabla',
            )
        );

        $this->client->submit($form);
        $crawler = $this->client->followRedirect();

        $this->assertGreaterThan(
            0,
            $crawler->filter('h3:contains("Chemin des Lucioles")')->count()
        );
    }

    public function testAddSansDate()
    {
        $rues = $this->entityManager->getRepository(Rue::class)->search(array('nom' => 'Forgeron'));
        $rue = $rues[0];

        $crawler = $this->client->request('GET', '/avaloirs/avaloir/new');
        $this->assertEquals(
            200,
            $this->client->getResponse()->getStatusCode()
        );

        $form = $crawler->selectButton('Ajouter')->form(
            array(
                'avaloir[numero]' => 155,
                'avaloir[rue]' => $rue->getNom(),
                'avaloir[rueId]' => $rue->getId(),
            )
        );

        $this->client->submit($form);
        $crawler = $this->client->followRedirect();

        $this->assertGreaterThan(
            0,
            $crawler->filter('td:contains("Rue des Forgerons")')->count()
        );
    }
}
