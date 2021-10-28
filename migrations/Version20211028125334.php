<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211028125334 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX UNIQ_C53D045FA76ED395');
        $this->addSql('CREATE TEMPORARY TABLE __temp__image AS SELECT id, user_id, name FROM image');
        $this->addSql('DROP TABLE image');
        $this->addSql('CREATE TABLE image (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, user_id INTEGER NOT NULL, name VARCHAR(255) NOT NULL COLLATE BINARY, CONSTRAINT FK_C53D045FA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO image (id, user_id, name) SELECT id, user_id, name FROM __temp__image');
        $this->addSql('DROP TABLE __temp__image');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C53D045FA76ED395 ON image (user_id)');
        $this->addSql('DROP INDEX IDX_7CE748AA76ED395');
        $this->addSql('CREATE TEMPORARY TABLE __temp__reset_password_request AS SELECT id, user_id, selector, hashed_token, requested_at, expires_at FROM reset_password_request');
        $this->addSql('DROP TABLE reset_password_request');
        $this->addSql('CREATE TABLE reset_password_request (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, user_id INTEGER NOT NULL, selector VARCHAR(20) NOT NULL COLLATE BINARY, hashed_token VARCHAR(100) NOT NULL COLLATE BINARY, requested_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , expires_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , CONSTRAINT FK_7CE748AA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO reset_password_request (id, user_id, selector, hashed_token, requested_at, expires_at) SELECT id, user_id, selector, hashed_token, requested_at, expires_at FROM __temp__reset_password_request');
        $this->addSql('DROP TABLE __temp__reset_password_request');
        $this->addSql('CREATE INDEX IDX_7CE748AA76ED395 ON reset_password_request (user_id)');
        $this->addSql('DROP INDEX IDX_694309E4A73F0036');
        $this->addSql('CREATE TEMPORARY TABLE __temp__site AS SELECT id, ville_id, nom, rue FROM site');
        $this->addSql('DROP TABLE site');
        $this->addSql('CREATE TABLE site (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, ville_id INTEGER NOT NULL, nom VARCHAR(255) NOT NULL COLLATE BINARY, rue VARCHAR(255) NOT NULL COLLATE BINARY, CONSTRAINT FK_694309E4A73F0036 FOREIGN KEY (ville_id) REFERENCES ville (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO site (id, ville_id, nom, rue) SELECT id, ville_id, nom, rue FROM __temp__site');
        $this->addSql('DROP TABLE __temp__site');
        $this->addSql('CREATE INDEX IDX_694309E4A73F0036 ON site (ville_id)');
        $this->addSql('DROP INDEX IDX_3C3FD3F2D936B2FA');
        $this->addSql('DROP INDEX IDX_3C3FD3F2F6BD1646');
        $this->addSql('DROP INDEX IDX_3C3FD3F2A2C2D9C8');
        $this->addSql('DROP INDEX IDX_3C3FD3F2B8C8DEB3');
        $this->addSql('CREATE TEMPORARY TABLE __temp__sortie AS SELECT id, ville_organisatrice_id, ville_accueil_id, site_id, organisateur_id, nom, date_sortie, date_limite_inscription, nb_places, duree, description, publiee, motif_annulation FROM sortie');
        $this->addSql('DROP TABLE sortie');
        $this->addSql('CREATE TABLE sortie (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, ville_organisatrice_id INTEGER NOT NULL, ville_accueil_id INTEGER NOT NULL, site_id INTEGER DEFAULT NULL, organisateur_id INTEGER NOT NULL, nom VARCHAR(255) NOT NULL COLLATE BINARY, date_sortie DATETIME NOT NULL, date_limite_inscription DATE NOT NULL, nb_places INTEGER NOT NULL, duree INTEGER NOT NULL, description VARCHAR(255) NOT NULL COLLATE BINARY, publiee BOOLEAN NOT NULL, motif_annulation VARCHAR(255) DEFAULT NULL COLLATE BINARY, CONSTRAINT FK_3C3FD3F2B8C8DEB3 FOREIGN KEY (ville_organisatrice_id) REFERENCES ville (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_3C3FD3F2A2C2D9C8 FOREIGN KEY (ville_accueil_id) REFERENCES ville (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_3C3FD3F2F6BD1646 FOREIGN KEY (site_id) REFERENCES site (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_3C3FD3F2D936B2FA FOREIGN KEY (organisateur_id) REFERENCES user (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO sortie (id, ville_organisatrice_id, ville_accueil_id, site_id, organisateur_id, nom, date_sortie, date_limite_inscription, nb_places, duree, description, publiee, motif_annulation) SELECT id, ville_organisatrice_id, ville_accueil_id, site_id, organisateur_id, nom, date_sortie, date_limite_inscription, nb_places, duree, description, publiee, motif_annulation FROM __temp__sortie');
        $this->addSql('DROP TABLE __temp__sortie');
        $this->addSql('CREATE INDEX IDX_3C3FD3F2D936B2FA ON sortie (organisateur_id)');
        $this->addSql('CREATE INDEX IDX_3C3FD3F2F6BD1646 ON sortie (site_id)');
        $this->addSql('CREATE INDEX IDX_3C3FD3F2A2C2D9C8 ON sortie (ville_accueil_id)');
        $this->addSql('CREATE INDEX IDX_3C3FD3F2B8C8DEB3 ON sortie (ville_organisatrice_id)');
        $this->addSql('DROP INDEX IDX_8A67684AA76ED395');
        $this->addSql('DROP INDEX IDX_8A67684ACC72D953');
        $this->addSql('CREATE TEMPORARY TABLE __temp__sortie_user AS SELECT sortie_id, user_id FROM sortie_user');
        $this->addSql('DROP TABLE sortie_user');
        $this->addSql('CREATE TABLE sortie_user (sortie_id INTEGER NOT NULL, user_id INTEGER NOT NULL, PRIMARY KEY(sortie_id, user_id), CONSTRAINT FK_8A67684ACC72D953 FOREIGN KEY (sortie_id) REFERENCES sortie (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_8A67684AA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO sortie_user (sortie_id, user_id) SELECT sortie_id, user_id FROM __temp__sortie_user');
        $this->addSql('DROP TABLE __temp__sortie_user');
        $this->addSql('CREATE INDEX IDX_8A67684AA76ED395 ON sortie_user (user_id)');
        $this->addSql('CREATE INDEX IDX_8A67684ACC72D953 ON sortie_user (sortie_id)');
        $this->addSql('DROP INDEX IDX_8D93D649A73F0036');
        $this->addSql('DROP INDEX UNIQ_8D93D649E7927C74');
        $this->addSql('CREATE TEMPORARY TABLE __temp__user AS SELECT id, ville_id, email, roles, password, pseudo, prenom, nom, tel, desactiver FROM user');
        $this->addSql('DROP TABLE user');
        $this->addSql('CREATE TABLE user (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, ville_id INTEGER NOT NULL, email VARCHAR(180) NOT NULL COLLATE BINARY, roles CLOB NOT NULL COLLATE BINARY --(DC2Type:json)
        , password VARCHAR(255) NOT NULL COLLATE BINARY, pseudo VARCHAR(255) NOT NULL COLLATE BINARY, prenom VARCHAR(255) NOT NULL COLLATE BINARY, nom VARCHAR(255) NOT NULL COLLATE BINARY, tel VARCHAR(255) NOT NULL COLLATE BINARY, desactiver BOOLEAN NOT NULL, CONSTRAINT FK_8D93D649A73F0036 FOREIGN KEY (ville_id) REFERENCES ville (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO user (id, ville_id, email, roles, password, pseudo, prenom, nom, tel, desactiver) SELECT id, ville_id, email, roles, password, pseudo, prenom, nom, tel, desactiver FROM __temp__user');
        $this->addSql('DROP TABLE __temp__user');
        $this->addSql('CREATE INDEX IDX_8D93D649A73F0036 ON user (ville_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON user (email)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX UNIQ_C53D045FA76ED395');
        $this->addSql('CREATE TEMPORARY TABLE __temp__image AS SELECT id, user_id, name FROM image');
        $this->addSql('DROP TABLE image');
        $this->addSql('CREATE TABLE image (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, user_id INTEGER NOT NULL, name VARCHAR(255) NOT NULL)');
        $this->addSql('INSERT INTO image (id, user_id, name) SELECT id, user_id, name FROM __temp__image');
        $this->addSql('DROP TABLE __temp__image');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C53D045FA76ED395 ON image (user_id)');
        $this->addSql('DROP INDEX IDX_7CE748AA76ED395');
        $this->addSql('CREATE TEMPORARY TABLE __temp__reset_password_request AS SELECT id, user_id, selector, hashed_token, requested_at, expires_at FROM reset_password_request');
        $this->addSql('DROP TABLE reset_password_request');
        $this->addSql('CREATE TABLE reset_password_request (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, user_id INTEGER NOT NULL, selector VARCHAR(20) NOT NULL, hashed_token VARCHAR(100) NOT NULL, requested_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , expires_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        )');
        $this->addSql('INSERT INTO reset_password_request (id, user_id, selector, hashed_token, requested_at, expires_at) SELECT id, user_id, selector, hashed_token, requested_at, expires_at FROM __temp__reset_password_request');
        $this->addSql('DROP TABLE __temp__reset_password_request');
        $this->addSql('CREATE INDEX IDX_7CE748AA76ED395 ON reset_password_request (user_id)');
        $this->addSql('DROP INDEX IDX_694309E4A73F0036');
        $this->addSql('CREATE TEMPORARY TABLE __temp__site AS SELECT id, ville_id, nom, rue FROM site');
        $this->addSql('DROP TABLE site');
        $this->addSql('CREATE TABLE site (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, ville_id INTEGER NOT NULL, nom VARCHAR(255) NOT NULL, rue VARCHAR(255) NOT NULL)');
        $this->addSql('INSERT INTO site (id, ville_id, nom, rue) SELECT id, ville_id, nom, rue FROM __temp__site');
        $this->addSql('DROP TABLE __temp__site');
        $this->addSql('CREATE INDEX IDX_694309E4A73F0036 ON site (ville_id)');
        $this->addSql('DROP INDEX IDX_3C3FD3F2B8C8DEB3');
        $this->addSql('DROP INDEX IDX_3C3FD3F2A2C2D9C8');
        $this->addSql('DROP INDEX IDX_3C3FD3F2F6BD1646');
        $this->addSql('DROP INDEX IDX_3C3FD3F2D936B2FA');
        $this->addSql('CREATE TEMPORARY TABLE __temp__sortie AS SELECT id, ville_organisatrice_id, ville_accueil_id, site_id, organisateur_id, nom, date_sortie, date_limite_inscription, nb_places, duree, description, publiee, motif_annulation FROM sortie');
        $this->addSql('DROP TABLE sortie');
        $this->addSql('CREATE TABLE sortie (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, ville_organisatrice_id INTEGER NOT NULL, ville_accueil_id INTEGER NOT NULL, site_id INTEGER DEFAULT NULL, organisateur_id INTEGER NOT NULL, nom VARCHAR(255) NOT NULL, date_sortie DATETIME NOT NULL, date_limite_inscription DATE NOT NULL, nb_places INTEGER NOT NULL, duree INTEGER NOT NULL, description VARCHAR(255) NOT NULL, publiee BOOLEAN NOT NULL, motif_annulation VARCHAR(255) DEFAULT NULL)');
        $this->addSql('INSERT INTO sortie (id, ville_organisatrice_id, ville_accueil_id, site_id, organisateur_id, nom, date_sortie, date_limite_inscription, nb_places, duree, description, publiee, motif_annulation) SELECT id, ville_organisatrice_id, ville_accueil_id, site_id, organisateur_id, nom, date_sortie, date_limite_inscription, nb_places, duree, description, publiee, motif_annulation FROM __temp__sortie');
        $this->addSql('DROP TABLE __temp__sortie');
        $this->addSql('CREATE INDEX IDX_3C3FD3F2B8C8DEB3 ON sortie (ville_organisatrice_id)');
        $this->addSql('CREATE INDEX IDX_3C3FD3F2A2C2D9C8 ON sortie (ville_accueil_id)');
        $this->addSql('CREATE INDEX IDX_3C3FD3F2F6BD1646 ON sortie (site_id)');
        $this->addSql('CREATE INDEX IDX_3C3FD3F2D936B2FA ON sortie (organisateur_id)');
        $this->addSql('DROP INDEX IDX_8A67684ACC72D953');
        $this->addSql('DROP INDEX IDX_8A67684AA76ED395');
        $this->addSql('CREATE TEMPORARY TABLE __temp__sortie_user AS SELECT sortie_id, user_id FROM sortie_user');
        $this->addSql('DROP TABLE sortie_user');
        $this->addSql('CREATE TABLE sortie_user (sortie_id INTEGER NOT NULL, user_id INTEGER NOT NULL, PRIMARY KEY(sortie_id, user_id))');
        $this->addSql('INSERT INTO sortie_user (sortie_id, user_id) SELECT sortie_id, user_id FROM __temp__sortie_user');
        $this->addSql('DROP TABLE __temp__sortie_user');
        $this->addSql('CREATE INDEX IDX_8A67684ACC72D953 ON sortie_user (sortie_id)');
        $this->addSql('CREATE INDEX IDX_8A67684AA76ED395 ON sortie_user (user_id)');
        $this->addSql('DROP INDEX UNIQ_8D93D649E7927C74');
        $this->addSql('DROP INDEX IDX_8D93D649A73F0036');
        $this->addSql('CREATE TEMPORARY TABLE __temp__user AS SELECT id, ville_id, email, roles, password, pseudo, prenom, nom, tel, desactiver FROM user');
        $this->addSql('DROP TABLE user');
        $this->addSql('CREATE TABLE user (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, ville_id INTEGER DEFAULT NULL, email VARCHAR(180) NOT NULL, roles CLOB NOT NULL --(DC2Type:json)
        , password VARCHAR(255) NOT NULL, pseudo VARCHAR(255) NOT NULL, prenom VARCHAR(255) NOT NULL, nom VARCHAR(255) NOT NULL, tel VARCHAR(255) NOT NULL, desactiver BOOLEAN NOT NULL)');
        $this->addSql('INSERT INTO user (id, ville_id, email, roles, password, pseudo, prenom, nom, tel, desactiver) SELECT id, ville_id, email, roles, password, pseudo, prenom, nom, tel, desactiver FROM __temp__user');
        $this->addSql('DROP TABLE __temp__user');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON user (email)');
        $this->addSql('CREATE INDEX IDX_8D93D649A73F0036 ON user (ville_id)');
    }
}
