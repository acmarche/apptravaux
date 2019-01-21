<?php
/**
 * Created by PhpStorm.
 * User: jfsenechal
 * Date: 21/01/19
 * Time: 13:58
 */

namespace AcMarche\Avaloir\Tests\Controller;

use AcMarche\Avaloir\Entity\Rue;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Panther\Client;

class BaseUnit extends WebTestCase
{
    /**
     * @var Client
     */
    protected $client;
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $entityManager;

    public function setUp()
    {
        $this->client = static::createClient(
            array(),
            array(
                'PHP_AUTH_USER' => 'admin',
                'PHP_AUTH_PW' => 'acmarche',
            )
        );
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    /**
     * @return Rue
     */
    public function getRues()
    {
        $rues = $this->entityManager->getRepository(Rue::class)->search(array('nom' => 'Libert'));
        return $rues[0];
    }

}