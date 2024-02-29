<?php

namespace App\Controller;

use App\Entity\Retour;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class ApiController extends AbstractController
{
    #[IsGranted("ROLE_USER")]
    #[Route('/api', name: 'app_api')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $retours = $entityManager->getRepository(Retour::class)->findAll();

        $retoursArray = [];
        foreach ($retours as $retour) {
            $retoursArray[] = [
                'numRetour' => $retour->getNumRetour(),
                'dateAutorisation' => $retour->getDateAutorisation(),
                'nomClient' => $retour->getNomClient(),
                'prenomClient' => $retour->getPrenomClient(),
                'transporteur' => $retour->getTransporteur(),
                'dateTraitement' => $retour->getDateTraitement(),
                'etat' => $retour->getEtat(),
                'commentaire' => $retour->getCommentaire(),
                'photo1' => $retour->getPhoto1(),
                'photo2' => $retour->getPhoto2(),
                'photo3' => $retour->getPhoto3(),
                'photo4' => $retour->getPhoto4(),
                'photo5' => $retour->getPhoto5(),
            ];
        }

        // Convertir le tableau en JSON et le renvoyer dans la réponse
        $response = new Response(json_encode($retoursArray));
        //$response->headers->set('Content-Type', 'application/json');
        // dd($response);
        return $response;
    }
}
