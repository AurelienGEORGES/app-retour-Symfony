<?php

namespace App\Controller;

use App\Entity\Retour;
use App\Entity\Bordereau;
use App\Form\SearchRetourType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ListeBordereauxController extends AbstractController
{
    #[IsGranted("ROLE_USER")]
    #[Route('/liste/bordereaux', name: 'app_liste_bordereaux')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        $bordereaux = $entityManager->getRepository(Bordereau::class)->findAll();

        $criteria = [];
        if (!empty($request->query->get('date_reception'))) {
            $dateReception = $request->query->get('date_reception');
            $dateTime = new \DateTime($dateReception);
            $formattedDate = $dateTime->format('Y-m-d H:i:s');
            $criteria['date_reception'] = $formattedDate;
            $bordereaux = $entityManager->getRepository(Bordereau::class)->findByDate($criteria);
        }

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

        if ($request->isMethod('GET')) {
            $bordereauId = $request->query->get('bordereau');
            $linkedRetours = $request->query->all('liste', []);

            if ($bordereauId) {
                // Récupérer le Bordereau associé à l'ID
                $bordereau = $entityManager->getRepository(Bordereau::class)->find($bordereauId);

                // Vérifier si le Bordereau existe
                if ($bordereau) {
                    // Parcourir les Retours liés et associer le Bordereau à chaque Retour
                    foreach ($linkedRetours as $retourId) {
                        $retour = $entityManager->getRepository(Retour::class)->find($retourId);

                        // Vérifier si le Retour existe
                        if ($retour) {
                            // Définir le Bordereau associé au Retour
                            $retour->setBordereau($bordereau);

                            // Enregistrer les modifications dans la base de données
                            $entityManager->persist($retour);
                        }
                    }

                    // Exécuter les modifications
                    $entityManager->flush();
                }
            }
        }
        return $this->render('liste_bordereaux/index.html.twig', [
            'controller_name' => 'ListeBordereauxController',
            'bordereaux' => $bordereaux,
            'form' => $form->createView(),
            'retours' => $retours,
            'retourProduits' => $retourProduits,
        ]);
    }
}
