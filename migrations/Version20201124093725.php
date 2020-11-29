<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201124093725 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE prescription_appointment_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE prescription_appointment (id INT NOT NULL, prescription_id INT NOT NULL, patient_appointment_id INT NOT NULL, staff_id INT NOT NULL, inclusion_time TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, enabled BOOLEAN DEFAULT \'true\' NOT NULL, confirmed_by_staff BOOLEAN DEFAULT \'false\' NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_885C871593DB413D ON prescription_appointment (prescription_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_885C87155FA482B2 ON prescription_appointment (patient_appointment_id)');
        $this->addSql('CREATE INDEX IDX_885C8715D4D57CD ON prescription_appointment (staff_id)');
        $this->addSql('COMMENT ON TABLE prescription_appointment IS \'Назначение на прием\'');
        $this->addSql('COMMENT ON COLUMN prescription_appointment.id IS \'Ключ назначения на прием\'');
        $this->addSql('COMMENT ON COLUMN prescription_appointment.prescription_id IS \'Ключ назначения\'');
        $this->addSql('COMMENT ON COLUMN prescription_appointment.patient_appointment_id IS \'Ключ приема пациента\'');
        $this->addSql('COMMENT ON COLUMN prescription_appointment.staff_id IS \'Ключ персонала\'');
        $this->addSql('COMMENT ON COLUMN prescription_appointment.inclusion_time IS \'Дата и время включения в назначение\'');
        $this->addSql('COMMENT ON COLUMN prescription_appointment.enabled IS \'Ограничение использования\'');
        $this->addSql('COMMENT ON COLUMN prescription_appointment.confirmed_by_staff IS \'Флаг подтверждения врачом назначения на прием\'');
        $this->addSql('ALTER TABLE prescription_appointment ADD CONSTRAINT FK_885C871593DB413D FOREIGN KEY (prescription_id) REFERENCES prescription (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE prescription_appointment ADD CONSTRAINT FK_885C87155FA482B2 FOREIGN KEY (patient_appointment_id) REFERENCES patient_appointment (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE prescription_appointment ADD CONSTRAINT FK_885C8715D4D57CD FOREIGN KEY (staff_id) REFERENCES staff (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE prescription_testing ADD confirmed_by_staff BOOLEAN DEFAULT \'false\' NOT NULL');
        $this->addSql('COMMENT ON COLUMN prescription_testing.confirmed_by_staff IS \'Флаг подтверждения врачом назначения на прием\'');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA IF NOT EXISTS public');
        $this->addSql('DROP SEQUENCE prescription_appointment_id_seq CASCADE');
        $this->addSql('DROP TABLE prescription_appointment');
        $this->addSql('ALTER TABLE prescription_testing DROP confirmed_by_staff');
    }
}
