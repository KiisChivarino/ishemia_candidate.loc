<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210216140020 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP SEQUENCE patient_medicine_id_seq CASCADE');
        $this->addSql('DROP TABLE patient_medicine');
//        $this->addSql('DROP INDEX "primary"');
        $this->addSql('ALTER TABLE background_diseases ADD clinical_diagnosis_id INT NOT NULL');
        $this->addSql('ALTER TABLE background_diseases DROP medical_history_id');
        $this->addSql('COMMENT ON COLUMN background_diseases.clinical_diagnosis_id IS \'Ключ клинического диагноза\'');
        $this->addSql('ALTER TABLE background_diseases ADD CONSTRAINT FK_2C1D0B80D88E3C0D FOREIGN KEY (clinical_diagnosis_id) REFERENCES clinical_diagnosis (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_2C1D0B80D88E3C0D ON background_diseases (clinical_diagnosis_id)');
        $this->addSql('ALTER TABLE background_diseases ADD PRIMARY KEY (clinical_diagnosis_id, diagnosis_id)');
        $this->addSql('ALTER TABLE complications DROP CONSTRAINT fk_bc020a5f3544ad9e');
        $this->addSql('DROP INDEX idx_bc020a5f3544ad9e');
//        $this->addSql('DROP INDEX "primary"');
        $this->addSql('ALTER TABLE complications ADD clinical_diagnosis_id INT NOT NULL');
        $this->addSql('ALTER TABLE complications DROP medical_history_id');
        $this->addSql('COMMENT ON COLUMN complications.clinical_diagnosis_id IS \'Ключ клинического диагноза\'');
        $this->addSql('ALTER TABLE complications ADD CONSTRAINT FK_BC020A5FD88E3C0D FOREIGN KEY (clinical_diagnosis_id) REFERENCES clinical_diagnosis (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_BC020A5FD88E3C0D ON complications (clinical_diagnosis_id)');
        $this->addSql('ALTER TABLE complications ADD PRIMARY KEY (clinical_diagnosis_id, diagnosis_id)');
        $this->addSql('ALTER TABLE concomitant_diseases DROP CONSTRAINT fk_59f3a8e03544ad9e');
        $this->addSql('DROP INDEX idx_59f3a8e03544ad9e');
//        $this->addSql('DROP INDEX "primary"');
        $this->addSql('ALTER TABLE concomitant_diseases ADD clinical_diagnosis_id INT NOT NULL');
        $this->addSql('ALTER TABLE concomitant_diseases DROP medical_history_id');
        $this->addSql('COMMENT ON COLUMN concomitant_diseases.clinical_diagnosis_id IS \'Ключ клинического диагноза\'');
        $this->addSql('ALTER TABLE concomitant_diseases ADD CONSTRAINT FK_59F3A8E0D88E3C0D FOREIGN KEY (clinical_diagnosis_id) REFERENCES clinical_diagnosis (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_59F3A8E0D88E3C0D ON concomitant_diseases (clinical_diagnosis_id)');
        $this->addSql('ALTER TABLE concomitant_diseases ADD PRIMARY KEY (clinical_diagnosis_id, diagnosis_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('CREATE SEQUENCE patient_medicine_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE patient_medicine (id INT NOT NULL, medical_history_id INT NOT NULL, medicine_name VARCHAR(255) NOT NULL, instruction TEXT NOT NULL, date_begin DATE NOT NULL, enabled BOOLEAN DEFAULT \'true\' NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_6fb372283544ad9e ON patient_medicine (medical_history_id)');
        $this->addSql('COMMENT ON TABLE patient_medicine IS \'Прием лекарства пациентом\'');
        $this->addSql('COMMENT ON COLUMN patient_medicine.id IS \'Ключ лекарства по назначению\'');
        $this->addSql('COMMENT ON COLUMN patient_medicine.medical_history_id IS \'Ключ истории болезни\'');
        $this->addSql('COMMENT ON COLUMN patient_medicine.medicine_name IS \'Название лекарства\'');
        $this->addSql('COMMENT ON COLUMN patient_medicine.instruction IS \'Инструкция по применению\'');
        $this->addSql('COMMENT ON COLUMN patient_medicine.date_begin IS \'Планируемая дата начала приема лекарства\'');
        $this->addSql('COMMENT ON COLUMN patient_medicine.enabled IS \'Ограничение использования\'');
        $this->addSql('ALTER TABLE patient_medicine ADD CONSTRAINT fk_6fb372283544ad9e FOREIGN KEY (medical_history_id) REFERENCES medical_history (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE background_diseases DROP CONSTRAINT FK_2C1D0B80D88E3C0D');
        $this->addSql('DROP INDEX IDX_2C1D0B80D88E3C0D');
        $this->addSql('DROP INDEX background_diseases_pkey');
        $this->addSql('ALTER TABLE background_diseases ADD medical_history_id INT NOT NULL');
        $this->addSql('ALTER TABLE background_diseases DROP clinical_diagnosis_id');
        $this->addSql('COMMENT ON COLUMN background_diseases.medical_history_id IS \'Ключ истории болезни\'');
        $this->addSql('ALTER TABLE background_diseases ADD PRIMARY KEY (medical_history_id, diagnosis_id)');
        $this->addSql('ALTER TABLE complications DROP CONSTRAINT FK_BC020A5FD88E3C0D');
        $this->addSql('DROP INDEX IDX_BC020A5FD88E3C0D');
        $this->addSql('DROP INDEX complications_pkey');
        $this->addSql('ALTER TABLE complications ADD medical_history_id INT NOT NULL');
        $this->addSql('ALTER TABLE complications DROP clinical_diagnosis_id');
        $this->addSql('COMMENT ON COLUMN complications.medical_history_id IS \'Ключ истории болезни\'');
        $this->addSql('ALTER TABLE complications ADD CONSTRAINT fk_bc020a5f3544ad9e FOREIGN KEY (medical_history_id) REFERENCES medical_history (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_bc020a5f3544ad9e ON complications (medical_history_id)');
        $this->addSql('ALTER TABLE complications ADD PRIMARY KEY (medical_history_id, diagnosis_id)');
        $this->addSql('ALTER TABLE concomitant_diseases DROP CONSTRAINT FK_59F3A8E0D88E3C0D');
        $this->addSql('DROP INDEX IDX_59F3A8E0D88E3C0D');
        $this->addSql('DROP INDEX concomitant_diseases_pkey');
        $this->addSql('ALTER TABLE concomitant_diseases ADD medical_history_id INT NOT NULL');
        $this->addSql('ALTER TABLE concomitant_diseases DROP clinical_diagnosis_id');
        $this->addSql('COMMENT ON COLUMN concomitant_diseases.medical_history_id IS \'Ключ истории болезни\'');
        $this->addSql('ALTER TABLE concomitant_diseases ADD CONSTRAINT fk_59f3a8e03544ad9e FOREIGN KEY (medical_history_id) REFERENCES medical_history (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_59f3a8e03544ad9e ON concomitant_diseases (medical_history_id)');
        $this->addSql('ALTER TABLE concomitant_diseases ADD PRIMARY KEY (medical_history_id, diagnosis_id)');
    }
}
