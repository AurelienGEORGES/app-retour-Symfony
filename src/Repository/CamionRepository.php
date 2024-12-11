<?php

namespace App\Repository;

use App\Entity\Camion;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Camion>
 */
class CamionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Camion::class);
    }

    //    /**
    //     * @return Camion[] Returns an array of Camion objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('c.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Camion
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
    public function findByDate($date)
    {
        $dateStart = new \DateTime($date);
        // $dateEnd = (clone $dateStart)->modify('+1 day');

        return $this->createQueryBuilder('e')
            ->andWhere('e.dateC >= :dateStart')
            // ->andWhere('e.date_reception < :dateEnd')
            ->setParameter('dateStart', $dateStart->format('Y-m-d'))
            // ->setParameter('dateEnd', $dateEnd->format('Y-m-d'))
            ->getQuery()
            ->getResult();
    }

    public function findByCriteria(array $criteria = [])
    {
        $qb = $this->createQueryBuilder('r');

        // Ajoutez les conditions de recherche en fonction des critères fournis
        foreach ($criteria as $field => $value) {
            $qb->andWhere("r.$field = :$field")->setParameter($field, $value);
            //$qb->andWhere("r.$field LIKE :$field")->setParameter($field, "%$value%");
        }

        // Vous pouvez ajouter d'autres conditions, tri, etc. si nécessaire

        return $qb->getQuery()->getResult();
    }
}
