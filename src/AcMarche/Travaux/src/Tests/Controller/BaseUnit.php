<?php
/**
 * Created by PhpStorm.
 * User: jfsenechal
 * Date: 14/12/18
 * Time: 10:48
 */

namespace AcMarche\Travaux\Tests\Controller;

use AcMarche\Travaux\Entity\Batiment;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BaseUnit extends WebTestCase
{
    /**
     * @var KernelBrowser
     */
    protected $admin;

    /**
     * @var KernelBrowser
     */
    protected $contributeur;

    /**
     * @var KernelBrowser
     */
    protected $auteur;

    /**
     * @var KernelBrowser
     */
    protected $redacteur;

    /**
     * @var KernelBrowser
     */
    protected $lecteur;

    /**
     * @var KernelBrowser
     */
    protected $anonyme;
    /**
     * @var \Symfony\Component\HttpKernel\KernelInterface
     */
    private $kernel2;
    /**
     * @var object|null
     */
    private $entityManager;

    public function setUp()
    {
        $this->kernel2 = self::bootKernel();

        $this->entityManager = $this->kernel2->getContainer()
            ->get('doctrine')
            ->getManager();

        static::ensureKernelShutdown();
        $this->admin = static::createClient(
            array(),
            array(
                'PHP_AUTH_USER' => 'admin',
                'PHP_AUTH_PW' => 'acmarche',
            )
        );

        static::ensureKernelShutdown();
        $this->contributeur = static::createClient(
            array(),
            array(
                'PHP_AUTH_USER' => 'contributeur',
                'PHP_AUTH_PW' => 'acmarche',
            )
        );

        static::ensureKernelShutdown();
        $this->auteur = static::createClient(
            array(),
            array(
                'PHP_AUTH_USER' => 'auteur',
                'PHP_AUTH_PW' => 'acmarche',
            )
        );

        static::ensureKernelShutdown();
        $this->redacteur = static::createClient(
            array(),
            array(
                'PHP_AUTH_USER' => 'redacteur',
                'PHP_AUTH_PW' => 'acmarche',
            )
        );

        static::ensureKernelShutdown();
        $this->lecteur = static::createClient(
            array(),
            array(
                'PHP_AUTH_USER' => 'lecteur',
                'PHP_AUTH_PW' => 'acmarche',
            )
        );

        static::ensureKernelShutdown();
        $this->anonyme = static::createClient();
    }

    protected function getBatiment(string $name): ?Batiment
    {
        return $this->entityManager
            ->getRepository(Batiment::class)
            ->findOneBy(['intitule' => $name]);
    }
}