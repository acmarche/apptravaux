<?php

namespace AcMarche\Travaux\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends BaseUnit
{
    public function testIndex()
    {
        $crawler = $this->admin->request('GET', '/documentation');
        $this->assertContains('Documentation', $this->admin->getResponse()->getContent());
        // print_r($this->client->getResponse()->getContent());
    }
}
