<?php

namespace App\Controller\Admin;

use App\Entity\Bordereau;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class BordereauCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Bordereau::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('num_bordereau'),
            DateTimeField::new('date_reception')->hideOnForm(),
            ImageField::new('photo_1')->setBasePath('uploads/photos')->setUploadDir('public/uploads/photos'),
            ImageField::new('photo_2')->setBasePath('uploads/photos')->setUploadDir('public/uploads/photos'),
            TextField::new('commentaire'),
        ];
    }

    public function persistEntity(EntityManagerInterface $em, $entityInstance): void
    {
        if (!$entityInstance instanceof Bordereau) return;
        $entityInstance->setDateReception(new \DateTimeImmutable);
        parent::persistEntity($em, $entityInstance);
    }

    public function deleteEntity(EntityManagerInterface $em, $entityInstance): void
    {
        if (!$entityInstance instanceof Bordereau) return;

        foreach($entityInstance->getRetours() as $retour) {
            $retour->setBordereau(NULL);
        }
        parent::deleteEntity($em, $entityInstance);
    }
}
