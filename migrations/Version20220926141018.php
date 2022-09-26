<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220926141018 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE artist ALTER fully_scrapped_date TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('COMMENT ON COLUMN artist.fully_scrapped_date IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE label ALTER fully_scrapped_date TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('COMMENT ON COLUMN label.fully_scrapped_date IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE release ALTER release_date DROP NOT NULL');
        $this->addSql('ALTER TABLE release ALTER fully_scrapped_date TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('COMMENT ON COLUMN release.fully_scrapped_date IS \'(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE label ALTER fully_scrapped_date TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('COMMENT ON COLUMN label.fully_scrapped_date IS NULL');
        $this->addSql('ALTER TABLE artist ALTER fully_scrapped_date TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('COMMENT ON COLUMN artist.fully_scrapped_date IS NULL');
        $this->addSql('ALTER TABLE release ALTER release_date SET NOT NULL');
        $this->addSql('ALTER TABLE release ALTER fully_scrapped_date TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('COMMENT ON COLUMN release.fully_scrapped_date IS NULL');
    }
}
