<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240216104643 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE palette (id INT AUTO_INCREMENT NOT NULL, code_couleur VARCHAR(10) DEFAULT NULL, depot VARCHAR(15) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE palette_produit (id INT AUTO_INCREMENT NOT NULL, palette_id INT DEFAULT NULL, id_produit INT NOT NULL, quantite SMALLINT NOT NULL, INDEX IDX_7B87DDE2908BC74 (palette_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE palette_produit ADD CONSTRAINT FK_7B87DDE2908BC74 FOREIGN KEY (palette_id) REFERENCES palette (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE palette_produit DROP FOREIGN KEY FK_7B87DDE2908BC74');
        $this->addSql('DROP TABLE palette');
        $this->addSql('DROP TABLE palette_produit');
    }
}
