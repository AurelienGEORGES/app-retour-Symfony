<?php

namespace App\Controller\Admin;

use App\Entity\ProduitLibre;
use Doctrine\DBAL\Types\SmallIntType;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class ProduitLibreCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return ProduitLibre::class;
    }

   
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            IntegerField::new('id_produit'),
            TextField::new('code_couleur'),
            IntegerField::new('quantite'),
            TextField::new('transporteur')
        ];
    }
    
}
