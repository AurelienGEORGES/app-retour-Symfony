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

    public function searchRetours(array $criteria, int $limit, int $offset)
    {
        $qb = $this->createQueryBuilder('r');
        
        // Ajout des critères dynamiquement
        if (!empty($criteria['numRetour'])) {
            $qb->andWhere('r.num_retour LIKE :numRetour')
                ->setParameter('numRetour', '%' . $criteria['numRetour'] . '%');
        }
        if (!empty($criteria['prenomClient'])) {
            $qb->andWhere('r.prenom_client LIKE :prenomClient')
                ->setParameter('prenomClient', '%' . $criteria['prenomClient'] . '%');
        }
        if (!empty($criteria['nomClient'])) {
            $qb->andWhere('r.nom_client LIKE :nomClient')
                ->setParameter('nomClient', '%' . $criteria['nomClient'] . '%');
        }
        if (!empty($criteria['transporteur'])) {
            $qb->andWhere('r.transporteur = :transporteur')
                ->setParameter('transporteur', $criteria['transporteur']);
        }
        if (!empty($criteria['dateAutorisationDebut']) && !empty($criteria['dateAutorisationFin'])) {
            $qb->andWhere('r.date_autorisation BETWEEN :dateDebutAutorisation AND :dateFinAutorisation')
                ->setParameter('dateDebutAutorisation', $criteria['dateAutorisationDebut'])
                ->setParameter('dateFinAutorisation', $criteria['dateAutorisationFin']);
        }
        if (!empty($criteria['dateReceptionDebut']) && !empty($criteria['dateReceptionFin'])) {
            $qb->andWhere('r.date_traitement BETWEEN :dateDebutReception AND :dateFinReception')
                ->setParameter('dateDebutReception', $criteria['dateReceptionDebut'])
                ->setParameter('dateFinReception', $criteria['dateReceptionFin']);
        }
        if (!empty($criteria['etatColis'])) {
            $qb->andWhere('r.etat = :etatColis')
                ->setParameter('etatColis', $criteria['etatColis']);
        }

        if (!empty($criteria['etatProduit'])) {
            $qb->andWhere('r.etat_produit = :etatProduit')
                ->setParameter('etatProduit', $criteria['etatProduit']);
        }

        // Gestion de la pagination
        $qb->setFirstResult($offset)
            ->setMaxResults($limit);
            
        return $qb->getQuery()->getResult();
    }

    public function countRetours(array $criteria)
    {
        
        $qb = $this->createQueryBuilder('r')
            ->select('COUNT(r.id)');
        
        // Ajout des mêmes critères que dans `searchRetours`
        if (!empty($criteria['numRetour'])) {
            $qb->andWhere('r.num_retour LIKE :numRetour')
                ->setParameter('numRetour', '%' . $criteria['numRetour'] . '%');
        }
        if (!empty($criteria['prenomClient'])) {
            $qb->andWhere('r.prenom_client LIKE :prenomClient')
                ->setParameter('prenomClient', '%' . $criteria['prenomClient'] . '%');
        }
        if (!empty($criteria['nomClient'])) {
            $qb->andWhere('r.nom_client LIKE :nomClient')
                ->setParameter('nomClient', '%' . $criteria['nomClient'] . '%');
        }
        if (!empty($criteria['transporteur'])) {
            $qb->andWhere('r.transporteur = :transporteur')
                ->setParameter('transporteur', $criteria['transporteur']);
        }
        if (!empty($criteria['dateAutorisationDebut']) && !empty($criteria['dateAutorisationFin'])) {
            $qb->andWhere('r.date_autorisation BETWEEN :dateDebutAutorisation AND :dateFinAutorisation')
                ->setParameter('dateDebutAutorisation', $criteria['dateAutorisationDebut'])
                ->setParameter('dateFinAutorisation', $criteria['dateAutorisationFin']);
        }
        if (!empty($criteria['dateReceptionDebut']) && !empty($criteria['dateReceptionFin'])) {
            $qb->andWhere('r.date_traitement BETWEEN :dateDebutReception AND :dateFinReception')
                ->setParameter('dateDebutReception', $criteria['dateReceptionDebut'])
                ->setParameter('dateFinReception', $criteria['dateReceptionFin']);
        }
        if (!empty($criteria['etatColis'])) {
            $qb->andWhere('r.etat = :etatColis')
                ->setParameter('etatColis', $criteria['etatColis']);
        }

        if (!empty($criteria['etatProduit'])) {
            $qb->andWhere('r.etat_produit = :etatProduit')
                ->setParameter('etatProduit', $criteria['etatProduit']);
        }
        
        return $qb->getQuery()->getSingleScalarResult();
    }

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
