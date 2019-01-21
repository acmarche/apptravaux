<?php

namespace AcMarche\Avaloir\Repository;

use AcMarche\Avaloir\Entity\Quartier;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Quartier|null find($id, $lockMode = null, $lockVersion = null)
 * @method Quartier|null findOneBy(array $criteria, array $orderBy = null)
 * @method Quartier[]    findAll()
 * @method Quartier[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class QuartierRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Quartier::class);
    }

    /**
     * @return Quartier[]
     */
    public function getForSearch()
    {
        $qb = $this->createQueryBuilder('q');

        $qb->orderBy('q.nom');
        $query = $qb->getQuery();

        $results = $query->getResult();
        $quartiers = array();

        foreach ($results as $quartier) {
            $quartiers[$quartier->getNom()] = $quartier->getId();
        }

        return $quartiers;
    }

    /**
     * @param $args
     * @return Quartier[]
     */
    public function search($args)
    {
        $nom = isset($args['nom']) ? $args['nom'] : null;

        $qb = $this->createQueryBuilder('q');
        $qb->leftJoin('q.rues', 'r', 'WITH');
        $qb->leftJoin('r.avaloirs', 'a', 'WITH');
        $qb->addSelect('r', 'a');

        if ($nom != null) {
            $qb->andWhere('q.nom LIKE :mot')
                ->setParameter('mot', '%'.$nom.'%');
        }

        $qb->addOrderBy('r.nom', 'ASC');

        $query = $qb->getQuery();

        //echo  $query->getSQL();

        $results = $query->getResult();

        return $results;
    }
}
