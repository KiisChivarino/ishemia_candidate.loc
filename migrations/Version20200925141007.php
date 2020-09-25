<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200925141007 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE complaint_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE complaint (id INT NOT NULL, name VARCHAR(255) NOT NULL, description TEXT DEFAULT NULL, enabled BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN complaint.name IS \'Название жалобы\'');
        $this->addSql('COMMENT ON COLUMN complaint.description IS \'Описание жалобы\'');
        $this->addSql('CREATE TABLE patient_appointment_complaint (patient_appointment_id INT NOT NULL, complaint_id INT NOT NULL, PRIMARY KEY(patient_appointment_id, complaint_id))');
        $this->addSql('CREATE INDEX IDX_7278AD235FA482B2 ON patient_appointment_complaint (patient_appointment_id)');
        $this->addSql('CREATE INDEX IDX_7278AD23EDAE188E ON patient_appointment_complaint (complaint_id)');
        $this->addSql('COMMENT ON COLUMN patient_appointment_complaint.patient_appointment_id IS \'Ключ приема пациента\'');
        $this->addSql('ALTER TABLE patient_appointment_complaint ADD CONSTRAINT FK_7278AD235FA482B2 FOREIGN KEY (patient_appointment_id) REFERENCES patient_appointment (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE patient_appointment_complaint ADD CONSTRAINT FK_7278AD23EDAE188E FOREIGN KEY (complaint_id) REFERENCES complaint (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE patient_appointment ADD complaints_comment TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE patient_appointment ADD objective_status TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE patient_appointment ADD therapy TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE patient_appointment RENAME COLUMN description TO recommendation');
        $this->addSql('COMMENT ON COLUMN patient_appointment.complaints_comment IS \'Комментарий врача по жалобам\'');
        $this->addSql('COMMENT ON COLUMN patient_appointment.objective_status IS \'Объективный статус\'');
        $this->addSql('COMMENT ON COLUMN patient_appointment.therapy IS \'Терапия\'');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA IF NOT EXISTS public');
        $this->addSql('ALTER TABLE patient_appointment_complaint DROP CONSTRAINT FK_7278AD23EDAE188E');
        $this->addSql('DROP SEQUENCE complaint_id_seq CASCADE');
        $this->addSql('DROP TABLE complaint');
        $this->addSql('DROP TABLE patient_appointment_complaint');
        $this->addSql('ALTER TABLE patient_appointment DROP complaints_comment');
        $this->addSql('ALTER TABLE patient_appointment DROP objective_status');
        $this->addSql('ALTER TABLE patient_appointment DROP therapy');
        $this->addSql('ALTER TABLE patient_appointment RENAME COLUMN recommendation TO description');
    }
}
