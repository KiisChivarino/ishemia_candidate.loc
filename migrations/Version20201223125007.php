<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201223125007 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP SEQUENCE received_sms_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE notification_type_id_seq CASCADE');
        $this->addSql('CREATE SEQUENCE patient_medicine_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE patient_medicine (id INT NOT NULL, medical_history_id INT NOT NULL, medicine_name VARCHAR(255) NOT NULL, instruction TEXT NOT NULL, date_begin DATE NOT NULL, enabled BOOLEAN DEFAULT \'true\' NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_6FB372283544AD9E ON patient_medicine (medical_history_id)');
        $this->addSql('COMMENT ON TABLE patient_medicine IS \'Прием лекарства пациентом\'');
        $this->addSql('COMMENT ON COLUMN patient_medicine.id IS \'Ключ лекарства по назначению\'');
        $this->addSql('COMMENT ON COLUMN patient_medicine.medical_history_id IS \'Ключ истории болезни\'');
        $this->addSql('COMMENT ON COLUMN patient_medicine.medicine_name IS \'Название лекарства\'');
        $this->addSql('COMMENT ON COLUMN patient_medicine.instruction IS \'Инструкция по применению\'');
        $this->addSql('COMMENT ON COLUMN patient_medicine.date_begin IS \'Планируемая дата начала приема лекарства\'');
        $this->addSql('COMMENT ON COLUMN patient_medicine.enabled IS \'Ограничение использования\'');
        $this->addSql('ALTER TABLE patient_medicine ADD CONSTRAINT FK_6FB372283544AD9E FOREIGN KEY (medical_history_id) REFERENCES medical_history (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE patient_medicine_id_seq CASCADE');
        $this->addSql('CREATE SEQUENCE received_sms_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE notification_type_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE received_sms (id INT NOT NULL, patient_id INT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, text VARCHAR(255) DEFAULT NULL, external_id VARCHAR(255) NOT NULL, is_processed BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN received_sms.patient_id IS \'Ключ пациента\'');
        $this->addSql('CREATE TABLE notification_type (id INT NOT NULL, name VARCHAR(255) NOT NULL, template TEXT DEFAULT NULL, enabled BOOLEAN DEFAULT \'true\' NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON TABLE notification_type IS \'Тип уведомления\'');
        $this->addSql('COMMENT ON COLUMN notification_type.id IS \'Ключ типа уведомления\'');
        $this->addSql('COMMENT ON COLUMN notification_type.name IS \'Наименование типа уведомления\'');
        $this->addSql('COMMENT ON COLUMN notification_type.template IS \'Шаблон типа уведомления\'');
        $this->addSql('COMMENT ON COLUMN notification_type.enabled IS \'Ограничение использования\'');
        $this->addSql('DROP TABLE patient_medicine');
    }
}
