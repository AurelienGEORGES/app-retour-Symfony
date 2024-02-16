<?php

namespace App\Controller;

use App\Entity\Retour;
use App\Form\SearchRetourType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ListeAttendusController extends AbstractController
{
    #[IsGranted("ROLE_USER")]
    #[Route('/liste/attendus', name: 'app_liste_attendus')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {

        $retours = $entityManager->getRepository(Retour::class)->findAll();

        $retourProduits = [];
        foreach ($retours as $retour) {
            $retourProduits = array_merge($retourProduits, $retour->getRetourProduits()->toArray());
        }

        $form = $this->createForm(SearchRetourType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $data = $form->getData();

            $criteria = [];
            if (!empty($data['numRetour'])) {
                $criteria['num_retour'] = $data['numRetour'];
            }

            if (!empty($data['prenomClient'])) {
                $criteria['prenom_client'] = $data['prenomClient'];
            }

            if (!empty($data['nomClient'])) {
                $criteria['nom_client'] = $data['nomClient'];
            }

            if (!empty($data['transporteur'])) {
                $criteria['transporteur'] = $data['transporteur'];
            }
            $retours = $entityManager->getRepository(Retour::class)->findByCriteria($criteria);
        } else {
            $retours = $entityManager->getRepository(Retour::class)->findAll();
        }

        return $this->render('liste_attendus/index.html.twig', [
            'controller_name' => 'ListeAttendusController',
            'form' => $form->createView(),
            'retours' => $retours,
            'retourProduits' => $retourProduits
        ]);
    }
}
