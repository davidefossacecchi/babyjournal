<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240303083653 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP SEQUENCE post_id_seq CASCADE');
        $this->addSql('ALTER TABLE post DROP CONSTRAINT fk_5a8a6c8df675f31b');
        $this->addSql('ALTER TABLE post DROP CONSTRAINT fk_5a8a6c8dc35e566a');
        $this->addSql('DROP TABLE post');
        $this->addSql('ALTER TABLE time_point ADD author_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE time_point ADD family_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE time_point ADD image_path VARCHAR(1024) DEFAULT NULL');
        $this->addSql('ALTER TABLE time_point ADD hash VARCHAR(512) DEFAULT NULL');
        $this->addSql('ALTER TABLE time_point ADD caption VARCHAR(1024) DEFAULT NULL');
        $this->addSql('ALTER TABLE time_point ALTER value DROP NOT NULL');
        $this->addSql('ALTER TABLE time_point ALTER notes DROP NOT NULL');
        $this->addSql('ALTER TABLE time_point ADD CONSTRAINT FK_F2D71258F675F31B FOREIGN KEY (author_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE time_point ADD CONSTRAINT FK_F2D71258C35E566A FOREIGN KEY (family_id) REFERENCES family (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_F2D71258F675F31B ON time_point (author_id)');
        $this->addSql('CREATE INDEX IDX_F2D71258C35E566A ON time_point (family_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('CREATE SEQUENCE post_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE post (id INT NOT NULL, author_id INT DEFAULT NULL, family_id INT DEFAULT NULL, image_path VARCHAR(1024) NOT NULL, hash VARCHAR(512) NOT NULL, caption VARCHAR(1024) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_5a8a6c8dc35e566a ON post (family_id)');
        $this->addSql('CREATE INDEX idx_5a8a6c8df675f31b ON post (author_id)');
        $this->addSql('ALTER TABLE post ADD CONSTRAINT fk_5a8a6c8df675f31b FOREIGN KEY (author_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE post ADD CONSTRAINT fk_5a8a6c8dc35e566a FOREIGN KEY (family_id) REFERENCES family (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE time_point DROP CONSTRAINT FK_F2D71258F675F31B');
        $this->addSql('ALTER TABLE time_point DROP CONSTRAINT FK_F2D71258C35E566A');
        $this->addSql('DROP INDEX IDX_F2D71258F675F31B');
        $this->addSql('DROP INDEX IDX_F2D71258C35E566A');
        $this->addSql('ALTER TABLE time_point DROP author_id');
        $this->addSql('ALTER TABLE time_point DROP family_id');
        $this->addSql('ALTER TABLE time_point DROP image_path');
        $this->addSql('ALTER TABLE time_point DROP hash');
        $this->addSql('ALTER TABLE time_point DROP caption');
        $this->addSql('ALTER TABLE time_point ALTER value SET NOT NULL');
        $this->addSql('ALTER TABLE time_point ALTER notes SET NOT NULL');
    }
}
