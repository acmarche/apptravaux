<?php

namespace AcMarche\Travaux\Tests\Controller;

class ZDeleteControllerTest extends BaseUnit
{
    public function testDeleteBatiment()
    {
        $batiment = $this->getBatiment('Les Carmes');
        $crawler = $this->admin->request('GET', '/batiment/'.$batiment->getId());
        $this->assertEquals(200, $this->admin->getResponse()->getStatusCode());

        $this->admin->submit($crawler->selectButton('Supprimer')->last()->form());

        $this->admin->followRedirect();

        $this->assertNotRegExp('/Carmes/', "Pas sur supprimer les carmes");
    }

    public function testDeleteDomaine()
    {
        $domaine = $this->getDomaine('Eaux');
        $crawler = $this->admin->request('GET', '/domaine/'.$domaine->getId());
        $this->assertEquals(200, $this->admin->getResponse()->getStatusCode());

        $this->admin->submit($crawler->selectButton('Supprimer')->last()->form());
        $this->admin->followRedirect();

        $this->assertNotRegExp('/Eaux/', "Pas su supprimer eaux");
    }

    public function testDeleteService()
    {
        $service = $this->getService('Carmes');
        $crawler = $this->admin->request('GET', '/service/'.$service->getId());
        $this->assertEquals(200, $this->admin->getResponse()->getStatusCode());

        $this->admin->submit($crawler->selectButton('Supprimer')->last()->form());
        $crawler = $this->admin->followRedirect();

        $this->assertEquals(0, $crawler->filter('td:contains("Carmes")')->count());
    }

    public function testDeleteCategorie()
    {
        $categorie = $this->getCategorie('Ma categorie');
        $crawler = $this->admin->request('GET', '/categorie/'.$categorie->getId());
        $this->assertEquals(200, $this->admin->getResponse()->getStatusCode());

        $this->admin->submit($crawler->selectButton('Supprimer')->last()->form());
        // print_r($this->admin->getResponse()->getContent());
        $this->admin->followRedirect();

        $this->assertNotRegExp('/Ma catégorie/', "Impossible supprimer categorie");
    }
}
