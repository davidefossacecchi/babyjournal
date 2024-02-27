<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240227212643 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE child_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE child (id INT NOT NULL, family_id INT DEFAULT NULL, name VARCHAR(50) NOT NULL, birth_date DATE NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_22B35429C35E566A ON child (family_id)');
        $this->addSql('COMMENT ON COLUMN child.birth_date IS \'(DC2Type:date_immutable)\'');
        $this->addSql('ALTER TABLE child ADD CONSTRAINT FK_22B35429C35E566A FOREIGN KEY (family_id) REFERENCES child (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE child_id_seq CASCADE');
        $this->addSql('ALTER TABLE child DROP CONSTRAINT FK_22B35429C35E566A');
        $this->addSql('DROP TABLE child');
    }
}
