<?php

namespace AcMarche\Travaux\Tests\Interventions;

use AcMarche\Travaux\Tests\Controller\BaseUnit;

class RedacteurControllerTest extends BaseUnit
{    

    public function testIndex()
    {
        $crawler = $this->redacteur->request('GET', '/intervention/');
        $this->assertEquals(200, $this->redacteur->getResponse()->getStatusCode());
    }

    public function testAdd()
    {
        $crawler = $this->redacteur->request('GET', '/intervention/new');
        $this->assertEquals(200, $this->redacteur->getResponse()->getStatusCode());

        //print_r($this->redacteur->getResponse()->getContent());

        $form = $crawler->selectButton('Ajouter')->form();

        $form['intervention[intitule]'] = 'Test de redacteur';
        $form['intervention[descriptif]'] = 'Tous les actions vont elles';

        $option = $crawler->filter('#intervention_service option:contains("Carmes")');

        $this->assertGreaterThan(0, count($option), 'Carmes non trouvée');
        $value = $option->attr('value');

        $form['intervention[service]']->select($value);

        // soumet le formulaire
        $this->redacteur->submit($form);
        $crawler = $this->redacteur->followRedirect();

        $this->assertGreaterThan(0, $crawler->filter('h3:contains("Test de redacteur")')->count());
    }

    public function testEdit()
    {
        $crawler = $this->redacteur->request('GET', '/intervention/');
        $this->assertEquals(200, $this->redacteur->getResponse()->getStatusCode());

        $crawler = $this->redacteur->click($crawler->selectLink('Test de redacteur')->link());

        $this->assertGreaterThan(0, $crawler->filter('h3:contains("Test de redacteur")')->count());

        $crawler = $this->redacteur->click($crawler->selectLink('Editer')->link());

        $form = $crawler->selectButton('Mettre à jour')->form(array());
        $form['intervention[cout_main]'] = 35;

        $this->redacteur->submit($form);
        $crawler = $this->redacteur->followRedirect();

        $this->assertGreaterThan(0, $crawler->filter('td:contains("35,00 €")')->count());
    }

    public function testAddFile()
    {
        $crawler = $this->redacteur->request('GET', '/intervention/');
        $this->assertEquals(200, $this->redacteur->getResponse()->getStatusCode());

        $crawler = $this->redacteur->click($crawler->selectLink('Test de redacteur')->link());

        $this->assertGreaterThan(0, $crawler->filter('h3:contains("Test de redacteur")')->count());

        $crawler = $this->redacteur->click($crawler->selectLink('Joindre un fichier')->link());

        $form = $crawler->selectButton('Ajouter')->form(array());

        $form['document[files][0]']->upload('/home/jfsenechal/Images/chouette.jpg');

        $this->redacteur->submit($form);
        $crawler = $this->redacteur->followRedirect();

        $this->assertGreaterThan(0, $crawler->filter('div:contains("Le document a bien été créé.")')->count());
    }

    public function testAddSuivis()
    {
        $crawler = $this->redacteur->request('GET', '/intervention/');
        $this->assertEquals(200, $this->redacteur->getResponse()->getStatusCode());

        $crawler = $this->redacteur->click($crawler->selectLink('Test de redacteur')->link());

        $this->assertGreaterThan(0, $crawler->filter('h3:contains("Test de redacteur")')->count());

        $crawler = $this->redacteur->click($crawler->selectLink('Ajouter un suivi')->link());

        $form = $crawler->selectButton('Ajouter')->form();

        $form['suivi[descriptif]'] = 'Il faut mettre une echarpe';

        $this->redacteur->submit($form);
        $crawler = $this->redacteur->followRedirect();

        $this->assertGreaterThan(0, $crawler->filter('div.panel-body:contains("echarpe")')->count());
    }
}
