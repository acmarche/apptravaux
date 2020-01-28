<?php

namespace AcMarche\Travaux\Tests\Interventions;

use AcMarche\Travaux\Tests\Controller\BaseUnit;

class AuteurControllerTest extends BaseUnit
{
    public function testIndex()
    {
        $crawler = $this->auteur->request('GET', '/intervention/');
        $this->assertEquals(200, $this->auteur->getResponse()->getStatusCode());
    }

    public function testAdd()
    {
        $crawler = $this->auteur->request('GET', '/intervention/new');
        $this->assertEquals(200, $this->auteur->getResponse()->getStatusCode());

        //print_r($this->auteur->getResponse()->getContent());

        $form = $crawler->selectButton('Ajouter')->form();

        $form['intervention[intitule]'] = 'Test de auteur';
        $form['intervention[descriptif]'] = 'Tous les actions vont elles';

        $option = $crawler->filter('#intervention_service option:contains("Carmes")');

        $this->assertGreaterThan(0, count($option), 'Carmes non trouvée');
        $value = $option->attr('value');

        $form['intervention[service]']->select($value);

        // soumet le formulaire
        $crawler = $this->auteur->submit($form);
        $crawler = $this->auteur->followRedirect();

        $this->assertGreaterThan(0, $crawler->filter('div:contains("Test de auteur")')->count());
    }

    public function testEdit()
    {
        $crawler = $this->auteur->request('GET', '/intervention/');
        $this->assertEquals(200, $this->auteur->getResponse()->getStatusCode());

        $crawler = $this->auteur->click($crawler->selectLink('Test de auteur')->link());

        $this->assertGreaterThan(0, $crawler->filter('div:contains("Test de auteur")')->count());

        $crawler = $this->auteur->click($crawler->selectLink('Editer')->link());

        $form = $crawler->selectButton('Mettre à jour')->form(array());
        $form['intervention[cout_main]'] = 40;

        $this->auteur->submit($form);
        $crawler = $this->auteur->followRedirect();

        $this->assertGreaterThan(0, $crawler->filter('td:contains("40,00 €")')->count());
    }

    public function testAddFile()
    {
        $crawler = $this->auteur->request('GET', '/intervention/');
        $this->assertEquals(200, $this->auteur->getResponse()->getStatusCode());

        $crawler = $this->auteur->click($crawler->selectLink('Test de auteur')->link());

        $this->assertGreaterThan(0, $crawler->filter('div:contains("Test de auteur")')->count());

        $crawler = $this->auteur->click($crawler->selectLink('Joindre un fichier')->link());

        $form = $crawler->selectButton('Ajouter')->form(array());

        $form['document[files][0]']->upload('/home/jfsenechal/Images/chouette.jpg');

        $this->auteur->submit($form);
        $crawler = $this->auteur->followRedirect();

        $this->assertGreaterThan(0, $crawler->filter('div:contains("Le document a bien été créé.")')->count());
    }

    public function testAddSuivis()
    {
        $crawler = $this->auteur->request('GET', '/intervention/');
        $this->assertEquals(200, $this->auteur->getResponse()->getStatusCode());

        $crawler = $this->auteur->click($crawler->selectLink('Test de auteur')->link());

        $this->assertGreaterThan(0, $crawler->filter('div:contains("Test de auteur")')->count());

        $crawler = $this->auteur->click($crawler->selectLink('Ajouter un suivi')->link());

        $form = $crawler->selectButton('Ajouter')->form();

        $form['suivi[descriptif]'] = 'Il faut mettre un pull';

        $this->auteur->submit($form);
        $crawler = $this->auteur->followRedirect();

        $this->assertGreaterThan(0, $crawler->filter('p:contains("pull")')->count());
    }
}
