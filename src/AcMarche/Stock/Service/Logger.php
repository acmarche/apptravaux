<?php
/**
 * Created by PhpStorm.
 * User: jfsenechal
 * Date: 20/03/19
 * Time: 10:54
 */

namespace AcMarche\Stock\Service;

use AcMarche\Stock\Entity\Log;
use AcMarche\Stock\Entity\Produit;
use AcMarche\Stock\Repository\LogRepository;
use Symfony\Component\Security\Core\Security;

class Logger
{
    /**
     * @var LogRepository
     */
    private $logRepository;
    /**
     * @var Security
     */
    private $security;

    public function __construct(LogRepository $logRepository, Security $security)
    {
        $this->logRepository = $logRepository;
        $this->security = $security;
    }

    /**
     * Logs with an arbitrary level.
     *
     * @param Produit $produit
     *
     * @return void
     */
    public function log(Produit $produit, int $quantite)
    {
        $log = new Log();
        $log->setNom($produit->getNom());
        $log->setQuantite($quantite);
        $user = $this->security->getUser();
        if (!$user) {
            $username = "smartphone";
        } else {
            $username = $user->getUsername();
        }
        
        $log->setUser($username);
        //$log->setCreatedAt(new \DateTime());
        $this->logRepository->insert($log);

    }
}