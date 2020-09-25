<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200925121031 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE background_diseases (medical_history_id INT NOT NULL, diagnosis_id INT NOT NULL, PRIMARY KEY(medical_history_id, diagnosis_id))');
        $this->addSql('CREATE INDEX IDX_2C1D0B803544AD9E ON background_diseases (medical_history_id)');
        $this->addSql('CREATE INDEX IDX_2C1D0B803CBE4D00 ON background_diseases (diagnosis_id)');
        $this->addSql('COMMENT ON COLUMN background_diseases.medical_history_id IS \'Ключ истории болезни\'');
        $this->addSql('COMMENT ON COLUMN background_diseases.diagnosis_id IS \'Ключ записи\'');
        $this->addSql('CREATE TABLE complications (medical_history_id INT NOT NULL, diagnosis_id INT NOT NULL, PRIMARY KEY(medical_history_id, diagnosis_id))');
        $this->addSql('CREATE INDEX IDX_BC020A5F3544AD9E ON complications (medical_history_id)');
        $this->addSql('CREATE INDEX IDX_BC020A5F3CBE4D00 ON complications (diagnosis_id)');
        $this->addSql('COMMENT ON COLUMN complications.medical_history_id IS \'Ключ истории болезни\'');
        $this->addSql('COMMENT ON COLUMN complications.diagnosis_id IS \'Ключ записи\'');
        $this->addSql('CREATE TABLE concomitant_diseases (medical_history_id INT NOT NULL, diagnosis_id INT NOT NULL, PRIMARY KEY(medical_history_id, diagnosis_id))');
        $this->addSql('CREATE INDEX IDX_59F3A8E03544AD9E ON concomitant_diseases (medical_history_id)');
        $this->addSql('CREATE INDEX IDX_59F3A8E03CBE4D00 ON concomitant_diseases (diagnosis_id)');
        $this->addSql('COMMENT ON COLUMN concomitant_diseases.medical_history_id IS \'Ключ истории болезни\'');
        $this->addSql('COMMENT ON COLUMN concomitant_diseases.diagnosis_id IS \'Ключ записи\'');
        $this->addSql('ALTER TABLE background_diseases ADD CONSTRAINT FK_2C1D0B803544AD9E FOREIGN KEY (medical_history_id) REFERENCES medical_history (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE background_diseases ADD CONSTRAINT FK_2C1D0B803CBE4D00 FOREIGN KEY (diagnosis_id) REFERENCES diagnosis (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE complications ADD CONSTRAINT FK_BC020A5F3544AD9E FOREIGN KEY (medical_history_id) REFERENCES medical_history (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE complications ADD CONSTRAINT FK_BC020A5F3CBE4D00 FOREIGN KEY (diagnosis_id) REFERENCES diagnosis (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE concomitant_diseases ADD CONSTRAINT FK_59F3A8E03544AD9E FOREIGN KEY (medical_history_id) REFERENCES medical_history (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE concomitant_diseases ADD CONSTRAINT FK_59F3A8E03CBE4D00 FOREIGN KEY (diagnosis_id) REFERENCES diagnosis (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE medical_history ADD main_disease_id INT NOT NULL');
        $this->addSql('ALTER TABLE medical_history ADD disease_history TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE medical_history ADD life_history TEXT DEFAULT NULL');
        $this->addSql('COMMENT ON COLUMN medical_history.main_disease_id IS \'Ключ записи\'');
        $this->addSql('COMMENT ON COLUMN medical_history.disease_history IS \'Анамнез болезни\'');
        $this->addSql('COMMENT ON COLUMN medical_history.life_history IS \'Анамнез жизни\'');
        $this->addSql('ALTER TABLE medical_history ADD CONSTRAINT FK_61B89085E0CD2722 FOREIGN KEY (main_disease_id) REFERENCES diagnosis (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_61B89085E0CD2722 ON medical_history (main_disease_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA if not exists public');
        $this->addSql('DROP TABLE background_diseases');
        $this->addSql('DROP TABLE complications');
        $this->addSql('DROP TABLE concomitant_diseases');
        $this->addSql('ALTER TABLE medical_history DROP CONSTRAINT FK_61B89085E0CD2722');
        $this->addSql('DROP INDEX IDX_61B89085E0CD2722');
        $this->addSql('ALTER TABLE medical_history DROP main_disease_id');
        $this->addSql('ALTER TABLE medical_history DROP disease_history');
        $this->addSql('ALTER TABLE medical_history DROP life_history');
    }
}
