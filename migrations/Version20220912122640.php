<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220912122640 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE "artist_id_seq" INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE "label_id_seq" INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE "release_id_seq" INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE "track_id_seq" INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE user_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE "artist" (id INT NOT NULL, discogs_id INT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE "label" (id INT NOT NULL, discogs_id INT NOT NULL, name VARCHAR(255) NOT NULL, last_time_fully_scraped TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE "release" (id INT NOT NULL, discogs_id INT NOT NULL, videos TEXT DEFAULT NULL, release_date TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN "release".videos IS \'(DC2Type:array)\'');
        $this->addSql('CREATE TABLE release_label (release_id INT NOT NULL, label_id INT NOT NULL, PRIMARY KEY(release_id, label_id))');
        $this->addSql('CREATE INDEX IDX_8D39229FB12A727D ON release_label (release_id)');
        $this->addSql('CREATE INDEX IDX_8D39229F33B92F39 ON release_label (label_id)');
        $this->addSql('CREATE TABLE release_artist (release_id INT NOT NULL, artist_id INT NOT NULL, PRIMARY KEY(release_id, artist_id))');
        $this->addSql('CREATE INDEX IDX_CFBBEC6AB12A727D ON release_artist (release_id)');
        $this->addSql('CREATE INDEX IDX_CFBBEC6AB7970CF8 ON release_artist (artist_id)');
        $this->addSql('CREATE TABLE "track" (id INT NOT NULL, release_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_D6E3F8A6B12A727D ON "track" (release_id)');
        $this->addSql('CREATE TABLE track_artist (track_id INT NOT NULL, artist_id INT NOT NULL, PRIMARY KEY(track_id, artist_id))');
        $this->addSql('CREATE INDEX IDX_499B576E5ED23C43 ON track_artist (track_id)');
        $this->addSql('CREATE INDEX IDX_499B576EB7970CF8 ON track_artist (artist_id)');
        $this->addSql('CREATE TABLE "user" (id INT NOT NULL, first_name VARCHAR(255) NOT NULL, family_name VARCHAR(255) NOT NULL, email_address VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, birth_date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, sex VARCHAR(255) NOT NULL, is_mail_address_verified BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE release_label ADD CONSTRAINT FK_8D39229FB12A727D FOREIGN KEY (release_id) REFERENCES "release" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE release_label ADD CONSTRAINT FK_8D39229F33B92F39 FOREIGN KEY (label_id) REFERENCES "label" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE release_artist ADD CONSTRAINT FK_CFBBEC6AB12A727D FOREIGN KEY (release_id) REFERENCES "release" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE release_artist ADD CONSTRAINT FK_CFBBEC6AB7970CF8 FOREIGN KEY (artist_id) REFERENCES "artist" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE "track" ADD CONSTRAINT FK_D6E3F8A6B12A727D FOREIGN KEY (release_id) REFERENCES "release" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE track_artist ADD CONSTRAINT FK_499B576E5ED23C43 FOREIGN KEY (track_id) REFERENCES "track" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE track_artist ADD CONSTRAINT FK_499B576EB7970CF8 FOREIGN KEY (artist_id) REFERENCES "artist" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE "artist_id_seq" CASCADE');
        $this->addSql('DROP SEQUENCE "label_id_seq" CASCADE');
        $this->addSql('DROP SEQUENCE "release_id_seq" CASCADE');
        $this->addSql('DROP SEQUENCE "track_id_seq" CASCADE');
        $this->addSql('DROP SEQUENCE user_id_seq CASCADE');
        $this->addSql('ALTER TABLE release_label DROP CONSTRAINT FK_8D39229FB12A727D');
        $this->addSql('ALTER TABLE release_label DROP CONSTRAINT FK_8D39229F33B92F39');
        $this->addSql('ALTER TABLE release_artist DROP CONSTRAINT FK_CFBBEC6AB12A727D');
        $this->addSql('ALTER TABLE release_artist DROP CONSTRAINT FK_CFBBEC6AB7970CF8');
        $this->addSql('ALTER TABLE "track" DROP CONSTRAINT FK_D6E3F8A6B12A727D');
        $this->addSql('ALTER TABLE track_artist DROP CONSTRAINT FK_499B576E5ED23C43');
        $this->addSql('ALTER TABLE track_artist DROP CONSTRAINT FK_499B576EB7970CF8');
        $this->addSql('DROP TABLE "artist"');
        $this->addSql('DROP TABLE "label"');
        $this->addSql('DROP TABLE "release"');
        $this->addSql('DROP TABLE release_label');
        $this->addSql('DROP TABLE release_artist');
        $this->addSql('DROP TABLE "track"');
        $this->addSql('DROP TABLE track_artist');
        $this->addSql('DROP TABLE "user"');
    }
}
