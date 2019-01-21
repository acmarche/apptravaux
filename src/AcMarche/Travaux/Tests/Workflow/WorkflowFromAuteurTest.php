<?php

namespace AcMarche\Travaux\Tests\Workflow;

use AcMarche\Travaux\Tests\Controller\BaseUnit;

class WorkflowFromAuteurTest extends BaseUnit
{
    public function testAddForRefusAdmin()
    {
        $crawler = $this->auteur->request('GET', '/intervention/new');
        $this->assertEquals(200, $this->auteur->getResponse()->getStatusCode());

        $form = $crawler->selectButton('Ajouter')->form();

        $form['intervention[intitule]'] = 'Demande de auteur pour poubelle';
        $form['intervention[descriptif]'] = 'Plus de ordis';

        $option = $crawler->filter('#intervention_service option:contains("Enseignement")');
        $this->assertGreaterThan(0, count($option), 'Enseignement non trouvée');
        $value = $option->attr('value');
        $form['intervention[service]']->select($value);

        $haute = $crawler->filter('#intervention_priorite option:contains("Haute")');
        $this->assertEquals(0, count($haute), 'Priorite Haute ne peut etre trouvée');

        $eglise = $crawler->filter('#intervention_batiment option:contains("Eglises")');
        $this->assertGreaterThan(0, count($eglise), 'Eglises non trouvée');
        $eglise_value = $eglise->attr('value');
        $form['intervention[batiment]']->select($eglise_value);

        $this->auteur->submit($form);
        $crawler = $this->auteur->followRedirect();

        $this->assertGreaterThan(
            0,
            $crawler->filter('div:contains("Cette intervention doit être validée par un administrateur")')->count()
        );
    }

    public function testRefusAdmin()
    {
        $crawler = $this->admin->request('GET', '/intervention/');
        $this->assertEquals(200, $this->admin->getResponse()->getStatusCode());

        $crawler = $this->admin->click($crawler->selectLink('intervention(s) à traiter')->link());
        $this->assertGreaterThan(0, $crawler->filter('td:contains("Demande de auteur pour poubelle")')->count());

        $crawler = $this->admin->click($crawler->selectLink('Demande de auteur pour poubelle')->link());

        $crawler = $this->admin->click($crawler->selectLink('Traiter cette demande')->link());

        $form = $crawler->selectButton('Refuser')->form(
            array(
                'validation[message]' => 'Non on en veut pas',
            )
        );

        $this->admin->submit($form);

        $crawler = $this->admin->followRedirect();

        $this->assertGreaterThan(
            0,
            $crawler->filter(
                'div:contains("L\'intervention a bien été refusée")'
            )->count()
        );

        $crawler = $this->admin->request('GET', '/intervention/');
        $this->assertEquals(0, $crawler->filter('div:contains("Demande de auteur pour poubelle")')->count());
    }

    public function testAdd()
    {
        $crawler = $this->auteur->request('GET', '/intervention/new');
        $this->assertEquals(200, $this->auteur->getResponse()->getStatusCode());

        $form = $crawler->selectButton('Ajouter')->form();

        $form['intervention[intitule]'] = 'Demande de auteur pour accord';
        $form['intervention[descriptif]'] = 'Plus de ordis';

        $option = $crawler->filter('#intervention_service option:contains("Enseignement")');
        $this->assertGreaterThan(0, count($option), 'Enseignement non trouvée');
        $value = $option->attr('value');
        $form['intervention[service]']->select($value);

        $haute = $crawler->filter('#intervention_priorite option:contains("Haute")');
        $this->assertEquals(0, count($haute), 'Priorite Haute ne peut etre trouvée');

        $eglise = $crawler->filter('#intervention_batiment option:contains("Eglises")');
        $this->assertGreaterThan(0, count($eglise), 'Eglises non trouvée');
        $eglise_value = $eglise->attr('value');
        $form['intervention[batiment]']->select($eglise_value);

        $this->auteur->submit($form);
        $crawler = $this->auteur->followRedirect();

        $this->assertGreaterThan(
            0,
            $crawler->filter('div:contains("Cette intervention doit être validée par un administrateur")')->count()
        );
    }

