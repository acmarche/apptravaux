<?php

namespace AcMarche\Travaux\Repository;

use AcMarche\Travaux\Entity\Etat;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Etat|null find($id, $lockMode = null, $lockVersion = null)
 * @method Etat|null findOneBy(array $criteria, array $orderBy = null)
 * @method Etat[]    findAll()
 * @method Etat[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EtatRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Etat::class);
    }
    /**
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getForList()
    {
        $qb = $this->createQueryBuilder('etat');
        $qb->orderBy('etat.intitule', 'DESC');
        return $qb;
    }

    /**
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getForListDefault()
    {
        $qb = $this->createQueryBuilder('etat');
        $qb->andWhere('etat.intitule LIKE :titre')
            ->setParameter('titre', "Nouveau");

        return $qb;
    }

    /**
     * Pour formulaire avec liste deroulante
     * @return array
     */
    public function getForSearch()
    {
        $qb = $this->createQueryBuilder('etat');

        $qb->orderBy('etat.intitule');
        $query = $qb->getQuery();

        $results = $query->getResult();
        $etats = array();

        foreach ($results as $etat) {
            $etats[$etat->getIntitule()] = $etat->getId();
        }

        return $etats;
    }
}
