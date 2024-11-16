<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241116092204 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE auth_token ADD family_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE auth_token ADD email VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE auth_token ADD CONSTRAINT FK_9315F04EC35E566A FOREIGN KEY (family_id) REFERENCES family (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_9315F04EC35E566A ON auth_token (family_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE auth_token DROP CONSTRAINT FK_9315F04EC35E566A');
        $this->addSql('DROP INDEX IDX_9315F04EC35E566A');
        $this->addSql('ALTER TABLE auth_token DROP family_id');
        $this->addSql('ALTER TABLE auth_token DROP email');
    }
}
