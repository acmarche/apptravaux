<?php

namespace AcMarche\Travaux\Repository;

use AcMarche\Travaux\Entity\Intervention;
use AcMarche\Travaux\Entity\Suivi;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Suivi|null find($id, $lockMode = null, $lockVersion = null)
 * @method Suivi|null findOneBy(array $criteria, array $orderBy = null)
 * @method Suivi[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SuiviRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Suivi::class);
    }

    /**
     * @param $args
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function setCriteria($args)
    {
        $intervention = isset($args['intervention']) ? $args['intervention'] : null;
        $sort = isset($args['sort']) ? $args['sort'] : null;
        $user = isset($args['user']) ? $args['user'] : null;

        $qb = $this->createQueryBuilder('suivi');
        $qb->leftJoin('suivi.intervention', 'intervention', 'WITH');
        $qb->addSelect('intervention');

        if ($intervention) {
            $qb->andWhere('suivi.intervention = :intervention')
                ->setParameter('intervention', $intervention);
        }

        if ($user) {
            $ids = join(",", $user);
            $qb->andWhere('user.id IN ('.$ids.')');
        }

        if ($sort) {
            $qb->addOrderBy('suivi.'.$sort, 'DESC');
        } else {
            $qb->addOrderBy('suivi.createdAt', 'DESC');
        }

        $qb->addOrderBy('suivi.id', 'ASC');

        return $qb;
    }

    /**
     * @param $args
     * @return Suivi[]
     */
    public function search($args)
    {
        $qb = $this->setCriteria($args);
        $query = $qb->getQuery();

        $results = $query->getResult();

        return $results;
    }

    /**
     * @param Intervention $intervention
     * @return Suivi|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getLastSuivi(Intervention $intervention)
    {
        $qb = $this->createQueryBuilder('suivi');
        $qb->leftJoin('suivi.intervention', 'intervention', 'WITH');
        $qb->addSelect('intervention');

        if ($intervention) {
            $qb->andWhere('suivi.intervention = :intervention')
                ->setParameter('intervention', $intervention);
        }

        $qb->addOrderBy('suivi.id', 'DESC');
        $qb->setMaxResults(1);

        $query = $qb->getQuery();
        $result = $query->getOneOrNullResult();

        return $result;
    }
}
