<?php

namespace App\Repository;

use App\Entity\Blogresponse;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Blogresponse|null find($id, $lockMode = null, $lockVersion = null)
 * @method Blogresponse|null findOneBy(array $criteria, array $orderBy = null)
 * @method Blogresponse[]    findAll()
 * @method Blogresponse[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BlogresponseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Blogresponse::class);
    }

//    /**
//     * @return Blogresponse[] Returns an array of Blogresponse objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('b.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Blogresponse
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
