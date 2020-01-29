<?php

namespace AcMarche\Travaux\Tests\Controller;

class DomaineControllerTest extends BaseUnit
{
    public function testIndex()
    {
        $crawler = $this->admin->request('GET', '/domaine/');
        $this->assertEquals(200, $this->admin->getResponse()->getStatusCode(), "Unexpected HTTP status code for GET /domaine/");
    }

    public function testAdd()
    {
        $crawler = $this->admin->request('GET', '/domaine/new');
        $this->assertEquals(200, $this->admin->getResponse()->getStatusCode(), "Unexpected HTTP status code for GET /domaine/new");
        
        $form = $crawler->selectButton('Ajouter')->form(array(
            'domaine[intitule]' => 'Eau',
        ));

        $this->admin->submit($form);
        $crawler = $this->admin->followRedirect();

        $this->assertGreaterThan(0, $crawler->filter('h3:contains("Eau")')->count(), 'Missing element h3:contains("Eau")');
    }

    public function testEdit()
    {
        $domaine = $this->getDomaine('Eau');
        $crawler = $this->admin->request('GET', '/domaine/'.$domaine->getId());
        $this->assertEquals(200, $this->admin->getResponse()->getStatusCode(), "Unexpected HTTP status code for GET /domaine/eau");

        $this->assertGreaterThan(0, $crawler->filter('h3:contains("Eau")')->count(), 'Missing element h3:contains("Eau")');

        $crawler = $this->admin->click($crawler->selectLink('Editer')->link());

        $form = $crawler->selectButton('Mettre à jour')->form(array(
            'domaine[intitule]' => 'Eaux',
        ));
        
        $this->admin->submit($form);
        $crawler = $this->admin->followRedirect();

        $this->assertGreaterThan(0, $crawler->filter('h3:contains("Eaux")')->count(), 'Missing element h3:contains("Eaux")');
    }
}
