<?php
namespace App\Tests\Entity;

use App\Entity\Retour;
use App\Entity\Bordereau;

class RetourTest extends \PHPUnit\Framework\TestCase
{
    public function testRetour()
    {
        $num_retour = 'retour_2024-08-26_09-32-20';
        $date_autorisation = new \DateTimeImmutable('2024-08-23T09:32:20+00:00');
        $nom_client = 'Lassi';
        $prenom_client = 'Miguel';
        $transporteur = 'DPD';
        $date_traitement = new \DateTimeImmutable('2024-08-24T09:32:20+00:00');
        $etat = 'ouvert';
        $commentaire = "c'est ouvert";
        $photo_1 = 'photo1_2024-08-26_09-32-20.jpeg';
        $photo_2 = 'photo2_2024-08-26_09-32-20.jpeg';
        $photo_3 = 'photo3_2024-08-26_09-32-20.jpeg';
        $photo_4 = 'photo4_2024-08-26_09-32-20.jpeg';
        $photo_5 = 'photo5_2024-08-26_09-32-20.jpeg';
        $bordereau = new Bordereau();
        $etat_produit = 'pièce manquante';
        $etat_02 = 'ouvert';
        $etat_produit_02 = 'pièce manquante';
        $etat_03 = 'ouvert';
        $etat_produit_03 = 'pièce manquante';
        $commentaire_autorisation = "le client n'est pas content";
        $id_produit_photo1 = 220123;
        $id_produit_photo2 = 220124;
        $id_produit_photo3 = 220125;
        $id_produit_photo4 = 220126;
        $id_produit_photo5 = 220127;
        $date_traitement_02 = new \DateTimeImmutable('2024-08-25T09:32:20+00:00');
        $date_traitement_03 = new \DateTimeImmutable('2024-08-26T09:32:20+00:00');
        $retour = new Retour();
        $retour->setNumRetour($num_retour);
        $retour->setDateAutorisation($date_autorisation);
        $retour->setNomClient($nom_client);
        $retour->setPrenomClient($prenom_client);
        $retour->setTransporteur($transporteur);
        $retour->setDateTraitement($date_traitement);
        $retour->setEtat($etat);
        $retour->setCommentaire($commentaire);
        $retour->setPhoto1($photo_1);
        $retour->setPhoto2($photo_2);
        $retour->setPhoto3($photo_3);
        $retour->setPhoto4($photo_4);
        $retour->setPhoto5($photo_5);
        $retour->setBordereau($bordereau);
        $retour->setEtatProduit($etat_produit);
        $retour->setEtatProduit02($etat_produit_02);
        $retour->setEtat02($etat_02);
        $retour->setEtat03($etat_03);
        $retour->setEtatProduit03($etat_produit_03);
        $retour->setCommentaireAutorisation($commentaire_autorisation);
        $retour->setIdProduitPhoto1($id_produit_photo1);
        $retour->setIdProduitPhoto2($id_produit_photo2);
        $retour->setIdProduitPhoto3($id_produit_photo3);
        $retour->setIdProduitPhoto4($id_produit_photo4);
        $retour->setIdProduitPhoto5($id_produit_photo5);
        $retour->setDateTraitement02($date_traitement_02);
        $retour->setDateTraitement03($date_traitement_03);
        $this->assertEquals($num_retour, $retour->getNumRetour());
        $this->assertEquals($date_autorisation, $retour->getDateAutorisation());
        $this->assertEquals($nom_client, $retour->getNomClient());
        $this->assertEquals($prenom_client, $retour->getPrenomClient());
        $this->assertEquals($transporteur, $retour->getTransporteur());
        $this->assertEquals($date_traitement, $retour->getDateTraitement());
        $this->assertEquals($etat, $retour->getEtat());
        $this->assertEquals($commentaire, $retour->getCommentaire());
        $this->assertEquals($photo_1, $retour->getPhoto1());
        $this->assertEquals($photo_2, $retour->getPhoto2());
        $this->assertEquals($photo_3, $retour->getPhoto3());
        $this->assertEquals($photo_4, $retour->getPhoto4());
        $this->assertEquals($photo_5, $retour->getPhoto5());
        $this->assertEquals($bordereau, $retour->getBordereau());
        $this->assertEquals($etat_produit, $retour->getEtatProduit());
        $this->assertEquals($etat_produit_02, $retour->getEtatProduit02());
        $this->assertEquals($etat_02, $retour->getEtat02());
        $this->assertEquals($etat_03, $retour->getEtat03());
        $this->assertEquals($etat_produit_03, $retour->getEtatProduit03());
        $this->assertEquals($commentaire_autorisation, $retour->getCommentaireAutorisation());
        $this->assertEquals($id_produit_photo1, $retour->getIdProduitPhoto1());
        $this->assertEquals($id_produit_photo2, $retour->getIdProduitPhoto2());
        $this->assertEquals($id_produit_photo3, $retour->getIdProduitPhoto3());
        $this->assertEquals($id_produit_photo4, $retour->getIdProduitPhoto4());
        $this->assertEquals($id_produit_photo5, $retour->getIdProduitPhoto5());
        $this->assertEquals($date_traitement_02, $retour->getDateTraitement02());
        $this->assertEquals($date_traitement_03, $retour->getDateTraitement03());
    }
}