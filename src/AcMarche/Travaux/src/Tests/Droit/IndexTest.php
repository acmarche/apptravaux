<?php

namespace AcMarche\Travaux\Tests\Droit;

use AcMarche\Travaux\Tests\Controller\BaseUnit;

class IndexTest extends BaseUnit
{

    public function testLectureContributeur()
    {
        $crawler = $this->contributeur->request('GET', '/intervention/');
        $this->assertEquals(1, $crawler->filter('td:contains("Arbres à Hollogne a retailler")')->count());
        $crawler = $this->contributeur->click($crawler->selectLink('Arbres à Hollogne a retailler')->link());
        $this->assertEquals(200, $this->contributeur->getResponse()->getStatusCode());

        $crawler = $this->contributeur->request('GET', '/intervention/');
        $this->assertEquals(0, $crawler->filter('td:contains("Nettoyer les cours des écoles")')->count());

        $crawler = $this->contributeur->request('GET', '/intervention/');
        $this->assertEquals(0, $crawler->filter('td:contains("Repeindre les chassis")')->count());

        $crawler = $this->contributeur->request('GET', '/intervention/');
        $this->assertEquals(0, $crawler->filter('td:contains("Réorganiser le parking")')->count());
    }

    public function testLectureAdmin()
    {
        $crawler = $this->admin->request('GET', '/validation/');
        $this->assertEquals(0, $crawler->filter('td:contains("Arbres à Hollogne a retailler")')->count());

        $crawler = $this->admin->request('GET', '/validation/');
        $this->assertEquals(1, $crawler->filter('td:contains("Nettoyer les cours des écoles")')->count());
        $crawler = $this->admin->click($crawler->selectLink('Nettoyer les cours des écoles')->link());
        $this->assertEquals(200, $this->admin->getResponse()->getStatusCode());

        $crawler = $this->admin->request('GET', '/validation/');
        $this->assertEquals(1, $crawler->filter('td:contains("Repeindre les chassis")')->count());
        $crawler = $this->admin->click($crawler->selectLink('Repeindre les chassis')->link());
        $this->assertEquals(200, $this->admin->getResponse()->getStatusCode());

        $crawler = $this->admin->request('GET', '/intervention/');
        $this->assertEquals(1, $crawler->filter('td:contains("Réorganiser le parking")')->count());
        $crawler = $this->admin->click($crawler->selectLink('Réorganiser le parking')->link());
        $this->assertEquals(200, $this->admin->getResponse()->getStatusCode());
    }

    public function testLectureAuteur()
    {
        $crawler = $this->auteur->request('GET', '/validation/');
        $this->assertEquals(1, $crawler->filter('td:contains("Arbres à Hollogne a retailler")')->count());
        $crawler = $this->auteur->click($crawler->selectLink('Arbres à Hollogne a retailler')->link());
        $this->assertEquals(200, $this->auteur->getResponse()->getStatusCode());

        $crawler = $this->auteur->request('GET', '/intervention/');
        $this->assertEquals(1, $crawler->filter('td:contains("Nettoyer les cours des écoles")')->count());
        $crawler = $this->auteur->click($crawler->selectLink('Nettoyer les cours des écoles')->link());
        $this->assertEquals(200, $this->auteur->getResponse()->getStatusCode());

        $crawler = $this->auteur->request('GET', '/intervention/');
        $this->assertEquals(1, $crawler->filter('td:contains("Repeindre les chassis")')->count());
        $crawler = $this->auteur->click($crawler->selectLink('Repeindre les chassis')->link());
        $this->assertEquals(200, $this->auteur->getResponse()->getStatusCode());

        $crawler = $this->auteur->request('GET', '/intervention/');
        $this->assertEquals(1, $crawler->filter('td:contains("Réorganiser le parking")')->count());
        $crawler = $this->auteur->click($crawler->selectLink('Réorganiser le parking')->link());
        $this->assertEquals(200, $this->auteur->getResponse()->getStatusCode());
    }

    public function testLectureRedacteur()
    {
        $crawler = $this->redacteur->request('GET', '/intervention/');
        $this->assertEquals(0, $crawler->filter('td:contains("Arbres à Hollogne a retailler")')->count());

        $crawler = $this->redacteur->request('GET', '/intervention/');
        $this->assertEquals(0, $crawler->filter('td:contains("Nettoyer les cours des écoles")')->count());

        $crawler = $this->redacteur->request('GET', '/intervention/');
        $this->assertEquals(0, $crawler->filter('td:contains("Repeindre les chassis")')->count());
        //$crawler = $this->redacteur->click($crawler->selectLink('Repeindre les chassis')->link());
        //$this->assertEquals(0, $this->redacteur->getResponse()->getStatusCode());

        $crawler = $this->redacteur->request('GET', '/intervention/');
        $this->assertEquals(1, $crawler->filter('td:contains("Réorganiser le parking")')->count());
        $crawler = $this->redacteur->click($crawler->selectLink('Réorganiser le parking')->link());
        $this->assertEquals(200, $this->redacteur->getResponse()->getStatusCode());
    }
}
