<?php

namespace App\Controller\Admin;

use App\Entity\Retour;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;

class RetourCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Retour::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('num_retour'),
            DateTimeField::new('date_autorisation'),
            TextField::new('nom_client'),
            TextField::new('prenom_client'),
            TextField::new('transporteur'),
            DateTimeField::new('date_traitement')->hideOnForm(),
            TextField::new('etat'),
            TextField::new('commentaire'),
            ImageField::new('photo_1')->setBasePath('litiges/photos')->setUploadDir('public/litiges/photos'),
            ImageField::new('photo_2')->setBasePath('litiges/photos')->setUploadDir('public/litiges/photos'),
            ImageField::new('photo_3')->setBasePath('litiges/photos')->setUploadDir('public/litiges/photos'),
            ImageField::new('photo_4')->setBasePath('litiges/photos')->setUploadDir('public/litiges/photos'),
            ImageField::new('photo_5')->setBasePath('litiges/photos')->setUploadDir('public/litiges/photos'),            
            AssociationField::new('bordereau'),
        ];
    }

    public function persistEntity(EntityManagerInterface $em, $entityInstance): void
    {
        if (!$entityInstance instanceof Retour) return;
        $entityInstance->setDateTraitement(new \DateTimeImmutable);
        parent::persistEntity($em, $entityInstance);
    }

    public function deleteEntity(EntityManagerInterface $em, $entityInstance): void
    {
        if (!$entityInstance instanceof Retour) return;

        foreach($entityInstance->getRetourProduits() as $retour) {
            $em->remove($retour);
        }

        foreach($entityInstance->getRetourProduitReceptionnes() as $retour) {
            $em->remove($retour);
        }

        parent::deleteEntity($em, $entityInstance);
    }

}
