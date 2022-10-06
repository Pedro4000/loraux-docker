<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221006083913 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE test_all_types_track (test_all_types_id INT NOT NULL, track_id VARCHAR(255) NOT NULL, PRIMARY KEY(test_all_types_id, track_id))');
        $this->addSql('CREATE INDEX IDX_32C0887333CA84A0 ON test_all_types_track (test_all_types_id)');
        $this->addSql('CREATE INDEX IDX_32C088735ED23C43 ON test_all_types_track (track_id)');
        $this->addSql('ALTER TABLE test_all_types_track ADD CONSTRAINT FK_32C0887333CA84A0 FOREIGN KEY (test_all_types_id) REFERENCES test_all_types (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE test_all_types_track ADD CONSTRAINT FK_32C088735ED23C43 FOREIGN KEY (track_id) REFERENCES track (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE discogs_video ADD name VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE test_all_types ADD exit VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE test_all_types_track DROP CONSTRAINT FK_32C0887333CA84A0');
        $this->addSql('ALTER TABLE test_all_types_track DROP CONSTRAINT FK_32C088735ED23C43');
        $this->addSql('DROP TABLE test_all_types_track');
        $this->addSql('ALTER TABLE test_all_types DROP exit');
        $this->addSql('ALTER TABLE discogs_video DROP name');
    }
}
