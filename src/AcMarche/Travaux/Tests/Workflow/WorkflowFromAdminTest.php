<?php

namespace AcMarche\Travaux\Tests\Workflow;

use AcMarche\Travaux\Tests\Controller\BaseUnit;

class WorkflowFromAdminTest extends BaseUnit
{
    public function testAdd()
    {
        $crawler = $this->admin->request('GET', '/intervention/new');
        $this->assertEquals(200, $this->admin->getResponse()->getStatusCode());

        $form = $crawler->selectButton('Ajouter')->form();

        $form['intervention[intitule]'] = 'Demande de admin';
        $form['intervention[descriptif]'] = 'Il a tous les droits';

        $option = $crawler->filter('#intervention_service option:contains("Enseignement")');
        $this->assertGreaterThan(0, count($option), 'Enseignement non trouvée');
        $value = $option->attr('value');
        $form['intervention[service]']->select($value);

        $eglise = $crawler->filter('#intervention_batiment option:contains("Eglises")');
        $this->assertGreaterThan(0, count($eglise), 'Eglises non trouvée');
        $eglise_value = $eglise->attr('value');
        $form['intervention[batiment]']->select($eglise_value);

        $this->admin->submit($form);
        $crawler = $this->admin->followRedirect();

        $this->assertEquals(
            0,
            $crawler->filter('div:contains("Cette intervention doit être validée par un auteur")')->count()
        );
        $this->assertEquals(
            0,
            $crawler->filter('div:contains("Cette intervention doit être validée par un administrateur")')->count()
        );

        $crawler = $this->admin->request('GET', '/intervention/');

        $this->assertGreaterThan(0, $crawler->filter('td:contains("Demande de admin")')->count());
    }

    public function testDeleteTravail()
    {
        $crawler = $this->admin->request('GET', '/intervention/');

        $crawler = $this->admin->click($crawler->selectLink('Demande de admin')->link());

        $this->assertEquals(200, $this->admin->getResponse()->getStatusCode());

        $this->admin->submit($crawler->selectButton('Supprimer')->last()->form());

        $crawler = $this->admin->followRedirect();

        $this->assertEquals(0, $crawler->filter('td:contains("Demande de lobet")')->count());
    }
}
