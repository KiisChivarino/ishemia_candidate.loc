<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201020082012 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE discharge_epicrisis_file_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE discharge_epicrisis_file (id INT NOT NULL, patient_discharge_epicrisis_id INT NOT NULL, patient_file_id INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_26D6C758BCF381C ON discharge_epicrisis_file (patient_discharge_epicrisis_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_26D6C758CEA82C87 ON discharge_epicrisis_file (patient_file_id)');
        $this->addSql('COMMENT ON COLUMN discharge_epicrisis_file.patient_discharge_epicrisis_id IS \'Ключ выписного эпикриза\'');
        $this->addSql('ALTER TABLE discharge_epicrisis_file ADD CONSTRAINT FK_26D6C758BCF381C FOREIGN KEY (patient_discharge_epicrisis_id) REFERENCES patient_discharge_epicrisis (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE discharge_epicrisis_file ADD CONSTRAINT FK_26D6C758CEA82C87 FOREIGN KEY (patient_file_id) REFERENCES patient_file (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA IF NOT EXISTS public');
        $this->addSql('DROP SEQUENCE discharge_epicrisis_file_id_seq CASCADE');
        $this->addSql('DROP TABLE discharge_epicrisis_file');
    }
}
