<?php

namespace AcMarche\Travaux\Tests\Controller;

class InterventionControllerTest extends BaseUnit
{
    public function testIndex()
    {
        $crawler = $this->admin->request('GET', '/intervention/');
        $this->assertEquals(200, $this->admin->getResponse()->getStatusCode());
    }

    public function testAdd()
    {
        $crawler = $this->admin->request('GET', '/intervention/new');
        $this->assertEquals(200, $this->admin->getResponse()->getStatusCode());

        //print_r($this->client->getResponse()->getContent());

        $form = $crawler->selectButton('Ajouter')->form();

        $form['intervention[intitule]'] = 'Test de travail';
        $form['intervention[descriptif]'] = 'Trop de eau cst';

        $option = $crawler->filter('#intervention_service option:contains("Carmes")');

        $this->assertGreaterThan(0, count($option), 'Carmes non trouvée');
        $value = $option->attr('value');

        $form['intervention[service]']->select($value);

        // soumet le formulaire
        $crawler = $this->admin->submit($form);
        $crawler = $this->admin->followRedirect();

        $this->assertGreaterThan(0, $crawler->filter('div:contains("Test de travail")')->count());
    }

    public function testEdit()
    {
        $crawler = $this->admin->request('GET', '/intervention/');
        $this->assertEquals(200, $this->admin->getResponse()->getStatusCode());

        $crawler = $this->admin->click($crawler->selectLink('Test de travail')->link());

        $this->assertGreaterThan(0, $crawler->filter('div:contains("Test de travail")')->count());

        $crawler = $this->admin->click($crawler->selectLink('Editer')->link());

        $form = $crawler->selectButton('Mettre à jour')->form(
            array()
        );

        $option = $crawler->filter('#intervention_batiment option:contains("Les carmes")');
        $this->assertEquals(1, count($option), 'Les carmes');
        $bat = $option->attr('value');
        $form['intervention[batiment]']->select($bat);

        $option = $crawler->filter('#intervention_domaine option:contains("Eaux")');
        $this->assertEquals(1, count($option), 'Eaux');
        $value = $option->attr('value');
        $form['intervention[domaine]']->select($value);

        $this->admin->submit($form);
        $crawler = $this->admin->followRedirect();

        $this->assertGreaterThan(0, $crawler->filter('td:contains("Eaux")')->count());
    }

    /**
     * Ca marche pas avec les tests mais en web oui ???
     */
    public function testAddFile()
    {
        $crawler = $this->admin->request('GET', '/intervention/');
        $this->assertEquals(200, $this->admin->getResponse()->getStatusCode());

        $crawler = $this->admin->click($crawler->selectLink('Test de travail')->link());

        $this->assertGreaterThan(0, $crawler->filter('div:contains("Test de travail")')->count());

        $crawler = $this->admin->click($crawler->selectLink('Joindre un fichier')->link());

        $form = $crawler->selectButton('Ajouter')->form(
            array()
        );

        $form['document[files][0]']->upload('/home/jfsenechal/Images/chouette.jpg');

        $this->admin->submit($form);
        $crawler = $this->admin->followRedirect();

        $this->assertGreaterThan(0, $crawler->filter('div:contains("Le document a bien été créé.")')->count());
    }
}
