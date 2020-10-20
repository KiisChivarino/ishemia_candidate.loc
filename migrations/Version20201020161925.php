<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201020161925 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE discharge_epicrisis_file DROP CONSTRAINT fk_26d6c758cea82c87');
        $this->addSql('ALTER TABLE patient_testing_file DROP CONSTRAINT fk_3d3cb73ecea82c87');
        $this->addSql('DROP SEQUENCE patient_file_id_seq CASCADE');
        $this->addSql('DROP TABLE patient_file');
        $this->addSql('DROP INDEX uniq_3d3cb73ecea82c87');
        $this->addSql('ALTER TABLE patient_testing_file ADD file_name VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE patient_testing_file ADD uploaded_date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL');
        $this->addSql('ALTER TABLE patient_testing_file ADD extension VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE patient_testing_file DROP patient_file_id');
        $this->addSql('DROP INDEX uniq_26d6c758cea82c87');
        $this->addSql('ALTER TABLE discharge_epicrisis_file ADD file_name VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE discharge_epicrisis_file ADD uploaded_date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL');
        $this->addSql('ALTER TABLE discharge_epicrisis_file ADD extension VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE discharge_epicrisis_file DROP patient_file_id');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA IF NOT EXISTS public');
        $this->addSql('CREATE SEQUENCE patient_file_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE patient_file (id INT NOT NULL, patient_id INT NOT NULL, file_name VARCHAR(255) NOT NULL, uploaded_date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, extension VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_50e7bd86b899279 ON patient_file (patient_id)');
        $this->addSql('COMMENT ON COLUMN patient_file.id IS \'Ключ файла пациента\'');
        $this->addSql('COMMENT ON COLUMN patient_file.patient_id IS \'Ключ пациента\'');
        $this->addSql('ALTER TABLE patient_file ADD CONSTRAINT fk_50e7bd86b899279 FOREIGN KEY (patient_id) REFERENCES patient (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE discharge_epicrisis_file ADD patient_file_id INT NOT NULL');
        $this->addSql('ALTER TABLE discharge_epicrisis_file DROP file_name');
        $this->addSql('ALTER TABLE discharge_epicrisis_file DROP uploaded_date');
        $this->addSql('ALTER TABLE discharge_epicrisis_file DROP extension');
        $this->addSql('COMMENT ON COLUMN discharge_epicrisis_file.patient_file_id IS \'Ключ файла пациента\'');
        $this->addSql('ALTER TABLE discharge_epicrisis_file ADD CONSTRAINT fk_26d6c758cea82c87 FOREIGN KEY (patient_file_id) REFERENCES patient_file (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX uniq_26d6c758cea82c87 ON discharge_epicrisis_file (patient_file_id)');
        $this->addSql('ALTER TABLE patient_testing_file ADD patient_file_id INT NOT NULL');
        $this->addSql('ALTER TABLE patient_testing_file DROP file_name');
        $this->addSql('ALTER TABLE patient_testing_file DROP uploaded_date');
        $this->addSql('ALTER TABLE patient_testing_file DROP extension');
        $this->addSql('COMMENT ON COLUMN patient_testing_file.patient_file_id IS \'Ключ файла пациента\'');
        $this->addSql('ALTER TABLE patient_testing_file ADD CONSTRAINT fk_3d3cb73ecea82c87 FOREIGN KEY (patient_file_id) REFERENCES patient_file (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX uniq_3d3cb73ecea82c87 ON patient_testing_file (patient_file_id)');
    }
}
