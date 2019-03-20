<?php
/**
 * Created by PhpStorm.
 * User: jfsenechal
 * Date: 24/01/18
 * Time: 12:59
 */

namespace AcMarche\Travaux\Tests\Interventions;

use AcMarche\Travaux\Tests\Controller\BaseUnit;

class ReporteControllerTest extends BaseUnit
{
    public function testAdd()
    {
        $crawler = $this->admin->request('GET', '/intervention/new');
        $this->assertEquals(200, $this->admin->getResponse()->getStatusCode());

        $form = $crawler->selectButton('Ajouter')->form();
        $today = new \DateTime('now');
        $today->modify('+3 day');

        $form['intervention[intitule]'] = 'Intervention a faire plus tard';
        $form['intervention[descriptif]'] = 'En été fait meilleur';
        $form['intervention[date_execution]'] = $today->format('d/m/Y');

        $option = $crawler->filter('#intervention_batiment option:contains("Eglises")');

        $this->assertGreaterThan(0, count($option), 'Eglises non trouvée');
        $value = $option->attr('value');

        $form['intervention[batiment]']->select($value);

        $crawler = $this->admin->submit($form);
        $crawler = $this->admin->followRedirect();

        $this->assertGreaterThan(0, $crawler->filter('div:contains("Intervention a faire plus tard")')->count());
    }

    public function testIndex()
    {
        $crawler = $this->admin->request('GET', '/intervention/');
        $this->assertEquals(200, $this->admin->getResponse()->getStatusCode());

        $this->assertEquals(0, $crawler->filter('td:contains("Intervention a faire plus tard")')->count());

        $crawler = $this->admin->click($crawler->selectLink('1 intervention(s) reportées')->link());
        $this->assertGreaterThan(0, $crawler->filter('td:contains("Intervention a faire plus tard")')->count());
    }

    public function testEdit()
    {
        $crawler = $this->admin->request('GET', '/intervention/');

        $crawler = $this->admin->click($crawler->selectLink('1 intervention(s) reportées')->link());

        $crawler = $this->admin->click($crawler->selectLink('Intervention a faire plus tard')->link());

        $crawler = $this->admin->click($crawler->selectLink('Editer')->link());

        $today = new \DateTime('now');
        $today->modify('-3 day');

        $form = $crawler->selectButton('Mettre à jour')->form(array());
        $form['intervention[date_execution]'] = $today->format('d/m/Y');

        $this->admin->submit($form);
        $crawler = $this->admin->followRedirect();

        $dateString = $today->format('d-m-Y');

        $this->assertEquals(1, $crawler->filter('td:contains("'.$dateString.'")')->count());

        $crawler = $this->admin->request('GET', '/intervention/');
        $this->assertEquals(1, $crawler->filter('td:contains("Intervention a faire plus tard")')->count());
    }
}
