<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240130124413 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE retour (id INT AUTO_INCREMENT NOT NULL, num_retour VARCHAR(30) DEFAULT NULL, date_autorisation DATE DEFAULT NULL, nom_client VARCHAR(20) DEFAULT NULL, prenom_client VARCHAR(20) DEFAULT NULL, transporteur VARCHAR(30) DEFAULT NULL, date_traitement DATE DEFAULT NULL, etat VARCHAR(255) DEFAULT NULL, commentaire VARCHAR(500) DEFAULT NULL, photo_1 VARCHAR(100) DEFAULT NULL, photo_2 VARCHAR(100) DEFAULT NULL, photo_3 VARCHAR(100) DEFAULT NULL, photo_4 VARCHAR(100) DEFAULT NULL, photo_5 VARCHAR(100) DEFAULT NULL, id_bordereau SMALLINT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE retour');
    }
}
