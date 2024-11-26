<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241123091210 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE child ADD representing_user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE child ADD CONSTRAINT FK_22B35429C3909BB4 FOREIGN KEY (representing_user_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_22B35429C3909BB4 ON child (representing_user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE child DROP CONSTRAINT FK_22B35429C3909BB4');
        $this->addSql('DROP INDEX IDX_22B35429C3909BB4');
        $this->addSql('ALTER TABLE child DROP representing_user_id');
    }
}
