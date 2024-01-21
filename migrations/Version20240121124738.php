<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240121124738 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE family_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE family (id INT NOT NULL, name VARCHAR(50) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE users_families (family_id INT NOT NULL, user_id INT NOT NULL, PRIMARY KEY(family_id, user_id))');
        $this->addSql('CREATE INDEX IDX_E21E1416C35E566A ON users_families (family_id)');
        $this->addSql('CREATE INDEX IDX_E21E1416A76ED395 ON users_families (user_id)');
        $this->addSql('ALTER TABLE users_families ADD CONSTRAINT FK_E21E1416C35E566A FOREIGN KEY (family_id) REFERENCES family (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE users_families ADD CONSTRAINT FK_E21E1416A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE family_id_seq CASCADE');
        $this->addSql('ALTER TABLE users_families DROP CONSTRAINT FK_E21E1416C35E566A');
        $this->addSql('ALTER TABLE users_families DROP CONSTRAINT FK_E21E1416A76ED395');
        $this->addSql('DROP TABLE family');
        $this->addSql('DROP TABLE users_families');
    }
}
