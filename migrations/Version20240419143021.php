<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240419143021 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE retour ADD id_produit_photo1 INT DEFAULT NULL, ADD id_produit_photo2 INT DEFAULT NULL, ADD id_produit_photo3 INT DEFAULT NULL, ADD id_produit_photo4 INT DEFAULT NULL, ADD id_produit_photo5 INT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE retour DROP id_produit_photo1, DROP id_produit_photo2, DROP id_produit_photo3, DROP id_produit_photo4, DROP id_produit_photo5');
    }
}
