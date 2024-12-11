<?php

namespace App\Controller;

use App\Entity\Camion;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ListeCamionController extends AbstractController
{
    #[Route('/liste/camion', name: 'app_liste_camion')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {

        $allCamions = $entityManager->getRepository(Camion::class)->findAll();

        if (!empty($request)) {

            $statutCamion = $request->query->get('recherche-statut-camion');
            $couleurCamion = $request->query->get('recherche-couleur-camion');
            $dateCreationCamion = $request->query->get('recherche-date-camion');

            $criteria = [];

            if ($statutCamion) {
                $criteria['statut'] = $statutCamion;
                $allCamions = $entityManager->getRepository(Camion::class)->findByCriteria($criteria);
            }
            if ($couleurCamion) {
                $criteria['couleur'] = $couleurCamion;
                $allCamions = $entityManager->getRepository(Camion::class)->findByCriteria($criteria);
            }
            if ($dateCreationCamion) {
                $dateTime = new \DateTime($dateCreationCamion);
                $formattedDate = $dateTime->format('Y-m-d');
                $allCamions = $entityManager->getRepository(Camion::class)->findByDate($formattedDate);
            }
        }

        if ($request->isMethod('POST') && !empty($request->request->get('depot-camion'))) {
            $choixDepot = $request->request->get('depot-camion');
            $idCamion = $request->request->get('id-camion');
            $camion = $entityManager->getRepository(Camion::class)->find($idCamion);
            $palettesCamion = $camion->getCamionPalettes();
            $palettesCamion->initialize(); 
            $currentDate = new \DateTime();
            $camion->setDateE($currentDate);
            $camion->setDepot($choixDepot);
            $entityManager->persist($camion);
            $entityManager->flush();

            $this->addFlash(
                'notice',
                'Le camion a bien été envoyé!'
            );
        }

        return $this->render('liste_camion/index.html.twig', [
            'controller_name' => 'ListeCamionController',
            'camions' => $allCamions,
        ]);
    }
}
