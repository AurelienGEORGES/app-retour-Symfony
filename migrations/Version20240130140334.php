<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240130140334 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE retour_produit (id INT AUTO_INCREMENT NOT NULL, retour_id INT NOT NULL, id_produit INT NOT NULL, quantite SMALLINT NOT NULL, INDEX IDX_DBFCC8AB28D6031F (retour_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE retour_produit_receptionnes (id INT AUTO_INCREMENT NOT NULL, retour_id INT NOT NULL, INDEX IDX_AADD062D28D6031F (retour_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE retour_produit ADD CONSTRAINT FK_DBFCC8AB28D6031F FOREIGN KEY (retour_id) REFERENCES retour (id)');
        $this->addSql('ALTER TABLE retour_produit_receptionnes ADD CONSTRAINT FK_AADD062D28D6031F FOREIGN KEY (retour_id) REFERENCES retour (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE retour_produit DROP FOREIGN KEY FK_DBFCC8AB28D6031F');
        $this->addSql('ALTER TABLE retour_produit_receptionnes DROP FOREIGN KEY FK_AADD062D28D6031F');
        $this->addSql('DROP TABLE retour_produit');
        $this->addSql('DROP TABLE retour_produit_receptionnes');
    }
}