    public function testInfoAdmin()
    {
        $crawler = $this->admin->request('GET', '/intervention/');
        $this->assertEquals(200, $this->admin->getResponse()->getStatusCode());

        $crawler = $this->admin->click($crawler->selectLink('intervention(s) à traiter')->link());
        $this->assertGreaterThan(0, $crawler->filter('td:contains("Demande de auteur pour accord")')->count());

        $crawler = $this->admin->click($crawler->selectLink('Demande de auteur pour accord')->link());

        $crawler = $this->admin->click($crawler->selectLink('Traiter cette demande')->link());

        $form = $crawler->selectButton('Plus d\'infos')->form(
            array(
                'validation[message]' => 'Je veux plus dinformations',
            )
        );

        $this->admin->submit($form);
        //   print_r($this->admin->getResponse()->getContent());
        $crawler = $this->admin->followRedirect();

        $this->assertGreaterThan(0, $crawler->filter('div:contains("L\'intervention a bien été traitée")')->count());

        $crawler = $this->admin->request('GET', '/intervention/');

        $this->assertEquals(0, $crawler->filter('td:contains("Demande de auteur pour accord")')->count());
    }

    public function testAuteurDonneInfo()
    {
        $crawler = $this->auteur->request('GET', '/intervention/');
        $this->assertEquals(200, $this->auteur->getResponse()->getStatusCode());

        $crawler = $this->auteur->click($crawler->selectLink('intervention(s) à traiter')->link());
        $this->assertGreaterThan(0, $crawler->filter('td:contains("Demande de auteur pour accord")')->count());

        $crawler = $this->auteur->click($crawler->selectLink('Demande de auteur pour accord')->link());

        $crawler = $this->auteur->click($crawler->selectLink('Traiter cette demande')->link());

        $form = $crawler->selectButton('Accepter')->form(
            array(
                'validation[message]' => 'Voila ton info',
            )
        );

        $this->auteur->submit($form);
        $crawler = $this->auteur->followRedirect();

        $this->assertGreaterThan(
            0,
            $crawler->filter(
                'div:contains("L\'intervention a bien été acceptée")'
            )->count()
        );

        $crawler = $this->auteur->request('GET', '/intervention/');

        $crawler = $this->auteur->click($crawler->selectLink('Demande de auteur pour accord')->link());

        $this->assertEquals(200, $this->auteur->getResponse()->getStatusCode());

        $this->assertGreaterThan(0, $crawler->filter('div:contains("Voila ton info")')->count());
    }

    public function testValidAdmin()
    {
        $crawler = $this->admin->request('GET', '/intervention/');
        $this->assertEquals(200, $this->admin->getResponse()->getStatusCode());

        $crawler = $this->admin->click($crawler->selectLink('intervention(s) à traiter')->link());
        $this->assertGreaterThan(0, $crawler->filter('td:contains("Demande de auteur pour accord")')->count());

        $crawler = $this->admin->click($crawler->selectLink('Demande de auteur pour accord')->link());
        $crawler = $this->admin->click($crawler->selectLink('Traiter cette demande')->link());

        $form = $crawler->selectButton('Accepter')->form(
            array(
                'validation[message]' => 'OKAY',
            )
        );

        $this->admin->submit($form);
        $crawler = $this->admin->followRedirect();

        $this->assertGreaterThan(0, $crawler->filter('div:contains("L\'intervention a bien été acceptée")')->count());

        $crawler = $this->admin->request('GET', '/intervention/');

        $crawler = $this->admin->click($crawler->selectLink('Demande de auteur pour accord')->link());
        $this->assertEquals(200, $this->admin->getResponse()->getStatusCode());

        $this->assertGreaterThan(0, $crawler->filter('div.panel-body:contains("OKAY")')->last()->count());
    }

    public function testDeleteTravail()
    {
        $crawler = $this->admin->request('GET', '/intervention/');
        $crawler = $this->admin->click($crawler->selectLink('Demande de auteur pour accord')->link());
        $this->assertEquals(200, $this->admin->getResponse()->getStatusCode());

        $crawler = $this->admin->click($crawler->selectLink('Supprimer l\'intervention')->link());

        $this->admin->submit($crawler->selectButton('Supprimer')->last()->form());

        $crawler = $this->admin->followRedirect();

        $this->assertEquals(0, $crawler->filter('td:contains("Demande de auteur pour accord")')->count());
    }
}
