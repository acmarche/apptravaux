<?php

namespace AcMarche\Avaloir\Repository;

use AcMarche\Avaloir\Entity\Quartier;
use AcMarche\Avaloir\Entity\Rue;
use AcMarche\Avaloir\Entity\Village;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Rue|null find($id, $lockMode = null, $lockVersion = null)
 * @method Rue|null findOneBy(array $criteria, array $orderBy = null)
 * method Rue[]    findAll()
 * @method Rue[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RueRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Rue::class);
    }

    /**
     * @return Rue[]
     */
    public function findAll()
    {
        return $this->findBy(array(), array('nom' => 'ASC'));
    }

    /**
     * @param $args
     * @return Rue[]
     */
    public function search($args)
    {
        $nom = isset($args['nom']) ? $args['nom'] : null;
        $village = isset($args['village']) ? $args['village'] : null;
        $quartier = isset($args['quartier']) ? $args['quartier'] : null;

        $qb = $this->createQueryBuilder('rue');
        $qb->leftJoin('rue.avaloirs', 'avaloirs', 'WITH');
        $qb->leftJoin('rue.quartier', 'quartier', 'WITH');
        $qb->leftJoin('rue.village', 'village', 'WITH');
        $qb->addSelect('avaloirs', 'quartier', 'village');

        if ($nom != null) {
            $qb->andWhere('rue.nom LIKE :mot')
                ->setParameter('mot', '%'.$nom.'%');
        }

        if ($village) {
            $qb->andWhere('village = :village')
                ->setParameter('village', $village);
        }

        if ($quartier) {
            $qb->andWhere('quartier.id = :quartier')
                ->setParameter('quartier', $quartier);
        }

        $qb->addOrderBy('rue.village', 'ASC');
        $qb->addOrderBy('rue.nom', 'ASC');

        $query = $qb->getQuery();

        $results = $query->getResult();

        return $results;
    }

    /**
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getForList()
    {
        $qb = $this
            ->createQueryBuilder('r')
            ->addOrderBy('r.village', 'ASC')
            ->addOrderBy('r.nom', 'ASC');

        return $qb;
    }

    /**
     * @return Village[]
     */
    public function getVillages()
    {
        $qb = $this->createQueryBuilder('r');
        $qb->addOrderBy('r.nom', 'ASC');

        $query = $qb->getQuery();

        //echo  $query->getSQL();

        $villages = array();
        $results = $query->getResult();

        foreach ($results as $result) {
            $villages[$result->getVillage()] = $result->getVillage();
        }

        ksort($villages);

        return $villages;
    }


    /**
     * @param Quartier $quartier
     * @param bool $groupBy
     * @return Rue[]
     */
    public function getByQuartier(Quartier $quartier, $groupBy = false)
    {
        $qb = $this->createQueryBuilder('rue');

        $qb->leftJoin('rue.quartier', 'quartier', 'WITH');
        $qb->leftJoin('rue.village', 'village', 'WITH');
        $qb->addSelect('quartier', 'village');

        $qb->andWhere('quartier.id = :quartier')
            ->setParameter('quartier', $quartier);
        $qb->addOrderBy('rue.nom', 'ASC');

        $query = $qb->getQuery();
        $results = $query->getResult();

        if (!$groupBy) {
            return $results;
        }

        $rues = array();
        foreach ($results as $rue) {
            $village = $rue->getVillage();
            $rues[$village->getId()][] = $rue;
        }

        return $rues;
    }

    public function findOneByRue(string $road): ?Rue
    {
        return $this->createQueryBuilder('rue')
            ->andWhere('rue.nom = :road')
            ->setParameter('road', $road)->getQuery()->getOneOrNullResult();
    }
}
