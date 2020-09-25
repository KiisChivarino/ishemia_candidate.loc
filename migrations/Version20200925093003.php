<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200925093003 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP TABLE patient_risk_factor');
        $this->addSql('ALTER TABLE patient ADD passport_issue_date DATE DEFAULT NULL');
        $this->addSql('ALTER TABLE patient ADD passport_issuing_authority VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE patient ADD passport_issuing_authority_code VARCHAR(7) NOT NULL');
        $this->addSql('ALTER TABLE patient DROP date_start_of_treatment');
        $this->addSql('ALTER TABLE patient DROP important_comment');
        $this->addSql('COMMENT ON COLUMN patient.passport_issue_date IS \'Дата выдачи паспорта\'');
        $this->addSql('COMMENT ON COLUMN patient.passport_issuing_authority IS \'Орган, выдавший паспорт\'');
        $this->addSql('COMMENT ON COLUMN patient.passport_issuing_authority_code IS \'Код органа, выдавшего паспорт\'');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA IF NOT EXISTS public');
        $this->addSql('CREATE TABLE patient_risk_factor (patient_id INT NOT NULL, risk_factor_id INT NOT NULL, PRIMARY KEY(patient_id, risk_factor_id))');
        $this->addSql('CREATE INDEX idx_1df0dd2b61639429 ON patient_risk_factor (risk_factor_id)');
        $this->addSql('CREATE INDEX idx_1df0dd2b6b899279 ON patient_risk_factor (patient_id)');
        $this->addSql('COMMENT ON COLUMN patient_risk_factor.patient_id IS \'Ключ пациента\'');
        $this->addSql('COMMENT ON COLUMN patient_risk_factor.risk_factor_id IS \'Ключ фактора риска\'');
        $this->addSql('ALTER TABLE patient_risk_factor ADD CONSTRAINT fk_1df0dd2b6b899279 FOREIGN KEY (patient_id) REFERENCES patient (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE patient_risk_factor ADD CONSTRAINT fk_1df0dd2b61639429 FOREIGN KEY (risk_factor_id) REFERENCES risk_factor (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE patient ADD date_start_of_treatment DATE NOT NULL');
        $this->addSql('ALTER TABLE patient ADD important_comment TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE patient DROP passport_issue_date');
        $this->addSql('ALTER TABLE patient DROP passport_issuing_authority');
        $this->addSql('ALTER TABLE patient DROP passport_issuing_authority_code');
        $this->addSql('COMMENT ON COLUMN patient.date_start_of_treatment IS \'Дата начала лечения\'');
        $this->addSql('COMMENT ON COLUMN patient.important_comment IS \'Важный комментарий для вывода\'');
    }
}
