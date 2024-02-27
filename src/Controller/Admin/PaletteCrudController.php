<?php

namespace App\Controller\Admin;

use App\Entity\Palette;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class PaletteCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Palette::class;
    }

    
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            IntegerField::new('code_couleur'),
            TextField::new('depot'),
        ];
    }

    public function deleteEntity(EntityManagerInterface $em, $entityInstance): void
    {
        if (!$entityInstance instanceof Palette) return;

        foreach($entityInstance->getPaletteProduits() as $paletteProduit) {
            $em->remove($paletteProduit);
        }
        parent::deleteEntity($em, $entityInstance);
    }
}
