<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220926135113 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE label_release (label_id INT NOT NULL, release_id INT NOT NULL, PRIMARY KEY(label_id, release_id))');
        $this->addSql('CREATE INDEX IDX_22C6AA3433B92F39 ON label_release (label_id)');
        $this->addSql('CREATE INDEX IDX_22C6AA34B12A727D ON label_release (release_id)');
        $this->addSql('CREATE TABLE release_artist (release_id INT NOT NULL, artist_id INT NOT NULL, PRIMARY KEY(release_id, artist_id))');
        $this->addSql('CREATE INDEX IDX_CFBBEC6AB12A727D ON release_artist (release_id)');
        $this->addSql('CREATE INDEX IDX_CFBBEC6AB7970CF8 ON release_artist (artist_id)');
        $this->addSql('CREATE TABLE test_all_types_user (test_all_types_id INT NOT NULL, user_id VARCHAR(255) NOT NULL, PRIMARY KEY(test_all_types_id, user_id))');
        $this->addSql('CREATE INDEX IDX_A02C08C533CA84A0 ON test_all_types_user (test_all_types_id)');
        $this->addSql('CREATE INDEX IDX_A02C08C5A76ED395 ON test_all_types_user (user_id)');
        $this->addSql('ALTER TABLE label_release ADD CONSTRAINT FK_22C6AA3433B92F39 FOREIGN KEY (label_id) REFERENCES label (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE label_release ADD CONSTRAINT FK_22C6AA34B12A727D FOREIGN KEY (release_id) REFERENCES release (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE release_artist ADD CONSTRAINT FK_CFBBEC6AB12A727D FOREIGN KEY (release_id) REFERENCES release (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE release_artist ADD CONSTRAINT FK_CFBBEC6AB7970CF8 FOREIGN KEY (artist_id) REFERENCES artist (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE test_all_types_user ADD CONSTRAINT FK_A02C08C533CA84A0 FOREIGN KEY (test_all_types_id) REFERENCES test_all_types (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE test_all_types_user ADD CONSTRAINT FK_A02C08C5A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE label_release DROP CONSTRAINT FK_22C6AA3433B92F39');
        $this->addSql('ALTER TABLE label_release DROP CONSTRAINT FK_22C6AA34B12A727D');
        $this->addSql('ALTER TABLE release_artist DROP CONSTRAINT FK_CFBBEC6AB12A727D');
        $this->addSql('ALTER TABLE release_artist DROP CONSTRAINT FK_CFBBEC6AB7970CF8');
        $this->addSql('ALTER TABLE test_all_types_user DROP CONSTRAINT FK_A02C08C533CA84A0');
        $this->addSql('ALTER TABLE test_all_types_user DROP CONSTRAINT FK_A02C08C5A76ED395');
        $this->addSql('DROP TABLE label_release');
        $this->addSql('DROP TABLE release_artist');
        $this->addSql('DROP TABLE test_all_types_user');
    }
}
