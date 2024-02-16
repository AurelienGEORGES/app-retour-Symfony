<?php

namespace App\Repository;

use App\Entity\Bordereau;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Bordereau>
 *
 * @method Bordereau|null find($id, $lockMode = null, $lockVersion = null)
 * @method Bordereau|null findOneBy(array $criteria, array $orderBy = null)
 * @method Bordereau[]    findAll()
 * @method Bordereau[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BordereauRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Bordereau::class);
    }

    //    /**
    //     * @return Bordereau[] Returns an array of Bordereau objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('b')
    //            ->andWhere('b.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('b.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Bordereau
    //    {
    //        return $this->createQueryBuilder('b')
    //            ->andWhere('b.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    public function findByDate($date)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.date_reception > :date')
            ->setParameter('date', $date)
            ->getQuery()
            ->getResult();
    }
    
}
