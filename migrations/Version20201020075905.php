<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201020075905 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE patient_file DROP CONSTRAINT fk_50e7bd8b0ec09fd');
        $this->addSql('DROP INDEX idx_50e7bd8b0ec09fd');
        $this->addSql('ALTER TABLE patient_file DROP patient_testing_id');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA IF NOT EXISTS public');
        $this->addSql('ALTER TABLE patient_file ADD patient_testing_id INT DEFAULT NULL');
        $this->addSql('COMMENT ON COLUMN patient_file.patient_testing_id IS \'Ключ сдачи анализов\'');
        $this->addSql('ALTER TABLE patient_file ADD CONSTRAINT fk_50e7bd8b0ec09fd FOREIGN KEY (patient_testing_id) REFERENCES patient_testing (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_50e7bd8b0ec09fd ON patient_file (patient_testing_id)');
    }
}
