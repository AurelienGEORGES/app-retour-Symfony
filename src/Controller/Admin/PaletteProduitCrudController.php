<?php

namespace App\Controller\Admin;

use App\Entity\PaletteProduit;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class PaletteProduitCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return PaletteProduit::class;
    }

    
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            AssociationField::new('palette'),
            IntegerField::new('id_produit'),
            IntegerField::new('quantite'),
        ];
    }
    
}
