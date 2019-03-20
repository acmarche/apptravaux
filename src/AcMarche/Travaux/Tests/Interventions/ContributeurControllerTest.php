<?php

namespace AcMarche\Travaux\Tests\Interventions;

use AcMarche\Travaux\Tests\Controller\BaseUnit;

class ContributeurControllerTest extends BaseUnit
{

    public function testIndex()
    {
        $crawler = $this->contributeur->request('GET', '/intervention/');
        $this->assertEquals(200, $this->contributeur->getResponse()->getStatusCode());
    }

    public function testAdd()
    {
        $crawler = $this->contributeur->request('GET', '/intervention/new');
        $this->assertEquals(200, $this->contributeur->getResponse()->getStatusCode());

        //print_r($this->contributeur->getResponse()->getContent());

        $form = $crawler->selectButton('Ajouter')->form();

        $form['intervention[intitule]'] = 'Test de contributeur';
        $form['intervention[descriptif]'] = 'Tous les actions vont elles';

        $option = $crawler->filter('#intervention_service option:contains("Carmes")');

        $this->assertGreaterThan(0, count($option), 'Carmes non trouvée');
        $value = $option->attr('value');

        $form['intervention[service]']->select($value);

        // soumet le formulaire
        $this->contributeur->submit($form);
        $crawler = $this->contributeur->followRedirect();

        $this->assertGreaterThan(0, $crawler->filter('div:contains("Test de contributeur")')->count());
    }

    public function testEdit()
    {
        $crawler = $this->contributeur->request('GET', '/intervention/');
        $this->assertEquals(200, $this->contributeur->getResponse()->getStatusCode());

        $crawler = $this->contributeur->click($crawler->selectLink('Test de contributeur')->link());

        $this->assertGreaterThan(0, $crawler->filter('div:contains("Test de contributeur")')->count());

        $crawler = $this->contributeur->click($crawler->selectLink('Editer')->link());

        $form = $crawler->selectButton('Mettre à jour')->form(array());
        $form['intervention[cout_main]'] = 20;

        $this->contributeur->submit($form);
        $crawler = $this->contributeur->followRedirect();

        $this->assertGreaterThan(0, $crawler->filter('td:contains("20,00 €")')->count());
    }

    public function testAddFile()
    {
        $crawler = $this->contributeur->request('GET', '/intervention/');
        $this->assertEquals(200, $this->contributeur->getResponse()->getStatusCode());

        $crawler = $this->contributeur->click($crawler->selectLink('Test de contributeur')->link());

        $this->assertGreaterThan(0, $crawler->filter('div:contains("Test de contributeur")')->count());

        $crawler = $this->contributeur->click($crawler->selectLink('Joindre un fichier')->link());

        $form = $crawler->selectButton('Ajouter')->form(array());

        $form['document[files][0]']->upload('/home/jfsenechal/Images/chouette.jpg');

        $this->contributeur->submit($form);
        $crawler = $this->contributeur->followRedirect();

        $this->assertGreaterThan(0, $crawler->filter('div:contains("Le document a bien été créé.")')->count());
    }

    public function testAddSuivis()
    {
        $crawler = $this->contributeur->request('GET', '/intervention/');
        $this->assertEquals(200, $this->contributeur->getResponse()->getStatusCode());

        $crawler = $this->contributeur->click($crawler->selectLink('Test de contributeur')->link());

        $this->assertGreaterThan(0, $crawler->filter('div:contains("Test de contributeur")')->count());

        $crawler = $this->contributeur->click($crawler->selectLink('Ajouter un suivi')->link());

        $form = $crawler->selectButton('Ajouter')->form();

        $form['suivi[descriptif]'] = 'Il faut mettre un gilet';

        $this->contributeur->submit($form);
        $crawler = $this->contributeur->followRedirect();

        $this->assertGreaterThan(0, $crawler->filter('div:contains("gilet")')->count());
    }
}
