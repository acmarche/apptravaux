<?php

namespace AcMarche\Travaux\Tests\Interventions;

use AcMarche\Travaux\Tests\Controller\BaseUnit;

class ArchiveTest extends BaseUnit
{
    public function testAdd()
    {
        $crawler = $this->admin->request('GET', '/intervention/new');
        $this->assertEquals(200, $this->admin->getResponse()->getStatusCode());

        $form = $crawler->selectButton('Ajouter')->form();

        $form['intervention[intitule]'] = 'Intervention a archiver';
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
        $this->admin->followRedirect();

        $crawler = $this->admin->request('GET', '/intervention/');
        $this->assertGreaterThan(0, $crawler->filter('td:contains("Intervention a archiver")')->count());
    }

    public function archiveTravail()
    {
        $crawler = $this->admin->request('GET', '/intervention/');

        $crawler = $this->admin->click($crawler->selectLink('Intervention a archiver')->link());

        $this->assertEquals(200, $this->admin->getResponse()->getStatusCode());

        $this->admin->submit($crawler->selectButton("Valider cette action")->form());

        $crawler = $this->admin->followRedirect();

        $this->assertEquals(0, $crawler->filter('div:contains("L\'intervention a bien été archivée")')->count());
    }
}
