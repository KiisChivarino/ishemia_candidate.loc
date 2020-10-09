<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201002085357 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE auth_user DROP description');
//        $this->addSql('ALTER TABLE medical_history DROP CONSTRAINT fk_61b89085d4d57cd');
        $this->addSql('DROP INDEX idx_61b89085d4d57cd');
        $this->addSql('ALTER TABLE medical_history DROP staff_id');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA IF NOT EXISTS public');
        $this->addSql('ALTER TABLE auth_user ADD description TEXT DEFAULT NULL');
        $this->addSql('COMMENT ON COLUMN auth_user.description IS \'Описание/комментарий\'');
        $this->addSql('ALTER TABLE medical_history ADD staff_id INT DEFAULT NULL');
        $this->addSql('COMMENT ON COLUMN medical_history.staff_id IS \'Ключ персонала\'');
        $this->addSql('ALTER TABLE medical_history ADD CONSTRAINT fk_61b89085d4d57cd FOREIGN KEY (staff_id) REFERENCES staff (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_61b89085d4d57cd ON medical_history (staff_id)');
    }
}
