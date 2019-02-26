<?php

namespace AcMarche\Avaloir\Repository;

use AcMarche\Avaloir\Entity\DateNettoyage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method DateNettoyage|null find($id, $lockMode = null, $lockVersion = null)
 * @method DateNettoyage|null findOneBy(array $criteria, array $orderBy = null)
 * @method DateNettoyage[]    findAll()
 * @method DateNettoyage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DateNettoyageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DateNettoyage::class);
    }
}
