<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240320092100 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE retour ADD etat_02 VARCHAR(30) DEFAULT NULL, ADD etat_produit_02 VARCHAR(30) DEFAULT NULL, ADD etat_03 VARCHAR(30) DEFAULT NULL, ADD etat_produit_03 VARCHAR(30) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE retour DROP etat_02, DROP etat_produit_02, DROP etat_03, DROP etat_produit_03');
    }
}
