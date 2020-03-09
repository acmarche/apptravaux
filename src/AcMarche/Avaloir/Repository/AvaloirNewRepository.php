<?php

namespace AcMarche\Avaloir\Repository;

use AcMarche\Avaloir\Entity\AvaloirNew;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method AvaloirNew|null find($id, $lockMode = null, $lockVersion = null)
 * @method AvaloirNew|null findOneBy(array $criteria, array $orderBy = null)
 * @method AvaloirNew[]    findAll()
 * @method AvaloirNew[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AvaloirNewRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AvaloirNew::class);
    }

    public function flush()
    {
        $this->_em->flush();
    }

    public function persist(AvaloirNew $avaloir)
    {
        $this->_em->persist($avaloir);
    }
}
