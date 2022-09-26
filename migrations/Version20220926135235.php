<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220926135235 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE release_label (release_id INT NOT NULL, label_id INT NOT NULL, PRIMARY KEY(release_id, label_id))');
        $this->addSql('CREATE INDEX IDX_8D39229FB12A727D ON release_label (release_id)');
        $this->addSql('CREATE INDEX IDX_8D39229F33B92F39 ON release_label (label_id)');
        $this->addSql('ALTER TABLE release_label ADD CONSTRAINT FK_8D39229FB12A727D FOREIGN KEY (release_id) REFERENCES release (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE release_label ADD CONSTRAINT FK_8D39229F33B92F39 FOREIGN KEY (label_id) REFERENCES label (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE label_release DROP CONSTRAINT fk_22c6aa3433b92f39');
        $this->addSql('ALTER TABLE label_release DROP CONSTRAINT fk_22c6aa34b12a727d');
        $this->addSql('DROP TABLE label_release');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('CREATE TABLE label_release (label_id INT NOT NULL, release_id INT NOT NULL, PRIMARY KEY(label_id, release_id))');
        $this->addSql('CREATE INDEX idx_22c6aa34b12a727d ON label_release (release_id)');
        $this->addSql('CREATE INDEX idx_22c6aa3433b92f39 ON label_release (label_id)');
        $this->addSql('ALTER TABLE label_release ADD CONSTRAINT fk_22c6aa3433b92f39 FOREIGN KEY (label_id) REFERENCES label (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE label_release ADD CONSTRAINT fk_22c6aa34b12a727d FOREIGN KEY (release_id) REFERENCES release (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE release_label DROP CONSTRAINT FK_8D39229FB12A727D');
        $this->addSql('ALTER TABLE release_label DROP CONSTRAINT FK_8D39229F33B92F39');
        $this->addSql('DROP TABLE release_label');
    }
}
