<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220926090055 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE artist ADD discogs_id INT NOT NULL');
        $this->addSql('ALTER TABLE artist ADD name VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE artist ADD fully_scrapped BOOLEAN NOT NULL');
        $this->addSql('ALTER TABLE artist ADD created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL');
        $this->addSql('COMMENT ON COLUMN artist.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE label ADD discogs_id INT NOT NULL');
        $this->addSql('ALTER TABLE label ADD name VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE label ADD fully_scrapped BOOLEAN NOT NULL');
        $this->addSql('ALTER TABLE label ADD created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL');
        $this->addSql('COMMENT ON COLUMN label.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE release ADD discogs_id INT NOT NULL');
        $this->addSql('ALTER TABLE release ADD name VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE release ADD fully_scrapped BOOLEAN NOT NULL');
        $this->addSql('ALTER TABLE release ADD created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL');
        $this->addSql('COMMENT ON COLUMN release.created_at IS \'(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE label DROP discogs_id');
        $this->addSql('ALTER TABLE label DROP name');
        $this->addSql('ALTER TABLE label DROP fully_scrapped');
        $this->addSql('ALTER TABLE label DROP created_at');
        $this->addSql('ALTER TABLE release DROP discogs_id');
        $this->addSql('ALTER TABLE release DROP name');
        $this->addSql('ALTER TABLE release DROP fully_scrapped');
        $this->addSql('ALTER TABLE release DROP created_at');
        $this->addSql('ALTER TABLE artist DROP discogs_id');
        $this->addSql('ALTER TABLE artist DROP name');
        $this->addSql('ALTER TABLE artist DROP fully_scrapped');
        $this->addSql('ALTER TABLE artist DROP created_at');
    }
}
