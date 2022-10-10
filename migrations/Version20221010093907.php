<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221010093907 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE artist ADD number_scrapped INT DEFAULT NULL');
        $this->addSql('ALTER TABLE artist ADD total_items INT DEFAULT NULL');
        $this->addSql('ALTER TABLE label ADD number_scrapped INT DEFAULT NULL');
        $this->addSql('ALTER TABLE label ADD total_items INT DEFAULT NULL');
        $this->addSql('ALTER TABLE release ADD number_scrapped INT DEFAULT NULL');
        $this->addSql('ALTER TABLE release ADD total_items INT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE release DROP number_scrapped');
        $this->addSql('ALTER TABLE release DROP total_items');
        $this->addSql('ALTER TABLE artist DROP number_scrapped');
        $this->addSql('ALTER TABLE artist DROP total_items');
        $this->addSql('ALTER TABLE label DROP number_scrapped');
        $this->addSql('ALTER TABLE label DROP total_items');
    }
}
