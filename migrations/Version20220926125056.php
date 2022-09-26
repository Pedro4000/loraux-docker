<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220926125056 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE artist ADD fully_scrapped_date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL');
        $this->addSql('COMMENT ON COLUMN artist.fully_scrapped_date IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE label ADD fully_scrapped_date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL');
        $this->addSql('COMMENT ON COLUMN label.fully_scrapped_date IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE release ADD fully_scrapped_date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL');
        $this->addSql('COMMENT ON COLUMN release.fully_scrapped_date IS \'(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE artist DROP fully_scrapped_date');
        $this->addSql('ALTER TABLE label DROP fully_scrapped_date');
        $this->addSql('ALTER TABLE release DROP fully_scrapped_date');
    }
}
