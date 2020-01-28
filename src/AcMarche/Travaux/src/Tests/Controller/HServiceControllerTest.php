<?php

namespace AcMarche\Travaux\Tests\Controller;

class HServiceControllerTest extends BaseUnit
{
    public function testIndex()
    {
        $crawler = $this->admin->request('GET', '/service/');
        $this->assertEquals(200, $this->admin->getResponse()->getStatusCode(), "Unexpected HTTP status code for GET /service/");
    }

    public function testAdd()
    {
        $crawler = $this->admin->request('GET', '/service/new');
        $this->assertEquals(200, $this->admin->getResponse()->getStatusCode(), "Unexpected HTTP status code for GET /service/new");

        $form = $crawler->selectButton('Ajouter')->form(array(
            'service[intitule]' => 'Crames',
        ));

        $this->admin->submit($form);
        $crawler = $this->admin->followRedirect();

        $this->assertGreaterThan(0, $crawler->filter('h3:contains("Crames")')->count(), 'Missing element h3:contains("Crames")');
    }

    public function testEdit()
    {
        $crawler = $this->admin->request('GET', '/service/crames');
        $this->assertEquals(200, $this->admin->getResponse()->getStatusCode(), "Unexpected HTTP status code for GET /service/crames");

        $this->assertGreaterThan(0, $crawler->filter('h3:contains("Crames")')->count(), 'Missing element h3:contains("Crames")');

        $crawler = $this->admin->click($crawler->selectLink('Editer')->link());

        $form = $crawler->selectButton('Mettre à jour')->form(array(
            'service[intitule]' => 'Carmes',
        ));

        $this->admin->submit($form);
        $crawler = $this->admin->followRedirect();

        $this->assertGreaterThan(0, $crawler->filter('h3:contains("Carmes")')->count(), 'Missing element h3:contains("Carmes")');
    }

    public function testAddEnseignement()
    {
        $crawler = $this->admin->request('GET', '/service/new');
        $this->assertEquals(200, $this->admin->getResponse()->getStatusCode(), "Unexpected HTTP status code for GET /service/new");

        $form = $crawler->selectButton('Ajouter')->form(array(
            'service[intitule]' => 'Enseignement',
        ));

        $this->admin->submit($form);
        $crawler = $this->admin->followRedirect();

        $this->assertGreaterThan(0, $crawler->filter('h3:contains("Enseignement")')->count(), 'Missing element h3:contains("Enseignement")');
    }
}
