<?php
/**
 * Created by PhpStorm.
 * User: jfsenechal
 * Date: 14/12/18
 * Time: 10:48
 */

namespace AcMarche\Travaux\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Panther\Client;


class BaseUnit extends WebTestCase
{
    /**
     * @var Client
     */
    protected $admin;

    /**
     * @var Client
     */
    protected $contributeur;

    /**
     * @var Client
     */
    protected $auteur;

    /**
     * @var Client
     */
    protected $redacteur;

    /**
     * @var Client
     */
    protected $lecteur;

    /**
     * @var Client
     */
    protected $anonyme;


    public function setUp()
    {
        $this->admin = static::createClient(
            array(),
            array(
                'PHP_AUTH_USER' => 'admin',
                'PHP_AUTH_PW' => 'acmarche',
            )
        );

        $this->contributeur = static::createClient(
            array(),
            array(
                'PHP_AUTH_USER' => 'contributeur',
                'PHP_AUTH_PW' => 'acmarche',
            )
        );

        $this->auteur = static::createClient(
            array(),
            array(
                'PHP_AUTH_USER' => 'auteur',
                'PHP_AUTH_PW' => 'acmarche',
            )
        );

        $this->redacteur = static::createClient(
            array(),
            array(
                'PHP_AUTH_USER' => 'redacteur',
                'PHP_AUTH_PW' => 'acmarche',
            )
        );

        $this->lecteur = static::createClient(
            array(),
            array(
                'PHP_AUTH_USER' => 'lecteur',
                'PHP_AUTH_PW' => 'acmarche',
            )
        );

        $this->anonyme = static::createClient(        );
    }
}