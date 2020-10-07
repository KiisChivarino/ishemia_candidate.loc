<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201007124606 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE patient_file ADD patient_testing_id INT DEFAULT NULL');
        $this->addSql('COMMENT ON COLUMN patient_file.patient_testing_id IS \'Ключ сдачи анализов\'');
        $this->addSql('ALTER TABLE patient_file ADD CONSTRAINT FK_50E7BD8B0EC09FD FOREIGN KEY (patient_testing_id) REFERENCES patient_testing (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_50E7BD8B0EC09FD ON patient_file (patient_testing_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE patient_file DROP CONSTRAINT FK_50E7BD8B0EC09FD');
        $this->addSql('DROP INDEX IDX_50E7BD8B0EC09FD');
        $this->addSql('ALTER TABLE patient_file DROP patient_testing_id');
    }
}
