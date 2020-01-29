<?php

namespace AcMarche\Travaux\Tests\Controller;

class CategorieControllerTest extends BaseUnit
{
    public function testIndex()
    {
        $this->admin->request('GET', '/categorie/');
        $this->assertEquals(200, $this->admin->getResponse()->getStatusCode(), "Unexpected HTTP status code for GET /categorie/");
    }

    public function testAdd()
    {
        $crawler = $this->admin->request('GET', '/categorie/new');
        $this->assertEquals(200, $this->admin->getResponse()->getStatusCode(), "Unexpected HTTP status code for GET /categorie/new");

        $form = $crawler->selectButton('Ajouter')->form(array(
            'categorie[intitule]' => 'Ma cat',
        ));

        $this->admin->submit($form);
        $crawler = $this->admin->followRedirect();

        $this->assertGreaterThan(0, $crawler->filter('h3:contains("Ma cat")')->count(), 'Missing element h3:contains("Ma cat")');
    }

    public function testEdit()
    {
        $categorie = $this->getCategorie('Ma cat');
        $crawler = $this->admin->request('GET', '/categorie/'.$categorie->getId());
        $this->assertEquals(200, $this->admin->getResponse()->getStatusCode(), "Unexpected HTTP status code for GET /categorie/ma_cat");
        //print_r($this->client->getResponse()->getContent());
        $this->assertGreaterThan(0, $crawler->filter('h3:contains("Ma cat")')->count(), 'Missing element h3:contains("Ma cat")');
        
        $crawler = $this->admin->click($crawler->selectLink('Editer')->link());

        $form = $crawler->selectButton('Mettre à jour')->form(array(
            'categorie[intitule]' => 'Ma catégorie',
        ));

        $this->admin->submit($form);
        $crawler = $this->admin->followRedirect();

        $this->assertGreaterThan(0, $crawler->filter('h3:contains("Ma catégorie")')->count(), 'Missing element h3:contains("Ma catégorie")');
    }
}
