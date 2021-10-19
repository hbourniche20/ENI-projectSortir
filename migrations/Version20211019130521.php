<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211019130521 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE admin_controller');
        $this->addSql('DROP INDEX IDX_694309E4A73F0036');
        $this->addSql('CREATE TEMPORARY TABLE __temp__site AS SELECT id, ville_id, nom, rue FROM site');
        $this->addSql('DROP TABLE site');
        $this->addSql('CREATE TABLE site (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, ville_id INTEGER NOT NULL, nom VARCHAR(255) NOT NULL COLLATE BINARY, rue VARCHAR(255) NOT NULL COLLATE BINARY, CONSTRAINT FK_694309E4A73F0036 FOREIGN KEY (ville_id) REFERENCES ville (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO site (id, ville_id, nom, rue) SELECT id, ville_id, nom, rue FROM __temp__site');
        $this->addSql('DROP TABLE __temp__site');
        $this->addSql('CREATE INDEX IDX_694309E4A73F0036 ON site (ville_id)');
        $this->addSql('DROP INDEX IDX_3C3FD3F2F6BD1646');
        $this->addSql('DROP INDEX IDX_3C3FD3F2A2C2D9C8');
        $this->addSql('DROP INDEX IDX_3C3FD3F2B8C8DEB3');
        $this->addSql('CREATE TEMPORARY TABLE __temp__sortie AS SELECT id, ville_organisatrice_id, ville_accueil_id, site_id, nom, date_sortie, date_limite_inscription, nb_places, duree, description FROM sortie');
        $this->addSql('DROP TABLE sortie');
        $this->addSql('CREATE TABLE sortie (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, ville_organisatrice_id INTEGER NOT NULL, ville_accueil_id INTEGER NOT NULL, site_id INTEGER DEFAULT NULL, organisateur_id INTEGER NOT NULL, nom VARCHAR(255) NOT NULL COLLATE BINARY, date_sortie DATETIME NOT NULL, date_limite_inscription DATE NOT NULL, nb_places INTEGER NOT NULL, duree INTEGER NOT NULL, description VARCHAR(255) NOT NULL COLLATE BINARY, CONSTRAINT FK_3C3FD3F2B8C8DEB3 FOREIGN KEY (ville_organisatrice_id) REFERENCES ville (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_3C3FD3F2A2C2D9C8 FOREIGN KEY (ville_accueil_id) REFERENCES ville (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_3C3FD3F2F6BD1646 FOREIGN KEY (site_id) REFERENCES site (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_3C3FD3F2D936B2FA FOREIGN KEY (organisateur_id) REFERENCES user (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO sortie (id, ville_organisatrice_id, ville_accueil_id, site_id, nom, date_sortie, date_limite_inscription, nb_places, duree, description) SELECT id, ville_organisatrice_id, ville_accueil_id, site_id, nom, date_sortie, date_limite_inscription, nb_places, duree, description FROM __temp__sortie');
        $this->addSql('DROP TABLE __temp__sortie');
        $this->addSql('CREATE INDEX IDX_3C3FD3F2F6BD1646 ON sortie (site_id)');
        $this->addSql('CREATE INDEX IDX_3C3FD3F2A2C2D9C8 ON sortie (ville_accueil_id)');
        $this->addSql('CREATE INDEX IDX_3C3FD3F2B8C8DEB3 ON sortie (ville_organisatrice_id)');
        $this->addSql('CREATE INDEX IDX_3C3FD3F2D936B2FA ON sortie (organisateur_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE admin_controller (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL)');
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
        $this->addSql('CREATE TEMPORARY TABLE __temp__sortie AS SELECT id, ville_organisatrice_id, ville_accueil_id, site_id, nom, date_sortie, date_limite_inscription, nb_places, duree, description FROM sortie');
        $this->addSql('DROP TABLE sortie');
        $this->addSql('CREATE TABLE sortie (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, ville_organisatrice_id INTEGER NOT NULL, ville_accueil_id INTEGER NOT NULL, site_id INTEGER DEFAULT NULL, nom VARCHAR(255) NOT NULL, date_sortie DATETIME NOT NULL, date_limite_inscription DATE NOT NULL, nb_places INTEGER NOT NULL, duree INTEGER NOT NULL, description VARCHAR(255) NOT NULL)');
        $this->addSql('INSERT INTO sortie (id, ville_organisatrice_id, ville_accueil_id, site_id, nom, date_sortie, date_limite_inscription, nb_places, duree, description) SELECT id, ville_organisatrice_id, ville_accueil_id, site_id, nom, date_sortie, date_limite_inscription, nb_places, duree, description FROM __temp__sortie');
        $this->addSql('DROP TABLE __temp__sortie');
        $this->addSql('CREATE INDEX IDX_3C3FD3F2B8C8DEB3 ON sortie (ville_organisatrice_id)');
        $this->addSql('CREATE INDEX IDX_3C3FD3F2A2C2D9C8 ON sortie (ville_accueil_id)');
        $this->addSql('CREATE INDEX IDX_3C3FD3F2F6BD1646 ON sortie (site_id)');
    }
}
