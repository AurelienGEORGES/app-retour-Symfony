<?php

namespace App\Controller;

use App\Entity\Bordereau;
use Doctrine\ORM\EntityManagerInterface;
use function Symfony\Component\Clock\now;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PhotoBordereauController extends AbstractController
{

    private $csrfTokenManager;

    public function __construct(CsrfTokenManagerInterface $csrfTokenManager)
    {
        $this->csrfTokenManager = $csrfTokenManager;
    }

    #[IsGranted("ROLE_USER")]
    #[Route('/photo/bordereau', name: 'app_photo_bordereau')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {

        if ($request->getMethod() === 'POST') {

            // récupération des données du formulaire
            $photo = $request->files->get('photo');
            $commentaire = $request->request->get('commentaire');

            // enregistrement de la photo sur le serveur
            $now = new \DateTime();
            $formattedNow = $now->format('Y-m-d_H-i-s');
            $fileName = 'bordereau_' . $formattedNow . '.jpeg';
            $photoPath = '/uploads/photos/' . $fileName;
            // Déplacez le fichier téléchargé vers le répertoire de destination
            $photo->move($this->getParameter('kernel.project_dir') . '/public/uploads/photos/', $fileName);

            // Enregistrement du nouveau bordereau
            $photoBordereau = new Bordereau();
            $photoBordereau->setPhoto1($photoPath);
            $photoBordereau->setCommentaire($commentaire);
            $photoBordereau->setNumBordereau('bordereau_' . $now->format('Y-m-d_H-i-s'));
            $photoBordereau->setDateReception(now());
            $entityManager->persist($photoBordereau);
            $entityManager->flush();

            $this->addFlash(
                'notice',
                'Le bordereau a bien été enregistré!'
            );
        }

        $csrfTokenProduitPhotoBordereau = $this->csrfTokenManager->getToken('form-photo-bordereau');

        return $this->render('photo_bordereau/index.html.twig', [
            'controller_name' => 'PhotoBordereauController',
        ]);
    }
}
