<?php

namespace App\Repository;

use App\Entity\PaletteProduit;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PaletteProduit>
 *
 * @method PaletteProduit|null find($id, $lockMode = null, $lockVersion = null)
 * @method PaletteProduit|null findOneBy(array $criteria, array $orderBy = null)
 * @method PaletteProduit[]    findAll()
 * @method PaletteProduit[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PaletteProduitRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PaletteProduit::class);
    }

    //    /**
    //     * @return PaletteProduit[] Returns an array of PaletteProduit objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('p.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?PaletteProduit
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

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

    public function findByDate($date)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.date_reception > :date')
            ->setParameter('date', $date)
            ->getQuery()
            ->getResult();
    }
}
