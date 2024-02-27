<?php

namespace App\Controller\Admin;

use App\Entity\Bordereau;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use App\Controller\Admin\BordereauCrudController;
use App\Entity\Palette;
use App\Entity\PaletteProduit;
use App\Entity\ProduitLibre;
use App\Entity\Retour;
use App\Entity\RetourProduit;
use App\Entity\RetourProduitReceptionnes;
use App\Entity\Stock;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;

class DashboardController extends AbstractDashboardController
{

    public function __construct(
        private AdminUrlGenerator $adminUrlGenerator
    ) {
        
    }

    #[IsGranted("ROLE_USER")]
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        $url = $this->adminUrlGenerator
        ->setController(BordereauCrudController::class)
        ->generateUrl();
        return $this->redirect($url);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('APP RETOUR');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::section('Bordereaux');
        yield MenuItem::subMenu('Actions', 'fas fa-bars')->setSubItems([
            MenuItem::linkToCrud('create bordereaux', 'fas fa-plus', Bordereau::class)->setAction(Crud::PAGE_NEW),
            MenuItem::linkToCrud('show bordereaux', 'fas fa-eye', Bordereau::class)
        ]);
        yield MenuItem::section('Retours');
        yield MenuItem::subMenu('Actions', 'fas fa-bars')->setSubItems([
            MenuItem::linkToCrud('create retours', 'fas fa-plus', Retour::class)->setAction(Crud::PAGE_NEW),
            MenuItem::linkToCrud('show retours', 'fas fa-eye', Retour::class)
        ]);
        yield MenuItem::section('Produits libre');
        yield MenuItem::subMenu('Actions', 'fas fa-bars')->setSubItems([
            MenuItem::linkToCrud('create produit libre', 'fas fa-plus', ProduitLibre::class)->setAction(Crud::PAGE_NEW),
            MenuItem::linkToCrud('show produit libre', 'fas fa-eye', ProduitLibre::class)
        ]);
        yield MenuItem::section('Produits du retour');
        yield MenuItem::subMenu('Actions', 'fas fa-bars')->setSubItems([
            MenuItem::linkToCrud('create produit du retour', 'fas fa-plus', RetourProduit::class)->setAction(Crud::PAGE_NEW),
            MenuItem::linkToCrud('show produits du retour', 'fas fa-eye', RetourProduit::class)
        ]); 
        yield MenuItem::section('Produits réceptionnés');
        yield MenuItem::subMenu('Actions', 'fas fa-bars')->setSubItems([
            MenuItem::linkToCrud('create produit réceptionnés', 'fas fa-plus', RetourProduitReceptionnes::class)->setAction(Crud::PAGE_NEW),
            MenuItem::linkToCrud('show produits réceptionnés', 'fas fa-eye', RetourProduitReceptionnes::class)
        ]);
        yield MenuItem::section('Stock');
        yield MenuItem::subMenu('Actions', 'fas fa-bars')->setSubItems([
            MenuItem::linkToCrud('create produit stock', 'fas fa-plus', Stock::class)->setAction(Crud::PAGE_NEW),
            MenuItem::linkToCrud('show produits stock', 'fas fa-eye', Stock::class)
        ]); 
        yield MenuItem::section('Palette');
        yield MenuItem::subMenu('Actions', 'fas fa-bars')->setSubItems([
            MenuItem::linkToCrud('create palette', 'fas fa-plus', Palette::class)->setAction(Crud::PAGE_NEW),
            MenuItem::linkToCrud('show palettes', 'fas fa-eye', Palette::class)
        ]); 
        yield MenuItem::section('Produits palette');
        yield MenuItem::subMenu('Actions', 'fas fa-bars')->setSubItems([
            MenuItem::linkToCrud('create produit palette', 'fas fa-plus', PaletteProduit::class)->setAction(Crud::PAGE_NEW),
            MenuItem::linkToCrud('show produits palette', 'fas fa-eye', PaletteProduit::class)
        ]); 
    }
}
