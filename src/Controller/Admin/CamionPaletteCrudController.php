<?php

namespace App\Controller\Admin;

use App\Entity\CamionPalette;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class CamionPaletteCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return CamionPalette::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            AssociationField::new('camion'),
            AssociationField::new('palette'),
        ];
    }  
}
