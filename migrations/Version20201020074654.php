<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201020074654 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE patient_discharge_epicrisis_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE patient_discharge_epicrisis (id INT NOT NULL, medical_history_id INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_5E69AD6E3544AD9E ON patient_discharge_epicrisis (medical_history_id)');
        $this->addSql('COMMENT ON COLUMN patient_discharge_epicrisis.id IS \'Ключ выписного эпикриза\'');
        $this->addSql('COMMENT ON COLUMN patient_discharge_epicrisis.medical_history_id IS \'Ключ истории болезни\'');
        $this->addSql('ALTER TABLE patient_discharge_epicrisis ADD CONSTRAINT FK_5E69AD6E3544AD9E FOREIGN KEY (medical_history_id) REFERENCES medical_history (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA IF NOT EXISTS public');
        $this->addSql('DROP SEQUENCE patient_discharge_epicrisis_id_seq CASCADE');
        $this->addSql('DROP TABLE patient_discharge_epicrisis');
    }
}
