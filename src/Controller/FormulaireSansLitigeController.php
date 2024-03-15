<?php

namespace App\Controller;

use App\Entity\Stock;
use App\Entity\Retour;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\RetourProduitReceptionnes;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class FormulaireSansLitigeController extends AbstractController
{
    private $csrfTokenManager;

    public function __construct(CsrfTokenManagerInterface $csrfTokenManager)
    {
        $this->csrfTokenManager = $csrfTokenManager;
    }

    #[IsGranted("ROLE_USER")]
    #[Route('/formulaire/sans/litige/{id}', name: 'app_formulaire_sans_litige')]
    public function index($id, EntityManagerInterface $entityManager, Request $request): Response
    {

        $retour = $entityManager->getRepository(Retour::class)->find($id);
        $retourObj = $entityManager->getRepository(Retour::class)->find($retour->getId());
        $numretour = $retour->getNumRetour();
        $transporteur = $retour->getTransporteur();
        $retourProduits = array_merge($retour->getRetourProduits()->toArray());

        if ($request->isMethod('POST')) {

            $retourTraite = $entityManager->getRepository(Retour::class)->find($id);
            $currentDate = new \DateTime();
            $retourTraite->setDateTraitement($currentDate);
            $numRetourTraite = $retourTraite->getNumRetour();
            if (substr($numRetourTraite, -3) === '-01') {
                $numRetourTraite = substr_replace($numRetourTraite, '-02', -3);
            } else if (substr($numRetourTraite, -3) === '-02') {
                $numRetourTraite = substr_replace($numRetourTraite, '-03', -3);
            } else if (substr($numRetourTraite, -3) === '-03') {
                $numRetourTraite = substr_replace($numRetourTraite, '-04', -3);
            } else {
                $numRetourTraite .= '-01';
            }
            $retourTraite->setNumretour($numRetourTraite);
            $entityManager->persist($retourTraite);
            foreach ($retourProduits as $retourProduit) {
                if (
                    !empty($request->request->get('id-form-sans-litige_' . $retourProduit->getId()))
                    && !empty($request->request->get('id-form-sans-litige_' . $retourProduit->getId()))
                    && !empty($request->request->get('quantite-form-sans-litige_' . $retourProduit->getId()))
                ) {
                    $idProduitReceptionnes = $request->request->get('id-form-sans-litige_' . $retourProduit->getId());
                    $codeCouleur = $request->request->get('code-couleur-form-sans-litige_' . $retourProduit->getId());
                    $quantite = $request->request->get('quantite-form-sans-litige_' . $retourProduit->getId());
                    $retourProduit = new RetourProduitReceptionnes();
                    $retourProduit->setIdproduit($idProduitReceptionnes);
                    $retourProduit->setCodeCouleur($codeCouleur);
                    $retourProduit->setQuantite($quantite);
                    $retourProduit->setRetour($retourObj);
                    $retourProduit->setDateReception($currentDate);
                    $entityManager->persist($retourProduit);
                    $stock = new Stock();
                    $stock->setIdProduit($idProduitReceptionnes);
                    $stock->setQuantite($quantite);
                    $stock->setCodeCouleur($codeCouleur);
                    $entityManager->persist($stock);
                }
            }

            $idProduits = $request->request->all('id-form-sans-litige', []);
            $codeCouleurs = $request->request->all('code-couleur-form-sans-litige', []);
            $quantites = $request->request->all('quantite-form-sans-litige', []);

            foreach ($idProduits as $index => $idProduit) {
                $produit = new RetourProduitReceptionnes();
                $produit->setIdproduit($idProduit);
                $produit->setCodeCouleur($codeCouleurs[$index]);
                $produit->setQuantite($quantites[$index]);
                $produit->setRetour($retourObj);
                $entityManager->persist($produit);
            }

            $idStockProduits = $request->request->all('id-form-sans-litige', []);
            $codeCouleursStock = $request->request->all('code-couleur-form-sans-litige', []);
            $quantitesStock = $request->request->all('quantite-form-sans-litige', []);

            foreach ($idStockProduits as $index => $idProduit) {
                $stock = new Stock();
                $stock->setIdProduit($idProduit);
                $stock->setQuantite($quantitesStock[$index]); 
                $stock->setCodeCouleur($codeCouleursStock[$index]);
                $stock->setDateReception($currentDate); 
                $entityManager->persist($stock);
            }

            $entityManager->flush();

            $this->addFlash(
                'notice',
                'Le formulaire a bien été enregistré!'
            );
        }

        $csrfTokenProduitSansLitige = $this->csrfTokenManager->getToken('form-sans-litige');

        return $this->render('formulaire_sans_litige/index.html.twig', [
            'controller_name' => 'FormulaireSansLitigeController',
            'transporteur' => $transporteur,
            'numretour' => $numretour,
            'retourProduits' => $retourProduits,
            'id' => $id
        ]);
    }
}
