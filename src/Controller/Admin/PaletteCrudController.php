<?php

namespace App\Controller\Admin;

use App\Entity\Palette;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
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
            TextField::new('code_couleur'),
            TextField::new('depot'),
            DateTimeField::new('date_termine')
                ->setFormTypeOptions([
                    'widget' => 'single_text',
                    'html5' => true,
                ]),
            DateTimeField::new('date_transmise')
                ->setFormTypeOptions([
                    'widget' => 'single_text',
                    'html5' => true,
                ]),
            TextField::new('statut'),
            AssociationField::new('camionPalette'),
        ];
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setDateTimeFormat('yyyy-MM-dd HH:mm'); // Format d'affichage des dates
    }

    public function deleteEntity(EntityManagerInterface $em, $entityInstance): void
    {
        if (!$entityInstance instanceof Palette) return;

        foreach ($entityInstance->getPaletteProduits() as $paletteProduit) {
            $em->remove($paletteProduit);
        }
        parent::deleteEntity($em, $entityInstance);
    }
}
