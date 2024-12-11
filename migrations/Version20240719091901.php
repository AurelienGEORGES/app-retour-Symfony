<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240719091901 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE camion_palette (id INT AUTO_INCREMENT NOT NULL, camion_id INT NOT NULL, palette_id INT NOT NULL, INDEX IDX_DC489143A706D3 (camion_id), UNIQUE INDEX UNIQ_DC48914908BC74 (palette_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE camion_palette ADD CONSTRAINT FK_DC489143A706D3 FOREIGN KEY (camion_id) REFERENCES camion (id)');
        $this->addSql('ALTER TABLE camion_palette ADD CONSTRAINT FK_DC48914908BC74 FOREIGN KEY (palette_id) REFERENCES palette (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE camion_palette DROP FOREIGN KEY FK_DC489143A706D3');
        $this->addSql('ALTER TABLE camion_palette DROP FOREIGN KEY FK_DC48914908BC74');
        $this->addSql('DROP TABLE camion_palette');
    }
}
