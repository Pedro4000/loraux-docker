<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220926084645 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE artist DROP CONSTRAINT fk_1599687bf396750');
        $this->addSql('ALTER TABLE label DROP CONSTRAINT fk_ea750e8bf396750');
        $this->addSql('ALTER TABLE release DROP CONSTRAINT fk_9e47031dbf396750');
        $this->addSql('DROP SEQUENCE discogs_class_id_seq CASCADE');
        $this->addSql('CREATE SEQUENCE artist_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE label_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE release_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('DROP TABLE discogs_class');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE artist_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE label_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE release_id_seq CASCADE');
        $this->addSql('CREATE SEQUENCE discogs_class_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE discogs_class (id INT NOT NULL, discogs_id INT NOT NULL, name VARCHAR(255) NOT NULL, fully_scrapped BOOLEAN NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, discr VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN discogs_class.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE artist ADD CONSTRAINT fk_1599687bf396750 FOREIGN KEY (id) REFERENCES discogs_class (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE label ADD CONSTRAINT fk_ea750e8bf396750 FOREIGN KEY (id) REFERENCES discogs_class (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE release ADD CONSTRAINT fk_9e47031dbf396750 FOREIGN KEY (id) REFERENCES discogs_class (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }
}
