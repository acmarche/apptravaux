<?php

namespace AcMarche\Avaloir\Repository;

use AcMarche\Avaloir\Entity\Avaloir;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Avaloir|null find($id, $lockMode = null, $lockVersion = null)
 * @method Avaloir|null findOneBy(array $criteria, array $orderBy = null)
 * method Avaloir[]    findAll()
 * @method Avaloir[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AvaloirRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Avaloir::class);
    }

    /**
     * @return Avaloir[]
     */
    public function findAll()
    {
        return $this->findBy(array(), array('date_nettoyage' => 'DESC'));
    }

    /**
     * @param array $args
     * @return Avaloir[]
     */
    public function search($args)
    {
        $qb = $this->setCriteria($args);
        $query = $qb->getQuery();

        //echo  $query->getSQL();

        $results = $query->getResult();

        return $results;
    }

    /**
     * @param $args
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function setCriteria($args)
    {
        $nom = isset($args['nom']) ? $args['nom'] : null;
        $village = isset($args['village']) ? $args['village'] : null;
        $id = isset($args['id']) ? $args['id'] : 0;
        $date_debut = isset($args['date_debut']) ? $args['date_debut'] : null;
        $date_fin = isset($args['date_fin']) ? $args['date_fin'] : null;
        $quartier = isset($args['quartier']) ? $args['quartier'] : null;

        $qb = $this->createQueryBuilder('avaloir');
        $qb->leftJoin('avaloir.dates', 'dates', 'WITH');
        $qb->leftJoin('avaloir.rue', 'rue', 'WITH');
        $qb->leftJoin('rue.quartier', 'quartier', 'WITH');
        $qb->addSelect('quartier', 'rue', 'dates');

        if ($nom) {
            $qb->andWhere('rue.nom LIKE :mot OR avaloir.descriptif LIKE :mot ')
                ->setParameter('mot', '%'.$nom.'%');
        }

        if ($village) {
            $qb->andWhere('rue.village = :village')
                ->setParameter('village', $village);
        }

        if ($quartier) {
            $qb->andWhere('quartier.id = :quartier')
                ->setParameter('quartier', $quartier);
        }

        if ($date_debut != null) {
            $date_start = $date_debut->format('Y-m-d');

            if ($date_fin != null) {
                $date_end = $date_fin->format('Y-m-d');
            } else {
                $date_end = $date_start;
            }

            $qb->andWhere('dates.jour BETWEEN :date_start AND :date_end')
                ->setParameter('date_start', $date_start)
                ->setParameter('date_end', $date_end);
        }

        if ($id) {
            $qb->andWhere("avaloir.id IN ('$id')");
        }

        $qb->addOrderBy('rue.village', 'ASC');
        $qb->addOrderBy('rue.nom', 'ASC');

        return $qb;
    }
}
