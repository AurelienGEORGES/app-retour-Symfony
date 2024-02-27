<?php

namespace App\Controller\Admin;

use App\Entity\RetourProduitReceptionnes;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class RetourProduitReceptionnesCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return RetourProduitReceptionnes::class;
    }

    
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            AssociationField::new('retour'),
            IntegerField::new('id_produit'),
            IntegerField::new('quantite'),
            TextField::new('code_couleur'),
        ];
    }
    
}
