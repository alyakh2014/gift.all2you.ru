<?php

namespace App\Repository;

use App\Entity\LogRoutes;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method LogRoutes|null find($id, $lockMode = null, $lockVersion = null)
 * @method LogRoutes|null findOneBy(array $criteria, array $orderBy = null)
 * @method LogRoutes[]    findAll()
 * @method LogRoutes[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LogRoutesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LogRoutes::class);
    }

    // /**
    //  * @return LogRoutes[] Returns an array of LogRoutes objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('l.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?LogRoutes
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
