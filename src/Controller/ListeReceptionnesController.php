<?php

namespace App\Controller;

use App\Entity\Retour;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\RetourProduitReceptionnes;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ListeReceptionnesController extends AbstractController
{
    #[IsGranted("ROLE_USER")]
    #[Route('/liste/receptionnes', name: 'app_liste_receptionnes')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {

        $retours = $entityManager->getRepository(Retour::class)->findAll();

        if (!empty($request)) {

            $idProduitReceptionne = $request->query->get('recherche-id-receptionnes');
            $numeroRetourReceptionne = $request->query->get('recherche-numero-receptionnes');

            $criteriaId = [];
            if ($idProduitReceptionne) {
                $criteriaId['id_produit'] = $idProduitReceptionne;
                $retoursProduitsReceptionnesIds = $entityManager->getRepository(RetourProduitReceptionnes::class)->findByCriteria($criteriaId);
                $retours = [];
                foreach ($retoursProduitsReceptionnesIds as $retourProduitReceptionnesId) {
                    $retourProduitReceptionnes = $entityManager->getRepository(RetourProduitReceptionnes::class)->find($retourProduitReceptionnesId);
                    if ($retourProduitReceptionnes && $retourProduitReceptionnes->getRetour()) {
                        $retours[] = $retourProduitReceptionnes->getRetour();
                    }
                }
            }
            $criteriaNum = [];
            if ($numeroRetourReceptionne) {
                $criteriaNum['num_retour'] = $numeroRetourReceptionne;
                $retours = $entityManager->getRepository(Retour::class)->findByCriteria($criteriaNum);
            }
        } else {
            $retours = $entityManager->getRepository(Retour::class)->findAll();
        }

        $retourProduitsReceptionnes = [];
        foreach ($retours as $retour) {
            $retourProduitsReceptionnes = array_merge($retourProduitsReceptionnes, $retour->getRetourProduitReceptionnes()->toArray());
        }

        return $this->render('liste_receptionnes/index.html.twig', [
            'controller_name' => 'ListeReceptionnesController',
            'retours' => $retours,
            'retourProduitsReceptionnes' => $retourProduitsReceptionnes
        ]);
    }
}
