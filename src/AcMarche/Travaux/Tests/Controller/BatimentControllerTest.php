<?php

namespace AcMarche\Travaux\Tests\Controller;

class BatimentControllerTest extends BaseUnit
{
    public function testIndex()
    {
        $crawler = $this->admin->request('GET', '/batiment/');
        $this->assertEquals(
            200,
            $this->admin->getResponse()->getStatusCode()
        );
    }

    public function testAdd()
    {
        $crawler = $this->admin->request('GET', '/batiment/new');
        $this->assertEquals(
            200,
            $this->admin->getResponse()->getStatusCode()
        );

        $this->admin->submitForm(
            'Ajouter',
            [
                'batiment[intitule]' => 'Les carme',
            ]
        );

        $crawler = $this->admin->followRedirect();

        $this->assertGreaterThan(
            0,
            $crawler->filter('h3:contains("Les carme")')->count(),
            'Missing element h3:contains("Les carme")'
        );
    }

    public function testEdit()
    {
        $crawler = $this->admin->request('GET', '/batiment/les-carme');
        $this->assertEquals(
            200,
            $this->admin->getResponse()->getStatusCode()
        );

        $this->assertGreaterThan(
            0,
            $crawler->filter('h3:contains("Les carme")')->count(),
            'Missing element h3:contains("Les carme")'
        );

        $crawler = $this->admin->click($crawler->selectLink('Editer')->link());

        $form = $crawler->selectButton('Mettre à jour')->form(
            array(
                'batiment[intitule]' => 'Les carmes',
            )
        );

        $this->admin->submit($form);
        $crawler = $this->admin->followRedirect();

        $this->assertGreaterThan(
            0,
            $crawler->filter('h3:contains("Les carmes")')->count(),
            'Missing element h3:contains("Les carmes")'
        );
    }

    public function testAddEglise()
    {
        $crawler = $this->admin->request('GET', '/batiment/new');
        $this->assertEquals(
            200,
            $this->admin->getResponse()->getStatusCode()
        );

        $form = $crawler->selectButton('Ajouter')->form(
            array(
                'batiment[intitule]' => 'Eglises',
            )
        );

        $this->admin->submit($form);
        $crawler = $this->admin->followRedirect();

        $this->assertGreaterThan(
            0,
            $crawler->filter('h3:contains("Eglises")')->count(),
            'Missing element h3:contains("Eglises")'
        );
    }
}
