<?php

namespace AcMarche\Travaux\Tests\Controller;

class SuivisControllerTest extends BaseUnit
{
    public function testAdd()
    {
        $crawler = $this->admin->request('GET', '/intervention/');
        $this->assertEquals(200, $this->admin->getResponse()->getStatusCode());

        $crawler = $this->admin->click($crawler->selectLink('Test de travail')->link());

        $this->assertGreaterThan(0, $crawler->filter('h3:contains("Test de travail")')->count());
        
        $crawler = $this->admin->click($crawler->selectLink('Ajouter un suivi')->link());

        $form = $crawler->selectButton('Ajouter')->form();

        $form['suivi[descriptif]'] = 'Il faut mettre un bouchon';

        $this->admin->submit($form);
        $crawler = $this->admin->followRedirect();

        $this->assertGreaterThan(0, $crawler->filter('div.panel-body:contains("bouchon")')->count());
    }

    public function testEdit()
    {
        $crawler = $this->admin->request('GET', '/intervention/');
        $this->assertEquals(200, $this->admin->getResponse()->getStatusCode());

        $crawler = $this->admin->click($crawler->selectLink('Test de travail')->link());
       
        $crawler = $this->admin->click($crawler->selectLink('Editer')->last()->link());

        $form = $crawler->selectButton('Mettre à jour')->form(array(
            'suivi[descriptif]' => 'Mettre des bouchons', //divers
        ));

        $this->admin->submit($form);
        $crawler = $this->admin->followRedirect();

        $this->assertGreaterThan(0, $crawler->filter('div.panel-body:contains("bouchons")')->count());
    }
}
