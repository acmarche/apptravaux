<?php

namespace AcMarche\Avaloir\Repository;

use AcMarche\Avaloir\Entity\Avaloir;
use AcMarche\Avaloir\Entity\Commentaire;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Commentaire|null find($id, $lockMode = null, $lockVersion = null)
 * @method Commentaire|null findOneBy(array $criteria, array $orderBy = null)
 * @method Commentaire[]    findAll()
 * @method Commentaire[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommentaireRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Commentaire::class);
    }

    public function flush()
    {
        $this->_em->flush();
    }

    public function persist(Commentaire $avaloir)
    {
        $this->_em->persist($avaloir);
    }

    /**
     * @param array $args
     * @return Commentaire[]
     */
    public function findByAvaloir(Avaloir $avaloir)
    {
        return $this->createQueryBuilder('commentaire')
            ->andWhere('commentaire.avaloir = :avaloir')
            ->setParameter('avaloir', $avaloir)
            ->addOrderBy('commentaire.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }


}
