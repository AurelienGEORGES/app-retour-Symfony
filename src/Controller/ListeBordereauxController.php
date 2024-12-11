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
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ListeBordereauxController extends AbstractController
{

    private $csrfTokenManager;

    public function __construct(CsrfTokenManagerInterface $csrfTokenManager)
    {
        $this->csrfTokenManager = $csrfTokenManager;
    }

    #[IsGranted("ROLE_USER")]
    #[Route('/liste/bordereaux', name: 'app_liste_bordereaux', methods: ['GET', 'POST'])]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        $retours = [];
        $bordereaux = [];

        $criteria = [];
        if (!empty($request->query->get('date_reception'))) {
            $dateReception = $request->query->get('date_reception');
            $dateTime = new \DateTime($dateReception);
            $formattedDate = $dateTime->format('Y-m-d H:i:s');
            $criteria['date_reception'] = $formattedDate;
            $bordereaux = $entityManager->getRepository(Bordereau::class)->findByDate($criteria);
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
            
        } 

        if ($request->isMethod('GET') && !empty($request->query->get('bordereau')) && !empty($request->query->all('liste', []))) {
            $bordereauId = $request->query->get('bordereau');
            $linkedRetours = $request->query->all('liste', []);

            if ($bordereauId) {

                $bordereau = $entityManager->getRepository(Bordereau::class)->find($bordereauId);

                if ($bordereau) {

                    foreach ($linkedRetours as $retourId) {

                        $retour = $entityManager->getRepository(Retour::class)->find($retourId);

                        if ($retour) {
                            
                            $retour->setBordereau($bordereau);
                            $retourToModified = $retour->getNumRetour();
                            $chaine = preg_replace('/^NT/', 'RETSA', $retourToModified);
                            $retour->setNumRetour($chaine);
                            $entityManager->persist($retour);
                        }
                    }

                    $entityManager->flush();

                    $this->addFlash(
                        'notice',
                        'Le bordereau a bien été lié aux attendus ou sans attendus!'
                    );
                }
            }
        }

        $csrfTokenDateBordereau = $this->csrfTokenManager->getToken('form-date-bordereau');
        $csrfTokenLierBordereau = $this->csrfTokenManager->getToken('form-lier-bordereau');

        return $this->render('liste_bordereaux/index.html.twig', [
            'controller_name' => 'ListeBordereauxController',
            'bordereaux' => $bordereaux,
            'form' => $form->createView(),
            'retours' => $retours,
        ]);
    }
}
