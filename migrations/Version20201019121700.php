<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201019121700 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE patient_appointment ADD planned_time TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL');
        $this->addSql('ALTER TABLE patient_appointment ALTER medical_record_id DROP NOT NULL');
        $this->addSql('ALTER TABLE patient_appointment ALTER staff_id DROP NOT NULL');
        $this->addSql('ALTER TABLE patient_appointment ALTER appointment_time DROP NOT NULL');
        $this->addSql('COMMENT ON COLUMN patient_appointment.planned_time IS \'Дата и время приема по плану\'');
        $this->addSql('COMMENT ON COLUMN patient_appointment.appointment_time IS \'Фактические дата и время приема\'');
        $this->addSql('ALTER TABLE auth_user ALTER phone TYPE VARCHAR(10)');
        $this->addSql('ALTER TABLE auth_user ALTER phone SET NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA IF NOT EXISTS public');
        $this->addSql('ALTER TABLE patient_appointment DROP planned_time');
        $this->addSql('ALTER TABLE patient_appointment ALTER medical_record_id SET NOT NULL');
        $this->addSql('ALTER TABLE patient_appointment ALTER staff_id SET NOT NULL');
        $this->addSql('ALTER TABLE patient_appointment ALTER appointment_time SET NOT NULL');
        $this->addSql('COMMENT ON COLUMN patient_appointment.appointment_time IS \'Дата и время приема\'');
        $this->addSql('ALTER TABLE auth_user ALTER phone TYPE CHAR(10)');
        $this->addSql('ALTER TABLE auth_user ALTER phone DROP NOT NULL');
    }
}
