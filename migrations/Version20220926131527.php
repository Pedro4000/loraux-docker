<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220926131527 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE UNIQUE INDEX UNIQ_159968761FD3E0A ON artist (discogs_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_EA750E861FD3E0A ON label (discogs_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_9E47031D61FD3E0A ON release (discogs_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP INDEX UNIQ_159968761FD3E0A');
        $this->addSql('DROP INDEX UNIQ_9E47031D61FD3E0A');
        $this->addSql('DROP INDEX UNIQ_EA750E861FD3E0A');
    }
}
