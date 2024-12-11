<?php

namespace App\Controller\Admin;

use App\Entity\Retour;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

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
            DateTimeField::new('date_traitement'),
            DateTimeField::new('date_traitement_02'),
            DateTimeField::new('date_traitement_03'),
            TextField::new('commentaire'),
            TextField::new('etat'),
            TextField::new('etat_produit'),
            TextField::new('etat_02'),
            TextField::new('etat_produit_02'),
            TextField::new('etat_03'),
            TextField::new('etat_produit_03'),
            TextField::new('commentaire_autorisation'),
            IntegerField::new('id_produit_photo1'),
            IntegerField::new('id_produit_photo2'),
            IntegerField::new('id_produit_photo3'),
            IntegerField::new('id_produit_photo4'),
            IntegerField::new('id_produit_photo5'),
            ImageField::new('photo_1')->setBasePath('litiges/photos')->setUploadDir('public/litiges/photos'),
            ImageField::new('photo_2')->setBasePath('litiges/photos')->setUploadDir('public/litiges/photos'),
            ImageField::new('photo_3')->setBasePath('litiges/photos')->setUploadDir('public/litiges/photos'),
            ImageField::new('photo_4')->setBasePath('litiges/photos')->setUploadDir('public/litiges/photos'),
            ImageField::new('photo_5')->setBasePath('litiges/photos')->setUploadDir('public/litiges/photos'),            
            AssociationField::new('bordereau'),
            AssociationField::new('retourProduits'),
            AssociationField::new('retourProduitReceptionnes'),
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
