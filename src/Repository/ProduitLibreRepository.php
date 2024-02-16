<?php

namespace App\Repository;

use App\Entity\ProduitLibre;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ProduitLibre>
 *
 * @method ProduitLibre|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProduitLibre|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProduitLibre[]    findAll()
 * @method ProduitLibre[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProduitLibreRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProduitLibre::class);
    }

    //    /**
    //     * @return ProduitLibre[] Returns an array of ProduitLibre objects
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

    //    public function findOneBySomeField($value): ?ProduitLibre
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
        }

        // Vous pouvez ajouter d'autres conditions, tri, etc. si nécessaire

        return $qb->getQuery()->getResult();
    }
}
