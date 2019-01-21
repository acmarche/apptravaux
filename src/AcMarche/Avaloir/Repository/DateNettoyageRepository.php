<?php

namespace AcMarche\Avaloir\Repository;

use AcMarche\Avaloir\Entity\Avaloir;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityRepository;

/**
 * @method Etat|null find($id, $lockMode = null, $lockVersion = null)
 * @method Etat|null findOneBy(array $criteria, array $orderBy = null)
 * @method Etat[]    findAll()
 * @method Etat[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DateNettoyageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Avaloir::class);
    }
}
