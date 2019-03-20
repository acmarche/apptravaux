<?php

namespace AcMarche\Travaux\Tests\Controller;

class ZDeleteControllerTest extends BaseUnit
{
    public function testDeleteBatiment()
    {
        $crawler = $this->admin->request('GET', '/batiment/les-carmes');
        $this->assertEquals(200, $this->admin->getResponse()->getStatusCode());

        $this->admin->submit($crawler->selectButton('Supprimer')->last()->form());

        $this->admin->followRedirect();

        $this->assertNotRegExp('/Carmes/', "Pas sur supprimer les carmes");
    }

    public function testDeleteDomaine()
    {
        $crawler = $this->admin->request('GET', '/domaine/eaux');
        $this->assertEquals(200, $this->admin->getResponse()->getStatusCode());

        $this->admin->submit($crawler->selectButton('Supprimer')->last()->form());
        $this->admin->followRedirect();

        $this->assertNotRegExp('/Eaux/', "Pas su supprimer eaux");
    }

    public function testDeleteService()
    {
        $crawler = $this->admin->request('GET', '/service/carmes');
        $this->assertEquals(200, $this->admin->getResponse()->getStatusCode());

        $this->admin->submit($crawler->selectButton('Supprimer')->last()->form());
        $crawler = $this->admin->followRedirect();

        $this->assertEquals(0, $crawler->filter('td:contains("Carmes")')->count());
    }

    public function testDeleteCategorie()
    {
        $crawler = $this->admin->request('GET', '/categorie/ma_categorie');
        $this->assertEquals(200, $this->admin->getResponse()->getStatusCode());

        $this->admin->submit($crawler->selectButton('Supprimer')->last()->form());
        // print_r($this->admin->getResponse()->getContent());
        $this->admin->followRedirect();

        $this->assertNotRegExp('/Ma catégorie/', "Impossible supprimer categorie");
    }
}
