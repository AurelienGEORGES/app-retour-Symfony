<?php

namespace App\Controller;

use App\Entity\Bordereau;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use function Symfony\Component\Clock\now;

class PhotoBordereauController extends AbstractController
{
    #[IsGranted("ROLE_USER")]
    #[Route('/photo/bordereau', name: 'app_photo_bordereau')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {

        if ($request->getMethod() === 'POST') {
            $photo = $request->files->get('photo');
            $commentaire = $request->request->get('commentaire');

            // Vérifiez si une photo a été soumise
            if ($photo) {
                // Obtenez la date et l'heure actuelles
                $now = new \DateTime();

                // Formattez la date et l'heure actuelles en chaîne de caractères
                $formattedNow = $now->format('Y-m-d_H-i-s');

                $fileName = 'bordereau_' . $formattedNow . '.jpeg';

                // Définissez le chemin d'accès complet pour sauvegarder le fichier
                //$photoPath = $this->getParameter('kernel.project_dir') . '/public/uploads/photos/' . $fileName;
                $photoPath = '/uploads/photos/' . $fileName;
                // Déplacez le fichier téléchargé vers le répertoire de destination
                $photo->move($this->getParameter('kernel.project_dir') . '/public/uploads/photos/', $fileName);
            } else {
                // Gérez le cas où aucune photo n'a été soumise
                // Peut-être afficher un message d'erreur ou rediriger l'utilisateur vers une autre page
                return new Response('Aucune photo n\'a été soumise', Response::HTTP_BAD_REQUEST);
            }

            // Créez une nouvelle instance de l'entité PhotoBordereau
            $photoBordereau = new Bordereau();
            $photoBordereau->setPhoto1($photoPath);
            $photoBordereau->setCommentaire($commentaire);
            $photoBordereau->setNumBordereau('bordereau_'.$now->format('Y-m-d_H-i-s'));
            $photoBordereau->setDateReception(now());

            // Enregistrez l'entité dans la base de données
            $entityManager->persist($photoBordereau);
            $entityManager->flush();

            $this->addFlash(
                'notice',
                'Le formulaire a bien été enregistré!'
            );
        }
        return $this->render('photo_bordereau/index.html.twig', [
            'controller_name' => 'PhotoBordereauController',
        ]);
    }
}
