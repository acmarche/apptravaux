<?php

namespace AcMarche\Travaux\Repository;

use AcMarche\Travaux\Entity\Priorite;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Priorite|null find($id, $lockMode = null, $lockVersion = null)
 * @method Priorite|null findOneBy(array $criteria, array $orderBy = null)
 * @method Priorite[]    findAll()
 * @method Priorite[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PrioriteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Priorite::class);
    }
    /**
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getForList()
    {
        $qb = $this->createQueryBuilder('priorite');
        $qb->orderBy('priorite.intitule', 'DESC');//desc : normal at first

        return $qb;
    }

    /**
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getForListDefault()
    {
        $qb = $this->createQueryBuilder('priorite');
        $qb->andWhere('priorite.intitule LIKE :titre')
            ->setParameter('titre', "Normal");

        return $qb;
    }


    /**
     * Pour formulaire avec liste deroulante
     * @return array
     */
    public function getForSearch()
    {
        $qb = $this->createQueryBuilder('priorite');

        $qb->orderBy('priorite.intitule');
        $query = $qb->getQuery();

        $results = $query->getResult();
        $priorites = array();

        foreach ($results as $priorite) {
            $priorites[$priorite->getIntitule()] = $priorite->getId();
        }

        return $priorites;
    }
}
