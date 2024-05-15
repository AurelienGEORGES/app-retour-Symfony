<?php

namespace App\Repository;

use App\Entity\Retour;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Retour>
 *
 * @method Retour|null find($id, $lockMode = null, $lockVersion = null)
 * @method Retour|null findOneBy(array $criteria, array $orderBy = null)
 * @method Retour[]    findAll()
 * @method Retour[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RetourRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Retour::class);
    }

    //    /**
    //     * @return Retour[] Returns an array of Retour objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('r.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Retour
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
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
            // $qb->andWhere("r.$field = :$field")->setParameter($field, $value);
            $qb->andWhere("r.$field LIKE :$field")->setParameter($field, "%$value%");
        }

        // Vous pouvez ajouter d'autres conditions, tri, etc. si nécessaire

        return $qb->getQuery()->getResult();
    }
}
