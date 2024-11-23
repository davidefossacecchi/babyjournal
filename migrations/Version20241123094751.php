<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241123094751 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE auth_token ADD child_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE auth_token ADD CONSTRAINT FK_9315F04EDD62C21B FOREIGN KEY (child_id) REFERENCES child (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_9315F04EDD62C21B ON auth_token (child_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE auth_token DROP CONSTRAINT FK_9315F04EDD62C21B');
        $this->addSql('DROP INDEX IDX_9315F04EDD62C21B');
        $this->addSql('ALTER TABLE auth_token DROP child_id');
    }
}
