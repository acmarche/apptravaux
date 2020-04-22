<?php

namespace AcMarche\Avaloir\Repository;

use AcMarche\Avaloir\Entity\Village;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Village|null find($id, $lockMode = null, $lockVersion = null)
 * @method Village|null findOneBy(array $criteria, array $orderBy = null)
 * @method Village[]    findAll()
 * @method Village[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VillageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Village::class);
    }
    /**
     * @return Village[]
     */
    public function getForSearch()
    {
        $qb = $this->createQueryBuilder('village');

        $qb->orderBy('village.nom');
        $query = $qb->getQuery();

        $results = $query->getResult();
        $villages = array();

        foreach ($results as $village) {
            $villages[$village->getNom()] = $village->getNom();
        }

        return $villages;
    }
}
