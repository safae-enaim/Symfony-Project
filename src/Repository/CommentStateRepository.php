<?php

namespace App\Repository;

use App\Entity\CommentState;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CommentState|null find($id, $lockMode = null, $lockVersion = null)
 * @method CommentState|null findOneBy(array $criteria, array $orderBy = null)
 * @method CommentState[]    findAll()
 * @method CommentState[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommentStateRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CommentState::class);
    }

    // /**
    //  * @return CommentState[] Returns an array of CommentState objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?CommentState
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
