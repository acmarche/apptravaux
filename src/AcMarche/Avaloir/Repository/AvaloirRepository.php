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

    public function flush()
    {
        $this->_em->flush();
    }

    public function persist(Avaloir $avaloir)
    {
        $this->_em->persist($avaloir);
    }

    /**
     * @return Avaloir[]
     */
    public function findAll()
    {
        return $this->findBy(array(), array('id' => 'DESC'));
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
        $rue = isset($args['rue']) ? $args['rue'] : null;
        $id = isset($args['id']) ? $args['id'] : 0;
        $date_debut = isset($args['date_debut']) ? $args['date_debut'] : null;
        $date_fin = isset($args['date_fin']) ? $args['date_fin'] : null;
        $quartier = isset($args['quartier']) ? $args['quartier'] : null;

        $qb = $this->createQueryBuilder('avaloir');
        $qb->leftJoin('avaloir.dates', 'dates', 'WITH');
        $qb->leftJoin('avaloir.commentaires', 'commentaires', 'WITH');
        $qb->leftJoin('avaloir.rueEntity', 'rueEntity', 'WITH');
        $qb->leftJoin('rueEntity.quartier', 'quartier', 'WITH');
        $qb->addSelect('quartier', 'rueEntity', 'dates', 'commentaires');

        if ($nom) {
            $qb->andWhere('avaloir.descriptif LIKE :mot ')
                ->setParameter('mot', '%'.$nom.'%');
        }

        if ($rue) {
            $qb->andWhere('avaloir.rue = :rue')
                ->setParameter('rue', $rue);
        }

        if ($village) {
            $qb->andWhere('rueEntity.village = :village')
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

        $qb->addOrderBy('avaloir.createdAt', 'DESC');

        //$qb->addOrderBy('rue.nom', 'ASC');

        return $qb;
    }
}
