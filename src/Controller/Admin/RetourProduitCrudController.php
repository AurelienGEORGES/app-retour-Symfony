<?php

namespace App\Controller\Admin;

use App\Entity\RetourProduit;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class RetourProduitCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return RetourProduit::class;
    }

    
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            AssociationField::new('retour'),
            IntegerField::new('id_produit'),
            IntegerField::new('quantite'),  
        ];
    }
    
}
