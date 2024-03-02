<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240302212641 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE time_point_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE time_point (id INT NOT NULL, child_id INT DEFAULT NULL, value DOUBLE PRECISION NOT NULL, date DATE NOT NULL, notes VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, type VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_F2D71258DD62C21B ON time_point (child_id)');
        $this->addSql('ALTER TABLE time_point ADD CONSTRAINT FK_F2D71258DD62C21B FOREIGN KEY (child_id) REFERENCES child (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE time_point_id_seq CASCADE');
        $this->addSql('ALTER TABLE time_point DROP CONSTRAINT FK_F2D71258DD62C21B');
        $this->addSql('DROP TABLE time_point');
    }
}
