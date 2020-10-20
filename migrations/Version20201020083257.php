<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201020083257 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE patient_testing_file_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE patient_testing_file (id INT NOT NULL, patient_testing_id INT NOT NULL, patient_file_id INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_3D3CB73EB0EC09FD ON patient_testing_file (patient_testing_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_3D3CB73ECEA82C87 ON patient_testing_file (patient_file_id)');
        $this->addSql('COMMENT ON COLUMN patient_testing_file.id IS \'Ключ файла обследования\'');
        $this->addSql('COMMENT ON COLUMN patient_testing_file.patient_testing_id IS \'Ключ сдачи анализов\'');
        $this->addSql('COMMENT ON COLUMN patient_testing_file.patient_file_id IS \'Ключ файла пациента\'');
        $this->addSql('ALTER TABLE patient_testing_file ADD CONSTRAINT FK_3D3CB73EB0EC09FD FOREIGN KEY (patient_testing_id) REFERENCES patient_testing (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE patient_testing_file ADD CONSTRAINT FK_3D3CB73ECEA82C87 FOREIGN KEY (patient_file_id) REFERENCES patient_file (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('COMMENT ON COLUMN patient_file.id IS \'Ключ файла пациента\'');
        $this->addSql('COMMENT ON COLUMN diagnosis.id IS \'Ключ диагноза\'');
        $this->addSql('COMMENT ON COLUMN discharge_epicrisis_file.id IS \'Ключ файла выписного эпикриза\'');
        $this->addSql('COMMENT ON COLUMN discharge_epicrisis_file.patient_file_id IS \'Ключ файла пациента\'');
        $this->addSql('COMMENT ON COLUMN medical_history.main_disease_id IS \'Ключ диагноза\'');
        $this->addSql('COMMENT ON COLUMN background_diseases.diagnosis_id IS \'Ключ диагноза\'');
        $this->addSql('COMMENT ON COLUMN complications.diagnosis_id IS \'Ключ диагноза\'');
        $this->addSql('COMMENT ON COLUMN concomitant_diseases.diagnosis_id IS \'Ключ диагноза\'');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE patient_testing_file_id_seq CASCADE');
        $this->addSql('DROP TABLE patient_testing_file');
        $this->addSql('COMMENT ON COLUMN discharge_epicrisis_file.id IS NULL');
        $this->addSql('COMMENT ON COLUMN discharge_epicrisis_file.patient_file_id IS NULL');
        $this->addSql('COMMENT ON COLUMN medical_history.main_disease_id IS \'Ключ записи\'');
        $this->addSql('COMMENT ON COLUMN patient_file.id IS NULL');
        $this->addSql('COMMENT ON COLUMN diagnosis.id IS \'Ключ записи\'');
        $this->addSql('COMMENT ON COLUMN background_diseases.diagnosis_id IS \'Ключ записи\'');
        $this->addSql('COMMENT ON COLUMN complications.diagnosis_id IS \'Ключ записи\'');
        $this->addSql('COMMENT ON COLUMN concomitant_diseases.diagnosis_id IS \'Ключ записи\'');
    }
}
